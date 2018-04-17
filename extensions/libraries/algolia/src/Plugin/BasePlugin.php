<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Plugin
 *
 * @copyright  Copyright (C) 2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Algolia\Plugin;

defined('_JEXEC') || die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Application\CMSApplication;

/**
 * Base plugin.
 *
 * @since  __DEPLOY_VERSION__
 */
abstract class BasePlugin extends CMSPlugin
{
	/**
	 * Application object
	 *
	 * @var  CMSApplication
	 */
	protected $app;

	/**
	 * Load the language file on instantiation.
	 *
	 * @var  boolean
	 */
	protected $autoloadLanguage = true;

	/**
	 * Database object
	 *
	 * @var  \JDatabaseDriver
	 */
	protected $db;

	/**
	 * Extension identifier in the #__extensions table.
	 *
	 * @var  array
	 */
	protected $extension;

	/**
	 * Path to the plugin folder.
	 *
	 * @var  string
	 */
	protected $pluginPath;

	/**
	 * Get current plugin data in the #__extensions table.
	 *
	 * @return  array
	 *
	 * @throws  \RuntimeException
	 */
	protected function extension()
	{
		if (null === $this->extension)
		{
			$db = $this->db;

			$query = $db->getQuery(true)
				->select('*')
				->from($db->qn('#__extensions'))
				->where($db->qn('folder') . ' = ' . $db->q($this->_type))
				->where($db->qn('element') . ' = ' . $db->q($this->_name));

			$db->setQuery($query);

			$this->extension = $db->loadAssoc();

			if (empty($this->extension))
			{
				throw new \RuntimeException('Error loading plugin `' . $this->_type . '/' . $this->_name . '` data from DB');
			}
		}

		return $this->extension;
	}

	/**
	 * Get the path to the folder of the current plugin.
	 *
	 * @return  string
	 */
	protected function pluginPath() : string
	{
		if (null === $this->pluginPath)
		{
			$reflection = new \ReflectionClass($this);

			$this->pluginPath = dirname($reflection->getFileName());
		}

		return $this->pluginPath;
	}
}
