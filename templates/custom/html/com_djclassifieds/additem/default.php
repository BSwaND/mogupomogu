<?php
	/**
	 * @version		2.0
	 * @package		DJ Classifieds
	 * @subpackage	DJ Classifieds Component
	 * @copyright	Copyright (C) 2010 DJ-Extensions.com LTD, All rights reserved.
	 * @license		http://www.gnu.org/licenses GNU/GPL
	 * @autor url    http://design-joomla.eu
	 * @autor email  contact@design-joomla.eu
	 * @Developer    Lukasz Ciastek - lukasz.ciastek@design-joomla.eu
	 *
	 *
	 */
	defined ('_JEXEC') or die('Restricted access');

	//jimport('joomla.media.images');
	JHTML::_('behavior.framework','More');
	JHTML::_('behavior.keepalive');
	JHTML::_('behavior.formvalidation');
	JHTML::_('behavior.modal');
	JHTML::_('behavior.calendar');
	$toolTipArray = array('className'=>'djcf_label');
	JHTML::_('behavior.tooltip', '.Tips1', $toolTipArray);


	$par 	    = $this->par;
	$app 	    = JFactory::getApplication();
	$imglimit   = $par->get('img_limit','3');
	$unit_price = $par->get('unit_price','');
	$id 		= JRequest::getVar('id', 0, '', 'int' );
	$user 		= JFactory::getUser();

	$mod_attribs=array();
	$mod_attribs['style'] = 'xhtml';

	$document= JFactory::getDocument();
	$config = JFactory::getConfig();
	DJClassifiedsTheme::includeMapsScript();

	/*if($par->get('region_add_type','1')==1){
		$document->addScript("http://maps.google.com/maps/api/js?sensor=false&language=".$par->get('region_lang','en'));
		$assets=JURI::base(true).'/components/com_djclassifieds/assets/';
		$document->addScript($assets.'scripts.js');
	}*/
	$points_a = $par->get('points',0);
	if($points_a){
		$menus	= $app->getMenu('site');
		$menu_ppackages_itemid = $menus->getItems('link','index.php?option=com_djclassifieds&view=points',1);
		$user_ppoints_link='index.php?option=com_djclassifieds&view=points';
		if($menu_ppackages_itemid){
			$user_ppoints_link .= '&Itemid='.$menu_ppackages_itemid->id;
		}
	}

	$map_styles = $par->get('gm_styles','');
	if (trim($map_styles) == '') {
		$map_styles = '[]';
	}

	$token = JRequest::getCMD('token', '' );

	jimport('joomla.application.module.helper');
	$attribs['style'] = 'none';
