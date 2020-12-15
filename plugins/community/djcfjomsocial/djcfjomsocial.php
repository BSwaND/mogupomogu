<?php
/**
* @version		1.0
* @package		DJ Classifieds
* @subpackage	DJ Classifieds JomSocial Plugin
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
$lang->load('plg_community_djcfjomsocial',JPATH_ADMINISTRATOR);
require_once JPATH_ROOT . '/components/com_community/libraries/core.php';
if(!defined("DS")){define('DS',DIRECTORY_SEPARATOR);}
require_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_djclassifieds'.DS.'lib'.DS.'djseo.php');
require_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_djclassifieds'.DS.'lib'.DS.'djtheme.php');

$c_lang = $lang->getTag();
if($c_lang=='pl-PL' || $c_lang=='en-GB'){
	$lang->load('com_djclassifieds', JPATH_SITE.'/components/com_djclassifieds', null, true);
}else{
	if(!$lang->load('com_djclassifieds', JPATH_SITE, null, true)){
		$lang->load('com_djclassifieds', JPATH_SITE.'/components/com_djclassifieds', null, true);
	}
}


class plgCommunityDJCFJomSocial extends CApplications {
	var $name = "djcfjomsocial";
	var $_name = 'djcfjomsocial';

	function plgCommunityExample(&$subject, $config) {
		parent::__construct($subject, $config);
	}

	function onProfileDisplay() {
		$content = '';
		if($this->params->get('enable_mod_latest', '1')){
			$document = JFactory::getDocument ();
			$document->addStyleSheet ( JURI::base () . 'plugins/community/djcfjomsocial/style.css' );
			ob_start();
			$user = CFactory::getRequestUser();						
			$cfpar = JComponentHelper::getParams( 'com_djclassifieds' );
			if($user->id){
				$db	= JFactory::getDBO();
				$query = "SELECT i.*,c.alias as c_alias,c.name as c_name, r.name as r_name, "
						."img.path as img_path, img.name as img_name, img.ext as img_ext,img.caption as img_caption " 
						."FROM #__djcf_categories c, #__djcf_items i "
						."LEFT JOIN #__djcf_regions r ON r.id=i.region_id "
						."LEFT JOIN ( SELECT img.id, img.item_id, img.name, img.path, img.ext, img.ordering, img.caption 
		 						  FROM (SELECT * FROM #__djcf_images WHERE type='item' ORDER BY ordering) img GROUP BY img.item_id ) AS img ON img.item_id=i.id "
						."WHERE i.date_exp > NOW() AND i.published = 1 AND c.published = 1 AND i.cat_id=c.id AND i.user_id=".$user->id." "			
						."ORDER BY i.date_start DESC limit ".$this->params->get('mod_latest_limit', '5');
				$db->setQuery($query);
				$items=$db->loadObjectList();
				echo '<div class="djcf_community_items">';
					foreach($items as $item){
						echo '<div class="item">';
							echo '<div class="title">';
							if($item->img_path && $item->img_name && $item->img_ext){																						
								echo '<a class="title_img" href="'.JRoute::_(DJClassifiedsSEO::getItemRoute($item->id.':'.$item->alias,$item->cat_id.':'.$item->c_alias)).'">';
								echo '<img style="margin-right:3px;" src="'.JURI::base().$item->img_path.$item->img_name.'_ths.'.$item->img_ext.'" alt="'.str_ireplace('"', "'", $item->name).'" title="'.$item->img_caption.'" />';
								echo '</a>';
							}
																									 
							echo '<a class="title" href="'.JRoute::_(DJClassifiedsSEO::getItemRoute($item->id.':'.$item->alias,$item->cat_id.':'.$item->c_alias)).'">'.$item->name.'</a>';							
								echo '<div class="date_cat">';								
									echo '<span class="date">';							
										if($cfpar->get('date_format_type_modules',0)){
											echo DJClassifiedsTheme::dateFormatFromTo(strtotime($item->date_start));	
										}else{
											echo date($cfpar->get('date_format','Y-m-d H:i:s'),strtotime($item->date_start));	
										}
									echo '</span>';
									
									echo '<span class="category">';																			
										echo '<a class="title_cat" href="'.JRoute::_(DJClassifiedsSEO::getCategoryRoute($item->cat_id.':'.$item->c_alias)).'">'.$item->c_name.'</a>';												
									echo '</span>';
																																		
									echo '<span class="region">';
										echo $item->r_name;
									echo '</span>';
										
								if($item->price){
									echo '<span class="price">';
										echo DJClassifiedsTheme::priceFormat($item->price,$item->currency);
									echo '</span>';
								}
								echo '</div>';
									
							echo '</div>';												
							echo '<div class="desc">';																	
									echo $item->intro_desc;		
							echo '</div>';							
						echo '</div>';				
					}
				echo '<div style="clear:both"></div>';
				echo '</div>';
				//echo '<pre>';print_r($items);die();	
			}
	
			$content = ob_get_contents();
			ob_end_clean();
		}

		return $content;
	}
	
	function addToStream($val){
		if($this->params->get('enable_stream', '0')){
			CFactory::load('libraries', 'activities');			
			$actor = CFactory::getUser($val['user_id']); 
			
				$act = new stdClass();
				$act->cmd = 'djclassifieds.newadvert';
				$act->actor = $val['user_id'];
				$act->target = 0;
				$act->title = '<a class="cStream-Author" href="' .CUrlHelper::userLink($actor->id).'">'.$actor->getDisplayName().'</a> ';
				$act->title .= JText::_('PLG_DJCLASSIFIEDS_ADDED_ADVERT');			
				$act->content = JText::_('PLG_DJCLASSIFIEDS_NEW_ADVERT').' <a href="'.$val['link'].'">'.$val['name'].'</a>';
					if($val['image_url']){
						$act->content .= '<br /><br />';
						$images=explode(';', substr($val['image_url'],0,-1));				
						$img_path= JURI::base().'/components/com_djclassifieds/images/';
						for($i=0; $i<count($images); $i++){
							if($i==3){break;} 
							$act->content .= '<a style="margin-right:10px" href="'.$val['link'].'"><img src="'.$img_path.$images[$i].'.ths.jpg" alt="" /></a>'; 
						}	
					}
	
				$act->app = 'djclassifieds';
				$act->cid = $val['id'];
				$act->params = '';
				
			CActivityStream::add($act);
		}
		return true;
	}

}
?>