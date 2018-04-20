<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Field
 *
 * @copyright  Copyright (C) 2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

defined('_JEXEC') || die;

use Phproberto\Joomla\Algolia\Field\Application;

/**
 * Application selector.
 *
 * @since  __DEPLOY_VERSION__
 */
class AlgoliaFormFieldApplication extends Application
{
	/**
	 * The field type.
	 *
	 * @var  string
	 */
	protected $type = 'Application';
}
