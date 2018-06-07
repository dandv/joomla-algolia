<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Component.Frontend
 *
 * @copyright  Copyright (C) 2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

defined('_JEXEC') || die;

use Phproberto\Joomla\Algolia\Layout\LayoutHelper;

echo LayoutHelper::render('algolia.index.default', ['index' => $this->index]);
