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
use AlgoliaSearch\Client;
use Phproberto\Joomla\Algolia\Entity\Index;
use Phproberto\Joomla\Algolia\Content\ArticlesIndexer;
use Phproberto\Joomla\Algolia\Plugin\BaseIndexerPlugin;
use Phproberto\Joomla\Algolia\Indexer\AlgoliaIndexerConfig;

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
					$indexer->delete([$article->id]);
				},
				$this->indexes()
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
					$indexer->$action((array) $ids);
				},
				$this->indexes()
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
			$action = 1 == $article->state ? 'indexItems' : 'deleteItems';

			array_map(
				function ($indexer) use ($article, $action)
				{
					$indexer->$action([$article->id]);
				},
				$this->indexes()
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
	 * Get the associated indexer.
	 *
	 * @param   Index  $index  Associated index
	 *
	 * @return  IndexerInterface
	 */
	protected function indexerInstance(Index $index)
	{
		return new ArticlesIndexer($index);
	}
}
