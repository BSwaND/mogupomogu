<?php
/**
 * @package DJ-Classifieds
 * @copyright Copyright (C) DJ-Extensions.com, All rights reserved.
 * @license http://www.gnu.org/licenses GNU/GPL
 * @author url: http://dj-extensions.com
 * @author email contact@dj-extensions.com
 */

defined('_JEXEC') or die();

class JFormFieldDjcfmapprovider extends JFormField {
	
	protected $type = 'Djcfmapprovider';
	
	protected function getInput()
	{
        $db = JFactory::getDBO();
        //$document = JFactory::getDocument();
        //$document->addStyleDeclaration('.djcfmapprovider ~ div{pointer-events: none;opacity: 0.5;}');

        $providers = array();
        $providers[] = JHTML::_('select.option', 'google', 'Google');
        
        $query = "SELECT * FROM #__extensions "
        ."WHERE type='plugin' AND folder='djclassifieds' AND (element IN ('leaflet','baidu','yandexmaps') OR element LIKE '%maps%') AND enabled=1 "
        ."ORDER BY extension_id";
        $db->setQuery($query);
        $map_providers = $db->loadObjectList();
        
        $selected = 'google';
        foreach($map_providers as $mp){
            $providers[] = JHTML::_('select.option', $mp->element, ucfirst($mp->element));
            $selected = $mp->element;
        }

		$html = JHTML::_('select.genericlist', $providers, $this->name, 'disabled', 'value', 'text', $selected);
		return $html;
	}
	
}
?>