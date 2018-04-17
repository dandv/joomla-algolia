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
 * Fields field.
 *
 * @since  __DEPLOY_VERSION__
 */
class Field extends \JFormFieldList
{
	/**
	 * Fields context.
	 *
	 * @var  string
	 */
	protected $context;

	/**
	 * Cached array of the category items.
	 *
	 * @var    array
	 */
	protected static $options = array();

	/**
	 * Method to get certain otherwise inaccessible properties from the form field object.
	 *
	 * @param   string  $name  The property name for which to get the value.
	 *
	 * @return  mixed  The property value or null.
	 */
	public function __get($name)
	{
		switch ($name)
		{
			case 'context':
				return $this->context;
		}

		return parent::__get($name);
	}

	/**
	 * Method to set certain otherwise inaccessible properties of the form field object.
	 *
	 * @param   string  $name   The property name for which to set the value.
	 * @param   mixed   $value  The value of the property.
	 *
	 * @return  void
	 */
	public function __set($name, $value)
	{
		switch ($name)
		{
			case 'context':
				$this->context = (string) $value;
				break;

			default:
				parent::__set($name, $value);
		}
	}

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
			static::$options[$hash] = array_merge(parent::getOptions(), $this->loadFields());
		}

		return static::$options[$hash];
	}

	/**
	 * Load extension from DB.
	 *
	 * @return  array
	 */
	protected function loadFields()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select($db->qn('id', 'value'))
			->select($db->qn('title', 'text'))
			->from('#__fields')
			->where('state = 1')
			->order('ordering, name');

		if ($this->context)
		{
			$query->where('context = ' . $db->quote($this->context));
		}

		return $db->setQuery($query)->loadObjectList() ?: [];
	}

	/**
	 * Method to attach a JForm object to the field.
	 *
	 * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the `<field>` tag for the form field object.
	 * @param   mixed             $value    The form field value to validate.
	 * @param   string            $group    The field name group control value. This acts as an array container for the field.
	 *                                      For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                      full field name would end up being "bar[foo]".
	 *
	 * @return  boolean  True on success.
	 */
	public function setup(\SimpleXMLElement $element, $value, $group = null)
	{
		if (!parent::setup($element, $value, $group))
		{
			return false;
		}

		$this->context = $this->getAttribute('context');

		return true;
	}
}
