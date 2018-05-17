<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Entity
 *
 * @copyright  Copyright (C) 2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Algolia\Entity;

defined('_JEXEC') || die;

use AlgoliaSearch\Index as AlgoliaIndex;
use AlgoliaSearch\Client;
use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Algolia\Entity\Item;
use Phproberto\Joomla\Algolia\Entity\BaseEntity;
use Phproberto\Joomla\Algolia\Indexer\AlgoliaConfig;
use Phproberto\Joomla\Algolia\Finder\FinderInterface;

/**
 * Indexer Entity.
 *
 * @since   __DEPLOY_VERSION__
 */
class Index extends BaseEntity
{
	/**
	 * Associated algolia client.
	 *
	 * @var  Client
	 */
	protected $algoliaClient;

	/**
	 * Associated Algolia config.
	 *
	 * @var  AlgoliaConfig
	 */
	protected $algoliaConfig;

	/**
	 * Associated Algolia index.
	 *
	 * @var  Index
	 */
	protected $algoliaIndex;

	/**
	 * Items indexed by this indexer.
	 *
	 * @var  Collection
	 */
	protected $items;

	/**
	 * Get the associated Algolia client.
	 *
	 * @return  Client
	 */
	public function algoliaClient()
	{
		if (null === $this->algoliaClient)
		{
			$algoliaConfig = $this->algoliaConfig();
			$algoliaConfig->validate();

			$this->algoliaClient = new Client($algoliaConfig->applicationId(), $algoliaConfig->apiKey());
		}

		return $this->algoliaClient;
	}

	/**
	 * Get the associated Algolia config.
	 *
	 * @return  AlgoliaConfig
	 */
	public function algoliaConfig()
	{
		if (null === $this->algoliaConfig)
		{
			$row = $this->all();

			$config = [
				'application_id' => $row['application_id'],
				'api_key'        => $row['api_key'],
				'search_key'     => $row['search_key'],
				'index_name'     => $row['index_name']
			];

			$this->algoliaConfig = new AlgoliaConfig($config);
		}

		return $this->algoliaConfig;
	}

	/**
	 * Algolia index.
	 *
	 * @return  AlgoliaIndex
	 */
	public function algoliaIndex()
	{
		if (null === $this->algoliaIndex)
		{
			$this->algoliaIndex = $this->algoliaClient()->initIndex($this->get('index_name'));
		}

		return $this->algoliaIndex;
	}

	/**
	 * Delete associated items.
	 *
	 * @param   array  $ids  Only remove specified items.
	 *
	 * @return  void
	 */
	public function deleteItems(array $ids = [])
	{
		// Delete all
		if (!$ids)
		{
			$ids = $this->items()->ids();
		}

		$this->algoliaIndex()->deleteObjects($ids);

		$table = $this->component()->table('item');

		array_map(
			function ($id) use ($table) {
				if (!$table->delete($id))
				{
					throw new \RuntimeException('Error deleting items: ' . $table->getError());
				}
			},
			$ids
		);

		$this->items = null;
	}

	/**
	 * Find a list of indexable items
	 *
	 * @param   array   $options  Options to search by
	 *
	 * @return  array
	 */
	public function findIndexableItems(array $options = [])
	{
		return [];
	}

	/**
	 * Get the associated indexer.
	 *
	 * @return  mixed
	 */
	public function indexer()
	{
		$indexers = $this->trigger('onAlgoliaGetIndexer', [(int) $this->get('extension_id')]);

		if (!$indexers)
		{
			throw new \RuntimeException('Unable to find indexer');
		}

		return $indexers[0];
	}

	/**
	 * Index specified items.
	 *
	 * @param   array   $ids  [description]
	 *
	 * @return  void
	 */
	public function indexItems(array $ids)
	{
		return;
	}

	/**
	 * Retrieve the items indexed by this indexer.
	 *
	 * @return  Collecion
	 */
	public function items()
	{
		if (null === $this->items)
		{
			$this->items = $this->loadItems();
		}

		return $this->items;
	}

	/**
	 * Load items from database.
	 *
	 * @return  Collection
	 */
	protected function loadItems()
	{
		return $this->searchItems(
			[
				'list.limit' => 0,
				'list.start' => 0
			]
		);
	}

	/**
	 * Search items inside this indexer.
	 *
	 * @param   array  $modelState  State for the list model
	 *
	 * @return  Collection
	 */
	public function searchItems(array $modelState = [])
	{
		$items = new Collection;

		if (!$this->hasId())
		{
			return $items;
		}

		// Defaults
		$modelState = array_merge(
			[
				'filter.state' => 1
			],
			$modelState
		);

		$modelState['filter.indexer'] = $this->id();

		$model = $this->component()->model('items');

		$itemsData = $model->search($modelState);

		foreach ($itemsData as $itemData)
		{
			$item = new Item;
			$item->bind($itemData);

			$items->add($item);
		}

		return $items;
	}
}
