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
use Joomla\CMS\Language\Text;

JLoader::import('twig.library');
JLoader::import('algolia.library');
JLoader::registerPrefix('Algolia', __DIR__);

$lang = Factory::getLanguage();
$lang->load('com_algolia');

$app = Factory::getApplication();

$controller = JControllerLegacy::getInstance('Algolia');
$controller->execute($app->input->get('task'));
$controller->redirect();
