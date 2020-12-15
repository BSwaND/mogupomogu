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


class plgDJClassifiedsSearchalerts extends JPlugin {
	public function __construct(& $subject, $config) {
		parent::__construct ( $subject, $config );
		$this->loadLanguage ();
	}
	
	
	function onBeforeDJClassifiedsDisplay($items, $par ,$event_view) {
		$db	     = JFactory::getDBO();
		$user    = JFactory::getUser();
		$app     = JFactory::getApplication();
		$se 	 = $app->input->getInt('se',0);
		$cid 	 = $app->input->getInt('cid',0);
		$config  	= JFactory::getConfig();
		
		$menus	= $app->getMenu('site');
		$menu_searchalerts_itemid = $menus->getItems('link','index.php?option=com_djclassifieds&view=searchalerts',1);
		$searchalerts_link='index.php?option=com_djclassifieds&view=searchalerts';
		if($menu_searchalerts_itemid){
			$searchalerts_link .= '&Itemid='.$menu_searchalerts_itemid->id;
		}
		
		$content = '';
		if($event_view=='items' || $event_view=='blog' ||  $event_view=='item'){			
			
			if($se>0 && $this->params->get('sa_display_search',1)>0 && ($this->params->get('sa_list_position',0)==0 || $this->params->get('sa_list_position',0)==2)){				
				$content .= '<div class="save_search_link">';
					$search_url = base64_encode(JFactory::getURI());
					$search_uri = $searchalerts_link.'&task=addSearch&url='.$search_url.'';
					$content .= '<a href="'.$search_uri.'">'.JText::_('COM_DJCLASSIFIEDS_ADD_SEARCH_ALERT').'</a>';
				$content .= '</div>';
			}
			
			
			if(($event_view=='items' || $event_view=='blog') && $cid>0 && $this->params->get('sa_display_category',1)>0 && ($this->params->get('sa_list_position',0)==0 || $this->params->get('sa_list_position',0)==2)){
				$se_results_link = JRoute::_(DJClassifiedsSEO::getCategoryRoute('0:all'));
				if($config->get('sef')){
					$se_results_link .='?se=1&amp;se_cats='.$cid;
				}else{
					$se_results_link .='&se=1&se_cats='.$cid;
				}
				
				$content .= '<div class="save_search_link">';
					$search_url = base64_encode($se_results_link);
					$search_uri = $searchalerts_link.'&task=addSearch&url='.$search_url.'';
					$content .= '<a href="'.$search_uri.'">'.JText::_('COM_DJCLASSIFIEDS_ADD_SEARCH_ALERT').'</a>';
				$content .= '</div>';							
			}
			
			if($event_view=='item' && $cid>0 && $this->params->get('sa_display_advert',1)>0 && ($this->params->get('sa_advert_position',0)==0 || $this->params->get('sa_advert_position',0)==2)){
				$se_results_link = JRoute::_(DJClassifiedsSEO::getCategoryRoute('0:all'));
				if($config->get('sef')){
					$se_results_link .='?se=1&amp;se_cats='.$cid;
				}else{
					$se_results_link .='&se=1&se_cats='.$cid;
				}
			
				$content .= '<div class="save_search_link">';
				$search_url = base64_encode($se_results_link);
				$search_uri = $searchalerts_link.'&task=addSearch&url='.$search_url.'';
				$content .= '<a href="'.$search_uri.'">'.JText::_('COM_DJCLASSIFIEDS_ADD_ADVERT_SEARCH_ALERT').'</a>';
				$content .= '</div>';
			}
		}
		
		return $content;	
	}
	
