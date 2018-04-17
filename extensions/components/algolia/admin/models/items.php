<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Component.Backend
 *
 * @copyright  Copyright (C) 2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

defined('_JEXEC') || die;

use Joomla\CMS\MVC\Model\ListModel;

/**
 * Items model.
 *
 * @since  __DEPLOY_VERSION__
 */
class AlgoliaModelItems extends ListModel
{
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
				'indexer_id', 'i.indexer_id',
				'item_id', 'i.item_id',
				'name', 'i.item_name'
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
			->select('ii.*')
			->select($db->qn('i.name', 'indexer_name'))
			->from($db->qn('#__algolia_indexer_item', 'ii'))
			->innerjoin(
				$db->qn('#__algolia_indexer', 'i')
				. ' ON ' . $db->qn('i.id') . ' = ' . $db->qn('ii.indexer_id')
			);

		$orderCol = $this->state->get('list.ordering', 'i.name');
		$orderDirn = $this->state->get('list.direction', 'asc');

		$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

		return $query;
	}
}
