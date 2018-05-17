<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Tests.Unit
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Algolia\Tests;

defined('_JEXEC') || die;

use Phproberto\Joomla\Algolia\IndexableItem;
use Phproberto\Joomla\Algolia\Indexer\IndexerInterface;

/**
 * IndexableItem tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class IndexableItemTest extends \TestCase
{
	/**
	 * @test
	 *
	 * @return void
	 */
	public function constructorSetsItemAndIndexer()
	{
		$data = ['id' => 1, 'title' => 'One indexable item'];
		$indexer = $this->getMockForAbstractClass(IndexerInterface::class);
		$indexableItem = new IndexableItem($data, $indexer);

		$this->assertSame($data, $indexableItem->data());
		$this->assertSame($indexer, $indexableItem->indexer());
	}
}
