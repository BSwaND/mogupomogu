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
$lang = JFactory::getLanguage();
$lang->load('plg_djclassifiedspayment_djcfAuthorizeNET',JPATH_ADMINISTRATOR);
require_once(JPATH_BASE.DS.'administrator/components/com_djclassifieds/lib/djnotify.php');
require JPATH_BASE . '/plugins/djclassifiedspayment/djcfAuthorizeNET/djcfAuthorizeNET/anet_php_sdk_v2/autoload.php';
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

class plgdjclassifiedspaymentdjcfAuthorizeNET extends JPlugin
{
	function __construct( &$subject, $config)
	{
		parent::__construct($subject, $config);
	
		$this->loadLanguage('plg_djcfAuthorizeNET');
		$params["plugin_name"] = "djcfAuthorizeNET";
		$params["icon"] = "";
		$params["logo"] = "authorized.jpg";
		$params["description"] = JText::_("PLG_DJCFAUTHORIZENET_PAYMENT_METHOD_DESC");
		$params["payment_method"] = JText::_("PLG_DJCFAUTHORIZENET_PAYMENT_METHOD_NAME");

		$params["login_id"] = $this->params->get("login_id");
		$params["transaction_key"] = $this->params->get("transaction_key");
		$params["currency_code"] = $this->params->get('currency_code', "USD");
		$params["account_type"] = $this->params->get('account_type', "test");
		$this->params = $params;

	}
	
	function onProcessPayment()
	{
		$ptype = JRequest::getVar('ptype','');
		$id = JRequest::getInt('id','');
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

	function _notify_url()
	{		
		$db = JFactory::getDBO();
		$app = JFactory::getApplication();
		$account_type=$this->params["account_type"];
		$Itemid = JRequest::getInt("Itemid",'0');
		$currency = $this->params["currency_code"];
		$user	= JFactory::getUser();
		$id	= JRequest::getInt('id','0');
		$ptype=JRequest::getVar('ptype');
		$par = JComponentHelper::getParams( 'com_djclassifieds' );
		$type=JRequest::getVar('type','');
		
		$msg_style = 'Warning';
		$message = JText::_('PLG_DJCFAUTHORIZENET_AFTER_FAILED_MSG');
		$redirect = 'index.php?option=com_djclassifieds&view=payment&id='.$id.'&type='.$type.'&Itemid='.$Itemid;

		$pdetails = DJClassifiedsPayment::processPayment($id, $type, $ptype);

		$query = "SELECT p.*  FROM #__djcf_payments p "
        		."WHERE p.id='".$pdetails['item_id']."' ";
        $db->setQuery($query);
        $payment = $db->loadObject();
	
		$login_id = $this->params["login_id"];
		$transaction_key = $this->params["transaction_key"];
		$card_no = JRequest::getVar('card_no');

		$card_num    = JRequest::getVar('card_no','0','','string');
		$exp_month = JRequest::getVar('exp_date','01','','string');
		$exp_year    = JRequest::getVar('exp_year','2019','','string');
		$exp_date = $exp_year."-".$exp_month;
		$cvv = JRequest::getVar('card_code','0','','int');

		if($card_num==0 || $cvv=='0'){
			$message = JText::_('COM_DJCLASSIFIEDS_ENTER_ALL_VALUES');
			$redirect= 'index.php?option=com_djclassifieds&view=payment&id='.$id.'&type='.$type.'&Itemid='.$Itemid;			
			$app->redirect($redirect, $message, 'Error');
		}

		//define("AUTHORIZENET_LOG_FILE", "phplog");
		$merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
		$merchantAuthentication->setName($login_id);
		$merchantAuthentication->setTransactionKey($transaction_key);
		// Create the payment data for a credit card
		$creditCard = new AnetAPI\CreditCardType();
		$creditCard->setCardNumber($card_num);

		$creditCard->setExpirationDate($exp_date);
		$creditCard->setCardCode($cvv);
		
		// Add the payment data to a paymentType object
		$paymentOne = new AnetAPI\PaymentType();
		$paymentOne->setCreditCard($creditCard);
		
		$transactionRequestType = new AnetAPI\TransactionRequestType();
		$transactionRequestType->setTransactionType("authCaptureTransaction");
		//$transactionRequestType->setAmount("5.99");
		$transactionRequestType->setAmount($pdetails['amount']);
		
		$transactionRequestType->setPayment($paymentOne);
		
		// Assemble the complete transaction request
		$request = new AnetAPI\CreateTransactionRequest();
		$request->setMerchantAuthentication($merchantAuthentication);
		$request->setTransactionRequest($transactionRequestType);
		
		// Create the controller and get the response
		$controller = new AnetController\CreateTransactionController($request);

		if($account_type=='secure'){
			$response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);
		}else{
			$response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
		}

		if ($response != null) {
		// Check to see if the API request was successfully received and acted upon
			if ($response->getMessages()->getResultCode() == "Ok") {
				
			// Since the API request was successful, look for a transaction response
			// and parse it to display the results of authorizing the card
				$tresponse = $response->getTransactionResponse();

				if ($tresponse != null && $tresponse->getMessages() != null) {
					if($tresponse->getResponseCode() == '1'){
						$trans_id = $tresponse->getTransId();
						DJClassifiedsPayment::completePayment($pdetails['item_id'], $pdetails['amount'], $trans_id);		
			
						$message = JText::_('COM_DJCLASSIFIEDS_THANKS_FOR_PAYMENT_WAIT_FOR_CONFIRMATION');
						$redirect = 'index.php?option=com_djclassifieds&view=items&cid=0&Itemid='.$Itemid;
						$msg_style = '';
					}
				}
			}else{
				$tresponse = $response->getTransactionResponse();

				if($tresponse){
                    $errors = $tresponse->getErrors();
                    if(isset($errors[0])){
                        $message = $errors[0]->getErrorText();
                        $msg_style = 'Error';
                    }
                }
			}

		} 

		$app->redirect($redirect, $message, $msg_style);
	}

