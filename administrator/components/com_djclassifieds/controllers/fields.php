<?php
/**
* @version		2.0
* @package		DJ Classifieds
* @subpackage 	DJ Classifieds Component
* @copyright 	Copyright (C) 2010 DJ-Extensions.com LTD, All rights reserved.
* @license 		http://www.gnu.org/licenses GNU/GPL
* @author 		url: http://design-joomla.eu
* @author 		email contact@design-joomla.eu
* @developer 	Łukasz Ciastek - lukasz.ciastek@design-joomla.eu
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

// No direct access.
defined('_JEXEC') or die;
JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_djclassifieds'.DS.'tables');
jimport('joomla.application.component.controlleradmin');

class DJClassifiedsControllerFields extends JControllerAdmin
{
	public function getModel($name = 'Field', $prefix = 'DJClassifiedsModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}

	function copyDefaultOrdering()
	{
		$app  = JFactory::getApplication();
		$db   = JFactory::getDBO();
		$cid  = $app->input->get('cid', array (), '', 'array');		

	    if (count($cid))
	    {
	        $cids = implode(',', $cid);
			$query = "UPDATE #__djcf_fields_xref fx JOIN #__djcf_fields f ON fx.field_id = f.id "
					."SET fx.ordering = f.ordering "
					."WHERE fx.field_id IN ( ".$cids." )";
			$db->setQuery($query);
	        if (!$db->execute())
	        {
				echo $db->getErrorMsg();
        		exit ();
	        }
		}
		$app->redirect('index.php?option=com_djclassifieds&view=fields', JText::_('COM_DJCLASSIFIEDS_FIELDS_CATEGORIES_ORDERING_CHANGED'));
	}
}