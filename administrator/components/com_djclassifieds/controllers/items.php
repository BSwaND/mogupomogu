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

// No direct access.
defined('_JEXEC') or die;
JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_djclassifieds'.DS.'tables');
jimport('joomla.application.component.controlleradmin');

class DJClassifiedsControllerItems extends JControllerAdmin
{
	public function getModel($name = 'Item', $prefix = 'DJClassifiedsModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}
	
	function recreateThumbnails(){	
		$app = JFactory::getApplication();
		$par = JComponentHelper::getParams( 'com_djclassifieds' );
		JToolBarHelper::title(JText::_('COM_DJCLASSIFIEDS_RECREATING_THUMBNAILS'), 'generic.png');
	    
		$cid = JRequest::getVar( 'cid', array(), 'default', 'array' );
		JArrayHelper::toInteger($cid);

		if (count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'COM_DJCLASSIFIEDS_SELECT_ITEM_TO_RECREATE_THUMBS ' ) );
		}
		
		$tmp = array();
		$tmp[0] = $cid[0];
		unset($cid[0]);		
			$db =  JFactory::getDBO();	
	        $query = "SELECT * FROM #__djcf_images WHERE item_id =  ".$tmp[0] ." AND type='item' ";
			$db->setQuery($query);
			$images = $db->loadObjectList();
			if($images){				
				$nw = (int)$par->get('th_width',-1);
				$nh = (int)$par->get('th_height',-1);
				$nws = $par->get('smallth_width',-1);
				$nhs = $par->get('smallth_height',-1);
				$nwm = $par->get('middleth_width',-1);
				$nhm = $par->get('middleth_height',-1);
				$nwb = $par->get('bigth_width',-1);
				$nhb = $par->get('bigth_height',-1);
				foreach($images as $image){
					$path = JPATH_SITE.$image->path.$image->name;	
					if (JFile::exists($path.'.'.$image->ext)){ 
        				if (JFile::exists($path.'_thb.'.$image->ext)){
            				JFile::delete($path.'_thb.'.$image->ext);
  						}
						if (JFile::exists($path.'_th.'.$image->ext)){
            				JFile::delete($path.'_th.'.$image->ext);
        				}
						if (JFile::exists($path.'_thm.'.$image->ext)){
            				JFile::delete($path.'_thm.'.$image->ext);
        				}
        				if (JFile::exists($path.'_ths.'.$image->ext)){
            				JFile::delete($path.'_ths.'.$image->ext);
        				}
						
				 		//DJClassifiedsImage::makeThumb($path.$images[$ii], $nws, $nhs, 'ths');
				 		DJClassifiedsImage::makeThumb($path.'.'.$image->ext,$path.'_ths.'.$image->ext, $nws, $nhs);					
						DJClassifiedsImage::makeThumb($path.'.'.$image->ext,$path.'_thm.'.$image->ext, $nwm, $nhm);
						DJClassifiedsImage::makeThumb($path.'.'.$image->ext,$path.'_thb.'.$image->ext, $nwb, $nhb);
					}
				}
			}
		
	    
		if (count( $cid ) < 1) {
			$this->setRedirect( 'index.php?option=com_djclassifieds&view=items', JText::_('COM_DJCLASSIFIEDS_THUMBNAILS_RECREATED') );	
		} else {	        
			$cids = null;
			foreach ($cid as $value) {
				$cids .= '&cid[]='.$value; 
			}
			echo '<h3>'.JTEXT::_('COM_DJCLASSIFIEDS_RESIZING_ITEM').' [id = '.$tmp[0].']... '.JTEXT::_('COM_DJCLASSIFIEDS_PLEASE_WAIT').'</h3>';
			header("refresh: 0; url=".JURI::base().'index.php?option=com_djclassifieds&task=items.recreateThumbnails'.$cids);				        
	    }
	    //$redirect = 'index.php?option=com_djclassifieds&view=items';
	    //$app->redirect($redirect, JText::_('COM_DJCLASSIFIEDS_THUMBNAILS_RECREATED'));
	}

	
	function resmushitThumbnails(){
		$app = JFactory::getApplication();
		$par = JComponentHelper::getParams( 'com_djclassifieds' );
		JToolBarHelper::title(JText::_('COM_DJCLASSIFIEDS_OPTIMIZING_THUMBNAILS'), 'generic.png');
		 
		$cid = JRequest::getVar( 'cid', array(), 'default', 'array' );
		JArrayHelper::toInteger($cid);
	
		if (count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'COM_DJCLASSIFIEDS_SELECT_ITEM_TO_OPTIMIZE_THUMBS ' ) );
		}
	
		$tmp = array();
		$tmp[0] = $cid[0];
		unset($cid[0]);
		$db =  JFactory::getDBO();
		$query = "SELECT * FROM #__djcf_images WHERE item_id =  ".$tmp[0] ." AND type='item' AND optimized=0 ";
		$db->setQuery($query);
		$images = $db->loadObjectList();
		if($images){
			foreach($images as $image){
				$path = JPATH_SITE.$image->path.$image->name;
				if (JFile::exists($path.'.'.$image->ext)){
					if (JFile::exists($path.'_thb.'.$image->ext)){
						DJClassifiedsImage::resmushitThumbnails($path.'_thb.'.$image->ext);
					}
					if (JFile::exists($path.'_th.'.$image->ext)){
						DJClassifiedsImage::resmushitThumbnails($path.'_th.'.$image->ext);
					}
					if (JFile::exists($path.'_thm.'.$image->ext)){
						DJClassifiedsImage::resmushitThumbnails($path.'_thm.'.$image->ext);
					}
					if (JFile::exists($path.'_ths.'.$image->ext)){
						DJClassifiedsImage::resmushitThumbnails($path.'_ths.'.$image->ext);
					}	
					if (JFile::exists($path.'.'.$image->ext)){
						//DJClassifiedsImage::resmushitThumbnails($path.'.'.$image->ext);
					}
				}
				$query = "UPDATE #__djcf_images SET optimized=1 WHERE id =  ".$image->id." ";
				$db->setQuery($query);
				$db->query();
			}						
		}
	
		 
		if (count( $cid ) < 1) {
			$this->setRedirect( 'index.php?option=com_djclassifieds&view=items', JText::_('COM_DJCLASSIFIEDS_THUMBNAILS_OPTIMIZED') );
		} else {
			$cids = null;
			foreach ($cid as $value) {
				$cids .= '&cid[]='.$value;
			}
			echo '<h3>'.JTEXT::_('COM_DJCLASSIFIEDS_OPTIMALIZING_ITEM').' [id = '.$tmp[0].']... '.JTEXT::_('COM_DJCLASSIFIEDS_PLEASE_WAIT').'</h3>';
			header("refresh: 0; url=".JURI::base().'index.php?option=com_djclassifieds&task=items.resmushitThumbnails'.$cids);
		}
		//$redirect = 'index.php?option=com_djclassifieds&view=items';
		//$app->redirect($redirect, JText::_('COM_DJCLASSIFIEDS_THUMBNAILS_RECREATED'));
	}
	
	
	function migrateImages(){
		$app = JFactory::getApplication();
		$par = JComponentHelper::getParams( 'com_djclassifieds' );
		JToolBarHelper::title(JText::_('COM_DJCLASSIFIEDS_MIGRATING_IMAGES'), 'generic.png');		 
	
		$db =  JFactory::getDBO();
		$query = "SELECT id, image_url FROM #__djcf_items WHERE image_url IS NOT NULL AND image_url!='' ORDER BY id LIMIT 1";
		$db->setQuery($query);
		$item = $db->loadObject();
		
		if($item){	
			$path = JPATH_BASE."/../components/com_djclassifieds/images/";
			$nw = (int)$par->get('th_width',-1);
			$nh = (int)$par->get('th_height',-1);
			$nws = $par->get('smallth_width',-1);
			$nhs = $par->get('smallth_height',-1);
			$nwm = $par->get('middleth_width',-1);
			$nhm = $par->get('middleth_height',-1);
			$nwb = $par->get('bigth_width',-1);
			$nhb = $par->get('bigth_height',-1);
			$query = "INSERT INTO #__djcf_images(`item_id`,`type`,`name`,`ext`,`path`,`caption`,`ordering`) VALUES ";
			$ord=1;
				$images = explode(";",$item->image_url);
				for($ii=0; $ii<count($images)-1;$ii++ ){
					if (JFile::exists($path.$images[$ii].'.thb.jpg')){
						JFile::delete($path.$images[$ii].'.thb.jpg');
					}
					if (JFile::exists($path.$images[$ii].'.th.jpg')){
						JFile::delete($path.$images[$ii].'.th.jpg');
					}
					if (JFile::exists($path.$images[$ii].'.thm.jpg')){
						JFile::delete($path.$images[$ii].'.thm.jpg');
					}
					if (JFile::exists($path.$images[$ii].'.ths.jpg')){
						JFile::delete($path.$images[$ii].'.ths.jpg');
					}

					$new_path = $path.'item/';
					rename($path.$images[$ii], $new_path.$images[$ii]);
					$name_parts = pathinfo($images[$ii]);
					$img_name = $name_parts['filename'];
					$img_ext = $name_parts['extension'];									
					
					//DJClassifiedsImage::makeThumb($path.$images[$ii], $nw, $nh, 'th');
					DJClassifiedsImage::makeThumb($new_path.$images[$ii],$new_path.$img_name.'_ths.'.$img_ext, $nws, $nhs);
					DJClassifiedsImage::makeThumb($new_path.$images[$ii],$new_path.$img_name.'_thm.'.$img_ext, $nwm, $nhm);
					DJClassifiedsImage::makeThumb($new_path.$images[$ii],$new_path.$img_name.'_thb.'.$img_ext, $nwb, $nhb);
					$query .= "('".$item->id."','item','".$img_name."','".$img_ext."','/components/com_djclassifieds/images/item/','','".$ord."'), ";
					$ord++;
				}
				if($ord>1){
					$query = substr($query, 0, -2).';';
					$db->setQuery($query);
					$db->query();
					
					$query = "UPDATE #__djcf_items SET image_url='' WHERE id=".$item->id;
					$db->setQuery($query);
					$db->query();
				}
				
			echo '<h3>'.JTEXT::_('COM_DJCLASSIFIEDS_MIGRATING_IMAGES_FROM_ITEM').' [id = '.$item->id.']... '.JTEXT::_('COM_DJCLASSIFIEDS_PLEASE_WAIT').'</h3>';
			header("refresh: 0; url=".JURI::base().'index.php?option=com_djclassifieds&task=items.migrateImages');				
		}else{
			$this->setRedirect( 'index.php?option=com_djclassifieds&view=items', JText::_('COM_DJCLASSIFIEDS_IMAGES_MIGRATED') );
		}
		 
		//$redirect = 'index.php?option=com_djclassifieds&view=items';
		//$app->redirect($redirect, JText::_('COM_DJCLASSIFIEDS_THUMBNAILS_RECREATED'));
	}	

	function migrateCatImages(){
		$app = JFactory::getApplication();
		$par = JComponentHelper::getParams( 'com_djclassifieds' );
		JToolBarHelper::title(JText::_('COM_DJCLASSIFIEDS_MIGRATING_IMAGES'), 'generic.png');
	
		$db =  JFactory::getDBO();
		$query = "SELECT id, icon_url FROM #__djcf_categories WHERE icon_url IS NOT NULL AND icon_url!='' ORDER BY id LIMIT 1";
		$db->setQuery($query);
		$item = $db->loadObject();
	
		if($item){
			$path = JPATH_BASE."/../components/com_djclassifieds/images/";
			$nw = (int)$par->get('catth_width',-1);
			$nh = (int)$par->get('catth_height',-1);			
						
				$image = $item->icon_url;
							
				if (JFile::exists($path.$image.'.ths.jpg')){
					JFile::delete($path.$image.'.ths.jpg');
				}
	
				$new_path = $path.'category/';
				rename($path.$image, $new_path.$image);
				$name_parts = pathinfo($image);
				$img_name = $name_parts['filename'];
				$img_ext = $name_parts['extension'];
					
				DJClassifiedsImage::makeThumb($new_path.$image,$new_path.$img_name.'_ths.'.$img_ext, $nw, $nh);
				$query = "INSERT INTO #__djcf_images(`item_id`,`type`,`name`,`ext`,`path`,`caption`,`ordering`) VALUES ";
				$query .= "('".$item->id."','category','".$img_name."','".$img_ext."','/components/com_djclassifieds/images/category/','','1'); ";
				$db->setQuery($query);
				$db->query();
						
				$query = "UPDATE #__djcf_categories SET icon_url='' WHERE id=".$item->id;
				$db->setQuery($query);
				$db->query();			
	
			echo '<h3>'.JTEXT::_('COM_DJCLASSIFIEDS_MIGRATING_IMAGES_FROM_CATEGORY').' [id = '.$item->id.']... '.JTEXT::_('COM_DJCLASSIFIEDS_PLEASE_WAIT').'</h3>';
			header("refresh: 0; url=".JURI::base().'index.php?option=com_djclassifieds&task=items.migrateCatImages');
		}else{
			$this->setRedirect( 'index.php?option=com_djclassifieds&view=categories', JText::_('COM_DJCLASSIFIEDS_IMAGES_MIGRATED') );
		}
			
		//$redirect = 'index.php?option=com_djclassifieds&view=items';
		//$app->redirect($redirect, JText::_('COM_DJCLASSIFIEDS_THUMBNAILS_RECREATED'));
	}
	
	function recreateThumbnails_all(){	
		$app = JFactory::getApplication();
		$par = &JComponentHelper::getParams( 'com_djclassifieds' );
	    $cid = JRequest::getVar('cid', array (), '', 'array');
		
	    $db = & JFactory::getDBO();
	    if (count($cid))
	    {
	        $cids = implode(',', $cid);
	        $query = "SELECT id, image_url FROM #__djcf_items WHERE id IN ( ".$cids." )";
			$db->setQuery($query);
			$items = $db->loadObjectList();
			$path = JPATH_BASE."/../components/com_djclassifieds/images/";
				$nw = (int)$par->get('th_width',-1);
	    		$nh = (int)$par->get('th_height',-1);
				$nws = $par->get('smallth_width',-1);
	    		$nhs = $par->get('smallth_height',-1);
				$nwm = $par->get('middleth_width',-1);
	    		$nhm = $par->get('middleth_height',-1);
				$nwb = $par->get('bigth_width',-1);
	    		$nhb = $par->get('bigth_height',-1);							
		
			foreach($items as $i){
				if($i->image_url){				
					$images = explode(";",$i->image_url);
					for($ii=0; $ii<count($images)-1;$ii++ ){												
	        				if (JFile::exists($path.$images[$ii].'.thb.jpg')){
	            				JFile::delete($path.$images[$ii].'.thb.jpg');
	  						}
							if (JFile::exists($path.$images[$ii].'.th.jpg')){
	            				JFile::delete($path.$images[$ii].'.th.jpg');
	        				}
							if (JFile::exists($path.$images[$ii].'.thm.jpg')){
	            				JFile::delete($path.$images[$ii].'.thm.jpg');
	        				}
	        				if (JFile::exists($path.$images[$ii].'.ths.jpg')){
	            				JFile::delete($path.$images[$ii].'.ths.jpg');
	        				}
							
						//DJClassifiedsImage::makeThumb($path.$images[$ii], $nw, $nh, 'th');
				 		DJClassifiedsImage::makeThumb($path.$images[$ii], $nws, $nhs, 'ths');
						DJClassifiedsImage::makeThumb($path.$images[$ii], $nwm, $nhm, 'thm');
						DJClassifiedsImage::makeThumb($path.$images[$ii], $nwb, $nhb, 'thb');				
					}
				}
			}				        
	    }
	    $redirect = 'index.php?option=com_djclassifieds&view=items';
	    $app->redirect($redirect, JText::_('COM_DJCLASSIFIEDS_THUMBNAILS_RECREATED'));
	}
	
	function generateCoordinates(){	
		$app = JFactory::getApplication();
		$par = JComponentHelper::getParams( 'com_djclassifieds' );
		$db  = JFactory::getDBO();
		JToolBarHelper::title(JText::_('COM_DJCLASSIFIEDS_GENERATING_COORDINATES'), 'generic.png');
	    
		
		$id_checked = JRequest::getInt('idc',0);
		/*$id_checked_s='';
		if($id_checked){
			$id_checked_s = 'AND id NOT IN ('.$id_checked.')';	
		}*/
			
	    $query = "SELECT * FROM #__djcf_items WHERE (region_id>0 OR address!='') "
	    		."AND latitude=0.000000000000000 AND longitude=0.000000000000000 "
	    		."AND id >".$id_checked." ORDER BY id LIMIT 1";
		$db->setQuery($query);
		$item = $db->loadObject();
		//echo '<pre>';print_r($db);die();				
		
		if($item){
			/*if($id_checked){
				$id_checked .=','.$item->id;
			}else{
				$id_checked .= $item->id;
			}*/
			$id_checked = $item->id;
			$address= '';
			
			if($item->region_id){
				
				$reg_path = DJClassifiedsRegion::getParentPath($item->region_id);
				for($r=count($reg_path)-1;$r>=0;$r--){
					if($reg_path[$r]->country){
						$address = $reg_path[$r]->name; 
					}
					if($reg_path[$r]->city){
						if($address){	$address .= ', ';}					
						$address .= $reg_path[$r]->name;										 
					}				
				}
			}
			if($address){	$address .= ', ';}
			$address .= $item->address;
			if($item->post_code){
				$address .= ', '.$item->post_code;	
			}
			
			$loc_coord = DJClassifiedsGeocode::getLocation($address);
			$loc_coord = isset($loc_coord[0]) && is_array($loc_coord[0]) ? $loc_coord[0] : $loc_coord;
			if(!empty($loc_coord)){
				$query = "UPDATE #__djcf_items SET latitude='".$loc_coord['lat']."',longitude='".$loc_coord['lng']."'  WHERE id=".$item->id;
				$db->setQuery($query);
				$db->query();
				//echo '<pre>';print_r($db);die();
			}
			echo '<h3>'.JTEXT::_('COM_DJCLASSIFIEDS_GENERATING_COORDINATES').' [id = '.$item->id.']... '.JTEXT::_('COM_DJCLASSIFIEDS_PLEASE_WAIT').'</h3>';			
			header("refresh: 0; url=".JURI::base().'index.php?option=com_djclassifieds&task=items.generateCoordinates&idc='.$id_checked);			
		}else{
			$redirect = 'index.php?option=com_djclassifieds&view=items';
	    	$app->redirect($redirect, JText::_('COM_DJCLASSIFIEDS_COORDINATES_REGENERATED'));
			
		}				
	}
	
	function delete()
	{
		JPluginHelper::importPlugin('djclassifieds');
		$app  = JFactory::getApplication();
	    $cid  = JRequest::getVar('cid', array (), '', 'array');
	    $db   = JFactory::getDBO();
	    $user = JFactory::getUser();
	    $dispatcher = JDispatcher::getInstance();
	    $par = JComponentHelper::getParams( 'com_djclassifieds' );
	    
	    
	    if (!$user->authorise('core.delete', 'com_djclassifieds')) {
	    	$this->setError(JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'));
	    	$this->setMessage($this->getError(), 'error');
	    	$this->setRedirect( 'index.php?option=com_djclassifieds&view=items' );
	    	return false;
	    }
	    
	    if (count($cid))
	    {
	        $cids = implode(',', $cid);
	        $query = "SELECT * FROM #__djcf_items WHERE id IN ( ".$cids." )";
			$db->setQuery($query);
			$items = $db->loadObjectList();
			
			foreach($items as $item){
				$dispatcher->trigger('onBeforeDJClassifiedsDeleteAdvert', array($item));
			}
			
			$query = "SELECT * FROM #__djcf_images WHERE item_id IN ( ".$cids." ) AND type='item' ";
			$db->setQuery($query);
			$items_images =$db->loadObjectList('id');
			
			
			if($items_images){
				foreach($items_images as $item_img){
					$path_to_delete = JPATH_ROOT.$item_img->path.$item_img->name;
					if (JFile::exists($path_to_delete.'.'.$item_img->ext)){
						JFile::delete($path_to_delete.'.'.$item_img->ext);
					}
					
					if($par->get('leave_small_th','0')==0){
						if (JFile::exists($path_to_delete.'_ths.'.$item_img->ext)){
							JFile::delete($path_to_delete.'_ths.'.$item_img->ext);
						}
					}
					if (JFile::exists($path_to_delete.'_thm.'.$item_img->ext)){
						JFile::delete($path_to_delete.'_thm.'.$item_img->ext);
					}
					if (JFile::exists($path_to_delete.'_thb.'.$item_img->ext)){
						JFile::delete($path_to_delete.'_thb.'.$item_img->ext);
					}
				}
			}
			
	        $cids = implode(',', $cid);
	        $query = "DELETE FROM #__djcf_items WHERE id IN ( ".$cids." )";
	        $db->setQuery($query);
	        if (!$db->query())
	        {
				echo $db->getErrorMsg();
        		exit ();	
	        }
			
			$query = "DELETE FROM #__djcf_fields_values WHERE item_id IN ( ".$cids." )";
	        $db->setQuery($query);
	        $db->query();
			
			$query = "DELETE FROM #__djcf_payments WHERE item_id IN ( ".$cids." )";
	        $db->setQuery($query);
	        $db->query();
	        
	        $query = "DELETE FROM #__djcf_images WHERE item_id IN ( ".$cids." ) AND type='item' ";
	        $db->setQuery($query);
	        $db->query();
	        
	        foreach($items as $item){
	        	$dispatcher->trigger('onAfterDJClassifiedsDeleteAdvert', array($item));
	        }
	        
	    }
	    $app->redirect('index.php?option=com_djclassifieds&view=items', JText::_('COM_DJCLASSIFIEDS_ITEMS_DELETED'));
	}
	
	
	public function publish(){
		$app  = JFactory::getApplication();		
		$par  = JComponentHelper::getParams( 'com_djclassifieds' );
		$data = array('publish' => 1, 'unpublish' => 0, 'archive' => 2, 'trash' => -2, 'report' => -3);		
		$task = $this->getTask();
		$new_status = JArrayHelper::getValue($data, $task, 0, 'int');
		$cid  = JRequest::getVar('cid', array (), '', 'array');						
		
			if($par->get('notify_status_change',2)==2){
				foreach($cid as $id){					
					DJClassifiedsNotify::notifyUserPublication($id,$new_status);					
				}
			}
				
		$publish = parent::publish(); 		
	
		return $publish;
	}

	
	function migratePromotions(){
		$app = JFactory::getApplication();
		$db =  JFactory::getDBO();
		$par = JComponentHelper::getParams( 'com_djclassifieds' );
		$exp_days = $par->get('exp_days',7);
	
		$query = "SELECT * FROM #__djcf_promotions ORDER BY id";
		$db->setQuery($query);
		$proms = $db->loadObjectList('name');
	
		$query = "INSERT INTO #__djcf_promotions_prices(`prom_id`,`days`,`price`,`points`) VALUES ";
		foreach($proms as $prom){
			$query .= "('".$prom->id."','".$exp_days."','".$prom->price."','".$prom->points."'), ";
		}
		$query = substr($query, 0, -2);
		$db->setQuery($query);
		$db->query();
	
	
		$query = "SELECT * FROM #__djcf_items WHERE promotions != '' ORDER BY id";
		$db->setQuery($query);
		$items = $db->loadObjectList();
	
		//echo '<pre>';print_r($items);die();
		$query = "INSERT INTO #__djcf_items_promotions(`item_id`,`prom_id`,`date_start`,`date_exp`,`days`) VALUES ";
		foreach($items as $item){
			$item_proms = explode(',', $item->promotions);
			foreach($item_proms as $item_p){
				if(isset($proms[$item_p])){
					$query .= "('".$item->id."','".$proms[$item_p]->id."','".$item->date_start."','".$item->date_exp."','".$exp_days."'), ";
				}
			}
		}
		$query = substr($query, 0, -2);
		$db->setQuery($query);
		$db->query();
	
		die('done');
	}
	
	

	function generateAdresses(){
		$app = JFactory::getApplication();
		$par = JComponentHelper::getParams( 'com_djclassifieds' );
		$db  = JFactory::getDBO();
		JToolBarHelper::title(JText::_('COM_DJCLASSIFIEDS_GENERATING_ADRESSES'), 'generic.png');
		 
	
		$id_checked = JRequest::getInt('idc',0);
		/*$id_checked_s='';
			if($id_checked){
			$id_checked_s = 'AND id NOT IN ('.$id_checked.')';
			}*/
			
		$query = "SELECT * FROM #__djcf_items WHERE address='' "
				."AND latitude!='0.000000000000000' AND longitude!='0.000000000000000' "
				."AND id > ".$id_checked." ORDER BY id LIMIT 1";
				$db->setQuery($query);
				$item = $db->loadObject();
				//echo '<pre>';print_r($item);die();
	
				if($item){					
					$id_checked = $item->id;
					$address= '';
					
					$address= DJClassifiedsGeocode::getAddressLatLon($item->latitude.','.$item->longitude);
					
				//echo $address;die();
					if($address){
						$query = "UPDATE #__djcf_items SET address='".addslashes($address)."'  WHERE id=".$item->id;
						$db->setQuery($query);
						$db->query();
						//echo '<pre>';print_r($db);die();
					}
					echo '<h3>'.JTEXT::_('COM_DJCLASSIFIEDS_GENERATING_ADRESSES').' [id = '.$item->id.']... '.JTEXT::_('COM_DJCLASSIFIEDS_PLEASE_WAIT').'</h3>';
					header("refresh: 0; url=".JURI::base().'index.php?option=com_djclassifieds&task=items.generateAdresses&idc='.$id_checked);
				}else{
					$redirect = 'index.php?option=com_djclassifieds&view=items';
					$app->redirect($redirect, JText::_('COM_DJCLASSIFIEDS_ADDRESSES_GENERATED'));
						
				}
	}	
	
	function moveImagesToFolders(){
		$app = JFactory::getApplication();
		$par = JComponentHelper::getParams( 'com_djclassifieds' );
		JToolBarHelper::title(JText::_('COM_DJCLASSIFIEDS_MOVING_IMAGES'), 'generic.png');
			
		$item_id = JRequest::getInt( 'id', '0' );
			
		$db =  JFactory::getDBO();
		$query = "SELECT * FROM #__djcf_images WHERE item_id >  ".$item_id ." AND type='item' ORDER BY item_id LIMIT 50 ";
		$db->setQuery($query);
		$images = $db->loadObjectList();
		
		if(count($images)){
			foreach($images as $image){				
				$img_c_path = trim($image->path); 
				if(substr($img_c_path,-1,1)=="/"){
					$img_c_path = substr($img_c_path, 0,-1);
				}
				
				$img_path_p = explode('/', $img_c_path);
				
				if(!is_int(end($img_path_p))){
									
					$new_img_path_rel = DJClassifiedsImage::generatePath($par->get('advert_img_path','/components/com_djclassifieds/images/item/'),$image->item_id) ;
					
						$path = JPATH_SITE.$image->path.$image->name;
						$path_new = JPATH_SITE.$new_img_path_rel.$image->name;
						 
						if (JFile::exists($path.'_thb.'.$image->ext)){
							JFile::move($path.'_thb.'.$image->ext,$path_new.'_thb.'.$image->ext ); 	
						}
						if (JFile::exists($path.'_th.'.$image->ext)){
							JFile::move($path.'_th.'.$image->ext,$path_new.'_th.'.$image->ext );
						}
						if (JFile::exists($path.'_thm.'.$image->ext)){
							JFile::move($path.'_thm.'.$image->ext,$path_new.'_thm.'.$image->ext );
						}
						if (JFile::exists($path.'_ths.'.$image->ext)){
							JFile::move($path.'_ths.'.$image->ext,$path_new.'_ths.'.$image->ext );
						}
						if (JFile::exists($path.'.'.$image->ext)){
							JFile::move($path.'.'.$image->ext,$path_new.'.'.$image->ext );
						}
					$query = "UPDATE #__djcf_images SET path='".$new_img_path_rel."' WHERE id =  ".$image->id." ";
					$db->setQuery($query);
					$db->query();
					$item_id = $image->item_id;
				}
			}
		}
	
			
		if (count( $images ) >0) {			
			echo '<h3>'.JTEXT::_('COM_DJCLASSIFIEDS_MOVING_ITEMS').' [id = '.$item_id.']... '.JTEXT::_('COM_DJCLASSIFIEDS_PLEASE_WAIT').'</h3>';
			header("refresh: 0; url=".JURI::base().'index.php?option=com_djclassifieds&task=items.moveImagesToFolders&id='.$item_id);
		//die('aaa');
		}else{
			$this->setRedirect( 'index.php?option=com_djclassifieds&view=items', JText::_('COM_DJCLASSIFIEDS_IMAGES_MOVED') );
		}
		//$redirect = 'index.php?option=com_djclassifieds&view=items';
		//$app->redirect($redirect, JText::_('COM_DJCLASSIFIEDS_THUMBNAILS_RECREATED'));
	}
	
	public function exportItems(){
		
		$app 	= JFactory::getApplication();
		$task 	= $this->getTask();
		$db 	=  JFactory::getDBO();
		$cid	= $app->input->get('cid', array(), 'array');
		
		jimport('joomla.application.component.modellist');				
		JLoader::import('items', JPATH_COMPONENT_ADMINISTRATOR.'/models/');
		$model = JModelList::getInstance('Items', 'DjclassifiedsModel');
		//echo '<pre>';print_r($model);die();
		
		
		$state = $model->getState();
		$context = 'com_djclassifieds.items';
		
		$start = $app->input->get('start', 0);
		$limit = 1000;
		if (count($cid) > 0) {
			$limit = $start = 0;
			JArrayHelper::toInteger($cid);
			$model->setState('filter.ids', implode(',',$cid));
		} else {	
			$search = $model->getUserStateFromRequest($context.'.filter.search', 'filter_search');
			$model->setState('filter.search', $search);
				
			$published = $model->getUserStateFromRequest($context.'.filter.published', 'filter_published', '');
			$model->setState('filter.published', $published);
				
			$category = $model->getUserStateFromRequest($context.'.filter.category', 'filter_category', '');
			$model->setState('filter.category', $category);
			
			$category = $model->getUserStateFromRequest($context.'.filter.active', 'filter_active', '');
			$model->setState('filter.active', $category);
		}
		
		$model->setState('list.start', $start);
		$model->setState('list.limit', $limit);
		
		$params = JComponentHelper::getParams('com_djcatalog2');
		$model->setState('params', $params);
		
		$items = $model->getItems();
		
		if(count($items)){
			
			
			$query = "SELECT * FROM #__djcf_types ";
			$db->setQuery($query);
			$types = $db->loadObjectList('id');
			
			$query = "SELECT * FROM #__djcf_fields ";
			$db->setQuery($query);
			$fields = $db->loadObjectList();
			
			$id_list= '';
			foreach($items as $item){
				$id_list .= ($id_list) ? ','.$item->id : $item->id;
			}
			
			
			$query ="SELECT * FROM #__djcf_fields_values WHERE item_id IN (".$id_list.") ";
			$db->setQuery($query);
			$fields_values =$db->loadObjectList();
			
			//echo '<pre>';print_r($fields);die();
			
			$itemsXML = new SimpleXMLElement("<items></items>");
			foreach($items as $item){
				$itemXML = $itemsXML->addChild('item');
				$itemXML->addChild('id',$item->id);
				$itemXML->addChild('start_date',$item->date_start);
				$itemXML->addChild('end_date',$item->date_exp);
				$itemXML->addChild('title',$item->name);
				$itemXML->addChild('short_description',$item->intro_desc);
				$itemXML->addChild('description',htmlspecialchars($item->description));
				$itemXML->addChild('price',$item->price);
				$itemXML->addChild('currency',$item->currency);
				$itemXML->addChild('video',$item->video);
				$itemXML->addChild('website',$item->website);
				$itemXML->addChild('hits',$item->display);
				
				if($item->type_id){
					$type = $itemXML->addChild('type',$types[$item->type_id]->name);
					$type->addAttribute('id',$item->type_id);
				}else{
					$itemXML->addChild('type');
				}
				
				$location = $itemXML->addChild('location');
				$location->addChild('address',$item->address);
				$location->addChild('postal_code',$item->post_code);
				
				if($item->region_id){
					$reg_path = DJClassifiedsRegion::getParentPath($item->region_id);
					$reg_path = array_reverse($reg_path);
				
					for($ri=0;$ri<5;$ri++){
						$rii= $ri+1;
						if(isset($reg_path[$ri])){
							$region = $location->addChild('region'.$rii, $reg_path[$ri]->name);
							$region->addAttribute(id,$reg_path[$ri]->id);
							$region->addAttribute(country,$reg_path[$ri]->country);
							$region->addAttribute(city,$reg_path[$ri]->city);
						}else{
							$location->addChild('region'.$rii);
						}
					}
				
				}else{
					for($ri=1;$ri<6;$ri++){
						$location->addChild('region'.$ri);
					}
				}												
				$itemXML->addChild('contact',$item->contact);
				$contact_fields = $itemXML->addChild('contact_specification');
				foreach ($fields as $field){
					if($field->source==1){						
						foreach($fields_values as $fv){
							if($fv->item_id==$item->id && $fv->field_id==$field->id){
								$f_value = $fv->value;
								if($field->type=='date'){
									$f_value = $fv->value_date;
								}
								if(substr($f_value, 0,1)==';'){
									$f_value = substr($f_value, 1);
								}
								if(substr($f_value, -1,1)==';'){
									$f_value = substr($f_value, 0,-1);
								}
								$f_value = str_ireplace(';', ', ', $f_value);
								$contact_fields->addChild($field->name,$f_value);
								break;
							}
						}
					}
				}
				
				if($item->cat_id){
					$cat_path = DJClassifiedsCategory::getParentPath(0,$item->cat_id);
					$cat_path = array_reverse($cat_path);
				
					for($ci=0;$ci<5;$ci++){
						$cii= $ci+1;
						if(isset($cat_path[$ci])){
							$category = $itemXML->addChild('category'.$cii, $cat_path[$ci]->name);
							$category->addAttribute(id,$cat_path[$ci]->id);
						}else{
							$itemXML->addChild('category'.$cii);
						}
					}
				
				}else{
					for($ci=1;$ci<6;$ci++){
						$itemXML->addChild('category'.$ci);
					}
				}
				
				$cat_fields = $itemXML->addChild('specification');
				foreach ($fields as $field){
					if($field->source==0){
						foreach($fields_values as $fv){
							if($fv->item_id==$item->id && $fv->field_id==$field->id){
								$f_value = $fv->value;
								if($field->type=='date'){
									$f_value = $fv->value_date;
								}
								if(substr($f_value, 0,1)==';'){
									$f_value = substr($f_value, 1);
								}
								if(substr($f_value, -1,1)==';'){
									$f_value = substr($f_value, 0,-1);
								}
								$f_value = str_ireplace(';', ', ', $f_value);
								$cat_fields->addChild($field->name,$f_value);
								break;
							}
						}
					}
				}
				
				$profileXML = $itemXML->addChild('profile');
				if($item->user_id){
					$profileXML->addChild('user_id',$item->user_id);
					$profileXML->addChild('name',$item->user_name);
					$profileXML->addChild('email',$item->u_email);
				}else{
					$profileXML->addChild('user_id','0');
					$profileXML->addChild('name','');
					$profileXML->addChild('email',$item->email);
				}
				
				$imagesXML = $itemXML->addChild('images');
				if(count($item->images)){
					foreach($item->images as $img){												
						$imge_url = JURI::root();
						if(substr($img->path, 0,1)=='/'){
							$imge_url .= substr($img->path, 1);
						}
						$imge_url .= $img->name.'_thb.'.$img->ext;						
						
						$imagesXML->addChild('image',$imge_url);
					}
				}
				
				
			}
			//echo '<pre>';print_r($items);die();
			//header('Content-type: text/xml');echo $itemsXML->asXML();die();
			$xml_file_name = 'Export_items_'.date("Y-m-d_H_i_s").'.xml';
			$xml_file = JPATH_COMPONENT_ADMINISTRATOR.'/export/'.$xml_file_name;
			$itemsXML->saveXML($xml_file);
			
			$xml_link = '<a target="_blank" href="'.JUri::base().'components/com_djclassifieds/export/'.$xml_file_name.'">'.$xml_file_name.'</a>';
			$this->setRedirect( 'index.php?option=com_djclassifieds&view=items', JText::_('COM_DJCLASSIFIEDS_XML_GENERATED').': '.$xml_link );
			
			
			
		}else{
			$this->setRedirect( 'index.php?option=com_djclassifieds&view=items', JText::_('COM_DJCLASSIFIEDS_NO_ADVERTS_FOR_EXPORT') );
		}
		
		
	}
	
	function deleteExpired($days = '0'){
			if(!is_numeric($days)){
				return false;
			}

			JPluginHelper::importPlugin('djclassifieds');
			$app  = JFactory::getApplication();
			$db   = JFactory::getDBO();
			$dispatcher = JDispatcher::getInstance();
			$par = JComponentHelper::getParams( 'com_djclassifieds' );
			
			$now = date('Y-m-d H:i:s');

			$query = "SELECT * FROM #__djcf_items WHERE DATE_ADD(date_exp, INTERVAL ".$days." DAY) < ".$db->quote($now);
			$db->setQuery($query);
			$items = $db->loadObjectList();
			
			if($items){
				$ids = implode(',', array_column($items, 'id'));
				
				foreach($items as $item){
					$dispatcher->trigger('onBeforeDJClassifiedsDeleteAdvert', array($item));
				}
				
				$query = "SELECT * FROM #__djcf_images WHERE item_id IN (".$ids.") AND type='item'";
				$db->setQuery($query);
				$items_images = $db->loadObjectList('id');
				
				if($items_images){
					foreach($items_images as $item_img){
						$path_to_delete = JPATH_ROOT.$item_img->path.$item_img->name;
						if (JFile::exists($path_to_delete.'.'.$item_img->ext)){
							JFile::delete($path_to_delete.'.'.$item_img->ext);
						}
						
						if($par->get('leave_small_th','0')==0){
							if (JFile::exists($path_to_delete.'_ths.'.$item_img->ext)){
								JFile::delete($path_to_delete.'_ths.'.$item_img->ext);
							}
						}
						if (JFile::exists($path_to_delete.'_thm.'.$item_img->ext)){
							JFile::delete($path_to_delete.'_thm.'.$item_img->ext);
						}
						if (JFile::exists($path_to_delete.'_thb.'.$item_img->ext)){
							JFile::delete($path_to_delete.'_thb.'.$item_img->ext);
						}
					}
				}

				$query = "DELETE FROM #__djcf_items WHERE id IN (".$ids.")";
				$db->setQuery($query);
				if (!$db->execute())
				{
					echo $db->getErrorMsg();
					exit();	
				}
				
				$query = "DELETE FROM #__djcf_fields_values WHERE item_id IN (".$ids.")";
				$db->setQuery($query);
				$db->execute();
				
				$query = "DELETE FROM #__djcf_payments WHERE item_id IN (".$ids.")";
				$db->setQuery($query);
				$db->execute();
				
				$query = "DELETE FROM #__djcf_images WHERE item_id IN (".$ids.") AND type='item'";
				$db->setQuery($query);
				$db->execute();
				
				foreach($items as $item){
					$dispatcher->trigger('onAfterDJClassifiedsDeleteAdvert', array($item));
				}

				$app->enqueueMessage(JText::sprintf('COM_DJCLASSIFIEDS_EXPIRED_ADS_DELETED', count($items)));
			}
	}

	public function batch(){
		$app  = JFactory::getApplication();
	    $db   = JFactory::getDBO();
		$user = JFactory::getUser();
		$redirect = 'index.php?option=com_djclassifieds&view=items';

		if (!$user->authorise('core.admin', 'com_djclassifieds')) {
	    	$this->setError(JText::_('JGLOBAL_BATCH_CANNOT_EDIT'));
	    	$this->setMessage($this->getError(), 'error');
	    	$this->setRedirect($redirect);
	    	return false;
	    }
		
		$id_arr = $app->input->get('cid', array());
		$cat_id = $app->input->getInt('batch_cid', '0');
		$user_id = $app->input->getInt('batch_uid', '0');
		$items_updated = '0';

	    if ($id_arr && ($cat_id || $user_id)){
	        $ids = implode(',', $id_arr);
			
			if($cat_id){
				$query = "UPDATE #__djcf_items SET cat_id=".$cat_id." WHERE id IN (".$ids.")";
				$db->setQuery($query);
				$db->execute();
			}

			if($user_id){
				if($user_id == '-1'){
					$user_id = '0';
				}
				$query = "UPDATE #__djcf_items SET user_id=".$user_id." WHERE id IN (".$ids.")";
				$db->setQuery($query);
				$db->execute();
			}
			
			$items_updated = count($id_arr);
		}		

		$message = JText::sprintf('COM_DJCLASSIFIEDS_BATCH_ITEMS_UPDATED', $items_updated);
	    $app->redirect($redirect, $message);
	}

}