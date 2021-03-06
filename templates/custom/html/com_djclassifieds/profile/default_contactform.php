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

JHTML::_('behavior.framework',true);
JHTML::_('behavior.formvalidation');
JHTML::_('behavior.calendar');
JHTML::_('behavior.tooltip');
$toolTipArray = array('className'=>'djcf');
JHTML::_('behavior.tooltip', '.Tips1', $toolTipArray);

$par	 = JComponentHelper::getParams( 'com_djclassifieds' );
$config  = JFactory::getConfig();
$app	 = JFactory::getApplication();
$main_id = JRequest::getVar('cid', 0, '', 'int');
$user	 = JFactory::getUser();

$session = JFactory::getSession();
require_once(JPATH_COMPONENT.DS.'assets'.DS.'recaptchalib.php');
$captcha_type = $par->get('captcha_type','recaptcha');
$publickey = $par->get('captcha_publickey',"6LfzhgkAAAAAAL9RlsE0x-hR2H43IgOFfrt0BxI0");
$privatekey = $par->get('captcha_privatekey',"6LfzhgkAAAAAAOJNzAjPz3vXlX-Bw0l-sqDgipgs");
if($captcha_type=='nocaptcha'){
	//$document= JFactory::getDocument();
	//$document->addScript("https://www.google.com/recaptcha/api.js");
	JHtml::_('script', 'plg_captcha_recaptcha/recaptcha.min.js', false, true);
	$file = 'https://www.google.com/recaptcha/api.js?onload=JoomlaInitReCaptcha2&render=explicit&hl=' . JFactory::getLanguage()->getTag();
	JHtml::_('script', $file);
}
$error='';
$Itemid = JRequest::getVar('Itemid', 0,'', 'int');

$own_profile = false;
$uid = JRequest::getVar('uid', 0,'', 'int');
if(($uid && $user->id && $uid == $user->id) || (!$uid && $user->id)){
	$own_profile = true;
}

if(!$user->id){
	$uri = JUri::getInstance();
	$login_url = JUri::getInstance(JRoute::_('index.php?option=com_users&view=login', false));
	$login_url->setVar('return', base64_encode($uri));
}

