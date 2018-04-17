<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Field
 *
 * @copyright  Copyright (C) 2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

defined('_JEXEC') || die;

use Phproberto\Joomla\Algolia\Field\Plugin;

/**
 * Plugin selector.
 *
 * @since  __DEPLOY_VERSION__
 */
class AlgoliaFormFieldPlugin extends Plugin
{
	/**
	 * The field type.
	 *
	 * @var  string
	 */
	protected $type = 'Plugin';
}
