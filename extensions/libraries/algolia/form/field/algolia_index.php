<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Field
 *
 * @copyright  Copyright (C) 2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

defined('_JEXEC') || die;

use Phproberto\Joomla\Algolia\Field\AlgoliaIndex;

/**
 * Index selector.
 *
 * @since  __DEPLOY_VERSION__
 */
class AlgoliaFormFieldAlgolia_Index extends AlgoliaIndex
{
	/**
	 * The field type.
	 *
	 * @var  string
	 */
	protected $type = 'Algolia_Index';
}
