<?php
	defined('_JEXEC') or die;

	$app = JFactory::getApplication();
	$user = JFactory::getUser();
	JFactory::getDocument()->setGenerator('');
	$this->setHtml5(true);
	$params = $app->getTemplate(true)->params;
	$menu = $app->getMenu()->getActive();
	$document = JFactory::getDocument();
	$document->setGenerator('');
	$template_url = JUri::root() . 'template/' . $this->template;
	$pageclass = '';
	if (is_object($menu))
		$pageclass = $menu->params->get('pageclass_sfx');

	// Подключение своих стилей:
	JHtml::_('stylesheet', 'styles.min.css', array('version' => 'v=1.3', 'relative' => true));
	JHtml::_('stylesheet', 'uform.css', array('version' => 'v=1.3', 'relative' => true));
	JHtml::_('stylesheet', 'custom.css', array('version' => 'v=1.3', 'relative' => true));


	//Протокол Open Graph
	$pageTitle = $document->getTitle();
	$metaDescription = $document->getMetaData('description');
	$type = 'website';
	$view = $app->input->get('view', '');
	$id = $app->input->get('id', '');
	$image = JURI::base() . 'templates/custom/icon/logo.png';
	$title = !empty($pageTitle) ? $pageTitle : "default title";
	$desc = !empty($metaDescription) ? $metaDescription : "default description";

	if (!empty($view) && $view === 'article' && !empty($id)) {
		$article = JControllerLegacy::getInstance('Content')->getModel('Article')->getItem($id);
		$type = 'article';
		$images = json_decode($article->images);
		$image = !empty($images->image_intro) ? JURI::base() . $images->image_intro : JURI::base() . $images->image_fulltext;
	}
	$document->addCustomTag('
    <meta property="og:type" content="' . $type . '" />
    <meta property="og:title" content="' . $title . '" />
    <meta property="og:description" content="' . $desc . '" />
    <meta property="og:image" content="' . $image . '" />
    <meta property="og:url" content="' . JURI:: current() . '" />
');

?>

<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" prefix="og: http://ogp.me/ns#">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" type="image/png" href="/templates/<?php echo $this->template; ?>/icon/favicon.ico"/>
	<jdoc:include type="head"/>
</head>
<body class="body <?=($user->guest == 1) ? 'user-guest' : null?> <?php echo $pageclass ? htmlspecialchars($pageclass) : 'default'; ?>">

<header class="header">
	<div class="container">
		<div class="row align-items-center">
			<div class="col-xl-6">
				<div class="header_logo_block">
					<a href="/" class="header_logo">
						<img src="images/logo.svg" alt="logo">
					</a>
				</div>
			</div>
			<div class="col-xl-12">
				<div class="header_info_outer">

					<div class="header_info_top">
						<div class="phone_outer">
							<div class="hamburger hamburger--slider">
								<div class="hamburger-box">
									<div class="hamburger-inner"></div>
								</div>
							</div>
							<div class="d-flex">
								<?php /*
								<img src="images/phone.svg" alt="phone" class="phone_img">
								*/ ?>
								<div class="phone_block">
									<jdoc:include type="modules" name="phones_block" style="none"/>
								</div>
							</div>
						</div>
						<div class="d-flex">
							<a href="/dobavit-obyavlenie" class="btn btn_header">Подать объявление</a>
							<jdoc:include type="modules" name="in_site" style="none"/>
						</div>
					</div>


					<div class="header_info_nav">
						<div class="hamburger hamburger--slider hamburger__mob-menu">
							<div class="hamburger-box">
								<div class="hamburger-inner"></div>
							</div>
						</div>
						<a href="/" class="nav_header__logo">
							<img src="images/logo.svg" alt="logo">
						</a>
						<jdoc:include type="modules" name="menu" style="none"/>
					</div>
				</div>
			</div>
		</div>
	</div>
</header>

<main>
	<div class="container">
		<jdoc:include type="message" />
		<div class="main_header">
			<jdoc:include type="modules" name="form_search_main" style="none"/>

			<jdoc:include type="modules" name="breadcrumbs" style="none"/>

			<jdoc:include type="component"/>

			<div class="row">
				<div class="col-18">
					<div class="bg-white">
						<div class="bg-white__header">
							<div class="h2">Карта объявления <img src="images/marker-map.svg" alt="map" class="marker-map__img"></div>
						</div>
						<jdoc:include type="modules" name="map" style="none"/>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>


<footer class="footer">
	<div class="footer_top">
		<div class="container">
			<div class="row">
				<div class="col-md-5">
					<div class="footer_top__title">Свяжитесь с нами</div>
				</div>
				<div class="col-md-5">
					<div class="footer_top__title">Информация</div>
				</div>
			</div>
		</div>
	</div>
	<div class="footer_body">
		<div class="container">
			<div class="row">
				<div class="col-md-5">
					<div class="footer_address_outer">
						<div class="footer_address_item d-flex">
							<p><img class="footer_address_item__icon" src="images/marker.svg" alt="" /></p>
							<jdoc:include type="modules" name="address_info_footer" style="none"/>
						</div>
						<div class="footer_address_item">
							<div class="phone_outer">
								<?php /*
								<img src="images/phone.svg" alt="phone" class="phone_img">
 */ ?>
								<div class="header_phone_block">
									<jdoc:include type="modules" name="phones_block" style="none"/>
								</div>
							</div>
						</div>

						<a class="btn btn_footer popup-with-form" href="#form_footer">Отправьте нам сообщение</a>
					</div>
				</div>
				<div class="col-md-5">
					<jdoc:include type="modules" name="menu_footer" style="none"/>
				</div>
				<div class="col-md-8">
					<div class="social_outer">
						<jdoc:include type="modules" name="social_block_footer" style="none"/>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="footer_sub">
		<div class="container">
			<p class="cooperate">Mogupomogu © Все права защищены.</p>
		</div>
	</div>
</footer>
<div class="prelouder">	<div class="louder"></div></div>
<jdoc:include type="modules" name="footer" style="none"/>


<div class="">
	<!-- form itself -->
	<form id="form_footer" class="uForm mfp-hide white-popup-block form_footer" method="post">
		<input class="uForm__extended" name="nospam" type="text" value="uform-empty" required>
		<h1>Оставьте сообщение</h1>
		<fieldset>
			<div>
				<p>
					<label for="form_footer_name">*ФИО</label>
					<input id="form_footer_name" name="name" type="text" placeholder="ФИО" required="">
				</p>
				<p>
					<label for="form_footer_email">*Email</label>
					<input id="form_footer_email" name="email" type="email" placeholder="example@domain.com" required="">
				</p>
				<p>
					<label for="form_footer_phone">Телефон</label>
					<input id="form_footer_phone" name="phone" type="tel" >
				</p>
				<p>
					<label for="form_footer_textarea">Ваше сообщениее</label><br>
					<textarea id="form_footer_textarea" class="form_footer_textarea" name="message" required></textarea>
				</p>
			</div>
		</fieldset>



		<input class="btn btn_accent" type="submit" value="Отправить">
		<p id="uForm__error-msg" class="uForm__error-msg"></p>
		<div id="uForm__preload" class="uForm__preload"></div>
	</form>

	<div id="uForm__overlay" class="uForm__overlay"></div>

	<div id="uForm__modal" class="uForm__modal">
		<p class="uForm__modal-text">Запрос успешно отправлен</p>
		<button>Закрыть</button>
	</div>

</div>


<script src="/templates/<?php echo $this->template; ?>/js/scripts.min.js"></script>
<script src="/templates/custom/uForm/js/script.js"></script>
<script src="/templates/<?php echo $this->template; ?>/js/custom.js"></script>
</body>
</html>