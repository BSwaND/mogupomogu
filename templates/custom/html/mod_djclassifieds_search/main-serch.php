<?php
	/**
	 * @version		2.0
	 * @package		DJ Classifieds
	 * @subpackage	DJ Classifieds Search Module
	 * @copyright	Copyright (C) 2010 DJ-Extensions.com LTD, All rights reserved.
	 * @license		http://www.gnu.org/licenses GNU/GPL
	 * @autor url    http://design-joomla.eu
	 * @autor email  contact@design-joomla.eu
	 * @Developer    Lukasz Ciastek - lukasz.ciastek@design-joomla.eu
	 *
	 *
	 *
	 */
	defined ('_JEXEC') or die('Restricted access');
	JHTML::_('behavior.calendar');
	$app 		= JFactory::getApplication();
	$config		= JFactory::getConfig();
	$document	= JFactory::getDocument();

	$cid=0;
	if($params->get('fallow_cat','1')==1 && JRequest::getVar('option') == 'com_djclassifieds'){
		$cid = JRequest::getInt('cid','0');
	}
	$layout_cl = '';
	if($params->get('search_layout',0)){
		$layout_cl = ' dj_cf_search_horizontal';
	}

	if($params->get('show_address','0')==1){
		DJClassifiedsTheme::includeMapsScript();
	}
	if($params->get('show_input_hints','0')==1 && $params->get('show_input','1')==1){
		$document->addScript(JURI::base(true).'/components/com_djclassifieds/assets/moocomplete/MooComplete.js');
		if($params->get('show_address','0')==0){
			$pac_styles = ".pac-container {background-color: #fff;border-radius: 2px;border-top: 1px solid #d9d9d9;box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);box-sizing: border-box;font-family: Arial,sans-serif;overflow: hidden;position: absolute !important;z-index: 1000;}.pac-item {border-top: 1px solid #e6e6e6;color: #999;cursor: default;font-size: 11px;line-height: 30px;overflow: hidden;padding: 0 4px;text-align: left;text-overflow: ellipsis;white-space: nowrap;}.pac-item:hover {background-color: #fafafa;}.pac-item-selected, .pac-item-selected:hover {background-color: #ebf2fe;}.pac-matched {font-weight: 700;}.pac-item-query {color: #000;font-size: 13px;padding-right: 3px;}.pac-placeholder {color: gray;}";
			$document->addStyleDeclaration($pac_styles);
		}
	}

	$link = DJClassifiedsSEO::getSearchResultsLink($params, $module);
	$link_reset = $link.'&reset=1';

	$radius_l = explode(',', $params->get('radius_list',''));
	$radius_list = array();
	$radius_unit = $params->get('radius_unit','km');
	foreach($radius_l as $radius){
		if($radius_unit=='mile'){
			$radius_label = $radius.' '.JText::_('COM_DJCLASSIFIEDS_SEARCH_MILES');
		}else{
			$radius_label = $radius.' '.JText::_('COM_DJCLASSIFIEDS_SEARCH_KM');
		}
		$radius_list[] = array('value'=>$radius,'text'=>$radius_label,'disabled'=>0);
	}

