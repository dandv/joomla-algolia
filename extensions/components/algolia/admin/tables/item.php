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
 * Featured Table class.
 *
 * @since  __DEPLOY_VERSION__
 */
class AlgoliaTableItem extends BaseTable
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
	 * Name of the primary key fields in the table.
	 *
	 * @var  array
	 */
	protected $_tbl_keys = ['index_id', 'object_id'];

	/**
	 * Constructor
	 *
	 * @param   \JDatabaseDriver  $db  A database connector object
	 */
	public function __construct(\JDatabaseDriver $db)
	{
		parent::__construct('#__algolia_item', 'id', $db);
	}
}
