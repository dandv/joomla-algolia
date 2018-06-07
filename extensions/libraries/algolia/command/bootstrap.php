<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Library.Command
 *
 * @copyright  Copyright (C) 2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

// Must be called from the command line
('cli' === php_sapi_name()) or die;

error_reporting(E_ALL);
ini_set('display_errors', 1);

const _JEXEC = 1;

// Load system defines
if (file_exists(dirname(__FILE__) . '/../../../defines.php'))
{
	require_once dirname(__FILE__) . '/../../../defines.php';
}

if (!defined('_JDEFINES'))
{
	define('JPATH_BASE', dirname(__FILE__) . '/../../..');
	require_once JPATH_BASE . '/includes/defines.php';
}

defined('JDEBUG') || define('JDEBUG', false);

// Get the framework.
require_once JPATH_LIBRARIES . '/import.legacy.php';

// Bootstrap the CMS libraries.
require_once JPATH_LIBRARIES . '/cms.php';

// Import the configuration.
require_once JPATH_CONFIGURATION . '/configuration.php';

if (!isset($_SERVER['HTTP_HOST']))
{
	$_SERVER['HTTP_HOST'] = 'localhost';
}

require_once JPATH_LIBRARIES . '/algolia/library.php';
