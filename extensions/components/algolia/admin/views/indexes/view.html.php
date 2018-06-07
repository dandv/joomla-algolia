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
 * Indexes view.
 *
 * @since  __DEPLOY_VERSION__
 */
class AlgoliaViewIndexes extends HtmlView
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
		AlgoliaHelper::addSubmenu('indexes');
		$model = $this->getModel();

		$this->items         = $model->getItems();
		$this->pagination    = $model->getPagination();
		$this->state         = $model->getState();
		$this->filterForm    = $model->getFilterForm();
		$this->activeFilters = $model->getActiveFilters();
		$this->model         = $model;

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

		JToolbarHelper::title(JText::_('Algolia - Indexes'), 'stack article');

		if ($canDo->get('core.create'))
		{
			JToolbarHelper::addNew('index.add');
		}

		if ($canDo->get('core.edit') || $canDo->get('core.edit.own'))
		{
			JToolbarHelper::editList('index.edit');
		}

		if ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::publish('indexes.publish', 'JTOOLBAR_PUBLISH', true);
			JToolbarHelper::unpublish('indexes.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			JToolbarHelper::checkin('indexes.checkin');
		}

		if ($canDo->get('core.delete'))
		{
			JToolbarHelper::custom('indexes.delete', 'trash', '', 'JTOOLBAR_DELETE', true);
		}

		JToolbarHelper::custom('indexes.reindex', 'arrow-down-4', '', 'LIB_ALGOLIA_BTN_INDEX', true);

		if ($user->authorise('core.admin', 'com_algolia'))
		{
			JToolbarHelper::preferences('com_algolia');
		}
	}
}
