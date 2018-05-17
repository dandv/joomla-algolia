<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Tests
 *
 * @copyright  Copyright (C) 2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Algolia\Tests\Finder;

defined('_JEXEC') || die;

use Phproberto\Joomla\Algolia\Indexer\Config;
use Phproberto\Joomla\Algolia\Finder\BaseFinder;
use Phproberto\Joomla\Algolia\Indexer\IndexerInterface;

/**
 * BaseFinder tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class BaseFinderTest extends \TestCase
{
	/**
	 * @test
	 *
	 * @return void
	 */
	public function classIsAbstract()
	{
		$reflection = new \ReflectionClass(BaseFinder::class);

		$this->assertTrue($reflection->isAbstract());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function constructorSetsConfig()
	{
		$indexer = $this->getMockForAbstractClass(IndexerInterface::class);

		$finder = $this->finderMock($indexer);

		$this->assertEquals($indexer, $finder->indexer());
	}

	/**
	 * Retrieve a finder mock.
	 *
	 * @param   array   $config   Finder configuration
	 * @param   array   $methods  Mocked methods
	 *
	 * @return  BaseFinder
	 */
	private function finderMock($config = [], array $methods = [])
	{
		return $this->getMockBuilder(BaseFinder::class)
			->setConstructorArgs([$config])
			->setMethods($methods)
			->getMockForAbstractClass();
	}
}
