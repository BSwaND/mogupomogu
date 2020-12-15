<?php
/**
* @version 2.0
* @package DJ Classifieds Menu Module
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
if(!defined("DS")){ define('DS',DIRECTORY_SEPARATOR);}
require_once (dirname(__FILE__).DS.'helper.php');
require_once(JPATH_BASE.DS.'administrator'.DS.'components'.DS.'com_djclassifieds'.DS.'lib'.DS.'djcategory.php');
require_once(JPATH_BASE.DS.'administrator'.DS.'components'.DS.'com_djclassifieds'.DS.'lib'.DS.'djseo.php');
require_once(JPATH_BASE.DS.'administrator'.DS.'components'.DS.'com_djclassifieds'.DS.'lib'.DS.'djtheme.php');

	$app = JFactory::getApplication();
	$menu = $app->getMenu();
	$active = $menu->getActive();
	
	if(JRequest::getVar('option')!='com_djclassifieds'){
		DJClassifiedsTheme::includeCSSfiles();
	}

	if($params->get('follow_cat', 0)){
		$se_cats = explode(',', $app->input->getStr('se_cats', ''));
		$se_cat_id = str_replace('p', '', end($se_cats));
		$cid = $se_cat_id ? $se_cat_id : $app->input->getInt('cid', 0);
	}

	if(!empty($cid)){
		$params->set('cat_id',$cid);
		$cats= DJClassifiedsCategory::getSubCatIemsCount($cid,1,$params->get('cat_ordering','name'));
	}elseif($params->get('cat_id',0) > 0){		
		$cats= DJClassifiedsCategory::getSubCatIemsCount($params->get('cat_id',0),1,$params->get('cat_ordering','name'));
	}else{
		$cats= DJClassifiedsCategory::getCatAllItemsCount(1,$params->get('cat_ordering','name'));
	}
	
	
	//	echo '<pre>';print_r($cats);die();
	
	$cat_images='';
	if($params->get('cattree_img',0)){
		$cat_images = modDjClassifiedsCatTree::getCatImages();
	}

require(JModuleHelper::getLayoutPath('mod_djclassifieds_cattree', $params->get('layout', 'default')));
?>



