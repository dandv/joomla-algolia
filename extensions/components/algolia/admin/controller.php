<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Component.Backend
 *
 * @copyright  Copyright (C) 2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;

/**
 * Front controller.
 *
 * @since  __DEPLOY_VERSION__
 */
class AlgoliaController extends BaseController
{
	/**
	 * Typical view method for MVC based architecture
	 *
	 * This function is provide as a default implementation, in most cases
	 * you will need to override it in your own controllers.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  BaseController  A controller object to support chaining.
	 */
	public function display($cachable = false, $urlparams = array())
	{
		$app = Factory::getApplication();
		$input = $app->input;

		$input->set('view', $input->get('view', 'indexes'));
		$input->set('task', $input->get('task', 'display'));

		return parent::display($cachable, $urlparams);
	}
}
