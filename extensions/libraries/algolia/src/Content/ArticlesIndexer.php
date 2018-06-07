<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Content
 *
 * @copyright  Copyright (C) 2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Algolia\Content;

defined('_JEXEC') || die;

use Phproberto\Joomla\Algolia\Entity\Index;
use Phproberto\Joomla\Algolia\Indexer\BaseIndexer;
use Phproberto\Joomla\Algolia\Content\ArticlesFinder;
use Phproberto\Joomla\Algolia\Indexer\IndexerInterface;
use Phproberto\Joomla\Algolia\Content\ArticlesIndexerConfig;

/**
 * Article indexer.
 *
 * @since  __DEPLOY_VERSION__
 */
class ArticlesIndexer extends BaseIndexer implements IndexerInterface
{
	/**
	 * Retrieve this indexer config.
	 *
	 * @return  ArticlesIndexerConfig
	 */
	public function config()
	{
		if (null === $this->config)
		{
			$this->config = new ArticlesIndexerConfig($this->index()->get('params'));
		}

		return $this->config;
	}

	/**
	 * Indexable items finder.
	 *
	 * @return  ArticlesFinder
	 */
	public function finder()
	{
		return new ArticlesFinder($this);
	}

	/**
	 * Get an instance of the associated indexable entity.
	 *
	 * @param   array   $data  Entity data
	 *
	 * @return  IndexableArticle
	 */
	public function indexableItem(array $data)
	{
		return new IndexableArticle($data, $this);
	}
}
