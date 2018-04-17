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
	 * Load items by their id.
	 *
	 * @param   array   $ids  Items identifiers
	 *
	 * @return  array
	 */
	protected function loadItems(array $ids)
	{
		return $this->loadableItems;
	}

}
