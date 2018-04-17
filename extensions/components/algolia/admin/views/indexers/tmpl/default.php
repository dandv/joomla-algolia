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

$indexers   = $this->model->getItems();
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
		<?php if (empty($indexers)) : ?>
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
						<th width="1%" class="nowrap center">
							<?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'i.state', $listDirn, $listOrder); ?>
						</th>
						<th style="min-width:100px" class="nowrap">
							<?php echo JHtml::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
						</th>
						<th width="1%" class="nowrap">
							<?php echo JHtml::_('searchtools.sort', 'COM_ALGOLIA_COL_EXTENSION', 'i.extension_name', $listDirn, $listOrder); ?>
						</th>
						<th width="1%" class="nowrap">
							<?php echo JHtml::_('searchtools.sort', 'COM_ALGOLIA_COL_APPLICATION_ID', 'a.application_id', $listDirn, $listOrder); ?>
						</th>
						<th width="1%" class="nowrap">
							<?php echo JHtml::_('searchtools.sort', 'COM_ALGOLIA_COL_INDEX_NAME', 'a.index_name', $listDirn, $listOrder); ?>
						</th>

					</tr>
				</thead>
				<tbody>
					<?php foreach ($indexers as $i => $item) : ?>
						<?php
							$canCreate  = $user->authorise('core.create', 'com_algolia');
							$canEdit    = $user->authorise('core.edit', 'com_algolia.indexer.' . $item->id);
							$canCheckin = $user->authorise('core.manage',     'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
							$canEditOwn = $user->authorise('core.edit.own',   'com_algolia.indexer.' . $item->id) && $item->created_by == $userId;
							$canChange  = $user->authorise('core.edit.state', 'com_algolia.indexer.' . $item->id) && $canCheckin;
						?>
						<tr class="row<?php echo $i % 2; ?>">
							<td class="center">
								<?php echo JHtml::_('grid.id', $i, $item->id); ?>
							</td>
							<td class="center">
								<div class="btn-group">
									<?php echo JHtml::_('jgrid.published', $item->state, $i, 'indexers.', $canChange, 'cb'); ?>
									<?php // Create dropdown items and render the dropdown list.
									if ($canChange)
									{
										JHtml::_('actionsdropdown.' . ((int) $item->state === -2 ? 'un' : '') . 'trash', 'cb' . $i, 'indexers');
										echo JHtml::_('actionsdropdown.render', $this->escape($item->name));
									}
									?>
								</div>
							</td>
							<td class="has-context">
								<div class="pull-left break-word">
									<?php if ($item->checked_out) : ?>
										<?php echo JHtml::_('jgrid.checkedout', $i, $item->modified_by, $item->checked_out_time, 'indexers.', $canCheckin); ?>
									<?php endif; ?>
									<?php if ($canEdit || $canEditOwn) : ?>
										<a class="hasTooltip" href="<?php echo JRoute::_('index.php?option=com_algolia&task=indexer.edit&id=' . $item->id); ?>" title="<?php echo JText::_('JACTION_EDIT'); ?>">
											<?php echo $this->escape($item->name); ?>
										</a>
									<?php else : ?>
										<span><?php echo $this->escape($item->name); ?></span>
									<?php endif; ?>
									<div class="small">

									</div>
								</div>
							</td>
							<td class="nowrap">
								<?php echo $this->escape(Text::_($item->extension_name)); ?>
							</td>
							<td>
								<?php echo $this->escape($item->application_id); ?>
							</td>
							<td>
								<?php echo $this->escape($item->index_name); ?>
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
