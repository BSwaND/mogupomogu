<?php
/**
 * @package DJ-Classifieds
 * @copyright Copyright (C) DJ-Extensions.com, All rights reserved.
 * @license http://www.gnu.org/licenses GNU/GPL
 * @author url: http://dj-extensions.com
 * @author email contact@dj-extensions.com
 */

defined('_JEXEC') or die();

class JFormFieldDjcfregistrationactive extends JFormField {

    protected $type = 'Djcfregistrationactive';
    
    protected function getLabel()
    {
        return '';
    }

	protected function getInput()
	{
        $db = JFactory::getDBO();
        $document = JFactory::getDocument();
        $document->addStyleDeclaration('.djcfregistrationactive ~ div{display:none}');
        $document->addScriptDeclaration("jQuery(function($){
            $('.djcfregistrationactive').closest('.control-group').hide();
        })");
        
        $query = "SELECT 1 FROM #__extensions WHERE type='plugin' AND element='registration' AND folder='djclassifieds' AND enabled=1";
        $db->setQuery($query);
        $enabled = $db->loadResult();
        $enabled = $enabled ? $enabled : '0';

        $options = array();
        $options[] = JHTML::_('select.option', $enabled, 'registration enabled');
        $html = JHTML::_('select.genericlist', $options, $this->name, array('class' => 'djcfregistrationactive'), 'value', 'text', $enabled);

		return $html;
    }
    
}
?>