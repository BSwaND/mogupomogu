<?php
	defined('_JEXEC') or die('Restricted access');


	$dbr = JFactory::getDbo();
	$queryRel = $dbr->getQuery(true);
	$queryRel
		->select('rel.*')
		->from($dbr->quoteName('#__plg_slogin_profile_relation' , 'rel'))
		->where($dbr->quoteName('rel.user_id') . ' ='.  $user->id);
	$dbr->setLimit(1);
	$dbr->setQuery($queryRel);
	$userRelationRel = $dbr->loadObject();




	if(!isset($userRelationRel))
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query
			->select('slogin.*')
			->from($db->quoteName('#__plg_slogin_profile' , 'slogin')) ;

		$query->select('users.*')
			->leftJoin(
				$db->quoteName('#__users', 'users')
				. ' ON '
				. $db->quoteName('users.id') . ' = ' . $user->id 	);

		$query->where($db->quoteName('slogin.user_id') . ' ='. $user->id);
		$db->setLimit(1);
		$db->setQuery($query);
		$userRelation = $db->loadObject();

		if($userRelation->slogin_id)
		{
			$avater = explode('.',$userRelation->avatar);
			
			// isert for  plg_slogin_profile_relation
			$dbRelInsert = JFactory::getDbo();
			$queryRelInsert = $dbRelInsert->getQuery(true);
			$columns = array( 'user_id', 'slogin_id','relations');
			// Insert values.
			$values = array(
				$db->quote($user->id),
				$db->quote($userRelation->slogin_id),
				$db->quote(1)
			);
			// Prepare the insert query.
			$queryRelInsert
				->insert($dbRelInsert->quoteName('#__plg_slogin_profile_relation'))
				->columns($dbRelInsert->quoteName($columns))
				->values(implode(',', $values));
			$dbRelInsert->setQuery($queryRelInsert);
			$dbRelInsert->execute();


			// isert for  #__djcf_images
			$dbDjImage = JFactory::getDbo();
			$queryDjImage = $dbDjImage->getQuery(true);
			$columns = array('item_id', 'type', 'name', 'ext', 'path');
			// Insert values.
			$values = array(
				$db->quote($user->id),
				$db->quote('profile'),
				$db->quote( $avater[0]),
				$db->quote($avater[1]),
				$db->quote('images/avatar/')
			);
			// Prepare the insert query.
			$queryDjImage
				->insert($dbDjImage->quoteName('#__djcf_images'))
				->columns($dbDjImage->quoteName($columns))
				->values(implode(',', $values));
			$dbDjImage->setQuery($queryDjImage);
			$dbDjImage->execute();


			// isert for  #__djcf_fields_values_profile
			$dbfp= JFactory::getDbo();
			$queryfp = $dbfp->getQuery(true);
			$columns = array('field_id', 'user_id', 'value');
			// Insert values.
			$values = array(
				2,
				$db->quote($user->id),
				$db->quote($userRelation->l_name)
			);
			// Prepare the insert query.
			$queryfp
				->insert($dbfp->quoteName('#__djcf_fields_values_profile'))
				->columns($dbfp->quoteName($columns))
				->values(implode(',', $values));
			$dbfp->setQuery($queryfp);
			$dbfp->execute();


			$object = new stdClass();
			$object->id = $user->id;
			$object->name = $userRelation->f_name;
			$result = JFactory::getDbo()->updateObject('#__users', $object, 'id');


		header("Refresh:0");
		}
	}

//	echo '<pre>';
//	(isset($userRelation)) ? 	print_r($userRelation) : null;
//	print_r($userRelationRel);
//	echo '</pre>';
