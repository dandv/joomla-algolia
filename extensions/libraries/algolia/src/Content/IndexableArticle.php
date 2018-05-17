<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Content
 *
 * @copyright  Copyright (C) 2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Algolia\Content;

\JLoader::register('ContentHelperRoute', JPATH_SITE . '/components/com_content/helpers/route.php');

defined('_JEXEC') || die;

use Joomla\CMS\Application\CMSApplication;
use Phproberto\Joomla\Algolia\IndexableItem;

/**
 * Represents an indexable item.
 *
 * @since  __DEPLOY_VERSION__
 */
class IndexableArticle extends IndexableItem
{
	/**
	 * Prepare indexable data before sending it.
	 *
	 * @return  array
	 */
	protected function prepareIndexableData()
	{
		$item = $this->data;

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
			'_tags'          => [],
			'url'            => $this->url()
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
	 * Get this article URL.
	 *
	 * @return  string
	 */
	protected function url()
	{
		$app = CMSApplication::getInstance('site');

		$router  = $app->getRouter();

		$rawUrl = \ContentHelperRoute::getArticleRoute(
			$this->data['id'] . ':' . $this->data['alias'],
			$this->data['catid'],
			$this->data['language']
		);
		$baseUrl = $router->build('index.php')->toString();
		$articleUrl = $router->build($rawUrl)->toString();

		$lastChar = $baseUrl[strlen($baseUrl) - 1];

		if ($lastChar !== '/')
		{
			return $rawUrl;
		}

		return '/' . str_replace($baseUrl, '', $articleUrl);
	}

	/**
	 * Use ContentHelperRoute to retrieve this article route.
	 *
	 * @param   string   $slug      Article slug
	 * @param   integer  $catid     Article category identifer
	 * @param   string   $language  Article language
	 *
	 * @return  string
	 */
	protected function articleHelperRoute($slug, $catid, $language)
	{
		return \ContentHelperRoute::getArticleRoute(
			$slug,
			$catid,
			$language
		);
	}
}
