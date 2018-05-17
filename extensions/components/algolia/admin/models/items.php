<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Component.Backend
 *
 * @copyright  Copyright (C) 2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

defined('_JEXEC') || die;

use Joomla\Utilities\ArrayHelper;
use Phproberto\Joomla\Algolia\Entity\Item;
use Phproberto\Joomla\Algolia\Entity\Index;
use Phproberto\Joomla\Algolia\Model\ListModel;

/**
 * Items model.
 *
 * @since  __DEPLOY_VERSION__
 */
class AlgoliaModelItems extends ListModel
{
	/**
	 * Name of the filter form to load
	 *
	 * @var  string
	 */
	protected $filterFormName = 'filter_items';

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id',
				'index_id', 'i.index_id',
				'object_id', 'i.object_id',
				'index',
				'i.name',
				'state'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  \JDatabaseQuery
	 */
	protected function getListQuery()
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true)
			->select('item.*')
			->select($db->qn('i.name', 'index_name'))
			->select($db->qn('e.element', 'extension_element'))
			->from($db->qn('#__algolia_item', 'item'))
			->innerjoin(
				$db->qn('#__algolia_index', 'i')
				. ' ON ' . $db->qn('i.id') . ' = ' . $db->qn('item.index_id')
			)
			->innerjoin(
				$db->qn('#__extensions', 'e')
				. ' ON ' . $db->qn('i.extension_id') . ' = ' . $db->qn('e.extension_id')
			);

		$ids = $this->idsFromState('filter.id');

		if ($ids)
		{
			$query->where($db->qn('item.id') . ' IN(' . implode(',', $ids) . ')');
		}

		// Filter: state
		$states = ArrayHelper::toInteger(
			array_filter(
				(array) $this->state->get('filter.state'),
				'is_numeric'
			)
		);

		if ($states)
		{
			$query->where($db->qn('item.state') . ' IN(' . implode(',', $states) . ')');
		}

		$indexesIds = $this->idsFromState('filter.index');

		if ($indexesIds)
		{
			$query->where($db->qn('item.index_id') . ' IN(' . implode(',', $indexesIds) . ')');
		}

		// Search
		if ($search = $this->state->get('filter.search'))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('item.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
				$query->where(
					'('
						. ' item.name LIKE ' . $search
						. ' OR i.name LIKE ' . $search
						. ' OR item.object_id LIKE ' . $search
					. ')'
				);
			}
		}

		$orderCol = $this->state->get('list.ordering', 'i.name, item.object_id');
		$orderDirn = $this->state->get('list.direction', 'ASC');

		$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

		return $query;
	}

	/**
	 * Reindex a list of items.
	 *
	 * @param   array  $ids  Items identifiers
	 *
	 * @return  boolean
	 */
	public function reindex($ids)
	{
		$itemsData = $this->search(
			[
				'filter.id' => $ids
			]
		);

		if (!$itemsData)
		{
			return true;
		}

		foreach ($itemsData as $itemData)
		{
			$item = Item::find($itemData->id)->bind($itemData);

			$item->index()->indexer()->indexItems([$itemData->{'object_id'}]);
		}

		return true;
	}
}
