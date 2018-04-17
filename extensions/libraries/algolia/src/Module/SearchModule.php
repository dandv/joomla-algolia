<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Module
 *
 * @copyright  Copyright (C) 2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Algolia\Module;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Joomla\Registry\Registry;
use Joomla\CMS\Layout\FileLayout;
use Structuralia\Joomla\Algolia\Config\CourseIndexerConfig;

/**
 * Module to search.
 *
 * @since   __DEPLOY_VERION__
 */
class SearchModule
{
	/**
	 * Module parameters.
	 *
	 * @var  Registry
	 */
	protected $params;

	/**
	 * Module name.
	 *
	 * @const
	 */
	const MODULE_NAME = 'mod_structuralia_algolia_search';

	/**
	 * Constructor.
	 *
	 * @param   Registry|null  $params  [description]
	 */
	public function __construct(Registry $params = null)
	{
		$this->params = $params ?: new Registry;
	}

	/**
	 * Retrieve the alglolia client+index configuration.
	 *
	 * @return  array
	 */
	public function algoliaConfig()
	{
		return CourseIndexerConfig::instance();
	}

	/**
	 * Debug a module layout render.
	 *
	 * @param   string  $layoutId  Layout identifier
	 * @param   array   $data      Optional data
	 *
	 * @return  string
	 */
	public function debug(string $layoutId, array $data = [])
	{
		return $this->renderer($layoutId)->debug(array_merge($this->layoutData(), $data));
	}


	/**
	 * Data that will be passed to the layouts.
	 *
	 * @return  array
	 */
	protected function layoutData()
	{
		return [
			'moduleInstance' => $this
		];
	}

	/**
	 * Get the paths where a layout can be stored.
	 *
	 * @return  array
	 */
	protected function layoutPaths()
	{
		return [
			JPATH_BASE . '/templates/' . Factory::getApplication()->getTemplate() . '/html/' . self::MODULE_NAME,
			JPATH_BASE . '/modules/' . self::MODULE_NAME . '/tmpl'
		];
	}

	/**
	 * Retrieve active mercado.
	 *
	 * @return  string
	 *
	 * @codeCoverageIgnore
	 */
	public function mercado()
	{
		require_once JPATH_SITE . '/components/com_str/helpers/str.php';

		return \STRHelper::mercado();
	}

	/**
	 * Retrieve the value of a param.
	 *
	 * @param   string  $name     Name of the parameter
	 * @param   mixed   $default  Default value
	 *
	 * @return  mixed
	 */
	public function param(string $name, $default = null)
	{
		return $this->params->get($name, $default);
	}

	/**
	 * Retrieve module parameters.
	 *
	 * @return  Registry
	 */
	public function params()
	{
		return $this->params;
	}

	/**
	 * Render a module layout.
	 *
	 * @param   string  $layoutId  Layout identifier
	 * @param   array   $data      Optional data
	 *
	 * @return  string
	 */
	public function render(string $layoutId, array $data = [])
	{
		return $this->renderer($layoutId)->render(array_merge($this->layoutData(), $data));
	}

	/**
	 * Retrieve the active renderer.
	 *
	 * @param   string  $layoutId  Layout identifier
	 *
	 * @return  FileLayout
	 */
	protected function renderer(string $layoutId)
	{
		$renderer = new FileLayout($layoutId);
		$renderer->setIncludePaths($this->layoutPaths());

		return $renderer;
	}
}
