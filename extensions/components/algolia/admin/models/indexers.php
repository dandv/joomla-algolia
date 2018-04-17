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
use Joomla\CMS\MVC\Model\ListModel;

/**
 * Indexers model.
 *
 * @since  __DEPLOY_VERSION__
 */
class AlgoliaModelIndexers extends ListModel
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
				'item_id', 'i.iitem_id',
				'name', 'i.item_name'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 */
	public function getItems()
	{
		$items = parent::getItems();

		if (!$items)
		{
			return $items;
		}

		$lang  = Factory::getLanguage();

		foreach ($items as $item)
		{
			$source    = JPATH_PLUGINS . '/' . $item->extension_folder . '/' . $item->extension_element;
			$fileName = 'plg_' . $item->extension_folder . '_' . $item->extension_element;
			$lang->load($fileName . '.sys', JPATH_ADMINISTRATOR, null, false, true) || $lang->load($fileName . '.sys', $source, null, false, true);
		}

		return $items;
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
			->from($db->qn('#__algolia_indexer', 'i'))
			->innerjoin(
				$db->qn('#__extensions', 'e')
				. ' ON ' . $db->qn('e.extension_id') . ' = ' . $db->qn('i.extension_id')
			);

		$orderCol = $this->state->get('list.ordering', 'i.name');
		$orderDirn = $this->state->get('list.direction', 'asc');

		$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

		return $query;
	}
}
