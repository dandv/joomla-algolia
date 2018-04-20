<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Library.Command
 *
 * @copyright  Copyright (C) 2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

require_once dirname(__FILE__) . '/bootstrap.php';

use Joomla\CMS\Factory;
use Phproberto\Joomla\Algolia\Command\IndexItems;

$indexDate = (new \DateTime)->modify('-1 hour');

$app = new IndexItems;
Factory::$application = $app;

$app->execute();
