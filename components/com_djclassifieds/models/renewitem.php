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
defined ('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

JTable::addIncludePath(JPATH_COMPONENT.DS.'tables');

class DjclassifiedsModelRenewItem extends JModelLegacy{	

	function getItem()
	{
		$app	= JFactory::getApplication();
		$id 	= JRequest::getVar('id', 0, '', 'int' );	       
        $row 	= JTable::getInstance('Items', 'DJClassifiedsTable');
		if($id>0){						
			$user=JFactory::getUser();			
			$row->load($id);
			
			if($user->id!=$row->user_id || $user->id==0){
				$message = JText::_("COM_DJCLASSIFIEDS_WRONG_AD");
				$redirect= 'index.php?option=com_djclassifieds&view=additem' ;
				$app->redirect($redirect,$message,'error');		
			}
		}
	  	
        return $row;
	}
	

	
	function getCategory($id){
			$db	= JFactory::getDBO();
			$query = "SELECT c.* FROM #__djcf_categories c "
					."WHERE c.id= ".$id." LIMIT 1 ";
	
			$db->setQuery($query);
			$category=$db->loadObject();
	
			return $category;
	}
	

	function getDays($cat_id){
			$db= JFactory::getDBO();
			
			$query = "SELECT d.*, IFNULL(c.cat_c,0) AS cat_c FROM #__djcf_days d "
					."LEFT JOIN (SELECT COUNT(id) as cat_c, day_id FROM #__djcf_days_xref GROUP BY day_id) c ON c.day_id=d.id "
					."WHERE d.published=1 AND (c.cat_c IS NULL OR d.id IN
							(SELECT day_id FROM #__djcf_days_xref WHERE cat_id='".$cat_id."')  )"
					."ORDER BY d.days ";

					$db->setQuery($query);
					$days=$db->loadObjectList('days');			
	
			$db->setQuery($query);
			$days=$db->loadObjectList();
			
	
			return $days;
	}	

	function getPromotions(){
			$db= JFactory::getDBO();
			$query = "SELECT p.*, '' as prices FROM #__djcf_promotions p "
					."WHERE p.published=1 "
					."ORDER BY p.ordering,p.id ";
	
			$db->setQuery($query);
			$promotions=$db->loadObjectList('id');
						
			$query = "SELECT p.* FROM #__djcf_promotions_prices p "
					."ORDER BY p.days ";
			$db->setQuery($query);
			$prom_prices=$db->loadObjectList();		
			
				foreach($prom_prices as $pp){
					if(isset($promotions[$pp->prom_id])){
						if(!is_array($promotions[$pp->prom_id]->prices)){
							$promotions[$pp->prom_id]->prices = array();
						}	
						$promotions[$pp->prom_id]->prices[$pp->days]=$pp;
					}
				}
			
			//echo '<pre>';print_r($promotions);die();			
	
			return $promotions;
	}	
	
	function getItemPromotions($id){
		$promotions = '';
		if($id){
			$db= JFactory::getDBO();
			$query = "SELECT p.* FROM #__djcf_items_promotions p "
					."WHERE item_id=".$id;
	
					$db->setQuery($query);
					$promotions=$db->loadObjectList('prom_id');
					//echo '<pre>';print_r($promotions);die();
		}
		return $promotions;
	}

	function checkCatAdsLimits(){
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$now = date('Y-m-d H:i:s');
		$id = $app->input->getInt('id', '0');

		if(!$user->id || !$id){
			return;
		}

		$query = "SELECT count(*) c, cat_id "
		."FROM ("
			."SELECT id, cat_id, user_id, date_exp FROM #__djcf_items "
			."UNION "
			."SELECT i.id, ic.cat_id, i.user_id, i.date_exp FROM #__djcf_items i INNER JOIN #__djcf_items_categories ic ON i.id=ic.item_id "
		.") v "
		."WHERE user_id=".$user->id." "
		."AND date_exp > ".$db->quote($now)." "
		."GROUP BY cat_id";
		$db->setQuery($query);
		$user_ad_count = $db->loadAssocList('cat_id', 'c');
		//echo '<pre>';print_r($user_ad_count);die();

		if(!$user_ad_count){
			return;
		}

		$query = "SELECT c.* FROM ( "
			."SELECT cat_id FROM #__djcf_items WHERE id=".$id." "
			."UNION "
			."SELECT cat_id FROM #__djcf_items_categories WHERE item_id=".$id." "
		.") v INNER JOIN #__djcf_categories c ON v.cat_id=c.id";
		$db->setQuery($query);
		$cats_to_check = $db->loadObjectList();
		//echo '<pre>';print_r($cats_to_check);die();

		foreach($cats_to_check as $cat){
			if(!empty($cat->ads_limit) && !empty($user_ad_count[$cat->id]) && $user_ad_count[$cat->id] >= $cat->ads_limit){
				$user_ads_link = JRoute::_(DJClassifiedsSEO::getUserAdsLink(), false);
				$app->redirect($user_ads_link, JText::sprintf('COM_DJCLASSIFIEDS_CATEGORY_ADS_LIMIT_REACHED', $cat->name), 'error');
			}
		}
	}
	
}

