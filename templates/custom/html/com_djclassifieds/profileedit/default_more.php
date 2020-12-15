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

?>  

<?php foreach($this->users_form->getFieldsets() as $name => $fieldset){ ?>
	<?php if(!in_array($name, array('core','params','settings'))){ ?>
		<div class="title_top"><?php echo JText::_($fieldset->label); ?></div>
		<div class="additem_djform_in">
			<?php foreach($this->users_form->getFieldset($name) as $field){ ?>
				<div class="djform_row">
					<?php echo str_replace('class="', 'class="label ', $field->label); ?>
					<div class="djform_field">
						<?php echo $field->input; ?>
					</div>
					<div class="clear_both"></div> 
				</div>
			<?php } ?>
		</div>
	<?php } ?>
<?php } ?>