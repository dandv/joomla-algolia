<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Indexer
 *
 * @copyright  Copyright (C) 2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Algolia\Indexer;

defined('_JEXEC') || die;

use Phproberto\Joomla\Algolia\Indexer\Config;
use Phproberto\Joomla\Algolia\Finder\FinderInterface;

/**
 * Indexer interface.
 *
 * @since  __DEPLOY_VERSION__
 */
interface IndexerInterface
{
	/**
	 * Retrieve this indexer config.
	 *
	 * @return  Config
	 */
	public function config();

	/**
	 * Retrieve the indexable items finder.
	 *
	 * @return  FinderInterface
	 */
	public function finder();
}