?>
	<div  id="mod_djcf_search<?php echo $module->id;?>" class="dj_cf_search<?php echo $layout_cl;?> form_search">
		<form action="<?php echo JRoute::_($link.'&se=1'); ?>" method="get" name="form-search<?php echo $module->id?>" id="form-search<?php echo $module->id?>" class="form_search">
			<input type="hidden" name="task" value="parsesearch" />
			<input type="hidden" name="mod_id" value="<?php echo $module->id?>" />
			<?php if($params->get('result_view','0')==1){ ?>
				<input type="hidden" name="layout" value="blog" />
			<?php } ?>
			<input type="hidden" name="option" value="com_djclassifieds" />
			<input type="hidden" name="view" value="items" />
			<input type="hidden" name="se" value="1" />
			<input type="hidden" name="Itemid" value="<?php echo DJClassifiedsSEO::getSearchResultsItemid($params, $module); ?>" />

			<?php if($params->get('show_input','1')==1){ ?>
				<div class="search_word djcf_se_row">
					<?php $s_value = htmlspecialchars(JRequest::getVar('search',''), ENT_COMPAT, 'UTF-8') ?>
					<input type="text" id="input_search<?php echo $module->id?>" size="12" name="search" class="inputbox first_input form_search__input" value="<?php echo $s_value; ?>" placeholder="Что Вы ищите?" />
				</div>
			<?php } ?>

			<?php if($params->get('show_geoloc','0')==1){ ?>
				<?php if($params->get('show_address','0')==1 || $params->get('show_postcode','0')==1){ ?>
					<span class="se_geoloc_or_label"><?php echo JText::_('MOD_DJCLASSIFIEDS_SEARCH_OR'); ?></span>
				<?php } ?>
				<span class="se_geoloc_icon button" id="se_geoloc_icon<?php echo $module->id;?>" title="<?php echo JText::_('MOD_DJCLASSIFIEDS_SEARCH_GEOLOC_TOOLTIP_INFO'); ?>" ></span>
				<input type="hidden" name="se_geoloc" id="se_geoloc<?php echo $module->id;?>" value="<?php echo JRequest::getVar('se_geoloc',''); ?>" />
				<?php if($user_address && $params->get('show_address','0')!=1){?>
					<div class="se_geoloc_address" >
						<?php echo $user_address; ?>
					</div>
				<?php } ?>
			<?php } ?>

			<input type="submit" value="" class="form_search__submit">
			<div class="form_search_dop-filter__control_outer">
				<div class="form_search_dop-filter__control__btn">Фильтр</div>
			</div>

			<div class="form_search_dop-filter">
				<div class="form_search_dop-filter__top-control">
					<div class="row">
						<div class="col-9">
							<label class="input_btn_outer">
								<input type="radio" name="se_cats[]" value="1" <?= (isset($_GET['se_cats']) && $_GET['se_cats'] == 1) ? 'checked': null ?> >
								<span class="input_btn">Помогу</span>
							</label>
						</div>
						<div class="col-9">
							<label class="input_btn_outer">
								<input type="radio" name="se_cats[]"  value="2" <?= (isset($_GET['se_cats']) && $_GET['se_cats'] == 2) ? 'checked': null ?>>
								<span class="input_btn">Нуждаюсь</span>
							</label>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-10">
						<?php	if($params->get('show_loc','1')==1){	?>
							<div class="search_regions djcf_se_row"> 
						<?php if($params->get('show_loc_label','0')==1){ ?>
							<label><?php echo JText::_('MOD_DJCLASSIFIEDS_SEARCH_LOZALIZATION_LABEL'); ?></label>
						<?php } ?>
						<?php
							if($params->get('loc_select_type',0)==1){
								$loc_address_country = '';
								if($params->get('show_address','0')==1){
									$loc_address_country = 'onchange="se'.$module->id.'_country_iso(this.value)"';
								}

								$reg_sel = '<select  class="inputbox" id="se'.$module->id.'_reg_0" name="se_regs[]"  '.$loc_address_country.' ><option value="0">'.JText::_('COM_DJCLASSIFIEDS_SELECT_LOCALIZATION').'</option>';

								foreach($regions as $reg){
									$r_name = str_ireplace("'", "&apos;", $reg->name);
									for($lev=0;$lev<$reg->level;$lev++){
										$r_name ="- ".$r_name;
									}
									$reg_sel .= '<option value="'.$reg->id.'">'.$r_name.'</option>';
								}
								$reg_sel .= '</select>';
								echo $reg_sel;

							}else{
//								echo '<pre>';
//								print_r($regions);
//								echo '</pre>';
								$reg_sel = '<select  class="inputbox" id="se'.$module->id.'_reg_0" name="se_regs[]" onchange="se'.$module->id.'_new_reg(0,this.value,new Array());"><option value="0">'.JText::_('COM_DJCLASSIFIEDS_SELECT_LOCALIZATION').'</option>';
								$parent_id=0;
								$lc=0;
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
							}

						?>

						<div id="se<?php echo $module->id;?>_after_reg_0"></div>
						<script type="text/javascript">
							var se<?php echo $module->id;?>_regs=new Array();

							<?php
							/*$reg_sel = '<select class="inputbox" name="se_regs[]" id="se'.$module->id.'_reg_0" onchange="se'.$module->id.'_new_reg(0,this.value);">';
							$parent_id=0;

							foreach($regions as $l){
								if($parent_id!=$l->parent_id){
									$reg_sel .= '</select>';
									echo "se".$module->id."_regs[$parent_id]='$reg_sel<div id=\"se".$module->id."_after_reg_$parent_id\"></div>';";
									$parent_id=$l->parent_id;
									$reg_sel = '<select class="inputbox" name="se_regs[]" id="se'.$module->id.'_reg_'.$l->parent_id.'" onchange="se'.$module->id.'_new_reg('.$parent_id.',this.value);">';
									$reg_sel .= '<option value="'.$parent_id.'">'.JTEXT::_('MOD_DJCLASSIFIEDS_SEARCH_LOCATION_SELECTOR_EMPTY_VALUE').'</option>';
								}
								$reg_sel .= '<option value="'.$l->id.'">'.str_ireplace("'", "&apos;", $l->name).'</option>';
							}
							$reg_sel .= '</select>';
							echo "se".$module->id."_regs[$parent_id]='$reg_sel<div id=\"se".$module->id."_after_reg_$parent_id\"></div>';";
							*/

							/*$se_url = '';
							foreach ($_GET as $k => $v) {
								if(strstr($k,'se_')){
									$se_url .= '&'.$k.'='.$v;
								}
							}*/
							?>
							var se<?php echo $module->id;?>_current=0;

							function se<?php echo $module->id;?>_new_reg(parent,a_parent,r_path){

								var myRequest = new Request({
									url: '<?php echo JURI::base()?>index.php',
									method: 'post',
									data: {
										'option': 'com_djclassifieds',
										'view': 'item',
										'task': 'getRegionSelect',
										'reg_id': a_parent,
										'mod_id': <?php echo $module->id;?>
									},
									onRequest: function(){
										document.id('se<?php echo $module->id;?>_after_reg_'+parent).innerHTML = '<div style="text-align:center;"><img src="<?php echo JURI::base(true) ?>/components/com_djclassifieds/assets/images/loading.gif" alt="..." /></div>';
									},
									onSuccess: function(responseText){
										if(responseText){
											document.id('se<?php echo $module->id;?>_after_reg_'+parent).innerHTML = responseText;
											document.id('se<?php echo $module->id;?>_reg_'+parent).value=a_parent;
										}else{
											document.id('se<?php echo $module->id;?>_after_reg_'+parent).innerHTML = '';
											document.id('se<?php echo $module->id;?>_reg_'+parent).value=a_parent;
										}
										//support for IE
										document.id('se<?php echo $module->id;?>_reg_'+parent).blur();
										if(r_path != 'null'){
											if(r_path.length>0){
												var first_path = r_path[0].split(',');
												r_path.shift();
												se<?php echo $module->id;?>_new_reg(first_path[0],first_path[1],r_path);
											}
										}
										<?php if($params->get('show_address','0')==1){ ?>
										se<?php echo $module->id;?>_country_iso(a_parent);
										<?php } ?>
									},
									onFailure: function(){}
								});
								myRequest.send();


								/*if(se<?php echo $module->id;?>_regs[a_parent]){
						//alert(cats[v]);
						if(parent==a_parent){
							document.id('se<?php echo $module->id;?>_after_reg_'+parent).innerHTML = '';
						}else{
							document.id('se<?php echo $module->id;?>_after_reg_'+parent).innerHTML = se<?php echo $module->id;?>_regs[a_parent];
						}
						document.id('se<?php echo $module->id;?>_reg_'+parent).value=a_parent;
					}else{
						document.id('se<?php echo $module->id;?>_after_reg_'+parent).innerHTML = '';
						document.id('se<?php echo $module->id;?>_reg_'+parent).value=a_parent;
					}*/

							}


							function se<?php echo $module->id;?>_country_iso(reg_id){
								if(typeof google === 'undefined') return;

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
											djcfmodSearchPlaces<?php echo $module->id;?>(responseText);
										}
									},
									onFailure: function(){}
								});
								myRequest.send();

							}

						</script>

					</div>
						<?php } ?>
						<?php /*
						<label>
							<p class="form_search_dop-filter__name-input">Место расположения</p>
							<input type="text" name="location">
						</label>
						<label>
							<p class="form_search_dop-filter__name-input">Страна</p>
							<input type="text" name="country">
						</label>
						<label>
							<p class="form_search_dop-filter__name-input">Город</p>
							<input type="text" name="sity">
						</label>
						<label>
							<p class="form_search_dop-filter__name-input">Область</p>
							<input type="text" name="region">
						</label>
						*/ ?>
					</div>
					<div class="col-md-8">
						<div class="form_search_dop-filter__last-col">
							<?php /* 
							<label class="label__radius">
								<span class="label__radius__marker"></span>
							<div class="search_radius_range">
								<input type="hidden" name="se_postcode_c"  value="<?php echo $params->get('postcode_country',''); ?>"  />
								<input type="hidden" name="se_radius_unit"  value="<?php echo $radius_unit; ?>"  />
								<select  name="se_radius" class="inputbox" >
									<option value=""><?php echo JText::_('COM_DJCLASSIFIEDS_SEARCH_SEARCH_RANGE');?></option>
									<?php echo JHtml::_('select.options', $radius_list, 'value', 'text', JRequest::getFloat('se_radius',$params->get('default_radius','50')), true);?>
								</select>
							</div>
							</label>
							*/ ?>

							<div class="form_search_dop-filter__control__footer">
								<input class="form_search__btn-footer" type="submit" value="Искать">
								<a href="<?php echo JRoute::_($link_reset);?>" class="form_search__btn-footer form_search__btn-footer__reset">Сбросить настройки</a>
							</div>
						</div>
					</div>
				</div>
			</div>

			<?php
				if($params->get('show_cat','1')==1 && count($categories)){
					$cat_outer_class = '';
					$cat_sel_class = '';
					$show_label_in_subcat = 0;
					if($params->get('cat_select_type','0')==0 && $params->get('cat_hide_1_level','0')==1){
						$cat_outer_class = ' cat_hide_1_lvl';
						$cat_sel_class = ' hide';
						$show_label_in_subcat = 1;
					}
					?>
					<div class="search_cats djcf_se_row<?php echo $cat_outer_class; ?>">
						<div id="se<?php echo $module->id;?>_after_cat_0" class="after_cat_lvl0"></div>
						<script type="text/javascript">
							var se<?php echo $module->id;?>_cats=new Array();


							var se_current=0;

							function se<?php echo $module->id;?>_new_cat(parent,a_parent,c_path){

								var myRequest = new Request({
									url: '<?php echo JURI::base()?>index.php',
									method: 'post',
									data: {
										'option': 'com_djclassifieds',
										'view': 'item',
										'task': 'getCategorySelect',
										'cat_id': a_parent,
										'mod_id': <?php echo $module->id;?>,
										'ord': '<?php echo $params->get('cat_ordering','ord'); ?>',
										'subcat_label': '<?php echo $show_label_in_subcat; ?>'
									},
									onRequest: function(){
										document.id('se<?php echo $module->id;?>_after_cat_'+parent).innerHTML = '<div style="text-align:center;"><img src="<?php echo JURI::base(true) ?>/components/com_djclassifieds/assets/images/loading.gif" alt="..." /></div>';
									},
									onSuccess: function(responseText){
										if(responseText){
											document.id('se<?php echo $module->id;?>_after_cat_'+parent).innerHTML = responseText;
											document.id('se<?php echo $module->id;?>_cat_'+parent).value=a_parent;
										}else{
											document.id('se<?php echo $module->id;?>_after_cat_'+parent).innerHTML = '';
											document.id('se<?php echo $module->id;?>_cat_'+parent).value=a_parent;
										}

										var mod_classes = document.id('mod_djcf_search<?php echo $module->id;?>').className.split(" ");
										mod_classes.each(function(mod_class,index){
											if(mod_class.lastIndexOf('cat_lvl')>-1){
												document.id('mod_djcf_search<?php echo $module->id;?>').removeClass(mod_class);
											}
										});

										var cat_level = document.getElements("#mod_djcf_search<?php echo $module->id;?> .search_cats select").length;
										if(cat_level>0){
											document.id('mod_djcf_search<?php echo $module->id;?>').addClass('cat_lvl'+cat_level);
										}


										if(c_path != 'null'){
											if(c_path.length>0){
												var first_path = c_path[0].split(',');
												c_path.shift();
												se<?php echo $module->id;?>_new_cat(first_path[0],first_path[1],c_path);
											}
										}
									},
									onFailure: function(){}
								});
								myRequest.send();



								/*if(se<?php echo $module->id;?>_cats[a_parent]){
							//alert(se_cats[v]);
							document.id('se<?php echo $module->id;?>_after_cat_'+parent).innerHTML = se<?php echo $module->id;?>_cats[a_parent];
							document.id('se<?php echo $module->id;?>_cat_'+parent).value=a_parent;
						}else{
							document.id('se<?php echo $module->id;?>_after_cat_'+parent).innerHTML = '';
							document.id('se<?php echo $module->id;?>_cat_'+parent).value=a_parent;
						}*/

							}

							function se<?php echo $module->id;?>_getFields(cat_id){

								<?php if($params->get('show_custom_fields','1')==0){
								echo 'return true';
							}?>

								var el = document.getElementById("search<?php echo $module->id;?>_ex_fields");
								var before = document.getElementById("search<?php echo $module->id;?>_ex_fields").innerHTML.trim();

								//if(cat_id!=0){
								el.innerHTML = '<div style="text-align:center"><img src="<?php echo JURI::base(); ?>components/com_djclassifieds/assets/images/loading.gif" /></div>';
								var url = '<?php echo JURI::base();?>index.php?option=com_djclassifieds&view=item&task=getSearchFields&cat_id=' + cat_id;
								var myRequest = new Request({
									<?php  $lang_code = explode('-', JRequest::getVar('lang','')); ?>
									url: '<?php echo JURI::base();?>index.php<?php if(JRequest::getVar('lang','')){echo '?lang='.$lang_code[0];}?>',
									method: 'post',
									data: {
										'option': 'com_djclassifieds',
										'view': 'item',
										'task': 'getSearchFields',
										'mod_id': '<?php echo $module->id;?>',
										'cat_id': cat_id,
										'se': '<?php echo JRequest::getInt('se', 0); ?>'
									},
									onSuccess: function(responseText){
										el.innerHTML = responseText;
										if(responseText){
											el.removeClass('no_fields');
										}else{
											el.addClass('no_fields');
										}

										var calendars = jQuery('#search<?php echo $module->id?>_ex_fields').find('.field-calendar');
										if (calendars.length > 0) {
											calendars.each(function(){
												JoomlaCalendar.init(jQuery(this)[0]);
											});
										}

										var djfields_accordion_o = document.getElements('#search<?php echo $module->id;?>_ex_fields .djfields_accordion_o');
										if(djfields_accordion_o){
											djfields_accordion_o.each(function(djfields_acc_o,index){
												new Fx.Accordion(djfields_acc_o.getElements('.label'),
													djfields_acc_o.getElements('.se_checkbox'), {
														alwaysHide : true,
														display : 0,
														duration : 100,
														onActive : function(toggler, element) {
															toggler.addClass('active');
															element.addClass('in');
														},
														onBackground : function(toggler, element) {
															toggler.removeClass('active');
															element.removeClass('in');
														}
													});
											})
										}

										var djfields_accordion_c = document.getElements('#search<?php echo $module->id;?>_ex_fields .djfields_accordion_c');
										if(djfields_accordion_c){
											djfields_accordion_c.each(function(djfields_acc_c,index){
												new Fx.Accordion(djfields_acc_c.getElements('.label'),
													djfields_acc_c.getElements('.se_checkbox'), {
														alwaysHide : true,
														display : -1,
														duration : 100,
														onActive : function(toggler, element) {
															toggler.addClass('active');
															element.addClass('in');
														},
														onBackground : function(toggler, element) {
															toggler.removeClass('active');
															element.removeClass('in');
														}
													});
											})
										}

									},
									onFailure: function(xhr){
										console.error(xhr);
									}
								});
								myRequest.send();
								/*}else{
									el.innerHTML = '';
									//el.innerHTML='<?php echo JText::_('COM_DJCLASSIFIEDS_PLEASE_SELECT_CATEGORY');?>';
					}*/
								return null;

							}
							<?php // echo $this->cat_path; ?>


						</script>
					</div>
					<div id="search<?php echo $module->id;?>_ex_fields" class="search_ex_fields no_fields"></div>
				<?php }?>
		</form>
	</div>

