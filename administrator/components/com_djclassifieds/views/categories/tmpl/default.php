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
defined ('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
$canOrder	= true;
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');

?>
<form action="index.php?option=com_djclassifieds&task=categories" method="post" name="adminForm" id="adminForm" >
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
		<div id="filter-bar" class="btn-toolbar">
		<div class="filter-search btn-group pull-left">
				<label class="element-invisible" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
				<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_CONTENT_FILTER_SEARCH_DESC'); ?>" />
			</div>
			<div class="btn-group pull-left">
				<button type="submit" class="btn"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
				<button type="button" class="btn" onclick="document.id('filter_search').value='';jQuery('[name^=filter_]').prop('selectedIndex',0);this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<select name="filter_published" class="inputbox" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
					<?php echo JHtml::_('select.options', array(JHtml::_('select.option', '1', 'JPUBLISHED'),JHtml::_('select.option', '0', 'JUNPUBLISHED')), 'value', 'text', $this->state->get('filter.published'), true);?>
				</select>
			</div>	
			<div class="btn-group pull-right">
				<?php /* <select name="filter_category" class="inputbox" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
					<option <?php if($this->state->get('filter.category')==-1){ echo 'SELECTED'; }?> value="-1"><?php echo JText::_('COM_DJCLASSIFIEDS_MAIN_CATEGORY');?></option>
					<?php $optionss=DJClassifiedsCategory::getCatSelect();?>			
					<?php echo JHtml::_('select.options', $optionss, 'value', 'text', $this->state->get('filter.category'));?>
				</select> */ ?>
					<?php 
						$app = JFactory::getApplication();
						$cid = $app->input->getInt('filter_category');
						$cats = DJClassifiedsCategory::getSEOParentPath($cid);
						$cats = array_reverse($cats);
					?>
					<div class="btn-group pull-left">
						<select name="filter_category" id="filter_category0" data-id="0" class="inputbox">
							<option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>	
							<option <?php if($this->state->get('filter.category')==-1){ echo 'SELECTED'; }?> value="-1"><?php echo JText::_('COM_DJCLASSIFIEDS_MAIN_CATEGORY');?></option>
							<?php echo JHtml::_('select.options', DJClassifiedsCategory::getSubCatSelect('0'), 'value', 'text', (int)$cats[0]);?>
						</select>
					</div>
					<?php foreach($cats as $key => $cat){ ?>
						<?php 
							$optionsss = DJClassifiedsCategory::getSubCatSelect((int)$cat); 
							if(!$optionsss) break;
							$sel = isset($cats[$key+1]) ? (int)$cats[$key+1] : '0';
						?>
						<div class="btn-group pull-left">
							<select name="filter_category" id="filter_category<?php echo (int)$cat; ?>" data-id="<?php echo (int)$cat; ?>" class="inputbox">
								<option value=""><?php echo JText::_('COM_DJCLASSIFIEDS_SELECT_SUBCATEGORY');?></option>	
								<?php echo JHtml::_('select.options', $optionsss, 'value', 'text', $sel);?>
							</select>
						</div>
					<?php } ?>
					<script>
						jQuery(function($){
							$('select[name="filter_category"]').change(function(){
								var id = parseInt($(this).attr('data-id'));
								$('select[name="filter_category"]').each(function(i, item){
									if(parseInt($(item).attr('data-id')) > id){
										$(item).prop('disabled', true);
									}
								});
								if(!$(this).val() && id!=0){
									$(this).prop('disabled', true);
								}
								$('#adminForm').submit();
							});
						});
					</script>
			</div>
		</div>
	<div class="clr"> </div>
    <table class="table table-striped" width="100%">
        <thead>
            <tr>
                <th width="5%">
                    <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
                </th>
                <th width="5%">
					<?php echo JHtml::_('grid.sort', JText::_('COM_DJCLASSIFIEDS_ID'), 'id', $listDirn, $listOrder); ?>
                </th>
                <th width="20%">
					<?php echo JHtml::_('grid.sort', JText::_('COM_DJCLASSIFIEDS_NAME'), 'name', $listDirn, $listOrder); ?>
                </th>
                <th width="30%">
					<?php echo JText::_('COM_DJCLASSIFIEDS_DESCRIPTION'); ?>
                </th>                
                <th width="7%">
					<?php echo JHtml::_('grid.sort', JText::_('COM_DJCLASSIFIEDS_ORDERING'), 'ordering', $listDirn, $listOrder); ?>
						<?php
						if($this->state->get('filter.category')!=''){
						 echo JHtml::_('grid.order',  $this->categories, 'filesave.png', 'categories.saveorder'); ?>
					<?php }; ?>
                </th>
                <th width="11%">
					<?php echo JText::_('COM_DJCLASSIFIEDS_PARENT_CATEGORY'); ?>
                </th>
                <th width="7%">
					<?php echo JText::_('COM_DJCLASSIFIEDS_ACCESS_RESTRICTIONS'); ?>
                </th>
                <th width="7%">
					<?php echo JHtml::_('grid.sort', JText::_('COM_DJCLASSIFIEDS_PRICE'), 'price', $listDirn, $listOrder); ?>
                </th>
                <th width="7%">
					<?php echo JText::_('COM_DJCLASSIFIEDS_AUTOPUBLISH'); ?>
                </th>                               
                <th width="5%">
					<?php echo JHtml::_('grid.sort', 'JPUBLISHED', 'published', $listDirn, $listOrder); ?>
					
                </th>
        </thead>
        <?php $i=0; 
	foreach($this->categories as $i =>$c){
	?>
        <tr>
            <td>
               <?php echo JHtml::_('grid.id', $i, $c->id); ?>
            </td>
            <td>
                <?php echo $c->id; ?>
            </td>
            <td>
            	<?php
            	echo '&nbsp';
				if(isset($c->level)){
					if($c->level>0){
						echo '&nbsp';
		            	for($ci=0;$ci<$c->level;$ci++){
		            		echo '-&nbsp';
		            	} 
					}
				}
            	?>
					<a href="<?php echo JRoute::_('index.php?option=com_djclassifieds&task=category.edit&id='.(int) $c->id); ?>">
					<?php echo $this->escape($c->name); ?></a>

            </td>
            <td>
            	<?php 
          			if(strlen(strip_tags($c->description)) > 130){
					   echo mb_substr(strip_tags($c->description), 0, 130,'utf-8').' ...';						
					}else{
						echo $c->description;
					}	
            	?>
            </td>
				<td class="order">
					<?php if($this->state->get('filter.category')!=''){
					$ordering = 'true';				
					?>
								<span><?php echo $this->pagination->orderUpIcon($i, ($c->parent_id == @$this->categories[$i-1]->parent_id),'categories.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>								
								<span><?php echo $this->pagination->orderDownIcon($i, count($this->categories), ($c->parent_id == @$this->categories[$i+1]->parent_id), 'categories.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
						<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
						<input type="text" name="order[]" size="5" value="<?php echo $c->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
					<?php }else{ ?>
						<?php echo $c->ordering; ?>
					<?php } ?>
				</td>           
			<td>
            	<?php 
            	if($c->parent_id>0){
            		echo $c->parent_name.' ('.$c->parent_id.')';	
            	}else{
            		echo '---';
            	}
            	
            	?>
            </td>
             <td>
            	<?php
            	if($c->access == '0'){
					echo JText::_('COM_DJCLASIFIEDS_DEFAULT_INHERIT');
				}elseif($c->access == '1'){
					echo JText::_('COM_DJCLASIFIEDS_RESTRICTED');
				}
            	 ?>
            </td>            
            <td>
            	<?php 
            		if($c->price){
            			echo $c->price/100;
            			echo ' ('.$c->points.JText::_('COM_DJCLASSIFIEDS_POINTS_SHORT').')';
            		}else{
            			echo JText::_('COM_DJCLASSIFIEDS_FREE');
            		}
            	?>
            </td>
            <td>
            	<?php
            	if($c->autopublish == '0'){
					echo JText::_('COM_DJCLASSIFIEDS_GLOBAL');
				}elseif($c->autopublish == '1'){
					echo JText::_('COM_DJCLASSIFIEDS_YES');
				}elseif($c->autopublish == '2'){
					echo JText::_('COM_DJCLASSIFIEDS_NO');
				}
            	 ?>
            </td>
            <td align="center">
                <?php echo JHtml::_('jgrid.published', $c->published, $i, 'categories.', true, 'cb'	); ?>
            </td>

        </tr>
        <?php  
		} ?>
    
    <tfoot>
        <td colspan="10">
            <?php echo $this->pagination->getListFooter(); ?>
        </td>
    </tfoot>
	</table>
    <input type="hidden" name="option" value="com_djclassifieds" />
	<input type="hidden" name="view" value="categories" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="categories" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php
		echo JHtml::_(
			'bootstrap.renderModal',
			'collapseModal',
			array(
				'title' => JText::_('COM_DJCLASSIFIEDS_BATCH_TITLE'),
				'footer' => $this->loadTemplate('batch_footer')
			),
			$this->loadTemplate('batch_body')
		);
	?>
	<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
<?php echo DJCFFOOTER; ?>