<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Tests.Unit
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Algolia\Tests\Entity;

defined('_JEXEC') || die;

use Phproberto\Joomla\Algolia\Model\ListModel;

/**
 * Indexer tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class ListModelTest extends \TestCase
{
	/**
	 * idsFromState tests provider.
	 *
	 * @return  array
	 */
	public function idsFromStateProvider()
	{
		return [
			[['filter.indexer' => '1'], 'filter.indexer', [1]],
			[['filter.indexer' => ['1', 2, null, ' ssss']], 'filter.indexer', [1, 2]],
			[['filter.indexer' => null], 'filter.indexer', []],
			[['filter.indexer' => ''], 'filter.indexer', []],
			[['filter.indexer' => ['1212', 232]], 'filter.indexer', [1212,232]],
			[['filter.indexer' => [15, 30]], 'filter.indexer', [15,30]]
		];
	}

	/**
	 * @test
	 *
	 * @dataProvider  idsFromStateProvider
	 *
	 * @param   array   $state     State to assing to the model
	 * @param   string  $key       Key that stored the ids
	 * @param   array   $expected  Expected result
	 *
	 * @return void
	 */
	public function idsFromStateReturnsExpectedValues(array $state, $key, array $expected)
	{
		$model = $this->getMockForAbstractClass(ListModel::class);

		foreach ($state as $key => $value)
		{
			$model->setState($key, $value);
		}

		$reflection = new \ReflectionClass($model);
		$method = $reflection->getMethod('idsFromState');
		$method->setAccessible(true);

		$this->assertSame($expected, $method->invoke($model, $key));
	}
}
