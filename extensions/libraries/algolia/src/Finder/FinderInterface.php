<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Finder
 *
 * @copyright  Copyright (C) 2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Algolia\Finder;

defined('_JEXEC') || die;

/**
 * Finder interface.
 *
 * @since  __DEPLOY_VERSION__
 */
interface FinderInterface
{
	/**
	 * Search indexable items.
	 *
	 * @param   array   $options  Array with search filders.
	 *
	 * @return  array
	 */
	public function find(array $options = []);
}
