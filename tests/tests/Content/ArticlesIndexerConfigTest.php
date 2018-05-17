<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Tests.Unit
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Algolia\Tests;

defined('_JEXEC') || die;

use Phproberto\Joomla\Algolia\Content\ArticlesIndexerConfig;

/**
 * ArticlesIndexerConfig tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class ArticlesIndexerConfigTest extends \TestCase
{
	/**
	 * Ids provider
	 *
	 * @return  array
	 */
	public function idsProvider()
	{
		return [
			['',[]],
			['45',[45]],
			[['0', 0, ' 0'],[]],
			['tt',[]],
			[null,[]],
			[['null', -1],[]]
		];
	}

	/**
	 * @test
	 *
	 * @dataProvider  idsProvider
	 *
	 * @param   mixed  $ids       Identifiers
	 * @param   array  $expected  Expected result
	 *
	 * @return void
	 */
	public function categoriesIdsReturnsCorrectValues($ids, array $expected)
	{
		$config = new ArticlesIndexerConfig(['categories' => $ids]);

		$this->assertSame($expected, $config->categoriesIds());
	}

	/**
	 * @test
	 *
	 * @dataProvider  idsProvider
	 *
	 * @param   mixed  $ids       Identifiers
	 * @param   array  $expected  Expected result
	 *
	 * @return void
	 */
	public function fieldsIdsReturnsCorrectValues($ids, array $expected)
	{
		$config = new ArticlesIndexerConfig(['fields' => $ids]);

		$this->assertSame($expected, $config->fieldsIds());
	}

	/**
	 * Provider to test isExcludeCategoriesMode.
	 *
	 * @return  array
	 */
	public function isExcludeCategoriesModeProvider()
	{
		return [
			[[], false],
			[['category_mode' => null], false],
			[['category_mode' => 'null'], false],
			[['category_mode' => ''], false],
			[['category_mode' => ' '], false],
			[['category_mode' => '0'], false],
			[['category_mode' => ArticlesIndexerConfig::CATEGORY_MODE_INCLUDE], false],
			[['category_mode' => ArticlesIndexerConfig::CATEGORY_MODE_INCLUDE_DESCENDANTS], false],
			[['category_mode' => ArticlesIndexerConfig::CATEGORY_MODE_EXCLUDE], true],
			[['category_mode' => ArticlesIndexerConfig::CATEGORY_MODE_EXCLUDE_DESCENDANTS], false]
		];
	}

	/**
	 * Provider to test isExcludeCategoriesWithDescendantsMode.
	 *
	 * @return  array
	 */
	public function isExcludeCategoriesWithDescendantsModeProvider()
	{
		return [
			[[], false],
			[['category_mode' => null], false],
			[['category_mode' => 'null'], false],
			[['category_mode' => ''], false],
			[['category_mode' => ' '], false],
			[['category_mode' => '0'], false],
			[['category_mode' => ArticlesIndexerConfig::CATEGORY_MODE_INCLUDE], false],
			[['category_mode' => ArticlesIndexerConfig::CATEGORY_MODE_INCLUDE_DESCENDANTS], false],
			[['category_mode' => ArticlesIndexerConfig::CATEGORY_MODE_EXCLUDE], false],
			[['category_mode' => ArticlesIndexerConfig::CATEGORY_MODE_EXCLUDE_DESCENDANTS], true]
		];
	}

	/**
	 * Is the setting to include categories
	 *
	 * @return  boolean  [description]
	 */
	public function isIncludeCategoriesModeProvider()
	{
		return [
			[[], false],
			[['category_mode' => null], false],
			[['category_mode' => 'null'], false],
			[['category_mode' => ''], false],
			[['category_mode' => ' '], false],
			[['category_mode' => '0'], false],
			[['category_mode' => ArticlesIndexerConfig::CATEGORY_MODE_INCLUDE], true],
			[['category_mode' => ArticlesIndexerConfig::CATEGORY_MODE_INCLUDE_DESCENDANTS], false],
			[['category_mode' => ArticlesIndexerConfig::CATEGORY_MODE_EXCLUDE], false],
			[['category_mode' => ArticlesIndexerConfig::CATEGORY_MODE_EXCLUDE_DESCENDANTS], false]
		];
	}

	/**
	 * Is the setting to include categories
	 *
	 * @return  array
	 */
	public function isIncludeCategoriesWithDescendantsModeProvider()
	{
		return [
			[[], true],
			[['category_mode' => null], true],
			[['category_mode' => 'null'], true],
			[['category_mode' => ''], true],
			[['category_mode' => ' '], true],
			[['category_mode' => '0'], true],
			[['category_mode' => ArticlesIndexerConfig::CATEGORY_MODE_INCLUDE], false],
			[['category_mode' => ArticlesIndexerConfig::CATEGORY_MODE_INCLUDE_DESCENDANTS], true],
			[['category_mode' => ArticlesIndexerConfig::CATEGORY_MODE_EXCLUDE], false],
			[['category_mode' => ArticlesIndexerConfig::CATEGORY_MODE_EXCLUDE_DESCENDANTS], false]
		];
	}

	/**
	 * @test
	 *
	 * @dataProvider  isExcludeCategoriesWithDescendantsModeProvider
	 *
	 * @param   array    $params    Indexer configuration
	 * @param   boolean  $expected  Expected result
	 *
	 * @return void
	 */
	public function isExcludeCategoriesWithDescendantsModeReturnsCorrectValue(array $params, $expected)
	{
		$config = new ArticlesIndexerConfig($params);

		$this->assertSame($expected, $config->isExcludeCategoriesWithDescendantsMode());
	}

	/**
	 * @test
	 *
	 * @dataProvider  isExcludeCategoriesModeProvider
	 *
	 * @param   array    $params    Indexer configuration
	 * @param   boolean  $expected  Expected result
	 *
	 * @return void
	 */
	public function isExcludeCategoriesModeReturnsCorrectValue(array $params, $expected)
	{
		$config = new ArticlesIndexerConfig($params);

		$this->assertSame($expected, $config->isExcludeCategoriesMode());
	}

	/**
	 * @test
	 *
	 * @dataProvider  isIncludeCategoriesModeProvider
	 *
	 * @param   array    $params    Indexer configuration
	 * @param   boolean  $expected  Expected result
	 *
	 * @return void
	 */
	public function isIncludeCategoriesModeReturnsCorrectValue(array $params, $expected)
	{
		$config = new ArticlesIndexerConfig($params);

		$this->assertSame($expected, $config->isIncludeCategoriesMode());
	}

	/**
	 * @test
	 *
	 * @dataProvider  isIncludeCategoriesWithDescendantsModeProvider
	 *
	 * @param   array    $params    Indexer configuration
	 * @param   boolean  $expected  Expected result
	 *
	 * @return void
	 */
	public function isIncludeCategoriesWithDescendantsModeReturnsCorrectValue(array $params, $expected)
	{
		$config = new ArticlesIndexerConfig($params);

		$this->assertSame($expected, $config->isIncludeCategoriesWithDescendantsMode());
	}
}
