<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Tests.Unit
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Algolia\Tests\Unit\Indexer\Stubs;

defined('_JEXEC') || die;

use Phproberto\Joomla\Algolia\Indexer\BaseIndexer;

/**
 * BaseIndexer tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class SampleIndexer extends BaseIndexer
{
	/**
	 * Items that will be returned by loadItems method.
	 *
	 * @var  array
	 */
	public $loadableItems = [];

	/**
	 * Search indexable items.
	 *
	 * @param   array   $search  Array with filtering information
	 *
	 * @return  array
	 */
	public function searchItems(array $search)
	{
		return $this->loadableItems;
	}

}
