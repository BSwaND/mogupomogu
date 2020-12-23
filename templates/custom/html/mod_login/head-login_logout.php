<?php
	/**
	 * @package     Joomla.Site
	 * @subpackage  mod_login
	 *
	 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
	 * @license     GNU General Public License version 2 or later; see LICENSE.txt
	 */

	defined('_JEXEC') or die;
	$document = JFactory::getDocument();


	$db = JFactory::getDbo();
	$query = $db->getQuery(true);
	$query
		->select(
			$db->quoteName(
				array('id','type','name', 'ext' ,'path')
			)
		)
		->from($db->quoteName('#__djcf_images'))
		->where($db->quoteName('item_id') . ' ='. $user->id);
	$db->setQuery($query);
	$db->setLimit(1);
	$document->profileUser = $db->loadAssoc();

	$fotoName = $document->profileUser['name'];
	$fotoPath = $document->profileUser['path'];
	$fotoExt =  $document->profileUser['ext'];

?>


	<div class="login-box_outer ">
		<?php if ($params->get('greeting', 1)) { ?>
			<div class="login-greeting">
			</div>
		<?php } ?>
		<div  class="btn btn_login "
		      <?= ($fotoName) ? 'style="background-image: url('.$fotoPath.$fotoName.'.'.$fotoExt.');background-size: cover "'  :  null ?> >
		<?= ($user->get('username')) ? '<span class="btn_login__user-name">'. htmlspecialchars($user->get("username")) .'</span>' : null ?>
		</div>

		<div class="login-box">
			<a class="login-box__link" href="/obyavleniya-polzovatelya">Мои объявления</a>
			<a class="login-box__link" href="index.php?option=com_djclassifieds&view=profileedit">Настройки</a>
			<span class="login-box__link login-box__link__out">Выход</span>  
		</div>
	</div>

<div class="d-none">
	<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure', 0)); ?>" method="post" id="login-form" class="form-vertical">
		<?php if ($params->get('profilelink', 0)) : ?>
			<ul class="unstyled">
				<li>
					<a href="<?php echo JRoute::_('index.php?option=com_users&view=profile'); ?>">
						<?php echo JText::_('MOD_LOGIN_PROFILE'); ?></a>
				</li>
			</ul>
		<?php endif; ?>
		<div class="logout-button">
			<input type="submit" name="Submit" class="btn btn-primary" value="<?php echo JText::_('JLOGOUT'); ?>" />
			<input type="hidden" name="option" value="com_users" />
			<input type="hidden" name="task" value="user.logout" />
			<input type="hidden" name="return" value="<?php echo $return; ?>" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>


