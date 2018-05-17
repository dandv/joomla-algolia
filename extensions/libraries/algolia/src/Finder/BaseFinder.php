<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Indexer
 *
 * @copyright  Copyright (C) 2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Algolia\Finder;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Joomla\Registry\Registry;
use Phproberto\Joomla\Algolia\Indexer\Config;
use Phproberto\Joomla\Algolia\Indexer\IndexerInterface;

/**
 * Base indexable items finder.
 *
 * @since  __DEPLOY_VERSION__
 */
abstract class BaseFinder
{
	/**
	 * Associated indexer.
	 *
	 * @var  IndexerInterface
	 */
	protected $indexer;

	/**
	 * Constructor.
	 *
	 * @param   IndexerInterface  $indexer  Associated indexer
	 */
	public function __construct(IndexerInterface $indexer)
	{
		$this->indexer = $indexer;
	}

	/**
	 * Retrieve finder configuration.
	 *
	 * @return  Config
	 */
	public function config()
	{
		return $this->indexer->config();
	}

	/**
	 * Get the database driver.
	 *
	 * @return  \JDatabaseDriver
	 *
	 * @codeCoverageIgnore
	 */
	protected function db()
	{
		return Factory::getDbo();
	}

	/**
	 * Retrieve the associated indexer.
	 *
	 * @return  IndexerInterface
	 */
	public function indexer()
	{
		return $this->indexer;
	}
}
