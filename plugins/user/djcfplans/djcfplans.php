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

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.plugin.plugin');
if(!defined("DS")){ define('DS',DIRECTORY_SEPARATOR);}
require_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_djclassifieds'.DS.'lib'.DS.'djnotify.php');

class plgUserdjcfplans extends JPlugin
{

	public function onUserAfterLogin($options){		
		$user = $options['user'];
		
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__djcf_plans_subscr WHERE user_id=".$user->id." ORDER BY id DESC ";
		$db->setQuery($query);
		$user_plans = $db->loadObjectList();
		
		$query = "SELECT * FROM #__djcf_plans";
		$db->setQuery($query);
		$plans = $db->loadObjectList('id');
		
		
		for($i=0;$i<10;$i++){
			$plan_id = $this->params->get('plan_id'.$i, '');
			if($plan_id && isset($plans[$plan_id])){
				if(isset($user->groups[$this->params->get('usergroup'.$i, '')])){
					$plan_found = 0 ;
					$plan_active = 0 ;
					$date_now = date("Y-m-d H:i:s");
					
					foreach($user_plans as $uplan){						
						if($plan_id==$uplan->plan_id){
							$plan_found++;
							if($uplan->adverts_available>0 && ($uplan->date_exp > $date_now || $uplan->date_exp='0000-00-00 00:00:00')){
								$plan_active++;
								break;
							}
						}												
					}
					
					if($plan_active==0){
						if($plan_found==0 || $this->params->get('plan_renew'.$i, '')==1){
							$plan = $plans[$plan_id];
							$registry = new JRegistry();
							$registry->loadString($plan->params);
							$plan_params = $registry->toObject();
								
							//echo '<pre>';print_r($plan_params);die();
							$date_start = date("Y-m-d H:i:s");
							$date_exp = '';
							if($plan_params->days_limit){
								$date_exp_time = time()+$plan_params->days_limit*24*60*60;
								$date_exp = date("Y-m-d H:i:s",$date_exp_time) ;
							}
							$query = "INSERT INTO #__djcf_plans_subscr (`user_id`,`plan_id`,`adverts_limit`,`adverts_available`,`date_start`,`date_exp`,`plan_params`) "
									."VALUES ('".$user->id."','".$plan->id."','".$plan_params->ad_limit."','".$plan_params->ad_limit."','".$date_start."','".$date_exp."','".addslashes($plan->params)."')";
							$db->setQuery($query);
							$db->query();
						}
						
					}
					
				}				
			}
		}
						
		
		return true;
	}
}