?>
<div class="main_body">
	<div class="row">
		<div class="col-md-12">
			<div class="bg-white mb-3">
				<div class="bg-white__header  mb-3">
					<div class="h2">
						<?php
								if(JRequest::getVar('copy', 0, '', 'int' )>0){
									echo JText::_('COM_DJCLASSIFIEDS_COPY_OF_AD');
								}else if(JRequest::getVar('id', 0, '', 'int' )>0){
									echo JText::_('COM_DJCLASSIFIEDS_EDIT_AD');
								}else{
									echo JText::_('COM_DJCLASSIFIEDS_NEW_AD');
								}
							?>
					</div>
				</div>

				<div id="dj-classifieds" class="clearfix djcftheme-<?php echo $par->get('theme','default');?>">
					<?php
						$modules_djcf = &JModuleHelper::getModules('djcf-top');
						if(count($modules_djcf)>0){
							echo '<div class="djcf-ad-top clearfix">';
							foreach (array_keys($modules_djcf) as $m){
								echo JModuleHelper::renderModule($modules_djcf[$m],$mod_attribs);
							}
							echo'</div>';
						}

						$modules_djcf = &JModuleHelper::getModules('djcf-additem-top');
						if(count($modules_djcf)>0){
							echo '<div class="djcf-ad-items-top clearfix">';
							foreach (array_keys($modules_djcf) as $m){
								echo JModuleHelper::renderModule($modules_djcf[$m],$mod_attribs);
							}
							echo'</div>';
						}

					?>

					<div class="dj-additem clearfix">
						<form action="<?php echo JRoute::_('index.php'); ?>" method="post" class="form-validate" name="djForm" id="djForm"  enctype="multipart/form-data">
							<div class="additem_djform">
								<div class="additem_djform_in">
									<center><img src='<?php echo JURI::base(true) ?>/components/com_djclassifieds/assets/images/long_loader.gif' alt='LOADING' style='display: none;' id='upload_loading' /><div id="alercik"></div></center>

									<?php if(count($this->plugin_title)){
										foreach($this->plugin_title as $plugin_title){
											echo $plugin_title;
										}
									}?>

									<?php if($points_a){ ?>
										<div class="djform_row points_info_row ">
											<div class="oints_info_row_in">
												<?php echo JText::_('COM_DJCLASSIFIEDS_POINTS_PACKAGE_INFO_IN_NEW_ADVERT'); ?>
												<a target="_blank" href="<?php echo JRoute::_($user_ppoints_link); ?>"><?php echo JText::_('COM_DJCLASSIFIEDS_POINTS_PACKAGES'); ?></a>
											</div>
										</div>
									<?php }?>


									<div class="djform_row">
										<?php if($par->get('show_tooltips_newad','0')){ ?>
											<label class="label Tips1" for="name" id="name-lbl" title="<?php echo JTEXT::_('COM_DJCLASSIFIEDS_TITLE_TOOLTIP')?>">
												<?php echo JText::_('COM_DJCLASSIFIEDS_TITLE');?> *
												<?php if($par->get('title_char_limit','0')>0){ ?>
													<span id="title_limit">(<?php echo $par->get('title_char_limit');?>)</span>
												<?php } ?>
												<img src="<?php echo JURI::base(true) ?>/components/com_djclassifieds/assets/images/tip.png" alt="?" />
											</label>
										<?php }else{ ?>
											<label class="label" for="name" id="name-lbl" >
												<?php echo JText::_('COM_DJCLASSIFIEDS_TITLE');?> *
												<?php if($par->get('title_char_limit','0')>0){ ?>
													<span id="title_limit">(<?php echo $par->get('title_char_limit');?>)</span>
												<?php } ?>
											</label>
										<?php } ?>
										<div class="djform_field">
											<?php
												$title_char_limit = $par->get('title_char_limit','0');
												if($title_char_limit>0){
													$input_title_limit =' onkeyup="titleLimit('.$title_char_limit.');" ';
												}else{
													$input_title_limit ='';
												} ?>
											<input class="inputbox required" <?php echo $input_title_limit; ?> type="text" name="name" id="name" size="50" maxlength="250" value="<?php echo $this->item->name; ?>" />
										</div>
										<div class="clear_both"></div>
									</div>

									<div class="djform_row">
										<?php if($par->get('show_tooltips_newad','0')){ ?>
											<label class="label Tips1" for="cat_0" id="cat_0-lbl" title="<?php echo JTEXT::_('COM_DJCLASSIFIEDS_CATEGORY_TOOLTIP')?>">
												<?php echo JText::_('COM_DJCLASSIFIEDS_CATEGORY');?> *
												<img src="<?php echo JURI::base(true) ?>/components/com_djclassifieds/assets/images/tip.png" alt="?" />
											</label>
										<?php }else{ ?>
											<label class="label" for="cat_0" id="cat_0-lbl">
												<?php echo JText::_('COM_DJCLASSIFIEDS_CATEGORY'); ?> *
											</label>
										<?php } ?>
										<div class="djform_field">
											<?php
												$cat_sel = '<select autocomplete="off" class="cat_sel required validate-djcat" id="cat_0" name="cats[]" onchange="new_cat(0,this.value,new Array());getFields(this.value,false);"><option value="">'.JText::_('COM_DJCLASSIFIEDS_PLEASE_SELECT_CATEGORY').'</option>';
												$parent_id=0;
												foreach($this->cats as $l){
													if($parent_id!=$l->parent_id){
														break;
													}
													if($l->price>0 || ($l->points>0 && $points_a)){
														$l->price = $l->price/100;
														$l->name .= ' (';
														if($points_a!=2){
															$l->name .=DJClassifiedsTheme::priceFormat($l->price,$unit_price);
														}
														if($l->points>0 && $points_a){
															if($points_a!=2){
																$l->name .= ' - ';
															}
															$l->name .= $l->points.JTEXT::_('COM_DJCLASSIFIEDS_POINTS_SHORT');
														}
														if($l->price_special>0){
															$l->name .= ' - '.DJClassifiedsTheme::priceFormat($l->price_special,$unit_price).' '.JTEXT::_('COM_DJCLASSIFIEDS_SPECIAL_PRICE_SHORT');
														}
														$l->name .= ')';
													}
													$cat_sel .= '<option value="'.$l->id.'">'.str_ireplace("'", "&apos;", $l->name).'</option>';
												}
												$cat_sel .= '</select>';
												echo $cat_sel;

											?><div class="clear_both"></div>
											<div id="after_cat_0"></div>
											<script type="text/javascript">
												var cats=new Array();

												<?php
												/*$cat_sel = '<select style="width:210px" class="cat_sel required validate-djcat" name="cats[]" id="cat_0" onchange="new_cat(0,this.value);getFields(this.value);">';
												$parent_id=0;

												$cat_req = array();
												foreach($this->cats as $l){
													if($l->ads_disabled){
														$cat_req[$l->id]=1;
													}
												}

												foreach($this->cats as $l){
													if($parent_id!=$l->parent_id){
														$cat_sel .= '</select>';
														echo "cats[$parent_id]='$cat_sel<div id=\"after_cat_$parent_id\"></div>';";
														$parent_id=$l->parent_id;
														$cl_select = '';
														if($l->ads_disabled || isset($cat_req[$parent_id])){
															$cl_select = ' class="cat_sel required validate-djcat" ';
														}
														$cat_sel = '<div class="clear_both"></div><select '.$cl_select.' style="width:210px" name="cats[]" id="cat_'.$l->parent_id.'" onchange="new_cat('.$parent_id.',this.value);getFields(this.value);">';
														$cat_sel .= '<option value="p'.$parent_id.'">'.JTEXT::_('COM_DJCLASSIFIEDS_CATEGORY_SELECTOR_EMPTY_VALUE').'</option>';
													}
													if($l->price>0){
														$l->price = $l->price/100;
														$l->name .= ' ('.DJClassifiedsTheme::priceFormat($l->price,$unit_price);
															if($l->points>0 && $points_a){
																$l->name .= ' - '.$l->points.JTEXT::_('COM_DJCLASSIFIEDS_POINTS_SHORT');
															}
														$l->name .= ')';
													}
													$cat_sel .= '<option value="'.$l->id.'">'.str_ireplace("'", "&apos;", $l->name).'</option>';
												}
												$cat_sel .= '</select>';
												echo "cats[$parent_id]='$cat_sel<div id=\"after_cat_$parent_id\"></div>';";
												*/
												?>
												var current=0;

												function new_cat(parent,a_parent,c_path){


													var myRequest = new Request({
														url: '<?php echo JURI::base()?>index.php',
														method: 'post',
														data: {
															'option': 'com_djclassifieds',
															'view': 'additem',
															'task': 'getCategorySelect',
															'cat_id': a_parent
															<?php if($this->subscr_id){echo ",'subscr_id':".$this->subscr_id."";}?>
														},
														onRequest: function(){
															document.id('after_cat_'+parent).innerHTML = '<div style="text-align:center;"><img src="<?php echo JURI::base(true) ?>/components/com_djclassifieds/assets/images/loading.gif" alt="..." /></div>';
														},
														onSuccess: function(responseText){
															if(responseText){
																document.id('after_cat_'+parent).innerHTML = responseText;
																document.id('cat_'+parent).value=a_parent;
															}else{
																document.id('after_cat_'+parent).innerHTML = '';
																document.id('cat_'+parent).value=a_parent;
															}
															if(c_path != 'null'){
																if(c_path.length>0){
																	var first_path = c_path[0].split(',');
																	c_path.shift();
																	new_cat(first_path[0],first_path[1],c_path);
																}
															}
															document.id('after_cat_'+parent).removeClass('invalid');
															document.id('after_cat_'+parent).setAttribute("aria-invalid", "false");
															<?php if($par->get('durations_list','') && $id==0){ ?>
															updateDuration(a_parent);
															<?php } ?>
														},
														onFailure: function(){}
													});
													myRequest.send();


													/*if(cats[a_parent]){
														//alert(cats[v]);
														document.id('after_cat_'+parent).innerHTML = cats[a_parent];
														document.id('cat_'+parent).value=a_parent;
													}else{
														document.id('after_cat_'+parent).innerHTML = '';
														document.id('cat_'+parent).value=a_parent;
													}
													document.id('after_cat_'+parent).removeClass('invalid');
													document.id('after_cat_'+parent).setAttribute("aria-invalid", "false");*/

												}
												<?php if(count($this->cat_path)){
													$c_path = $this->cat_path;
													echo 'var cat_path = new Array();';
													$cat_path_f = 'new_cat(';
													for($c=count($c_path)-1;$c>-1;$c--){
														if($c<count($c_path)-1){
															$ci = count($c_path) - $c -2;
															echo "cat_path[$ci]='$c_path[$c]';";
														}else{
															$cat_path_f .= $c_path[$c];
														}
													}
													echo $cat_path_f.',cat_path);';
												}?>
											</script>

										</div>
										<div class="clear_both"></div>
									</div>

									<?php /*if(count($this->plugin_category)){
										foreach($this->plugin_category as $plugin_category){
											echo $plugin_category;
										}
									} */?>

									<div class="djform_row extra_fields">
										<div id="ex_fields"></div>
										<div class="clear_both"></div>
									</div>
									<?php
										$types = $this->types;
										if($par->get('show_types','0') && $types){?>
											<div class="djform_row">
												<?php if($par->get('show_tooltips_newad','0')){ ?>
													<label class="label Tips1" for="type_id" id="type_id-lbl" title="<?php echo JTEXT::_('COM_DJCLASSIFIEDS_TYPE_TOOLTIP')?>">
														<?php echo JText::_('COM_DJCLASSIFIEDS_TYPE');if($par->get('types_required','0')){ echo ' * ';} ?>
														<img src="<?php echo JURI::base(true) ?>/components/com_djclassifieds/assets/images/tip.png" alt="?" />
													</label>
												<?php }else{ ?>
													<label class="label" for="type_id" id="type_id-lbl">
														<?php echo JText::_('COM_DJCLASSIFIEDS_TYPE');if($par->get('types_required','0')){ echo ' * ';} ?>
													</label>
												<?php } ?>
												<div class="djform_field">
													<?php if($par->get('types_display_layout','0')==1){

														echo '<div class="radiofield_box" style="float:left">';
														foreach($types as $type){
															$checked = '';
															if($type->id == $this->item->type_id){
																$checked = 'CHECKED';
															}
															echo '<div style="float:left;"><input type="radio" '.$checked.' value ="'.$type->id.'" name="type_id" id="type_id'.$type->id.'" />';
															echo '<label for="type_id'.$type->id.'" class="radio_label" style="display:inline-block;">';
															echo $type->preview.$type->pricing;
															echo '</label>';
															echo '</div>';
															echo '<div class="clear_both"></div>';
														}
														$checked = '';
														if($this->item->type_id == 0){
															$checked = 'CHECKED';
														}
														echo '<div style="float:left;"><input type="radio" '.$checked.' value ="0" name="type_id" id="type_id0" />';
														echo '<label for="type_id0" class="radio_label radio_label_none" style="display:inline-block;">';
														echo JText::_('COM_DJCLASSIFIEDS_NONE');
														echo '</label>';
														echo '</div>';
														echo '<div class="clear_both"></div>';
														echo '</div>';


														?>
													<?php }else{ ?>
														<select autocomplete="off" name="type_id" id="type_id" class="inputbox<?php if($par->get('types_required','0')){ echo ' required';} ?>" >
															<option value=""><?php echo JText::_('COM_DJCLASSIFIEDS_SELECT_TYPE');?></option>
															<?php echo JHtml::_('select.options', $types, 'value', 'text', $this->item->type_id, true);?>
														</select>
													<?php } ?>
													<div class="clear_both"></div>
												</div>
												<div class="clear_both"></div>
											</div>
										<?php }?>
									<?php if(count($this->regions) && $par->get('show_regions','1')){?>
										<div class="djform_row">
											<?php if($par->get('show_tooltips_newad','0')){ ?>
												<label class="label Tips1" for="reg_0" id="reg_0-lbl" title="<?php echo JTEXT::_('COM_DJCLASSIFIEDS_LOCALIZATION_TOOLTIP')?>">
													<?php echo JText::_('COM_DJCLASSIFIEDS_LOCALIZATION');?> *
													<img src="<?php echo JURI::base(true) ?>/components/com_djclassifieds/assets/images/tip.png" alt="?" />
												</label>
											<?php }else{ ?>
												<label class="label" for="reg_0" id="cat_0-lbl">
													<?php echo JText::_('COM_DJCLASSIFIEDS_LOCALIZATION'); ?> *
												</label>
											<?php } ?>
											<div class="djform_field" id="locations_list">
												<?php
													$reg_sel = '<select autocomplete="off" id="reg_0" class="required" name="regions[]" onchange="new_reg(0,this.value,new Array());"><option value="">'.JText::_('COM_DJCLASSIFIEDS_SELECT_LOCALIZATION').'</option>';
													$parent_id=0;
													foreach($this->regions as $l){
														if($parent_id!=$l->parent_id){
															break;
														}
														$reg_sel .= '<option value="'.$l->id.'">'.str_ireplace("'", "&apos;", $l->name).'</option>';
														//$ri++;
													}
													$reg_sel .= '</select>';
													echo $reg_sel;                     

												?><div class="clear_both"></div>
												<div id="after_reg_0"></div>
												<script type="text/javascript">
													var regs=new Array();

													<?php
													/*$reg_sel = '<select style="width:210px" name="regions[]" id="reg_0" onchange="new_reg(0,this.value);">';
													$parent_id=0;

													foreach($this->regions as $l){
														if($parent_id!=$l->parent_id){
															$reg_sel .= '</select>';
															echo "regs[$parent_id]='$reg_sel<div id=\"after_reg_$parent_id\"></div>';";
															$parent_id=$l->parent_id;
															$reg_sel = '<div class="clear_both"></div><select style="width:210px" name="regions[]" id="reg_'.$l->parent_id.'" onchange="new_reg('.$parent_id.',this.value);">';
															$reg_sel .= '<option value="">'.JTEXT::_('COM_DJCLASSIFIEDS_LOCATION_SELECTOR_EMPTY_VALUE').'</option>';
														}
														$reg_sel .= '<option value="'.$l->id.'">'.str_ireplace("'", "&apos;", $l->name).'</option>';
													}
													$reg_sel .= '</select>';
													echo "regs[$parent_id]='$reg_sel<div id=\"after_reg_$parent_id\"></div>';";*/

													?>
													var current=0;

													function new_reg(parent,a_parent, r_path){

														var myRequest = new Request({
															url: '<?php echo JURI::base()?>index.php',
															method: 'post',
															data: {
																'option': 'com_djclassifieds',
																'view': 'additem',
																'task': 'getRegionSelect',
																'reg_id': a_parent
															},
															onRequest: function(){
																document.id('after_reg_'+parent).innerHTML = '<div style="text-align:center;"><img src="<?php echo JURI::base(true) ?>/components/com_djclassifieds/assets/images/loading.gif" alt="..." /></div>';
															},
															onSuccess: function(responseText){
																if(responseText){
																	document.id('after_reg_'+parent).innerHTML = responseText;
																	document.id('reg_'+parent).value=a_parent;
																}else{
																	document.id('after_reg_'+parent).innerHTML = '';
																	document.id('reg_'+parent).value=a_parent;
																}
																if(r_path != 'null'){
																	if(r_path.length>0){
																		var first_path = r_path[0].split(',');
																		r_path.shift();
																		new_reg(first_path[0],first_path[1],r_path);
																	}
																}
																<?php if($par->get('places_in_address','0')==1 && $par->get('show_address','1')){ ?>
																if(typeof google !== 'undefined' && typeof google.maps !== 'undefined') se_country_iso(a_parent);
																<?php } ?>
															},
															onFailure: function(){}
														});
														myRequest.send();


														/*if(regs[a_parent]){
															//alert(cats[v]);
															document.id('after_reg_'+parent).innerHTML = regs[a_parent];
															document.id('reg_'+parent).value=a_parent;
														}else{
															document.id('after_reg_'+parent).innerHTML = '';
															document.id('reg_'+parent).value=a_parent;
														}*/

													}

													function se_country_iso(reg_id){

														var myRequest = new Request({
															url: '<?php echo JURI::base()?>index.php',
															method: 'post',
															data: {
																'option': 'com_djclassifieds',
																'view': 'item',
																'task': 'getCountryISO',
																'reg_id': reg_id
															},
															onRequest: function(){},
															onSuccess: function(responseText){
																if(responseText){
																	djcfAddressPlaces(responseText);
																	<?php if($par->get('allow_user_lat_lng','0')){ ?>
																	if(!document.getElementById('latitude').value && !document.getElementById('longitude').value){
																		geokoder.geocode(
																			{address: responseText},
																			function (results, status){
																				if(status == google.maps.GeocoderStatus.OK){
																					my_lat = results[0].geometry.location.lat();
																					my_lng = results[0].geometry.location.lng();
																					map.setCenter(new google.maps.LatLng(my_lat,my_lng));
																					var coord   = new google.maps.LatLng(my_lat, my_lng);
																					marker.setPosition(coord);
																				}
																			});
																		;
																	}
																	<?php } ?>
																}
															},
															onFailure: function(){}
														});
														myRequest.send();

													}

													<?php if(count($this->reg_path)){
														$r_path = $this->reg_path;
														echo 'var reg_path = new Array();';
														$reg_path_f = 'new_reg(';
														for($r=count($r_path)-1;$r>-1;$r--){
															if($r<count($r_path)-1){
																$ri = count($r_path) - $r -2;
																echo "reg_path[$ri]='$r_path[$r]';";
															}else{
																$reg_path_f .= $r_path[$r];
															}
														}

														echo $reg_path_f.',reg_path);';
													}?>
												</script>
											</div>
											<div class="clear_both"></div>
										</div>
									<?php }else{ ?>
										<input type="hidden" name="regions[]" value="0" />
									<?php } ?>
									<?php if($par->get('show_address','1')){?>
										<div class="djform_row">
											<?php if($par->get('show_tooltips_newad','0')){ ?>
												<label class="label Tips1" title="<?php echo JTEXT::_('COM_DJCLASSIFIEDS_ADDRESS_TOOLTIP')?>">
													<?php echo JText::_('COM_DJCLASSIFIEDS_ADDRESS');?>
													<img src="<?php echo JURI::base(true) ?>/components/com_djclassifieds/assets/images/tip.png" alt="?" />
												</label>
											<?php }else{ ?>
												<label class="label">
													<?php echo JText::_('COM_DJCLASSIFIEDS_ADDRESS'); ?>
												</label>
											<?php } ?>
											<div class="djform_field">
												<?php
													$addr_cl = '';
													$addr_placeholder = '';
													if($par->get('places_in_address','0')==1){
														$addr_cl = ' poi';
														if(JText::_('COM_DJCLASSIFIEDS_ADDRESS_PLACES_PLACEHOLDER') != 'COM_DJCLASSIFIEDS_ADDRESS_PLACES_PLACEHOLDER'){
															$addr_placeholder = ' placeholder="'.JText::_('COM_DJCLASSIFIEDS_ADDRESS_PLACES_PLACEHOLDER').'"';
														}
													}
												?>
												<input class="text_area<?php echo $addr_cl; ?>" type="text" name="address" id="address" size="50" maxlength="250" value="<?php echo $this->item->address; ?>"<?php echo $addr_placeholder; ?>/>
											</div>
											<div class="clear_both"></div>
										</div>
									<?php } ?>
									<?php if($par->get('show_postcode','0')){?>
										<div class="djform_row">
											<?php if($par->get('show_tooltips_newad','0')){ ?>
												<label class="label Tips1" title="<?php echo JTEXT::_('COM_DJCLASSIFIEDS_POSTCODE_TOOLTIP')?>">
													<?php echo JText::_('COM_DJCLASSIFIEDS_POSTCODE');?>
													<img src="<?php echo JURI::base(true) ?>/components/com_djclassifieds/assets/images/tip.png" alt="?" />
												</label>
											<?php }else{ ?>
												<label class="label">
													<?php echo JText::_('COM_DJCLASSIFIEDS_POSTCODE'); ?>
												</label>
											<?php } ?>
											<div class="djform_field">
												<input class="text_area" type="text" name="post_code" id="post_code" size="50" maxlength="250" value="<?php echo $this->item->post_code; ?>" />
											</div>
											<div class="clear_both"></div>
										</div>
										<?php
									}
										if($par->get('allow_user_lat_lng','0')){?>
											<div class="djform_row" <?php if($par->get('allow_user_lat_lng','0')==2){echo 'style="display:none;"';} ?> >
												<?php if($par->get('show_tooltips_newad','0')){ ?>
													<label class="label Tips1" title="<?php echo JTEXT::_('COM_DJCLASSIFIEDS_LATITUDE_TOOLTIP')?>">
														<?php echo JText::_('COM_DJCLASSIFIEDS_LATITUDE');?>
														<img src="<?php echo JURI::base(true) ?>/components/com_djclassifieds/assets/images/tip.png" alt="?" />
													</label>
												<?php }else{ ?>
													<label class="label">
														<?php echo JText::_('COM_DJCLASSIFIEDS_LATITUDE'); ?>
													</label>
												<?php } ?>
												<div class="djform_field">
													<input class="text_area" type="text" name="latitude" id="latitude" size="50" maxlength="250" value="<?php echo $this->item->latitude; ?>" />
												</div>
												<div class="clear_both"></div>
											</div>
											<div class="djform_row" <?php if($par->get('allow_user_lat_lng','0')==2){echo 'style="display:none;"';} ?> >
												<?php if($par->get('show_tooltips_newad','0')){ ?>
													<label class="label Tips1" title="<?php echo JTEXT::_('COM_DJCLASSIFIEDS_LONGITUDE_TOOLTIP')?>">
														<?php echo JText::_('COM_DJCLASSIFIEDS_LONGITUDE');?>
														<img src="<?php echo JURI::base(true) ?>/components/com_djclassifieds/assets/images/tip.png" alt="?" />
													</label>
												<?php }else{ ?>
													<label class="label">
														<?php echo JText::_('COM_DJCLASSIFIEDS_LONGITUDE'); ?>
													</label>
												<?php } ?>
												<div class="djform_field">
													<input class="text_area" type="text" name="longitude" id="longitude" size="50" maxlength="250" value="<?php echo $this->item->longitude; ?>" />
												</div>
												<div class="clear_both"></div>
											</div>
											<div class="djform_row">
												<div class="djmap_intro">
													<div class="djmap_intro_desc"><?php echo JText::_('COM_DJCLASSIFIEDS_ADDITEM_SELECT_ON_MAP'); ?></div>
													<span class="button" type="button" id="map_use_my_location"><?php echo JText::_('COM_DJCLASSIFIEDS_USE_MY_LOCATION')?></span>
													<?php if($par->get('show_address','1')){?>
														<span class="button" type="button" id="map_update_latlng"><?php echo JText::_('COM_DJCLASSIFIEDS_UPDATE_USING_ADDRESS')?></span>
													<?php } ?>
													<div class="clear_both"></div>
													<div id="mapalert"><?php echo JText::_('COM_DJCLASSIFIEDS_WE_CANT_FIND_COORDS_ON_ADDRESS');?></div>
												</div>
												<div id="djmap" style="width:100%;height:300px;"></div>
											</div>
											<?php
										}
										$exp_days_list = $par->get('exp_days_list','');
										$exp_days = $par->get('exp_days','');
										if($par->get('durations_list','') && $id==0){
											//print_r($this->days);die();
											?>
											<div class="djform_row">
												<?php if($par->get('show_tooltips_newad','0')){ ?>
													<label class="label Tips1" for="exp_days" id="exp_days-lbl" title="<?php echo JTEXT::_('COM_DJCLASSIFIEDS_EXPIRE_AFTER_TOOLTIP')?>">
														<?php echo JText::_('COM_DJCLASSIFIEDS_EXPIRE_AFTER');?>
														<img src="<?php echo JURI::base(true) ?>/components/com_djclassifieds/assets/images/tip.png" alt="?" />
													</label>
												<?php }else{ ?>
													<label class="label" for="exp_days" id="exp_days-lbl" >
														<?php echo JText::_('COM_DJCLASSIFIEDS_EXPIRE_AFTER'); ?>
													</label>
												<?php } ?>
												<div class="djform_field">
													<div id="exp_days_box">
														<select id="exp_days" name="exp_days">
															<?php
																foreach($this->days as $day){
																	echo '<option value="'.$day->days.'"';
																	if($day->days==$exp_days){
																		echo ' SELECTED ';
																	}
																	echo '>';
																	if($day->days==0){
																		echo JText::_('COM_DJCLASSIFIEDS_UNLIMITED');
																	}else if($day->days==1){
																		echo $day->days.'&nbsp;'.JText::_('COM_DJCLASSIFIEDS_DAY');
																	}else{
																		echo $day->days.'&nbsp;'.JText::_('COM_DJCLASSIFIEDS_DAYS');
																	}

																	if($day->price !='0.00' && $points_a!=2){
																		//echo '&nbsp;-&nbsp;'.$day->price.'&nbsp;'.$par->get('unit_price');
																		echo '&nbsp;-&nbsp;'.DJClassifiedsTheme::priceFormat($day->price,$par->get('unit_price'));
																	}
																	if($day->points>0 && $points_a){
																		echo '&nbsp;-&nbsp;'.$day->points.'&nbsp;'.JTEXT::_('COM_DJCLASSIFIEDS_POINTS_SHORT');
																	}
																	if($day->price_special>0){
																		echo '&nbsp;-&nbsp;'.DJClassifiedsTheme::priceFormat($day->price_special,$par->get('unit_price')).' '.JTEXT::_('COM_DJCLASSIFIEDS_SPECIAL_PRICE_SHORT');
																	}
																	echo '</option>';
																}
															?>
														</select>
													</div>
												</div>
												<div class="clear_both"></div>
											</div>
										<?php }else if($id>0){
											echo '<input type="hidden" id="exp_days" value="'.$this->item->exp_days.'" />';
										} ?>
									<?php if($par->get('show_introdesc','1')){?>
										<div class="djform_row">
											<?php if($par->get('show_tooltips_newad','0')){ ?>
												<label class="label Tips1" for="intro_desc" id="intro_desc-lbl" title="<?php echo JTEXT::_('COM_DJCLASSIFIEDS_INTRO_DESCRIPTION_TOOLTIP')?>">
													<?php echo JText::_('COM_DJCLASSIFIEDS_INTRO_DESCRIPTION');?> *
													<span id="introdesc_limit">(<?php echo $par->get('introdesc_char_limit');?>)</span>
													<img src="<?php echo JURI::base(true) ?>/components/com_djclassifieds/assets/images/tip.png" alt="?" />
												</label>
											<?php }else{ ?>
												<label class="label" for="intro_desc" id="intro_desc-lbl">
													<?php echo JText::_('COM_DJCLASSIFIEDS_INTRO_DESCRIPTION');?> *
													<span id="introdesc_limit">(<?php echo $par->get('introdesc_char_limit');?>)</span>
												</label>
											<?php } ?>
											<div class="djform_field">
												<textarea id="intro_desc" name="intro_desc" rows="5" cols="55" class="inputbox required" onkeyup="introdescLimit(<?php echo $par->get('introdesc_char_limit');?>);" ><?php echo $this->item->intro_desc; ?></textarea>
											</div>
											<div class="clear_both"></div>
										</div>
									<?php } ?>
									<div class="djform_row">
										<?php if($par->get('show_tooltips_newad','0')){ ?>
											<label class="label Tips1" title="<?php echo JTEXT::_('COM_DJCLASSIFIEDS_DESCRIPTION_TOOLTIP')?>">
												<?php echo JText::_('COM_DJCLASSIFIEDS_DESCRIPTION');?>
												<img src="<?php echo JURI::base(true) ?>/components/com_djclassifieds/assets/images/tip.png" alt="?" />
											</label>
										<?php }else{ ?>
											<label class="label" >
												<?php echo JText::_('COM_DJCLASSIFIEDS_DESCRIPTION'); ?>
											</label>
										<?php } ?>
										<div class="djform_field">
											<?php
												if($par->get('pay_desc_chars','0')){
													$pay_desc_chars_limit = $par->get('pay_desc_chars_limit',0);
													if($pay_desc_chars_limit>0){ ?>
														<div class="desc_info_row"><?php echo JText::_('COM_DJCLASSIFIEDS_CHARS_LIMIT'); ?> : <span ><?php echo $pay_desc_chars_limit ;?></span></div>
													<?php } ?>
													<div class="desc_info_row"><?php echo JText::_('COM_DJCLASSIFIEDS_FREE_CHARS_LIMIT'); ?> : <span ><?php echo $par->get('pay_desc_chars_free_limit')+$this->item->extra_chars;?></span></div>
													<?php
													$char_price	= $par->get('desc_char_price','0');
													$char_price_points	= $par->get('desc_char_price_points','0');
													$char_price_special	= 0;
													if(isset($this->special_prices['desc_char_price'])){
														$char_price_special	= $this->special_prices['desc_char_price'];
													}

													if($id==0 && $par->get('durations_list','')){
														$exp_days_def = $par->get('exp_days','7');
														if(isset($this->days[$exp_days_def])){
															if($this->days[$exp_days_def]->char_price_default==0){
																$char_price = $this->days[$exp_days_def]->char_price;
																$char_price_points = $this->days[$exp_days_def]->char_points;
															}
														}
													}else if(isset($this->days[$this->item->exp_days])){
														if($this->days[$this->item->exp_days]->char_price_default==0){
															$char_price = $this->days[$this->item->exp_days]->char_price;
															$char_price_points = $this->days[$this->item->exp_days]->char_points;
														}
													}

													echo '<div class="desc_info_row">'.JText::_('COM_DJCLASSIFIEDS_PRICE_FOR_ADDITIONAL_CHAR').': <span  id="extra_char_price" >';
													if($points_a!=2){
														echo DJClassifiedsTheme::priceFormat($char_price,$par->get('unit_price'));
													}
													if($points_a && $char_price_points>0){
														if($points_a!=2){
															echo ' - ';
														}
														echo $char_price_points.JTEXT::_('COM_DJCLASSIFIEDS_POINTS_SHORT');
													}
													if($char_price_special>0){
														echo '&nbsp;-&nbsp;'.DJClassifiedsTheme::priceFormat($char_price_special,$par->get('unit_price')).' '.JTEXT::_('COM_DJCLASSIFIEDS_SPECIAL_PRICE_SHORT');
													}
													echo '</span></div> <br />';
													?>
													<div class="desc_info_row"><?php echo JText::_('COM_DJCLASSIFIEDS_CHARS_USED'); ?> : <span id="desc_chars"><?php echo strlen($this->item->description);?></span></div>
													<div class="desc_info_row"><?php echo JText::_('COM_DJCLASSIFIEDS_PRICE_FOR_ADDITIONAL_CHARS'); ?> : <span id="desc_price">0</span></div>
													<textarea id="djdesc" name="description" rows="5" cols="55" class="inputbox" onkeyup="descCharsLimit();" ><?php echo $this->item->description; ?></textarea>
													<?php
												}else if($par->get('desc_editor','1')){
													if($par->get('desc_editor','1')==2){
														$editor = JFactory::getEditor();
													}else{
														$editor = JFactory::getEditor('tinymce');
													}

													$p = array('mode'=>'0');
													echo $editor->display("description",$this->item->description,'100%','250','5','55', false, '', '', '',$p);
												}else{
													$allowed_tags = explode(';', $par->get('allowed_htmltags',''));
													$a_tags = '';
													for($a = 0;$a<count($allowed_tags);$a++){
														$a_tags .= '<'.$allowed_tags[$a].'>';
													}
													$this->item->description = strip_tags($this->item->description,$a_tags); ?>
													<?php $this->item->description = str_ireplace(array('<br />','<br>','<br/>'), '', $this->item->description); ?>
													<textarea id="description" name="description" rows="5" cols="55" class="inputbox" ><?php echo $this->item->description; ?></textarea>
												<?php }
											?>
										</div>
										<div class="clear_both"></div>
									</div>
									<?php if($par->get('seo_metadesc_user_edit','0')){?>
										<div class="djform_row">
											<?php if($par->get('show_tooltips_newad','0')){ ?>
												<label class="label Tips1" for="meta_desc" id="meta_desc-lbl" title="<?php echo JTEXT::_('COM_DJCLASSIFIEDS_METADESCRIPTION_TOOLTIP')?>">
													<?php echo JText::_('COM_DJCLASSIFIEDS_METADESCRIPTION');?>
													<span id="metadesc_limit">(<?php echo $par->get('seo_metadesc_char_limit','160');?>)</span>
													<img src="<?php echo JURI::base(true) ?>/components/com_djclassifieds/assets/images/tip.png" alt="?" />
												</label>
											<?php }else{ ?>
												<label class="label" for="meta_desc" id="meta_desc-lbl">
													<?php echo JText::_('COM_DJCLASSIFIEDS_METADESCRIPTION');?>
													<span id="metadesc_limit">(<?php echo $par->get('seo_metadesc_char_limit');?>)</span>
												</label>
											<?php } ?>
											<div class="djform_field">
												<textarea id="meta_desc" name="metadesc" rows="5" cols="55" class="inputbox" onkeyup="metadescLimit(<?php echo $par->get('seo_metadesc_char_limit');?>);" ><?php echo $this->item->metadesc; ?></textarea>
											</div>
											<div class="clear_both"></div>
										</div>
									<?php } ?>
									<?php if($par->get('seo_keywords_user_edit','0')){?>
										<div class="djform_row">
											<?php if($par->get('show_tooltips_newad','0')){ ?>
												<label class="label Tips1" for="keywords" id="keywords-lbl" title="<?php echo JTEXT::_('COM_DJCLASSIFIEDS_KEYWORDS_TOOLTIP')?>">
													<?php echo JText::_('COM_DJCLASSIFIEDS_KEYWORDS');?>
													<img src="<?php echo JURI::base(true) ?>/components/com_djclassifieds/assets/images/tip.png" alt="?" />
												</label>
											<?php }else{ ?>
												<label class="label" for="keywords" id="keywords-lbl">
													<?php echo JText::_('COM_DJCLASSIFIEDS_KEYWORDS');?>
												</label>
											<?php } ?>
											<div class="djform_field">
												<textarea id="keywords" name="metakey" rows="5" cols="55" class="inputbox" ><?php echo $this->item->metakey; ?></textarea>
											</div>
											<div class="clear_both"></div>
										</div>
									<?php } ?>
									<?php if($par->get('show_contact','1')==1){?>
										<div class="djform_row">
											<?php if($par->get('show_tooltips_newad','0')){ ?>
												<label class="label Tips1" for="contact" id="contact-lbl" title="<?php echo JTEXT::_('COM_DJCLASSIFIEDS_CONTACT_TOOLTIP')?>">
													<?php echo JText::_('COM_DJCLASSIFIEDS_CONTACT');?> *
													<img src="<?php echo JURI::base(true) ?>/components/com_djclassifieds/assets/images/tip.png" alt="?" />
												</label>
											<?php }else{ ?>
												<label class="label" for="contact" id="contact-lbl" >
													<?php echo JText::_('COM_DJCLASSIFIEDS_CONTACT'); ?> *
												</label>
											<?php } ?>
											<div class="djform_field">
												<?php $this->item->contact = str_ireplace(array('<br />','<br>','<br/>'), '', $this->item->contact); ?>
												<textarea id="contact" name="contact" rows="4" cols="55" class="inputbox required"><?php echo $this->item->contact; ?></textarea>
											</div>
											<div class="clear_both"></div>
										</div>
										<?php
									}
										echo  $this->loadTemplate('contactfields');
										if($par->get('email_for_guest','0') && !$user->id && !$this->item->id){ ?>
											<div class="djform_row">
												<?php if($par->get('show_tooltips_newad','0')){ ?>
													<label for="guest_email" id="guest_email-lbl" class="label Tips1" title="<?php echo JTEXT::_('COM_DJCLASSIFIEDS_EMAIL_GUEST_TOOLTIP')?>">
														<?php echo JText::_('COM_DJCLASSIFIEDS_EMAIL');?> <?php if($par->get('email_for_guest','0')==2){echo '*';}?>
														<img src="<?php echo JURI::base(true) ?>/components/com_djclassifieds/assets/images/tip.png" alt="?" />
													</label>
												<?php }else{ ?>
													<label for="guest_email" id="guest_email-lbl" class="label">
														<?php echo JText::_('COM_DJCLASSIFIEDS_EMAIL'); ?> <?php if($par->get('email_for_guest','0')==2){echo '*';}?>
													</label>
												<?php } ?>
												<div class="djform_field">
													<input class="text_area validate-djemail <?php if($par->get('email_for_guest','0')==2){echo ' required';}?>" onchange="checkDJEmail(this.value);" type="text" name="email" id="guest_email" size="50" maxlength="250" value="" />
													<span id="guest_email_loader"><img src="<?php echo JURI::base(true) ?>/components/com_djclassifieds/assets/images/newad_loader.gif" alt="..." /></span>
													<div id="guest_email_info"></div>
												</div>
												<div class="clear_both"></div>
											</div>
											<?php
										}
										if($par->get('show_website','0')){?>
											<div class="djform_row">
												<?php if($par->get('show_tooltips_newad','0')){ ?>
													<label class="label Tips1" title="<?php echo JTEXT::_('COM_DJCLASSIFIEDS_WEBSITE_TOOLTIP')?>">
														<?php echo JText::_('COM_DJCLASSIFIEDS_WEBSITE');?>
														<img src="<?php echo JURI::base(true) ?>/components/com_djclassifieds/assets/images/tip.png" alt="?" />
													</label>
												<?php }else{ ?>
													<label class="label">
														<?php echo JText::_('COM_DJCLASSIFIEDS_WEBSITE'); ?>
													</label>
												<?php } ?>
												<div class="djform_field">
													<input class="text_area" type="text" name="website" id="website" size="50" maxlength="250" value="<?php echo $this->item->website; ?>" />
												</div>
												<div class="clear_both"></div>
											</div>
											<?php
										}
										if($par->get('show_video','0')){?>
											<div class="djform_row">
												<?php if($par->get('show_tooltips_newad','0')){ ?>
													<label class="label Tips1" title="<?php echo JTEXT::_('COM_DJCLASSIFIEDS_VIDEO_TOOLTIP')?>">
														<?php echo JText::_('COM_DJCLASSIFIEDS_VIDEO');?>
														<img src="<?php echo JURI::base(true) ?>/components/com_djclassifieds/assets/images/tip.png" alt="?" />
														<br /><span><?php echo JText::_('COM_DJCLASSIFIEDS_LINK_TO_YOUTUBE_OR_VIMEO');?></span>
													</label>
												<?php }else{ ?>
													<label class="label">
														<?php echo JText::_('COM_DJCLASSIFIEDS_VIDEO'); ?>
														<br /><span><?php echo JText::_('COM_DJCLASSIFIEDS_LINK_TO_YOUTUBE_OR_VIMEO');?></span>
													</label>
												<?php } ?>
												<div class="djform_field">
													<input class="text_area" type="text" name="video" id="video" size="50" maxlength="250" value="<?php echo $this->item->video; ?>" />
												</div>
												<div class="clear_both"></div>
											</div>
											<?php
										}
										if($par->get('buynow','0')==1){
											echo  $this->loadTemplate('buynow');
										}
										if($par->get('show_price','1')==1){

											$price_lbl_class='';
											$price_lbl = JText::_('COM_DJCLASSIFIEDS_PRICE');
											$price_lbl_tooltip = JTEXT::_('COM_DJCLASSIFIEDS_PRICE_TOOLTIP');
											if($this->item->buynow == 1){
												$price_lbl_class=' label-buynow';
												$price_lbl = JText::_('COM_DJCLASSIFIEDS_PRICE_BUYNOW');
											}else if($this->item->auction == 1){
												$price_lbl_class=' label-auction';
												$price_lbl = JText::_('COM_DJCLASSIFIEDS_START_PRICE');
											}
											?>
											<div class="djform_row">
												<?php if($par->get('show_tooltips_newad','0')){ ?>
													<label class="label Tips1<?php echo $price_lbl_class;?>" id="price-lbl" for="price" title="<?php echo $price_lbl_tooltip; ?>">
														<?php echo $price_lbl;?>
														<img src="<?php echo JURI::base(true) ?>/components/com_djclassifieds/assets/images/tip.png" alt="?" />
													</label>
												<?php }else{ ?>
													<label class="label<?php echo $price_lbl_class;?>" id="price-lbl" for="price">
														<?php echo $price_lbl ?>
													</label>
												<?php } ?>
												<div class="djform_field">
													<?php if ($par->get('unit_price_position','0')== 0) {	?>
														<input class="text_area<?php if($par->get('price_only_numbers','0')){echo ' validate-numeric';}?>" type="text" name="price" id="price" size="30" maxlength="250" value="<?php echo $this->item->price; ?>" />
													<?php }

														if($par->get('unit_price_list','')){
															$c_list = explode(';', $par->get('unit_price_list',''));
															echo '<select name="currency" class="price_currency">';
															for($cl=0;$cl<count($c_list);$cl++){
																if($c_list[$cl]==$this->item->currency){
																	$csel=' SELECTED ';
																}else{
																	$csel='';
																}
																echo '<option '.$csel.' name="'.$c_list[$cl].' ">'.$c_list[$cl].'</option>';
															}
															echo '</select>';

														}else{
															echo $par->get('unit_price','EUR');
															echo '<input type="hidden" name="currency" value="" >';
														}
														if ($par->get('unit_price_position','0')== 1) { ?>
															<input class="text_area<?php if($par->get('price_only_numbers','0')){echo 'validate-numeric';}?>" type="text" name="price" id="price" size="30" maxlength="250" value="<?php echo $this->item->price; ?>" />
														<?php }

														if($par->get('show_price_negotiable','0')){ ?>
															<div class="price_neg_box">
																<input type="checkbox" autocomplete="off" name="price_negotiable" value="1" <?php if($this->item->price_negotiable){ echo 'checked="CHECKED"';}?> />
																<span><?php echo JText::_('COM_DJCLASSIFIEDS_PRICE_NEGOTIABLE')?></span>
															</div>
														<?php  }else{ ?>
															<input type="hidden" name="price_negotiable" value="0" />
														<?php } ?>
												</div>
												<div class="clear_both"></div>
											</div>
											<?php
										}
										if($par->get('auctions','0')==1){
											echo  $this->loadTemplate('auctions');
										}

										if(count($this->plugin_rows)){
											foreach($this->plugin_rows as $plugin_row){
												echo $plugin_row;
											}
										}
									?>
								</div>
							</div>
							<?php
								if($imglimit>0){
									echo  $this->loadTemplate('images');
								}
								if($par->get('promotion','1')=='1' && count($this->promotions)>0){
									echo  $this->loadTemplate('promotions');
								}

								if(count($this->plugin_sections)){
									foreach($this->plugin_sections as $plugin_section){
										echo $plugin_section;
									}
								}
							?>
							<?php if($par->get('terms',1)>0 && $par->get('terms_article_id',0)>0 && $this->terms_link && JRequest::getVar('id', 0, '', 'int' )==0 && !$token){ ?>
								<div class="djform_row terms_and_conditions">
									<label class="label" >&nbsp;</label>
									<div class="djform_field checkboxes">
										<input type="checkbox" name="terms_and_conditions" id="terms_and_conditions0" value="1" class="inputbox required" />
										<?php
											echo ' <label class="label_terms" for="terms_and_conditions0" id="terms_and_conditions-lbl" >'.JText::_('COM_DJCLASSIFIEDS_I_AGREE_TO_THE').' ';
											if($par->get('terms',0)==1){
												echo '<a href="'.$this->terms_link.'" target="_blank">'.JText::_('COM_DJCLASSIFIEDS_TERMS_AND_CONDITIONS').'</a>';
											}else if($par->get('terms',0)==2){
												echo '<a href="'.$this->terms_link.'" rel="{size: {x: 700, y: 500}, handler:\'iframe\'}" class="modal" target="_blank">'.JText::_('COM_DJCLASSIFIEDS_TERMS_AND_CONDITIONS').'</a>';
											}
											echo ' *</label>';
										?>
									</div>
									<div class="clear_both"></div>
								</div>
							<?php } ?>

							<?php if($par->get('privacy_policy',0)>0 && $par->get('privacy_policy_article_id',0)>0 && $this->privacy_policy_link && JRequest::getVar('id', 0, '', 'int' )==0 && !$token && $user->id==0){ ?>
								<div class="djform_row terms_and_conditions privacy_policy">
									<label class="label" >&nbsp;</label>
									<div class="djform_field checkboxes">
										<input type="checkbox" name="privacy_policy" id="privacy_policy0" value="1" class="inputbox required" />
										<?php
											echo ' <label class="label_terms" for="privacy_policy0" id="privacy_policy-lbl" >'.JText::_('COM_DJCLASSIFIEDS_I_AGREE_TO_THE').' ';
											if($par->get('privacy_policy',0)==1){
												echo '<a href="'.$this->privacy_policy_link.'" target="_blank">'.JText::_('COM_DJCLASSIFIEDS_PRIVACY_POLICY').'</a>';
											}else if($par->get('privacy_policy',0)==2){
												echo '<a href="'.$this->privacy_policy_link.'" rel="{size: {x: 700, y: 500}, handler:\'iframe\'}" class="modal" target="_blank">'.JText::_('COM_DJCLASSIFIEDS_PRIVACY_POLICY').'</a>';
											}
											echo ' *</label>';
										?>
									</div>
									<div class="clear_both"></div>
								</div>
							<?php } ?>

							<?php if($par->get('gdpr_agreement',1)>0 && JRequest::getVar('id', 0, '', 'int' )==0 && !$token && $user->id==0){ ?>
								<div class="djform_row terms_and_conditions gdpr_agreement">
									<label class="label" >&nbsp;</label>
									<div class="djform_field checkboxes">
										<input type="checkbox" name="gdpr_agreement" id="gdpr_agreement0" value="1" class="inputbox required" />
										<?php
											echo ' <label class="label_terms" for="gdpr_agreement0" id="gdpr_agreement-lbl" >';
											if($par->get('gdpr_agreement_info','')){
												echo $par->get('gdpr_agreement_info','');
											}else{
												echo JText::_('COM_DJCLASSIFIEDS_GDPR_AGREEMENT_LABEL');
											}
											echo ' *</label>';
										?>
									</div>
									<div class="clear_both"></div>
								</div>
							<?php } ?>
							<label id="verification_alert"  style="display:none;color:red;" >
								<?php echo JText::_('COM_DJCLASSIFIEDS_ENTER_ALL_REQUIRED_FIELDS'); ?>
							</label>
							<div id="verification_alert_system" ></div>
							<div class="classifieds_buttons">
								<?php if($user->id>0){
									$cancel_link = DJClassifiedsSEO::getUserAdsLink();
								}else{
									$cancel_link = DJClassifiedsSEO::getCategoryRoute('0:all');
								}
								?>
								<a class="button btn btn_accent-black" href="<?php echo $cancel_link;?>"><?php echo JText::_('COM_DJCLASSIFIEDS_CANCEL')?></a>
								<button class="button validate btn btn_primary" type="submit" id="submit_button"  ><?php echo JText::_('COM_DJCLASSIFIEDS_SAVE'); ?></button>
								<?php if($par->get('ad_preview','0') && $user->id>0){ ?>
									<button type="button" class="button"  id="preview_button"  ><?php echo JText::_('COM_DJCLASSIFIEDS_PREVIEW'); ?></button>
									<input type="hidden" name="preview_value" id="preview_value" value="0" />
								<?php } ?>
								<input type="hidden" name="option" value="com_djclassifieds" />

								<input type="hidden" name="id" value="<?php echo JRequest::getVar('id', 0, '', 'int' ); ?>" />
								<input type="hidden" name="token" value="<?php echo $token; ?>" />
								<input type="hidden" name="view" value="additem" />
								<input type="hidden" name="task" value="save" />
								<input type="hidden" name="boxchecked" value="0" />
							</div>
						</form>
					</div>
				</div>

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

	<script type="text/javascript">

		<?php if($par->get('show_introdesc','1')){?>
		function introdescLimit(limit){
			if(document.djForm.intro_desc.value.length<=limit){
				a=document.djForm.intro_desc.value.length;
				b=limit;
				c=b-a;
				document.getElementById('introdesc_limit').innerHTML= '('+c+')';
			}else{
				document.djForm.intro_desc.value = document.djForm.intro_desc.value.substring(0, limit);
			}
		}
		<?php } ?>

		<?php if($par->get('seo_metadesc_user_edit','0')){?>
		function metadescLimit(limit){
			if(document.djForm.meta_desc.value.length<=limit){
				a=document.djForm.meta_desc.value.length;
				b=limit;
				c=b-a;
				document.getElementById('metadesc_limit').innerHTML= '('+c+')';
			}else{
				document.djForm.meta_desc.value = document.djForm.meta_desc.value.substring(0, limit);
			}
		}
		<?php } ?>

		<?php if($title_char_limit>0){ ?>
		function titleLimit(limit){
			if(document.djForm.name.value.length<=limit){
				a=document.djForm.name.value.length;
				b=limit;
				c=b-a;
				document.getElementById('title_limit').innerHTML= '('+c+')';
			}else{
				document.djForm.name.value = document.djForm.name.value.substring(0, limit);
			}
		}
		<?php }?>

		var category_id = 0;

		function getFields(cat_id, m){
			var el = document.getElementById("ex_fields");
			var before = document.getElementById("ex_fields").innerHTML.trim();
			var main_cats = document.getElements('select[name=cats[]]');
			if(m){
				cat_id = main_cats.getLast().value;
			};

			category_id = cat_id;

			if(cat_id!=0){

				var mcat_count = document.getElements(".djform_mcat_row").length;
				var mcat_ids = '';
				if(mcat_count>0){
					var djmcats = document.getElements('.djform_mcat_row');
					if(djmcats){
						djmcats.each(function(djmcat,index){
							mcat_ids += djmcat.getElements('select').getLast().value + ',';
						});
					}
				}

				el.innerHTML = '<div style="text-align:center;margin-bottom:15px;"><img src="<?php echo JURI::base(); ?>components/com_djclassifieds/assets/images/loading.gif" /></div>';
				var url = '<?php echo JURI::base()?>index.php?option=com_djclassifieds&view=additem&task=getFields&cat_id=' + cat_id <?php if($this->item->id){echo "+ '&id='+".$this->item->id;} ?><?php if($this->item->token){echo "+ '&token=".$this->item->token."'";} ?>;
				var myRequest = new Request({
					url: '<?php echo JURI::base()?>index.php',
					method: 'post',
					data: {
						'option': 'com_djclassifieds',
						'view': 'additem',
						'task': 'getFields',
						'cat_id': cat_id,
						'mcat_ids': mcat_ids
						<?php if($this->item->id){echo ",'id':'".$this->item->id."'";} ?>
						<?php if($this->item->token){echo ",'token':'".$this->item->token."'";} ?>
						<?php if(JRequest::getInt('copy',0)>0){echo ",'id_copy':'".JRequest::getInt('copy',0)."'";} ?>
					},
					onSuccess: function(responseText){
						el.innerHTML = responseText;
						var JTooltips = new Tips($$('.Tips1'), {
							showDelay: 200, hideDelay: 200, className: 'djcf_label', fixed: false
						});

						var calendars = jQuery('#ex_fields').find('.field-calendar');
						if (calendars.length > 0) {
							calendars.each(function(){
								JoomlaCalendar.init(jQuery(this)[0]);
							});
						}
						document.formvalidator.attachToForm(document.id('djForm'));
					},
					onFailure: function(){
						console.error('getFields failure');
					}
				});
				myRequest.send();

				<?php if($par->get('buynow','0')==1){?>
				<?php if($this->item->id){ ?>
				getBuynowOptions();
				<?php } ?>
				document.getElementById("buynow_options1").innerHTML = '';
				document.getElementById("buynow_new_options1").setStyle('display','none');
				getBuynowFields(1);
				document.getElementById("buynow_options2").innerHTML = '';
				document.getElementById("buynow_new_options2").setStyle('display','none');
				document.getElementById("buynow_options2_info").setStyle('display','none');
				getBuynowFields(2);
				<?php }?>

			}else{
				el.innerHTML = '';
				//el.innerHTML='<?php echo JText::_('COM_DJCLASSIFIEDS_PLEASE_SELECT_CATEGORY');?>';
			}

		}


		function getBuynowOptions(){

			var bn_el = document.id("buynow_options");
			var cat_id = category_id;
			if(cat_id){
				//bn_el.innerHTML = '<img src="<?php echo JURI::base(); ?>components/com_djclassifieds/images/loading.gif" />';
				var url = 'index.php?option=com_djclassifieds&view=additem&task=getBuynowOptions&cat_id=' + cat_id <?php if($this->item->id){echo "+ '&id='+".$this->item->id;} ?>;
				var myRequest2 = new Request({
					url: 'index.php',
					method: 'post',
					evalResponse: false,
					data: {
						'option': 'com_djclassifieds',
						'view': 'additem',
						'task': 'getBuynowOptions',
						'cat_id': cat_id,
						<?php if($this->item->id){echo "'id':'".$this->item->id."'";} ?>
					},
					onSuccess: function(responseText){
						bn_el.innerHTML = responseText;
					},
					onFailure: function(){
						console.error('getBuynowOptions failure');
					}
				});
				myRequest2.send();
			}else{
				bn_el.innerHTML = '';
				//bn_el.innerHTML='<?php echo JText::_('COM_DJCLASSIFIEDS_PLEASE_SELECT_CATEGORY');?>';
			}
		}

		function getBuynowFields(type){

			var bn_el = document.id("buynow_options"+type);
			var cat_id = category_id;
			if(cat_id){
				//bn_el.innerHTML = '<img src="<?php echo JURI::base(); ?>components/com_djclassifieds/images/loading.gif" />';
				var url = 'index.php?option=com_djclassifieds&view=additem&task=getBuynowFields&cat_id=' + cat_id + '&type=' + type<?php if($this->item->id){echo "+ '&id='+".$this->item->id;} ?>;
				var myRequest2 = new Request({
					url: 'index.php',
					method: 'post',
					evalResponse: false,
					data: {
						'option': 'com_djclassifieds',
						'view': 'additem',
						'task': 'getBuynowFields',
						'cat_id': cat_id,
						'type': type,
						<?php if($this->item->id){echo "'id':'".$this->item->id."'";} ?>
					},
					onSuccess: function(responseText){
						if(responseText){
							var respElement = new Element('div');
							respElement.innerHTML = responseText;
							respElement.inject(bn_el,'bottom');
							document.getElementById("buynow_new_options"+type).setStyle('display','block');
							document.id('buynow_quantity_box').setStyle('display','none');
							if(type==2){
								document.getElementById("buynow_options2_info").setStyle('display','block');
							}
						}else{
							document.id('buynow_quantity_box').setStyle('display','block');
						}
					},
					onFailure: function(){
						console.error('getBuynowFields failure');
					}
				});
				myRequest2.send();
			}else{
				bn_el.innerHTML = '';
				//bn_el.innerHTML='<?php echo JText::_('COM_DJCLASSIFIEDS_PLEASE_SELECT_CATEGORY');?>';
				document.id('buynow_quantity_box').setStyle('display','block');
			}
		}

		function deleteBuynowField(row_id){
			document.getElementById(row_id).innerHTML = '';
		}

		function getCities(region_id){
			var el = document.getElementById("city");
			var before = document.getElementById("city").innerHTML.trim();

			if(region_id>0){
				el.innerHTML = '<img src="<?php echo JURI::base(); ?>components/com_djclassifieds/assets/images/loading.gif" />';
				var url = '<?php echo JURI::base()?>index.php?option=com_djclassifieds&view=additemtask=getCities&r_id=' + region_id <?php if($this->item->id){echo "+ '&id='+".$this->item->id;} ?>;
				var myRequest = new Request({
					url: '<?php echo JURI::base()?>index.php',
					method: 'post',
					data: {
						'option': 'com_djclassifieds',
						'view': 'additem',
						'task': 'getCities',
						'r_id': region_id
						<?php if($this->item->id){echo ",'id':'".$this->item->id."'";} ?>
					},
					onSuccess: function(responseText){
						el.innerHTML = responseText;
					},
					onFailure: function(){
						console.error('getCities failure');
					}
				});
				myRequest.send();
			}else{
				el.innerHTML='<?php echo JText::_('COM_DJCLASSIFIEDS_PLEASE_SELECT_REGION');?>';
			}

		}


		function checkDJEmail(value){
			document.id('guest_email_loader').setStyle('display','inline-block');
			var myRequest = new Request({
				url: '<?php echo JURI::base()?>index.php',
				method: 'post',
				data: {
					'option': 'com_djclassifieds',
					'view': 'additem',
					'task': 'checkEmail',
					'email': value
				},
				onSuccess: function(responseText){
					if(responseText){
						document.id('guest_email_info').innerHTML = responseText;
						document.id('guest_email_info').setStyle('display','block');
						document.id('guest_email').addClass('invalid');
						document.id('guest_email').addClass('djinvalid');
						document.id('guest_email-lbl').addClass('invalid');
						document.id('guest_email').set('aria-invalid','true');
						document.id('guest_email_loader').setStyle('display','none');
					}else{
						document.id('guest_email_info').innerHTML = '';
						document.id('guest_email_info').setStyle('display','none');
						document.id('guest_email_loader').setStyle('display','none');
						if(document.id('guest_email').hasClass('djinvalid')){
							document.id('guest_email').removeClass('invalid');
							document.id('guest_email').removeClass('djinvalid');
							document.id('guest_email-lbl').removeClass('invalid');
							document.id('guest_email').set('aria-invalid','false');
						}

					}
				},
				onFailure: function(){
				}
			});
			myRequest.send();
		}


		<?php if($par->get('allow_user_lat_lng','0')){
		if(($id || $token) && $this->item->latitude && $this->item->longitude){
			$lat = $this->item->latitude;
			$lon = $this->item->longitude;
		}else if(isset($_COOKIE["djcf_latlon"])) {
			$lat_lon = explode('_', $_COOKIE["djcf_latlon"]);
			$lat = $lat_lon[0];
			$lon = $lat_lon[1];
		}else{
			$loc_coord = DJClassifiedsGeocode::getLocation($par->get('map_lat_lng_address','England, London'));
			if(is_array($loc_coord)){
				$lat = $loc_coord['lat'];
				$lon = $loc_coord['lng'];
			}else{
				$lat = '';
				$lon = '';
			}
		}
		$gm_scrollwheel = ($par->get('gm_scrollwheel','1')? 'true' : 'false');
		?>
		if(typeof google !== 'undefined' && typeof google.maps !== 'undefined'){
			var map         = null;
			var marker      = null;
			var start_lat   = '<?php echo $lat; ?>';
			var start_lon   = '<?php echo $lon; ?>';
			var start_zoom  = <?php echo $par->get('gm_zoom','10'); ?>;
			var my_lat = start_lat;
			var my_lng = start_lon;
			var geokoder = new google.maps.Geocoder();
			var scrollw = <?php echo $gm_scrollwheel; ?>;
		}

		function initDjMap() {
			//var zoom    = 13;
			var coord   = new google.maps.LatLng(start_lat, start_lon);

			var mapoptions = {
				zoom: start_zoom,
				scrollwheel: scrollw,
				center: coord,
				mapTypeControl: true,
				navigationControl: true,
				zoomControl: true,
				mapTypeId: google.maps.MapTypeId.ROADMAP,
				styles: <?php echo $map_styles; ?>
			}

			// create the map
			map = new google.maps.Map(document.getElementById('djmap'),mapoptions);

			marker  = new google.maps.Marker({
				position: coord,
				draggable: true,
				visible: true,
				clickable: false,
				map: map
			});

			google.maps.event.addListener(marker, 'dragend', function(event) {
				latlng  = marker.getPosition();
				my_lat     = latlng.lat();
				my_lng     = latlng.lng();
				document.getElementById('latitude').value   = my_lat;
				document.getElementById('longitude').value  = my_lng;
			});

			google.maps.event.trigger(map, 'resize');
			map.setCenter(new google.maps.LatLng(my_lat,my_lng));

			map.setZoom( map.getZoom() );

			document.id('latitude').addEvent('change', function(){
				my_lat = this.value;
				map.setCenter(new google.maps.LatLng(my_lat,my_lng));
				coord   = new google.maps.LatLng(my_lat, my_lng);
				marker.setPosition(coord);
			});
			document.id('longitude').addEvent('change', function(){
				my_lng = this.value;
				map.setCenter(new google.maps.LatLng(my_lat,my_lng));
				coord   = new google.maps.LatLng(my_lat, my_lng);
				marker.setPosition(coord);
			});
			document.id('map_use_my_location').addEvent('click', function(){
				if(navigator.geolocation){
					navigator.geolocation.getCurrentPosition(showDJPosition);
				}else{
					x.innerHTML="<?php echo JText::_('COM_DJCLASSIFIEDS_GEOLOCATION_IS_NOT_SUPPORTED_BY_THIS_BROWSER');?>";}
			});
			<?php if($par->get('show_address','1')){?>
			document.id('map_update_latlng').addEvent('click', function(){
				updateLatLngFromAddress();
			});
			<?php } ?>
			<?php if(!$id && $par->get('show_address','1')){ ?>
			document.id('address').addEvent('change', function(){
				updateLatLngFromAddress();
			});
			<?php } ?>
		}

		function showDJPosition(position){
			var exdate=new Date();
			exdate.setDate(exdate.getDate() + 1);
			var ll = position.coords.latitude+'_'+position.coords.longitude;
			document.cookie = "djcf_latlon=" + ll + "; expires=" + exdate.toUTCString();
			my_lat = position.coords.latitude;
			my_lng = position.coords.longitude;
			document.getElementById('latitude').value   = my_lat;
			document.getElementById('longitude').value  = my_lng;
			map.setCenter(new google.maps.LatLng(my_lat,my_lng));
			var coord   = new google.maps.LatLng(my_lat, my_lng);
			marker.setPosition(coord);
			geokoder.geocode({ 'latLng': coord }, function (results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					if(results){
						if(jQuery('#address').length && !jQuery('#address').val()){
							jQuery('#address').val(results[0].formatted_address);
						}
						results[0].address_components.forEach(function(item, index){
							if(item.types.indexOf('postal_code') != -1){
								if(jQuery('#post_code').length && !jQuery('#post_code').val()){
									jQuery('#post_code').val(item.short_name);
								}
							}
						});
					}
				}
			});
		}

		function updateLatLngFromAddress(){
			var address = '';
			var address_raw = document.id('address').value;
			$$(document.getElementsByName('regions[]')).each(function(el){
				if(el.value!=0){address = address+el.getSelected().get('text')+', ';}
			});
			address = address+document.id('address').value;
			if(document.id('post_code')){
				if(address+document.id('post_code').value){
					address += ', '+document.id('post_code').value;
				}
			}
			geokoder.geocode(
				{address: address},
				function (results, status){
					if(address_raw != document.id('address').value){ // if address changed from autocomplete
						return;
					}
					if(status == google.maps.GeocoderStatus.OK){
						my_lat = results[0].geometry.location.lat();
						my_lng = results[0].geometry.location.lng();
						document.getElementById('latitude').value   = my_lat;
						document.getElementById('longitude').value  = my_lng;
						map.setCenter(new google.maps.LatLng(my_lat,my_lng));
						var coord   = new google.maps.LatLng(my_lat, my_lng);
						marker.setPosition(coord);
					}else{
						document.id('mapalert').setStyle('display','block');
						(function() {
							document.id('mapalert').setStyle('display','none');
						}).delay(5000);
					}
				});
		}
		<?php } ?>


		<?php if($par->get('pay_desc_chars','0') && $par->get('durations_list','')){ ?>
		var char_label_prices = [];
		var char_prices = [];
		var char_points = [];
		<?php if($id==0){ ?>
		document.id('exp_days').addEvent('change', function(){
			document.id('extra_char_price').innerHTML = char_label_prices[document.id('exp_days').value];
			descCharsLimit();
		});
		<?php }

		foreach($this->days as $day){
			if($day->char_price_default==0){
				$char_price = '';
				if($points_a!=2){
					$char_price = DJClassifiedsTheme::priceFormat($day->char_price,$par->get('unit_price'));
				}
				if($points_a && $day->char_points>0){
					if($points_a!=2){
						$char_price .= ' - ';
					}
					$char_price .= $day->char_points.' '.JTEXT::_('COM_DJCLASSIFIEDS_POINTS_SHORT');
				}
				echo 'char_label_prices['.$day->days.']="'.addslashes($char_price).'"; ';
				echo 'char_prices['.$day->days.']="'.addslashes($day->char_price).'"; ';
				echo 'char_points['.$day->days.']="'.addslashes($day->char_points).'"; ';
			}else{
				$char_price = '';
				if($points_a!=2){
					$char_price = DJClassifiedsTheme::priceFormat($par->get('desc_char_price','0'),$par->get('unit_price'));
				}
				if($points_a && $par->get('desc_char_price_points','0')>0){
					if($points_a!=2){
						$char_price .= ' - ';
					}
					$char_price .= $par->get('desc_char_price_points','0').' '.JTEXT::_('COM_DJCLASSIFIEDS_POINTS_SHORT');
				}
				echo 'char_label_prices['.$day->days.']="'.addslashes($char_price).'"; ';
				echo 'char_prices['.$day->days.']="'.addslashes($par->get('desc_char_price','0')).'"; ';
				echo 'char_points['.$day->days.']="'.addslashes($par->get('desc_char_price_points','0')).'"; ';
			}
		}
		?>

		var desc_chars_limit = <?php echo $par->get('pay_desc_chars_limit',0); ?>;
		var desc_chars_free_limit = <?php echo $par->get('pay_desc_chars_free_limit') + $this->item->extra_chars; ?>;

		function descCharsLimit(){
			if(document.id("djdesc").value.length<=desc_chars_limit || desc_chars_limit==0){
				var chars_c=document.id("djdesc").value.length;
				document.id('desc_chars').innerHTML= chars_c;
				if(chars_c>=desc_chars_free_limit){
					var chars_to_pay = chars_c - desc_chars_free_limit;
					var new_desc_price = '';
					if(chars_to_pay>0){
						<?php if($points_a!=2){ ?>
						new_desc_price =  <?php echo "'".$par->get('unit_price')." '"; ?>+Math.round(char_prices[document.id('exp_days').value]*chars_to_pay*100)/100;
						<?php }	?>
						<?php if($points_a){ ?>
						<?php if($points_a!=2){ ?>
						new_desc_price = new_desc_price+" - ";
						<?php }	?>
						new_desc_price = new_desc_price+Math.round(char_points[document.id('exp_days').value]*chars_to_pay*100)/100+"<?php echo JTEXT::_('COM_DJCLASSIFIEDS_POINTS_SHORT');?>";
						<?php }	?>
					}
					document.id('desc_price').innerHTML = new_desc_price;
				}else{
					document.id('desc_price').innerHTML =0;
				}
			}else{
				document.id("djdesc").value = document.id("djdesc").value.substring(0, desc_chars_limit);
			}
		}

		<?php }else if($par->get('pay_desc_chars','0')){
		$char_price = DJClassifiedsTheme::priceFormat($par->get('desc_char_price','0'),$par->get('unit_price'));
		if($points_a && $par->get('desc_char_price_points','0')>0){
			$char_price .= ' - '.$par->get('desc_char_price_points','0').JTEXT::_('COM_DJCLASSIFIEDS_POINTS_SHORT');
		}
		echo 'char_prices="'.addslashes($par->get('desc_char_price','0')).'"; ';
		echo 'char_points="'.addslashes($par->get('desc_char_price_points','0')).'"; ';
		?>

		var desc_chars_limit = <?php echo $par->get('pay_desc_chars_limit',0); ?>;
		var desc_chars_free_limit = <?php echo $par->get('pay_desc_chars_free_limit') + $this->item->extra_chars; ?>;

		function descCharsLimit(){
			if(document.id("djdesc").value.length<=desc_chars_limit || desc_chars_limit==0){
				var chars_c=document.id("djdesc").value.length;
				document.id('desc_chars').innerHTML= chars_c;
				if(chars_c>=desc_chars_free_limit){
					var chars_to_pay = chars_c - desc_chars_free_limit;
					var new_desc_price = 0;
					if(chars_to_pay>0){
						new_desc_price =  <?php echo "'".$par->get('unit_price')." '"; ?>+Math.round(char_prices*chars_to_pay*100)/100;
						<?php if($points_a){ ?>
						new_desc_price = new_desc_price+" - "+Math.round(char_points*chars_to_pay*100)/100+"<?php echo JTEXT::_('COM_DJCLASSIFIEDS_POINTS_SHORT');?>";
						<?php }	?>
					}
					document.id('desc_price').innerHTML = new_desc_price;
				}else{
					document.id('desc_price').innerHTML =0;
				}
			}else{
				document.id("djdesc").value = document.id("djdesc").value.substring(0, desc_chars_limit);
			}
		}
		<?php } ?>

		function updateDuration(cat_id){
			var c_days = document.id('exp_days').value;
			var myRequest = new Request({
				url: '<?php echo JURI::base()?>index.php',
				method: 'post',
				data: {
					'option': 'com_djclassifieds',
					'view': 'additem',
					'task': 'getDurationSelect',
					'cat_id': cat_id,
					'c_days': c_days
				},
				onRequest: function(){},
				onSuccess: function(responseText){
					document.id('exp_days_box').innerHTML = responseText;
					document.id('exp_days').addEvent('change', function(){
						limitDurationsPromotions(); //update
					});
					limitDurationsPromotions();
				},
				onFailure: function(){}
			});
			myRequest.send();

		}



		window.addEvent("load", function(){
			<?php if($par->get('show_introdesc','1')){?>
			introdescLimit(<?php echo $par->get('introdesc_char_limit');?>);
			<?php } ?>
			<?php if($par->get('seo_metadesc_user_edit','0')){?>
			metadescLimit(<?php echo $par->get('seo_metadesc_char_limit','160');?>);
			<?php } ?>
			<?php if($title_char_limit>0){?>
			titleLimit(<?php echo $title_char_limit;?>);
			<?php } ?>
			setTimeout(function(){
				<?php if($this->item->cat_id!=''){ ?>
				getFields(<?php echo $this->item->cat_id; ?>,false);
				<?php }else{ ?>
				getFields(<?php echo $this->item->cat_id; ?>);
				<?php } ?>
			}, 100);
			<?php if($par->get('allow_user_lat_lng','0')){ ?>
			if(typeof google !== 'undefined' && typeof google.maps !== 'undefined') initDjMap();
			<?php } ?>
			<?php if($par->get('pay_desc_chars','0') && $par->get('durations_list','')){ ?>
			descCharsLimit();
			<?php } ?>


			<?php if($par->get('promotion','1')=='1' && count($this->promotions)>0 && $par->get('durations_list','') && !$this->item->id){ ?>
			document.id('exp_days').addEvent('change', function(){
				limitDurationsPromotions();
			});
			limitDurationsPromotions();
			<?php } ?>

			var calendars = jQuery('#ex_fields').find('.field-calendar');
			if (calendars.length > 0) {
				calendars.each(function(){
					JoomlaCalendar.init(jQuery(this)[0]);
				});
			}
		});

		window.addEvent('domready', function(){
			var JTooltips = new Tips($$('.Tips1'), {
				showDelay: 200, hideDelay: 200, className: 'djcf_label', fixed: true
			});
			var JTooltips = new Tips($$('.Tips2'), {
				showDelay: 200, hideDelay: 200, className: 'djcf_prom', fixed: false
			});

			<?php if($par->get('ad_preview','0')){ ?>
			var redirect_preview = 0;
			<?php } ?>

			document.id('submit_button').addEvent('click', function(){

				<?php if($par->get('ad_preview','0') && $user->id>0){ ?>
				if(redirect_preview==0){
					document.id('preview_value').set("value", 0);
				}
				redirect_preview=0;

				<?php } ?>

				var cat_list = 	document.id('after_cat_0').getElements('select.cat_sel.required');
				if(cat_list.length>0){
					cat_list.each(function(cat_l){
						var check_s = /^p/;
						if(check_s.test(cat_l.get('value'))){
							cat_l.addClass('invalid');
							cat_l.setAttribute("aria-invalid", "true");
							document.id('cat_0-lbl').addClass('invalid');
							document.id('cat_0-lbl').setAttribute("aria-invalid", "true");
							//console.log('invalid');
						}else{
							cat_l.removeClass('invalid');
							cat_l.setAttribute("aria-invalid", "false");
							document.id('cat_0-lbl').removeClass('invalid');
							document.id('cat_0-lbl').setAttribute("aria-invalid", "false");
							//console.log('ok');
						}
					})
				}

				var img_invalid = 0;
				<?php if($par->get('img_required','0')){ ?>
				if(document.getElements('#djForm #itemImages .itemImage').length==0){
					document.getElements('#djForm .images_box .title_top')[0].addClass('invalid');
					img_invalid = 1;
				}else{
					document.getElements('#djForm .images_box .title_top')[0].removeClass('invalid');
				}
				<?php } ?>

				if(document.id('system-message-container')){
					(function() {

						if(document.getElements('#djForm .invalid').length==1 && img_invalid){
							document.id('system-message-container').innerHTML = '<div class="alert alert-error"><p><?php echo addslashes(JText::_('COM_DJCLASSIFIEDS_INVALID_FIELD').': '.JText::_('COM_DJCLASSIFIEDS_IMAGES'));?></p></div>';
						}

						if(document.id('system-message-container').innerHTML){
							document.id('verification_alert_system').innerHTML = document.id('system-message-container').innerHTML;
							if(document.getElements('#djForm .invalid').length>1 && img_invalid){
								document.getElements('#verification_alert_system .alert.alert-error')['0'].appendText('<?php echo addslashes(JText::_('COM_DJCLASSIFIEDS_INVALID_FIELD').': '.JText::_('COM_DJCLASSIFIEDS_IMAGES'));?>');
							}
						}else{
							document.id('verification_alert_system').innerHTML = '';
						}
					}).delay(1000);
				}

				if(document.getElements('#djForm .invalid').length>0){
					document.id('verification_alert').setStyle('display','block');
					(function() {
						document.id('verification_alert').setStyle('display','none');
					}).delay(3000);
					return false;
				}else{
					return true;
				}
			});

			document.id('djForm').addEvent('submit', function(e){
				document.id('submit_button').disabled=true;
			});


			<?php if($par->get('ad_preview','0') && $user->id>0){ ?>
			document.id('preview_button').addEvent('click', function(){
				document.id('preview_value').set("value", 1);
				redirect_preview = 1;
				document.id('submit_button').click();
			});
			<?php } ?>

			<?php if($par->get('places_in_address','0')==1 && $par->get('show_address','1')){ ?>
			if(typeof google !== 'undefined' && typeof google.maps !== 'undefined') djcfAddressPlaces('<?php echo $par->get('places_api_country',''); ?>');
			<?php } ?>

			<?php if($par->get('price_only_numbers','0')==1){ ?>
			djcfPriceFormat();
			<?php } ?>

		});



		function djcfAddressPlaces(country_iso){
			var input = (document.getElementById('address'));
			var aut_options = '';
			if(country_iso){
				aut_options = {componentRestrictions: {country: country_iso}};
			}
			var autocomplete = new google.maps.places.Autocomplete(input,aut_options);
			var infowindow = new google.maps.InfoWindow();
			var last_place = '';

			autocomplete.addListener('place_changed', function(){
				var place = autocomplete.getPlace();
				var coord = new google.maps.LatLng(place.geometry.location.lat(), place.geometry.location.lng());
				map.setCenter(coord);
				marker.setPosition(coord);
				place.address_components.forEach(function(item, index){
					if(item.types.indexOf('postal_code') != -1){
						if(jQuery('#post_code').length && !jQuery('#post_code').val()){
							jQuery('#post_code').val(item.short_name);
						}
					}
				});
			});

			google.maps.event.addDomListener(input, 'keydown', function(e) {
				if (e.keyCode == 13){
					if (e.preventDefault){
						e.preventDefault();
					}else{
						e.cancelBubble = true;
						e.returnValue = false;
					}
				}
			});

		}


		function djcfPriceFormat(){
			if(document.id('price')){
				document.id('price').addEvent('keyup', function(){
					this.value = this.value.replace(',','');
				});
			}
		}

		function limitDurationsPromotions(){
			var c_days = document.id('exp_days').value;
			var prom_selects = document.getElements('.prom_rows.additem_djform .djform_prom_v select');
			if(prom_selects){
				prom_selects.each(function(prom_select,index){

					if(c_days<prom_select.value){
						prom_select.value = 0;
					}

					prom_select.getElements('option').each(function(option) {
						if(parseInt(c_days)<parseInt(option.get('value')) && parseInt(c_days)>0){
							//console.log(c_days+' '+option.get('value')+'disabled');
							option.disabled = true;
						}else{
							//console.log(c_days+' '+option.get('value')+'ok');
							option.disabled = false;
						}
					});

				});
			}
		}
	</script>