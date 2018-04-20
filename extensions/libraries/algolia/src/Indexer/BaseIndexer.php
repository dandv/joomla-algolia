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

use Joomla\CMS\Factory;
use AlgoliaSearch\Index;
use AlgoliaSearch\Client;
use Joomla\CMS\Table\Table;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;
use AlgoliaSearch\AlgoliaException;
use Phproberto\Joomla\Algolia\Indexer\Config\Config;
use Phproberto\Joomla\Algolia\Indexer\AlgoliaIndexerConfig;

/**
 * Base indexer.
 *
 * @since  __DEPLOY_VERSION__
 */
abstract class BaseIndexer
{
	const STATE_INDEXED = 1;
	const STATE_REFRESH = 2;

	/**
	 * Algolia search client.
	 *
	 * @var  Client
	 */
	protected $client;

	/**
	 * Indexer identifier.
	 *
	 * @var  integer
	 */
	protected $id;

	/**
	 * Algolia index.
	 *
	 * @var  Index
	 */
	protected $index;

	/**
	 * Indexer parameters.
	 *
	 * @var  Registry
	 */
	protected $params;

	/**
	 * Indexer DB row.
	 *
	 * @var  array
	 */
	protected $row;

	/**
	 * Constructor.
	 *
	 * @param   integer  $id  Indexer identifier
	 */
	public function __construct(int $id)
	{
		$this->id = $id;

		if (!$id)
		{
			throw new \InvalidArgumentException("Missing indexer identifier");
		}
	}

	/**
	 * Retrieve algolia configuration.
	 *
	 * @return  AlgoliaIndexerConfig
	 */
	public function algoliaConfig()
	{
		$row = $this->row();

		$config = [
			'application_id' => $row['application_id'],
			'api_key'        => $row['api_key'],
			'search_key'     => $row['search_key'],
			'index_name'     => $row['index_name']
		];

		return new AlgoliaIndexerConfig($config);
	}

	/**
	 * Retrieve the search client.
	 *
	 * @return  Client
	 */
	protected function client()
	{
		if (null === $this->client)
		{
			$algoliaConfig = $this->algoliaConfig();

			$this->client = new Client($algoliaConfig->applicationId(), $algoliaConfig->apiKey());
		}

		return $this->client;
	}

	/**
	 * Bind data to this indexer.
	 *
	 * @param   array   $data  Indexer information
	 *
	 * @return  self
	 */
	public function bind(array $data)
	{
		if (null === $this->row)
		{
			$this->row = [];
		}

		foreach ($data as $column => $value)
		{
			$this->row[$column] = $value;
		}

		return $this;
	}

	/**
	 * Get the database driver.
	 *
	 * @return  \JDatabaseDriver
	 *
	 * @codeCoverageIgnore
	 */
	protected function db()
	{
		return Factory::getDbo();
	}

	/**
	 * Delete an item.
	 *
	 * @param   integer  $id  [description]
	 *
	 * @return  void
	 */
	public function deleteItem(int $id)
	{
		$this->index()->deleteObject($id);
	}

	/**
	 * Delete items from the algolia index.
	 *
	 * @param   array   $ids  Array of items to delete
	 *
	 * @return  void
	 */
	public function deleteItems(array $ids)
	{
		$ids = array_values(array_filter(ArrayHelper::toInteger($ids)));

		$this->index()->deleteObjects($ids);
	}

	/**
	 * Retrieve this indexer identifier.
	 *
	 * @return  integer
	 */
	public function id()
	{
		return $this->id;
	}

	/**
	 * Retrieve the algolia index.
	 *
	 * @return  Index
	 */
	protected function index()
	{
		if (null === $this->index)
		{
			$row = $this->row();

			$this->index = $this->client()->initIndex($row['index_name']);
		}

		return $this->index;
	}

