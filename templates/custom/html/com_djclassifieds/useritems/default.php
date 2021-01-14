<?php
	/**
	 * @version    2.0
	 * @package    DJ Classifieds
	 * @subpackage  DJ Classifieds Component
	 * @copyright  Copyright (C) 2010 DJ-Extensions.com LTD, All rights reserved.
	 * @license    http://www.gnu.org/licenses GNU/GPL
	 * @autor url    http://design-joomla.eu
	 * @autor email  contact@design-joomla.eu
	 * @Developer    Lukasz Ciastek - lukasz.ciastek@design-joomla.eu
	 *
	 */
	defined('_JEXEC') or die('Restricted access');
	$toolTipArray = array('className' => 'djcf');
	JHTML::_('behavior.tooltip', '.Tips1', $toolTipArray);
	$par = JComponentHelper::getParams('com_djclassifieds');
	$app = JFactory::getApplication();
	$main_id = JRequest::getVar('cid', 0, '', 'int');
	$it = JRequest::getVar('Itemid', 0, '', 'int');
	$points_a = $par->get('points', 0);
	$document = JFactory::getDocument();
	$user = JFactory::getUser();


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
	$profileUser = $db->loadAssoc();

	$profileUserInfo = JUserHelper::getProfile($user->id);
	$profileUserPhone = isset($profileUserInfo->profile['phone']) ? $profileUserInfo->profile['phone'] : null;
	$profileUserCity =  isset($profileUserInfo->profile['city']) ? $profileUserInfo->profile['city'] : null;


	

	jimport('joomla.application.module.helper');
	$attribs['style'] = 'none';

	$order = JRequest::getCmd('order', $par->get('items_ordering', 'date_e'));
	$ord_t = JRequest::getCmd('ord_t', $par->get('items_ordering_dir', 'desc'));
	$ord_dir = JRequest::getCmd('ord_t', $par->get('items_ordering_dir', 'desc'));
	if ($ord_t == "desc") {
		$ord_t = 'asc';
	} else {
		$ord_t = 'desc';
	}

	$sw = htmlspecialchars(JRequest::getVar('search', ''), ENT_COMPAT, 'UTF-8');
	$uid = JRequest::getVar('uid', 0, '', 'int');

	$menus = $app->getMenu('site');
	$menu_item = $menus->getItems('link', 'index.php?option=com_djclassifieds&view=items', 1);

	$itemid = '';
	if ($menu_item) {
		$itemid = '&Itemid=' . $menu_item->id;
	}



?>

