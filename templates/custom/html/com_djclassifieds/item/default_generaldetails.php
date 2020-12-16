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
JHTML::_('behavior.framework');
JHTML::_('behavior.formvalidation');
$par = JComponentHelper::getParams( 'com_djclassifieds' );
$app = JFactory::getApplication();
$config = JFactory::getConfig();
$user =  JFactory::getUser();
$document= JFactory::getDocument();
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
$item = $this->item;

if($item->published==2){
	$par->set('ask_seller',0);
}

if(!$user->id){
	$uri = JUri::getInstance();
	$login_url = JUri::getInstance(JRoute::_('index.php?option=com_users&view=login',false));
	$login_url->setVar('return',base64_encode($uri));
}
	
	if(!count($this->item_images) && $par->get('ad_image_default','0')==0 ){ $gd_class=' no_images';
	}else{ $gd_class=''; }
	
	$gd_style = '';
	echo '<div class=""><div class="general_det_in">';

if($this->item->event->beforeDJClassifiedsDisplayContact) { ?>
			<div class="djcf_before_contact">
				<?php echo $this->item->event->beforeDJClassifiedsDisplayContact; ?>
			</div>
		<?php } ?>
		
		<?php					
		if(($par->get('show_contact','1') && $item->contact) || ($par->get('show_website','1') && $item->website) || count($this->fields_contact)){?>
		<div class="row_gd djcf_contact">
			 
			<?php			
			$display_fields = 1;
			if(($par->get('show_contact_only_registered',0)==1 && $user->id==0) || $item->event->onDJClassifiedsAccessContact){
				$display_fields = 0;
			}
			
			if(count($this->fields_contact)>0 && $display_fields){?>
					<?php 
					//echo '<pre>';print_r($this->fields);die(); 
					
					foreach($this->fields_contact as $f){
						if($f->name=='contact'){
							continue;
						}
						if($par->get('show_empty_cf','1')==0){
							if(!$f->value && ($f->value_date=='' || $f->value_date=='0000-00-00')){
								continue;
							}
						}
						if($f->source!=1){continue;}
						$f_value = $f->value;
						$link_url = '';
						if(strstr($f->name, 'tel')){
							$link_url = 'tel:'.$f->value;
						}else if(strstr($f->name, 'whatsapp')){
							$link_url='https://wa.me/'.preg_replace('/\D/', '', $f->value);
						}else if($f->type=='link' && $f->value){
							if(strstr($f->value, 'http://') || strstr($f->value, 'https://')){
								$link_url = $f->value;
								$f_value = str_ireplace(array("http://","https://"), array('',''), $f->value);
							}else if(strstr($f->value, '@')){
								$link_url = 'mailto:'.$f->value;
							}else{
								$link_url = 'http://'.$f->value;
							}																
						}
						?>
						<div class="contact_row row_<?php echo $f->name;?>">
							<span class="row_label"><?php echo JText::_($f->label); ?>:</span>
							<span class="row_value<?php if($f->hide_on_start){echo ' djsvoc" title="'.htmlentities($f_value); }?>" rel="<?php echo $link_url; ?>" >
								<?php 
								if($f->type=='textarea'){							
									if($f->value==''){echo '---'; }
									else{echo $f->value;}								
								}else if($f->type=='checkbox'){
									if($f->value==''){echo '---'; }
									else{
										if($par->get('cf_values_to_labels','0')){
											$ch_values = explode(';', substr($f->value,1,-1));
											foreach($ch_values as $chi=>$chv){
												if($chi>0){ echo ', ';}
												echo JText::_('COM_DJCLASSIFIEDS_'.str_ireplace(array(' ',"'"), array('_',''), strtoupper($chv)));
											}
										}else{
											echo str_ireplace(';', ', ', substr($f->value,1,-1));
										}
									}
								}else if($f->type=='date'){									
									if($f->value_date=='0000-00-00'){echo '---'; }
									else{
										if(!$f->date_format){$f->date_format = 'Y-m-d';}
										echo DJClassifiedsTheme::formatDate(strtotime($f->value_date),'','',$f->date_format);
									}									
								}else if($f->type=='date_from_to'){
									if(!$f->date_format){$f->date_format = 'Y-m-d';}									
									if($f->value_date=='0000-00-00'){echo '---'; }
									else{
										echo DJClassifiedsTheme::formatDate(strtotime($f->value_date),'','',$f->date_format);
									}

									if($f->value_date_to!='0000-00-00'){
										echo '<span class="date_from_to_sep"> - </span>';
										echo DJClassifiedsTheme::formatDate(strtotime($f->value_date_to),'','',$f->date_format);
									}
								}else if($f->type=='link'){
									if($f->value==''){echo '---'; }
									else{
										if($f->hide_on_start){
											echo substr(str_ireplace(array("http://","https://"), array('',''), $f->value), 0,2).'..........<a href="javascript:void(0)" class="djsvoc_link">'.JText::_('COM_DJCLASSIFIEDS_SHOW').'</a>';
										}else{																				
											if(strstr($f->value, 'http://') || strstr($f->value, 'https://')){
												echo '<a '.$f->params.' href="'.$f->value.'">'.str_ireplace(array("http://","https://"), array('',''), $f->value).'</a>';
											}else if(strstr($f->value, '@')){
												echo '<a '.$f->params.' href="mailto:'.$f->value.'">'.$f->value.'</a>';
											}else if(strstr($f->name, 'tel')){
												echo '<a '.$f->params.' href="tel:'.$f->value.'">'.$f->value.'</a>';
											}else if(strstr($f->name, 'whatsapp')){
												echo '<a '.$f->params.' href="https://wa.me/'.preg_replace('/\D/', '', $f->value).'">'.$f->value.'</a>';
											}else{
												echo '<a '.$f->params.' href="http://'.$f->value.'">'.$f->value.'</a>';
											}		
										}														
									}							
								}else{
									if($par->get('cf_values_to_labels','0') && $f->type!='inputbox' && $f->type!='textarea'){
										echo JText::_('COM_DJCLASSIFIEDS_'.str_ireplace(' ', '_', strtoupper($f->value)));
									}else{		
										if($f->hide_on_start){
											echo substr($f->value, 0,2).'..........<a href="javascript:void(0)" class="djsvoc_link">'.JText::_('COM_DJCLASSIFIEDS_SHOW').'</a>';
										}else{
											echo $f->value;										
										}
									}
								}
								?>
							</span>
						</div>		
					<?php
					} ?>
			<?php }?>
						 			 			 
		</div>
		<?php } ?>

