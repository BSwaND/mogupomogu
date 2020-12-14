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

?>

<div class="main_body">
	<div class="row">
		<div class="offset-md-4 col-md-10">
			<div class="bg-white mb-3">
				<div class="bg-white__header">
					<div class="h1">Вход</div>
				</div>

				<div class="pt-4 pl-4 pr-4">
					<div class="login<?php echo $this->pageclass_sfx; ?>">
						<?php if ($this->params->get('show_page_heading')) : ?>
							<div class="page-header">
								<h1>
									<?php echo $this->escape($this->params->get('page_heading')); ?>
								</h1>
							</div>
						<?php endif; ?>
						<?php if (($this->params->get('logindescription_show') == 1 && str_replace(' ', '', $this->params->get('login_description')) != '') || $this->params->get('login_image') != '') : ?>
						<div class="login-description">
							<?php endif; ?>
							<?php if ($this->params->get('logindescription_show') == 1) : ?>
								<?php echo $this->params->get('login_description'); ?>
							<?php endif; ?>
							<?php if ($this->params->get('login_image') != '') : ?>
								<img src="<?php echo $this->escape($this->params->get('login_image')); ?>" class="login-image" alt="<?php echo JText::_('COM_USERS_LOGIN_IMAGE_ALT'); ?>" />
							<?php endif; ?>
							<?php if (($this->params->get('logindescription_show') == 1 && str_replace(' ', '', $this->params->get('login_description')) != '') || $this->params->get('login_image') != '') : ?>
						</div>
					<?php endif; ?>
						<form action="<?php echo JRoute::_('index.php?option=com_users&task=user.login'); ?>" method="post" class="form-validate form-horizontal well">
							<fieldset>
								<?php echo $this->form->renderFieldset('credentials'); ?>
								<?php if ($this->tfa) : ?>
									<?php echo $this->form->renderField('secretkey'); ?>
								<?php endif; ?>

								<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
									<div class="control-group mt-5 mb-2">
										<div class="control-label">
											<label class="checkbox_outer checkbox_outer__right">
												<input  id="remember" type="checkbox" name="remember" value="yes"  class="checkbox_hidden-input inputbox">
												<span class="checkbox_span-label"><?php echo JText::_('COM_USERS_LOGIN_REMEMBER_ME'); ?></span>
											</label>
										</div>
									</div>
								<?php endif; ?>
								<div class="control-group control-group__authorization">
									<div class="controls">
										<button type="submit" class="btn btn_primary">
											<?php echo JText::_('JLOGIN'); ?>
										</button>
									</div>
								</div>
								<?php $return = $this->form->getValue('return', '', $this->params->get('login_redirect_url', $this->params->get('login_redirect_menuitem'))); ?>
								<input type="hidden" name="return" value="<?php echo base64_encode($return); ?>" />
								<?php echo JHtml::_('form.token'); ?>
							</fieldset>
						</form>
					</div>	
				</div>
			</div>

			<div class="bg-white p-3">
					<ul class="nav nav-tabs nav-stacked nav-stacked__authorization ">
						<li class="nav-stacked__authorization__item">
							<a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>">
								<?php echo JText::_('COM_USERS_LOGIN_RESET'); ?>
							</a>
						</li>
						<li class="nav-stacked__authorization__item">
							<a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>">
								<?php echo JText::_('COM_USERS_LOGIN_REMIND'); ?>
							</a>
						</li>
						<?php $usersConfig = JComponentHelper::getParams('com_users'); ?>
						<?php if ($usersConfig->get('allowUserRegistration')) : ?>
							<li class="nav-stacked__authorization__item">
								<a href="<?php echo JRoute::_('index.php?option=com_users&view=registration'); ?>">
									<?php echo JText::_('COM_USERS_LOGIN_REGISTER'); ?>
								</a>
							</li>
						<?php endif; ?>
					</ul>
			</div>
		</div>
	</div>
</div>