<div id="dj-classifieds" class="clearfix obyavleniya-polzovatelya djcftheme-<?php echo $par->get('theme', 'default'); ?> ">
	<div class="row">
		<div class="col-md-12">
			<div class="adt_block bg-white">
				<div class="bg-white__header mb-3">
					<div class="h2">Мой профиль</div>
				</div>

				<div class="mt-3 p-3">
							<div class="adt_author_card">
								<div class="row">
									<?php
										echo '<div class="col-lg-5">';
                       if($profileUser['name']){
												echo '<img class="adt_author_foto" itemprop="image" alt="logo" src="'.JURI::base(true).$profileUser['path'].$profileUser['name'].'.'.$profileUser['ext'].'" />';
											} else {
												echo '<img class="adt_author_foto" itemprop="image" alt="logo"  src="'.JURI::base(true).'/components/com_djclassifieds/assets/images/default_profile_s.png" />';
											}
										echo '</div>';
									 ?>
									<div class="col-lg-13">
										<div class="adt_author_card_header__name d-flex justify-content-between">
											<span>
												<?= $user->name ?>
											</span>
											<?= ($profileUserCity) ? '<span class="adt_author_card__marker">'. $profileUserCity .'</span>': null ?>
										</div>

										<div class="adt_author_block_body">
											<?php /* if(isset($profileUserPhone)) {?>
												<div class="adt_author_block_body_row">
													<span class="adt_author_block_body_row__title">Телефон:</span>
													<span><?= $profileUserPhone ?></span>
												</div>
											<?php }?>
											<?php
											<div class="adt_author_block_body_row mb-4">
												<span class="adt_author_block_body_row__title">Email:</span>
												<span><?= $user->email ?></span>
											</div>
											   */?>
											<a href="/component/djclassifieds/?view=profileedit" class="btn btn_grey">Редактировать профиль</a>
										</div>
									</div>
								</div>							                                                                                     								
							</div>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="bg-white menu_for_user mb-3 ">
				<?php
					$modules = JModuleHelper::getModules('menu_for_user');
					foreach ($modules as $module) {
						echo JModuleHelper::renderModule($module, $attribs);
					}
				?>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-18">
			<?php
				$flag = true;
				foreach ($this->items as $item) {
					if($item->published == 1) {
					$linkItem =  DJClassifiedsSEO::getItemRoute($item->id . ':' . $item->alias, $item->cat_id . ':' . $item->c_alias, $item->region_id . ':' . $item->r_name);
					$itemIsActive = null;


					switch ($item->s_active){
						case '1':
							$itemIsActive = '<div class="label label_active">Активно</div>';
							break;
						case '0':
							$itemIsActive = '<div class="label label_deactive">Неактивно</div>';
							break;
					}

					if ($flag) {
						$flag = false;	?>
						<div class="adt_block bg-white">
							<div class="bg-white__header mb-3 sort-adt__bg-white">
								<div class="h2"><?php echo JText::_('COM_DJCLASSIFIEDS_YOUR_ADS'); ?></div>

								<div class="sort-adt__outer">
									<div class="sort-adt__link">Сортировать</div>
									<div class="sort-adt__block-links">
										<?php if ($order == "title") {
											$class = "active";
										} else {
											$class = "normal";
										} ?>
										<div class="main_title_box name first <?php echo $class; ?>">
											<div class="main_title_box_in">
												<a class="<?php echo $class; ?>"
												   href="index.php?option=com_djclassifieds&view=useritems&Itemid=<?php echo $it; ?>&cid=<?php echo $main_id; ?>&order=title&ord_t=<?php echo $ord_t; ?><?php if ($sw) {
													   echo '&search=' . $sw;
												   };
													   if ($uid) {
														   echo '&uid=' . $uid;
													   } ?>">
													<?php echo JText::_('COM_DJCLASSIFIEDS_TITLE');
														if ($order == "title") {
															if ($ord_t == 'asc') {
																echo '<img src="' . JURI::base(true) . '/components/com_djclassifieds/assets/images/sort_desc.gif" />';
															} else {
																echo '<img src="' . JURI::base(true) . '/components/com_djclassifieds/assets/images/sort_asc.gif" />';
															}
														} else {
															echo '<img src="' . JURI::base(true) . '/components/com_djclassifieds/assets/images/sort.gif" />';
														} ?>
												</a>
											</div>
										</div>
										<?php
											if ($order == "date_a") {
												$class = "active";
											} else {
												$class = "normal";
											} ?>
										<div class="main_title_box <?php echo $class; ?>">
											<div class="main_title_box_in">
												<a class="<?php echo $class; ?>"
												   href="index.php?option=com_djclassifieds&view=useritems&Itemid=<?php echo $it; ?>&cid=<?php echo $main_id; ?>&order=date_a&ord_t=<?php echo $ord_t; ?><?php if ($sw) {
													   echo '&search=' . $sw;
												   };
													   if ($uid) {
														   echo '&uid=' . $uid;
													   } ?>">
													<?php echo JText::_('COM_DJCLASSIFIEDS_DATE_ADDED');
														if ($order == "date_a") {
															if ($ord_t == 'asc') {
																echo '<img src="' . JURI::base(true) . '/components/com_djclassifieds/assets/images/sort_desc.gif" />';
															} else {
																echo '<img src="' . JURI::base(true) . '/components/com_djclassifieds/assets/images/sort_asc.gif" />';
															}
														} else {
															echo '<img src="' . JURI::base(true) . '/components/com_djclassifieds/assets/images/sort.gif" />';
														} ?>
												</a>
											</div>
										</div>
										<?php if ($order == "date_e") {
											$class = "active";
										} else {
											$class = "normal";
										} ?>
										<div class="main_title_box <?php echo $class; ?>">
											<div class="main_title_box_in">
												<a class="<?php echo $class; ?>"
												   href="index.php?option=com_djclassifieds&view=useritems&Itemid=<?php echo $it; ?>&cid=<?php echo $main_id; ?>&order=date_e&ord_t=<?php echo $ord_t; ?><?php if ($sw) {
													   echo '&search=' . $sw;
												   };
													   if ($uid) {
														   echo '&uid=' . $uid;
													   } ?>">
													<?php echo JText::_('COM_DJCLASSIFIEDS_DATE_EXPIRATION');
														if ($order == "date_e") {
															if ($ord_t == 'asc') {
																echo '<img src="' . JURI::base(true) . '/components/com_djclassifieds/assets/images/sort_desc.gif" />';
															} else {
																echo '<img src="' . JURI::base(true) . '/components/com_djclassifieds/assets/images/sort_asc.gif" />';
															}
														} else {
															echo '<img src="' . JURI::base(true) . '/components/com_djclassifieds/assets/images/sort.gif" />';
														} ?></a>
											</div>
										</div>
										<?php if ($order == "active") {
											$class = "active";
										} else {
											$class = "normal";
										} ?>
										<div class="main_title_box <?php echo $class; ?>">
											<div class="main_title_box_in">
												<a class="<?php echo $class; ?>"
												   href="index.php?option=com_djclassifieds&view=useritems&Itemid=<?php echo $it; ?>&cid=<?php echo $main_id; ?>&order=active&ord_t=<?php echo $ord_t; ?><?php if ($sw) {
													   echo '&search=' . $sw;
												   };
													   if ($uid) {
														   echo '&uid=' . $uid;
													   } ?>">
													<?php echo JText::_('COM_DJCLASSIFIEDS_ACTIVE');
														if ($order == "active") {
															if ($ord_t == 'asc') {
																echo '<img src="' . JURI::base(true) . '/components/com_djclassifieds/assets/images/sort_desc.gif" />';
															} else {
																echo '<img src="' . JURI::base(true) . '/components/com_djclassifieds/assets/images/sort_asc.gif" />';
															}
														} else {
															echo '<img src="' . JURI::base(true) . '/components/com_djclassifieds/assets/images/sort.gif" />';
														} ?></a>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="adt_profile__outer">
								<div class="adt_profile_item">
									<div class="adt_profile_td_outer">
										<div class="adt_profile_td adt_profile_td__first-child">
											<div class="adt_profile_head"><?= 	$itemIsActive ?></div>
											<div class="adt_profile_body">
												<a href="<?= $linkItem ?>">
													<img class="adt_profile_img" src="<?= ($item->images[0])->thumb_b ?>" alt="<?= $item->name ?>">
												</a>
											</div>
										</div>
										<div class="adt_profile_td">
											<div class="adt_profile_head">Заголовок</div>
											<div class="adt_profile_body"><a href="<?= $linkItem ?>"><?= $item->name ?></a></div>
										</div>
										<div class="adt_profile_td">
											<div class="adt_profile_head">Категория</div>
											<div class="adt_profile_body"><?= $item->c_name ?></div>
										</div>
										<div class="adt_profile_td adt_profile_td__descriptor">
											<div class="adt_profile_head">Описание</div>
											<div class="adt_profile_body"><?= mb_substr($item->intro_desc, 0, 100, 'UTF-8')?></div>
										</div>
										<div class="adt_profile_td adt_profile_td__location">
											<div class="adt_profile_head">Расположение</div>
											<div class="adt_profile_body"><?= $item->r_name ?></div>
										</div>
										<div class="adt_profile_td">
											<div class="adt_profile_head">Добавлено</div>
											<div class="adt_profile_body"><?= date('d.m.Y',  strtotime($item->date_start)) ?></div>
										</div>
										<div class="adt_profile_td adt_profile_td__vied ">
											<div class="adt_profile_head">Показы</div>
											<div class="adt_profile_body"><?= $item->display ?></div>
										</div>
									</div>
									<div class="adt_profile_footer">
										<a class="btn btn_wite" href="#">Поделиться</a>
										<a class="btn btn_grey" href="index.php?option=com_djclassifieds&view=additem&id=<?= $item->id . $itemid_new ?>">Редактировать объявление</a>
										<a class="btn btn_accent-black" href="index.php?option=com_djclassifieds&view=useritems&t=delete&id=<?=  $item->id . '&Itemid=' . $it?>">Удалить</a>
									</div>
								</div>
							</div>
						</div>
					<?php } else { ?>
						<div class="adt_profile__outer">
							<div class="adt_profile_item bg-white p-3">
								<div class="adt_profile_td_outer">
									<div class="adt_profile_td adt_profile_td__first-child">
										<div class="adt_profile_head"><?= 	$itemIsActive ?> <br><b>до <?= date('d.m.Y',  strtotime($item->date_exp)) ?></b></div> 
										<div class="adt_profile_body">
											<a href="<?= $linkItem ?>">
												<img class="adt_profile_img" src="<?= ($item->images[0])->thumb_b ?>" alt="<?= $item->name ?>">
											</a>
										</div>
									</div>
									<div class="adt_profile_td">
										<div class="adt_profile_head">Заголовок</div>
										<div class="adt_profile_body"><a href="<?= $linkItem ?>"><?= $item->name ?></a></div>
									</div>
									<div class="adt_profile_td">
										<div class="adt_profile_head">Категория</div>
										<div class="adt_profile_body"><?= $item->c_name ?></div>
									</div>
									<div class="adt_profile_td adt_profile_td__descriptor">
										<div class="adt_profile_head">Описание</div>
										<div class="adt_profile_body"><?= mb_substr($item->intro_desc, 0, 100, 'UTF-8')?></div>
									</div>
									<div class="adt_profile_td adt_profile_td__location">
										<div class="adt_profile_head">Расположение</div>
										<div class="adt_profile_body"><?= $item->r_name ?></div>
									</div>
									<div class="adt_profile_td">
										<div class="adt_profile_head">Добавлено</div>
										<div class="adt_profile_body"><?= date('d.m.Y',  strtotime($item->date_start)) ?></div>
									</div>
									<div class="adt_profile_td adt_profile_td__vied ">
										<div class="adt_profile_head">Показы</div>
										<div class="adt_profile_body"><?= $item->display ?></div>
									</div>
								</div>
								<div class="adt_profile_footer">
									<a class="btn btn_wite" href="#">Поделиться</a>
									<a class="btn btn_grey" href="index.php?option=com_djclassifieds&view=additem&id=<?= $item->id . $itemid_new ?>">Редактировать объявление</a>
									<a class="btn btn_accent-black" href="index.php?option=com_djclassifieds&view=useritems&t=delete&id=<?=  $item->id . '&Itemid=' . $it?>">Удалить</a>
								</div>
							</div>
						</div>
					<?php }
					}
				} ?>
		</div>
	</div>
