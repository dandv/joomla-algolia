<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Plugin
 *
 * @copyright  Copyright (C) 2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Algolia\Plugin;

defined('_JEXEC') || die;

use Joomla\CMS\Form\Form;
use Joomla\CMS\Table\Table;
use Joomla\Utilities\ArrayHelper;
use Phproberto\Joomla\Algolia\Entity\Index;
use Phproberto\Joomla\Algolia\Plugin\BasePlugin;
use Phproberto\Joomla\Algolia\Indexer\IndexerInterface;

/**
 * Base plugin.
 *
 * @since  __DEPLOY_VERSION__
 */
abstract class BaseIndexerPlugin extends BasePlugin
{
	/**
	 * Get the indexer associated to a plugin.
	 *
	 * @param   Index    $index        Associated index
	 * @param   integer  $extensionId  Extension identifier
	 *
	 * @return  mixed
	 */
	public function onAlgoliaGetIndexer(Index $index, $extensionId)
	{
		if ($extensionId !== $this->extensionId())
		{
			return;
		}

		return $this->indexerInstance($index);
	}

	/**
	 * [onAlgoliaIndexItems description]
	 *
	 * @param   array   $search      Search parameters
	 * @param   array   $indexedIds  Count of already indexed items
	 *
	 * @return  void
	 */
	public function onAlgoliaIndexItems(array $search, array &$indexedIds)
	{
		// Specific indexer type searched
		if (!empty($search['filter']['element']) && $search['filter']['element'] !== $this->_name)
		{
			return;
		}

		if (!empty($search['list']['limit']) && count($indexedIds) >= $search['list']['limit'])
		{
			return;
		}

		$indexerIds = empty($search['filter']['indexers']) ? [] : $search['filter']['indexers'];

		foreach ($this->indexes($indexerIds) as $indexer)
		{
			$indexedIds = array_merge(
				$indexedIds,
				$indexer->findAndIndexItems($search)
			);

			if (!empty($search['list']['limit']) && count($indexedIds) >= $search['list']['limit'])
			{
				return;
			}
		}
	}

	/**
	 * When injecting plugin params.
	 *
	 * @param   int   $indexerId  Indexer id
	 * @param   Form  $form       Form where parameters will be injected
	 *
	 * @return  void
	 */
	public function onAlgoliaInjectIndexerParams($indexerId, &$form)
	{
		$extension = $this->extension();

		if ($indexerId !== (int) $extension['extension_id'])
		{
			return true;
		}

		$formsFolder = $this->pluginPath() . '/forms';

		if (!is_dir($formsFolder))
		{
			return true;
		}

		Form::addFormPath($formsFolder);

		return $form->loadFile('params', true);
	}

	/**
	 * Get the associated indexer.
	 *
	 * @param   Index  $index  Associated index
	 *
	 * @return  IndexerInterface
	 */
	abstract protected function indexerInstance(Index $index);

	/**
	 * Retrieve active indexes.
	 *
	 * @param   array  $ids  Return only specified indexers
	 *
	 * @return  ContentArticleIndex[]
	 */
	protected function indexes(array $ids = [])
	{
		$ids = array_filter(ArrayHelper::toInteger($ids));

		$db = $this->db;

		$query = $db->getQuery(true)
			->select('i.*')
			->from($db->qn('#__algolia_index', 'i'))
			->innerjoin(
				$db->qn('#__extensions', 'e')
				. ' ON ' . $db->qn('e.extension_id') . ' = ' . $db->qn('i.extension_id')
			)
			->where('i.state = 1')
			->where('e.type = ' . $db->q('plugin'))
			->where('e.folder = ' . $db->q($this->_type))
			->where('e.element = ' . $db->q($this->_name))
			->where('e.enabled = 1');

		if ($ids)
		{
			$query->where($db->qn('i.id') . ' IN(' . implode(',', $ids) . ')');
		}

		$db->setQuery($query);

		return array_map(
			function ($indexerData)
			{
				$index = Index::find($indexerData['id'])->bind($indexerData);

				return $this->indexerInstance($index);
			},
			$db->loadAssocList() ?: []
		);
	}
}
