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
use Joomla\Utilities\ArrayHelper;
use AlgoliaSearch\AlgoliaException;
use Phproberto\Joomla\Algolia\Entity\Index;
use Phproberto\Joomla\Algolia\IndexableItem;
use Phproberto\Joomla\Algolia\Indexer\Config;
use Phproberto\Joomla\Algolia\Indexer\AlgoliaConfig;
use Phproberto\Joomla\Algolia\Finder\FinderInterface;

/**
 * Base indexer.
 *
 * @since  __DEPLOY_VERSION__
 */
abstract class BaseIndexer
{
	/**
	 * Indexer configuration.
	 *
	 * @var  Config
	 */
	protected $config;

	/**
	 * Assciated index.
	 *
	 * @var  Index
	 */
	protected $index;

	/**
	 * Constructor.
	 *
	 * @param   Index  $index  Associated index
	 */
	public function __construct(Index $index)
	{
		$this->index = $index;
	}

	/**
	 * Get associated index.
	 *
	 * @return  Index
	 */
	public function index()
	{
		return $this->index;
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
		$this->index()->deleteItems($ids);
	}

	/**
	 * Indexable items finder.
	 *
	 * @return  FinderInterface
	 */
	abstract public function finder();

	/**
	 * Index a list of items by their id.
	 *
	 * @param   integer[]   $ids  Items identifiers.
	 *
	 * @return  void
	 */
	public function indexItems(array $ids)
	{
		$indexableItems = $this->finder()->find(
			[
				'filter' => ['ids' => $ids]
			]
		);

		if (empty($indexableItems))
		{
			return;
		}

		// Index items
		$this->index()->algoliaIndex()->saveObjects(
			array_map(
				function ($indexableItem)
				{
					return $indexableItem->indexableData();
				},
				$indexableItems
			)
		);

		// Update saved items
		array_map(
			function ($indexableItem)
			{
				$indexableItem->save(['state' => IndexableItem::STATE_INDEXED]);

			},
			$indexableItems
		);
	}

	/**
	 * Searh items and index them.
	 *
	 * @param   array  $search  Array with filtering information
	 *
	 * @return   array
	 */
	public function searchAndIndexItems(array $search)
	{
		$indexableItems = $this->finder()->find($search);

		if (empty($indexableItems))
		{
			return [];
		}

		// Index items
		$this->algoliaIndex()->saveObjects(
			array_map(
				function ($indexableItem)
				{
					return $indexableItem->indexableData();
				},
				$indexableItems
			)
		);

		// Update saved items
		return array_map(
			function ($indexableItem)
			{
				$indexableItem->save(['state' => IndexableItem::STATE_INDEXED]);

				return $indexableItem->indexableData()['objectID'];
			},
			$indexableItems
		);
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
			'index_id'  => $this->index()->id(),
			'object_id' => $objectId
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
