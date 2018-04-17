<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Library
 *
 * @copyright  Copyright (C) 2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

defined('_JEXEC') || die;

use Joomla\CMS\Form\FormHelper;

defined('JPATH_ALGOLIA_LIBRARY') || define('JPATH_ALGOLIA_LIBRARY', __DIR__);

$composerAutoload = __DIR__ . '/vendor/autoload.php';

if (!file_exists($composerAutoload))
{
	throw new \RuntimeException("Cannot find Twig library autoloader");
}

require_once $composerAutoload;

JLoader::setup();
JLoader::registerPrefix('Algolia', JPATH_ALGOLIA_LIBRARY);

// Fields
FormHelper::addFieldPath(__DIR__ . '/form/field');

$lang = JFactory::getLanguage();
$lang->load('lib_algolia', __DIR__);

