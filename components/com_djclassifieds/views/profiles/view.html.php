<?php
/**
* @version 2.0
* @package DJ Classifieds
* @subpackage DJ Classifieds Component
* @copyright Copyright (C) 2010 DJ-Extensions.com LTD, All rights reserved.
* @license http://www.gnu.org/licenses GNU/GPL
* @author url: http://design-joomla.eu
* @author email contact@design-joomla.eu
* @developer Łukasz Ciastek - lukasz.ciastek@design-joomla.eu
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
defined ('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('joomla.html.pagination');

class DJClassifiedsViewProfiles extends JViewLegacy{

	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->_addPath('template', JPATH_COMPONENT.  '/themes/default/views/profiles');
		$par = JComponentHelper::getParams( 'com_djclassifieds' );
		$theme = $par->get('theme','default');
		if ($theme && $theme != 'default') {
			$this->_addPath('template', JPATH_COMPONENT.  '/themes/'.$theme.'/views/profiles');
		}
	}	
	
	public function display($tpl = null)
	{
		$document = JFactory::getDocument();
		$par 	= JComponentHelper::getParams( 'com_djclassifieds' );
		$app 	= JFactory::getApplication();
		$user 	= JFactory::getUser();
		$model 	= $this->getModel();

		$menus	= $app->getMenu('site');
		$m_active = $menus->getActive();
		if($m_active){
			if($m_active->params->get('menu-meta_keywords')){
				$document->setMetaData('keywords',$m_active->params->get('menu-meta_keywords'));
			}
			if($m_active->params->get('menu-meta_description')){
				$document->setDescription($m_active->params->get('menu-meta_description'));
			}
		}
		
		$items= $model->getItems();
		$countitems = $model->getCountItems();
		$regions = DJClassifiedsRegion::getRegAll();
		
		
		$limit	= JRequest::getVar('limit', $par->get('limit_djitem_show'), '', 'int');
		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');
		$pagination = new JPagination( $countitems, $limitstart, $limit );
	
		$this->assignRef('items', $items);		
		$this->assignRef('regions', $regions);
		$this->assignRef('pagination', $pagination);
		
		parent::display($tpl);		  
	}

}




