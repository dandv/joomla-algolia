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
use Phproberto\Joomla\Algolia\Indexer\Config;

/**
 * Content Articles finder.
 *
 * @since  __DEPLOY_VERSION__
 */
class ArticlesIndexerConfig extends Config
{
	const CATEGORY_MODE_INCLUDE = '1';
	const CATEGORY_MODE_INCLUDE_DESCENDANTS = '2';
	const CATEGORY_MODE_EXCLUDE = '3';
	const CATEGORY_MODE_EXCLUDE_DESCENDANTS = '4';

	/**
	 * Retrieve active category mode.
	 *
	 * @return  string
	 */
	public function categoryMode()
	{
		$mode = trim($this->get('category_mode'));
		$allowedModes = [
			self::CATEGORY_MODE_INCLUDE,
			self::CATEGORY_MODE_INCLUDE_DESCENDANTS,
			self::CATEGORY_MODE_EXCLUDE,
			self::CATEGORY_MODE_EXCLUDE_DESCENDANTS
		];

		if (!in_array($mode, $allowedModes))
		{
			return self::CATEGORY_MODE_INCLUDE_DESCENDANTS;
		}

		return $mode;
	}

	/**
	 * Categories to include/exclude.
	 *
	 * @return  integer[]
	 */
	public function categoriesIds()
	{
		return array_values(
			array_filter(
				ArrayHelper::toInteger((array) $this->get('categories')),
				function ($value)
				{
					return $value > 0;
				}
			)
		);
	}


	/**
	 * Fields to index.
	 *
	 * @return  integer[]
	 */
	public function fieldsIds()
	{
		return array_values(
			array_filter(
				ArrayHelper::toInteger((array) $this->get('fields')),
				function ($value)
				{
					return $value > 0;
				}
			)
		);
	}

	/**
	 * Check if include articles of only specific categories is enabled.
	 *
	 * @return  boolean
	 */
	public function isIncludeCategoriesMode()
	{
		return self::CATEGORY_MODE_INCLUDE === $this->categoryMode();
	}

	/**
	 * Check if include articles of only specific categories is enabled.
	 *
	 * @return  boolean
	 */
	public function isIncludeCategoriesWithDescendantsMode()
	{
		return self::CATEGORY_MODE_INCLUDE_DESCENDANTS === $this->categoryMode();
	}

	/**
	 * Check if include articles of only specific categories is enabled.
	 *
	 * @return  boolean
	 */
	public function isExcludeCategoriesMode()
	{
		return self::CATEGORY_MODE_EXCLUDE === $this->categoryMode();
	}

	/**
	 * Check if include articles of only specific categories is enabled.
	 *
	 * @return  boolean
	 */
	public function isExcludeCategoriesWithDescendantsMode()
	{
		return self::CATEGORY_MODE_EXCLUDE_DESCENDANTS === $this->categoryMode();
	}
}
