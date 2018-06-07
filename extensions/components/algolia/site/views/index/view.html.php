<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Component.Frontend
 *
 * @copyright  Copyright (C) 2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView;
use Phproberto\Joomla\Algolia\Entity\Index;

/**
 * Index view.
 *
 * @since  __DEPLOY_VERSION__
 */
class AlgoliaViewIndex extends HtmlView
{
	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 */
	public function display($tpl = null)
	{
		$id = Factory::getApplication()->input->getInt('id');

		$this->index = Index::find($id);

		return parent::display($tpl);
	}
}
