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

	 */
	defined ('_JEXEC') or die('Restricted access');
	$toolTipArray = array('className'=>'djcf');
	JHTML::_('behavior.tooltip', '.Tips1', $toolTipArray);
//$par = JComponentHelper::getParams( 'com_djclassifieds' );
	$par = DJClassifiedsParams::getParams();
	$user= JFactory::getUser();
	$app = JFactory::getApplication();
	$active_m = $app->getMenu('site')->getActive();

	jimport('joomla.application.module.helper');
	$attribs['style'] = 'none';

	?>

<div class="main_body">
	<div class="row">
		<div class="col-lg-14">
			<div class="adt_block bg-white">
				<div class="bg-white__header">
					<div class="row">
						<div class="col-md-10">
							<div class="h1"><?=  $active_m->title ?></div>
						</div>
						<div class="col-md-8">
						</div>
					</div>
				</div>
				<div class="adt_items">
					<div class="row">
						<?php foreach ($this->items as $item){ ?>
							<div class="col-sm-9">
								<div class="adt_item">
									<a href="<?= JRoute::_(DJClassifiedsSEO::getItemRoute($item->id.':'.$item->alias,$item->cat_id.':'.$item->c_alias,$item->region_id.':'.$item->r_name)) ?>">
										<img src="<?= $item->images['0']->thumb_item_main?>" alt="<?= $item->name ?>" class="adt_item__img">
									</a>
									<div class="adt_item_info">
										<a href="<?= JRoute::_(DJClassifiedsSEO::getItemRoute($item->id.':'.$item->alias,$item->cat_id.':'.$item->c_alias,$item->region_id.':'.$item->r_name)) ?>" class="adt_item__header"><?= $item->name ?></a>
										<div class="adt_item__date"><?= date("d.m.Y", strtotime( $item->date_start) )?></div>
										<?php /*
											<a href="<?= JRoute::_(DJClassifiedsSEO::getCategoryRoute($item->cat_id.':'.$item->c_alias)) ?>" class="adt_item__category"><?= $item->c_name ?></a>
					            */ ?>
										<?php	if($item->fields && $item->fields[1] != '---'){ ?>
											<div class="label" data-class="<?= $item->fields[1]?>"><?= $item->fields[1]?></div>
										<?php	}		?>
										<div class="">
											<a href="<?= DJClassifiedsSEO::getRegionRoute($item->region_id.':'.$item->r_name) ?>" class="adt_item__sity"><?= $item->r_name ?></a>
										</div>
									</div>
								</div>
							</div>
						<?php } ?>
					</div>
					<?php if($this->pagination->getPagesLinks()){ ?>
						<div class="pagination">
							<?php echo $this->pagination->getPagesLinks(); ?>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>

		<div class="col-lg-4">
			<div class="bg-white mb-5">
				<div class="bg-white__header ">
					<div class="h2">Все регионы <img src="images/marker.svg" alt="map" class="marker-map__img"></div>
				</div>
				<div class="all-region_outer">
					<?php
						$modules = JModuleHelper::getModules('menu_regions_adt');
						foreach($modules as $module){
							echo JModuleHelper::renderModule($module, $attribs);
						}
					?>
				</div>
			</div>
			<div class="bg-white mb-5">
				<div class="bg-white__header">
					<div class="h2">Случайное объявление</div>
				</div>
				<div class="">
					<?php
						$modules = JModuleHelper::getModules('random_adt');
						foreach($modules as $module){
						echo JModuleHelper::renderModule($module, $attribs);
						}
					?>
				</div>
			</div>
		</div>
	</div>
</div>

