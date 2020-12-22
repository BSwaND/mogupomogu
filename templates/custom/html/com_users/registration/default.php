<?php
	/**
	 * @package     Joomla.Site
	 * @subpackage  com_users
	 *
	 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
	 * @license     GNU General Public License version 2 or later; see LICENSE.txt
	 */

	defined('_JEXEC') or die;

	JHtml::_('behavior.keepalive');
	JHtml::_('behavior.formvalidator');

	jimport('joomla.application.module.helper');
	$attribs['style'] = 'none';

?>
<div class="main_body">
	<div class="row">
		<div class="offset-md-4 col-md-10">
			<div class="bg-white mb-5">
				<div class="bg-white__header">
					<div class="h1"><?php echo $this->escape($this->params->get('page_heading')); ?></div>
				</div>

				<div class="pt-4 pl-4 pr-4">  
					<div class="registration<?php echo $this->pageclass_sfx; ?>">
						<form id="member-registration" action="<?php echo JRoute::_('index.php?option=com_users&task=registration.register'); ?>" method="post" class="form-validate form-horizontal well" enctype="multipart/form-data">
							<?php // Iterate through the form fieldsets and display each one. ?>
							<?php foreach ($this->form->getFieldsets() as $fieldset) : ?>
								<?php $fields = $this->form->getFieldset($fieldset->name); ?>
								<?php if (count($fields)) : ?>
									<fieldset>
										<?php // If the fieldset has a label set, display it as the legend. ?>
										<?php echo $this->form->renderFieldset($fieldset->name); ?>
									</fieldset>
								<?php endif; ?>
							<?php endforeach; ?>
							<div class="control-group">
								<div class="controls">
									<button type="submit" class="btn btn_primary validate">
										<?php echo JText::_('JREGISTER'); ?>
									</button>
									<a class="btn" href="<?php echo JRoute::_(''); ?>" title="<?php echo JText::_('JCANCEL'); ?>">
										<?php echo JText::_('JCANCEL'); ?>
									</a>
									<input type="hidden" name="option" value="com_users" />
									<input type="hidden" name="task" value="registration.register" />
								</div>
							</div>
							<?php echo JHtml::_('form.token'); ?>
						</form>
					</div>
					<?php
						$modules = JModuleHelper::getModules('slogin');
						foreach($modules as $module){
							echo JModuleHelper::renderModule($module, $attribs);
						} ?>

				</div>
			</div>
		</div>
	</div>