<?php
			if($par->get('ask_seller_position',0)==1){
				//general details end
			 	echo '</div></div>'; 	
			 }
						
			if($par->get('ask_seller','0')==1 || ($par->get('ask_seller','0')==2 && $item->user_id>0) || ($par->get('abuse_reporting','0')==1 && $par->get('notify_user_email','')!='')){ ?>
				<div class="ask_form_abuse_outer">
			<?php }			 
			if($par->get('ask_seller','0')==1 && ($item->user_id>0 || $item->email)){?>
				<div id="ask_form_button" class="btn btn_accent"><?php echo JText::_('COM_DJCLASSIFIEDS_ASK_SELLER'); ?></div>
			<?php }else if($par->get('ask_seller','0')==2 && $item->user_id>0){
				if ( file_exists( JPATH_ROOT.'/components/com_community/libraries/core.php' ) ) {
					$jspath = JPATH_ROOT.DS.'components'.DS.'com_community';
					include_once($jspath.DS.'libraries'.DS.'core.php');
					include_once($jspath.DS.'libraries'.DS.'messaging.php');
					$onclick = CMessaging::getPopup($item->user_id); ?>
					<div id="ask_form_button" class="button" onclick="<?php echo $onclick; ?>" ><?php echo JText::_('COM_DJCLASSIFIEDS_ASK_SELLER'); ?></div>
				<?php }else{
					echo 'JoomSocial not installed!';
				} ?> 
				
			<?php }
			if($par->get('abuse_reporting','0')==1){?>
				<div id="abuse_form_button" class="btn btn_accent-black"><?php echo JText::_('COM_DJCLASSIFIEDS_REPORT_ABUSE'); ?></div>
			<?php } ?>
		 <div class="clear_both"></div>
		<?php if($par->get('ask_seller','0')==1 && ($item->user_id>0 || $item->email) && (($par->get('ask_seller_type','0')==1) || ($par->get('ask_seller_type','0')==0 && $user->id>0))){?>
			<div id="ask_form" class="af_hidden bg-white p-3" style="display:none;overflow:hidden;">
			<form action="<?php echo JURI::base();?>index.php" method="post" name="djForm" id="djForm" class="form-validate" enctype="multipart/form-data" >
				<?php if($par->get('ask_seller_type','0')==0 || $user->id>0){?>
			   		<label for="ask_name" id="ask_name-lbl"><?php echo JText::_('COM_DJCLASSIFIEDS_YOUR_NAME'); ?></label>
			   		<input type="text" class="inputbox required" value="<?php echo $user->name; ?>" name="ask_name" id="ask_name" />
			   		<label for="ask_email" id="ask_email-lbl"><?php echo JText::_('COM_DJCLASSIFIEDS_YOUR_EMAIL'); ?></label>
			   		<input type="text" class="inputbox required validate-email" value="<?php echo $user->email; ?>" name="ask_email" id="ask_email" />
			   		<?php echo $this->loadTemplate('askformfields'); ?>
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
			   		<?php echo $this->loadTemplate('askformfields'); ?>
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
			   <button class="button validate" type="submit" id="submit_b" ><?php echo JText::_('COM_DJCLASSIFIEDS_SEND'); ?></button>
			   <input type="hidden" name="ask_status" id="ask_status" value="0" />
			   <input type="hidden" name="item_id" id="item_id" value="<?php echo $item->id; ?>">
			   <input type="hidden" name="cid" id="cid" value="<?php echo $item->cat_id; ?>">
			   <input type="hidden" name="option" value="com_djclassifieds" />
			   <input type="hidden" name="view" value="item" />
			   <input type="hidden" name="task" value="ask" />
			   <div class="clear_both"></div>
			</form> 	 
			</div>
		<?php } ?>

		<?php if($par->get('abuse_reporting','0')==1 && $user->id>0){?>
			<div id="abuse_form" style="display:none;overflow:hidden;">
			<form action="index.php" method="post" name="djabuseForm" id="djabuseForm" class="form-validate bg-white p-3"  enctype="multipart/form-data" >
			   <label for="abuse_message" id="abuse_message-lbl"><?php echo JText::_('COM_DJCLASSIFIEDS_MESSAGE'); ?></label><br />
			   <textarea id="abuse_message" name="abuse_message" rows="5" cols="55" class="inputbox required"></textarea><br />
			   
					<?php if($par->get('terms',1)>0 && $par->get('terms_article_id',0)>0 && $this->terms_link){ ?>
			    		<div class="djform_row terms_and_conditions">
			                <label class="label" >&nbsp;</label>
			                <div class="djform_field">
			                	<fieldset id="aterms_and_conditions" class="checkboxes required">
			                		<input type="checkbox" name="aterms_and_conditions" id="aterms_and_conditions0" value="1" class="inputbox" />                	
									<?php 					 
									echo ' <label class="label_terms" for="aterms_and_conditions" id="aterms_and_conditions-lbl" >'.JText::_('COM_DJCLASSIFIEDS_I_AGREE_TO_THE').' </label>';					
									if($par->get('terms',0)==1){
										echo '<a href="'.$this->terms_link.'" target="_blank">'.JText::_('COM_DJCLASSIFIEDS_TERMS_AND_CONDITIONS').'</a>';
									}else if($par->get('terms',0)==2){
										echo '<a href="'.$this->terms_link.'" rel="{size: {x: 700, y: 500}, handler:\'iframe\'}" class="modal" target="_blank">'.JText::_('COM_DJCLASSIFIEDS_TERMS_AND_CONDITIONS').'</a>';
									}					
									?> *
								</fieldset>
			                </div>
			                <div class="clear_both"></div>
			            </div>
					 <?php } ?>	
			 		
				 	<?php if($par->get('privacy_policy',0)>0 && $par->get('privacy_policy_article_id',0)>0 && $this->privacy_policy_link && $user->id==0){ ?>				
			    		<div class="djform_row terms_and_conditions privacy_policy">
			                <label class="label" >&nbsp;</label>
			                <div class="djform_field">
			                	<fieldset id="aprivacy_policy" class="checkboxes required">
			                		<input type="checkbox" name="aprivacy_policy" id="aprivacy_policy0" value="1" class="inputbox" />                	
									<?php 					 
									echo ' <label class="label_terms" for="aprivacy_policy" id="aprivacy_policy-lbl" >'.JText::_('COM_DJCLASSIFIEDS_I_AGREE_TO_THE').' </label>';					
									if($par->get('terms',0)==1){
										echo '<a href="'.$this->privacy_policy_link.'" target="_blank">'.JText::_('COM_DJCLASSIFIEDS_PRIVACY_POLICY').'</a>';
									}else if($par->get('terms',0)==2){
										echo '<a href="'.$this->privacy_policy_link.'" rel="{size: {x: 700, y: 500}, handler:\'iframe\'}" class="modal" target="_blank">'.JText::_('COM_DJCLASSIFIEDS_PRIVACY_POLICY').'</a>';
									}					
									?> *
								</fieldset>
			                </div>
			                <div class="clear_both"></div>
			            </div>
					 <?php } ?>	
			
			 		 	<?php if($par->get('gdpr_agreement',1)>0 && $user->id==0){ ?>				
			    		<div class="djform_row terms_and_conditions gdpr_agreement">
			                <label class="label" >&nbsp;</label>
			                <div class="djform_field">
			                	<fieldset id="agdpr_agreement" class="checkboxes required">
			                		<input type="checkbox" name="agdpr_agreement" id="agdpr_agreement0" value="1" class="inputbox" />                	
									<?php 					 
									echo ' <label class="label_terms" for="agdpr_agreement" id="agdpr_agreement-lbl" >';
										if($par->get('gdpr_agreement_info','')){
											echo $par->get('gdpr_agreement_info','');
										}else{
											echo JText::_('COM_DJCLASSIFIEDS_GDPR_AGREEMENT_LABEL');
										}												
									echo ' </label>';											
									?> *
								</fieldset>
			                </div>
			                <div class="clear_both"></div>
			            </div>
					 <?php } ?>				   
					 			   
			   <button class="button validate" type="submit" id="submit_b" ><?php echo JText::_('COM_DJCLASSIFIEDS_SEND'); ?></button>
			   <input type="hidden" name="abuse_status" id="abuse_status" value="0" />
			   <input type="hidden" name="item_id" id="item_id" value="<?php echo $item->id; ?>">
			   <input type="hidden" name="cid" id="cid" value="<?php echo $item->cat_id; ?>">
			   <input type="hidden" name="option" value="com_djclassifieds" />
			   <input type="hidden" name="view" value="item" />
			   <input type="hidden" name="task" value="abuse" />
			</form> 	 
			</div>       
		<?php } ?>
		<?php if($par->get('ask_seller','0')==1 || ($par->get('ask_seller','0')==2 && $item->user_id>0) || ($par->get('abuse_reporting','0')==1 && $par->get('notify_user_email','')!='')){ ?>
				</div>
		<?php }	?>
			<div class="clear_both"></div>			
		<?php
			 //general details end
			 if($par->get('ask_seller_position',0)==0){
			 	echo '</div></div>'; 	
			 }
		?>			
		
<script type="text/javascript">

window.addEvent('load', function(){	
	<?php  
		if($par->get('ask_seller','0')==1 && ($item->user_id>0 || $item->email)){
			if($par->get('ask_seller_type','0')==1 || ($user->id>0 && $par->get('ask_seller_type','0')==0)){ ?>
				if (document.id('ask_form_button') && document.id('ask_form')) {
					document.id('ask_form').setStyle('display','block');
					var ask_form_slide = new Fx.Slide('ask_form');				
					document.id('ask_form_button').addEvent('click', function(e){	
						if(typeof formTogglerFx2 !== 'undefined'){
							formTogglerFx2.start('height', 0);		
						}			
						if(typeof e !== 'undefined') e.stop();
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
		  }?>
	
	<?php if($par->get('abuse_reporting','0')==1){
		if($user->id>0){ 
		?>
			if (document.id('abuse_form_button') && document.id('abuse_form')) {
				document.id('abuse_form').setStyle('display','block');
				var formTogglerFx2 = new Fx.Tween('abuse_form' ,{duration: 300});
				var formTogglerHeight2 = document.id('abuse_form').getSize().y;
				
				document.id('abuse_form_button').addEvent('click', function(){
					if(typeof ask_form_slide !== 'undefined'){
						ask_form_slide.slideOut();
					}
					if(document.id('abuse_form').getStyle('height').toInt() > 0){
						formTogglerFx2.start('height',0);
					}else{
						formTogglerFx2.start('height',formTogglerHeight2);
						
					}
				return false;
				});
			
				document.id('abuse_form').setStyle('height',0);
			}
		
	<?php }else{?>
			document.id('abuse_form_button').addEvent('click', function(){
				var arr = confirm('<?php echo DJClassifiedsTheme::sanitizePopupText(JText::_('COM_DJCLASSIFIEDS_PLEASE_LOGIN')); ?>');
				if (arr == true) {
					window.location.href = '<?php echo $login_url; ?>';
				}
			});
	<?php }
		}?>
		
	<?php  if(JRequest::getInt('ae',0)){ ?>
			document.id('ask_form_button').fireEvent('click');	
	<?php	}?>
	
});
</script>		