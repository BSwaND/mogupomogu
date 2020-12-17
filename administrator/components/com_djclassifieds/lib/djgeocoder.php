<?php
/**
* @version		2.3
* @package		DJ Classifieds
* @subpackage 	DJ Classifieds Component
* @copyright 	Copyright (C) 2010 DJ-Extensions.com LTD, All rights reserved.
* @license 		http://www.gnu.org/licenses GNU/GPL
* @author 		url: http://design-joomla.eu
* @author 		email contact@design-joomla.eu
* @developer 	Łukasz Ciastek - lukasz.ciastek@design-joomla.eu
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
jimport( 'joomla.application.component.controller' );


class DJClassifiedsGeocode {
	
	static private $url = "https://maps.google.com/maps/api/geocode/json";

	public static function getLocation($address)
	{
		$par = JComponentHelper::getParams('com_djclassifieds');
		if($par->get('geolocation', '1') == '0'){
			return false;
		}

    	JPluginHelper::importPlugin( 'djclassifieds' );
		$dispatcher = JDispatcher::getInstance();
		$use_gm = 1;
		$lat_lng = $dispatcher->trigger('onGeocoderGetLocation', array (& $use_gm, & $address));
		$lat_lng = isset($lat_lng[0]) && is_array($lat_lng[0]) ? $lat_lng[0] : $lat_lng;
		
    	if($address==''){
    		return false;
    	}
		
		if($use_gm){		
	        $url = self::$url."?address=".urlencode($address);
	        $params = JComponentHelper::getParams( 'com_djclassifieds' );
	        if($params->get('map_api_key','')){
	        	$url .= '&key='.trim($params->get('map_api_key',''));
	        }
	        
	        $resp_json = self::curl_file_get_contents($url);
	        $resp = json_decode($resp_json, true);                
	
	        if($resp['status']='OK' && isset($resp['results'][0])){
	            $lat_lng = $resp['results'][0]['geometry']['location'];
	        }else{
	        	if(isset($resp['error_message'])){
	        		JFactory::getApplication()->enqueueMessage($resp['error_message'],'error');
	        		return false;
	        	}        	
	        }
		}
		
		return $lat_lng;
    }
    
	public static function getLocationPostCode($post_code, $country='')
	{
		$par = JComponentHelper::getParams('com_djclassifieds');
		if($par->get('geolocation', '1') == '0'){
			return false;
		}

    	JPluginHelper::importPlugin( 'djclassifieds' );
		$dispatcher = JDispatcher::getInstance();
		$use_gm = 1;
		$lat_lng = $dispatcher->trigger('onGeocoderGetLocationPostCode', array (&$use_gm, &$post_code, &$country));
		$lat_lng = isset($lat_lng[0]) && is_array($lat_lng[0]) ? $lat_lng[0] : $lat_lng;
    	    	
		if(!$lat_lng){ // backward compatibility for older map plugins not supporting 'onGeocoderGetLocationPostCode' event
			$lat_lng = self::getLocation($country.', '.$post_code);
		}
		
		if($post_code==''){		    	
    		return false;
    	}
    	
    	if($use_gm){
    		$url_zip = '';
    		if($country){
    			$url_zip = '?address='.urlencode($country);
    			$url = self::$url.$url_zip."&components=postal_code:".urlencode($post_code);
    		}else{
    			$url = self::$url."?components=postal_code:".urlencode($post_code);
    		}
    			
    	
    		$params = JComponentHelper::getParams( 'com_djclassifieds' );
    		if($params->get('map_api_key','')){
    			$url .= '&key='.trim($params->get('map_api_key',''));
    		}
    			
    		$resp_json = self::curl_file_get_contents($url);
    		$resp = json_decode($resp_json, true);
    			
    		if($resp['status']='OK' && isset($resp['results'][0])){
    			return $resp['results'][0]['geometry']['location'];
    		}else{
    			JFactory::getApplication()->enqueueMessage($resp['error_message'],'error');
    			return false;
    		}
    	}
    	
    	return $lat_lng;
    	 
    	
    	
    }
    
    public static function getAddressLatLon($latlng)
    {
        $par = JComponentHelper::getParams('com_djclassifieds');
		if($par->get('geolocation', '1') == '0'){
			return false;
		}
        
    	JPluginHelper::importPlugin( 'djclassifieds' );
		$dispatcher = JDispatcher::getInstance();
		$use_gm = 1;
		$user_address = $dispatcher->trigger('onGeocoderGetAddressLatLon', array (&$use_gm, &$latlng));
		$user_address = isset($user_address[0]) ? $user_address[0] : $user_address;

		if(isset($user_address['message'])){ // error message
			JFactory::getApplication()->enqueueMessage($user_address['message'], 'error');
    		return false;
		}

		if($use_gm){
			$url = self::$url."?latlng=".urlencode($latlng);
			$params = JComponentHelper::getParams( 'com_djclassifieds' );
			if($params->get('map_api_key','')){
				$url .= '&key='.$params->get('map_api_key','');
			}else{
				return false;
			}
			
			$resp_json = self::curl_file_get_contents($url);
			$resp = json_decode($resp_json, true);
		
			//echo '<pre>';print_r($resp);die();	
				
			if($resp['status']='OK' && isset($resp['results'][0])){
				
				$user_address = $resp['results'][0]['formatted_address']; 
				//foreach($resp['results'][0]['address_components'] as $item ){
				
				//}
				
				return $user_address;
			}else{
				JFactory::getApplication()->enqueueMessage($resp['error_message'],'error');
				return false;
			}
		}

		return $user_address;
    }


    static private function curl_file_get_contents($URL){
        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $URL);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($c, CURLOPT_SSL_VERIFYHOST, false);
        
        $contents = curl_exec($c);
        curl_close($c);

        if ($contents) return $contents;
            else return FALSE;
    }


}
