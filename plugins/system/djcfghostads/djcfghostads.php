<?php
/**
 * @package     LOGman
 * @copyright   Copyright (C) 2011 - 2016 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

/**
 * DOCman LOGman plugin.
 *
 * Wires DOCman loggers to Files and DOCman components controllers.
 *
 * Also provides event handlers for DOCman 1.x.
 *
 * @author  Arunas Mazeika <https://github.com/amazeika>
 * @package Joomlatools\Plugin\LOGman
 */
class PlgSystemDJCFGhostAds extends JPlugin
{
    protected static $_ghost = array();
	
	public function __construct(&$subject, $config) {

		parent::__construct($subject, $config);

		$this->loadLanguage();
	}
	
	function onAdminPrepareSidebar() {
		
		$result = array (
			array (
				'label' => JText::_ ( 'COM_DJCLASSIFIEDS_GHOSTADS' ),
				'link' => 'index.php?option=com_djclassifieds&view=ghostads',
				'view' => 'ghostads' 
			)
		);
		
		return $result;
	}
	
	public function onAfterRoute() {
		
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		
		if($app->isSite() && $app->input->get('option') == 'com_djclassifieds' && $app->input->get('view') == 'item') {
			
			$id = (int)$app->input->getInt('id');
			
			// if advert exists do nothing
			$db->setQuery('SELECT id FROM #__djcf_items WHERE id='.$id.' LIMIT 1');
			if($db->loadResult()) return true;
			
			// if ghost advert doesn't exist do nothing
			$db->setQuery('SELECT id FROM #__djcf_ghostads WHERE item_id='.$id.' LIMIT 1');
			if(!$db->loadResult()) return true;
			
			// advert doesn't exist and ghost advert exist, so redirect to ghost advert view
			$app->enqueueMessage(JText::_('PLG_SYSTEM_DJCFGHOSTADS_REDIRECT_TO_GHOST_AD_MSG'), 'error');			
			
			$uri = JURI::getInstance();
			$uri->setVar('view', 'ghostad');
			$app->redirect($uri->toString());
		}
		
		return true;
	}
	
	public function onBeforeDJClassifiedsDeleteAdvert($item) {
		
		// create Ghost Ad before deleting advert 	
		JTable::addIncludePath(JPATH_ROOT.'/administrator/components/com_djclassifieds/tables');
		$ghost = JTable::getInstance('GhostAds', 'DJClassifiedsTable');
		
		$ghost->item_id = $item->id;
		$ghost->cat_id = $item->cat_id;
		$ghost->user_id = $item->user_id;
		$ghost->name = $item->name;
		$ghost->alias = $item->alias;
		$ghost->date_start = $item->date_start;
		$ghost->date_exp = $item->date_exp;
		$ghost->access_view = $item->access_view;
		$ghost->blocked = $item->blocked;
		$ghost->content = $this->_getGhostContent($item);
		$ghost->deleted = JFactory::getDate()->toSql();
		$ghost->deleted_by = JFactory::getUser()->id;
				
		self::$_ghost[] = $ghost;
	}
	
	public function onAfterDJClassifiedsDeleteAdvert($item) {
		
		// save Ghost Ad after successfull delete action
		if(count(self::$_ghost)) {
			
			foreach (self::$_ghost as $ghost) {
				if(!$ghost->store()) {
					$app->enqueueMessage('Ghost Advert creation failed', 'warning');
				}
			}
		}
	}
	
