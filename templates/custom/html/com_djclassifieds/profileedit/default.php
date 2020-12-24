<?php
	/**
	 * @version    2.0
	 * @package    DJ Classifieds
	 * @subpackage  DJ Classifieds Component
	 * @copyright  Copyright (C) 2010 DJ-Extensions.com LTD, All rights reserved.
	 * @license    http://www.gnu.org/licenses GNU/GPL
	 * @autor url    http://design-joomla.eu
	 * @autor email  contact@design-joomla.eu
	 * @Developer    Lukasz Ciastek - lukasz.ciastek@design-joomla.eu
	 *
	 */
	defined('_JEXEC') or die('Restricted access');

	//jimport('joomla.media.images');
	JHTML::_('behavior.framework', 'More');
	JHTML::_('behavior.keepalive');
	JHTML::_('behavior.formvalidation');
	JHTML::_('behavior.modal');
	JHTML::_('behavior.calendar');
	$toolTipArray = array('className' => 'djcf_label');
	//JHTML::_('behavior.tooltip', '.Tips1', $toolTipArray);
	$par = JComponentHelper::getParams('com_djclassifieds');
	$user = JFactory::getUser();
	$app = JFactory::getApplication();

	jimport('joomla.application.module.helper');
	$attribs['style'] = 'none';
	
	$mod_attribs = array();
	$mod_attribs['style'] = 'xhtml';
	$document = JFactory::getDocument();
	$menus = $app->getMenu('site');

	$menu_jprofileedit_itemid = $menus->getItems('link', 'index.php?option=com_users&view=profile&layout=edit', 1);
	$juser_edit_profile = 'index.php?option=com_users&view=profile&layout=edit';
	if ($menu_jprofileedit_itemid) {
		$juser_edit_profile .= '&Itemid=' . $menu_jprofileedit_itemid->id;
	}


	include(JPATH_BASE .'/templates/custom/html/com_djclassifieds/profileedit/_getRalationForSlogin.php');

?>

