<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Component.Backend
 *
 * @copyright  Copyright (C) 2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

defined('_JEXEC') || die;

use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\MVC\Controller\AdminController;

/**
 * Indexes.
 *
 * @since  __DEPLOY_VERSION__
 */
class AlgoliaControllerIndexes extends AdminController
{
	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  The array of possible config values. Optional.
	 *
	 * @return  JModelLegacy
	 */
	public function getModel($name = 'Index', $prefix = 'AlgoliaModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}

	/**
	 * Reindex items.
	 *
	 * @return  void
	 */
	public function reindex()
	{
		$ids = array_values(
			array_filter(
				ArrayHelper::toInteger((array) $this->input->get('cid', [], 'array')),
				function ($value)
				{
					return $value > 0;
				}
			)
		);

		$url = 'index.php?option=' . $this->option . '&view=' . $this->{'view_list'};
		$this->setRedirect(Route::_($url, false));

		try
		{
			$itemsIds = $this->getModel('Indexes')->reindex($ids);
		}
		catch (Exception $e)
		{
			$msg = Text::sprintf('LIB_ALGOLIA_ITEMS_ERROR_INDEXING', $e->getMessage);
			$this->setMessage($msg, 'error');

			return false;
		}

		$msg = Text::sprintf('LIB_ALGOLIA_ITEMS_MSG_N_ITEMS_INDEXED', count($itemsIds));
		$this->setMessage($msg, 'message');
	}
}
