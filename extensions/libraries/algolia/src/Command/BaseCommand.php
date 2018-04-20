<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Plugin
 *
 * @copyright  Copyright (C) 2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Algolia\Command;

defined('_JEXEC') || die;

use Joomla\CMS\Application\CliApplication;
use Joomla\CMS\Plugin\PluginHelper;

/**
 * Base Cli application.
 *
 * @since  __DEPLOY_VERSION__
 *
 * @codeCoverageIgnore
 */
abstract class BaseCommand extends CliApplication
{
	/**
	 * Gets the value of a user state variable.
	 *
	 * @param   string  $key      The key of the user state variable.
	 * @param   string  $request  The name of the variable passed in a request.
	 * @param   string  $default  The default value for the variable if not found. Optional.
	 * @param   string  $type     Filter for the variable, for valid values see {@link JFilterInput::clean()}. Optional.
	 *
	 * @return  object  The request user state.
	 */
	public function getUserStateFromRequest($key, $request, $default = null, $type = 'none')
	{
		return $default;
	}

	/**
	 * Gets the template.
	 *
	 * @return  string  The template
	 */
	public function getTemplate()
	{
		return 'system';
	}

	/**
	 * Check the client interface by name.
	 *
	 * @param   string  $identifier  String identifier for the application interface
	 *
	 * @return  boolean  True if this application is of the given type client interface.
	 */
	public function isClient($identifier)
	{
		return false;
	}

	/**
	 * Stub method for allow cache.
	 *
	 * @return  void
	 */
	public function allowCache()
	{
	}

	/**
	 * Stub method for set header.
	 *
	 * @return  void
	 */
	public function setHeader()
	{
	}

	/**
	 * Sets the body.
	 *
	 * @param   string  $data  The data
	 *
	 * @return  void
	 */
	public function setBody($data)
	{
		$this->body = $data;
	}

	/**
	 * Returns a string representation of the app.
	 *
	 * @return  string  The string
	 */
	public function toString()
	{
		return $this->body;
	}

	/**
	 * Trigger an event.
	 *
	 * @param   string  $event      Event name
	 * @param   array   $arguments  Arguments to the event
	 *
	 * @return  array
	 */
	protected function trigger($event, $arguments)
	{
		PluginHelper::importPlugin('algolia_indexer');
		$dispatcher = \JEventDispatcher::getInstance();

		return $dispatcher->trigger('onAlgoliaIndexItems', $arguments);
	}

	/**
	 * The app is not admin.
	 *
	 * @return  boolean
	 */
	public function isAdmin()
	{
		return false;
	}

	/**
	 * The app is not site.
	 *
	 * @return  boolean
	 */
	public function isSite()
	{
		return false;
	}
}
