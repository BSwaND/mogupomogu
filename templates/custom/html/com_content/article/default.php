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

	<?php
			$modules = JModuleHelper::getModules('after_content');
			foreach($modules as $module){
			echo JModuleHelper::renderModule($module, $attribs);
		} ?>
</div>

