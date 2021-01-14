<?php
	/**
	 * @package     Joomla.Site
	 * @subpackage  com_users
	 *
	 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
	 * @license     GNU General Public License version 2 or later; see LICENSE.txt
	 */

	defined('_JEXEC') or die;

	$params  = $this->params;

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
								<?php foreach ($this->form->getFieldsets() as $fieldset) { ?>
									<?php $fields = $this->form->getFieldset($fieldset->name); ?>
									<?php if (count($fields)) : ?>

										<?php if($fieldset->name == 'profile'){ ?>
											<?php // Start load regions this djclass ?>
												<div class="user_region_selects">
												<?php
													$mod_id = '999';
													$parent_id=0;
													$lc=0;

													$db= JFactory::getDBO();
													$query = "SELECT r.* FROM #__djcf_regions r "
															."WHERE r.published=1 "
															."ORDER BY r.parent_id, r.name COLLATE utf8_polish_ci ";
													$db->setQuery($query);
													$regions=$db->loadObjectList();

													$reg_sel = '<select  class="inputbox" id="se'.$mod_id.'_reg_0" name="se_regs[]" onchange="se'.$mod_id.'_new_reg(0,this.value,new Array(),  jQuery(this) )"><option value="0">Выберете местоположение</option>';
													$lcount = count($regions);

													foreach($regions as $l){
														$lc++;
														if($parent_id!=$l->parent_id){
															$reg_sel .= '</select>';
															echo $reg_sel;
															break;
														}
														$reg_sel .= '<option value="'.$l->id.'">'.str_ireplace("'", "&apos;", $l->name).'</option>';

														if($parent_id==$l->parent_id && $lc==$lcount){
															$reg_sel .= '</select>';
															echo $reg_sel;
															break;
														}
													}
												?>
												<div id="se<?php echo $mod_id;?>_after_reg_0"></div>
											</div>
											<?php // End load regions this djclass ?>
										<?php } ?>

										<fieldset>
											<?php // If the fieldset has a label set, display it as the legend. ?>
											<?= $this->form->renderFieldset($fieldset->name); ?>
										</fieldset>
									<?php endif; ?>
								<?php } ?>
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
							}
							?>
					</div>
				</div>
			</div>
		</div>
	</div>


	<script type="text/javascript">
		var se<?php echo $mod_id;?>_regs=new Array();
		var se<?php echo $mod_id;?>_current=0;

		function se<?php echo $mod_id;?>_new_reg(parent,a_parent,r_path,this_value){
			var myRequest = new Request({
				url: '<?php echo JURI::base()?>index.php',
				method: 'post',
				data: {
					'option': 'com_djclassifieds',
					'view': 'item',
					'task': 'getRegionSelect',
					'reg_id': a_parent,
					'mod_id': <?php echo $mod_id;?>
				},
				onRequest: function(){
					document.id('se<?php echo $mod_id;?>_after_reg_'+parent).innerHTML = '<div style="text-align:center;"><img src="<?php echo JURI::base(true) ?>/components/com_djclassifieds/assets/images/loading.gif" alt="..." /></div>';
				},
				onSuccess: function(responseText){
					if(responseText){
						document.id('se<?php echo $mod_id;?>_after_reg_'+parent).innerHTML = responseText;
						document.id('se<?php echo $mod_id;?>_reg_'+parent).value=a_parent;
					}else{
						document.id('se<?php echo $mod_id;?>_after_reg_'+parent).innerHTML = '';
						document.id('se<?php echo $mod_id;?>_reg_'+parent).value=a_parent;
					}
				},
				onFailure: function(){}
			});
			myRequest.send();
		}

		 <?php // Start add region in default input joomla ?>
		document.querySelector('#member-registration .btn').addEventListener('click',function (e){
			jQuery('#jform_profile_address1').val(jQuery('#se999_reg_0 option:selected').text())

			jQuery('#jform_profile_country').val(jQuery('#se999_after_reg_0 > select option:selected').text())
			let	idAfterOption = jQuery('#se999_after_reg_0 > select option:selected').val();

			jQuery('#jform_profile_city').val(jQuery('#se999_reg_'+idAfterOption).find('option:selected').text())
		})
		<?php // Start add region in default input joomla ?>
	</script>
