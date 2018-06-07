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
	 * Searh items and index them.
	 *
	 * @param   array  $search  Array with filtering information
	 *
	 * @return   array
	 */
	public function findAndIndexItems(array $search)
	{
		return $this->indexItems($this->findItems($search));
	}

	/**
	 * Search indexable items.
	 *
	 * @param   array   $options  Array with search filders.
	 *
	 * @return  array
	 */
	public function findItems(array $options = [])
	{
		return $this->finder()->find($options);
	}

	/**
	 * Indexable items finder.
	 *
	 * @return  FinderInterface
	 */
	abstract public function finder();

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
	 * Get an instance of the associated indexable entity.
	 *
	 * @param   array   $data  Entity data
	 *
	 * @return  IndexableItem
	 */
	public function indexableItem(array $data)
	{
		return new IndexableItem($data, $this);
	}

	/**
	 * Index an indexable item.
	 *
	 * @param   IndexableItem  $indexableItem  Indexable item.
	 *
	 * @return  string
	 */
	public function indexItem(IndexableItem $indexableItem)
	{
		return $this->indexItems([$indexableItem])[0];
	}

	/**
	 * Index an array of indexable items.
	 *
	 * @param   IndexableItem[]  $indexableItems  Indexable items.
	 *
	 * @return  array
	 */
	public function indexItems(array $indexableItems)
	{
		if (empty($indexableItems))
		{
			return [];
		}

		$this->index()->algoliaIndex()->saveObjects(
			array_map(
				function ($indexableItem)
				{
					return $indexableItem->indexableData();
				},
				$indexableItems
			)
		);

		return array_map(
			function ($indexableItem)
			{
				$indexableItem->save(['state' => IndexableItem::STATE_INDEXED]);

				return $indexableItem->objectId();
			},
			$indexableItems
		);
	}
}
