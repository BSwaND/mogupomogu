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
	JHTML::_('behavior.framework',true);
	JHTML::_('behavior.formvalidation');
	JHTML::_('behavior.calendar');
	$par = JComponentHelper::getParams( 'com_djclassifieds' );
	$app = JFactory::getApplication();
	$config = JFactory::getConfig();
	$user =  JFactory::getUser();
	$Itemid = JRequest::getVar('Itemid', 0,'', 'int');
	$item = $this->item;
	$item_class='';

	$icon_new_a	= $par->get('icon_new','1');
	$icon_new_date = mktime(date("G"), date("i"), date("s"), date("m"), date("d")-$par->get('icon_new_time','3'), date("Y"));
	$date_start = strtotime($item->date_start);
	$icon_new=0;
	if($item->promotions){
		$item_class .=' promotion '.str_ireplace(',', ' ', $item->promotions);
	}
	if($date_start>$icon_new_date && $icon_new_a){
		$icon_new=1;
		$item_class .= ' item_new';
	}

	if($item->auction){
		$item_class .=' item_auction';
	}

	if($par->get('favourite','1') && $user->id>0){
		if($item->f_id){ $item_class .= ' item_fav'; }
	}

	if($item->user_id && isset($this->profile['details']->verified)){
		if($this->profile['details']->verified==1){
			$item_class .= ' verified_profile';
		}
	}

	$menus	= $app->getMenu('site');
	$menu_newad_m = $menus->getItems('link','index.php?option=com_djclassifieds&view=additem',1);
	$menu_newad_itemid='';
	if($menu_newad_m){
		$menu_newad_itemid = '&Itemid='.$menu_newad_m->id;
	}

	$pageclass_sfx ='';
	if($Itemid){
		$menu_item = $app->getMenu()->getItem($Itemid);
		$pc_sfx = $menu_item->params->get('pageclass_sfx');
		if($pc_sfx){$pageclass_sfx =' '.$pc_sfx;}
	}

	$mod_attribs=array();
	$mod_attribs['style'] = 'xhtml';


	$userProfile =  JFactory::getUser();
	$userProfileField =  JUserHelper::getProfile($item->user_id);

	$uid_slug = $item->user_id.':'.DJClassifiedsSEO::getAliasName($item->username);
	$profile_itemid = DJClassifiedsSEO::getUserProfileItemid() ? DJClassifiedsSEO::getUserProfileItemid() : '&Itemid='.$Itemid;

	jimport('joomla.application.module.helper');
	$attribs['style'] = 'none';

