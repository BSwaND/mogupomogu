<?php
	/**
	 * @version		2.0
	 * @package		DJ Classifieds
	 * @subpackage	DJ Classifieds Component
	 * @copyright	Copyright (C) 2010 DJ-Extensions.com LTD, All rights reserved.
	 * @license		http://www.gnu.org/licenses GNU/GPL
	 * @autor url    http://design-joomla.eu
	 * @autor email  contact@design-joomla.eu
	 * @Developer    Lukasz Ciastek - lukasz.ciastek@design-joomla.eu

	 *
	 */
	defined ('_JEXEC') or die('Restricted access');

	JHTML::_('behavior.framework');
	JHTML::_('behavior.tooltip');
	$toolTipArray = array('className'=>'djcf');
	JHTML::_('behavior.tooltip', '.Tips1', $toolTipArray);

//$par	 = JComponentHelper::getParams( 'com_djclassifieds' );
	$par = DJClassifiedsParams::getParams();
	$config  = JFactory::getConfig();
	$app	 = JFactory::getApplication();
	$main_id = JRequest::getVar('cid', 0, '', 'int');
	$user	 = JFactory::getUser();

	$order = JRequest::getCmd('order', $par->get('items_ordering','date_e'));
	$ord_t = JRequest::getCmd('ord_t', $par->get('items_ordering_dir','desc'));
	if($ord_t=="desc"){
		$ord_t='asc';
	}else{
		$ord_t='desc';
	}

	$se = JRequest::getVar('se', '0', '', 'int');
	$re = JRequest::getVar('re', '0', '', 'int');
	$uid	= JRequest::getVar('uid', 0, '', 'int');
	$fav	= JRequest::getVar('fav', 0, '', 'int');
	$fav_a	= $par->get('favourite','1');

	$Itemid = JRequest::getInt('Itemid', 0);
	