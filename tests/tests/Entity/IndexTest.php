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
use AlgoliaSearch\Client;
use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Algolia\Entity\Index;
use Phproberto\Joomla\Algolia\Indexer\AlgoliaConfig;
use Phproberto\Joomla\Entity\Core\Extension\Component;

/**
 * Index tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class IndexTest extends \TestCaseDatabase
{
	/**
	 * Index being tested.
	 *
	 * @var  Indexer
	 */
	protected $index;

	/**
	 * @test
	 *
	 * @return void
	 */
	public function algoliaClientReturnsCorrectClient()
	{
		$this->assertInstanceOf(Client::class, $this->index->algoliaClient());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function algoliaClientReturnsCachedClient()
	{
		$client = $this->getMockBuilder(Client::class)
			->disableOriginalConstructor()
			->getMock();

		$reflection = new \ReflectionClass($this->index);
		$algoliaClientProperty = $reflection->getProperty('algoliaClient');
		$algoliaClientProperty->setAccessible(true);

		$this->assertSame(null, $algoliaClientProperty->getValue($this->index));

		$algoliaClientProperty->setValue($this->index, $client);

		$this->assertSame($client, $this->index->algoliaClient());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function algoliaConfigReturnsValidInstance()
	{
		$config = $this->index->algoliaConfig();

		$this->assertInstanceOf(AlgoliaConfig::class, $config);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function algoliaConfigReturnsCachedConfig()
	{
		$config = new AlgoliaConfig(['test' => 'my-value']);

		$reflection = new \ReflectionClass($this->index);
		$algoliaConfigProperty = $reflection->getProperty('algoliaConfig');
		$algoliaConfigProperty->setAccessible(true);

		$this->assertSame(null, $algoliaConfigProperty->getValue($this->index));

		$algoliaConfigProperty->setValue($this->index, $config);

		$this->assertSame($config, $this->index->algoliaConfig());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function algoliaIndexReturnsCorrectIndex()
	{
		$index = $this->getMockBuilder(Index::class)
			->disableOriginalConstructor()
			->getMock();

		$client = $this->getMockBuilder(Client::class)
			->disableOriginalConstructor()
			->setMethods(['initIndex'])
			->getMock();

		$client->expects($this->once())
			->method('initIndex')
			->with($this->equalTo('blog'))
			->willReturn($index);

		$reflection = new \ReflectionClass($this->index);
		$algoliaClientProperty = $reflection->getProperty('algoliaClient');
		$algoliaClientProperty->setAccessible(true);

		$algoliaClientProperty->setValue($this->index, $client);

		$this->assertSame($index, $this->index->algoliaIndex());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function deleteItemsRemovesItems()
	{
		$algoliaIndex = $this->getMockBuilder(Index::class)
			->disableOriginalConstructor()
			->setMethods(['deleteObjects'])
			->getMock();

		$algoliaIndex->expects($this->once())
			->method('deleteObjects')
			->with($this->equalTo([1,2]));

		$reflection = new \ReflectionClass($this->index);
		$algoliaIndexProperty = $reflection->getProperty('algoliaIndex');
		$algoliaIndexProperty->setAccessible(true);

		$algoliaIndexProperty->setValue($this->index, $algoliaIndex);

		$this->assertTrue($this->index->items()->count() > 0);

		$this->index->deleteItems();

		$this->assertFalse($this->index->items()->count() > 0);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function deleteItemsThrowsExceptionIfErrorHappens()
	{
		$thrownErrorMessage = 'WordPress broke your site';

		$table = $this->getMockBuilder(Table::class)
			->disableOriginalConstructor()
			->setMethods(['delete', 'getError'])
			->getMock();

		$table->expects($this->once())
			->method('delete')
			->with($this->equalTo(23))
			->willReturn(false);

		$table->expects($this->once())
			->method('getError')
			->willReturn($thrownErrorMessage);

		$component = $this->getMockBuilder(Component::class)
			->disableOriginalConstructor()
			->setMethods(['table'])
			->getMock();

		$component->expects($this->once())
			->method('table')
			->with($this->equalTo('item'))
			->willReturn($table);

		$algoliaIndex = $this->getMockBuilder(Index::class)
			->disableOriginalConstructor()
			->setMethods(['deleteObjects'])
			->getMock();

		$algoliaIndex->expects($this->once())
			->method('deleteObjects')
			->with($this->equalTo([23]));

		$index = $this->getMockBuilder(Index::class)
			->disableOriginalConstructor()
			->setMethods(['component', 'algoliaIndex'])
			->getMock();

		$index->expects($this->once())
			->method('component')
			->willReturn($component);

		$index->expects($this->once())
			->method('algoliaIndex')
			->willReturn($algoliaIndex);

		$error = '';

		try
		{
			$index->deleteItems([23]);
		}
		catch (\Exception $e)
		{
			$error = $e->getMessage();
		}

		$this->assertTrue(substr_count($error, $thrownErrorMessage) > 0);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function entityCanBeLoaded()
	{
		$this->assertNotEmpty($this->index->all());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function itemsReturnsIndexItems()
	{
		$items = $this->index->items();

		$this->assertInstanceOf(Collection::class, $items);
		$this->assertTrue($items->count() > 0);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function itemsReturnsEmptyCollectionForNoId()
	{
		$index = new Index;

		$items = $index->items();

		$this->assertInstanceOf(Collection::class, $items);
		$this->assertTrue($items->count() === 0);
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

		$this->index = Index::find(1);
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

		Index::clearAll();
	}
}