</div>
<script type="text/javascript">
	function confirm_renew(title, id) {
		var answer = confirm('<?php echo DJClassifiedsTheme::sanitizePopupText(JText::_('COM_DJCLASSIFIEDS_RENEW_CONFIRM')); ?>' + ' "' + title + '"');
		if (answer) {
			window.location = "index.php?option=com_djclassifieds&view=item&task=renew&id=" + id + "&Itemid=<?php echo $it . '&order=' . $order . '&ord_t=' . $ord_dir;?>";
		}
	}

	function confirm_archive(title, id) {
		var answer = confirm('<?php echo DJClassifiedsTheme::sanitizePopupText(JText::_('COM_DJCLASSIFIEDS_MOVE_TO_ARCHIVE_CONFIRM')); ?>' + ' "' + title + '"');
		if (answer) {
			window.location = "index.php?option=com_djclassifieds&view=item&task=archive&id=" + id + "&Itemid=<?php echo $it;?>";
		}
	}

	window.addEvent('load', function () {
		var djcfpagebreak_acc = new Fx.Accordion('.row_ua_orders .row_ua_orders_title',
			'.row_ua_orders .row_ua_orders_content', {
				alwaysHide: true,
				display: -1,
				duration: 100,
				onActive: function (toggler, element) {
					toggler.addClass('active');
					element.addClass('in');
				},
				onBackground: function (toggler, element) {
					toggler.removeClass('active');
					element.removeClass('in');
				}
			});
	});

</script>