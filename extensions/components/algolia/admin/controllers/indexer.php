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
use Joomla\CMS\MVC\Controller\FormController;

/**
 * Indexer controller
 *
 * @since  __DEPLOY_VERSION__
 */
class AlgoliaControllerIndexer extends FormController
{
	/**
	 * Set the active extension id in the indexer form.
	 *
	 * @return  void
	 */
	public function reloadEditForm()
	{
		$app  = Factory::getApplication();

		$id   = (int) $this->input->getInt('id', 0);
		$data = $this->input->get('jform', [], 'array');

		$app->setUserState('com_algolia.edit.indexer.data', $data);

		$this->setRedirect('index.php?option=com_algolia&view=indexer&layout=edit&id=' . $id);
	}
}
