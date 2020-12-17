<?php
	/**
	 * @version 2.0
	 * @package DJ Classifieds Menu Module
	 * @subpackage DJ Classifieds Component
	 * @copyright Copyright (C) 2010 DJ-Extensions.com LTD, All rights reserved.
	 * @license http://www.gnu.org/licenses GNU/GPL
	 * @author url: http://design-joomla.eu
	 * @author email contact@design-joomla.eu
	 * @developer Łukasz Ciastek - lukasz.ciastek@design-joomla.eu
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

	defined ('_JEXEC') or die('Restricted access');

	if($fields){
		$arrItemsField = [];
		foreach ($fields as $field ){
			$arrItemsField[ $field->item_id] = $field->value;
		}
	}

//	echo '<pre>';
//
//	print_r($arrItemsField);
//
//	echo '<hr>';
//	print_r($items);

	if($items){	?>
		<div class="row">
			<?php foreach ($items as $item){ ?>
				<div class="col-sm-9">
					<div class="adt_item">
						<a href="<?= JRoute::_(DJClassifiedsSEO::getItemRoute($item->id.':'.$item->alias,$item->cat_id.':'.$item->c_alias,$item->region_id.':'.$item->r_name)) ?>">
							<img src="<?= $item->images['0']->thumb_item_main?>" alt="<?= $item->name ?>" class="adt_item__img">
						</a>
						<div class="adt_item_info">
							<a href="<?= JRoute::_(DJClassifiedsSEO::getItemRoute($item->id.':'.$item->alias,$item->cat_id.':'.$item->c_alias,$item->region_id.':'.$item->r_name)) ?>" class="adt_item__header"><?= $item->name ?></a>
							<div class="adt_item__date"><?= date("d.m.Y", strtotime( $item->date_start) )?></div>
							<?php /*
							<a  href="<?= JRoute::_(DJClassifiedsSEO::getCategoryRoute($item->cat_id.':'.$item->c_alias)) ?>" class="adt_item__category"><?= $item->c_name ?></a>
              */ ?>
							<?php	if($arrItemsField[$item->id] && $arrItemsField[$item->id] != '---'){ ?>
								<div class="label" data-class="<?= $arrItemsField[$item->id]?>"><?= $arrItemsField[$item->id]?></div>
							<?php	}		?>
							<div class="adt_item__sity">
								<a href="<?= DJClassifiedsSEO::getRegionRoute($item->region_id.':'.$item->r_name) ?>"><?= $item->r_name ?></a>
							</div>
						</div>
					</div>
				</div>
			<?php } ?>
		</div>
	<?php } ?>