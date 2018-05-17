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

use Phproberto\Joomla\Algolia\Entity\BaseEntity;

/**
 * BaseEntity tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class BaseEntityTest extends \TestCase
{
	/**
	 * Data provider for filterIds method testing.
	 *
	 * @return  array
	 */
	public function filterIdsProvider()
	{
		return [
			[1,[1]],
			['1',[1]],
			[['1', null, '', ' ', 'test me'],[1]],
			[['1', '25'],[1,25]],
			[null,[]]
		];
	}

	/**
	 * @test
	 *
	 * @dataProvider  filterIdsProvider
	 *
	 * @return void
	 */
	public function filterIdsReturnsCorrectValue($ids, array $expected)
	{
		$entity = $this->getMockForAbstractClass(BaseEntity::class);

		$reflection = new \ReflectionClass($entity);
		$method = $reflection->getMethod('filterIds');
		$method->setAccessible(true);

		$this->assertEquals($expected, $method->invoke($entity, $ids));
	}
}
