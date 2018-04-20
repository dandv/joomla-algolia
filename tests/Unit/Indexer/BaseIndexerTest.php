<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Tests.Unit
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Algolia\Tests\Unit\Indexer;

defined('_JEXEC') || die;

use AlgoliaSearch\Index;
use AlgoliaSearch\Client;
use Joomla\Registry\Registry;
use Phproberto\Joomla\Algolia\Indexer\BaseIndexer;
use Phproberto\Joomla\Algolia\Indexer\AlgoliaIndexerConfig;
use Phproberto\Joomla\Algolia\Tests\Unit\Indexer\Stubs\SampleIndexer;

/**
 * BaseIndexer tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class BaseIndexerTest extends \TestCase
{
	/**
	 * Invalid ids provider
	 *
	 * @return  array
	 */
	public function invalidIds()
	{
		return [['0'], [0]];
	}
	/**
	 * @test
	 *
	 * @return void
	 */
	public function classIsAbstract()
	{
		$reflection = new \ReflectionClass(BaseIndexer::class);

		$this->assertTrue($reflection->isAbstract());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function clientReturnsCorrectClient()
	{
		$indexer = new SampleIndexer(55);

		$row = [
			'id'             => 44,
			'name'           => 'Sample indexer',
			'application_id' => '4444id',
			'api_key'        => '5555apikey',
			'search_key'     => '6666searchkey',
			'index_name'     => '7777indexname'
		];

		$reflection = new \ReflectionClass($indexer);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);
		$method = $reflection->getMethod('client');
		$method->setAccessible(true);

		$rowProperty->setValue($indexer, $row);

		$client = $method->invoke($indexer);

		$this->assertInstanceOf(Client::class, $client);

		$this->assertSame($row['application_id'], $client->getContext()->applicationID);
		$this->assertSame($row['api_key'], $client->getContext()->apiKey);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function constructorSetsId()
	{
		$indexer = new SampleIndexer(23);

		$reflection = new \ReflectionClass($indexer);
		$idProperty = $reflection->getProperty('id');
		$idProperty->setAccessible(true);

		$this->assertSame(23, $idProperty->getValue($indexer));
	}

	/**
	 * @test
	 *
	 * @dataProvider  invalidIds
	 *
	 * @param   integer  $id  Identifer
	 *
	 * @return  void
	 *
	 * @expectedException  \InvalidArgumentException
	 */
	public function constructorThrowsExceptionForWrongId($id)
	{
		$indexer = new SampleIndexer($id);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function algoliaConfigRetrievesConfig()
	{
		$indexer = new SampleIndexer(44);

		$expectedConfig = [
			'application_id' => '4444id',
			'api_key'        => '5555apikey',
			'search_key'     => '6666searchkey',
			'index_name'     => '7777indexname'
		];

		$row = array_merge(
			[
				'id'             => 44,
				'name'           => 'Sample indexer'
			],
			$expectedConfig
		);

		$reflection = new \ReflectionClass($indexer);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($indexer, $row);

		$algoliaConfig = $indexer->algoliaConfig();

		$this->assertInstanceOf(AlgoliaIndexerConfig::class, $algoliaConfig);

		$configReflection = new \ReflectionClass($algoliaConfig);
		$configProperty = $configReflection->getProperty('config');
		$configProperty->setAccessible(true);

		$this->assertEquals($expectedConfig, $configProperty->getValue($algoliaConfig));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function bindSetsRowData()
	{
		$data = [
			'id' => 15,
			'name' => 'My indexer'
		];

		$indexer = new SampleIndexer(15);

		$reflection = new \ReflectionClass($indexer);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$this->assertSame(null, $rowProperty->getValue($indexer));

		$indexer->bind($data);

		$this->assertSame($data, $rowProperty->getValue($indexer));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function deleleItemCallsIndexDeleteObject()
	{
		$index = $this->indexMock(['deleteObject']);

		$index->expects($this->once())
			->method('deleteObject')
			->with(23)
			->willReturn(null);

		$indexer = new SampleIndexer(455);

		$reflection = new \ReflectionClass($indexer);
		$indexProperty = $reflection->getProperty('index');
		$indexProperty->setAccessible(true);

		$indexProperty->setValue($indexer, $index);

		$indexer->deleteItem(23);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function deleleItemsCallsIndexDeleteObjects()
	{
		$index = $this->indexMock(['deleteObjects']);

		$index->expects($this->once())
			->method('deleteObjects')
			->with([23, 66])
			->willReturn(null);

		$indexer = new SampleIndexer(665);

		$reflection = new \ReflectionClass($indexer);
		$indexProperty = $reflection->getProperty('index');
		$indexProperty->setAccessible(true);

		$indexProperty->setValue($indexer, $index);

		$indexer->deleteItems([null, '', '23', 66]);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function idReturnsActiveId()
	{
		$indexer = new SampleIndexer(88);

		$this->assertSame(88, $indexer->id());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function indexReturnsCorrectIndex()
	{
		$indexer = new SampleIndexer(55);

		$row = [
			'id'             => 44,
			'name'           => 'Sample indexer',
			'application_id' => '4444id',
			'api_key'        => '5555apikey',
			'search_key'     => '6666searchkey',
			'index_name'     => '7777indexname'
		];

		$reflection = new \ReflectionClass($indexer);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);
		$method = $reflection->getMethod('index');
		$method->setAccessible(true);

		$rowProperty->setValue($indexer, $row);

		$index = $method->invoke($indexer);

		$this->assertInstanceOf(Index::class, $index);
		$this->assertSame($row['index_name'], $index->indexName);

		$indexReflection = new \ReflectionClass($index);
		$contextProperty = $indexReflection->getProperty('context');
		$contextProperty->setAccessible(true);

		$context = $contextProperty->getValue($index);

		$this->assertSame($row['application_id'], $context->applicationID);
		$this->assertSame($row['api_key'], $context->apiKey);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function indexItemSendsCorrectDataToIndexItems()
	{
		$indexer = $this->indexerMock(['indexItems']);

		$indexer->expects($this->at(0))
			->method('indexItems')
			->with([24]);

		$indexer->indexItem(24);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function indexItemsCallsIndexItemForEachItem()
	{
		$ids = [62, 63];
		$data = [
			[
				'id'          => 62,
				'title'       => 'Curso de ejemplo',
				'title_latam' => 'Título latinoamérica',
				'title_mex'   => 'Título México',
				'slug'        => 'curso-de-ejemplo',
				'status'      => 1,
				'data'        => '{"foto":"my-picture","programa":"Descripción del programa"}'
			],
			[
				'id'          => 63,
				'title'       => 'Otro curso',
				'title_latam' => 'Otro Título latinoamérica',
				'title_mex'   => 'Título México',
				'slug'        => 'otro-curso',
				'status'      => 1,
				'data'        => '{"foto":"other-picture","programa":"Otra descripción del programa"}'
			]
		];

		$preparedData = array_map(
			function ($item)
			{
				return [
					'objectID' => $item['id'],
					'title'    => 'Prepared ' . $item['title']
				];
			},
			$data
		);

		$indexer = $this->indexerMock(['loadItems', 'prepareItems', 'updateIndexerItem']);

		$indexer->expects($this->at(0))
			->method('loadItems')
			->with($ids)
			->willReturn($data);

		$indexer->expects($this->at(1))
			->method('prepareItems')
			->with($data)
			->willReturn($preparedData);

		$indexer->expects($this->at(2))
			->method('updateIndexerItem')
			->with($preparedData[0]['objectID'], BaseIndexer::STATE_INDEXED);

		$indexer->expects($this->at(3))
			->method('updateIndexerItem')
			->with($preparedData[1]['objectID'], BaseIndexer::STATE_INDEXED);

		$index = $this->indexMock(['saveObjects']);
		$index->expects($this->once())
			->method('saveObjects')
			->with($preparedData);

		$reflection = new \ReflectionClass($indexer);
		$indexProperty = $reflection->getProperty('index');
		$indexProperty->setAccessible(true);
		$indexProperty->setValue($indexer, $index);

		$indexer->indexItems($ids);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function paramsReturnsCorrectParams()
	{
		$indexer = new SampleIndexer(44);

		$expectedParams = [
			'sample'     => 'value',
			'categories' => '1,2'
		];

		$row = [
			'id'     => 44,
			'name'   => 'Sample indexer',
			'params' => json_encode($expectedParams)
		];

		$reflection = new \ReflectionClass($indexer);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($indexer, $row);

		$this->assertEquals(new Registry($expectedParams), $indexer->params());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function prepareItemReturnsItem()
	{
		$item = ['id' => 66, 'title' => 'Test item'];

		$reflection = new \ReflectionClass($this->indexer);
		$method = $reflection->getMethod('prepareItem');
		$method->setAccessible(true);

		$this->assertSame($item, $method->invoke($this->indexer, $item));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function prepareItemsCallsPrepareItemForEachItem()
	{
		$items = [
			[
				'id' => 12,
				'title' => 'Indexable item'
			],
			[
				'id' => 21,
				'title' => 'Another indexable item'
			]
		];

		$indexer = $this->indexerMock(['prepareItem']);

		$indexer->expects($this->at(0))
			->method('prepareItem')
			->with($items[0])
			->willReturn($items[0]);

		$indexer->expects($this->at(1))
			->method('prepareItem')
			->with($items[1])
			->willReturn($items[1]);

		$reflection = new \ReflectionClass($indexer);
		$method = $reflection->getMethod('prepareItems');
		$method->setAccessible(true);

		$this->assertSame($items, $method->invoke($indexer, $items));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function rowLoadsDatathroughTable()
	{
		$dbData = [
			'id' => 23,
			'name' => 'Blog articles'
		];

		$table = $this->getMockBuilder(AlgoliaTableIndexer::class)
			->disableOriginalConstructor()
			->setMethods(['load', 'getProperties'])
			->getMock();

		$table->expects($this->at(0))
			->method('load')
			->with(23)
			->willReturn(true);

		$table->expects($this->at(1))
			->method('getProperties')
			->with(true)
			->willReturn($dbData);

		$indexer = $this->indexerMock(['table']);

		$reflection = new \ReflectionClass($indexer);
		$idProperty = $reflection->getProperty('id');
		$idProperty->setAccessible(true);

		$idProperty->setValue($indexer, 23);

		$indexer->expects($this->once())
			->method('table')
			->willReturn($table);

		$this->assertSame($dbData, $indexer->row());
	}

	/**
	 * @test
	 *
	 * @return void
	 *
	 * @expectedException  \RuntimeException
	 */
	public function rowThrowsExceptionIfLoadFails()
	{
		$dbData = [
			'id' => 23,
			'name' => 'Blog articles'
		];

		$table = $this->getMockBuilder(AlgoliaTableIndexer::class)
			->disableOriginalConstructor()
			->setMethods(['load', 'getError'])
			->getMock();

		$table->expects($this->at(0))
			->method('load')
			->with(33)
			->willReturn(false);

		$table->expects($this->at(1))
			->method('getError')
			->willReturn('Something is broken');

		$indexer = $this->indexerMock(['table']);

		$reflection = new \ReflectionClass($indexer);
		$idProperty = $reflection->getProperty('id');
		$idProperty->setAccessible(true);

		$idProperty->setValue($indexer, 33);

		$indexer->expects($this->once())
			->method('table')
			->willReturn($table);

		$this->assertSame($dbData, $indexer->row());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function updateIndexerItemUpdatesIndex()
	{
		$data = [
			'indexer_id' => 23,
			'object_id' => '22'
		];

		$table = $this->getMockBuilder(AlgoliaTableIndexer::class)
			->disableOriginalConstructor()
			->setMethods(['load', 'save'])
			->getMock();

		$table->expects($this->at(0))
			->method('load')
			->with($data)
			->willReturn(false);

		$data['state'] = BaseIndexer::STATE_REFRESH;

		$table->expects($this->at(1))
			->method('save')
			->with($data)
			->willReturn(true);

		$indexer = $this->indexerMock(['table']);

		$reflection = new \ReflectionClass($indexer);
		$idProperty = $reflection->getProperty('id');
		$idProperty->setAccessible(true);

		$idProperty->setValue($indexer, 23);

		$indexer->expects($this->once())
			->method('table')
			->willReturn($table);

		$method = $reflection->getMethod('updateIndexerItem');
		$method->setAccessible(true);

		$method->invoke($indexer, '22', BaseIndexer::STATE_REFRESH);
	}

	/**
	 * @test
	 *
	 * @return void
	 *
	 * @expectedException  \RuntimeException
	 */
	public function updateIndexerItemThrowsExceptionIfSaveFails()
	{
		$data = [
			'indexer_id' => 23,
			'object_id'  => '22'
		];

		$table = $this->getMockBuilder(AlgoliaTableIndexer::class)
			->disableOriginalConstructor()
			->setMethods(['load', 'save', 'getError'])
			->getMock();

		$table->expects($this->at(0))
			->method('load')
			->with($data)
			->willReturn(false);

		$table->expects($this->at(0))
			->method('getError')
			->willReturn('Something is broken!');

		$data['state'] = BaseIndexer::STATE_REFRESH;

		$table->expects($this->at(1))
			->method('save')
			->with($data)
			->willReturn(false);

		$indexer = $this->indexerMock(['table']);

		$reflection = new \ReflectionClass($indexer);
		$idProperty = $reflection->getProperty('id');
		$idProperty->setAccessible(true);

		$idProperty->setValue($indexer, 23);

		$indexer->expects($this->once())
			->method('table')
			->willReturn($table);

		$method = $reflection->getMethod('updateIndexerItem');
		$method->setAccessible(true);

		$method->invoke($indexer, '22', BaseIndexer::STATE_REFRESH);
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

		$this->indexer = new SampleIndexer(666);
	}

	/**
	 * Get a mocked index.
	 *
	 * @param   array   $methods  Mockable methdos
	 *
	 * @return  Index
	 */
	private function indexMock(array $methods = [])
	{
		return $this->getMockBuilder(Index::class)
			->disableOriginalConstructor()
			->setMethods($methods)
			->getMock();
	}

	/**
	 * Get a mocked indexer.
	 *
	 * @param   array   $methods  Mockable methods
	 *
	 * @return  CourseIndexer
	 */
	private function indexerMock(array $methods = ['indexItem'])
	{
		return $this->getMockBuilder(SampleIndexer::class)
			->disableOriginalConstructor()
			->setMethods($methods)
			->getMock();
	}
}