?>
<div id="dj-classifieds" class="clearfix djcftheme-<?php echo $this->theme;?><?php echo $pageclass_sfx;?><?php if(JFactory::getDate() >= $item->date_exp){ echo ' item_expired'; }?>">
	  
	<div class="row">
		<div class="col-lg-4">
			<div class="bg-white">
				<div class="bg-white__header ">
					<div class="h2">Фильтр</div>
				</div>
				<form action="" class="form_aside-search">
					<div class="form_aside-search__input-outer">
						<input type="text" placeholder="Что вы ищете?">
						<input type="text" placeholder="Адрес">
						<label class="label__radius">
							<span class="label__radius__marker"></span>
							<input type="text" name="radius" placeholder="Растояние">
						</label>
					</div>

					<div class="form_aside-search__input-outer">
						<div class="form_aside-search__title">Место расположениея</div>
						<select name="" id="">
							<option value="Киев">Киев</option>
							<option value="Одесса">Одесса</option>
							<option value="Львов">Львов</option>
						</select>
					</div>

					<div class="form_aside-search__input-outer">
						<select name="" >
							<option value="Киев">Мебель</option>
							<option value="Одесса">Одесса</option>
							<option value="Львов">Львов</option>
						</select>
					</div>

					<div class="form_aside-search__input-outer">
						<div class="form_aside-search__title">Спальни</div>
						<label class="checkbox_outer">
							<input type="checkbox" class="checkbox_hidden-input">
							<span class="checkbox_span-label">1</span>
						</label>
						<label class="checkbox_outer">
							<input type="checkbox" class="checkbox_hidden-input">
							<span class="checkbox_span-label">2</span>
						</label>
						<label class="checkbox_outer">
							<input type="checkbox" class="checkbox_hidden-input">
							<span class="checkbox_span-label">3</span>
						</label>
						<label class="checkbox_outer">
							<input type="checkbox" class="checkbox_hidden-input">
							<span class="checkbox_span-label">4+</span>
						</label>
					</div>

					<input type="submit" class="btn form_aside-search__btn-submit" value="Искать">
					<div class="form_aside-search__btn-clear">Сбросить настройки</div>
				</form>
			</div>
		</div>

		<div class="col-lg-14">
			<div class="">
				<div class="">
					<?php
						if($user->id>0 && $item->published!=2 &&
							($user->id==$item->user_id || ($par->get('admin_can_edit_delete','0') && $user->authorise('core.admin', 'com_djclassifieds')))){
							echo '<a href="index.php?option=com_djclassifieds&view=additem&id='.$item->id.$menu_newad_itemid.'" class="btn btn_accent">'.JText::_('COM_DJCLASSIFIEDS_EDIT').'</a>';
							if($par->get('ad_preview','0') && JRequest::getInt('prev',0)){
								echo '<a href="index.php?option=com_djclassifieds&view=additem&task=publish&id='.$item->id.$menu_newad_itemid.'" class="title_save button btn btn_accent">'.JText::_('COM_DJCLASSIFIEDS_SAVE_AND_PUBLISH').'</a>';
							}
							echo '<a href="index.php?option=com_djclassifieds&view=useritems&t=delete&id='.$item->id.$menu_newad_itemid.'" class="title_delete button btn btn_accent-black">'.JText::_('COM_DJCLASSIFIEDS_DELETE').'</a>';
						}
					?>
				</div>
				<div class="adt_card_header">

					<h1 class="h1"><?= $item->name ?></h1>
					<div class="">
						<?= ($item->t_name) ? ' <div class="label '. json_decode($item->t_params)->bt_class.'">'. $item->t_name .'</div>' : null ?>
						<?= ($this->fields[1] && $this->fields[1]->value != '---') ? ' <div class="label " data-class="'. $this->fields[1]->value .'">'. $this->fields[1]->value .'</div>' : null ?>
					</div>
				</div>
				<div class="adt_card_header_info">
					<div class="adt_item__sity"><?= $item->r_name ?></div>
					<div class="adt_item__date"><?=  date("d.m.Y",  strtotime($item->date_start)) ?></div>
				</div>

				<div class="adt_card_body">
					<div class="row">
						<div class="col-md-8">
							<?= (count($this->item_images) || $par->get('ad_image_default','0')==1 ) ? $this->loadTemplate('images') : null?>
							<img src="images/krovat.jpg" alt="">
						</div>
						<div class="col-md-10">
							<div class="bg-white p-3 fw-3">
								<?= htmlspecialchars_decode( $item->description )?>
								<div class="mt-3">
									<?php if($par->get('sb_position','0')=='bottom' && $par->get('sb_code','')!=''){
										//	echo '<span class="sb_bottom">'.$par->get('sb_code','').'</span>';
									}?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="adt_author_block">
				<div class="">
					<div class="h1">Информация о пользователе</div>
					<div class="row mt-3">
						<div class="col-md-10">
							<div class="bg-white p-3">
								<div class="adt_author_card">
									<div class="adt_author_card_header">
										<?php
											echo '<a itemprop="url" class="profile_img" href="index.php?option=com_djclassifieds&view=profile&uid='.$uid_slug.$profile_itemid.'">';
											if($par->get('profile_avatar_source','')){

											}else{
												if($this->profile['img']){
													echo '<img class="adt_author_foto" itemprop="image" alt="'.$item->username.' - logo" src="'.JURI::base(true).$this->profile['img']->path.$this->profile['img']->name.'_ths.'.$this->profile['img']->ext.'" />';
												}else{
													echo '<img class="adt_author_foto" itemprop="image" alt="'.$item->username.' - logo"  src="'.JURI::base(true).'/components/com_djclassifieds/assets/images/default_profile_s.png" />';
												}
											}
											echo '</a>';
										?>
										<div class="">
											<div class="adt_author_card_header__name">
												<a href="index.php?option=com_djclassifieds&view=profile&uid=<?= $uid_slug.$profile_itemid ?>">
													<?= $item->username ?>
												</a>
											</div>

											<?php if($userProfile->registerDate){  ?>
												<div>на сайте с <?= date("Y",  strtotime($userProfile->registerDate)) ?></div>
											<?php }  ?>
										</div>
									</div>

									<div class="adt_author_block_body">
										<?php /*
										<div class="adt_author_block_body_row">
											<span class="adt_author_block_body_row__title">Детали:</span>
											<span><a href="#">Подробнее о моем профиле</a></span>
										</div>
 */ ?>

										<?php if(isset($userProfileField->profile['phone'])) {?>
											<div class="adt_author_block_body_row">
												<span class="adt_author_block_body_row__title">Телефон:</span>
												<span><?= $userProfileField->profile['phone'] ?></span>
											</div>
										<?php }?>

										<div class="adt_author_block_body_row">
											<span class="adt_author_block_body_row__title">Email:</span>
											<span><?= $item->u_email ?></span>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-8">
							<div class="adt_author_block__control">
								<?=  $this->loadTemplate('generaldetails') ?>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>

	<div class="adt_card_statistics">
		<div class="h1">Статистика объявления</div>
		<div class="row">
			<div class="col-md-4">
				<div class="">Идентификатор:</div>
				<div class="fw-3"><?= $item->id ?></div>
			</div>
			<div class="col-md-3">
				<div class="">Показы:</div>
				<div class="fw-3"><?= $item->display ?></div>
			</div>
			<?php /*
			<div class="col-md-4">
				<div class="">Срок действия истекает:</div>
				<div class="fw-3">12.12.2020   21:00</div>
			</div>
 */ ?>
			<div class="col-md-6">
				<div class="">В категориях:</div>
				<div class="fw-3">
					<span class="row_value">
						<?php
							echo '<a href="'.DJClassifiedsSEO::getCategoryRoute($item->cat_id.':'.$item->c_alias).'" >'. mb_strtolower($item->c_name).'</a>';
							if (count($item->extra_cats)) {
								foreach($item->extra_cats as $ecat){
									echo ', <a href="'.DJClassifiedsSEO::getCategoryRoute($ecat->id.':'.$ecat->alias).'" >'.mb_strtolower( $ecat->name) .'</a>';
								}
							}
						?>
					</span>
				</div>
			</div>
		</div>
	</div>


	<?php
		$modules = JModuleHelper::getModules('aftor_items');
		foreach($modules as $module) {
			echo JModuleHelper::renderModule($module, $attribs);
		}
	?>
