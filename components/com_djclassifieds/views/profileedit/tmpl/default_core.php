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
* DJ Classifieds is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* DJ Classifieds is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with DJ Classifieds. If not, see <http://www.gnu.org/licenses/>.
* 
*/
defined ('_JEXEC') or die('Restricted access');

//jimport('joomla.media.images');
JHTML::_('behavior.framework','More');
JHTML::_('behavior.keepalive');
JHTML::_('behavior.formvalidation');
JHTML::_('behavior.modal');
$toolTipArray = array('className'=>'djcf_label');
JHTML::_('behavior.tooltip', '.Tips1', $toolTipArray);


$par 	  = JComponentHelper::getParams( 'com_djclassifieds' );
$app 	  = JFactory::getApplication();
$user 	  = JFactory::getUser();
$document = JFactory::getDocument();
$config   = JFactory::getConfig();

?>  
         <div class="title_top"><?php echo JText::_('COM_DJCLASSIFIEDS_PROFILE_EDITION_BASIC');	?></div>
			<div class="additem_djform_in">
            	<div class="djform_row">
            		<?php if($par->get('show_tooltips_newad','0')){ ?>
		            	<label for="u_name" id="u_name-lbl" class="label Tips1" title="<?php echo JTEXT::_('COM_USERS_PROFILE_NAME_DESC')?>">
		                    <?php echo JText::_('COM_USERS_PROFILE_NAME_LABEL');?> *
		                    <img src="<?php echo JURI::base(true) ?>/components/com_djclassifieds/assets/images/tip.png" alt="?" />
		                </label>	                               			                	
					<?php }else{ ?>
		            	<label for="u_name" id="u_name-lbl" class="label">
		                	  <?php echo JText::_('COM_USERS_PROFILE_NAME_LABEL'); ?> *					
		                </label>
	            	<?php } ?>
	                <div class="djform_field">
	                    <input class="text_area required" type="text" name="u_name" id="u_name" size="50" maxlength="250" value="<?php echo $user->name; ?>" />
	                </div>
	                <div class="clear_both"></div> 
	            </div>   
	                     
            	<div class="djform_row">
            		<?php if($par->get('show_tooltips_newad','0')){ ?>
		            	<label for="u_password1" id="u_password1-lbl" class="label Tips1" title="<?php echo JTEXT::_('COM_USERS_DESIRED_PASSWORD')?>">
		                    <?php echo JText::_('COM_USERS_PROFILE_PASSWORD1_LABEL');?>
		                    <img src="<?php echo JURI::base(true) ?>/components/com_djclassifieds/assets/images/tip.png" alt="?" />
		                </label>	                               			                	
					<?php }else{ ?>
		            	<label for="u_password1" id="u_password1-lbl" class="label">
		                	  <?php echo JText::_('COM_USERS_PROFILE_PASSWORD1_LABEL'); ?>					
		                </label>
	            	<?php } ?>
	                <div class="djform_field">
	                    <input class="text_area validate-password" type="password" name="u_password1" id="u_password1" size="50" maxlength="250" value="" />
	                </div>
	                <div class="clear_both"></div> 
	            </div> 	          
	            
	            <div class="djform_row">
            		<?php if($par->get('show_tooltips_newad','0')){ ?>
		            	<label for="u_password2" id="u_password2-lbl" class="label Tips1" title="<?php echo JTEXT::_('COM_USERS_PROFILE_PASSWORD2_DESC')?>">
		                    <?php echo JText::_('COM_USERS_PROFILE_PASSWORD2_LABEL');?>
		                    <img src="<?php echo JURI::base(true) ?>/components/com_djclassifieds/assets/images/tip.png" alt="?" />
		                </label>	                               			                	
					<?php }else{ ?>
		            	<label for="u_password2" id="u_password2-lbl" class="label">
		                	  <?php echo JText::_('COM_USERS_PROFILE_PASSWORD2_LABEL'); ?>					
		                </label>
	            	<?php } ?>
	                <div class="djform_field">
	                    <input class="text_area validate-password" type="password" name="u_password2" id="u_password2" size="50" maxlength="250" value="" />
	                </div>
	                <div class="clear_both"></div> 
	            </div> 	 
	            
	            <div class="djform_row">
            		<?php if($par->get('show_tooltips_newad','0')){ ?>
		            	<label for="u_email1" id="u_email1-lbl" class="label Tips1" title="<?php echo JTEXT::_('COM_USERS_PROFILE_EMAIL1_DESC')?>">
		                    <?php echo JText::_('COM_USERS_PROFILE_EMAIL1_LABEL');?> *
		                    <img src="<?php echo JURI::base(true) ?>/components/com_djclassifieds/assets/images/tip.png" alt="?" />
		                </label>	                               			                	
					<?php }else{ ?>
		            	<label for="u_email1" id="u_email1-lbl" class="label">
		                	  <?php echo JText::_('COM_USERS_PROFILE_EMAIL1_LABEL'); ?> *					
		                </label>
	            	<?php } ?>
	                <div class="djform_field">
	                    <input class="text_area required" type="text" name="u_email1" id="u_email1" size="50" maxlength="250" value="<?php echo $user->email; ?>" />
	                </div>
	                <div class="clear_both"></div> 
	            </div>      
	            
  				<div class="djform_row">
            		<?php if($par->get('show_tooltips_newad','0')){ ?>
		            	<label for="u_email2" id="u_email2-lbl" class="label Tips1" title="<?php echo JTEXT::_('COM_USERS_PROFILE_EMAIL2_DESC')?>">
		                    <?php echo JText::_('COM_USERS_PROFILE_EMAIL2_LABEL');?> *
		                    <img src="<?php echo JURI::base(true) ?>/components/com_djclassifieds/assets/images/tip.png" alt="?" />
		                </label>	                               			                	
					<?php }else{ ?>
		            	<label for="u_email2" id="u_email2-lbl" class="label">
		                	  <?php echo JText::_('COM_USERS_PROFILE_EMAIL2_LABEL'); ?> *					
		                </label>
	            	<?php } ?>
	                <div class="djform_field">
	                    <input class="text_area required" type="text" name="u_email2" id="u_email2" size="50" maxlength="250" value="<?php echo $user->email; ?>" />
	                </div>
	                <div class="clear_both"></div> 
	            </div>    	                
			</div> 