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

defined('_JEXEC') or die('Restricted access');

JHtml::_('bootstrap.tooltip');

?>

<form action="index.php" method="post" name="adminForm">
	<div id="j-sidebar-container">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container">
		<div class="djc_control_panel clearfix">
			<div class="cpanel-left">
				<div class="cpanel clearfix">
					<?php
					$cpanelmodules = JModuleHelper::getModules('djcf-cpanel');
					if ($cpanelmodules)
					{
						foreach ($cpanelmodules as $cpanelmodule)
						{
							echo JModuleHelper::renderModule($cpanelmodule);
						}
					} else {
					?>
					<div class="icon">
						<a href="index.php?option=com_djclassifieds&amp;view=categories">
							<span>
								<img alt="<?php echo JText::_('COM_DJCLASSIFIEDS_CATEGORIES'); ?>" src="<?php echo JURI::base(); ?>components/com_djclassifieds/assets/images/categories.svg" />
								<span><?php echo JText::_('COM_DJCLASSIFIEDS_CATEGORIES'); ?></span>
							</span>
						</a>
					</div>
					<div class="icon">
						<a href="index.php?option=com_djclassifieds&amp;view=items">
							<span>
								<img alt="<?php echo JText::_('COM_DJCLASSIFIEDS_ITEMS'); ?>" src="<?php echo JURI::base(); ?>components/com_djclassifieds/assets/images/items.svg" />
								<span><?php echo JText::_('COM_DJCLASSIFIEDS_ITEMS'); ?></span>
							</span>
						</a>
					</div>
					<div class="icon">
						<a href="index.php?option=com_djclassifieds&amp;view=fields">
							<span>
								<img alt="<?php echo JText::_('COM_DJCLASSIFIEDS_EXTRA_FIELDS'); ?>" src="<?php echo JURI::base(); ?>components/com_djclassifieds/assets/images/extra-fields.svg" />
								<span><?php echo JText::_('COM_DJCLASSIFIEDS_EXTRA_FIELDS'); ?></span>
							</span>
						</a>
					</div>
					<div class="icon">
						<a href="index.php?option=com_djclassifieds&amp;view=regions">
							<span>
								<img alt="<?php echo JText::_('COM_DJCLASSIFIEDS_LOCALIZATION'); ?>" src="<?php echo JURI::base(); ?>components/com_djclassifieds/assets/images/location.svg" />
								<span><?php echo JText::_('COM_DJCLASSIFIEDS_LOCALIZATION'); ?></span>
							</span>
						</a>
					</div>
					<div class="icon">
						<a href="index.php?option=com_djclassifieds&amp;view=days">
							<span>
								<img alt="<?php echo JText::_('COM_DJCLASSIFIEDS_DURATIONS'); ?>" src="<?php echo JURI::base(); ?>components/com_djclassifieds/assets/images/durations.svg" />
								<span><?php echo JText::_('COM_DJCLASSIFIEDS_DURATIONS'); ?></span>
							</span>
						</a>
					</div>
					<div class="icon">
						<a href="index.php?option=com_djclassifieds&amp;task=category.add">
							<span>
								<img alt="<?php echo JText::_('COM_DJCLASSIFIEDS_NEW_CATEGORY'); ?>" src="<?php echo JURI::base(); ?>components/com_djclassifieds/assets/images/add-category.svg" />
								<span><?php echo JText::_('COM_DJCLASSIFIEDS_NEW_CATEGORY'); ?></span>
							</span>
						</a>
					</div>
					<div class="icon">
						<a href="index.php?option=com_djclassifieds&amp;task=item.add">
							<span>
								<img alt="<?php echo JText::_('COM_DJCLASSIFIEDS_NEW_ITEM'); ?>" src="<?php echo JURI::base(); ?>components/com_djclassifieds/assets/images/add-item.svg" />
								<span><?php echo JText::_('COM_DJCLASSIFIEDS_NEW_ITEM'); ?></span>
							</span>
						</a>
					</div>
					<div class="icon">
						<a href="index.php?option=com_djclassifieds&amp;task=field.add">
							<span>
								<img alt="<?php echo JText::_('COM_DJCLASSIFIEDS_NEW_EXTRA_FIELD'); ?>" src="<?php echo JURI::base(); ?>components/com_djclassifieds/assets/images/add-field.svg" />
								<span><?php echo JText::_('COM_DJCLASSIFIEDS_NEW_EXTRA_FIELD'); ?></span>
							</span>
						</a>
					</div>
					<div class="icon">
						<a href="index.php?option=com_djclassifieds&amp;task=region.add">
							<span>
								<img alt="<?php echo JText::_('COM_DJCLASSIFIEDS_NEW_LOCATION'); ?>" src="<?php echo JURI::base(); ?>components/com_djclassifieds/assets/images/add-location.svg" />
								<span><?php echo JText::_('COM_DJCLASSIFIEDS_NEW_LOCATION'); ?></span>
							</span>
						</a>
					</div>
					<div class="icon">
						<a href="index.php?option=com_djclassifieds&amp;task=day.add">
							<span>
								<img alt="<?php echo JText::_('COM_DJCLASSIFIEDS_ADD_DURATIONS'); ?>" src="<?php echo JURI::base(); ?>components/com_djclassifieds/assets/images/add-duration.svg" />
								<span><?php echo JText::_('COM_DJCLASSIFIEDS_ADD_DURATIONS'); ?></span>
							</span>
						</a>
					</div>
					<div class="icon">
						<a href="index.php?option=com_djclassifieds&amp;view=types">
							<span>
								<img alt="<?php echo JText::_('COM_DJCLASSIFIEDS_TYPES'); ?>" src="<?php echo JURI::base(); ?>components/com_djclassifieds/assets/images/types.svg" />
								<span><?php echo JText::_('COM_DJCLASSIFIEDS_TYPES'); ?></span>
							</span>
						</a>
					</div>
					<div class="icon">
						<a href="index.php?option=com_djclassifieds&amp;view=points">
							<span>
								<img alt="<?php echo JText::_('COM_DJCLASSIFIEDS_POINTS_PACKAGES'); ?>" src="<?php echo JURI::base(); ?>components/com_djclassifieds/assets/images/points-packages.svg" />
								<span><?php echo JText::_('COM_DJCLASSIFIEDS_POINTS_PACKAGES'); ?></span>
							</span>
						</a>
					</div>
					<div class="icon">
						<a href="index.php?option=com_djclassifieds&amp;view=payments">
							<span>
								<img alt="<?php echo JText::_('COM_DJCLASSIFIEDS_PAYMENTS'); ?>" src="<?php echo JURI::base(); ?>components/com_djclassifieds/assets/images/payments.svg" />
								<span><?php echo JText::_('COM_DJCLASSIFIEDS_PAYMENTS'); ?></span>
							</span>
						</a>
					</div>
					<div class="icon">
						<a href="index.php?option=com_djclassifieds&amp;view=userspoints">
							<span>
								<img alt="<?php echo JText::_('COM_DJCLASSIFIEDS_USERS_POINTS'); ?>" src="<?php echo JURI::base(); ?>components/com_djclassifieds/assets/images/user-points.svg" />
								<span><?php echo JText::_('COM_DJCLASSIFIEDS_USERS_POINTS'); ?></span>
							</span>
						</a>
					</div>
					<div class="icon">
						<a href="index.php?option=com_djclassifieds&amp;view=promotions">
							<span>
								<img alt="<?php echo JText::_('COM_DJCLASSIFIEDS_PROMOTIONS'); ?>" src="<?php echo JURI::base(); ?>components/com_djclassifieds/assets/images/promotions.svg" />
								<span><?php echo JText::_('COM_DJCLASSIFIEDS_PROMOTIONS'); ?></span>
							</span>
						</a>
					</div>
					<div class="icon">
						<a href="index.php?option=com_djclassifieds&amp;task=type.add">
							<span>
								<img alt="<?php echo JText::_('COM_DJCLASSIFIEDS_ADD_TYPE'); ?>" src="<?php echo JURI::base(); ?>components/com_djclassifieds/assets/images/add-type.svg" />
								<span><?php echo JText::_('COM_DJCLASSIFIEDS_ADD_TYPE'); ?></span>
							</span>
						</a>
					</div>
					<div class="icon">
						<a href="index.php?option=com_djclassifieds&amp;task=point.add">
							<span>
								<img alt="<?php echo JText::_('COM_DJCLASSIFIEDS_ADD_POINTS_PACKAGE'); ?>" src="<?php echo JURI::base(); ?>components/com_djclassifieds/assets/images/add-package.svg" />
								<span><?php echo JText::_('COM_DJCLASSIFIEDS_ADD_POINTS_PACKAGE'); ?></span>
							</span>
						</a>
					</div>
					<div class="icon">
						<a href="index.php?option=com_plugins&view=plugins&filter_folder=djclassifiedspayment">
							<span>
								<img alt="<?php echo JText::_('COM_DJCLASSIFIEDS_PAYMENT_PLUGINS'); ?>" src="<?php echo JURI::base(); ?>components/com_djclassifieds/assets/images/payment-plugins.svg" />
								<span><?php echo JText::_('COM_DJCLASSIFIEDS_PAYMENT_PLUGINS'); ?></span>
							</span>
						</a>
					</div>
					<div class="icon">
						<a href="http://dj-extensions.com/dj-classifieds" target="_blank">
							<span>
								<img alt="<?php echo JText::_('COM_DJCLASSIFIEDS_DOCUMENTATION'); ?>" src="<?php echo JURI::base(); ?>components/com_djclassifieds/assets/images/documentation.svg" />
								<span><?php echo JText::_('COM_DJCLASSIFIEDS_DOCUMENTATION'); ?></span>
							</span>
						</a>
					</div>
					<div class="icon">
						<a href="index.php?option=com_config&view=component&component=com_djclassifieds&path=&return=<?php echo base64_encode('index.php?option=com_djclassifieds')?>">
							<span>
								<img alt="<?php echo JText::_('JOPTIONS'); ?>" src="<?php echo JURI::base(); ?>components/com_djclassifieds/assets/images/options.svg" />
								<span><?php echo JText::_('JOPTIONS'); ?></span>
							</span>
						</a>
					</div>
					<?php } ?>
				</div>
			</div>
			<div class="cpanel-right">
				<div class="cpanel">
					<?php echo DJLicense::getSubscription('Classifieds'); ?>
				</div>
			</div>
		</div>
</div>
<input type="hidden" name="option" value="com_djclassifieds" />
<input type="hidden" name="c" value="cpanel" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="view" value="cpanel" />
<input type="hidden" name="boxchecked" value="0" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
<?php echo DJCFFOOTER; ?>