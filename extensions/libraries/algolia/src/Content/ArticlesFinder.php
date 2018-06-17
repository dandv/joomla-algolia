<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Indexer
 *
 * @copyright  Copyright (C) 2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Algolia\Content;

defined('_JEXEC') || die;

use Joomla\Utilities\ArrayHelper;
use Phproberto\Joomla\Algolia\Finder\BaseFinder;
use Phproberto\Joomla\Algolia\Finder\FinderInterface;
use Phproberto\Joomla\Algolia\Content\IndexableArticle;

/**
 * Content Articles finder.
 *
 * @since  __DEPLOY_VERSION__
 */
class ArticlesFinder extends BaseFinder implements FinderInterface
{
	/**
	 * Search indexable items.
	 *
	 * @param   array   $options  Array with search filders.
	 *
	 * @return  IndexableArticle[]
	 */
	public function find(array $options = [])
	{
		$categoriesIds = $this->config()->categoriesIds();

		if (!$categoriesIds && ($this->config()->isIncludeCategoriesMode() || $this->config()->isIncludeCategoriesWithDescendantsMode()))
		{
			return [];
		}

		$db = $this->db();

		$query = $db->getQuery(true)
			->select('a.*')
			->select($db->qn('c.title', 'category_title'))
			->select($db->qn('u.name', 'author_name'))
			->from($db->qn('#__content', 'a'))
			->innerjoin(
				$db->qn('#__categories', 'c')
				. ' ON ' . $db->qn('c.id') . ' = ' . $db->qn('a.catid')
			)
			->leftjoin(
				$db->qn('#__users', 'u')
				. ' ON ' . $db->qn('u.id') . ' = ' . $db->qn('a.created_by')
			)
			->leftjoin(
				$db->qn('#__algolia_item', 'ii')
				. ' ON ' . $db->qn('ii.object_id') . ' = ' . $db->qn('a.id')
			)
			->order('a.ordering ASC');

		if (!empty($options['filter']['ids']))
		{
			$ids = array_filter(ArrayHelper::toInteger((array) $options['filter']['ids']));

			$query->where($db->qn('a.id') . ' IN(' . implode(',', $ids) . ')');
		}

		if ($this->config()->isIncludeCategoriesMode())
		{
			$query->where($db->qn('a.catid') . ' IN(' . implode(',', $categoriesIds) . ')');
		}

		if ($this->config()->isIncludeCategoriesWithDescendantsMode())
		{
			$query->innerJoin(
				$db->qn('#__categories', 'anc1') . ' ON ' . $db->qn('c.lft') . ' >= ' . $db->qn('anc1.lft') .
					' AND ' . $db->qn('c.rgt') . ' <= ' . $db->qn('anc1.rgt')
			)->where($db->qn('anc1.id') . ' IN(' . implode(',', $categoriesIds) . ')');
		}

		if ($categoriesIds && $this->config()->isExcludeCategoriesMode())
		{
			$query->where($db->qn('a.catid') . ' NOT IN(' . implode(',', $categoriesIds) . ')');
		}

		if ($categoriesIds && $this->config()->isExcludeCategoriesWithDescendantsMode())
		{
			$query->innerJoin(
				$db->qn('#__categories', 'anc1') . ' ON ' . $db->qn('c.lft') . ' < ' . $db->qn('anc1.lft') .
					' OR ' . $db->qn('c.rgt') . ' > ' . $db->qn('anc1.rgt')
			)->where($db->qn('anc1.id') . ' IN(' . implode(',', $categoriesIds) . ')');
		}

		$indexDate = null;
		$now = new \DateTime;

		if (!empty($options['filter']['hours']))
		{
			$hours = (int) $options['filter']['hours'];
			$indexDate = $now->modify('-' . $hours . ' hours');
		}
		elseif (!empty($options['filter']['days']))
		{
			$days = (int) $options['filter']['days'];

			$indexDate = $now->modify('-' . $days . ' days');
		}

		if ($indexDate)
		{
			$query->where(
				'('
					. $db->qn('ii.modified_date') . ' IS NULL'
					. ' OR ' . $db->qn('ii.modified_date') . ' < ' . $db->q($indexDate->format('Y-m-d H:i:s'))
				. ')'
			);
		}

		$limit = 0;

		if (!empty($options['list']['limit']))
		{
			$limit = (int) $options['list']['limit'];
		}

		$db->setQuery($query, 0, $limit);

		$items = $this->loadTags(
			$this->loadFields(
				$db->loadAssocList('id') ?: []
			)
		);

		return array_map(
			function ($item)
			{
				return $this->indexer->indexableItem($item);
			},
			$items
		);
	}

