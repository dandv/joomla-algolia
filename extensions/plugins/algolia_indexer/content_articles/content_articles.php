<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Plugin.Algolia_Articles
 *
 * @copyright  Copyright (C) 2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

defined('_JEXEC') || die;

JLoader::import('algolia.library');

use Joomla\CMS\Factory;
use AlgoliaSearch\Index;
use AlgoliaSearch\Client;
use Phproberto\Joomla\Algolia\Plugin\BaseIndexerPlugin;
use Phproberto\Joomla\Algolia\Indexer\AlgoliaIndexerConfig;
use Phproberto\Joomla\Algolia\Indexer\ContentArticleIndexer;

/**
 * Article indexer.
 *
 * @since  __DEPLOY_VERSION__
 */
final class PlgAlgolia_IndexerContent_Articles extends BaseIndexerPlugin
{
	/**
	 * Contexts to detect when an article is saved.
	 *
	 * @var  array
	 */
	private $allowedContexts = [
		'com_content.article'
	];

	/**
	 * Triggered after an item is deleted.
	 *
	 * @param   string  $context  The context of the content passed to the plugin (added in 1.6).
	 * @param   object  $article  A JTableContent object.
	 *
	 * @return  void
	 */
	public function onContentAfterDelete($context, $article)
	{
		if (!in_array($context, $this->allowedContexts, true))
		{
			return;
		}

		try
		{
			array_map(
				function ($indexer) use ($article)
				{
					$indexer->deleteItem($article->id);
				},
				$this->indexers()
			);
		}
		catch (\Exception $e)
		{
			Factory::getApplication()->enqueueMessage(
				'Error deleting indexed article in Algolia: ' . $e->getMessage(),
				'warning'
			);

			return;
		}
	}

	/**
	 * Triggered after items state is changed.
	 *
	 * @param   string   $context  The context of the content passed to the plugin (added in 1.6).
	 * @param   array    $ids      Identifier of the items being modified
	 * @param   integer  $state    New state assigned
	 *
	 * @return  void
	 */
	public function onContentChangeState($context, $ids, $state)
	{
		if (!$ids || !in_array($context, $this->allowedContexts, true))
		{
			return;
		}

		try
		{
			$action = 1 === $state ? 'indexItems' : 'deleteItems';

			array_map(
				function ($indexer) use ($ids, $action)
				{
					$indexer->$action($ids);
				},
				$this->indexers()
			);
		}
		catch (\Exception $e)
		{
			Factory::getApplication()->enqueueMessage(
				'Error updating articles in Algolia: ' . $e->getMessage(),
				'warning'
			);

			return;
		}
	}

	/**
	 * Triggered after an item is saved.
	 *
	 * @param   string   $context  The context of the content passed to the plugin (added in 1.6)
	 * @param   object   $article  A JTableContent object
	 * @param   boolean  $isNew    If the content is just about to be created
	 *
	 * @return  void
	 */
	public function onContentAfterSave($context, $article, $isNew)
	{
		if (!in_array($context, $this->allowedContexts, true))
		{
			return;
		}

		try
		{
			array_map(
				function ($indexer) use ($article)
				{
					$indexer->indexItem($article->id);
				},
				$this->indexers()
			);
		}
		catch (\Exception $e)
		{
			Factory::getApplication()->enqueueMessage(
				'Unable to index article in Algolia: ' . $e->getMessage(),
				'warning'
			);
		}
	}

	/**
	 * Retrieve active indexers.
	 *
	 * @return  ContentArticleIndexer[]
	 */
	private function indexers()
	{
		$db = $this->db;

		$query = $db->getQuery(true)
			->select('i.*')
			->from($db->qn('#__algolia_indexer', 'i'))
			->innerjoin(
				$db->qn('#__extensions', 'e')
				. ' ON ' . $db->qn('e.extension_id') . ' = ' . $db->qn('i.extension_id')
			)
			->where('i.state = 1')
			->where('e.enabled = 1');

		$db->setQuery($query);

		$indexersData = $db->loadAssocList() ?: [];
		$indexers = [];

		foreach ($indexersData as $indexerData)
		{
			$indexer = new ContentArticleIndexer($indexerData['id']);
			$indexer->bind($indexerData);

			$indexers[] = $indexer;
		}

		return $indexers;
	}
}
