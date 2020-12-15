<?php


// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.plugin.plugin');


class plgUserdjcfadstoken extends JPlugin
{
	
	public function onUserAfterSave($user, $isnew, $success, $msg){
				
		if ($isnew){
			$db = JFactory::getDBO();
			$query = "UPDATE #__djcf_items SET user_id='".$user['id']."' WHERE user_id=0 AND email='".$user['email']."' ";					
			$db->setQuery($query);
			$db->query();				
		}
		
		return true;
	}
}