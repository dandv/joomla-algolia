<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Module.mod_algolia_autocomplete
 *
 * @copyright  Copyright (C) 2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

defined('_JEXEC') || die;

JLoader::import('algolia.library');

use Joomla\CMS\Helper\ModuleHelper;
use Phproberto\Joomla\Algolia\Entity\Index;

$index = Index::find($params->get('index_id'));

require ModuleHelper::getLayoutPath('mod_algolia_autocomplete', $params->get('layout', 'default'));
