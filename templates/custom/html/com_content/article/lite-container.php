<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

?>

<?php include_once(JPATH_BASE .'/templates/custom/html/com_content/article/_header.php');  ?>

<div class="item-page<?php echo $this->pageclass_sfx; ?>" itemscope itemtype="https://schema.org/Article">
	<meta itemprop="inLanguage" content="<?php echo ($this->item->language === '*') ? JFactory::getConfig()->get('language') : $this->item->language; ?>" />
	<div class="row">
		<div class="col-md-12">
			<div class="bg-white">
				<div class="bg-white__header mb-3">
					<h1 class="h2"><?= $this->item->title ?></h1>
					<?= $this->item->event->afterDisplayTitle ?>
				</div>
				<div class="p-3">
					<?= $this->item->event->beforeDisplayContent ?>
					<?= $this->item->text ?>
					<?= $this->item->event->afterDisplayContent ?>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="article-banner">
				<img class="article-banner__img" src="<?= json_decode($this->item->images)->image_fulltext ?>" alt="<?= $this->item->title ?>">
				<div class="article-banner__text"><?= json_decode($this->item->images)->image_fulltext_caption?></div>
			</div>
		</div>
	</div>
</div>
<!---->
<!--<pre>-->
<!--	--><?php
//		print_r(json_decode($this->item->images));
//		print_r($this->item);
//	?>
<!--</pre>-->

