<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Component.Backend
 *
 * @copyright  Copyright (C) 2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

defined('_JEXEC') || die;

use Joomla\CMS\MVC\View\HtmlView;

/**
 * Indexer view.
 *
 * @since  __DEPLOY_VERSION__
 */
class AlgoliaViewIndexers extends HtmlView
{
	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 */
	public function display($tpl = null)
	{
		AlgoliaHelper::addSubmenu('indexers');
		$model = $this->getModel();

		$this->items         = $model->getItems();
		$this->pagination    = $model->getPagination();
		$this->state         = $model->getState();
		$this->filterForm    = $model->getFilterForm();
		$this->activeFilters = $model->getActiveFilters();
		$this->model = $model;

		$this->addToolbar();
		$this->sidebar = JHtmlSidebar::render();

		return parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 */
	protected function addToolbar()
	{
		$canDo = JHelperContent::getActions('com_algolia');
		$user  = JFactory::getUser();

		// Get the toolbar object instance
		$bar = JToolbar::getInstance('toolbar');

		JToolbarHelper::title(JText::_('Algolia - Indexers'), 'stack article');

		if ($canDo->get('core.create'))
		{
			JToolbarHelper::addNew('indexer.add');
		}

		if ($canDo->get('core.edit') || $canDo->get('core.edit.own'))
		{
			JToolbarHelper::editList('indexer.edit');
		}

		if ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::publish('indexers.publish', 'JTOOLBAR_PUBLISH', true);
			JToolbarHelper::unpublish('indexers.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			JToolbarHelper::checkin('indexers.checkin');
		}

		if ($canDo->get('core.delete'))
		{
			JToolbarHelper::custom('indexers.delete', 'trash', '', 'JTOOLBAR_DELETE', true);
		}

		if ($user->authorise('core.admin', 'com_algolia'))
		{
			JToolbarHelper::preferences('com_algolia');
		}
	}
}
