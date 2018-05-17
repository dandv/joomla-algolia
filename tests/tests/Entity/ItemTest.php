<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Tests.Unit
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Algolia\Tests\Entity;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Phproberto\Joomla\Algolia\Entity\Index;
use Phproberto\Joomla\Algolia\Entity\Item;

/**
 * Indexer tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class ItemTest extends \TestCaseDatabase
{
	/**
	 * @test
	 *
	 * @return void
	 */
	public function dataReturnsCorrectData()
	{
		$data = $this->item->data();

		$this->assertTrue($data && !empty($data));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function entityCanBeLoaded()
	{
		$this->assertNotEmpty($this->item->all());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function indexerReturnsIndexer()
	{
		$index = $this->item->index();

		$this->assertInstanceOf(Index::class, $index);
		$this->assertTrue($index->hasId());
	}

	/**
	 * Gets the data set to be loaded into the database during setup
	 *
	 * @return  \PHPUnit_Extensions_Database_DataSet_CsvDataSet
	 */
	protected function getDataSet()
	{
		$dataSet = new \PHPUnit_Extensions_Database_DataSet_CsvDataSet(',', "'", '\\');
		$dataSet->addTable('jos_extensions', JPATH_ALGOLIA_TESTS . '/db/data/extensions.csv');
		$dataSet->addTable('jos_algolia_index', JPATH_ALGOLIA_TESTS . '/db/data/algolia_index.csv');
		$dataSet->addTable('jos_algolia_item', JPATH_ALGOLIA_TESTS . '/db/data/algolia_item.csv');

		return $dataSet;
	}

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 */
	protected function setUp()
	{
		parent::setUp();

		// Get the mocks
		$this->saveFactoryState();

		Factory::$session     = $this->getMockSession();
		Factory::$config      = $this->getMockConfig();
		Factory::$application = $this->getMockCmsApp();

		$this->item = Item::find(1);
	}

	/**
	 * This method is called before the first test of this test class is run.
	 *
	 * @return  void
	 */
	public static function setUpBeforeClass()
	{
		parent::setUpBeforeClass();

		$sqlFiles = [
			JPATH_ALGOLIA_TESTS . '/db/schema/algolia_index.sql',
			JPATH_ALGOLIA_TESTS . '/db/schema/algolia_item.sql'
		];

		foreach ($sqlFiles as $sqlFile)
		{
			static::$driver->setQuery(
				file_get_contents($sqlFile)
			);

			static::$driver->execute();
		}

		Factory::$database = static::$driver;
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 */
	protected function tearDown()
	{
		$this->restoreFactoryState();

		parent::tearDown();

		Item::clearAll();
	}
}
