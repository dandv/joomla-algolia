<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Component.Backend
 *
 * @copyright  Copyright (C) 2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

defined('_JEXEC') || die;

use Phproberto\Joomla\Algolia\Table\BaseTable;

/**
 * Index Table.
 *
 * @since  __DEPLOY_VERSION__
 */
class AlgoliaTableIndex extends BaseTable
{
	/**
	 * Array with alias for "special" columns such as ordering, hits etc etc
	 *
	 * @var    array
	 */
	protected $_columnAlias = [
		'published' => 'state'
	];

	/**
	 * An array of key names to be json encoded in the bind function
	 *
	 * @var  array
	 */
	protected $_jsonEncode = ['params'];

	/**
	 * Constructor
	 *
	 * @param   \JDatabaseDriver  $db  A database connector object
	 */
	public function __construct(\JDatabaseDriver $db)
	{
		parent::__construct('#__algolia_index', 'id', $db);
	}
}
