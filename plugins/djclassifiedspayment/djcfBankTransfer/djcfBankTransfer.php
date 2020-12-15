<?php
/**
* @version		1.0
* @package		DJ Classifieds
* @subpackage	DJ Classifieds Payment Plugin
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
defined('_JEXEC') or die('Restricted access');
jimport('joomla.event.plugin');
if(!defined("DS")){ define('DS',DIRECTORY_SEPARATOR);}
$lang = JFactory::getLanguage();
$lang->load('plg_djclassifiedspayment_djcfBankTransfer',JPATH_ADMINISTRATOR);
require_once(JPATH_BASE.DS.'administrator'.DS.'components'.DS.'com_djclassifieds'.DS.'lib'.DS.'djnotify.php');
require_once(JPATH_BASE.DS.'administrator'.DS.'components'.DS.'com_djclassifieds'.DS.'lib'.DS.'djpayment.php');


class plgdjclassifiedspaymentdjcfBankTransfer extends JPlugin
{
	function __construct( &$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage('plg_djcfBankTransfer');
		$params["plugin_name"] = "djcfBankTransfer";
		$params["icon"] = "banktransfer_icon.jpg";
		$params["logo"] = "banktransfer_overview.jpg";
		$params["description"] = JText::_("PLG_DJCFBANKTRANSFER_PAYMENT_METHOD_DESC");
		$params["payment_method"] = JText::_("PLG_DJCFBANKTRANSFER_PAYMENT_METHOD_NAME");		
		$params["pay_info"] = $this->params->get("pay_info");
		$this->params = $params;

	}
	
	function onPaymentMethodList($val)
	{
		if($val["direct_payment"]){
			return null;
		}
		$type='';
		if($val['type']){
			$type='&type='.$val['type'];	
		}	
		$html ='';
		$user = JFactory::getUser();
		
		if($this->params["pay_info"]!=''){
			
		$paymentLogoPath = JURI::root()."plugins/djclassifiedspayment/".$this->params["plugin_name"]."/".$this->params["plugin_name"]."/images/".$this->params["logo"];
			//$form_action = JRoute :: _("index.php?option=com_djclassifieds&task=processPayment&ptype=".$this->params["plugin_name"]."&pactiontype=process&id=".$val["id"].$type, false);
			$form_action = JURI::root()."index.php?option=com_djclassifieds&task=processPayment&ptype=".$this->params["plugin_name"]."&pactiontype=process&id=".$val["id"].$type;
			$html ='<table cellpadding="5" cellspacing="0" width="100%" border="0">
				<tr>';
					if($this->params["logo"] != ""){
				$html .='<td class="td1" width="160" align="center">
						<img src="'.$paymentLogoPath.'" title="'.$this->params["payment_method"].'"/>
					</td>';
					 }
					$html .='<td class="td2">
						<h2>'. $this->params["payment_method"].'</h2>
						<p style="text-align:justify;">'.$this->params["description"].'</p>
					</td>
					<td class="td3" width="130" align="center">
						<a class="button" style="text-decoration:none;" href="'.$form_action.'">'.JText::_('COM_DJCLASSIFIEDS_BUY_NOW').'</a>
					</td>
				</tr>
			</table>';		
			
		}
		return $html;
	}	
	
	function onProcessPayment()
	{
		$ptype = JRequest::getVar('ptype','');
		$id = JRequest::getInt('id','0');
		$html="";
					
		
			
		if($ptype == $this->params["plugin_name"])
		{
			$action = JRequest::getVar('pactiontype','');
			switch ($action)
			{
				case "process" :
				$html = $this->process($id);
				break;
				case "notify" :
				$html = $this->_notify_url();
				break;
				case "paymentmessage" :
				$html = $this->_paymentsuccess();
				break;
				default :
				$html =  $this->process($id);
				break;
			}
		}
		return $html;
	}


	
	function process($id)
	{
		$db 	= JFactory::getDBO();
		$app 	= JFactory::getApplication();
		$Itemid = JRequest::getInt("Itemid",'0');
		$par 	= JComponentHelper::getParams( 'com_djclassifieds' );
		$user 	= JFactory::getUser();
		$config = JFactory::getConfig();
		$ptype	= JRequest::getVar('ptype');
		$type	= JRequest::getVar('type','');
		$row 	= JTable::getInstance('Payments', 'DJClassifiedsTable');

		$pdetails = DJClassifiedsPayment::processPayment($id, $type,$ptype);

		$payment_info = array();
		$payment_info['payment_id'] = $pdetails['item_id'];
		$payment_info['itemname'] = $pdetails['itemname'];
		$payment_info['amount'] = DJClassifiedsTheme::priceFormat($pdetails['amount'],$par->get('unit_price',''));
		$payment_info['info'] = $this->params["pay_info"];
		
		DJClassifiedsNotify::notifyUserPayment($type,$id,$payment_info);
		
		// keeping the payment info in session and redirecting to the bank tranfer info payment page
		$payment_info['type'] = $type;
		$payment_info['item_id'] = $id;
		$session = JFactory::getSession();
		$session->set('payment_info_bt', $payment_info);
		$app->redirect(JRoute::_('index.php?option=com_djclassifieds&view=payment&layout=info_bt', false));
	
		/*
			echo '<div id="dj-classifieds" class="clearfix">';
				echo '<table width="98%" cellspacing="0" cellpadding="0" border="0" class="paymentdetails first">';
				echo '<tr><td class="td_title"><h2>'.$this->params["payment_method"].'</h2></td></tr>';
					echo '<tr><td class="td_pdetails">';
						echo '<div class="pd_row">';
							if($type=='points'){
								echo '<span class="djcfpay_label">'.JText::_('PLG_DJCFBANKTRANSFER_POINTS_PACKAGE').':</span>';
							}else{
								echo '<span class="djcfpay_label">'.JText::_('PLG_DJCFBANKTRANSFER_PAYMENT_AD_NAME').':</span>';
							}
							echo '<span class="djcfpay_value">'.$pdetails['itemname'].'</span>';
						echo '</div>';
						echo '<div class="pd_row">';
							echo '<span class="djcfpay_label">'.JText::_('PLG_DJCFBANKTRANSFER_PAYMENT_PRICE_TO_PAY').':</span>';
							echo '<span class="djcfpay_value">'.DJClassifiedsTheme::priceFormat($pdetails['amount'],$par->get('unit_price','')).'</span>';
						echo '</div>';
						echo '<div class="pd_row">';
							if($type=='points'){
								echo '<span class="djcfpay_label">'.JText::_('PLG_DJCFBANKTRANSFER_PAYMENT_POINTS_ID').':</span>';
							}else{								
								echo '<span class="djcfpay_label">'.JText::_('PLG_DJCFBANKTRANSFER_PAYMENT_AD_ID').':</span>';
							}							
							echo '<span class="djcfpay_value">'.$id.'</span>';
						echo '</div>';						
						echo '<div class="pd_row">';
							echo '<span class="djcfpay_label">'.JText::_('PLG_DJCFBANKTRANSFER_PAYMENT_ID').':</span>';							
							echo '<span class="djcfpay_value">'.$pdetails['item_id'].'</span>';
						echo '</div>';						
						echo '<div class="pd_row">';
							echo '<span class="djcfpay_label">'.JText::_('PLG_DJCFBANKTRANSFER_PAYMENT_PAY_INFORMATION').': </span><br /><br />';
							echo '<span class="djcfpay_value">'.JHTML::_('content.prepare',nl2br($this->params["pay_info"])).'</span>';							
						echo '</div>';	
					echo '</td></tr>';							
				echo '</table>';
			echo '</div>';
		*/
	}
}

?>