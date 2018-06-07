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

use Phproberto\Joomla\Algolia\Layout\LayoutFile;
use Joomla\CMS\Layout\LayoutHelper as BaseLayoutHelper;

/**
 * Layout helper for fast rendering
 *
 * @since  __DEPLOY_VERSION__
 */
class LayoutHelper extends BaseLayoutHelper
{
	/**
	 * Method to debug a rendered layout.
	 *
	 * @param   string  $layoutFile   Dot separated path to the layout file, relative to base path
	 * @param   object  $displayData  Object which properties are used inside the layout file to build displayed output
	 * @param   string  $basePath     Base path to use when loading layout files
	 * @param   mixed   $options      Optional custom options to load. JRegistry or array format
	 *
	 * @return  string
	 */
	public static function debug($layoutFile, $displayData = array(), $basePath = '', $options = null)
	{
		$basePath = empty($basePath) ? self::$defaultBasePath : $basePath;

		// Make sure we send null to JLayoutFile if no path set
		$basePath = empty($basePath) ? null : $basePath;
		$layout = new LayoutFile($layoutFile, $basePath, $options);
		$renderedLayout = $layout->debug($displayData);

		return $renderedLayout;
	}

	/**
	 * Method to render the layout.
	 *
	 * @param   string  $layoutFile   Dot separated path to the layout file, relative to base path
	 * @param   object  $displayData  Object which properties are used inside the layout file to build displayed output
	 * @param   string  $basePath     Base path to use when loading layout files
	 * @param   mixed   $options      Optional custom options to load. JRegistry or array format
	 *
	 * @return  string
	 */
	public static function render($layoutFile, $displayData = array(), $basePath = '', $options = null)
	{
		$basePath = empty($basePath) ? self::$defaultBasePath : $basePath;

		// Make sure we send null to JLayoutFile if no path set
		$basePath = empty($basePath) ? null : $basePath;
		$layout = new LayoutFile($layoutFile, $basePath, $options);
		$renderedLayout = $layout->render($displayData);

		return $renderedLayout;
	}
}
