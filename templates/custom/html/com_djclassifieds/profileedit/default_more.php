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
*/
defined ('_JEXEC') or die('Restricted access');

?>  

<?php foreach($this->users_form->getFieldsets() as $name => $fieldset){ ?>
	<?php if(!in_array($name, array('core','params','settings'))){ ?>
		<div class="additem_djform_in">
			<?php foreach($this->users_form->getFieldset($name) as $field){ ?>
				<div class="djform_row">
					<?php echo str_replace('class="', 'class="label ', $field->label) ?>
					<div class="djform_field">
						<?php echo $field->input; ?>
					</div>
				</div>
			<?php } ?>
		</div>
	<?php } ?>
<?php } ?>