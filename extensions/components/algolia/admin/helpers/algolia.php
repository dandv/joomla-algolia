<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Component.Backend
 *
 * @copyright  Copyright (C) 2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

defined('_JEXEC') || die;

use Joomla\CMS\Language\Text;

/**
 * Algolia component helper.
 *
 * @since  __DEPLOY_VERSION__
 */
class AlgoliaHelper extends JHelperContent
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName  The name of the active view.
	 *
	 * @return  void
	 */
	public static function addSubmenu($vName)
	{
		JHtmlSidebar::addEntry(
			Text::_('Indexes'),
			'index.php?option=com_algolia&view=indexes',
			$vName == 'indexes'
		);

		JHtmlSidebar::addEntry(
			Text::_('Items'),
			'index.php?option=com_algolia&view=items',
			$vName == 'items'
		);
	}
}