if($par->get('ask_seller','0') && !$own_profile){ ?>
	<div class="profile_contact_form">		
		<?php if($par->get('ask_seller','0')==1){ ?>
			<div id="ask_form_button" class="button btn btn_accent-black" >Связаться с автором</div>
		<?php }else if($par->get('ask_seller','0')==2){
			if ( file_exists( JPATH_ROOT.'/components/com_community/libraries/core.php' ) ) {
				$jspath = JPATH_ROOT.DS.'components'.DS.'com_community';
				include_once($jspath.DS.'libraries'.DS.'core.php');
				include_once($jspath.DS.'libraries'.DS.'messaging.php');
				$onclick = CMessaging::getPopup($uid); ?>
				<button id="ask_form_button" class="button" type="button" onclick="<?php echo $onclick; ?>" ><?php echo JText::_('COM_DJCLASSIFIEDS_PROFILE_ASK_SELLER'); ?></button>					
			<?php }else{
				echo 'JoomSocial not installed!';
			} ?> 
		<?php } ?>
		<div id="ask_form" class=" bg-white af_hidden p-4" style="display:none;overflow:hidden;">
									
			<form action="<?php echo JURI::base();?>index.php" method="post" name="djForm" id="djForm" class="form-validate" enctype="multipart/form-data" >
				<?php if($par->get('ask_seller_type','0')==0 || $user->id>0){?>
			   		<label for="ask_name" id="ask_name-lbl"><?php echo JText::_('COM_DJCLASSIFIEDS_YOUR_NAME'); ?></label>
			   		<input type="text" class="inputbox required" value="<?php echo $user->name; ?>" name="ask_name" id="ask_name" />
			   		<label for="ask_email" id="ask_email-lbl"><?php echo JText::_('COM_DJCLASSIFIEDS_YOUR_EMAIL'); ?></label>
			   		<input type="text" class="inputbox required validate-email" value="<?php echo $user->email; ?>" name="ask_email" id="ask_email" />
			   		<?php //echo $this->loadTemplate('askformfields'); ?>
			   		<label for="ask_message" id="ask_message-lbl"><?php echo JText::_('COM_DJCLASSIFIEDS_MESSAGE'); ?></label>
			   		<textarea id="ask_message" name="ask_message" rows="5" cols="55" class="inputbox required"></textarea>
			   		<?php if($par->get('ask_seller_file','0')==1){ ?>
				   		<label for="ask_file" id="ask_file-lbl"><?php echo JText::_('COM_DJCLASSIFIEDS_ATTACHMENT'); ?> <span>(<?php echo $par->get('ask_seller_file_size','2').'MB - '.$par->get('ask_seller_file_types','doc,pdf,zip'); ?>)</span></label>
				   		<input type="file" class="inputbox" value="" name="ask_file" id="ask_file" />	
			   		<?php } ?> 		   			   		
			   	<?php }else{		   						
					?>
					<label for="ask_name" id="ask_name-lbl"><?php echo JText::_('COM_DJCLASSIFIEDS_YOUR_NAME'); ?></label>
			   		<input type="text" class="inputbox required" value="<?php echo $session->get('askform_name',''); ?>" name="ask_name" id="ask_name" />
			   		<label for="ask_email" id="ask_email-lbl"><?php echo JText::_('COM_DJCLASSIFIEDS_YOUR_EMAIL'); ?></label>
			   		<input type="text" class="inputbox required validate-email" value="<?php echo $session->get('askform_email',''); ?>" name="ask_email" id="ask_email" />	   			   		
			   		<?php //echo $this->loadTemplate('askformfields'); ?>
			   		<label for="ask_message" id="ask_message-lbl"><?php echo JText::_('COM_DJCLASSIFIEDS_MESSAGE'); ?></label>
			   		<textarea id="ask_message" name="ask_message" rows="5" cols="55" class="inputbox required"><?php echo $session->get('askform_message',''); ?></textarea>			   		
			   		<?php if($par->get('ask_seller_file','0')==1){ ?>
				   		<label for="ask_file" id="ask_file-lbl"><?php echo JText::_('COM_DJCLASSIFIEDS_ATTACHMENT'); ?> <span>(<?php echo $par->get('ask_seller_file_size','2').'MB - '.$par->get('ask_seller_file_types','doc,pdf,zip'); ?>)</span></label>
				   		<input type="file" class="inputbox" value="" name="ask_file" id="ask_file" />	
			   		<?php } ?>
			   					   					   					   					   		
			   		<script type="text/javascript">
					 	var RecaptchaOptions = {
					    	theme : '<?php echo $par->get('captcha_theme','red'); ?>'
					 	};
					</script>
					<?php	
						if($captcha_type=='recaptcha'){
							if($config->get('force_ssl',0)==2){
								echo recaptcha_get_html($publickey, $error,true);
							}else{
								echo recaptcha_get_html($publickey, $error);
							}						 
						}else if($captcha_type=='nocaptcha'){ ?>
							<div class="g-recaptcha" data-sitekey="<?php echo $publickey; ?>"></div>
				  		<?php }
				   	}?>			
				   	
					<?php if($par->get('terms',1)>0 && $par->get('terms_article_id',0)>0 && $this->terms_link){ ?>
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
			 		
				 	<?php if($par->get('privacy_policy',0)>0 && $par->get('privacy_policy_article_id',0)>0 && $this->privacy_policy_link && $user->id==0){ ?>				
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
			
			 		 	<?php if($par->get('gdpr_agreement',1)>0 && $user->id==0){ ?>				
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
				   	
				   	   		
			   <div class="clear_both"></div>		
			   <input class="button validate btn btn_accent" type="submit" id="submit_b" ><?php echo JText::_('COM_DJCLASSIFIEDS_SEND'); ?></input>
			   <input type="hidden" name="ask_status" id="ask_status" value="0" />
			   <input type="hidden" name="option" value="com_djclassifieds" />
			   <input type="hidden" name="view" value="profile" />
			   <input type="hidden" name="task" value="ask" />
			   <input type="hidden" name="uid" value="<?php echo JRequest::getInt('uid', 0); ?>" />
			   <input type="hidden" name="Itemid" value="<?php echo JRequest::getVar('Itemid',''); ?>" />
			   <div class="clear_both"></div>
			</form> 	 						
		</div>						
		<div class="clear_both"></div>					
	</div>
	<script type="text/javascript">
		window.addEvent('load', function(){	
			<?php 	if($par->get('ask_seller','0')==1){
					if($par->get('ask_seller_type','0')==1 || ($user->id>0 && $par->get('ask_seller_type','0')==0)){ ?>
						if (document.id('ask_form_button') && document.id('ask_form')) {
							document.id('ask_form').setStyle('display','block');
							var ask_form_slide = new Fx.Slide('ask_form');				
							document.id('ask_form_button').addEvent('click', function(e){						
								e.stop();
								ask_form_slide.toggle();						
								return false;
							});				
							ask_form_slide.hide();
						}
					
				<?php }else{?>	
						document.id('ask_form_button').addEvent('click', function(){
							var asr = confirm('<?php echo DJClassifiedsTheme::sanitizePopupText(JText::_('COM_DJCLASSIFIEDS_PLEASE_LOGIN')); ?>');
							if (asr == true) {
								window.location.href = '<?php echo $login_url; ?>';
							}
						});
				<?php }
			} ?>
				
			<?php  if(JRequest::getInt('ae',0)){ ?>
					document.id('ask_form_button').fireEvent('click');	
			<?php	}?>
			
		});										
	</script>
<?php } ?>