<div id="dj-classifieds" class="clearfix djcftheme-<?php echo $par->get('theme', 'default'); ?>">
	<?php
		$modules_djcf = &JModuleHelper::getModules('djcf-top');
		if (count($modules_djcf) > 0) {
			echo '<div class="djcf-ad-top clearfix">';
			foreach (array_keys($modules_djcf) as $m) {
				echo JModuleHelper::renderModule($modules_djcf[$m], $mod_attribs);
			}
			echo '</div>';
		}

		$modules_djcf = &JModuleHelper::getModules('djcf-profileedit-top');
		if (count($modules_djcf) > 0) {
			echo '<div class="djcf-ad-items-top clearfix">';
			foreach (array_keys($modules_djcf) as $m) {
				echo JModuleHelper::renderModule($modules_djcf[$m], $mod_attribs);
			}
			echo '</div>';
		}
	?>

	<div class="row">
		<div class="col-md-12">
			<div class="dj-additem clearfix bg-white mb-3 ">
				<div class="bg-white__header">
					<div class="h2"><?php echo JText::_('COM_DJCLASSIFIEDS_PROFILE_EDITION'); ?></div>
				</div>
				   
				<form action="<?php echo JRoute::_('index.php'); ?>" method="post" class="form-validate p-3" name="djForm" id="djForm"  enctype="multipart/form-data">
					<div class="additem_djform profile_edit_djform">
						<div class="additem_djform_in">
							<?php if ($par->get('profile_avatar_source', '')) { ?>
								<div class="">
									<label class="label" for="cat_0" id="cat_0-lbl">
										<?php echo JText::_('COM_DJCLASSIFIEDS_PROFILE_IMAGE'); ?>
									</label>
									<div class="djform_field">
										<?php echo DJClassifiedsSocial::getUserAvatar($user->id, $par->get('profile_avatar_source', ''), 'L'); ?>
									</div>
									<div class="clear_both"></div>
								</div>
							<?php } else { ?>

								<div class="">
									<span class="label_grey">
										<?= JText::_('COM_DJCLASSIFIEDS_PROFILE_IMAGE') ?>
									</span>
									<div class="djform_field djform_field_foto-block">
										<?php if (!$this->avatar){
											echo '<img style="width:' . $par->get('profth_width', '190') . 'px" src="' . JURI::base(true) . '/components/com_djclassifieds/assets/images/default_profile.png" />';
										}else{
											echo '<img src="' . JURI::root() . $this->avatar->path . $this->avatar->name . '_th.' . $this->avatar->ext . '" />'; 
												} ?>
										<div class="">
											<input type="checkbox" name="del_avatar" id="del_avatar" value="<?php echo $this->avatar->id; ?>"/>
											<input type="hidden" name="del_avatar_id" value="<?php echo $this->avatar->id; ?>"/>
											<?php echo JText::_('COM_DJCLASSIFIEDS_CHECK_TO_DELETE');   ?>
										</div>
									</div>
								</div>

								<div class="text-center bb mb-5 pb-3">
									<div class="djform_field input_file__outer">
										<input type="file" name="new_avatar"/>
									</div>
								</div>
								
								<?php
							}

								echo $this->loadTemplate('localization');

								foreach ($this->custom_fields as $fl) {
									echo '<div class="mb-20 pb-20"> ';
									if ($fl->type == "inputbox") {
										if ($this->custom_values_c > 0) {
											$fl_value = $fl->value;
										} else {
											$fl_value = $fl->default_value;
										}
										$fl_value = htmlspecialchars($fl_value);

										$val_class = '';
										$req = '';
										$fl_cl = '';
										if ($fl->required) {
											$fl_cl = 'inputbox required';
											$req = ' * ';
										} else {
											$fl_cl = 'inputbox';
										}

										if ($fl->numbers_only) {
											$fl_cl .= ' validate-numeric';
										}
										$cl = 'class="' . $fl_cl . '" ';

										if ($par->get('show_tooltips_newad', '0') && $fl->description) {
											echo '<label class="label Tips1" for="dj' . $fl->name . '" title="' . $fl->description . '" id="dj' . $fl->name . '-lbl" >' . $fl->label . $req;
											echo ' <img src="' . JURI::base(true) . '/components/com_djclassifieds/assets/images/tip.png" alt="?" />';
											echo '</label>';
										} else {
											echo '<label class="label" for="dj' . $fl->name . '" id="dj' . $fl->name . '-lbl" >' . $fl->label . $req . '</label>';
										}

										echo '<div class="djform_field">';

										echo '<input ' . $cl . ' type="text" id="dj' . $fl->name . '" name="' . $fl->name . '" ' . $fl->params;
										echo ' value="' . $fl_value . '" ';
										echo ' />';
										echo '</div>';
									} else if ($fl->type == "textarea") {
										if ($this->custom_values_c > 0) {
											$fl_value = $fl->value;
										} else {
											$fl_value = $fl->default_value;
										}
										$fl_value = htmlspecialchars($fl_value);

										$val_class = '';
										$req = '';
										if ($fl->required) {
											$cl = 'class="inputbox required" ';
											$req = ' * ';
										} else {
											$cl = 'class="inputbox"';
										}

										if ($par->get('show_tooltips_newad', '0') && $fl->description) {
											echo '<label class="label Tips1" for="dj' . $fl->name . '" title="' . $fl->description . '" id="dj' . $fl->name . '-lbl" >' . $fl->label . $req;
											echo ' <img src="' . JURI::base(true) . '/components/com_djclassifieds/assets/images/tip.png" alt="?" />';
											echo '</label>';
										} else {
											echo '<label class="label" for="dj' . $fl->name . '" id="dj' . $fl->name . '-lbl">' . $fl->label . $req . '</label>';
										}
										echo '<div class="djform_field">';
										echo '<textarea ' . $cl . ' id="dj' . $fl->name . '" name="' . $fl->name . '" ' . $fl->params . ' />';
										echo $fl_value;
										echo '</textarea>';
										echo '</div>';
									} else if ($fl->type == "selectlist") {
										if ($this->custom_values_c > 0) {
											$fl_value = $fl->value;
										} else {
											$fl_value = $fl->default_value;
										}

										$val_class = '';
										$req = '';
										if ($fl->required) {
											$cl = 'class="inputbox required" ';
											$req = ' * ';
										} else {
											$cl = 'class="inputbox"';
										}

										if ($par->get('show_tooltips_newad', '0') && $fl->description) {
											echo '<label class="label Tips1" for="dj' . $fl->name . '" title="' . $fl->description . '" id="dj' . $fl->name . '-lbl" >' . $fl->label . $req;
											echo ' <img src="' . JURI::base(true) . '/components/com_djclassifieds/assets/images/tip.png" alt="?" />';
											echo '</label>';
										} else {
											echo '<label class="label" for="dj' . $fl->name . '" id="dj' . $fl->name . '-lbl">' . $fl->label . $req . '</label>';
										}
										echo '<div class="djform_field">';
										echo '<select ' . $cl . ' id="dj' . $fl->name . '" name="' . $fl->name . '" ' . $fl->params . ' >';
										if (substr($fl->values, -1) == ';') {
											$fl->values = substr($fl->values, 0, -1);
										}
										$val = explode(';', $fl->values);
										for ($i = 0; $i < count($val); $i++) {
											if ($fl_value == $val[$i]) {
												$sel = "selected";
											} else {
												$sel = "";
											}
											echo '<option ' . $sel . ' value="' . $val[$i] . '">';
											if ($par->get('cf_values_to_labels', '0')) {
												echo JText::_('COM_DJCLASSIFIEDS_' . str_ireplace(' ', '_', strtoupper($val[$i])));
											} else {
												echo $val[$i];
											}
											echo '</option>';
										}

										echo '</select>';
										echo '</div>';
									} else if ($fl->type == "radio") {
										if ($this->custom_values_c > 0) {
											$fl_value = $fl->value;
										} else {
											$fl_value = $fl->default_value;
										}

										$val_class = '';
										$req = '';
										if ($fl->required) {
											$cl = 'class="required validate-radio" ';
											$req = ' * ';
										} else {
											$cl = 'class=""';
										}

										if ($par->get('show_tooltips_newad', '0') && $fl->description) {
											echo '<label class="label Tips1" for="dj' . $fl->name . '" title="' . $fl->description . '" id="dj' . $fl->name . '-lbl" >' . $fl->label . $req;
											echo ' <img src="' . JURI::base(true) . '/components/com_djclassifieds/assets/images/tip.png" alt="?" />';
											echo '</label>';
										} else {
											echo '<label class="label" for="dj' . $fl->name . '" id="dj' . $fl->name . '-lbl">' . $fl->label . $req . '</label>';
										}
										echo '<div class="djform_field">';
										if (substr($fl->values, -1) == ';') {
											$fl->values = substr($fl->values, 0, -1);
										}
										$val = explode(';', $fl->values);
										echo '<div class="radiofield_box" style="float:left">';
										for ($i = 0; $i < count($val); $i++) {
											$checked = '';
											if ($fl_value == $val[$i]) {
												$checked = 'CHECKED';
											}

											echo '<div style="float:left;"><input type="radio" ' . $cl . '  ' . $checked . ' value ="' . $val[$i] . '" name="' . $fl->name . '" id="dj' . $fl->name . $i . '" />';
											echo '<label for="dj' . $fl->name . $i . '" class="radio_label dj' . $fl->name . $i . '">';
											if ($par->get('cf_values_to_labels', '0')) {
												echo JText::_('COM_DJCLASSIFIEDS_' . str_ireplace(' ', '_', strtoupper($val[$i])));
											} else {
												echo $val[$i];
											}
											echo '</label>';
											echo '</div>';
										}
										echo '</div>';
										echo '</div>';
									} else if ($fl->type == "checkbox") {
										$val_class = '';
										$req = '';
										if ($this->custom_values_c > 0) {
											$fl_value = $fl->value;
										} else {
											$fl_value = $fl->default_value;
										}

										if ($fl->required) {
											$cl = 'class="checkboxes required" ';
											$req = ' * ';
										} else {
											$cl = 'class=""';
										}
										if ($par->get('show_tooltips_newad', '0') && $fl->description) {
											echo '<label class="label Tips1" for="dj' . $fl->name . '" title="' . $fl->description . '" id="dj' . $fl->name . '-lbl" >' . $fl->label . $req;
											echo ' <img src="' . JURI::base(true) . '/components/com_djclassifieds/assets/images/tip.png" alt="?" />';
											echo '</label>';
										} else {
											echo '<label class="label" for="dj' . $fl->name . '" id="dj' . $fl->name . '-lbl">' . $fl->label . $req . '</label>';
										}
										echo '<div class="djform_field">';
										if (substr($fl->values, -1) == ';') {
											$fl->values = substr($fl->values, 0, -1);
										}
										$val = explode(';', $fl->values);
										echo '<div class="radiofield_box" style="float:left">';
										echo '<fieldset id="dj' . $fl->name . '" ' . $cl . ' >';
										for ($i = 0; $i < count($val); $i++) {
											$checked = '';
											if ($this->custom_values_c > 0) {
												if (strstr($fl->value, ';' . $val[$i] . ';')) {
													$checked = 'CHECKED';
												}
											} else {
												$def_val = explode(';', $fl->default_value);
												for ($d = 0; $d < count($def_val); $d++) {
													if ($def_val[$d] == $val[$i]) {
														$checked = 'CHECKED';
													}
												}

											}

											echo '<div style="float:left;"><input type="checkbox" id="dj' . $fl->name . $i . '" class="checkbox" ' . $checked . ' value ="' . $val[$i] . '" name="' . $fl->name . '[]" />';
											echo '<label for="dj' . $fl->name . $i . '" class="radio_label dj' . $fl->name . $i . '">';
											if ($par->get('cf_values_to_labels', '0')) {
												echo JText::_('COM_DJCLASSIFIEDS_' . str_ireplace(' ', '_', strtoupper($val[$i])));
											} else {
												echo $val[$i];
											}
											echo '</label>';
											echo '</div>';
										}
										echo '</fieldset>';
										echo '</div>';
										echo '</div>';
									} else if ($fl->type == "date") {


										if ($this->custom_values_c > 0) {
											$fl_value = $fl->value_date;
										} else {
											if ($fl->default_value == 'current_date') {
												$fl_value = date("Y-m-d");
											} else {
												$fl_value = $fl->default_value;
											}
										}

										$val_class = '';
										$req = '';
										if ($fl->required) {
											//$cl = 'class="inputbox required djcalendar" ';
											$cl = 'inputbox required djcalendar';
											$req = ' * ';
										} else {
											//$cl = 'class="inputbox djcalendar"';
											$cl = 'inputbox djcalendar';
										}

										if ($par->get('show_tooltips_newad', '0') && $fl->description) {
											echo '<label class="label Tips1" for="dj' . $fl->name . '" title="' . $fl->description . '" id="dj' . $fl->name . '-lbl" >' . $fl->label . $req;
											echo ' <img src="' . JURI::base(true) . '/components/com_djclassifieds/assets/images/tip.png" alt="?" />';
											echo '</label>';
										} else {
											echo '<label class="label" for="dj' . $fl->name . '" id="dj' . $fl->name . '-lbl" >' . $fl->label . $req . '</label>';
										}

										echo '<div class="djform_field">';

										/*echo '<input '.$cl.' type="text" size="10" maxlenght="19" id="dj'.$fl->name.'" name="'.$fl->name.'" '.$fl->params;
										echo ' value="'.$fl_value.'" ';
										echo ' />';
										echo ' <img class="calendar" src="'.JURI::base(true).'/components/com_djclassifieds/assets/images/calendar.png" alt="calendar" id="dj'.$fl->name.'button" />';*/
										echo JHTML::calendar($fl_value, $fl->name, 'dj' . $fl->name, '%Y-%m-%d', array('size' => '10', 'maxlength' => '19', 'class' => $cl));
										echo '</div>';

									} else if ($fl->type == "date_from_to") {


										if ($this->custom_values_c > 0) {
											$fl_value = $fl->value_date;
											$fl_value_to = $fl->value_date_to;
										} else {
											if ($fl->default_value == 'current_date') {
												$fl_value = date("Y-m-d");
												$fl_value_to = date("Y-m-d");
											} else {
												$fl_value = $fl->default_value;
												$fl_value_to = $fl->default_value;
											}
										}

										$val_class = '';
										$req = '';
										if ($fl->required) {
											//$cl = 'class="inputbox required djcalendar" ';
											$cl = 'inputbox required djcalendar';
											$req = ' * ';
										} else {
											//$cl = 'class="inputbox djcalendar"';
											$cl = 'inputbox djcalendar';
										}

										if ($par->get('show_tooltips_newad', '0') && $fl->description) {
											echo '<label class="label Tips1" for="dj' . $fl->name . '" title="' . $fl->description . '" id="dj' . $fl->name . '-lbl" >' . $fl->label . $req;
											echo ' <img src="' . JURI::base(true) . '/components/com_djclassifieds/assets/images/tip.png" alt="?" />';
											echo '</label>';
										} else {
											echo '<label class="label" for="dj' . $fl->name . '" id="dj' . $fl->name . '-lbl" >' . $fl->label . $req . '</label>';
										}

										echo '<div class="djform_field">';

										/*echo '<input '.$cl.' type="text" size="10" maxlenght="19" id="dj'.$fl->name.'" name="'.$fl->name.'" '.$fl->params;
										echo ' value="'.$fl_value.'" ';
										echo ' />';
										echo ' <img class="calendar" src="'.JURI::base(true).'/components/com_djclassifieds/assets/images/calendar.png" alt="calendar" id="dj'.$fl->name.'button" />';*/
										echo JHTML::calendar($fl_value, $fl->name, 'dj' . $fl->name, '%Y-%m-%d', array('size' => '10', 'maxlength' => '19', 'class' => $cl));

										echo '<span class="date_from_to_sep"> - </span>';

										/*echo '<input '.$cl.' type="text" size="10" maxlenght="19" id="dj'.$fl->name.'_to" name="'.$fl->name.'_to" '.$fl->params;
										echo ' value="'.$fl_value_to.'" ';
										echo ' />';
										echo ' <img class="calendar" src="'.JURI::base(true).'/components/com_djclassifieds/assets/images/calendar.png" alt="calendar" id="dj'.$fl->name.'_tobutton" />';*/
										echo JHTML::calendar($fl_value_to, $fl->name, 'dj' . $fl->name, '%Y-%m-%d', array('size' => '10', 'maxlength' => '19', 'class' => $cl));
										echo '</div>';

									} else if ($fl->type == "link") {
										if ($this->custom_values_c > 0) {
											$fl_value = $fl->value;
										} else {
											$fl_value = $fl->default_value;
										}
										$fl_value = htmlspecialchars($fl_value);

										$val_class = '';
										$req = '';
										if ($fl->required) {
											$cl = 'class="inputbox required" ';
											$req = ' * ';
										} else {
											$cl = 'class="inputbox"';
										}

										if ($par->get('show_tooltips_newad', '0') && $fl->description) {
											echo '<label class="label Tips1" for="dj' . $fl->name . '" title="' . $fl->description . '" id="dj' . $fl->name . '-lbl" >' . $fl->label . $req;
											echo ' <img src="' . JURI::base(true) . '/components/com_djclassifieds/assets/images/tip.png" alt="?" />';
											echo '</label>';
										} else {
											echo '<label class="label" for="dj' . $fl->name . '" id="dj' . $fl->name . '-lbl" >' . $fl->label . $req . '</label>';
										}

										echo '<div class="djform_field">';

										echo '<input ' . $cl . ' type="text" id="dj' . $fl->name . '" name="' . $fl->name . '" ' . $fl->params;
										echo ' value="' . $fl_value . '" ';
										echo ' />';
										echo '</div>';
									}

									echo '</div>';
								}

								echo $this->loadTemplate('core');

							?>
						</div>

						<?php echo $this->loadTemplate('more'); ?>

					</div>
					<?php
						if (count($this->plugin_sections)) {
							foreach ($this->plugin_sections as $plugin_section) {
								echo $plugin_section;
							}
						}
					?>
					<label id="verification_alert" style="display:none;color:red;"/>
					<?php echo JText::_('COM_DJCLASSIFIEDS_ENTER_ALL_REQUIRED_FIELDS'); ?>
					</label>
					<div class="classifieds_buttons">
						<?php
							$cancel_link = JRoute::_('index.php?option=com_djclassifieds&view=profile&Itemid=' . JRequest::getVar('Itemid', '0'), false);
						?>
						<a class="btn btn_accent-black"
						   href="<?php echo $cancel_link; ?>"><?php echo JText::_('COM_DJCLASSIFIEDS_CANCEL') ?></a>

						<button class="button validate btn btn_accent" type="submit"
						        id="submit_button"><?php echo JText::_('COM_DJCLASSIFIEDS_SAVE'); ?></button>
						<input type="hidden" name="user_id" value="<?php echo $user->id ?>"/>
						<input type="hidden" name="option" value="com_djclassifieds"/>
						<input type="hidden" name="view" value="profileedit"/>
						<input type="hidden" name="task" value="save"/>
						<input type="hidden" name="boxchecked" value="0"/>
						<?php echo JHtml::_('form.token'); ?>
					</div>
				</form>
			</div>
		</div>

		<div class="col-md-6">
			<div class="bg-white menu_for_user mb-3 ">
				<?php
					$modules = JModuleHelper::getModules('menu_for_user');
					foreach($modules as $module) {
						echo JModuleHelper::renderModule($module, $attribs);
					}
				?>
			</div>
			<div class="bg-white menu_for_user mb-3">
				<?php
					$modules = JModuleHelper::getModules('menu_for_user_asaid_profile');
					foreach($modules as $module) {
						echo JModuleHelper::renderModule($module, $attribs);
					}
				?>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	window.addEvent('domready', function () {
		var JTooltips = new Tips($$('.Tips1'), {
			showDelay: 200, hideDelay: 200, className: 'djcf_label', fixed: true
		});
		var djcals = document.getElements('.djcalendar');
		if (djcals) {
			var startDate = new Date(2008, 8, 7);
			djcals.each(function (djcla, index) {
				Calendar.setup({
					inputField: djcla.id,
					ifFormat: "%Y-%m-%d",
					button: djcla.id + "button",
					date: startDate
				});
			});
		}
	})

	document.id('submit_button').addEvent('click', function () {
		if (document.getElements('#djForm .invalid').length > 0) {
			document.id('verification_alert').setStyle('display', 'block');
			(function () {
				document.id('verification_alert').setStyle('display', 'none');
			}).delay(3000);
			return false;
		} else {
			return true;
		}
	});


	function addInfoForInput(){
		fetch('https://api.sypexgeo.net/')
			.then((response) => {
				return response.json();
			})
			.then((data) => {
				console.log(data);
				let inputPhone =  jQuery('#jform_profile_phone');
				if(!inputPhone.val()){
					inputPhone.attr('placeholder', '+'+data.country.phone+'...' )

					inputPhone.click(function (){
						inputPhone.val('+'+data.country.phone )
					})
				}
			});
	}
	addInfoForInput();

</script>