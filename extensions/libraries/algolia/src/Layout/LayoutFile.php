<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Plugin
 *
 * @copyright  Copyright (C) 2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Algolia\Layout;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Joomla\CMS\Layout\FileLayout;

/**
 * Layout renderer class
 *
 * @since  __DEPLOY_VERSION__
 */
final class LayoutFile extends FileLayout
{
	/**
	 * Get the list of default includePaths
	 *
	 * @return  array
	 */
	public function getDefaultIncludePaths()
	{
		$defaultPaths = array();

		// (1 - highest priority) Received a custom high priority path ?
		if (!is_null($this->basePath))
		{
			$defaultPaths[] = rtrim($this->basePath, DIRECTORY_SEPARATOR);
		}

		// Component layouts & overrides if exist
		$component = $this->options->get('component', null);

		if (!empty($component))
		{
			// (2) Component template overrides path
			$defaultPaths[] = JPATH_THEMES . '/' . Factory::getApplication()->getTemplate() . '/html/layouts/' . $component;

			// (3) Component path
			if ($this->options->get('client') == 0)
			{
				$defaultPaths[] = JPATH_SITE . '/components/' . $component . '/layouts';
			}
			else
			{
				$defaultPaths[] = JPATH_ADMINISTRATOR . '/components/' . $component . '/layouts';
			}
		}

		// (4) Standard Joomla! layouts overriden
		$defaultPaths[] = JPATH_THEMES . '/' . Factory::getApplication()->getTemplate() . '/html/layouts';

		// (5) Our library path
		$defaultPaths[] = JPATH_LIBRARIES . '/algolia/layouts';

		// (6 - lower priority) Frontend base layouts
		$defaultPaths[] = JPATH_ROOT . '/layouts';

		return $defaultPaths;
	}
}
