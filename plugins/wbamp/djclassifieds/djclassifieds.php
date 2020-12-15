<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     wbAmp
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     1.6.0.607
 * @date        2016-10-31
 */

// no direct access
defined('_JEXEC') or die;

class Plgwbampdjclassifieds extends JPlugin
{
	/**
	 * Build up an array of meta data that can be json_encoded and output
	 * directly to the page
	 *
	 * @param $data
	 * @return array
	 */
	public function onWbampGetJsonldData($context, &$rawJsonLd, $request, $data)
	{
		$par 	  	= JComponentHelper::getParams( 'com_djclassifieds' );
		if ('com_djclassifieds' != $context)
		{
			return true;
		}
		
		require_once(JPATH_BASE.DS.'administrator'.DS.'components'.DS.'com_djclassifieds'.DS.'lib'.DS.'djimage.php');
		require_once(JPATH_BASE.DS.'administrator'.DS.'components'.DS.'com_djclassifieds'.DS.'lib'.DS.'djseo.php');
		require_once(JPATH_BASE.DS.'administrator'.DS.'components'.DS.'com_djclassifieds'.DS.'lib'.DS.'djtheme.php');
	
		
		// start with current
		$jsonld = $rawJsonLd;

		try
		{
			// find article data
			$view = $request->getCmd('view');
			$task = $request->getCmd('task');
			$id = $request->getInt('id');
			$cid = $request->getInt('cid');
			
			if ($task != '') {
				$jsonld = array();
			} else {
				if ($view == 'items') {
						//$jsonld = array();
						$jsonld['@context'] = 'https://schema.org';
						$jsonld['@type'] = 'CollectionPage';
						if (isset($jsonld['publisher'])){
							unset($jsonld['publisher']);
						}
						if (isset($jsonld['headline'])){
							unset($jsonld['headline']);
						}
						if (isset($jsonld['image'])){
							unset($jsonld['image']);
						}
						
						$cat_id	  = JRequest::getVar('cid', 0, '', 'int');							
						$catlist = ''; 
						if($cat_id>0){
							$cats= DJClassifiedsCategory::getSubCatIemsCount($cat_id,1,$par->get('subcats_ordering', 'ord'),$par->get('subcats_hide_empty', 0));
							$catlist= $cat_id;			
							foreach($cats as $c){
								$catlist .= ','. $c->id;
							}				
						}else{
							$cats= DJClassifiedsCategory::getCatAllItemsCount(1,$par->get('subcats_ordering', 'ord'),$par->get('subcats_hide_empty', 0));
						}
						
						$model = JModelLegacy::getInstance('Items', 'DjclassifiedsModel');
						$subcats = '';
						$cat_images='';
						foreach($cats as $c){
							if($c->parent_id==$cat_id){
								$subcats .= $c->id.',';	
							}
						}		
						if($subcats){
							$subcats = substr($subcats, 0, -1); 
						}						
						
						//$model->getState();
						$items= $model->getItems($catlist);
						
						$jsonld['mainContentOfPage'] = array();
						$jsonld['mainContentOfPage']['@type'] = 'WebPageElement';
						$jsonld['mainContentOfPage']['@graph'] = array();																		
						
						$u = JURI::getInstance( JURI::root() );					
						if($u->getScheme()){
							$base_link = $u->getScheme().'://';
						}else{
							$base_link = 'http://';
						}	
						$base_link .= $u->getHost();
						
						foreach($items as $item) {
							$jsonItem = array();
							$jsonItem['@type'] = 'SomeProducts';
							$jsonItem['name'] = $item->name;
							$jsonItem['url'] = $base_link.JRoute::_(DJClassifiedsSEO::getItemRoute($item->id.':'.$item->alias,$item->cat_id.':'.$item->c_alias));
							
							/*if ($item->item_image) {
								$jsonItem['image'] = DJCatalog2ImageHelper::getImageUrl($item->image_fullpath, 'fullscreen');
							}*/
							
							//echo '<pre>';print_r($item);die();
							
							if (count($item->images)) {
								$img_info = getimagesize(JPATH_ROOT.$item->images[0]->thumb_b);
								if(isset($img_info[0])){
									$img_w_h = (isset($img_info[3])? $img_info[3] : '' );
									$jsonItem['image'] = array();
									$jsonItem['image']['@type'] = 'ImageObject'; 
									$jsonItem['image']['url'] = JURI::base().substr($item->images[0]->thumb_b,1);
									$jsonItem['image']['width'] = $img_info[0]; 
									$jsonItem['image']['height'] = $img_info[1];  									
								} 
								$jsonItem['offers'] = array();
								$jsonItem['offers']['@type'] = 'Offer';
								$jsonItem['offers']['price'] = $item->price;
								$jsonItem['offers']['priceCurrency'] = $item->currency;
							}
							
							$jsonld['mainContentOfPage']['@graph'][] = $jsonItem;
						}
						
						/*
						if (!empty($items)) {
							$category=$model->getCategory($item->cat_id);
							$jsonld['@type'] = 'Product';
							if($category->schema_type){
								$jsonld['@type'] = $category->schema_type; 	
							}
							$item_images = DJClassifiedsImage::getAdsImages($item->id);
							
							$profile='';
							if($item->user_id){
								$profile =$model->getProfile($item->user_id);				
							}
							//echo '<pre>';print_r($item_images);die();
							
							$jsonld['name'] = $item->name;
							$u = JURI::getInstance( JURI::root() );					
							if($u->getScheme()){
								$base_link = $u->getScheme().'://';
							}else{
								$base_link = 'http://';
							}	
							$base_link .= $u->getHost();
							
							$correct_link = $base_link.JRoute::_(DJClassifiedsSEO::getItemRoute($item->id.':'.$item->alias,$item->cat_id.':'.$item->c_alias));
							$jsonld['url'] = $correct_link;
													
							if ($item_images) {
								$img_info = getimagesize(JPATH_ROOT.$item_images[0]->thumb_b);
								if(isset($img_info[0])){
									$img_w_h = (isset($img_info[3])? $img_info[3] : '' );
									$jsonld['image'] = array();
									$jsonld['image']['@type'] = 'ImageObject'; 
									$jsonld['image']['url'] = JURI::base().substr($item_images[0]->thumb_b,1);
									$jsonld['image']['width'] = $img_info[0]; 
									$jsonld['image']['height'] = $img_info[1];  
									
								} 
							}																	
							
							$jsonld['offers'] = array();
							$jsonld['offers']['@type'] = 'Offer';
							$jsonld['offers']['price'] = $item->price;
							$jsonld['offers']['priceCurrency'] = $item->currency;
						}*/
				}else if ($view == 'item') {
					//$jsonld = array();
					$jsonld['@context'] = 'https://schema.org';
					$jsonld['@type'] = 'Product';
					if (isset($jsonld['publisher'])){
						unset($jsonld['publisher']);
					}
					if (isset($jsonld['headline'])){
						unset($jsonld['headline']);
					}
					if (isset($jsonld['image'])){
						unset($jsonld['image']);
					}
					
					$model = JModelLegacy::getInstance('Item', 'DjclassifiedsModel');
					//$model->getState();
					$item = $model->getItem();
					
					if (!empty($item)) {
						$category=$model->getCategory($item->cat_id);
						$jsonld['@type'] = 'Product';
						if($category->schema_type){
							$jsonld['@type'] = $category->schema_type; 	
						}
						$item_images = DJClassifiedsImage::getAdsImages($item->id);
						
						$profile='';
						if($item->user_id){
							$profile =$model->getProfile($item->user_id);				
						}
						//echo '<pre>';print_r($item_images);die();
						
						$jsonld['name'] = $item->name;
						$u = JURI::getInstance( JURI::root() );					
						if($u->getScheme()){
							$base_link = $u->getScheme().'://';
						}else{
							$base_link = 'http://';
						}	
						$base_link .= $u->getHost();
						
						$correct_link = $base_link.JRoute::_(DJClassifiedsSEO::getItemRoute($item->id.':'.$item->alias,$item->cat_id.':'.$item->c_alias));
						$jsonld['url'] = $correct_link;
												
						if ($item_images) {
							$img_info = getimagesize(JPATH_ROOT.$item_images[0]->thumb_b);
							if(isset($img_info[0])){
								$img_w_h = (isset($img_info[3])? $img_info[3] : '' );
								$jsonld['image'] = array();
								$jsonld['image']['@type'] = 'ImageObject'; 
								$jsonld['image']['url'] = JURI::base().substr($item_images[0]->thumb_b,1);
								$jsonld['image']['width'] = $img_info[0]; 
								$jsonld['image']['height'] = $img_info[1];  
								
							} 
						}																	
						
						$jsonld['offers'] = array();
						$jsonld['offers']['@type'] = 'Offer';
						$jsonld['offers']['price'] = $item->price;
						$jsonld['offers']['priceCurrency'] = $item->currency;
					}
				}
			}

			//update with our changes
			$rawJsonLd = $jsonld;
		}
		catch (Exception $e)
		{
			ShlSystem_Log::error('wbamp', __METHOD__ . ' ' . $e->getMessage());
		}

		return true;
	}
}