	function onPaymentMethodList($val)
	{
		if($val["direct_payment"]){
			return null;
		}
		$html='';
		$login_id = $this->params["login_id"];
		$transaction_key = $this->params["transaction_key"];
		
		if($login_id!='' && $transaction_key!=''){
		$user	= JFactory::getUser();
		$Itemid = JRequest::getInt("Itemid",'0');
		$paymentLogoPath = JURI::root()."plugins/djclassifiedspayment/".$this->params["plugin_name"]."/".$this->params["plugin_name"]."/images/".$this->params["logo"];

		$action_url = JURI::root().'index.php?option=com_djclassifieds&task=processPayment&ptype='.$this->params["plugin_name"].'&pactiontype=notify&Itemid='.$Itemid.'&id='.$val["id"];
		$ADN_form = "<script language='javascript'>function adotnetSubmitForm(){if(document.addtocart.card_no.value==''){alert('Credit Card Number Field is Empty!');return;}else if(document.addtocart.card_code.value==''){alert('Credit Card Security Code Field is Empty!');return;}else{document.addtocart.submit(); } }</script><form name='addtocart' action='".$action_url."' method='post'>
		<table align='left'>
		<td><b>".JText::_('PLG_DJCFAUTHORIZENET_CREDIT_CART_PAYMENT').":</b></td>
		<tr>
		<td>".JText::_('PLG_DJCFAUTHORIZENET_CREDIT_CARD_NUMBER').": </td>
		<td><input type='text' name='card_no' /></td>
		</tr>

		<tr>
		<td>".JText::_('PLG_DJCFAUTHORIZENET_CREDIT_CARD_SECURITY_CODE').": </td>
		<td><input type='text' name='card_code' /></td>
		</tr>

		<tr>
		<td>".JText::_('PLG_DJCFAUTHORIZENET_EXPIRATION_DATE').": </td>
		<td><select name='exp_date' id='exp_date'>
		                  <option value='01'>".JText::_('JANUARY')."</option>
						  <option value='02'>".JText::_('FEBRUARY')."</option>
						  <option value='03'>".JText::_('MARCH')."</option>
						  <option value='04'>".JText::_('APRIL')."</option>
						  <option value='05'>".JText::_('MAY')."</option>
						  <option value='06'>".JText::_('JUNE')."</option>
						  <option value='07'>".JText::_('JULY')."</option>
		                  <option value='08'>".JText::_('AUGUST')."</option>
						  <option value='09'>".JText::_('SEPTEMBER')."</option>
						  <option value='10'>".JText::_('OCTOBER')."</option>
						  <option value='11'>".JText::_('NOVEMBER')."</option>
						  <option value='12'>".JText::_('DECEMBER')."</option>
		                  </select>
						  <select name='exp_year' id='exp_year'>";
						  for($i=date("Y");$i<date("Y")+10;$i++){
							$ADN_form .= "<option value='".$i."'>".$i."</option>";
						  }
						  $ADN_form .= "</select></td>
						  </tr>
						  <tr><td></td><td>";

						$ADN_form .= "<input type='hidden' name='option' value='com_djclassifieds' /><input type='hidden' name='task' value='processPayment' /><input type='hidden' name='ptype' value='".$this->params["plugin_name"]."' /><input type='hidden' name='pactiontype' value='notify' /><input type='hidden' name='type' value='".JRequest::getCmd('type','')."' />";
						$ADN_form .= "</td></tr> </table>

		</form>";
		

		$html ='<table cellpadding="5" cellspacing="0" width="100%" border="0">
			<tr>';
				if($this->params["logo"] != ""){
			$html .='<td class="td1" width="160" align="center">
					<img src="'.$paymentLogoPath.'" title="'. $this->params["payment_method"].'"/>
				</td>';
				 }
				$html .='<td class="td2">
					<h2>Authorize.net</h2>
					<p style="text-align:justify;">'.$this->params["description"]."<br/>".$ADN_form.'</p>
				</td>
				<td class="td3" width="130" align="center">
					<a class="button"  style="text-decoration:none;" onclick="adotnetSubmitForm();" href="javascript:void(0);">'.JText::_('COM_DJCLASSIFIEDS_BUY_NOW').'</a>
				</td>
			</tr>
		</table>';
		}
		return $html;
	}


	
}

?>