	protected function _getGhostContent($item) {
		
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$params = JComponentHelper::getParams( 'com_djclassifieds' );
		
		$theme = $params->get('theme', 'default');
		
		$tplPath = JPath::clean(JPATH_ROOT.'/components/com_djclassifieds/themes/'.$theme.'/views/ghostad/template.php');
		if(!JFile::exists($tplPath)) {
			$tplPath = JPath::clean(JPATH_ROOT.'/components/com_djclassifieds/themes/default/views/ghostad/template.php');
		}
		if(!JFile::exists($tplPath)) {
			$tplPath = JPath::clean(JPATH_ROOT.'/components/com_djclassifieds/views/ghostad/tmpl/template.php');
		}
		
		if(!JFile::exists($tplPath)) {
			$app->enqueueMessage(JText::sprintf('PLG_SYSTEM_DJCFGHOSTADS_MISSING_TEMPLATE_FILE_MSG', $tplPath), 'warning');
			return null;
		}
		
		$query = $db->getQuery(true);
		$query->select('i.*');
		$query->from('#__djcf_items i')->where('i.id='.$item->id);
		
		$query->select('c.name as category')->leftJoin('#__djcf_categories c ON i.cat_id=c.id');
		$query->select('t.name as type')->leftJoin('#__djcf_types t ON i.type_id=t.id');
		$query->select('u.username, u.name as author')->leftJoin('#__users u ON i.user_id=u.id');
		
		$db->setQuery($query);
		$item = $db->loadObject();
		
		// get category path
		$item->category_path = '';
		$cpath = array_reverse(DJClassifiedsCategory::getParentPath(0, $item->cat_id));
		foreach($cpath as $key => $cat) {
			$item->category_path .= ($key!=0?' / ':'').$cat->name;
		}
		
		// get region path
		$item->region_path = '';
		$cpath = array_reverse(DJClassifiedsRegion::getParentPath($item->region_id));
		foreach($cpath as $key => $cat) {
			$item->region_path .= ($key!=0?', ':'').$cat->name;
		}
		
		// get custom fields
		$query = "SELECT f.*, v.value,v.value_date,v.value_date_to FROM #__djcf_fields f "
			    ."LEFT JOIN #__djcf_fields_xref fx ON f.id=fx.field_id " 
		 		."LEFT JOIN (SELECT * FROM #__djcf_fields_values WHERE item_id=".$item->id.") v "
				."ON v.field_id=f.id "
		 		."WHERE fx.cat_id  = ".$item->cat_id." AND f.published=1 AND source=0 AND f.access=0 AND f.name!='price' AND f.name!='contact' ORDER BY fx.ordering, f.ordering ";
	    $db->setQuery($query);
		$fields = $db->loadObjectList('id');
		
		// prepare custom fields for easy templating
		$item->fields = $this->_prepareFields($fields);
		
		if($app->isAdmin()){
			
			$lang = JFactory::getLanguage();
			$lang->load('com_djclassifieds', JPATH_SITE, 'en-GB', false, false);
			$lang->load('com_djclassifieds', JPATH_SITE . '/components/com_djclassifieds', 'en-GB', false, false);
			$lang->load('com_djclassifieds', JPATH_SITE, null, true, false);
			$lang->load('com_djclassifieds', JPATH_SITE . '/components/com_djclassifieds', null, true, false);
		}
		
		ob_start();
		require $tplPath;
		$content = ob_get_clean();
		
		return $content;
	}
	
	protected function _prepareFields($fields) {
		
		$par = JComponentHelper::getParams( 'com_djclassifieds' );
		
		foreach($fields as $f) {
				
			$text = '';
			
						if($f->type=='textarea'){							
							if($f->value==''){$text .= '---'; }
							else{$text .= $f->value;}								
						}else if($f->type=='checkbox'){
							if($f->value==''){$text .= '---'; }
							else{
								if($par->get('cf_values_to_labels','0')){
									$ch_values = explode(';', substr($f->value,1,-1));
									foreach($ch_values as $chi=>$chv){
										if($chi>0){ $text .= ', ';}
										$text .= JText::_('COM_DJCLASSIFIEDS_'.str_ireplace(array(' ',"'"), array('_',''), strtoupper($chv)));
									}
								}else{
									$text .= str_ireplace(';', ', ', substr($f->value,1,-1));
								}
							}
						}else if($f->type=='date'){							
							if($f->value_date=='0000-00-00'){$text .= '---'; }
							else{
								if(!$f->date_format){$f->date_format = 'Y-m-d';}
								$text .= DJClassifiedsTheme::formatDate(strtotime($f->value_date),'','',$f->date_format);
							}
						}else if($f->type=='date_from_to'){
							if(!$f->date_format){$f->date_format = 'Y-m-d';}
							if($f->value_date=='0000-00-00'){$text .= '---'; }
							else{
								$text .= DJClassifiedsTheme::formatDate(strtotime($f->value_date),'','',$f->date_format);
							}
							
							if($f->value_date_to!='0000-00-00'){
								$text .= '<span class="date_from_to_sep"> - </span>'.DJClassifiedsTheme::formatDate(strtotime($f->value_date_to),'','',$f->date_format);
							}
						}else if($f->type=='link'){
							if($f->value==''){$text .= '---'; }
							else{
								if(strstr($f->value, 'http://') || strstr($f->value, 'https://')){
									$text .= '<a '.$f->params.' href="'.$f->value.'" target="_blank">'.str_ireplace(array("http://","https://"), array('',''), $f->value).'</a>';
								}else if(strstr($f->value, '@')){
									$text .= '<a '.$f->params.' href="mailto:'.$f->value.'">'.$f->value.'</a>';
								}else{
									$text .= '<a '.$f->params.' href="http://'.$f->value.'" target="_blank">'.$f->value.'</a>';
								}																	
							}							
						}else{
							if($f->value==''){$text .= '---'; }
							else{ 
								if($par->get('cf_values_to_labels','0') && $f->type!='inputbox'){
									$text .= JText::_('COM_DJCLASSIFIEDS_'.str_ireplace(' ', '_', strtoupper($f->value)));
								}else{
									$text .= $f->value;
								}
							}	
						}
						
			$f->value_text = $text;
		}

		return $fields;
	}
}