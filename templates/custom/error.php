<?php
/**
 * @package     Joomla.Site
 * @subpackage  Templates.protostar
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/** @var JDocumentError $this */

$app  = JFactory::getApplication();
$user = JFactory::getUser();
$document = JFactory::getDocument();

// Getting params from template
$params = $app->getTemplate(true)->params;
$sitename = htmlspecialchars($app->get('sitename'), ENT_QUOTES, 'UTF-8');

?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>">
<head>
	<meta charset="utf-8" />
	<title><?php echo $this->title; ?> <?php echo htmlspecialchars($this->error->getMessage(), ENT_QUOTES, 'UTF-8'); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,700&amp;display=swap&amp;subset=cyrillic" rel="stylesheet">
	<link href="<?php echo JUri::root(); ?>/templates/<?php echo $this->template; ?>/icon/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon" />
	<base href="<?php echo JUri::root(); ?>">
    <link href="/templates/<?php echo $this->template; ?>/css/bootstrap.min.css" rel="stylesheet" />
    <link href="/templates/<?php echo $this->template; ?>/css/owl.carousel.css" rel="stylesheet" />
    <link href="/templates/<?php echo $this->template; ?>/css/owl.theme.default.css" rel="stylesheet" />
    <link href="/templates/<?php echo $this->template; ?>/css/fa5-all.min.css" rel="stylesheet" />
    <link href="/templates/<?php echo $this->template; ?>/css/template.css" rel="stylesheet" />
    <link href="/templates/<?php echo $this->template; ?>/css/style.css" rel="stylesheet" />
    <script src="/media/jui/js/jquery.min.js"></script>
    <script src="/media/jui/js/jquery-noconflict.js"></script>
    <script src="/media/jui/js/jquery-migrate.min.js"></script>
</head>
<body class="page404">
    <header class="header">
        <div class="container">
            <div class="row mx-0">
				<div class="logo">
					<a href="/">
						<img src="/templates/<?php echo $this->template; ?>/icon/logo.png" alt="logo">
					</a>
				</div>
				<div class="burger-menu">
					<span></span>
					<span></span>
					<span></span>
					<span></span>
				</div>
	            <?php echo $document->getBuffer('modules', 'position-7', array('style' => 'none')); ?>
	            <?php echo $document->getBuffer('modules', 'menu', array('style' => 'none')); ?>
            </div>
        </div>
    </header>
    <section class="content404">
        <div class="container">
            <div class="error">
                <h1 class="error__title">Упс...что-то пошло не так</h1>
                <p class="error__subtitle">Вы находитесь здесь, потому что ввели адрес страницы, которая уже
                    не существует или была перемещена по другому адресу</p>
                <p class="number-error-big">404</p>
                <div class="go-main">
                    <a class="all-btn main-btn" href="/"><span>На Главную</span></a>
                </div>
            </div>
        </div>
    </section>
	<div class="footer">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <p>&copy; <?php echo date('Y'); ?> <?php echo $sitename; ?></p>
                </div>
            </div>
        </div>
	</div>
	<script src="/templates/<?php echo $this->template; ?>/js/owl.carousel.js"></script>
    <script src="/templates/<?php echo $this->template; ?>/js/script.js"></script>
</body>
</html>
