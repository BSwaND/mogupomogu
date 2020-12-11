<?php

	defined('_JEXEC') or die;
	JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

// Create shortcuts to some parameters.
	$params  = $this->item->params;
	$images  = json_decode($this->item->images);
	$urls    = json_decode($this->item->urls);
	$canEdit = $params->get('access-edit');
	$user    = JFactory::getUser();
	$info    = $params->get('info_block_position', 0);

// Check if associations are implemented. If they are, define the parameter.
	$assocParam = (JLanguageAssociations::isEnabled() && $params->get('show_associations'));
	JHtml::_('behavior.caption');

