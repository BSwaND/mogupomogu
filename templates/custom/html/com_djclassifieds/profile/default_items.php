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
$toolTipArray = array('className'=>'djcf');
JHTML::_('behavior.tooltip', '.Tips1', $toolTipArray);
$par = JComponentHelper::getParams( 'com_djclassifieds' );
$user= JFactory::getUser();
$app = JFactory::getApplication();

$main_id= JRequest::getVar('cid', 0, '', 'int');
$fav_a	= $par->get('favourite','1');
$icon_new_a	= $par->get('icon_new','1');
$icon_new_date = mktime(date("G"), date("i"), date("s"), date("m"), date("d")-$par->get('icon_new_time','3'), date("Y"));
$icon_col_w = $par->get('smallth_width','56')+20;
$columns_a=2;

$order = JRequest::getCmd('order', $par->get('items_ordering','date_e'));
$ord_t = JRequest::getCmd('ord_t', $par->get('items_ordering_dir','desc'));
if($ord_t=="desc"){
	$ord_t='asc';
}else{
	$ord_t='desc';
}

$sw = htmlspecialchars(JRequest::getVar('search',''), ENT_COMPAT, 'UTF-8');
$uid	= JRequest::getVar('uid', 0, '', 'int');
$se = JRequest::getVar('se', '0', '', 'int');

$Itemid = JRequest::getInt('Itemid', 0);

$layout  = JRequest::getVar('layout','');
	if($layout=='favourites'){
		$menus	= $app->getMenu('site');	
		$menu_item = $menus->getItems('link','index.php?option=com_djclassifieds&view=items&cid=0',1);
		$menu_item_blog = $menus->getItems('link','index.php?option=com_djclassifieds&view=items&layout=blog&cid=0',1);
						
		if($menu_item){
			$Itemid = $menu_item->id;
		}else if($menu_item_blog){
			$Itemid = $menu_item_blog->id;
		}		
	}

$se_link='';
if($se){
	$se_link='&se=1';	
	if($sw){
		$se_link .= '&search='.$sw; 
	}
	foreach($_GET as $key=>$get_v){
		if(strstr($key, 'se_')){
			if(is_array($get_v)){
				for($gvi=0;$gvi<count($get_v);$gvi++){
					$se_link .= '&'.$key.'[]='.htmlspecialchars($get_v[$gvi], ENT_COMPAT, 'UTF-8');
				}
			}else{
				$se_link .= '&'.$key.'='.htmlspecialchars($get_v, ENT_COMPAT, 'UTF-8');
			}
		}
	}
}

$uid_slug = $this->profile['id'].':'.DJClassifiedsSEO::getAliasName($this->profile['name']);
?>
	<div class="items">
		<div class="row">
			<div class="col-lg-18">
				<div class="adt_block bg-white">
					<div class="bg-white__header">
						<div class="row">
							<div class="col-md-18">
								<div class="h1">Объявления пользователя</div>
							</div>
						</div>
					</div>
					<div class="adt_items">
						<div class="row">
							<?php foreach ($this->items as $item){ ?>
								<div class="col-sm-9">
									<div class="adt_item">
										<a href="<?= JRoute::_(DJClassifiedsSEO::getItemRoute($item->id.':'.$item->alias,$item->cat_id.':'.$item->c_alias,$item->region_id.':'.$item->r_name)) ?>">
											<img src="<?= $item->images['0']->thumb_item_main?>" alt="<?= $item->name ?>" class="adt_item__img">
										</a>
										<div class="adt_item_info">
											<a href="<?= JRoute::_(DJClassifiedsSEO::getItemRoute($item->id.':'.$item->alias,$item->cat_id.':'.$item->c_alias,$item->region_id.':'.$item->r_name)) ?>" class="adt_item__header"><?= $item->name ?></a>
											<div class="adt_item__date"><?= date("d.m.Y", strtotime( $item->date_start) )?></div>
											<?php /*
											<a href="<?= JRoute::_(DJClassifiedsSEO::getCategoryRoute($item->cat_id.':'.$item->c_alias)) ?>" class="adt_item__category"><?= $item->c_name ?></a>
					            */ ?>
											<?php	if($item->fields && $item->fields[1] != '---'){ ?>
												<div class="label" data-class="<?= $item->fields[1]?>"><?= $item->fields[1]?></div>
											<?php	}		?>
											<div class="">
												<a href="<?= DJClassifiedsSEO::getRegionRoute($item->region_id.':'.$item->r_name) ?>" class="adt_item__sity"><?= $item->r_name ?></a>
											</div>
										</div>
									</div>
								</div>
							<?php } ?>
						</div>
						<?php if($this->pagination->getPagesLinks()){ ?>
							<div class="pagination">
								<?php echo $this->pagination->getPagesLinks(); ?>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
			</div>


		<?php if($this->pagination->getPagesLinks()){ ?>
			<div class="pagination">
				<?php echo $this->pagination->getPagesLinks(); ?> 
			</div>
		<?php } ?>
		<?php 
		if($se>0 && count($this->items)==0){
			echo '<div class="no_results">';
				echo JText::_('COM_DJCLASSIFIEDS_NO_RESULTS');
			echo '</div>';
		}else if(!$se && count($this->items)==0 && $main_id){
			echo '<div class="no_results">';
				echo JText::_('COM_DJCLASSIFIEDS_NO_CATEGORY_RESULTS');
			echo '</div>';
		}
		?>
	</div>

