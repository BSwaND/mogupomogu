<?php
	          
	defined('_JEXEC') or die;

	$myScript = false; // если "true" - то отключаем все стандатрные скрипты и подключаем свои (в том числе и jQuery)
	$app = JFactory::getApplication();
	$user = JFactory::getUser();
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

	if ($myScript) { // при необходимости отключаем все скрипты и подключаем свежий jQuery (параметр выше)
		$this->_scripts = array();
		unset($this->_script['text/javascript']);
		JHtml::_('script', $template_url . '/js/jquery-3.3.1.min.js', array('version' => 'v=3.3.1'));
	}

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
<body class="<?php echo $pageclass ? htmlspecialchars($pageclass) : 'default'; ?>">


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
								<img src="images/phone.svg" alt="phone" class="phone_img">
								<div class="phone_block">
									<a href="tel:380939393933" class="phone">+380939393933</a>
									<a href="tel:380969396966" class="phone">+380969396966</a>
								</div>
							</div>
						</div>
						<div class="d-flex">
							<a href="/dobavit-obyavlenie" class="btn btn_header">Подать объявление</a>
							<a href="#" class="btn btn_login "></a>
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
		<div class="main_header">
			<form action="" class="form_search">
				<input type="text" placeholder="Поиск по объявлениям ..." class="form_search__input">
				<input type="submit" value="" class="form_search__submit">
				<div class="form_search_dop-filter__control_outer">
					<div class="form_search_dop-filter__control__btn">Фильтр</div>
				</div>

				<div class="form_search_dop-filter">
					<div class="form_search_dop-filter__top-control">
						<div class="row">
							<div class="col-9">
								<label class="input_btn_outer">
									<input type="radio" name="type" checked>
									<span class="input_btn">Помогу</span>
								</label>
							</div>
							<div class="col-9">
								<label class="input_btn_outer">
									<input type="radio" name="type">
									<span class="input_btn">Нуждаюсь</span>
								</label>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-10">
							<label>
								<p class="form_search_dop-filter__name-input">Место расположения</p>
								<input type="text" name="location">
							</label>
							<label>
								<p class="form_search_dop-filter__name-input">Страна</p>
								<input type="text" name="country">
							</label>
							<label>
								<p class="form_search_dop-filter__name-input">Город</p>
								<input type="text" name="sity">
							</label>
							<label>
								<p class="form_search_dop-filter__name-input">Область</p>
								<input type="text" name="region">
							</label>
						</div>
						<div class="col-md-8">
							<div class="form_search_dop-filter__last-col">
								<label class="label__radius">
									<span class="label__radius__marker"></span>
									<input type="text" name="radius"  placeholder="Растояние">
								</label>

								<div class="form_search_dop-filter__control__footer">
									<input class="form_search__btn-footer" type="submit" value="Искать">
									<span class="form_search__btn-footer form_search__btn-footer__reset">Сбросить настройки</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</form>



			<jdoc:include type="modules" name="breadcrumbs" style="none"/>

			<jdoc:include type="component"/>

			<div class="row">
				<div class="col-18">
					<div class="bg-white">
						<div class="bg-white__header">
							<div class="h2">Карта объявления <img src="images/marker-map.svg" alt="map" class="marker-map__img"></div>
						</div>
						<div class="map-block" id="map-block"></div>



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
							<img src="images/marker.svg" alt="" class="footer_address_item__icon">
							<div class="">
								Украина, Одесса <br>
								Балковская улица
							</div>
						</div>
						<div class="footer_address_item">
							<div class="phone_outer">
								<img src="images/phone.svg" alt="phone" class="phone_img">
								<div class="header_phone_block">
									<a href="tel:380939393933" class="phone">+380939393933</a>
									<a href="tel:380969396966" class="phone">+380969396966</a>
								</div>
							</div>
						</div>

						<div class="btn btn_footer">Отправьте нам сообщение</div>
					</div>
				</div>
				<div class="col-md-5">
					<ul class="nav nav_footer">
						<li><a href="#">О нас</a></li>
						<li><a href="#">Политика конфиденциальности</a></li>
						<li><a href="#">Условия о доставке</a></li>
						<li><a href="#">Мой кабинет</a></li>
					</ul>
				</div>
				<div class="col-md-8">
					<div class="social_outer">
						<a href="#" class="social_link"><img src="images/instagram.svg" alt="instagram"></a>
						<a href="#" class="social_link"><img src="images/fb.svg" alt="fb"></a>
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


<script>
	function initMap() {
		var element = document.getElementById('map-block');
		var options = {
			zoom: 12,
			center: {lat: 46.468982, lng: 30.740729 	}

		};

		var myMap = new google.maps.Map(element, options);

		var markers = [
			{
				coordinates: {lat: 46.468982, lng: 30.740729 	},
				info: '<p>Этот объект</p>'
			}
		];


		for(var i = 0; i < markers.length; i++) {
			addMarker(markers[i]);
		}
		//
		// new MarkerClusterer(myMap, markers, {
		// 	imagePath:
		// 			"https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m",
		// });

		function addMarker(properties) {
			var marker = new google.maps.Marker({
				position: properties.coordinates,
				map: myMap
			});

			if(properties.image) {
				marker.setIcon(properties.image);
			}

			if(properties.info) {
				var InfoWindow = new google.maps.InfoWindow({
					content: properties.info
				});

				marker.addListener('click', function(){
					InfoWindow.open(myMap, marker);
				});
			}
		}
	}
</script>

<script async defer  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD05yH55GKkhSphg8Fz8OIueKEp-kq_hkg&callback=initMap&libraries=&v=weekly"></script>
<script src="/templates/<?php echo $this->template; ?>/js/scripts.min.js"></script>

</body>
</html>