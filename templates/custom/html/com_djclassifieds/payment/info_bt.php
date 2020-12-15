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
defined('_JEXEC') or die;

$pdetails = $this->pdetails;
$id = $pdetails['item_id'];
$type = $pdetails['type'];

echo '<div id="dj-classifieds" class="clearfix">';
echo '<table width="98%" cellspacing="0" cellpadding="0" border="0" class="paymentdetails first">';
echo '<tr><td class="td_title"><h2>'.JText::_("PLG_DJCFBANKTRANSFER_PAYMENT_METHOD_NAME").'</h2></td></tr>';
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
            echo '<span class="djcfpay_value">'.$pdetails['amount'].'</span>';
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
            echo '<span class="djcfpay_value">'.$pdetails['payment_id'].'</span>';
        echo '</div>';						
        echo '<div class="pd_row">';
            echo '<span class="djcfpay_label">'.JText::_('PLG_DJCFBANKTRANSFER_PAYMENT_PAY_INFORMATION').': </span><br /><br />';
            echo '<span class="djcfpay_value">'.JHTML::_('content.prepare',nl2br($pdetails['info'])).'</span>';							
        echo '</div>';	
    echo '</td></tr>';							
echo '</table>';
echo '</div>';

?>
