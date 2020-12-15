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


class plgDJClassifiedsMultiCategories extends JPlugin {
	public function __construct(& $subject, $config) {
		parent::__construct ( $subject, $config );
		$this->loadLanguage ();
	}
	
	function onItemEditFormCategory(& $item, $cats, &$par ,$subscr_id) {

		$db	     = JFactory::getDBO();
		$user    = JFactory::getUser();
		$app     = JFactory::getApplication();
		$menus	 = $app->getMenu('site');
		$content = NULL;
		$menus	= $app->getMenu('site');
		$unit_price = $par->get('unit_price','');
		$points_a = $par->get('points',0);
		
		/*$menu_userplans_itemid = $menus->getItems('link','index.php?option=com_djclassifieds&view=userplans',1);
		$userplans_link='index.php?option=com_djclassifieds&view=userplans';
		if($menu_userplans_itemid){
			$userplans_link .= '&Itemid='.$menu_userplans_itemid->id;
		}*/
		
		if($item->id){
			$query = "SELECT * FROM #__djcf_items_categories ic "
					."WHERE ic.item_id =".$item->id." ";
				
			$db->setQuery($query);
			$icats=$db->loadObjectList();
		}
		
		$content = '<div class="djform_row djform_mcat_row" id="djform_mcat0_row" >';
		         	if($par->get('show_tooltips_newad','0')){
			          	$content .= '<label class="label Tips1" for="mcat0_0" id="mcat0_0-lbl" title="'.JTEXT::_('COM_DJCLASSIFIEDS_CATEGORY_TOOLTIP').'">';
			                $content .= JText::_('COM_DJCLASSIFIEDS_ADDITIONAL_CATEGORY');
			                $content .= '<img src="'.JURI::base(true).'/components/com_djclassifieds/assets/images/tip.png" alt="?" />';
			            $content .= '</label>';	                               			                	
					}else{
			            $content .= '<label class="label" for="mcat0_0" id="mcat0_0-lbl">';
			                	  $content .= JText::_('COM_DJCLASSIFIEDS_ADDITIONAL_CATEGORY');					
			            $content .= '</label>';
		            } 

		            $content .= '<div class="djform_field">';
						$cat_sel = '<select autocomplete="off" class="cat_sel" id="mcat0_0" name="mcats0[]" onchange="new_mcat(0,this.value,new Array(),0);getFields(this.value,true);"><option value="">'.JText::_('COM_DJCLASSIFIEDS_PLEASE_SELECT_CATEGORY').'</option>';
						$parent_id=0;	
						foreach($cats as $l){
							if($parent_id!=$l->parent_id){
								break;
							}	
							$l_name = $l->name; 
							if($l->price>0 || ($l->points>0 && $points_a)){
								$l_price = $l->price/100;												
								$l_name .= ' (';
								if($points_a!=2){
									$l_name .= DJClassifiedsTheme::priceFormat($l_price,$unit_price); //$l->name .= DJClassifiedsTheme::priceFormat($l_price,$unit_price);
								}							
									if($l->points>0 && $points_a){
										if($points_a!=2){
											$l_name .= ' - '; //$l->name .= ' - ';
										}
										$l_name .= $l->points.JTEXT::_('COM_DJCLASSIFIEDS_POINTS_SHORT');		
									}
									if($l->price_special>0){
										$l_name .= ' - '.DJClassifiedsTheme::priceFormat($l->price_special,$unit_price).' '.JTEXT::_('COM_DJCLASSIFIEDS_SPECIAL_PRICE_SHORT');
									}														
								$l_name .= ')'; 
							}
							$cat_sel .= '<option value="'.$l->id.'">'.str_ireplace("'", "&apos;", $l_name).'</option>';
						}
						$cat_sel .= '</select>';
						$content .=  $cat_sel;				
						
						$content .= '<button type="button" class="button button_mc_delete" onclick="deleteMultiCat(0);">'.JText::_('COM_DJCLASSIFIEDS_DELETE').'</button>';
						$content .= '<div class="clear_both"></div>';
						$content .= '<div id="after_mcat0_0"></div>';
						
						$content .= '
						<script type="text/javascript">		
						var mcat = 1;
						var mcat_limit = '.$this->params->get('mc_cats_limit','5').';																										
						function new_mcat(parent,a_parent,c_path,mcat_id){
								//console.log(mcat_id);				
							var myRequest = new Request({
							    url: "'.JURI::base().'index.php",
							    method: "post",
								data: {
							      "option": "com_djclassifieds",
							      "view": "additem",
							      "task": "getCategorySelect",
								  "cat_id": a_parent,
							      "mc": mcat_id							  
								  },
							    onRequest: function(){
							    	document.id(\'after_mcat\'+mcat_id+\'_\'+parent).innerHTML = \'<div style="text-align:center;"><img src="'.JURI::base(true).'/components/com_djclassifieds/assets/images/loading.gif" alt="..." /></div>\';
							    	},
							    onSuccess: function(responseText){																
							    	if(responseText){
							    		//console.log(\'after_mcat\'+mcat_id+\'_\'+parent);	
										document.id(\'after_mcat\'+mcat_id+\'_\'+parent).innerHTML = responseText; 
										document.id(\'mcat\'+mcat_id+\'_\'+parent).value=a_parent;
									}else{
										document.id(\'after_mcat\'+mcat_id+\'_\'+parent).innerHTML = "";
										document.id(\'mcat\'+mcat_id+\'_\'+parent).value=a_parent;		
									}	
									if(c_path != "null"){
										if(c_path.length>0){
											var first_path = c_path[0].split(",");							    		
											c_path.shift();
											new_mcat(first_path[0],first_path[1],c_path,mcat_id);												
										}
									}
							    },
							    onFailure: function(){}
							});
							myRequest.send();																							
						}
							
						</script>';
							
						
						if($item->id>0){
							$query ="SELECT * FROM #__djcf_items_categories "
									."WHERE item_id  = ".$item->id." ORDER BY ordering ";
							$db->setQuery($query);
							$icats =$db->loadObjectList();							
							if($icats){
								$content .= '<script type="text/javascript">
										window.addEvent(\'domready\', function(){';
								for($ic=0;$ic<count($icats)-1;$ic++){
									$content .= 'addMultiCat();';																												
								}												
								
								
								$icc=0;
								foreach($icats as $icat){
									$cat_path = array();
									$c_name = '';
									if($cats){
										$id = Array();
										$cid = $icat->cat_id;
										if($cid!=0){
											while($cid!=0){
												foreach($cats as $li){
													if($li->id==$cid){
														$cid=$li->parent_id;
														$id[]=$li->id;													
														$cat_path[] = $li->parent_id.','.$li->id;
														break;
													}
												}
											//	if($cid==$item->cat_id){ break; }
											}
										}
										if(count($cat_path)){											
												$c_path = $cat_path;
												$content .= 'var cat_path'.$icc.' = new Array();';
												$cat_path_f = 'new_mcat(';
												for($c=count($c_path)-1;$c>-1;$c--){
													if($c<count($c_path)-1){
														$ci = count($c_path) - $c -2;
														$content .= "cat_path".$icc."[$ci]='$c_path[$c]';";
													}else{
														$cat_path_f .= $c_path[$c];
													}
												}
												$content .= $cat_path_f.',cat_path'.$icc.','.$icc.');';	
												$icc++;
										}										
									}
									//echo '<pre>';echo $content;print_r($cat_path);die();									
									
								}

								$content .= 'getFields('.$item->cat_id.',true);
											})</script>';  								
							}
						}
							
						 /* if(count($this->cat_path)){
								$c_path = $this->cat_path;
								echo 'var cat_path = new Array();';
								$cat_path_f = 'new_cat(';
								for($c=count($c_path)-1;$c>-1;$c--){
									if($c<count($c_path)-1){
										$ci = count($c_path) - $c -2;
										echo "cat_path[$ci]='$c_path[$c]';";
									}else{
										$cat_path_f .= $c_path[$c];
									}
								}			
								echo $cat_path_f.',cat_path);';																
							} */
							
							
		                $content .= '</div>';
		                $content .= '<div class="clear_both"></div>';
		            $content .= '</div>';
		
		            $content .= '<div id="after_mcat_all" class="djform_row"></div>';
		            $content .= '<input type="hidden" name="mcat_limit" id="mcat_limit" value="1" />';
		            
			            if($this->params->get('mc_cats_limit','5')>1){
			            	$content .= '<div class="djform_row" id="djform_row_new_mcat"  >';
			            }else{
			            	$content .= '<div class="djform_row" id="djform_row_new_mcat" style="display:none" >';
			            }		            			           
				            	$content .= '<label class="label">&nbsp;</label>';
				            	$content .= '<div class="djform_field">';
				            		$content .= '<button type="button" class="button new_mcat" onclick="addMultiCat()" >'.JText::_('COM_DJCLASSIFIEDS_ADD_NEW_CATEGORY').'</button>';
				            	$content .= '</div>'; 
				            	$content .= '<div class="clear_both"></div>';
			           		 $content .= '</div>';
		            
		            
		            
		            
		            
		           
		            
		            
		            
		            
		            //var newcat_content = \'<div class="djform_row djform_mcat_row" id="djform_mcat\'+mcat+\'_row" >\';';
		            $content .= '
		            <script type="text/javascript">			            
			            function addMultiCat(){
							     				            	
		            		 var newcat_content = \'\';';
		            		
		            		//var newcat_content = \'<div class="djform_row djform_mcat_row" id="djform_mcat\'+mcat+\'_row" >\';';
		            
					            if($par->get('show_tooltips_newad','0')){ 
					            	$content .= ' newcat_content  += \'<label class="label Tips1" for="mcat\'+mcat+\'_0" id="mcat\'+mcat+\'_0-lbl" title="'.addslashes(JTEXT::_('COM_DJCLASSIFIEDS_CATEGORY_TOOLTIP')).'">\';';
					            	$content .= ' newcat_content  += \''.addslashes(JText::_('COM_DJCLASSIFIEDS_ADDITIONAL_CATEGORY')).'\';';
					            	$content .= ' newcat_content  += \'<img src="'.JURI::base(true).'/components/com_djclassifieds/assets/images/tip.png" alt="?" />\';';
					            	$content .= ' newcat_content  += \'</label>\';';
					            }else{
					            	$content .= ' newcat_content  += \'<label class="label" for="mcat\'+mcat+\'_0" id="mcat\'+mcat+\'_0-lbl">\';';
					            	$content .= ' newcat_content  += \''.addslashes(JText::_('COM_DJCLASSIFIEDS_ADDITIONAL_CATEGORY')).'\';';
					            	$content .= ' newcat_content  += \'</label>\';';
					            }
					            
					            $content .= ' newcat_content  += \'<div class="djform_field">\';					            							            		
					            		';
					            
						            $content .= 'newcat_content  += \'<select autocomplete="off" class="cat_sel" id="mcat\'+mcat+\'_0"  style="width:210px" name="mcats\'+mcat+\'[]" onchange="new_mcat(0,this.value,new Array(),\'+mcat+\');getFields(this.value,true);"><option value="">'.addslashes(JText::_('COM_DJCLASSIFIEDS_PLEASE_SELECT_CATEGORY')).'</option>\';';
						            $parent_id=0;
						            $cat_sel = '';
						            foreach($cats as $l){
						            	if($parent_id!=$l->parent_id){
						            		break;
						            	}
						            	$l_name = $l->name;
						            	if($l->price>0){
						            		$l_price = $l->price/100;
						            		$l_name .= ' ('.DJClassifiedsTheme::priceFormat($l_price,$unit_price);
						            		if($l->points>0 && $points_a){
						            			$l_name .= ' - '.$l->points.JTEXT::_('COM_DJCLASSIFIEDS_POINTS_SHORT');
						            		}
						            		if($l->price_special>0){
						            			$l_name .= ' - '.DJClassifiedsTheme::priceFormat($l->price_special,$unit_price).' '.JTEXT::_('COM_DJCLASSIFIEDS_SPECIAL_PRICE_SHORT');
						            		}
						            		$l_name .= ')';
						            	}
						            	$cat_sel .= '<option value="'.$l->id.'">'.str_ireplace("'", "&apos;", $l_name).'</option>';
						            }
						            $cat_sel .= '</select>';
						            $content .= ' newcat_content  += \''.$cat_sel.'\';';
						            $content .= ' newcat_content  += \'<button type="button" class="button button_mc_delete" onclick="deleteMultiCat(\'+mcat+\');">'.JText::_('COM_DJCLASSIFIEDS_DELETE').'</button>\';
						             
						            			  newcat_content  += \'<div class="clear_both"></div>\';		            		            		            		            
						            			  newcat_content  += \'<div id="after_mcat\'+mcat+\'_0"></div>\';		            		           		            			         		            	
					            			newcat_content  += \'</div>\';
				            				newcat_content  += \'<div class="clear_both"></div>\';
				            		//newcat_content += \'</div>\';
						            //console.log(newcat_content);		            
						            //document.id("after_mcat_all").innerHTML = document.id("after_mcat_all").innerHTML + newcat_content;

						            var newcat_content_el = new Element(\'div\',{
								    						\'id\': \'djform_mcat\'+mcat+\'_row\',
								    						\'class\': \'djform_row djform_mcat_row\' });
		
					            	newcat_content_el.set(\'html\', newcat_content);
						            		
					            	newcat_content_el.inject(document.id("after_mcat_all"));		
						            		
						            mcat ++ ;
						            document.id("mcat_limit").value = mcat;	
						            var mcat_count = document.getElements(".djform_mcat_row").length;					       	     						            
						            if(mcat_count>=mcat_limit){
						            	document.id("djform_row_new_mcat").setStyle("display","none");	
						            }		       				
						            					
						}
						            		
						            		 
						function deleteMultiCat(del_cat_id){
							document.id(\'djform_mcat\'+del_cat_id+\'_row\').destroy();		         		
						    //mcat -- ;
						    var mcat_count = document.getElements(".djform_mcat_row").length;
				            if(mcat_count<mcat_limit){
				            	document.id("djform_row_new_mcat").setStyle("display","block");	
				            }	
						    getFields(-1);
						}            		
						            		
		            </script>				            				            		
		            ';		
		            
		return $content;
		
	}
	
	
	
	
	
	function onAfterDJClassifiedsSaveAdvert(&$row,$is_new){
		$db	    = JFactory::getDBO();
		$user   = JFactory::getUser();
		$app    = JFactory::getApplication();
		$mcat_limit = JRequest::getInt('mcat_limit',0);
		if($mcat_limit>0){
						
			if(!$is_new){
				$query = "DELETE FROM #__djcf_items_categories WHERE item_id= ".$row->id." ";
				$db->setQuery($query);
				$db->query();
			}						
			
			$cat_ord = 0;
			$query = "INSERT INTO #__djcf_items_categories(`item_id`,`cat_id`,`ordering`) VALUES ";
			
			for($mi=0;$mi<$mcat_limit;$mi++){
				$mcat = $app->input->get('mcats'.$mi,array(),'ARRAY');
				if(count($mcat)){
					
					$mc = intval(str_ireplace('p', '', end($mcat)));
					if($mc>0){						
						$query .= "('".$row->id."','".$mc."','".$cat_ord."'), ";
						$cat_ord ++;
					}															
				}
			}
			
			if($cat_ord>0){
				$query = substr($query, 0, -2).';';
				$db->setQuery($query);
				$db->query();
			}
								
		
		}
		return NULL;
	}
	
	
	function onAdminItemEditCategory($item){
		$db	    = JFactory::getDBO(); 
		$user   = JFactory::getUser();
		$app    = JFactory::getApplication();
		
		$active_mcats = array();
		if($item->id){
			$query = "SELECT * FROM #__djcf_items_categories ic "
					."WHERE ic.item_id =".$item->id." ";
		
			$db->setQuery($query);
			$icats=$db->loadObjectList();
			if(count($icats)){
				foreach($icats as $ic){
					$active_mcats[] = $ic->cat_id;
				}
			}
		}
		
		$content = '<div class="control-group">';		
			$content .= '<div class="control-label">'.JText::_('COM_DJCLASSIFIEDS_ADDITIONAL_CATEGORIES').'</div>';
			$content .= '<div class="controls">';
				$content .= '<select autocomplete="off" multiple size="10" id="mcat_ids" name="mcat_ids[]" class="inputbox" onchange="getFields(this.value,true)" >';
					$content .= '<option value="">'.JText::_('COM_DJCLASSIFIEDS_SELECT_CATEGORIES').'</option>';
					$content .= JHtml::_('select.options', DJClassifiedsCategory::getCatSelect(), 'value', 'text', $active_mcats, true);
				$content .= '</select>';
			$content .= '</div>';
		$content .= '</div>';
		
		return $content;
		
	}
	
	function onAfterAdminDJClassifiedsSaveAdvert($row,$is_new){
		
		$db	    = JFactory::getDBO();
		$user   = JFactory::getUser();
		$app    = JFactory::getApplication();		
		$mcat = $app->input->get('mcat_ids',array(),'ARRAY');
	 	
			if(!$is_new){
				$query = "DELETE FROM #__djcf_items_categories WHERE item_id= ".$row->id." ";
				$db->setQuery($query);
				$db->query();
			}

			
			if(count($mcat)){
				$cat_ord = 0;
				$query = "INSERT INTO #__djcf_items_categories(`item_id`,`cat_id`,`ordering`) VALUES ";								
					
				for($mi=0;$mi<count($mcat);$mi++){								
					$query .= "('".$row->id."','".$mcat[$mi]."','".$cat_ord."'), ";
					$cat_ord ++;					
				}
							
				$query = substr($query, 0, -2).';';
				$db->setQuery($query);
				$db->query();				
			
			}
							
	
		
		return NULL;
	}
	
	
}