	/**
	 * Load fields for an array of articles.
	 *
	 * @param   array   $articles  Array of articles
	 *
	 * @return  array
	 */
	protected function loadFields(array $articles)
	{
		$fieldsIds = $this->config()->fieldsIds();

		if (empty($articles) || empty($fieldsIds))
		{
			return $articles;
		}

		$db = $this->db();

		$query = $db->getQuery(true)
			->select($db->qn('fv.item_id', 'article_id'))
			->select('f.*')
			->select($db->qn('fv.value'))
			->from($db->qn('#__fields', 'f'))
			->innerjoin(
				$db->qn('#__fields_values', 'fv')
				. ' ON ' . $db->qn('f.id') . ' = ' . $db->qn('fv.field_id')
			)
			->where($db->qn('context') . ' = ' . $db->q('com_content.article'))
			->where($db->qn('fv.item_id') . ' IN(' . implode(',', array_keys($articles)) . ')')
			->where($db->qn('f.id') . ' IN(' . implode(',', $fieldsIds) . ')')
			->order('fv.item_id ASC');

		$db->setQuery($query);

		$fields = $db->loadAssocList() ?: [];

		foreach ($fields as $field)
		{
			$articleId = $field['article_id'];
			unset($field['article_id']);

			if (!array_key_exists($articleId, $articles))
			{
				continue;
			}

			if (!isset($articles[$articleId]['fields']))
			{
				$articles[$articleId]['fields'] = [];
			}

			if (!isset($articles[$articleId]['fields'][$field['id']]))
			{
				$articles[$articleId]['fields'][$field['id']] = [];
			}

			$articles[$articleId]['fields'][$field['id']][] = $field;
		}

		return $articles;
	}

	/**
	 * Load items by their id.
	 *
	 * @param   array   $articles  Array of articles
	 *
	 * @return  array
	 */
	protected function loadTags(array $articles)
	{
		if (empty($articles))
		{
			return [];
		}

		$db = $this->db();

		$query = $db->getQuery(true)
			->select($db->qn('ctm.content_item_id', 'article_id'))
			->select('t.*')
			->from($db->qn('#__tags', 't'))
			->innerjoin(
				$db->qn('#__contentitem_tag_map', 'ctm')
				. ' ON ' . $db->qn('t.id') . ' = ' . $db->qn('ctm.tag_id')
			)
			->where($db->qn('ctm.type_alias') . ' = ' . $db->q('com_content.article'))
			->where($db->qn('ctm.content_item_id') . ' IN(' . implode(',', array_keys($articles)) . ')')
			->order('t.lft ASC');

		$db->setQuery($query);

		$tags = $db->loadAssocList() ?: [];

		foreach ($tags as $tag)
		{
			$articleId = $tag['article_id'];
			unset($tag['article_id']);

			if (!array_key_exists($articleId, $articles))
			{
				continue;
			}

			if (!isset($articles[$articleId]['tags']))
			{
				$articles[$articleId]['tags'] = [];
			}

			$articles[$articleId]['tags'][$tag['id']] = $tag;
		}

		return $articles;
	}
}
