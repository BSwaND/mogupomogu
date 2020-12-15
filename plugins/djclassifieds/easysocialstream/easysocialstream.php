<?php
/**
* @version 2.0
* @package DJ Classifieds
* @subpackage DJ Classifieds EasySocial Stream
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
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.plugin.plugin');

require_once( JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php' );
require_once(JPATH_ADMINISTRATOR.'/components/com_djclassifieds/lib/djimage.php');
require_once(JPATH_ADMINISTRATOR.'/components/com_djclassifieds/lib/djseo.php');
require_once(JPATH_ADMINISTRATOR.'/components/com_djclassifieds/lib/djsocial.php');

class plgDJClassifiedsEasysocialstream extends JPlugin{

	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	public function onAfterDJClassifiedsSaveAdvert( $row, $is_new ){
		$app	  = JFactory::getApplication();
		$db		  = JFactory::getDBO();
		$category = '';
		$catslug  = '0:all';		
		$pluginParams	= $this->params; 

		if($row->user_id && ($is_new==1 || $pluginParams->get('stream_on_edit', 0)==1)){

			$stream     = Foundry::stream();
			$template   = $stream->getTemplate();
			$actor 		= Foundry::user( $row->user_id  );
			
			$template->setActor( $actor->id , 'user' );
			 
			//echo '<pre>';print_R($actor);die();
			/*
			$profile_link = DJClassifiedsSocial::getUserProfileLink($row->user_id,'easysocial'); 
			
			$item_title = '<a href="'.$profile_link.'">'.$actor->name.'</a> ';

			if($row->cat_id){
				$query = "SELECT * FROM #__djcf_categories WHERE id='".$row->cat_id."' LIMIT 1";
			 	$db->setQuery($query);
			 	$category = $db->loadObject();	
				$catslug = $category->id.':'.$category->alias;
			}		 	
			$ad_link = DJClassifiedsSEO::getItemRoute($row->id.':'.$row->alias,$catslug);

			if($is_new){
				//$act->cmd = 'djclassifieds.newadvert';
				$item_title .= JText::_('PLG_DJCLASSIFIEDS_EASYSOCIALSTREAM_ADDED_ADVERT');
				$item_content = JText::_('PLG_DJCLASSIFIEDS_EASYSOCIALSTREAM_NEW_ADVERT');	
			}else{
				//$act->cmd = 'djclassifieds.editadvert';
				$item_title .= JText::_('PLG_DJCLASSIFIEDS_EASYSOCIALSTREAM_EDITED_ADVERT');
				$item_content = JText::_('PLG_DJCLASSIFIEDS_EASYSOCIALSTREAM_CHANGED_ADVERT');
			}
			//echo $item_title;die();
			$item_content .= ' <a href="'.$ad_link.'">'.$row->name.'</a><br />';
			$item_content .= $row->intro_desc;
			
			$item_imgs = DJClassifiedsImage::getAdsImages($row->id);	
																					
			if($item_imgs){
				$item_content .= '<br /><br />';				
				//echo '<pre>';print_r($item_imgs);die();
				for($i=0; $i<count($item_imgs); $i++){
					if($i==3){break;} 
					$item_content .= '<a style="margin-right:10px" href="'.$ad_link.'"><img src="'.JURI::base().$item_imgs[$i]->thumb_s.'" alt="" /></a>'; 
				}	
			}
			*/
			$item_title = '';
			$item_content = $is_new ? 'new:'.$row->id : 'edit:'.$row->id;

			$template->setTitle( $item_title );
			$template->setType( 'full' );
			$template->setContent( $item_content );

			$template->setContext( $row->id , 'djclassifieds' );
			$template->setVerb( 'create' );

			//$template->setLocation('http://www.google.pl');
			//$template->setSideWide( true )

			$stream->add( $template );
			
			//echo '<pre>';print_r($template);die();
			//$my         = Foundry::user();
		}
		return true;
	}

	public function onAfterDJClassifiedsDeleteAdvert($item){
		$app	  = JFactory::getApplication();								
		$stream     = Foundry::stream();
		$stream->delete( $item->id , 'djclassifieds' );
		
		return true;
	}

	function onPrepareDJClassifiedsStreamItem(&$item)
	{
		$data_arr = explode(':', $item->content);

		if(empty($data_arr) || empty($data_arr[1]) || !is_numeric($data_arr[1])){
			return false;
		}

		$is_new = $data_arr[0] == 'new' ? true : false;
		$item_id = $data_arr[1];

		$db = JFactory::getDBO();
		$query = "SELECT i.*, c.alias c_alias, u.username u_username, u.name u_name "
		."FROM #__djcf_items i "
		."INNER JOIN #__djcf_categories c ON i.cat_id=c.id "
		."INNER JOIN #__users u ON i.user_id=u.id "
		."WHERE i.id=".$item_id;
		$db->setQuery($query);
		$row = $db->loadObject();

		$profile_link = DJClassifiedsSocial::getUserProfileLink($row->user_id, 'easysocial');
		$item_title = '<a href="'.$profile_link.'">'.$row->u_name.'</a> ';
		$ad_link = DJClassifiedsSEO::getItemRoute($row->id.':'.$row->alias,$row->cat_id.':'.$row->c_alias);

		if($is_new){
			$item_title .= JText::_('PLG_DJCLASSIFIEDS_EASYSOCIALSTREAM_ADDED_ADVERT');
			$item_content = JText::_('PLG_DJCLASSIFIEDS_EASYSOCIALSTREAM_NEW_ADVERT');	
		}else{
			$item_title .= JText::_('PLG_DJCLASSIFIEDS_EASYSOCIALSTREAM_EDITED_ADVERT');
			$item_content = JText::_('PLG_DJCLASSIFIEDS_EASYSOCIALSTREAM_CHANGED_ADVERT');
		}

		$item_content .= ' <a href="'.$ad_link.'">'.$row->name.'</a><br />';
		$item_content .= $row->intro_desc;
		
		$item_imgs = DJClassifiedsImage::getAdsImages($row->id);	
																				
		if($item_imgs){
			$item_content .= '<br /><br />';				
			for($i=0; $i<count($item_imgs); $i++){
				if($i==3){break;} 
				$item_content .= '<a style="margin-right:10px" href="'.$ad_link.'"><img src="'.JURI::base().$item_imgs[$i]->thumb_s.'" alt="" /></a>'; 
			}	
		}

		$item->title = $item_title;
		$item->content_raw = $item_content;
	}
}