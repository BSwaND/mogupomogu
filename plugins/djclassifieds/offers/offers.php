<?php
/**
* @version 2.0
* @package DJ Classifieds
* @subpackage DJ Classifieds Component
* @copyright Copyright (C) 2010 DJ-Extensions.com LTD, All rights reserved.
* @license http://www.gnu.org/licenses GNU/GPL
* @author url: http://design-joomla.eu
* @author email contact@design-joomla.eu
* @developer Łukasz Ciastek - lukasz.ciastek@design-joomla.eu
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
defined ( '_JEXEC' ) or die ( 'Restricted access' );

jimport ( 'joomla.plugin.plugin' );
jimport ( 'joomla.utilities.utility' );
if(!defined("DS")){ define('DS',DIRECTORY_SEPARATOR);}
require_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_djclassifieds'.DS.'lib'.DS.'djtheme.php');


class plgDJClassifiedsOffers extends JPlugin {
	public function __construct(& $subject, $config) {
		parent::__construct ( $subject, $config );
		$this->loadLanguage ();
	}
	
	

	function onAdminPrepareSidebar() {
		$result = array (
				array (
						'label' => JText::_ ( 'COM_DJCLASSIFIEDS_OFFERS' ),
						'link' => 'index.php?option=com_djclassifieds&view=offers',
						'view' => 'offers'
				)
		);
		return $result;
	}

	
	function onAdminItemEditTabTitle($item) {
		$tab_title = '<li><a href="#offers" data-toggle="tab">'.JText::_('COM_DJCLASSIFIEDS_OFFERS').'</a></li>';		
		return $tab_title;
	}
	function onAdminItemEditTabContent($item) {
		
		$tab_content = '<div class="tab-pane" id="offers">';
			$tab_content .= '<div class="control-group">';
				$tab_content .= '<div class="control-label">'.JText::_('COM_DJCLASSIFIEDS_OFFERING_ACTIVE').'</div>';
				$tab_content .= '<div class="controls">';
					if($item->offer==1 && $item->id>0){$checked = "checked";}else{$checked = '';}
					$tab_content .= '<input autocomplete="off" type="radio" name="offer" value="1" '.$checked.' /><span style="float:left; margin:2px 15px 0 5px;">'.JText::_('COM_DJCLASSIFIEDS_YES').'</span>';					
					if($item->offer==0 || $item->id==0){$checked = "checked";}else{$checked = '';}
					$tab_content .= '<input autocomplete="off" type="radio" name="offer" value="0" '.$checked.' /><span style="float:left; margin:2px 15px 0 5px;">'.JText::_('COM_DJCLASSIFIEDS_NO').'</span>';									
				$tab_content .= '</div>';
			$tab_content .= '</div>';
		$tab_content .= '</div>';		
		
		return $tab_content;
	}
		
	function onItemEditFormRows($item, $par ,$subscr_id) {
		$content = null;
		if($par->get('offer','1')){
			$content ='<div class="djform_row">';
		         if($par->get('show_tooltips_newad','0')){
		           	$content .= '<label class="label Tips1" id="offer-lbl" for="offer" title="'.JTEXT::_("COM_DJCLASSIFIEDS_OFFERING_ACTIVE_TOOLTIP").'">';
		               $content .= JText::_('COM_DJCLASSIFIEDS_OFFERING_ACTIVE');
		               $content .= ' <img src="'.JURI::base(true).'/components/com_djclassifieds/assets/images/tip.png" alt="?" />';
		            $content .='</label>';	                               			                	
				 }else{
		           	$content .='<label class="label" id="offer" for="offer">';
		               	$content .=JText::_('COM_DJCLASSIFIEDS_OFFERING_ACTIVE'); 					
			        $content .='</label>';
		         }
		        
		        $selected = ''; 
		        if($item->offer){
		        	$selected = 'SELECTED'; 
		        }	
		        $content .= '<div class="djform_field">';
		        	$content .='<select id="offer" name="offer" autocomplete="off" >';
						$content .='<option value="0">'.JText::_('JNO').'</option>';
						$content .=' <option value="1" '.$selected.' >'.JText::_('JYES').'</option>';
					$content .='</select>';
		        $content .='</div>';
		        $content .='<div class="clear_both"></div>';
		     $content .='</div>';									
		}
		return $content;
	}
	
	
	/*function onBeforeDJClassifiedsDisplayContact($item, $par ,$subscr_id) {
		$user = JFactory::getUser();
		$content = '';
		if($item->offer){ 
			$content .='<div class="offer_box">';				
				if($user->id>0){	
					$content .=JText::_('COM_DJCLASSIFIEDS_MAKE_YOUR_OFFER');
					$content .='<form action="index.php" method="post" name="djForm" id="djForm" class="form-validate" enctype="multipart/form-data" >';
						if($item->quantity>1){
							$content .='<input type="text" class="buynow_quantity required validate-numeric" name="offer_quantity" id="offer_quantity" value="1" />';
							$content .= ' '.$item->unit_name.' '.JText::_('COM_DJCLASSIFIEDS_FOR').' ';
						}
						
						if($item->currency){
							$content .=$item->currency;
						}else{
							$content .= $par->get('unit_price','EUR');	
						} 
						
						$content .='<input type="text" class="buynow_quantity required validate-numeric" name="offer_price" id="offer_price" value="'.$item->price.'" />';
						$content .='<textarea name="offer_msg" id="offer_msg" placeholder="'.JText::_('COM_DJCLASSIFIEDS_MESSAGE_FOR_AUTHOR').'"></textarea>';
						$content .='<button class="button validate" type="submit" id="submit_b" >'.JText::_('COM_DJCLASSIFIEDS_POST_OFFER').'</button>';			    
					    $content .='<input type="hidden" name="item_id" id="item_id" value="'.$item->id.'">';
					    $content .='<input type="hidden" name="cid" id="cid" value="'.$item->cat_id.'">';
					    $content .='<input type="hidden" name="option" value="com_djclassifieds" />';
					    $content .='<input type="hidden" name="view" value="checkout" />';
					    $content .='<input type="hidden" name="task" value="saveOffer" />';
					   $content .='<div class="clear_both"></div>';
					$content .='</form>';	
				}else{					
					$uri = JFactory::getURI(); 
					$content .='<div class="bids_login_info"><a href="'.JRoute::_('index.php?option=com_users&view=login&return='.base64_encode($uri)).'">';
						$content .=	JText::_('COM_DJCLASSIFIEDS_LOGIN');
					$content .='</a> '.JText::_('COM_DJCLASSIFIEDS_TO_MAKE_OFFER').'</div>';
				} 
			$content.= '</div>';
		} 
		
		return $content;
	}*/
	
	
	function onBeforeDJClassifiedsDisplayContact($item, $par ,$subscr_id) {
		$user = JFactory::getUser();
		$content = '';
		if($item->offer){
			$content .= '<div id="offer_outer" class="offer_outer">';
				$content .= '<button id="offer_button" class="button" type="button" >'.JText::_('COM_DJCLASSIFIEDS_MAKE_OFFER_BUTTON').'</button>';
				$content .='<div class="clear_both"></div>';
				$content .='<div class="offer_box" id="offer_box" style="display:none;">';
				if($user->id>0){
					$content .='<div class="offer_title">'.JText::_('COM_DJCLASSIFIEDS_MAKE_YOUR_OFFER').'</div>';
					$content .='<form action="index.php" method="post" name="offerForm" id="offerForm" class="form-validate" enctype="multipart/form-data" >';
					if($item->quantity>1){
						$content .='<div class="offer_quantity_line"><input type="text" class="buynow_quantity required validate-numeric inputbox" name="offer_quantity" id="offer_quantity" value="1" />';
						$content .= '<span class="offer_unit">'.$item->unit_name.' '.JText::_('COM_DJCLASSIFIEDS_FOR').'</span></div>';
					}
			
					$content .='<div class="offer_price_line">';
						$content .='<span class="offer_currency">';
						if($item->currency){
							$content .=$item->currency;
						}else{
							$content .= $par->get('unit_price','EUR');
						}
						$content .='</span>';
			
					$content .='<input type="text" class="buynow_quantity required validate-numeric inputbox" name="offer_price" id="offer_price" value="'.$item->price.'" /></div>';
					$content .='<textarea name="offer_msg" class="inputbox" id="offer_msg" placeholder="'.JText::_('COM_DJCLASSIFIEDS_MESSAGE_FOR_AUTHOR').'"></textarea>';
					$content .='<button class="button validate" type="submit" id="submit_b" >'.JText::_('COM_DJCLASSIFIEDS_POST_OFFER').'</button>';
					$content .='<input type="hidden" name="item_id" id="item_id" value="'.$item->id.'">';
					$content .='<input type="hidden" name="cid" id="cid" value="'.$item->cat_id.'">';
					$content .='<input type="hidden" name="option" value="com_djclassifieds" />';
					$content .='<input type="hidden" name="view" value="checkout" />';
					$content .='<input type="hidden" name="task" value="saveOffer" />';
					$content .='<div class="clear_both"></div>';
					$content .='</form>';
				}else{
					$uri = JFactory::getURI();
					$login_url = JRoute::_('index.php?option=com_users&view=login&return='.base64_encode($uri));
					$content .='<div class="bids_login_info"><a href="'.$login_url.'">';
					$content .=	JText::_('COM_DJCLASSIFIEDS_LOGIN');
					$content .='</a> '.JText::_('COM_DJCLASSIFIEDS_TO_MAKE_OFFER').'</div>';
				}
				$content.= '</div>';
		
				$content.= "
					<script>
						window.addEvent('load', function(){
							if (document.id('offer_button') && document.id('offer_box')){
								document.id('offer_box').setStyle('display','block');
								var offer_box_slide = new Fx.Slide('offer_box',{resetHeight:true});
								document.id('offer_button').addEvent('click', function(e){
									e.stop();
									offer_box_slide.toggle();
									//this.toggleClass('active');
									document.id('offer_outer').toggleClass('offer_open');
												
									return false;
								});
								offer_box_slide.hide();
							}
						});
					</script>
				";
			$content .= '</div>';				
		}
	
		return $content;
	}
	
	
}


