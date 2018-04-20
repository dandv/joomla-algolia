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
				'object_id', 'i.object_id'
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
			->select($db->qn('e.element', 'extension_element'))
			->from($db->qn('#__algolia_indexer_item', 'ii'))
			->innerjoin(
				$db->qn('#__algolia_indexer', 'i')
				. ' ON ' . $db->qn('i.id') . ' = ' . $db->qn('ii.indexer_id')
			)
			->innerjoin(
				$db->qn('#__extensions', 'e')
				. ' ON ' . $db->qn('i.extension_id') . ' = ' . $db->qn('e.extension_id')
			);

		$orderCol = $this->state->get('list.ordering', 'i.name, ii.object_id');
		$orderDirn = $this->state->get('list.direction', 'asc');

		$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

		return $query;
	}
}
