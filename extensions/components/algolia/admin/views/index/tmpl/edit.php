<?php
/**
 * @package     Phproberto.Joomla-Algolia
 * @subpackage  Component.Backend
 *
 * @copyright  Copyright (C) 2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

defined('_JEXEC') || die;

JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');

$app = JFactory::getApplication();
$input = $app->input;
?>
<form method="post" name="adminForm" id="adminForm" class="form-validate">
	<div class="form-inline form-inline-header">
		<?php echo $this->form->renderField('name');?>
	</div>
	<hr>
	<div class="row-fluid">
		<div class="span4">
			<h3>Algolia settings</h3>
			<hr>
			<?php
				echo $this->form->renderField('application_id');
				echo $this->form->renderField('search_key');
				echo $this->form->renderField('api_key');
				echo $this->form->renderField('index_name');
			?>
		</div>
		<div class="span4">
			<h3>Indexer settings</h3>
			<hr>
			<?php
				echo $this->form->renderField('extension_id');

				foreach ($this->form->getGroup('params') as $fieldName => $field)
				{
					echo $field->renderField();
				}
			?>
		</div>
		<div class="span4">
			<?php
				echo $this->form->renderField('state');
				echo $this->form->renderField('created_date');
				echo $this->form->renderField('created_by');
				echo $this->form->renderField('modified_date');
				echo $this->form->renderField('modified_by');
			?>
		</div>
	</div>
	<input type="hidden" name="id" value="<?=$this->item->id?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="return" value="<?php echo $input->getCmd('return'); ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