</div>

<?php if($item->event->afterDJClassifiedsDisplayTitle) { ?>
	<div class="djcf_after_title">
		<?php echo $this->item->event->afterDJClassifiedsDisplayTitle; ?>
	</div>
<?php } ?>
<div class="dj-item-in">

	<div class="clear_both" ></div>
	<?php if($this->item->event->afterDJClassifiedsDisplayContent) { ?>
		<div class="djcf_after_desc">
			<?php echo $item->event->afterDJClassifiedsDisplayContent; ?>
		</div>
	<?php } ?>
	<?php  echo $this->loadTemplate('comments'); ?>
	<div class="clear_both" ></div>
</div>
</div>
<?php
	$trigger_after = trim(implode("\n", $this->dispatcher->trigger('onAfterDJClassifiedsDisplay', array (&$this->items, & $par, 'item'))));
	if($trigger_after) { ?>
		<div class="djcf_after_display">
			<?php echo $trigger_after; ?>
		</div>
	<?php } ?>
<?php
	$modules_djcf = &JModuleHelper::getModules('djcf-item-bottom');
	if(count($modules_djcf)>0){
		echo '<div class="djcf-ad-item-bottom clearfix">';
		foreach (array_keys($modules_djcf) as $m){
			echo JModuleHelper::renderModule($modules_djcf[$m],$mod_attribs);
		}
		echo'</div>';
	}

	$modules_djcf = &JModuleHelper::getModules('djcf-bottom');
	if(count($modules_djcf)>0){
		echo '<div class="djcf-ad-bottom clearfix">';
		foreach (array_keys($modules_djcf) as $m){
			echo JModuleHelper::renderModule($modules_djcf[$m],$mod_attribs);
		}
		echo'</div>';
	}

	$modules_djcf = &JModuleHelper::getModules('djcf-bottom-cat'.$item->cat_id);
	if(count($modules_djcf)>0){
		echo '<div class="djcf-ad-bottom-cat clearfix">';
		foreach (array_keys($modules_djcf) as $m){
			echo JModuleHelper::renderModule($modules_djcf[$m],$mod_attribs);
		}
		echo'</div>';
	}
?>


<script type="text/javascript">
	this.DJCFShowValueOnClick = function (){
		var fields = document.id('dj-classifieds').getElements('.djsvoc');
		if(fields) {
			fields.each(function(field,index){
				field.addEvent('click', function(evt) {
					var f_rel = field.getProperty('rel');
					if(f_rel){
						field.innerHTML = '<a href="'+f_rel+'">'+field.title+'</a>';
					}else{
						field.innerHTML = field.title;
					}
					return true;
				});
			});
		}
	};

	window.addEvent('domready', function(){
		DJCFShowValueOnClick();
	});
</script>
