<?php
/**
* @version 2.0
* @package DJ Classifieds
* @subpackage DJ Classifieds Component
* @copyright Copyright (C) 2010 DJ-Extensions.com LTD, All rights reserved.
* @license http://www.gnu.org/licenses GNU/GPL
* @author url: http://design-joomla.eu
* @author email contact@design-joomla.eu
* @developer Ĺ�ukasz Ciastek - lukasz.ciastek@design-joomla.eu
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


class plgDJClassifiedsPlans extends JPlugin {
	public function __construct(& $subject, $config) {
		parent::__construct ( $subject, $config );
		$this->loadLanguage ();
	}
	function onAdminPrepareSidebar() {
		$result = array (
				array (
						'label' => JText::_ ( 'COM_DJCLASSIFIEDS_SUBSCRIPTION_PLANS' ),
						'link' => 'index.php?option=com_djclassifieds&view=plans',
						'view' => 'plans' 
				) , 
				array (
						'label' => JText::_ ( 'COM_DJCLASSIFIEDS_USER_SUBSCRIPTION_PLANS' ),
						'link' => 'index.php?option=com_djclassifieds&view=usersplans',
						'view' => 'usersplans'
				)
		);
		return $result;
	}
	
	function onBeforeItemEditForm(& $id, &$par ,$subscr_id, $token) {
		$db	     = JFactory::getDBO();
		$user    = JFactory::getUser();
		$app     = JFactory::getApplication();
		$menus	 = $app->getMenu('site');
		$content = NULL;
		$menus	= $app->getMenu('site');
	
		$menu_userplans_itemid = $menus->getItems('link','index.php?option=com_djclassifieds&view=userplans',1);
		$userplans_link='index.php?option=com_djclassifieds&view=userplans';
		if($menu_userplans_itemid){
			$userplans_link .= '&Itemid='.$menu_userplans_itemid->id;
		}
	
		$menu_subplans_itemid = $menus->getItems('link','index.php?option=com_djclassifieds&view=plans',1);
		$menu_subplans_link='index.php?option=com_djclassifieds&view=plans';
		if($menu_subplans_itemid){
			$menu_subplans_link .= '&Itemid='.$menu_subplans_itemid->id;
		}
	
		if((!$id && $user->id) || $token){
			if($subscr_id==0){
				$date_now = date("Y-m-d H:i:s");
				$query  = "SELECT s.id FROM #__djcf_plans_subscr s, #__djcf_plans p "
						."WHERE p.id=s.plan_id AND s.user_id=".$user->id." AND s.adverts_available>0 AND (date_exp > '".$date_now."' OR date_exp='0000-00-00 00:00:00' ) ";
					
				$db->setQuery($query);
				$user_plans=$db->loadObjectList();
				if(count($user_plans)==0){
					if($this->params->get('ps_plan_required',0)==1){
						$message = JText::_('COM_DJCLASSIFIEDS_PLEASE_BUY_SUBSCRIPTION_PLAN');
						$redirect = JRoute::_($menu_subplans_link,false);
						$app->redirect($redirect, $message);
					}
				}else if(count($user_plans)>0){
					if($this->params->get('ps_plan_autoselect',0)==1){
						$menu_newad_itemid = $menus->getItems('link','index.php?option=com_djclassifieds&view=additem',1);
						$new_ad_link='index.php?option=com_djclassifieds&view=additem';
						if($menu_newad_itemid){
							$new_ad_link .= '&Itemid='.$menu_newad_itemid->id;
						}
						$redirect = JRoute::_($new_ad_link.'&subscr_id='.$user_plans[0]->id, false);
						$app->redirect($redirect);
					}
				}
			}
	
		}
	
		return null;
	
	}
	
	function onItemEditFormTitle(& $item, &$par ,$subscr_id) {
		$db	     = JFactory::getDBO();
		$user    = JFactory::getUser();
		$app     = JFactory::getApplication();
		$menus	 = $app->getMenu('site');
		$content = NULL;
		$menus	= $app->getMenu('site');
		
		$menu_userplans_itemid = $menus->getItems('link','index.php?option=com_djclassifieds&view=userplans',1);
		$userplans_link='index.php?option=com_djclassifieds&view=userplans';
		if($menu_userplans_itemid){
			$userplans_link .= '&Itemid='.$menu_userplans_itemid->id;
		}
		
		$menu_subplans_itemid = $menus->getItems('link','index.php?option=com_djclassifieds&view=plans',1);
		$menu_subplans_link='index.php?option=com_djclassifieds&view=plans';
		if($menu_subplans_itemid){
			$menu_subplans_link .= '&Itemid='.$menu_subplans_itemid->id;
		}		
		
		if(!$item->id && $user->id){
			if($subscr_id>0){		
				$query  = "SELECT s.*, p.name, p.description FROM #__djcf_plans_subscr s, #__djcf_plans p "
						 ."WHERE p.id=s.plan_id AND s.user_id=".$user->id." AND s.id=".$subscr_id;
					
				$db->setQuery($query);
				$plan=$db->loadObject();
				
				if($plan){  
					$content = '<div class="djform_row djform_info_row "><div class="djform_info_row_in alert alert-info">';
					$content .= '<h3>'.JText::_('COM_DJCLASSIFIEDS_NEW_ADVERT_IN_PLAN').' : <b>'.$plan->name.'</b> (<a href="'.$userplans_link.'">'.JText::_('COM_DJCLASSIFIEDS_CHANGE_PLAN').'</a>)</h3>';
					$content .= '<div>'.$plan->description.'</div>'; 
					$content .= '</div></div>';
				}				
			}else{
				$date_now = date("Y-m-d H:i:s");
				$query  = "SELECT s.id FROM #__djcf_plans_subscr s, #__djcf_plans p "
						."WHERE p.id=s.plan_id AND s.user_id=".$user->id." AND s.adverts_available>0 AND (date_exp > '".$date_now."' OR date_exp='0000-00-00 00:00:00' ) ";
					
				$db->setQuery($query);
				$user_plans=$db->loadObjectList();
				if(count($user_plans)==0){
					if($this->params->get('ps_plan_required',0)==1){
						$message = JText::_('COM_DJCLASSIFIEDS_PLEASE_BUY_SUBSCRIPTION_PLAN');
						$redirect = JRoute::_($menu_subplans_link,false);
						$app->redirect($redirect, $message);
					}					
				}else if(count($user_plans)>0){
					if($this->params->get('ps_plan_autoselect',0)==1){
						$menu_newad_itemid = $menus->getItems('link','index.php?option=com_djclassifieds&view=additem',1);
						$new_ad_link='index.php?option=com_djclassifieds&view=additem';
						if($menu_newad_itemid){
							$new_ad_link .= '&Itemid='.$menu_newad_itemid->id;
						}
						$redirect = JRoute::_($new_ad_link.'&subscr_id='.$user_plans[0]->id);
						$app->redirect($redirect);
					}
					$menu_usubplans_itemid = $menus->getItems('link','index.php?option=com_djclassifieds&view=userplans',1);
					$menu_usubplans_link='index.php?option=com_djclassifieds&view=userplans';
					if($menu_usubplans_itemid){
						$menu_usubplans_link .= '&Itemid='.$menu_usubplans_itemid->id;
					}
					$content = '<div class="djform_row djform_info_row "><div class="djform_info_row_in alert alert-info">';
					$content .= '<h3>'.JText::_('COM_DJCLASSIFIEDS_YOU_HAVE_ACTIVE_SUBSCRIPTION_PLAN_PLESE_USE_IT_HERE').' : <b><a href="'.JRoute::_($menu_usubplans_link).'">'.JText::_('COM_DJCLASSIFIEDS_YOUR_SUBSCRIPTION_PLANS').'</a></b></h3>';
					$content .= '</div></div>';
				}
			}						

		}else if($item->id){
			$query = "SELECT i.*, p.plan_params FROM #__djcf_plans_subscr_items i, #__djcf_plans_subscr p "
					."WHERE p.id=i.subscr_id AND i.item_id=".$item->id;
			
			$db->setQuery($query);
			$subscr=$db->loadObject();
			 
			if(isset($subscr->id)){
					$registry = new JRegistry();
					$registry->loadString($subscr->plan_params);
					$plan_params = $registry->toObject();					
					if(!isset($plan_params->types)){
						$plan_params->types = 0;
					}
					$par->set('points',0);
					$par->set('show_video',$plan_params->video);
					$par->set('show_website',$plan_params->website);
					$par->set('buynow',$plan_params->buynow);
					$par->set('offer',$plan_params->offer);
					$par->set('auctions',$plan_params->auction);
					$par->set('show_types',$plan_params->types);
					
					$par->set('img_limit',$plan_params->img_limit);
					$par->set('img_free_limit',-1);
					
					if($plan_params->chars_limit){
						$par->set('pay_desc_chars_limit',$plan_params->chars_limit);
						$par->set('pay_desc_chars_free_limit',$plan_params->chars_limit);							
					}else{
						$par->set('pay_desc_chars',0);
					}
					
					if(isset($plan_params->duration)){
					
						$query = "SELECT * FROM #__djcf_days  "
								."WHERE id=".$plan_params->duration;
					
						$db->setQuery($query);
						$duration=$db->loadObject();
			
						if($duration){
							$par->set('durations_list',0);
							$par->set('exp_days',$duration->days);
						}
					}
										
			}
			
		}
		
		return $content;
		
	}
	
	function onItemEditFormRows($item, $par ,$subscr_id) {
		$input = '<input type="hidden" name="subscr_id" value="'.$subscr_id.'" />';
		return $input;
	}
	
	function onItemEditForm(& $item, &$par ,$subscr_id,&$promotions, &$categories, &$types) {					
		$db	    = JFactory::getDBO();
		$user   = JFactory::getUser();		
		$app    = JFactory::getApplication();
		$menus	= $app->getMenu('site');
		$cfpar 	=  JComponentHelper::getParams( 'com_djclassifieds' );
		
		$menu_userplans_itemid = $menus->getItems('link','index.php?option=com_djclassifieds&view=userplans',1);
		$userplans_link='index.php?option=com_djclassifieds&view=userplans';
		if($menu_userplans_itemid){
			$userplans_link .= '&Itemid='.$menu_userplans_itemid->id;
		}
		
		//echo '<pre>';print_r($categories);print_r($item);die();

		if($subscr_id ){
			$query = "SELECT * FROM #__djcf_plans_subscr p "
					."WHERE user_id=".$user->id." AND id=".$subscr_id;
			
			$db->setQuery($query);
			$plan=$db->loadObject();
			//echo '<pre>';print_r($plan);die();

			if($plan){
				
				if($plan->adverts_available || $item->id>0){
					
					if($plan->date_exp=='0000-00-00 00:00:00' || strtotime($plan->date_exp)>time() || $item->id>0){
						$registry = new JRegistry();
						$registry->loadString($plan->plan_params);
						$plan_params = $registry->toObject();
						if(!isset($plan_params->types)){
							$plan_params->types = 0;
						}
						$par->set('points',0);
						$par->set('show_video',$plan_params->video);
						$par->set('show_website',$plan_params->website);
						$par->set('buynow',$plan_params->buynow);
						$par->set('offer',$plan_params->offer);
						$par->set('auctions',$plan_params->auction);
						$par->set('show_types',$plan_params->types);
						
						$par->set('img_limit',$plan_params->img_limit);
						$par->set('img_free_limit',-1);
						
						if($plan_params->chars_limit){
							$par->set('pay_desc_chars_limit',$plan_params->chars_limit);
							$par->set('pay_desc_chars_free_limit',$plan_params->chars_limit);							
						}else{
							$par->set('pay_desc_chars',0);
						}												
						
						if(isset($plan_params->duration)){
						
							$query = "SELECT * FROM #__djcf_days  "
									."WHERE id=".$plan_params->duration;
						
							$db->setQuery($query);
							$duration=$db->loadObject();
				
							if($duration){
								$par->set('durations_list',0);
								$par->set('exp_days',$duration->days);
							}
						}
						
						if($plan_params->pay_categories){
							for($i=0;$i<count($categories);$i++){
								$categories[$i]->price=0;
								$categories[$i]->points=0;
							}
						}else{
							for($i=0;$i<count($categories);$i++){
								if($categories[$i]->price>0){
									unset($categories[$i]);
								}
							}
						}
						
						$new_promotions = array();
						//$promotions = null;
						if(isset($plan_params->promotions)){
							if(count($plan_params->promotions)){
								foreach ($promotions as $prom){
									if (in_array($prom->id, $plan_params->promotions)){
										$prom->price = 0;
										$prom->points = 0;
										foreach($prom->prices as $pp){
											$pp->price = 0;
											$pp->points = 0;
										}
										$new_promotions[]  = $prom; 
										//$item->promotions .= $prom->name.',';										
										//print_r($prom);die();
									}
								}
							}
						}
						$promotions = $new_promotions;

						if($plan_params->types){
							if($cfpar->get('types_display_layout','0')==1){
								$types = DJClassifiedsType::getTypesLabels(false);
							}else{
								$types = DJClassifiedsType::getTypesSelect(false);
							}
						}
						
						//echo '<pre>';print_r($par);
						//echo '<pre>';print_r($plan_params);
						//echo '<pre>';print_r($promotions);die();
						
						
					}else{
						$message = JText::_('COM_DJCLASSIFIEDS_SUBSCRIPTION_PLAN_NOT_ACTIVE');
						$redirect = JRoute::_($userplans_link,false);
						$app->redirect($redirect, $message);
					}
				}else{
					$message = JText::_('COM_DJCLASSIFIEDS_ADVERTS_LIMIT_FOR_THIS_PLAN_REACHED');
					$redirect = JRoute::_($userplans_link,false);
					$app->redirect($redirect, $message);
				}
			}else{
				$message = JText::_('COM_DJCLASSIFIEDS_WRONG_SUBSCRIPTION_PLAN');
				$redirect = JRoute::_($userplans_link,false);
				$app->redirect($redirect, $message);				
			}
			
			//echo '<pre>';print_r($db);print_r($plan);die();
		}
		
		
		
		return null;
	}
	
	
	function onAfterInitialiseDJClassifiedsSaveAdvert(&$row,&$par){
		$db	    = JFactory::getDBO();
		$user   = JFactory::getUser();
		$app    = JFactory::getApplication();		
		
		$subscr_id = JRequest::getInt('subscr_id',0);
		if($subscr_id>0){
			$query = "SELECT * FROM #__djcf_plans_subscr p "
					."WHERE user_id=".$user->id." AND id=".$subscr_id;
				
			$db->setQuery($query);
			$plan=$db->loadObject();
			//echo '<pre>';print_r($plan);die();
			
			if($plan){
			
				if($plan->adverts_available || $row->id>0 ){
						
					if($plan->date_exp=='0000-00-00 00:00:00' || strtotime($plan->date_exp)>time() || $row->id>0){
						$registry = new JRegistry();
						$registry->loadString($plan->plan_params);
						$plan_params = $registry->toObject();
						if(!isset($plan_params->types)){
							$plan_params->types = 0;
						}
						$par->set('points',0);
						$par->set('show_video',$plan_params->video);
						$par->set('show_website',$plan_params->website);
						$par->set('buynow',$plan_params->buynow);
						$par->set('offer',$plan_params->offer);
						$par->set('auctions',$plan_params->auction);
						$par->set('show_types',$plan_params->types);
			
						$par->set('img_limit',$plan_params->img_limit);
						$par->set('img_free_limit',-1);
			
						if($plan_params->chars_limit){
							$par->set('pay_desc_chars_limit',$plan_params->chars_limit);
							$par->set('pay_desc_chars_free_limit',$plan_params->chars_limit);
						}else{
							$par->set('pay_desc_chars',0);
						}
			
						if(isset($plan_params->duration)){
						
							$query = "SELECT * FROM #__djcf_days  "
									."WHERE id=".$plan_params->duration;
						
							$db->setQuery($query);
							$duration=$db->loadObject();
				
							if($duration){
								$par->set('durations_list',0);
								$par->set('exp_days',$duration->days);
							}
						}								
			
						//echo '<pre>';print_r($par);
						//echo '<pre>';print_r($plan_params);
						//echo '<pre>';print_r($promotions);die();
			
			
					}else{
						$message = JText::_('COM_DJCLASSIFIEDS_SUBSCRIPTION_PLAN_NOT_ACTIVE');
						$redirect = JRoute::_($userplans_link,false);
						$app->redirect($redirect, $message);
					}
				}else{
					$message = JText::_('COM_DJCLASSIFIEDS_ADVERTS_LIMIT_FOR_THIS_PLAN_REACHED');
					$redirect = JRoute::_($userplans_link,false);
					$app->redirect($redirect, $message);
				}
			}else{
				$message = JText::_('COM_DJCLASSIFIEDS_WRONG_SUBSCRIPTION_PLAN');
				$redirect = JRoute::_($userplans_link,false);
				$app->redirect($redirect, $message);
			}
		}
		return null;
	}
	

	function onBeforePaymentsDJClassifiedsSaveAdvert(&$row,$is_new,&$cat, &$promotions,&$type_price){
		$db	    = JFactory::getDBO();
		$user   = JFactory::getUser();
		$app    = JFactory::getApplication(); 
		$subscr_id = JRequest::getInt('subscr_id',0);
		//if(!$row->id && $subscr_id>0){
		if($row->id){
			$query = "SELECT subscr_id FROM #__djcf_plans_subscr_items "
					."WHERE item_id=".$row->id;
			
			$db->setQuery($query);
			$subscr_id=$db->loadResult();
		}
		if($subscr_id){
			$query = "SELECT * FROM #__djcf_plans_subscr p "
					."WHERE user_id=".$user->id." AND id=".$subscr_id;
				
			$db->setQuery($query);
			$plan=$db->loadObject();
			//echo '<pre>';print_r($cat);die();			
			if($plan){			
				if($plan->adverts_available || $row->id){						
					if($plan->date_exp=='0000-00-00 00:00:00' || strtotime($plan->date_exp)>time()){
						$registry = new JRegistry();
						$registry->loadString($plan->plan_params);
						$plan_params = $registry->toObject();
						if($plan_params->pay_categories){						
								$cat->price=0;						
						}
						
						if($plan_params->types){
							$type_price=0;
						}
						
						$new_promotions = array();
						if(count($plan_params->promotions)){
							if(count($plan_params->promotions)){
								foreach ($promotions as $prom){
									if (in_array($prom->id, $plan_params->promotions)){
										$prom->price = 0;
										$prom->points = 0;
										foreach($prom->prices as $pp){
											$pp->price = 0;
											$pp->points = 0;
										}
										$new_promotions[]  = $prom; 
										//$row->promotions .= $prom->name.',';										
										//print_r($prom);die();
									}
								}
							}						
							$promotions = $new_promotions;
						}else{
							$promotions = null;
						}				
						
					}
				}
			}	
				
			
		}
		return NULL;
	}
	
	
	
	function onAfterDJClassifiedsSaveAdvert(&$row,$is_new){
		$db	    = JFactory::getDBO();
		$user   = JFactory::getUser();
		$app    = JFactory::getApplication();
		$subscr_id = JRequest::getInt('subscr_id',0);
		if($subscr_id>0 && $is_new){
			$query = "SELECT * FROM #__djcf_plans_subscr p "
					."WHERE user_id=".$user->id." AND id=".$subscr_id;
		
			$db->setQuery($query);
			$plan=$db->loadObject();
			//echo '<pre>';print_r($cat);die();
			if($plan){
				if($plan->adverts_available){
					if($plan->date_exp=='0000-00-00 00:00:00' || strtotime($plan->date_exp)>time()){
						$query = "INSERT INTO #__djcf_plans_subscr_items (`subscr_id`,`item_id`,`item_name`) " 
								." VALUES ('".$subscr_id."','".$row->id."','".addslashes($row->name)."') ";
						$db->setQuery($query);
						$db->query();
						
						$query = "UPDATE #__djcf_plans_subscr SET adverts_available = adverts_available-1  "
								." WHERE id=".$subscr_id;
						$db->setQuery($query);
						$db->query();
					}
				}
			}
		
				
		}
		return NULL;
	}
	
	
	
	function onPrepareItemDescription(&$item,&$par,$type = 'item'){
		$db	    = JFactory::getDBO();
		$user   = JFactory::getUser();
		$app    = JFactory::getApplication();
	
			$query = "SELECT s.* FROM #__djcf_plans_subscr_items i, #__djcf_plans_subscr s "
					."WHERE s.id=i.subscr_id AND i.item_id=".$item->id." LIMIT 1 ";
	
			$db->setQuery($query);
			$plan=$db->loadObject();
			//echo '<pre>';print_r($plan);die();
					
			if($plan){
				$registry = new JRegistry();
				$registry->loadString($plan->plan_params);
				$plan_params = $registry->toObject();
				if(!isset($plan_params->types)){
					$plan_params->types = 0;
				}
				$par->set('show_video',$plan_params->video);
				$par->set('show_website',$plan_params->website);
				$par->set('buynow',$plan_params->buynow);
				$par->set('offer',$plan_params->offer);
				$par->set('auctions',$plan_params->auction);
				$par->set('show_types',$plan_params->types);
					
				$par->set('img_limit',$plan_params->img_limit);
				$par->set('img_free_limit',-1);
				$par->set('showauthor',$plan_params->user_profile_ad);
				
				if(isset($plan_params->ask_seller)){
					if($plan_params->ask_seller==1){
						if($par->get('ask_seller',0)==0){
							$par->set('ask_seller',1);
						}
					}else{
						$par->set('ask_seller',0);
					}
					
				}
																	
				//echo '<pre>';print_r($par);
				//echo '<pre>';print_r($plan_params);
				//echo '<pre>';print_r($promotions);die();														
			}
	
		
		return null;
	}	
	
	function  onAfterDJClassifiedsCronNotifications(){
			$app 	= JFactory::getApplication();
			$config = JFactory::getConfig();
			$par 	=  JComponentHelper::getParams( 'com_djclassifieds' );
			$db 	= JFactory::getDBO();
			$user   = JFactory::getUser();
			$date_now = date("Y-m-d H:i:s");
						
			$query = "SELECT * FROM #__djcf_plans_subscr s "
					."WHERE s.notify=0 AND (s.adverts_available=0 OR (s.date_exp < '".$date_now."' AND s.date_exp!='0000-00-00 00:00:00' ))";
		
			$db->setQuery($query);
			$subs = $db->loadObjectList();
		
			if($subs){				
				$query = "SELECT * FROM #__djcf_plans p ";				
				$db->setQuery($query);
				$plans =$db->loadObjectList('id');
				
				foreach($subs as $sub){
					
					//echo '<pre>';print_r($sub);
					
					if(isset($plans[$sub->plan_id])){
						$user_group = $plans[$sub->plan_id]->groups_assignment;
						if($user_group){
							
							$query = "SELECT count(s.id) FROM #__djcf_plans p, #__djcf_plans_subscr s "
									."WHERE s.plan_id=p.id AND s.user_id=".$sub->user_id." AND p.groups_assignment=".$user_group." "
									."AND s.adverts_available>0 AND (s.date_exp > '".$date_now."' OR s.date_exp='0000-00-00 00:00:00') ";
							
							$db->setQuery($query);
							$user_subs = $db->loadResult();

							if(!$user_subs){								
								JUserHelper::removeUserFromGroup($sub->user_id, $plans[$sub->plan_id]->groups_assignment);
							}
														
						}
						
					}
					
					$query = "UPDATE `#__djcf_plans_subscr` SET notify=1 WHERE id = ".$sub->id." ";
					$db->setQuery($query);
					$db->query();
					
					//print_R($sub);die();	
				
				}
			}
			return null;
		
	}
	
	
}


