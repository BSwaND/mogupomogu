<?php
	defined('_JEXEC') or die('Restricted access');

//	// связь USER for Slogin and Jommla
//	$db = JFactory::getDbo();
//	$query = $db->getQuery(true);
//	$query
//		->select('slogin.*')
//		->from($db->quoteName('#__plg_slogin_profile' , 'slogin'))
//		->where($db->quoteName('user_id') . ' ='. 500);
//
//	$query->select($db->quoteName('relation.relations'));
//	$query->leftJoin(
//		$db->quoteName('#__plg_slogin_profile_relation', 'relation')
//		. ' ON '
//		. $db->quoteName('relation.user_id') . ' = ' .  $db->quoteName(500)
//	);





////	$db->setLimit(1);
//	$db->setQuery($query);
//
//	// Получаем результат в виде списка stdClass объектов
//	$userRelation = $db->loadAssoc();
//
//
//	echo '<pre>';
//	print_r($userRelation);
//	echo '</pre>';