<?php
	$cat_id_se = 0;
	if($params->get('show_cat','1')==1){
		if(JRequest::getVar('se','0','','string')!='0' && isset($_GET['se_cats'])){
			if(is_array($_GET['se_cats'])){
				$cat_id_se= end($_GET['se_cats']);
				if($cat_id_se=='' && count($_GET['se_cats'])>2){
					$cat_id_se =$_GET['se_cats'][count($_GET['se_cats'])-2];
				}
			}else{
				$cat_ids_se = explode(',', JRequest::getVar('se_cats'));
				$cat_id_se = end($cat_ids_se);
			}
			$cat_id_se = str_ireplace('p', '', $cat_id_se);
			$cat_id_se = (int)$cat_id_se;
		}
		if($cat_id_se=='0'){
			$cat_id_se = $cid;
		}

		$se_parents = array();

		if($cat_id_se){
			$se_parents[] = $cat_id_se;
		}

		$act_parent = 0;
		if($cat_id_se > 0){
			foreach($list as $c){
				if($cat_id_se == $c->id ){
					$se_parents[] = $c->parent_id.','.$c->id;
					$act_parent = $c->parent_id;
					break;
				}
			}
			while($act_parent!=0){
				foreach($list as $c){
					if($act_parent == $c->id ){
						$se_parents[] = $c->parent_id.','.$c->id;
						$act_parent = $c->parent_id;
						break;
					}
				}
			}

		}
	}
	$reg_id_se = 0;

	if($params->get('show_loc','1')==1 && $params->get('follow_region','1')==1 && JRequest::getVar('option') == 'com_djclassifieds'){
		$act_reg_parent = 0;
		if(JRequest::getVar('se','0','','string')!='0' && isset($_GET['se_regs'])){
			if(is_array($_GET['se_regs'])){
				$reg_id_se= end($_GET['se_regs']);
				if($reg_id_se=='' && count($_GET['se_regs'])>2){
					$reg_id_se =$_GET['se_regs'][count($_GET['se_regs'])-2];
				}
			}else{
				$reg_ids_se = explode(',', JRequest::getVar('se_regs'));
				$reg_id_se = end($reg_ids_se);
			}
			$reg_id_se=(int)$reg_id_se;
		}

		if($reg_id_se=='0'){
			$reg_id_se = JRequest::getInt('rid',DJClassifiedsRegion::getDefaultRegion());
		}
		$se_reg_parents = array();
		if($reg_id_se){
			$se_reg_parents[] = $reg_id_se;
		}

		if($reg_id_se > 0){
			foreach($regions as $r){
				if($reg_id_se == $r->id ){
					$se_reg_parents[] = $r->parent_id.','.$r->id;
					$act_reg_parent = $r->parent_id;
					break;
				}
			}
			while($act_reg_parent!=0){
				foreach($regions as $r){
					if($act_reg_parent == $r->id ){
						$se_reg_parents[] = $r->parent_id.','.$r->id;
						$act_reg_parent = $r->parent_id;
						break;
					}
				}
			}
		}
	}
	if($cat_id_se > 0 || $reg_id_se > 0){

		?>
		<script type="text/javascript">
			window.addEvent("load", function(){
				<?php
				if($cat_id_se>0){
				/*for($sp=count($se_parents)-1;$sp>0 ;$sp--){		
					echo 'se'.$module->id.'_new_cat('.$se_parents[$sp] .','.$se_parents[$sp-1].');';
				} 
				?>
				se<?php echo $module->id;?>_new_cat(<?php echo $se_parents[0]; ?>,<?php echo $cat_id_se; ?>);*/

				$c_path = $se_parents;

				/*echo 'var cat_path = new Array();';
				$cat_path_f = 'se'.$module->id.'_new_cat(';
				for($r=count($c_path)-1;$r>0;$r--){
					if($r<count($c_path)-1){
						$ri = count($c_path) - $r -2;
						echo "cat_path[$ri]='$c_path[$r]';";
					}else{
						$cat_path_f .= $c_path[$r];
					}
				}

				echo $cat_path_f.',cat_path);'; ?>*/

				if($params->get('cat_select_type','0')==1){
					echo "document.id('se".$module->id."_cat_0').value=".$cat_id_se.";";
				}else{
					echo 'var cat_path = new Array();';
					$cat_path_match = false;
					$cat_path_f = 'se'.$module->id.'_new_cat(';
					for($r=count($c_path)-1;$r>0;$r--){
						if($r<count($c_path)-1){
							$ri = count($c_path) - $r -2;
							echo "cat_path[$ri]='$c_path[$r]';";
						}else{
							$cat_path_f .= $c_path[$r];
							$cat_path_match = true;
						}
					}

					if($cat_path_match) echo $cat_path_f.',cat_path);';
				}
				?>


				se<?php echo $module->id;?>_getFields(<?php echo $cat_id_se; ?>);

				<?php } ?>
				<?php
				if($reg_id_se > 0){
					$r_path = $se_reg_parents;

					if($params->get('loc_select_type',0)==1){
						echo "document.id('se".$module->id."_reg_0').value=".$reg_id_se.";";
					}else{
						echo 'var reg_path = new Array();';
						$reg_path_match = false;
						$reg_path_f = 'se'.$module->id.'_new_reg(';
						for($r=count($r_path)-1;$r>0;$r--){
							if($r<count($r_path)-1){
								$ri = count($r_path) - $r -2;
								echo "reg_path[$ri]='$r_path[$r]';";
							}else{
								$reg_path_f .= $r_path[$r];
								$reg_path_match = true;
							}
						}
						if($reg_path_match) echo $reg_path_f.',reg_path);';
					}

					//print_r($se_reg_parents);die();
					/*for($sp=count($se_reg_parents)-1;$sp>0 ;$sp--){
						echo 'se'.$module->id.'_new_reg('.$se_reg_parents[$sp] .','.$se_reg_parents[$sp-1].');';
					} */

					/*if($reg_id_se>0){ ?>
					se<?php echo $module->id;?>_new_reg(<?php echo $se_reg_parents[0]; ?>,<?php echo $reg_id_se; ?>);
					<?php }*/
				}	 ?>
			});
		</script>
		<?php

	}

	if($cat_id_se==0 && $params->get('show_cat','1')==1 && $params->get('cat_id','0')>0){
		if(JRequest::getVar('option','')!='com_djclassifieds' || (JRequest::getInt('se',0)==0 && JRequest::getInt('option','')=='com_djclassifieds')){
			$cat_id = $params->get('cat_id','0');
			$se_parents = array();
			$se_parents[]=$cat_id;
			$act_parent = 0;
			foreach($list as $c){
				if($cat_id == $c->id ){
					$se_parents[] = $c->parent_id.','.$c->id;
					$act_parent = $c->parent_id;
					break;
				}
			}
			while($act_parent!=0){
				foreach($list as $c){
					if($act_parent == $c->id ){
						$se_parents[] = $c->parent_id.','.$c->id;
						$act_parent = $c->parent_id;
						break;
					}
				}
			}

			//print_r($se_parents);die();

			?>
			<script type="text/javascript">
				window.addEvent("load", function(){
					<?php
					if($cat_id>0){

					$c_path = $se_parents;

					echo 'var cat_path = new Array();';
					$cat_path_f = 'se'.$module->id.'_new_cat(';
					for($r=count($c_path)-1;$r>0;$r--){
						if($r<count($c_path)-1){
							$ri = count($c_path) - $r -2;
							echo "cat_path[$ri]='$c_path[$r]';";
						}else{
							$cat_path_f .= $c_path[$r];
						}
					}

					echo $cat_path_f.',cat_path);'; ?>


					se<?php echo $module->id;?>_getFields(<?php echo $cat_id; ?>);

					<?php } ?>


				});
			</script>




			<?php
		}
	}

	if($cat_id_se==0 && $params->get('show_cat','1')==1 && $params->get('cat_id','0')==0){ ?>

		<script type="text/javascript">
			window.addEvent('domready', function(){
				se<?php echo $module->id;?>_getFields(0);
			});
		</script>
		<?php
	}

	if($params->get('show_address','0')==1){ ?>
		<script type="text/javascript">
			window.addEvent('domready', function(){
				if(typeof google !== 'undefined' && typeof google.maps !== 'undefined'){
					djcfmodSearchPlaces<?php echo $module->id;?>('<?php echo $params->get('api_country',''); ?>');
				}
			});

			function djcfmodSearchPlaces<?php echo $module->id;?>(country_iso){
				var input = (document.getElementById('se_address<?php echo $module->id;?>'));
				var aut_options = '';
				if(country_iso){
					aut_options = {componentRestrictions: {country: country_iso}};
				}


				var autocomplete = new google.maps.places.Autocomplete(input,aut_options);
				var infowindow = new google.maps.InfoWindow();
				var last_place = '';
				google.maps.event.addListener(autocomplete, 'places_changed', function() {
					/*	var place = autocomplete.getPlaces()[0]; //to get first on enter
						if (!place.geometry) {
								return;
						}

						if (place.geometry.viewport) {
							djmod_map< ?php echo $module->id;?>.fitBounds(place.geometry.viewport);
						} else {
							djmod_map< ?php echo $module->id;?>.setCenter(place.geometry.location);
						}
						*/

				});


				// dojo.connect(input, 'onkeydown', function(e) {
				google.maps.event.addDomListener(input, 'keydown', function(e) {
					if (e.keyCode == 13)
					{
						if (e.preventDefault)
						{
							e.preventDefault();
						}
						else
						{
							// Since the google event handler framework does not handle early IE versions, we have to do it by our self. :-(
							e.cancelBubble = true;
							e.returnValue = false;
						}
					}
				});

			}


		</script>
	<?php }

	if($params->get('show_geoloc','0')==1){ ?>
		<script type="text/javascript">
			window.addEvent('domready', function(){
				document.id('se_geoloc_icon<?php echo $module->id;?>').addEvent('click',function(event){
					if(navigator.geolocation){
						navigator.geolocation.getCurrentPosition(modSearchShowDJPosition<?php echo $module->id;?>,
							function(error){
								//alert("<?php echo str_ireplace('"', "'",JText::_(''));?>");
								alert(error.message);

							}, {
								timeout: 30000, enableHighAccuracy: true, maximumAge: 90000
							});
					}
				})
			});



			function modSearchShowDJPosition<?php echo $module->id;?>(position){
				var exdate=new Date();
				exdate.setDate(exdate.getDate() + 1);
				var ll = position.coords.latitude+'_'+position.coords.longitude;
				document.cookie = "djcf_latlon=" + ll + "; expires=" + exdate.toUTCString()+";path=/";
				//document.id('se_postcode<?php echo $module->id?>').value = '00-000';
				document.id('se_geoloc<?php echo $module->id?>').value = '1';
				document.id('form-search<?php echo $module->id; ?>').submit();
			}
		</script>


	<?php }

	if($params->get('show_input_hints','0')==1 && $params->get('show_input','1')==1){ ?>
		
		<script type="text/javascript">
			window.addEvent('domready', function(){
				var myRequest = new Request({
					url: '<?php echo JURI::base()?>index.php',
					method: 'post',
					data: {
						'option': 'com_djclassifieds',
						'view': 'item',
						'task': 'getSearchTags',
						'source': '<?php echo implode(',', $params->get('input_hints_source',Array('items','categories','regions')));?>'
					},
					onRequest: function(){},
					onSuccess: function(responseText){
						//console.log(responseText);
						var djlist = JSON.parse(responseText);
						//console.log(djlist);

						new MooComplete('input_search<?php echo $module->id?>', {
							list: djlist,
							size: 5,
							render: function(v) {
								// console.log(v);
								var se_i = document.id('input_search<?php echo $module->id?>').value;
								var se_ib = se_i.charAt(0).toUpperCase() + se_i.slice(1);
								//console.log(se_ib);
								v = v.replace(se_ib,'||||'+se_ib+'|||');
								nv = v.replace(se_i,'||||'+se_i+'|||');
								nv = nv.replace('||||','<b class="pac-matched">');
								nv = nv.replace('||||','<b class="pac-matched">');
								nv = nv.replace("|||","</b>");
								nv = nv.replace("|||","</b>");

								return [
									new Element('span', {
										'class':  'pac-item-query',
										html: nv
									})
								];
							},
							get: function(v) {
								return v;
								//var vs = v.split(" > ");
								//return vs[0];
							}
						});
					},
					onFailure: function(){}
				});
				myRequest.send();

			});
		</script>


	<?php }

?>