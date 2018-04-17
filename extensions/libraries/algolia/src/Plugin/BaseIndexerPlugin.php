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

use Joomla\CMS\Form\Form;
use Phproberto\Joomla\Algolia\Plugin\BasePlugin;

/**
 * Base plugin.
 *
 * @since  __DEPLOY_VERSION__
 */
abstract class BaseIndexerPlugin extends BasePlugin
{
	/**
	 * When injecting plugin params.
	 *
	 * @param   int   $indexerId  Indexer id
	 * @param   Form  $form       Form where parameters will be injected
	 *
	 * @return  void
	 */
	public function onAlgoliaInjectIndexerParams($indexerId, &$form)
	{
		$extension = $this->extension();

		if ($indexerId !== (int) $extension['extension_id'])
		{
			return true;
		}

		$formsFolder = $this->pluginPath() . '/forms';

		if (!is_dir($formsFolder))
		{
			return true;
		}

		Form::addFormPath($formsFolder);

		return $form->loadFile('params', true);
	}
}
