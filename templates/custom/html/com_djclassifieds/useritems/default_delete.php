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

JHTML::_('behavior.framework');
JHTML::_('behavior.tooltip');
$toolTipArray = array('className'=>'djcf');
JHTML::_('behavior.tooltip', '.Tips1', $toolTipArray);

$par	 = JComponentHelper::getParams( 'com_djclassifieds' );
$config  = JFactory::getConfig();
$app	 = JFactory::getApplication();
$user	 = JFactory::getUser();
$id 	 = JRequest::getInt('id', 0);
$token	 = JRequest::getCmd('token','');
$itemid  = JRequest::getVar('Itemid', 0, '', 'int');

$mod_attribs=array();
$mod_attribs['style'] = 'xhtml';


?>
<div id="dj-classifieds" class="djcf_warning_delete clearfix">
	<div class="adt_block bg-white">
		<div class="bg-white__header mb-3">
			<div class="h2"><?php echo JText::_('COM_DJCLASSIFIEDS_DELETE_CONFIRMATION');?></div>
		</div>
		<div class="djcf_warning_outer clearfix">
			<div class="djcf_warning_outer_in">
				<?php $modules_djcf = &JModuleHelper::getModules('djcf-delete-top');
					if(count($modules_djcf)>0){
						echo '<div class="djcf-war-top clearfix">';
						foreach (array_keys($modules_djcf) as $m){
							echo JModuleHelper::renderModule($modules_djcf[$m],$mod_attribs);
						}
						echo'</div>';
					}	?>
				<div class="djcf_war_content">
					<?php echo JText::_('COM_DJCLASSIFIEDS_DELETE_CONFIRM');?>
					<?php
						echo ' "<a class="" href="'.DJClassifiedsSEO::getItemRoute($this->item->id.':'.$this->item->alias,$this->item->cat_id.':'.$this->item->c_alias,$this->item->region_id.':'.$this->item->r_name).'">';
						echo $this->item->name;
						echo '</a>"';?>
				</div>
				<?php $modules_djcf = &JModuleHelper::getModules('djcf-delete-center');
					if(count($modules_djcf)>0){
						echo '<div class="djcf-war-center clearfix">';
						foreach (array_keys($modules_djcf) as $m){
							echo JModuleHelper::renderModule($modules_djcf[$m],$mod_attribs);
						}
						echo'</div>';
					}	?>
				<div class="djcf_war_buttons">
					<a class="btn btn_accent-black" href="<?php echo DJClassifiedsSEO::getUserAdsLink(); ?>">
						<?php echo JText::_('COM_DJCLASSIFIEDS_CANCEL'); ?>
					</a>
					<?php if($user->id>0 && $id>0){
						$delete_link = 'index.php?option=com_djclassifieds&view=item&task=delete&id='.$id.'&Itemid='.$itemid ;
					}else{
						$delete_link = 'index.php?option=com_djclassifieds&view=item&task=deletetoken&token='.$token;
					}?>
					<a href="<?php echo $delete_link ; ?>" class="btn btn_accent" >
						<?php echo JText::_('COM_DJCLASSIFIEDS_DELETE');?>
					</a>
					<div class="clear_both"></div>
				</div>
				<?php $modules_djcf = &JModuleHelper::getModules('djcf-delete-bottom');
					if(count($modules_djcf)>0){
						echo '<div class="djcf-war-bottom clearfix">';
						foreach (array_keys($modules_djcf) as $m){
							echo JModuleHelper::renderModule($modules_djcf[$m],$mod_attribs);
						}
						echo'</div>';
					}	?>
				<div class="clear_both"></div>
			</div>
		</div>
	</div>

</div>