	/**
	 * Index an item in Algolia.
	 *
	 * @param   integer  $id  Id of the item to index
	 *
	 * @return  void
	 *
	 * @throws  AlgoliaException
	 */
	public function indexItem(int $id)
	{
		return $this->indexItems([$id]);
	}

	/**
	 * Index a list of items by their id.
	 *
	 * @param   integer[]   $ids  Items identifiers.
	 *
	 * @return  void
	 */
	public function indexItems(array $ids)
	{
		$items = $this->loadItems($ids);

		if (empty($items))
		{
			return;
		}

		$preparedItems = $this->prepareItems($items);
		$this->index()->saveObjects($preparedItems);

		array_map(
			function ($preparedItem)
			{
				$this->updateIndexerItem($preparedItem['objectID'], self::STATE_INDEXED);
			},
			$preparedItems
		);
	}


	/**
	 * Load items by their id.
	 *
	 * @param   array   $ids  Items identifiers
	 *
	 * @return  array
	 */
	protected function loadItems(array $ids)
	{
		$ids = array_values(array_filter(ArrayHelper::toInteger($ids)));

		if (empty($ids))
		{
			return [];
		}

		return $this->searchItems(['filter' => ['ids' => $ids]]);
	}

	/**
	 * Retrieve this indexer params.
	 *
	 * @return  Registry
	 */
	public function params()
	{
		if (null === $this->params)
		{
			$this->params = new Registry($this->row()['params']);
		}

		return $this->params;
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
		return $item;
	}

	/**
	 * Prepare an array of items to be indexed.
	 *
	 * @param   array   $items  Array containing items information.
	 *
	 * @return  array
	 */
	protected function prepareItems(array $items)
	{
		return array_map([$this, 'prepareItem'], $items);
	}

	/**
	 * Retrieve the indexer information from the DB.
	 *
	 * @return  array
	 */
	public function row()
	{
		if (null === $this->row)
		{
			$table = $this->table();

			if (!$table->load($this->id))
			{
				throw new \RuntimeException("Error loading indexer from DB: " . $table->getError());
			}

			$this->row = $table->getProperties(true);
		}

		return $this->row;
	}

	/**
	 * Search indexable items.
	 *
	 * @param   array   $search  Array with filtering information
	 *
	 * @return  array
	 */
	abstract public function searchItems(array $search);

	/**
	 * Searh items and index them.
	 *
	 * @param   array  $search  Array with filtering information
	 *
	 * @return   array
	 */
	public function searchAndIndexItems(array $search)
	{
		$items = $this->searchItems($search);

		if (empty($items))
		{
			return [];
		}

		$preparedItems = $this->prepareItems($items);
		$this->index()->saveObjects($preparedItems);

		return array_map(
			function ($preparedItem)
			{
				$this->updateIndexerItem($preparedItem['objectID'], self::STATE_INDEXED);

				return $preparedItem['objectID'];
			},
			$preparedItems
		);
	}

	/**
	 * Load associated table.
	 *
	 * @param   string  $name    Table name
	 * @param   string  $prefix  Table prefix
	 *
	 * @return  \AlgoliaTableIndexer
	 *
	 * @codeCoverageIgnore
	 */
	protected function table($name = 'Indexer', $prefix = 'AlgoliaTable')
	{
		Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_algolia/tables');

		return Table::getInstance($name, $prefix);
	}

	/**
	 * Update indexer item.
	 *
	 * @param   string   $objectId  Object identifier
	 * @param   integer  $state     State to assign to the item
	 *
	 * @return  void
	 *
	 * @throws  \RuntimeException
	 */
	protected function updateIndexerItem(string $objectId, int $state)
	{
		$table = $this->table('Item');

		$data = [
			'indexer_id' => (int) $this->id,
			'object_id'  => $objectId
		];

		// Try to load old item first
		$table->load($data);

		$data['state'] = $state;

		if (!$table->save($data))
		{
			throw new \RuntimeException("Error saving indexed item " . $table->getError());
		}
	}
}
