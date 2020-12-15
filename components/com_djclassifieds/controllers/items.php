<?php
/**
 * @package DJ-Classifieds
 * @copyright Copyright (C) DJ-Extensions.com, All rights reserved.
 * @license http://www.gnu.org/licenses GNU/GPL
 * @author url: http://dj-extensions.com
 * @author email contact@dj-extensions.com
 */

defined('_JEXEC') or die;

class DJClassifiedsControllerItems extends JControllerLegacy {

	function parsesearch(){
		$app = JFactory::getApplication();
		$jinput = $app->input;
		$mod_id = $jinput->get('mod_id', 0);
		if($mod_id){
			$module = JModuleHelper::getModuleById($mod_id);
			$mod_params = new JRegistry($module->params);
			$link = DJClassifiedsSEO::getSearchResultsLink($mod_params, $module);
		}else{
			$par = DJClassifiedsParams::getParams();
			$link = DJClassifiedsSEO::getSearchResultsLink($par, null);
		}
		$link = JRoute::_($link.'&se=1', false);
				
		foreach($jinput->get->getArray() as $key => $input){
			if(empty($input) || $input == '0000-00-00 00:00:00' || in_array($key, array('task', 'se', 'option', 'view', 'Itemid', 'mod_id'))){
				continue;
			}
			if(is_array($input)){
				$input = array_filter($input);
				if(!$input){
					continue;
				}
				$link .='&'.$key.'='.urlencode(implode(',', $input));
			}else{
				$link .='&'.$key.'='.urlencode($input);
			}
								 
		}

		$app->redirect($link);
		die();
	}
}