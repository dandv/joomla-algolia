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

use Joomla\CMS\Table\Table;
use Joomla\Utilities\ArrayHelper;
use Phproberto\Joomla\Entity\ComponentEntity;
use Phproberto\Joomla\Entity\Contracts\EntityInterface;

/**
 * Base Entity.
 *
 * @since   __DEPLOY_VERSION__
 */
abstract class BaseEntity extends ComponentEntity implements EntityInterface
{
	/**
	 * Component option.
	 *
	 * @var string
	 */
	protected $componentOption = 'com_algolia';

	/**
	 * Get the plugin types that will be used by this entity.
	 *
	 * @return  array
	 */
	protected function eventsPlugins()
	{
		return ['joomla_entity', 'algolia_indexer'];
	}

	/**
	 * Filter a list of ids.
	 *
	 * @param   integer|array  $ids  Ids to filter
	 *
	 * @return  integer[]
	 */
	protected function filterIds($ids)
	{
		return array_values(
			array_filter(
				ArrayHelper::toInteger((array) $ids),
				function ($value)
				{
					return $value > 0;
				}
			)
		);
	}

	/**
	 * Get a table instance. Defauts to \JTableContent.
	 *
	 * @param   string  $name     Table name. Optional.
	 * @param   string  $prefix   Class prefix. Optional.
	 * @param   array   $options  Configuration array for the table. Optional.
	 *
	 * @return  \JTable
	 *
	 * @throws  \InvalidArgumentException
	 *
	 * @codeCoverageIgnore
	 */
	public function table($name = '', $prefix = null, $options = array())
	{
		Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/' . $this->componentOption . '/tables');

		$name   = $name ?: ucfirst($this->name());
		$prefix = $prefix ?: 'AlgoliaTable';

		return parent::table($name, $prefix, $options);
	}
}
