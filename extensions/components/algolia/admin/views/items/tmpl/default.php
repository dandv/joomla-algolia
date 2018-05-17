<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Component.Backend
 *
 * @copyright  Copyright (C) 2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

$user      = Factory::getUser();
$userId    = $user->get('id');

$items      = $this->model->getItems();
$pagination = $this->model->getPagination();
$state      = $this->model->getState();

$listOrder = $this->escape($state->get('list.ordering'));
$listDirn  = $this->escape($state->get('list.direction'));
?>
<form method="post" name="adminForm" id="adminForm">
	<?php if (!empty( $this->sidebar)) : ?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="span10">
	<?php else : ?>
		<div id="j-main-container">
	<?php endif; ?>
		<?php
		// Search tools bar
		echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
		?>
		<?php if (empty($items)) : ?>
			<div class="alert alert-no-items">
				<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php else : ?>
			<table class="table table-striped" id="articleList">
				<thead>
					<tr>
						<th width="1%" class="center">
							<?php echo JHtml::_('grid.checkall'); ?>
						</th>
						<th style="min-width:100px"  class="nowrap">
							<?php echo JHtml::_('searchtools.sort', 'COM_ALGOLIA_COL_NAME', 'i.name', $listDirn, $listOrder); ?>
						</th>
						<th width="1%" class="nowrap">
							<?php echo JHtml::_('searchtools.sort', 'COM_ALGOLIA_COL_INDEX', 'i.index_name', $listDirn, $listOrder); ?>
						</th>
						<th width="1%" class="nowrap">
							<?php echo JHtml::_('searchtools.sort', 'COM_ALGOLIA_COL_PLUGIN', 'e.name', $listDirn, $listOrder); ?>
						</th>
						<th width="1%" class="nowrap center">
							<?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'i.state', $listDirn, $listOrder); ?>
						</th>
						<th width="1%" class="nowrap">
							<?php echo JHtml::_('searchtools.sort', 'COM_ALGOLIA_COL_UPDATE_DATE', 'ii.modified_date', $listDirn, $listOrder); ?>
						</th>
						<th width="1%" class="nowrap center">
							<?php echo JHtml::_('searchtools.sort', 'COM_ALGOLIA_COL_OBJECT_ID', 'ii.object_id', $listDirn, $listOrder); ?>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($items as $i => $item) : ?>
						<?php
							$canCreate  = $user->authorise('core.create', 'com_algolia');
							$canEdit    = $user->authorise('core.edit', 'com_algolia.item.' . $item->index_id);
							$canCheckin = $user->authorise('core.manage',     'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
							$canEditOwn = $user->authorise('core.edit.own',   'com_algolia.item.' . $item->index_id) && $item->created_by == $userId;
							$canChange  = $user->authorise('core.edit.state', 'com_algolia.item.' . $item->index_id) && $canCheckin;
						?>
						<tr class="row<?php echo $i % 2; ?>">
							<td class="center">
								<?php echo JHtml::_('grid.id', $i, $item->id); ?>
							</td>
							<td class="nowrap">
								<?php echo $this->escape(Text::_($item->name)); ?>
							</td>
							<td class="nowrap">
								<?php echo $this->escape(Text::_($item->index_name)); ?>
							</td>
							<td class="nowrap">
								<?php echo $this->escape(Text::_($item->extension_element)); ?>
							</td>
							<td class="center">
								<?php echo $this->escape($item->state); ?>
							</td>
							<td class="nowrap">
								<?php echo $this->escape($item->modified_date); ?>
							</td>
							<td class="nowrap center">
								<?php echo $this->escape(Text::_($item->object_id)); ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
		<?php echo $this->model->getPagination()->getListFooter(); ?>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
