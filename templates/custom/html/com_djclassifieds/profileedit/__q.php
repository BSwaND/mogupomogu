<?php
	$object = new stdClass();
	$object->id = $user->id;
	$object->name = 'UUPP';
	$result = JFactory::getDbo()->updateObject('#__users', $object, 'id');
