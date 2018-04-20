<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Indexer
 *
 * @copyright  Copyright (C) 2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Algolia\Indexer;

defined('_JEXEC') || die;

use Joomla\Utilities\ArrayHelper;

/**
 * Article indexer.
 *
 * @since  __DEPLOY_VERSION__
 */
class ContentArticleIndexer extends BaseIndexer
{
	const CATEGORY_MODE_INCLUDE = '1';
	const CATEGORY_MODE_INCLUDE_DESCENDANTS = '2';
	const CATEGORY_MODE_EXCLUDE = '3';
	const CATEGORY_MODE_EXCLUDE_DESCENDANTS = '4';

	/**
	 * Categories to include/exclude.
	 *
	 * @return  integer[]
	 */
	protected function categoriesIds()
	{
		return array_values(
			array_filter(
				ArrayHelper::toInteger($this->params()->get('categories'))
			)
		);
	}

	/**
	 * Fields to index.
	 *
	 * @return  integer[]
	 */
	protected function fieldsIds()
	{
		return array_values(
			array_filter(
				ArrayHelper::toInteger($this->params()->get('fields'))
			)
		);
	}

	/**
	 * Check if include articles of only specific categories is enabled.
	 *
	 * @return  boolean
	 */
	protected function isIncludeCategoriesMode()
	{
		return self::CATEGORY_MODE_INCLUDE === $this->params()->get('category_mode');
	}

	/**
	 * Check if include articles of only specific categories is enabled.
	 *
	 * @return  boolean
	 */
	protected function isIncludeCategoriesWithDescendantsMode()
	{
		return self::CATEGORY_MODE_INCLUDE_DESCENDANTS === $this->params()->get('category_mode');
	}

	/**
	 * Check if include articles of only specific categories is enabled.
	 *
	 * @return  boolean
	 */
	protected function isExcludeCategoriesMode()
	{
		return self::CATEGORY_MODE_EXCLUDE === $this->params()->get('category_mode');
	}

	/**
	 * Check if include articles of only specific categories is enabled.
	 *
	 * @return  boolean
	 */
	protected function isExcludeCategoriesWithDescendantsMode()
	{
		return self::CATEGORY_MODE_EXCLUDE_DESCENDANTS === $this->params()->get('category_mode');
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
		$fieldsIds = $this->fieldsIds();

		if (empty($articles) || empty($fieldsIds))
		{
			return $articles;
		}

		$db = $this->db();

		$query = $db->getQuery(true)
			->select($db->qn('fv.item_id', 'article_id'))
			->select($db->qn('f.*'))
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
			->select($db->qn('t.*'))
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

	/**
	 * Prepare an item to be index.
	 *
	 * @param   array   $item  Array containing item information.
	 *
	 * @return  array
	 */
	protected function prepareItem(array $item)
	{
		$indexableObject = [
			'objectID'       => $item['id'],
			'title'          => $item['title'],
			'introtext'      => $item['introtext'],
			'state'          => (int) $item['state'],
			'catid'          => (int) $item['catid'],
			'category_title' => $item['category_title'],
			'ordering'       => (int) $item['ordering'],
			'metakey'        => $item['metakey'],
			'metadesc'       => $item['metadesc'],
			'access'         => (int) $item['access'],
			'language'       => $item['language'],
			'created'        => strtotime($item['created']),
			'author_name'    => $item['author_name'],
			'modified'       => strtotime($item['modified']),
			'_tags'          => []
		];

		if (isset($item['fields']))
		{
			foreach ($item['fields'] as $fieldId => $fieldValues)
			{
				foreach ($fieldValues as $fieldValue)
				{
					if (!isset($indexableObject[$fieldValue['name']]))
					{
						$indexableObject[$fieldValue['name']] = [];
					}

					$indexableObject[$fieldValue['name']][] = $fieldValue['value'];
				}
			}
		}

		if (isset($item['tags']))
		{
			foreach ($item['tags'] as $tag)
			{
				$indexableObject['_tags'][] = $tag['title'];
			}
		}

		return $indexableObject;
	}

	/**
	 * Search indexable items.
	 *
	 * @param   array   $search  Array with filtering information
	 *
	 * @return  array
	 */
	public function searchItems(array $search)
	{
		$categoriesIds = $this->categoriesIds();

		if (!$categoriesIds && ($this->isIncludeCategoriesMode() || $this->isIncludeCategoriesWithDescendantsMode()))
		{
			return [];
		}

		$db = $this->db();

		$query = $db->getQuery(true)
			->select($db->qn('a.*'))
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
				$db->qn('#__algolia_indexer_item', 'ii')
				. ' ON ' . $db->qn('ii.object_id') . ' = ' . $db->qn('a.id')
			)
			->order('a.ordering ASC');

		if (!empty($search['filter']['ids']))
		{
			$ids = array_filter(ArrayHelper::toInteger($search['filter']['ids']));

			$query->where($db->qn('a.id') . ' IN(' . implode(',', $ids) . ')');
		}

		if ($this->isIncludeCategoriesMode())
		{
			$query->where($db->qn('a.catid') . ' IN(' . implode(',', $categoriesIds) . ')');
		}

		if ($this->isIncludeCategoriesWithDescendantsMode())
		{
			$query->innerJoin(
				$db->qn('#__categories', 'anc1') . ' ON ' . $db->qn('c.lft') . ' >= ' . $db->qn('anc1.lft') .
					' AND ' . $db->qn('c.rgt') . ' <= ' . $db->qn('anc1.rgt')
			)->where($db->qn('anc1.id') . ' IN(' . implode(',', $categoriesIds) . ')');
		}

		if ($categoriesIds && $this->isExcludeCategoriesMode())
		{
			$query->where($db->qn('a.catid') . ' NOT IN(' . implode(',', $categoriesIds) . ')');
		}

		if ($categoriesIds && $this->isExcludeCategoriesWithDescendantsMode())
		{
			$query->innerJoin(
				$db->qn('#__categories', 'anc1') . ' ON ' . $db->qn('c.lft') . ' < ' . $db->qn('anc1.lft') .
					' OR ' . $db->qn('c.rgt') . ' > ' . $db->qn('anc1.rgt')
			)->where($db->qn('anc1.id') . ' IN(' . implode(',', $categoriesIds) . ')');
		}

		$indexDate = null;
		$now = new \DateTime;

		if (!empty($search['filter']['hours']))
		{
			$hours = (int) $search['filter']['hours'];
			$indexDate = $now->modify('-' . $hours . ' hours');
		}
		elseif (!empty($search['filter']['days']))
		{
			$days = (int) $search['filter']['days'];

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

		if (!empty($search['list']['limit']))
		{
			$limit = (int) $search['list']['limit'];
		}

		$db->setQuery($query, 0, $limit);

		$items = $db->loadAssocList('id') ?: [];

		return $this->loadTags(
			$this->loadFields(
				$items
			)
		);
	}
}
