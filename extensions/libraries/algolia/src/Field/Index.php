<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Field
 *
 * @copyright  Copyright (C) 2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Algolia\Field;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\FormHelper;

FormHelper::loadFieldClass('list');

/**
 * Algolia index field.
 *
 * @since  __DEPLOY_VERSION__
 */
class Index extends \JFormFieldList
{
	/**
	 * Cached array of the category items.
	 *
	 * @var    array
	 */
	protected static $options = array();

	/**
	 * Method to get the options to populate list
	 *
	 * @return  array  The field option objects.
	 */
	protected function getOptions()
	{
		$hash = md5($this->element);

		if (!isset(static::$options[$hash]))
		{
			static::$options[$hash] = array_merge(parent::getOptions(), $this->loadIndexes());
		}

		return static::$options[$hash];
	}

	/**
	 * Load indexes from DB.
	 *
	 * @return  array
	 */
	protected function loadIndexes()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('DISTINCT ' . $db->qn('index_name', 'value'))
			->select($db->qn('index_name', 'text'))
			->from('#__algolia_indexer')
			->where('state = 1')
			->order('index_name, name');

		return $db->setQuery($query)->loadObjectList() ?: [];
	}
}
