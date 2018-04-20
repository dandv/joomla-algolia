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
 * Algolia application field.
 *
 * @since  __DEPLOY_VERSION__
 */
class Application extends \JFormFieldList
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
			static::$options[$hash] = array_merge(parent::getOptions(), $this->loadApplicationIds());
		}

		return static::$options[$hash];
	}

	/**
	 * Load application ids from DB.
	 *
	 * @return  array
	 */
	protected function loadApplicationIds()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('DISTINCT ' . $db->qn('application_id', 'value'))
			->select($db->qn('application_id', 'text'))
			->from('#__algolia_indexer')
			->where('state = 1')
			->order('application_id, name');

		return $db->setQuery($query)->loadObjectList() ?: [];
	}
}
