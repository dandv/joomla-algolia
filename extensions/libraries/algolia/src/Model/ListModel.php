<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Model
 *
 * @copyright  Copyright (C) 2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Algolia\Model;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\MVC\Model\ListModel as BaseListModel;

/**
 * Base list model.
 *
 * @since  __DEPLOY_VERSION__
 */
abstract class ListModel extends BaseListModel
{
	/**
	 * Method to search items based on a state.
	 *
	 * Note: This method clears the model state.
	 *
	 * @param   array  $state  Array with filters + list options
	 *
	 * @return  array
	 */
	public function search($state = array())
	{
		// Clear current state and avoid populateState
		$this->{'state'} = new \JObject;
		$this->{'__state_set'} = true;

		foreach ($state as $key => $value)
		{
			$this->setState($key, $value);
		}

		return $this->getItems();
	}

	/**
	 * Retrieve an array of ids from a state key.
	 *
	 * @param   string  $key  State key
	 *
	 * @return  integer[]
	 */
	protected function idsFromState($key)
	{
		return array_values(
			array_filter(
				ArrayHelper::toInteger((array) $this->state->get($key)),
				function ($value)
				{
					return $value > 0;
				}
			)
		);
	}
}
