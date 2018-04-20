<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Plugin
 *
 * @copyright  Copyright (C) 2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Algolia\Table;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Table\Table;

/**
 * Base plugin.
 *
 * @since  __DEPLOY_VERSION__
 */
abstract class BaseTable extends Table
{
	/**
	 * Method to bind an associative array or object to the Table instance.This
	 * method only binds properties that are publicly accessible and optionally
	 * takes an array of properties to ignore when binding.
	 *
	 * @param   array|object  $src     An associative array or object to bind to the Table instance.
	 * @param   array|string  $ignore  An optional array or space separated list of properties to ignore while binding.
	 *
	 * @return  boolean  True on success.
	 *
	 * @throws  \InvalidArgumentException
	 */
	public function bind($src, $ignore = array())
	{
		// Autofill created_by and modified_by information
		$now = new \DateTime;
		$userId = Factory::getUser()->get('id');

		if (property_exists($this, 'created_by')
			&& empty($src['created_by']) && (is_null($this->created_by) || empty($this->created_by)))
		{
			$src['created_by']   = $userId;
		}

		if (property_exists($this, 'created_date')
			&& (empty($src['created_date']) || $src['created_date'] === '0000-00-00 00:00:00')
			&& (empty($this->created_date) || $this->created_date === '0000-00-00 00:00:00'))
		{
			$src['created_date'] = $now->format('Y-m-d H:i:s');
		}

		if (property_exists($this, 'modified_by') && empty($src['modified_by']))
		{
			$src['modified_by']   = $userId;
		}

		if (property_exists($this, 'modified_date')
			&& (empty($src['modified_date']) || $src['modified_date'] === '0000-00-00 00:00:00'))
		{
			$src['modified_date'] = $now->format('Y-m-d H:i:s');
		}

		return parent::bind($src, $ignore);
	}
}
