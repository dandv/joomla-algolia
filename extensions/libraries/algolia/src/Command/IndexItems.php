<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Plugin
 *
 * @copyright  Copyright (C) 2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Algolia\Command;

defined('_JEXEC') || die;

use Phproberto\Joomla\Algolia\Command\BaseCommand;

/**
 * Index items before date.
 *
 * @since  __DEPLOY_VERSION__
 *
 * @codeCoverageIgnore
 */
class IndexItems extends BaseCommand
{
	/**
	 * Entry point for the script
	 *
	 * @return  void
	 */
	public function doExecute()
	{
		// Check if they passed --help
		if ($this->input->get('help'))
		{
			$this->displayHelp();
			exit(0);
		}

		$this->out('Searching indexable items');
		$this->out('============================');

		$indexedIds = [];

		$this->trigger('onAlgoliaIndexItems', [$this->parseArguments(), &$indexedIds]);

		if (empty($indexedIds))
		{
			$this->out('No indexable items found');
		}
		else
		{
			$this->out(count($indexedIds) . ' items indexed: ' . implode(',', $indexedIds));
		}
	}

	/**
	 * Parse script arguments and compose a search array.
	 *
	 * @return  array
	 */
	protected function parseArguments()
	{
		$search = [
			'filter' => [],
			'list'    => [
				'limit' => 40
			]
		];

		if ($hours = (int) $this->input->get('hours'))
		{
			$search['filter']['hours'] = $hours;
		}

		if ($ids = $this->input->get('ids', '', 'string'))
		{
			$search['filter']['ids'] = explode(',', $ids);
		}

		if ($days = (int) $this->input->get('days'))
		{
			$search['filter']['days'] = $days;
		}

		if ($element = $this->input->get('element'))
		{
			$search['filter']['element'] = $element;
		}

		if ($indexer = $this->input->get('indexer'))
		{
			$search['filter']['indexers'] = [$indexer];
		}
		elseif ($indexers = $this->input->get('indexers'))
		{
			$search['filter']['indexers'] = explode(',', $indexers);
		}

		if ($limit = (int) $this->input->get('limit'))
		{
			$search['list']['limit'] = $limit;
		}

		return $search;
	}

	/**
	 * Display the help information
	 *
	 * @return  void
	 */
	protected function displayHelp()
	{
		$help = <<<HELP
Command line indexer for Algolia

usage: php {$this->input->executable} [--hours=<number>] [--days=<number>]
	[--ids=<comma-separated-ids>] [--element=<string>] [--indexer=<number>]
	[--indexers=<comma-separated-ids>] [--limit=<number>]
	[command] [<args>]

OPTIONS

  --hours=<number>
    Only index items that hasn't been indexed in the last X hours. Example: --hours=2

  --days=<number>
    Only index items that hasn't been indexed in the last X days. Example: --days=2

  --ids=<comma-separated-ids>
    Only index items with the specified ids. Example: --ids=20,23

  --element=<string>
    Only index items associated to a specific indexer plugin. Example: --element=content_articles

  --indexer=<number>
    Only index items found in a configured indexer with a specific identifier. Example: --indexer=1

  --indexers=<comma-separated-ids>
    Only index items found in a configured indexer with specific identifiers. Example: --indexers=1,2

  --limit=<number>
    Limit the number of items that will be indexed. Default value is 40. Example: --limit=30

HELP;
		$this->out($help);
	}
}
