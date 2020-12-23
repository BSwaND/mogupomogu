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
*/
defined ('_JEXEC') or die('Restricted access');

JHTML::_('behavior.framework');
JHTML::_('behavior.tooltip');
$toolTipArray = array('className'=>'djcf');
JHTML::_('behavior.tooltip', '.Tips1', $toolTipArray);

$par	 = JComponentHelper::getParams( 'com_djclassifieds' );
$config  = JFactory::getConfig();
$app	 = JFactory::getApplication();
$main_id = JRequest::getVar('cid', 0, '', 'int');
$user	 = JFactory::getUser();


$menus	= $app->getMenu('site');
$menu_profileedit_itemid = $menus->getItems('link','index.php?option=com_djclassifieds&view=profileedit',1);
$user_edit_profile='index.php?option=com_djclassifieds&view=profileedit';
if($menu_profileedit_itemid){
	$user_edit_profile .= '&Itemid='.$menu_profileedit_itemid->id;
}

$menu_jprofileedit_itemid = $menus->getItems('link','index.php?option=com_users&view=profile&layout=edit',1);
$juser_edit_profile='index.php?option=com_users&view=profile&layout=edit';
if($menu_jprofileedit_itemid){
	$juser_edit_profile .= '&Itemid='.$menu_jprofileedit_itemid->id;
}

?>

<div class="profile_box">
	<?php //if($this->profile['img'] || $par->get('profile_avatar_source','')){		
		$avatar_w = $par->get('profth_width','120')+10;
		echo '<span style="width: '.$avatar_w.'px" class="profile_img" >';
			if($par->get('profile_avatar_source','')){
				echo DJClassifiedsSocial::getUserAvatar($this->profile['id'],$par->get('profile_avatar_source',''),'L');
			}else{
				if($this->profile['img']){
					echo '<img alt="'.$this->profile['name'].' - logo" src="'.JURI::base(true).$this->profile['img']->path.$this->profile['img']->name.'_th.'.$this->profile['img']->ext.'" />';	
				}else{
					echo '<img alt="'.$this->profile['name'].' - logo" style="width:'.$par->get('profth_width','120').'px" src="'.JURI::base(true).'/components/com_djclassifieds/assets/images/default_profile.png" />';
				}
				
			}
			
		echo '</span>';
	//}?>
	<div class="profile_name_data">
		<div class="main_cat_title">		
			<h2 class="profile_name"><?php echo $this->profile['name']; ?>	
			<?php  			
			
			if(isset($this->profile['details']->verified)){
				if($this->profile['details']->verified==1){
					echo '<span class="verified_icon" title="'.JText::_('COM_DJCLASSIFIEDS_VERIFIED_SELLER').'" ></span>';
				}
			}
			
			if($user->id==$this->profile['id'] && $user->id>0){			
				echo '<div><a href="'.$user_edit_profile.'" class="title_edit button">'.JText::_('COM_DJCLASSIFIEDS_PROFILE_EDITION').'</a></div>';
				//echo '<a href="'.$juser_edit_profile.'" class="title_edit title_jedit button">'.JText::_('COM_DJCLASSIFIEDS_CHANGE_PASSWORD_EMAIL').'</a>';
			} ?>
			</h2>			
		</div>
		<?php if($this->profile['data']){ ?>
			<div class="profile_data">
			<?php foreach($this->profile['data'] as $f){
				if($par->get('show_empty_cf','1')==0){
					if(!$f->value && ($f->value_date=='' || $f->value_date=='0000-00-00')){
						continue;
					}
				}
				$tel_tag = '';
				if(strstr($f->name, 'tel')){
					$tel_tag='tel:'.$f->value;
				}else if(strstr($f->name, 'whatsapp')){
					$tel_tag='https://wa.me/'.preg_replace('/\D/', '', $f->value);
				}				
				?>

			<?php
			}?>
			</div> 
		<?php } ?>

			<?php echo $this->loadTemplate('localization'); ?>
			<?php echo $this->loadTemplate('contactform'); ?>

			<?php if($par->get('profile_social_link','')){ ?>
				<div class="profile_row row_social_link">
					<span class="profile_row_label"></span>
					<span class="profile_row_value">
						<a href="<?php echo DJClassifiedsSocial::getUserProfileLink($this->profile['id'],$par->get('profile_social_link','')); ?>" alt="" >
							<?php echo JText::_('COM_DJCLASSIFIEDS_VISIT_SOCIAL_PROFILE'); ?>
						</a>
					</span>
				</div>
			<?php }?>
	</div>
	
	<div class="clear_both"></div>					
</div>
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