	function onAfterDJClassifiedsDisplay($items, $par ,$event_view) {
		$db	     = JFactory::getDBO();
		$user    = JFactory::getUser();
		$app     = JFactory::getApplication();
		$se 	 = $app->input->getInt('se',0);
		$cid 	 = $app->input->getInt('cid',0);
		$config  	= JFactory::getConfig();
		
		$menus	= $app->getMenu('site');		
		$menu_searchalerts_itemid = $menus->getItems('link','index.php?option=com_djclassifieds&view=searchalerts',1);
		$searchalerts_link='index.php?option=com_djclassifieds&view=searchalerts';
		if($menu_searchalerts_itemid){
			$searchalerts_link .= '&Itemid='.$menu_searchalerts_itemid->id;
		}
		
	
		$content = '';
		if($event_view=='items' || $event_view=='blog' ||  $event_view=='item'){			
			
			if($se>0 && $this->params->get('sa_display_search',1)>0 && ($this->params->get('sa_list_position',0)==1 || $this->params->get('sa_list_position',0)==2)){				
				$content .= '<div class="save_search_link">';
					$search_url = base64_encode(JFactory::getURI());
					$search_uri = $searchalerts_link.'&task=addSearch&url='.$search_url.'';
					$content .= '<a href="'.$search_uri.'">'.JText::_('COM_DJCLASSIFIEDS_ADD_SEARCH_ALERT').'</a>';
				$content .= '</div>';
			}
			
			
			if(($event_view=='items' || $event_view=='blog') && $cid>0 && $this->params->get('sa_display_category',1)>0 && ($this->params->get('sa_list_position',0)==1 || $this->params->get('sa_list_position',0)==2)){
				$se_results_link = JRoute::_(DJClassifiedsSEO::getCategoryRoute('0:all'));
				if($config->get('sef')){
					$se_results_link .='?se=1&amp;se_cats='.$cid;
				}else{
					$se_results_link .='&se=1&se_cats='.$cid;
				}
				
				$content .= '<div class="save_search_link">';
					$search_url = base64_encode($se_results_link);
					$search_uri = $searchalerts_link.'&task=addSearch&url='.$search_url.'';
					$content .= '<a href="'.$search_uri.'">'.JText::_('COM_DJCLASSIFIEDS_ADD_SEARCH_ALERT').'</a>';
				$content .= '</div>';							
			}
			
			if($event_view=='item' && $cid>0 && $this->params->get('sa_display_advert',1)>0 && ($this->params->get('sa_advert_position',0)==1 || $this->params->get('sa_advert_position',0)==2)){
				$se_results_link = JRoute::_(DJClassifiedsSEO::getCategoryRoute('0:all'));
				if($config->get('sef')){
					$se_results_link .='?se=1&amp;se_cats='.$cid;
				}else{
					$se_results_link .='&se=1&se_cats='.$cid;
				}
			
				$content .= '<div class="save_search_link">';
				$search_url = base64_encode($se_results_link);
				$search_uri = $searchalerts_link.'&task=addSearch&url='.$search_url.'';
				$content .= '<a href="'.$search_uri.'">'.JText::_('COM_DJCLASSIFIEDS_ADD_ADVERT_SEARCH_ALERT').'</a>';
				$content .= '</div>';
			}
		}
		
	
		return $content;
	}
	
	public static function updateSearchAlerts(){
		$app 	= JFactory::getApplication();
		$config = JFactory::getConfig();
		$par 	= JComponentHelper::getParams( 'com_djclassifieds' );
		$db 	= JFactory::getDBO();
	
		JModelLegacy::addIncludePath(JPATH_ROOT.'/components/com_djclassifieds/models/items.php');
		$items_model = JModelLegacy::getInstance('Items','DjclassifiedsModel');
	
		$query = "SELECT s.*, u.name as u_name,u.email as u_email, u.username as u_username "
				."FROM #__djcf_search_alerts s "
				."LEFT JOIN #__users u ON u.id=s.user_id "
						."ORDER BY last_check ASC";
							
						$db->setQuery($query);
						$alerts =$db->loadObjectList();
						
						JRequest::setVar('limit',100);
						JRequest::setVar('order','date_a');
						JRequest::setVar('ord_t','desc');
	
						foreach($alerts as $f){
							//JRequest::setVar($val,$se_q);
							$se_query = json_decode($f->search_query);
	
							foreach($se_query as $val=>$se_q){
								if($val!='option' && $val!='view' && $val!='Itemid'){
									JRequest::setVar($val,$se_q);
								}
							}
	
							$items = $items_model->getItems();
	
							foreach($se_query as $val=>$se_q){
								if($val!='option' && $val!='view' && $val!='Itemid'){
									JRequest::setVar($val,'');
								}
							}
	
							$date_now = date('Y-m-d H:i:s');
							$query_ins = 'INSERT IGNORE INTO #__djcf_search_notifications (`search_id` , `user_id` , `item_id`, `created` ) VALUES ';
							$ins = 0;
							foreach($items as $item){
								if($item->date_start>$f->last_check){
									$query_ins .= "('".$f->id."','".$f->user_id."','".$item->id."','".$date_now."'), ";
									$ins ++;
								}
							}
	
							if($ins){
								$query_ins = substr($query_ins, 0, -2).';';
								$db->setQuery($query_ins);
								$db->query();
							}
	
							$query = "UPDATE #__djcf_search_alerts SET `last_check`='".$date_now."' WHERE id=".$f->id;
							$db->setQuery($query);
							$db->query();
	
							/*echo '<pre>';
							 print_r($db);
							 print_r($items);
							 die();*/
						}
	
						return true;
	}
	
	
}


