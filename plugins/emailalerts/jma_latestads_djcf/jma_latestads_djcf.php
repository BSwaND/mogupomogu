<?php
/*
 * @package Latest ADS - DJ-Classifieds Plugin for J!MailAlerts Component
 * @copyright Copyright (C) 2009 -2013 Techjoomla, Tekdi Web Solutions . All rights reserved.
 * @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link http://www.techjoomla.com
 */

// Do not allow direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.plugin.plugin' );

/*load language file for plugin frontend*/
$lang =  JFactory::getLanguage();
$lang->load('plg_emailalerts_jma_latestnews_js', JPATH_ADMINISTRATOR);

//include plugin helper file
$jma_helper=JPATH_SITE.DS.'components'.DS.'com_jmailalerts'.DS.'helpers'.DS.'plugins.php';
if(JFile::exists($jma_helper)){
	include_once($jma_helper);
}
else//this is needed when JMA integration plugin is used on sites where JMA is not installed
{
	if(JVERSION>'1.6.0'){
		$jma_integration_helper=JPATH_SITE.DS.'plugins'.DS.'system'.DS.'plg_sys_jma_integration'.DS.'plg_sys_jma_integration'.DS.'plugins.php';
	}
	if(JFile::exists($jma_integration_helper)){
		include_once($jma_integration_helper);
	}
}

class plgEmailalertsjma_latestads_djcf extends JPlugin
{
	function plgEmailalertsLatestads(&$subject,$config)
	{
		parent::__construct($subject, $config);
	}

	function onEmail_jma_latestads_djcf($id,$date,$userparam,$fetch_only_latest)
	{
		$areturn=array();
		$areturn[0] =$this->_name;
		if(!$id)
		{
			$areturn[1]	= '';
			$areturn[2]	= '';
			return $areturn;
		}
		
		$db	= JFactory::getDBO();
		$plugin_params=$this->params;
		
		$no_of_ads = isset($userparam['no_of_ads']) ? $userparam['no_of_ads'] : $plugin_params->get('no_of_ads');
		$cat = isset($userparam['cat']) ? explode(',',$userparam['cat']) : $plugin_params->get('cat');
		$restr_cat = isset($userparam['restr_cat']) ? explode(',',$userparam['restr_cat']) : $plugin_params->get('restr_cat');
		$user_filter = isset($userparam['user_filter']) ? $userparam['user_filter'] : $plugin_params->get('user_filter');

		$query ="SELECT i.id, i.date_start, concat(im.path,im.name,'_ths.',im.ext) image_url, i.name, i.alias, i.cat_id, 
				c.name c_name, c.alias c_alias, i.intro_desc, i.price, u.username author
				FROM #__djcf_items i
LEFT JOIN (select item_id, path, name, ext from #__djcf_images where type='item' group by item_id) im ON i.id=im.item_id
				LEFT JOIN #__users u ON i.user_id=u.id
				LEFT JOIN #__djcf_categories c ON i.cat_id=c.id 
LEFT JOIN (select user_id, max(group_id) group_id from #__user_usergroup_map group by user_id) g ON i.user_id=g.user_id
				WHERE i.published=1 AND i.date_exp>=NOW()";

		if($fetch_only_latest)
		{
			$query .=" AND i.date_start >= ";
			$query .=$db->Quote($date);
		}
		
		
		if($user_filter=='1')
		{
			$query .=" AND g.group_id IN (6,7,8)";
		}

		if($cat){
			$cat_where = http_build_query($cat,'i.cat_id');
			$cat_where = preg_replace('/[0-9]*=/', '=', $cat_where);
			$cat_where = str_replace('&', ' OR ', $cat_where);
			$query .=" AND (".$cat_where.")";
		}

		if($restr_cat){
			$restr_cat_where = http_build_query($restr_cat,'i.cat_id');
			$restr_cat_where = preg_replace('/[0-9]*=/', '!=', $restr_cat_where);
			$restr_cat_where = str_replace('&', ' AND ', $restr_cat_where);
			$query .=" AND ".$restr_cat_where;
		}
		
		$query .=" ORDER BY date_start desc";
		
		if($no_of_ads){
			$query .=" LIMIT $no_of_ads";
		}

		$db->setQuery($query);
		$adsinfo = $db->loadObjectList(); 

		$areturn	=  array();
		$areturn[0]	= $this->_name;
		if(!$adsinfo)
		{
			//if no output is found, return array with 2 indexes with NO values
			$areturn[1]	= '';
			$areturn[2]	= '';
		}
		else
		{ 
			//create object for helper class
			$helper = new pluginHelper(); 
			//set other values needed in plugin template file and pass to helper function
			//call helper function to get plugin layout
			$ht = $helper->getLayout($this->_name,$adsinfo,$plugin_params);
			$areturn[1]	= $ht;
			//call helper function to get plugin CSS layout path
			$cssfile=$helper->getCSSLayoutPath($this->_name,$plugin_params);
			$cssdata=JFile::read($cssfile);
			$areturn[2] = $cssdata;
		}		
		
		return $areturn;

	}
	
}
