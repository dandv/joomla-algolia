<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Tests
 *
 * @copyright  Copyright (C) 2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

require_once JPATH_BASE . '/tests/unit/bootstrap.php';

if (!defined('JPATH_ALGOLIA_TESTS'))
{
	define('JPATH_ALGOLIA_TESTS', realpath(__DIR__));
}

if (!defined('JPATH_ALGOLIA_EXTENSIONS'))
{
	define('JPATH_ALGOLIA_EXTENSIONS', realpath(__DIR__ . '/../extensions'));
}

$loader = new \Composer\Autoload\ClassLoader;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../extensions/libraries/algolia/vendor/autoload.php';
