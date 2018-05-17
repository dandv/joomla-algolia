<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Entity
 *
 * @copyright  Copyright (C) 2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Algolia\Entity;

defined('_JEXEC') || die;

use Phproberto\Joomla\Algolia\Entity\Index;
use Phproberto\Joomla\Algolia\Entity\BaseEntity;

/**
 * Item Entity.
 *
 * @since   __DEPLOY_VERSION__
 */
class Item extends BaseEntity
{
	/**
	 * Retrieve data;
	 *
	 * @return  array
	 */
	public function data()
	{
		return json_decode($this->get('data'));
	}

	/**
	 * Retrieve related indexer.
	 *
	 * @return  Indexer
	 */
	public function index()
	{
		return Index::find($this->get('index_id'));
	}
}
