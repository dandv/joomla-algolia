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
use Joomla\CMS\Language\Text;

JLoader::import('algolia.library');
JLoader::registerPrefix('Algolia', __DIR__);
JLoader::register('AlgoliaHelper', __DIR__ . '/helpers/algolia.php');

$lang = Factory::getLanguage();
$lang->load('com_algolia');

if (!Factory::getUser()->authorise('core.manage', 'com_algolia'))
{
	$app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');

	return false;
}

$app = Factory::getApplication();

$controller = JControllerLegacy::getInstance('Algolia');
$controller->execute($app->input->get('task'));
$controller->redirect();
