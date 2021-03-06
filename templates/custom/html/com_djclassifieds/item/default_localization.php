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
$par = JComponentHelper::getParams( 'com_djclassifieds' );
$app = JFactory::getApplication();
$document= JFactory::getDocument();
$config = JFactory::getConfig();
$item = $this->item;
if($par->get('show_googlemap')==1){
	DJClassifiedsTheme::includeMapsScript();		
}

$map_styles = $par->get('gm_styles','');
if (trim($map_styles) == '') {
	$map_styles = '[]';
}

?>







<?php if($item->event->onBeforeDJClassifiedsDisplayAdvertMap) { ?>
	<div class="djcf_custom_map">
		<?php   echo $this->item->event->onBeforeDJClassifiedsDisplayAdvertMap; ?>
	</div>
<?php } ?>