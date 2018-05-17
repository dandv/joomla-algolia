<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  IndexableItem
 *
 * @copyright  Copyright (C) 2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Algolia;

defined('_JEXEC') || die;

use Joomla\CMS\Table\Table;
use Phproberto\Joomla\Algolia\Indexer\IndexerInterface;

/**
 * Represents an indexable item.
 *
 * @since  __DEPLOY_VERSION__
 */
class IndexableItem
{
	const STATE_INDEXED = 1;
	const STATE_REFRESH = 2;

	/**
	 * Source item data.
	 *
	 * @var  array
	 */
	protected $data;

	/**
	 * Prepared item data for indexer.
	 *
	 * @var  array
	 */
	protected $indexableData;

	/**
	 * Associated indexer.
	 *
	 * @var  IndexerInterface
	 */
	protected $indexer;

	/**
	 * Constructor.
	 *
	 * @param   array             $data     Array containing item information
	 * @param   IndexerInterface  $indexer  Indexer
	 */
	public function __construct(array $data, IndexerInterface $indexer)
	{
		$this->data    = $data;
		$this->indexer = $indexer;
	}

	/**
	 * Retrieve item data.
	 *
	 * @return  array
	 */
	public function data()
	{
		return $this->data;
	}

	/**
	 * Retrieve the associated indexer.
	 *
	 * @return  IndexerInterface
	 */
	public function indexer()
	{
		return $this->indexer;
	}

	/**
	 * Data to be indexed.
	 *
	 * @return  array
	 */
	public function indexableData()
	{
		if (null === $this->indexableData)
		{
			$this->indexableData = $this->prepareIndexableData();
		}

		return $this->indexableData;
	}

	/**
	 * Name to display for this item.
	 *
	 * @return  string
	 */
	public function name()
	{
		$indexableData = $this->indexableData();

		if (array_key_exists('name', $indexableData))
		{
			return $indexableData['name'];
		}

		if (array_key_exists('title', $indexableData))
		{
			return $indexableData['title'];
		}

		return $indexableData['objectID'];
	}

	/**
	 * Return this object identifier.
	 *
	 * @return  string
	 */
	public function objectId()
	{
		return $this->indexableData()['objectID'];
	}

	/**
	 * Prepare indexable data before sending it.
	 *
	 * @return  array
	 */
	protected function prepareIndexableData()
	{
		return $this->item;
	}

	/**
	 * Save this item into the database.
	 *
	 * @param   array  $data  Optional data to save
	 *
	 * @return  array
	 */
	public function save(array $data = [])
	{
		$indexableData = $this->indexableData();
		$table = $this->table('Item');

		$savedData = [
			'index_id' => (int) $this->indexer->index()->id(),
			'object_id'  => $indexableData['objectID']
		];

		// Try to load old item first
		$table->load($savedData);

		$savedData['name']  = $this->name();
		$savedData['data']  = json_encode($indexableData);

		$savedData = array_merge($savedData, $data);

		if (!$table->save($savedData))
		{
			throw new \RuntimeException("Error saving indexed item " . $table->getError());
		}

		return $table->getProperties(true);
	}

	/**
	 * Set the value of a data property.
	 *
	 * @param   string  $property  Property from $this->data
	 * @param   mixed   $value     Value to assign to the property
	 *
	 * @return  self
	 */
	public function set(string $property, $value)
	{
		$this->data[$property] = $value;

		return $this;
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
	protected function table($name = 'Item', $prefix = 'AlgoliaTable')
	{
		Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_algolia/tables');

		return Table::getInstance($name, $prefix);
	}
}
