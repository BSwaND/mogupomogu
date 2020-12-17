<?php
/**
* @version		2.0
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
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_djclassifieds'.DS.'lib'.DS.'persiancalendar.php');


class DJClassifiedsTheme {
	
	function __construct(){
	}

	public static function priceFormat($price,$unit='',$price_decimals = ''){
		$app = JFactory::getApplication();
        $par = JComponentHelper::getParams( 'com_djclassifieds' );				
		$price_decimal_separator = null;
		$price_thousands_separator = null;
		if(!$price_decimals){
			$price_decimals = $par->get('price_decimals',2);
		}		
		
		if($unit){
			$unit = '<span class=\'price_unit\'>'.$unit.'</span>';
		}else{	
			$unit = '<span class=\'price_unit\'>'.$par->get('unit_price','EUR').'</span>';
		}
		
		switch($par->get('price_thousand_separator',0)) {
			case 0: $price_thousands_separator=''; break;
			case 1: $price_thousands_separator=' '; break;
			case 2: $price_thousands_separator='\''; break;
			case 3: $price_thousands_separator=','; break;
			case 4: $price_thousands_separator='.'; break;
			default: $price_thousands_separator=''; break;
		}
		
		switch($par->get('price_decimal_separator',0)) {
			case 0: $price_decimal_separator=','; break;
			case 1: $price_decimal_separator='.'; break;
			default: $price_decimal_separator=','; break;
		}
		
		$price_to_format = $price;
		if ($par->get('price_format','0')== 1) {
			$price = str_ireplace(',', '.', $price);
			if(is_numeric($price)){
				$price_to_format = number_format($price, $price_decimals, $price_decimal_separator, $price_thousands_separator);	
			}
			
		}
		
		if ($par->get('unit_price_position','0')== 1) {			
			$formated_price = $unit;
			if ($par->get('unit_price_space','1')== 1) {$formated_price .= ' ';}
			$formated_price .= $price_to_format;
		}else {
			$formated_price = $price_to_format;
			if ($par->get('unit_price_space','1')== 1) {$formated_price .= ' ';}
			$formated_price .= $unit;
		}
		return $formated_price;
		
	}
	public static function formatDate($from, $to = null, $date_format=0,$custom_format=''){
		$par = JComponentHelper::getParams( 'com_djclassifieds' );
		//if($from=='2145913200'){
		if(date('Y-m-d',$from)=='2038-01-01'){
			return JText::_('COM_DJCLASSIFIEDS_NEVER_EXPIRE');
		}else if($date_format){
			return DJClassifiedsTheme::dateFormatFromTo($from, $to);
		}else{
			if($par->get('date_persian',0)){
				return mds_date($par->get('date_format','Y-m-d H:i:s'),$from,1);
			}else if($custom_format){
				$from = date('Y-m-d H:i:s', $from);
				return JHtml::_('date', $from, $custom_format);
			}else{
				//return date($par->get('date_format','Y-m-d H:i:s'),$from);
				if($from>0){
					$from = date('Y-m-d H:i:s', $from);
					return JHtml::_('date', $from, $par->get('date_format','Y-m-d H:i:s'));
				}else{
					return null;
				}
				
			}							
		}
	}
	public static function dateFormatFromTo($from, $to = null)
	 {
	 	$par = JComponentHelper::getParams( 'com_djclassifieds' );		
	  	$to = (($to === null || $to=='') ? (time()) : ($to));
	  	$to = ((is_int($to)) ? ($to) : (strtotime($to)));
	  	$from = ((is_int($from)) ? ($from) : (strtotime($from)));
	  	$output = '';	  
	  	$limit = $par->get('date_format_ago_limit','2');
	  	$units = array
	  	(
		   "COM_DJCLASSIFIEDS_DATE_YEAR"   => 31536000, 
		   "COM_DJCLASSIFIEDS_DATE_MONTH"  => 2628000,  
		   "COM_DJCLASSIFIEDS_DATE_WEEK"   => 604800,   
		   "COM_DJCLASSIFIEDS_DATE_DAY"    => 86400,    
		   "COM_DJCLASSIFIEDS_DATE_HOUR"   => 3600,     
		   "COM_DJCLASSIFIEDS_DATE_MINUTE" => 60,       
		   "COM_DJCLASSIFIEDS_DATE_SECOND" => 1         
	  	);
	
	  	$diff = abs($from - $to);
	  	$suffix = (($from > $to) ? (JTEXT::_('COM_DJCLASSIFIEDS_DATE_FROM_NOW')) : (JTEXT::_('COM_DJCLASSIFIEDS_DATE_AGO')));
		
		$i=0;
		  	foreach($units as $unit => $mult){
		   		if($diff >= $mult){
		    		if($i==$limit-1 && $i>0){
		    		 	$output .= " ".JTEXT::_('COM_DJCLASSIFIEDS_DATE_AND').' '.intval($diff / $mult)." ";
					}else{
						$output .= ", ".intval($diff / $mult)." ";
					}	
		    		//$and = (($mult != 1) ? ("") : (JTEXT::_('COM_DJCLASSIFIEDS_DATE_AND')));
		    		//$output .= ", ".$and.intval($diff / $mult)." ";
					if(intval($diff / $mult) == 1){
						$output .= JTEXT::_($unit);	
					}else{
						$output .= JTEXT::_($unit."S");
					}
		    		
		    		$diff -= intval($diff / $mult) * $mult;
					$i++;
					if($i==$limit){ break; }			
		   		}
			}
			$output .= " ".$suffix;
	  		$output = substr($output, strlen(", "));
	  return $output;
	 }
	static function dateFormatConvert($date_format = 'Y-m-d'){
		if(!$date_format){
			$date_format = 'Y-m-d';
		}
		$a_arr = array('d','m','y','Y','H','i','s');
		$b_arr = array('%d','%m','%y','%Y','%H','%M','%S');

		return str_replace($a_arr, $b_arr, $date_format);
	}
	 
	static function includeCSSfiles($theme=''){				
	 	$par = JComponentHelper::getParams( 'com_djclassifieds' );
	 	$document= JFactory::getDocument();
	 	if(!$theme){ $theme = $par->get('theme','default');}
	 	$theme_path = JPATH_BASE.DS.'components'.DS.'com_djclassifieds'.DS.'themes'.DS.$theme.DS.'css'.DS;
	 	
		if (JFile::exists($theme_path.'style.css')){
	 		$cs = JURI::base().'components/com_djclassifieds/themes/'.$theme.'/css/style.css';
	 		$document->addStyleSheet($cs);
	 	}else if($theme!='default'){
	 		$cs = JURI::base().'components/com_djclassifieds/themes/default/css/style.css'; 
	 		$document->addStyleSheet($cs);
	 	}
	 	
	 	if($par->get('include_css','1')){
	 		if (JFile::exists($theme_path.'style_default.css')){
	 			$cs = JURI::base().'components/com_djclassifieds/themes/'.$theme.'/css/style_default.css';
	 			$document->addStyleSheet($cs);
	 		}else if($theme!='default'){
	 			$cs = JURI::base().'components/com_djclassifieds/themes/default/css/style_default.css'; 
	 			$document->addStyleSheet($cs);
	 		}  
	 	}
	 	
	 	$add_rtl=0;
	 	if($document->direction=='rtl'){
	 		$add_rtl=1;
		}else if (isset($_COOKIE["jmfdirection"])){
			if($_COOKIE["jmfdirection"]=='rtl'){
				$add_rtl=1;	
			}
		}else if (isset($_COOKIE["djdirection"])){
			if($_COOKIE["djdirection"]=='rtl'){
				$add_rtl=1;	
			}
		}
		if($add_rtl){
	 		if (JFile::exists($theme_path.'style_rtl.css')){
	 			$cs = JURI::base().'components/com_djclassifieds/themes/'.$theme.'/css/style_rtl.css';
	 			$document->addStyleSheet($cs);
	 		}
	 	}
	 	if (JFile::exists($theme_path.'responsive.css')){
	 		$cs = JURI::base().'components/com_djclassifieds/themes/'.$theme.'/css/responsive.css';
	 		$document->addStyleSheet($cs);
	 	}else if($theme!='default'){
	 		$cs = JURI::base().'components/com_djclassifieds/themes/default/css/responsive.css'; 
	 		$document->addStyleSheet($cs);
	 	}  	 	
	 	
	 	/*if($par->get('include_awesome_font','1')){
	 		$cs = JURI::base().'components/com_djclassifieds/assets/fontawesome/css/font-awesome.min.css';
	 		$document->addStyleSheet($cs);
	 	}*/
	 	
	 	
	 }	

	 static function includeMapsScript(){
	 	$app 	  = JFactory::getApplication();
	 	$par 	  = JComponentHelper::getParams( 'com_djclassifieds' );
	 	$config	  = JFactory::getConfig();
	 	$document = JFactory::getDocument();
	 	$dispatcher = JDispatcher::getInstance();
	 	$load_gm_script = 1;
	 	
	 	JPluginHelper::importPlugin( 'djclassifieds' );
	 	$dispatcher->trigger('onIncludeMapsScripts', array (& $load_gm_script));
	 	
	 	if($load_gm_script){
		 	if($config->get('force_ssl',0)==2){
		 		$maps_script = 'https://maps.google.com/maps/api/js?';
		 	}else{
		 		$maps_script = 'http://maps.google.com/maps/api/js?';
		 	}
		 	if($par->get('map_api_key_browser','')){
		 		$maps_script .= 'key='.$par->get('map_api_key_browser','').'&';
		 	}
		 	$document->addScript($maps_script."v=3.exp&amp;libraries=places");
	 	}
	 	return null;	 	
	 }
	 
	 public static function includeCalendarScripts() {
	 	$version = new JVersion;
	 	if (!version_compare($version->getShortVersion(), '3.7.0', '<')) {
	 		/** new Calendar setup **/
	 		$tag       = JFactory::getLanguage()->getTag();
	 		$calendar  = JFactory::getLanguage()->getCalendar();
	 		$direction = strtolower(JFactory::getDocument()->getDirection());
	 
	 		$localesPath = 'system/fields/calendar-locales/en.js';
	 		if (is_file(JPATH_ROOT . '/media/system/js/fields/calendar-locales/' . strtolower($tag) . '.js'))
	 		{
	 			$localesPath = 'system/fields/calendar-locales/' . strtolower($tag) . '.js';
	 		}
	 		elseif (is_file(JPATH_ROOT . '/media/system/js/fields/calendar-locales/' . strtolower(substr($tag, 0, -3)) . '.js'))
	 		{
	 			$localesPath = 'system/fields/calendar-locales/' . strtolower(substr($tag, 0, -3)) . '.js';
	 		}
	 		$cssFileExt = ($direction === 'rtl') ? '-rtl.css' : '.css';
	 		// Load polyfills for older IE
	 		JHtml::_('behavior.polyfill', array('event', 'classlist', 'map'), 'lte IE 11');
	 		// The static assets for the calendar
	 		JHtml::_('script', $localesPath, false, true, false, false, true);
	 		JHtml::_('script', 'system/fields/calendar-locales/date/gregorian/date-helper.min.js', false, true, false, false, true);
	 		JHtml::_('script', 'system/fields/calendar.min.js', false, true, false, false, true);
	 		JHtml::_('stylesheet', 'system/fields/calendar' . $cssFileExt, array(), true);
	 	}
	 }	 
	 
	 public static function djAccessRestriction($type=''){
	 	$app = JFactory::getApplication();
	 	$par = JComponentHelper::getParams( 'com_djclassifieds' );
	 	
	 	if($type=='category'){
	 		$message = JText::_("COM_DJCLASSIFIEDS_YOU_ARE_NOT_AUTHORIZED_TO_VIEW_THIS_CATEGORY");
	 	}else{
	 		$message = JText::_("COM_DJCLASSIFIEDS_YOU_ARE_NOT_AUTHORIZED_TO_VIEW_THIS_ADVERT");
	 	}	 	
		 	
		 	if($par->get('acl_redirect','0')==1){
		 		JError::raiseWarning(403, $message);
		 		$redirect = JURI::base();
		 	}else if($par->get('acl_redirect','0')==2 && $par->get('acl_red_article_id','0')>0){		 				 		
		 		
		 		$db= JFactory::getDBO();
				$query = "SELECT a.id, a.alias, a.catid, c.alias as c_alias FROM #__content a "
						."LEFT JOIN #__categories c ON c.id=a.catid "
						."WHERE a.state=1 AND a.id=".$par->get('acl_red_article_id','0');
				
				$db->setQuery($query);
				$acl_article=$db->loadObject();		 				 				 				 		
		 		
		 		if($acl_article){
		 			require_once JPATH_ROOT.'/components/com_content/helpers/route.php';
		 			$slug = $acl_article->id.':'.$acl_article->alias;
		 			$cslug = $acl_article->catid.':'.$acl_article->c_alias;
		 			$article_link = ContentHelperRoute::getArticleRoute($slug,$cslug);		 			
		 			$redirect = JRoute::_($article_link);
		 		}else{
		 			$redirect = JURI::base();
		 		}
		 		
		 	}else{
		 		$redirect=DJClassifiedsSEO::getCategoryRoute('0:all');		 		
		 	}
		 	$redirect = JRoute::_($redirect);
		 	$app->redirect($redirect,$message,'error');
	 	
	 	return null;
	 	
	 }

	 public static function getMapsProvider(){
		$db = JFactory::getDBO();
		
		$query = "SELECT element FROM #__extensions "
        ."WHERE type='plugin' AND folder='djclassifieds' AND (element IN ('leaflet','baidu','yandexmaps') OR element LIKE '%maps%') AND enabled=1 "
        ."ORDER BY extension_id DESC LIMIT 1";
        $db->setQuery($query);
		$map_provider = $db->loadResult();
		
		return $map_provider;
	 }

	 public static function sanitizeText($text){
		return trim(preg_replace('/\s+/', ' ', addslashes($text)));
	 }

	 public static function sanitizePopupText($text){ // backward compatibility
		return self::sanitizeText($text);
	 }

	 public static function getImgUploadPath()
	 {
		$par = JComponentHelper::getParams('com_djclassifieds');
		return $par->get('upload_path', '/tmp/djupload');
	 }
	 
}