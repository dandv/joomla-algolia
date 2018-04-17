<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Installer
 *
 * @copyright  Copyright (C) 2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Algolia\Indexer;

defined('_JEXEC') || die;

use Phproberto\Joomla\Algolia\Exception\InvalidIndexerConfig;

/**
 * Represents Algolia configuration.
 *
 * @since  __DEPLOY_VERSION__
 */
class AlgoliaIndexerConfig
{
	const SETTING_APPLICATION_ID = 'application_id';
	const SETTING_API_KEY = 'api_key';
	const SETTING_INDEX_NAME = 'index_name';
	const SETTING_SEARCH_KEY = 'search_key';

	/**
	 * Algolia configuration.
	 *
	 * @var  array
	 */
	protected $config;

	/**
	 * Constructor.
	 *
	 * @param   array  $config  Array with configuration
	 */
	public function __construct(array $config)
	{
		$this->config = $config;
	}

	/**
	 * Retrieve the Admin API Key.
	 *
	 * @return  string
	 */
	public function apiKey()
	{
		return $this->config[self::SETTING_API_KEY];
	}

	/**
	 * Retrieve the application id.
	 *
	 * @return  string
	 */
	public function applicationId()
	{
		return $this->config[self::SETTING_APPLICATION_ID];
	}

	/**
	 * Retrieve the index name.
	 *
	 * @return  string
	 */
	public function indexName()
	{
		return $this->config[self::SETTING_INDEX_NAME];
	}

	/**
	 * Check if this configuration is valid.
	 *
	 * @return  boolean
	 */
	public function isValid()
	{
		try
		{
			$result = $this->validate();
		}
		catch (Exception $e)
		{
			$result = false;
		}

		return $result;
	}

	/**
	 * Checke that current config is valid.
	 *
	 * @return  boolean
	 *
	 * @throws  InvalidIndexerConfig
	 */
	public function validate()
	{
		if (empty(trim($this->config[self::SETTING_APPLICATION_ID])))
		{
			return InvalidIndexerConfig::missingApplicationId($this);
		}

		if (empty(trim($this->config[self::SETTING_API_KEY])))
		{
			return InvalidIndexerConfig::missingAdminApiKey($this);
		}

		if (empty(trim($this->config[self::SETTING_SEARCH_KEY])))
		{
			return InvalidIndexerConfig::missingSearchKey($this);
		}

		if (empty(trim($this->config[self::SETTING_INDEX_NAME])))
		{
			return InvalidIndexerConfig::missingIndexName($this);
		}

		return true;
	}
}
