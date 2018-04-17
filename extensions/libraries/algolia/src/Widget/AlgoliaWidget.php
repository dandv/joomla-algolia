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

use AlgoliaSearch\Index;
use Phproberto\Joomla\Algolia\Indexer\Config\Config;

/**
 * Base indexer.
 *
 * @since  __DEPLOY_VERSION__
 */
abstract class AlgoliaWidget
{
	/**
	 * Algolia config.
	 *
	 * @var  Config
	 */
	protected $config;

	/**
	 * Constructor.
	 *
	 * @param   Client  $index   Algolia index
	 * @param   Config  $config  Indexer configuration
	 */
	public function __construct(Index $index, Config $config)
	{
		$this->config = $config;
	}
}
