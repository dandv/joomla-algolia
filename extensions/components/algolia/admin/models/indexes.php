<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Component.Backend
 *
 * @copyright  Copyright (C) 2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Phproberto\Joomla\Algolia\Entity\Index;
use Phproberto\Joomla\Algolia\Model\ListModel;

/**
 * Indexes model.
 *
 * @since  __DEPLOY_VERSION__
 */
class AlgoliaModelIndexes extends ListModel
{
	/**
	 * Name of the filter form to load
	 *
	 * @var  string
	 */
	protected $filterFormName = 'filter_indexes';

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
				'index_id', 'i.index_id',
				'item_id', 'i.iitem_id',
				'name', 'i.name',
				'state', 'i.state',
				'index_name', 'i.index_name',
				'extension_id',
				'extension_name',
				'i.created_date',
				'application_id', 'i.application_id',
				'i.modified_date',
				'i.id'
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
			->select('i.*')
			->select($db->qn('e.name', 'extension_name'))
			->select($db->qn('e.folder', 'extension_folder'))
			->select($db->qn('e.element', 'extension_element'))
			->from($db->qn('#__algolia_index', 'i'))
			->innerjoin(
				$db->qn('#__extensions', 'e')
				. ' ON ' . $db->qn('e.extension_id') . ' = ' . $db->qn('i.extension_id')
			);

		$ids = $this->idsFromState('filter.id');

		if ($ids)
		{
			$query->where($db->qn('i.id') . ' IN(' . implode(',', $ids) . ')');
		}

		// Search
		if ($search = $this->state->get('filter.search'))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('i.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
				$query->where(
					'('
						. ' i.name LIKE ' . $search
						. ' OR e.name LIKE ' . $search
						. ' OR i.application_id LIKE ' . $search
						. ' OR i.index_name LIKE ' . $search
					. ')'
				);
			}
		}

		// State
		$state = $this->getState('filter.state');

		if (is_numeric($state))
		{
			$query->where('i.state = ' . (int) $state);
		}

		// Index name
		if ($indexName = $this->getState('filter.index_name'))
		{
			$query->where('i.index_name = ' . $db->q($indexName));
		}

		// Extension
		if ($extensionId = $this->getState('filter.extension_id'))
		{
			$query->where('i.extension_id = ' . $db->q($extensionId));
		}

		// ApplicationId
		if ($applicationId = $this->getState('filter.application_id'))
		{
			$query->where('i.application_id = ' . $db->q($applicationId));
		}

		$orderCol = $this->state->get('list.ordering', 'i.name');
		$orderDirn = $this->state->get('list.direction', 'asc');

		$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

		return $query;
	}

	/**
	 * Reindex a list of items.
	 *
	 * @param   array  $ids  Items identifiers
	 *
	 * @return  array
	 */
	public function reindex($ids)
	{
		$itemsData = $this->search(
			[
				'filter.id' => $ids
			]
		);

		$indexed = [];

		foreach ($itemsData as $itemData)
		{
			$index = Index::find($itemData->id)->bind($itemData);

			$indexer = $index->indexer();
			$indexableItems = $indexer->finder()->find();

			$indexed = array_merge($indexed, $indexer->indexItems($indexableItems));
		}

		return $indexed;
	}
}
