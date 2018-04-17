<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Algolia\Exception;

defined('_JEXEC') || die;

use Joomla\CMS\Language\Text;
use Phproberto\Joomla\Algolia\Indexer\AlgoliaIndexerConfig;

/**
 * Invalid entity data errors.
 *
 * @since  __DEPLOY_VERISON__
 */
class InvalidIndexerConfig extends \InvalidArgumentException implements ExceptionInterface
{
	/**
	 * Adin API Key is empty.
	 *
	 * @param   AlgoliaIndexerConfig  $config  Config instance
	 *
	 * @return  static
	 */
	public static function missingAdminApiKey(AlgoliaIndexerConfig $config)
	{
		$msg = Text::_('LIB_PHPROBERTO_ALGOLIA_CONFIG_ERROR_MISSING_ADMIN_API_KEY');

		return new static($msg,	500);
	}

	/**
	 * Application ID is empty.
	 *
	 * @param   AlgoliaIndexerConfig  $config  Config instance
	 *
	 * @return  static
	 */
	public static function missingApplicationId(AlgoliaIndexerConfig $config)
	{
		$msg = Text::_('LIB_PHPROBERTO_ALGOLIA_CONFIG_ERROR_MISSING_APPLICATION_ID');

		return new static($msg,	500);
	}

	/**
	 * Index name is empty.
	 *
	 * @param   AlgoliaIndexerConfig  $config  Config instance
	 *
	 * @return  static
	 */
	public static function missingIndexName(AlgoliaIndexerConfig $config)
	{
		$msg = Text::_('LIB_PHPROBERTO_ALGOLIA_CONFIG_ERROR_MISSING_INDEX_NAME');

		return new static($msg,	500);
	}

	/**
	 * Data is empty.
	 *
	 * @param   AlgoliaIndexerConfig  $config  Config instance
	 *
	 * @return  static
	 */
	public static function missingSearchKey(AlgoliaIndexerConfig $config)
	{
		$msg = Text::_('LIB_PHPROBERTO_ALGOLIA_CONFIG_ERROR_MISSING_SEARCH_KEY');

		return new static($msg,	500);
	}
}
