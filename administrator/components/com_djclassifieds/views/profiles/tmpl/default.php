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
//$limit = JRequest::getVar('limit', 25, '', 'int');
//$limitstart = JRequest::getVar('limitstart', 0, '', 'int');

$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');

$canOrder	= true; //$user->authorise('core.edit.state', 'com_contact.category');
/*
if($listOrder == 'i.ordering' && $this->state->get('filter.category')>0){
	$saveOrder	= true;	
}else{
	$saveOrder	= false;
}*/
$saveOrder	= $listOrder == 'i.ordering'; 
$par = JComponentHelper::getParams( 'com_djclassifieds' );
?>
<form action="index.php?option=com_djclassifieds&view=profiles" method="post" name="adminForm" id="adminForm">
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
				<select name="filter_active" class="inputbox" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('COM_DJCLASSIFIEDS_SELECT_ACTIVE');?></option>
					<?php echo JHtml::_('select.options', array(JHtml::_('select.option', '1', 'COM_DJCLASSIFIEDS_ACTIVE'),JHtml::_('select.option', '0', 'COM_DJCLASSIFIEDS_INACTIVE')), 'value', 'text', $this->state->get('filter.active'), true);?>
				</select>
			</div>			
			<div class="btn-group pull-right hidden-phone">
				<select name="filter_attachment" class="inputbox" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('COM_DJCLASSIFIEDS_SELECT_ATTACHMENT');?></option>
					<?php echo JHtml::_('select.options', array(JHtml::_('select.option', '1', 'COM_DJCLASSIFIEDS_WITH_ATTACHMENT'),JHtml::_('select.option', '0', 'COM_DJCLASSIFIEDS_WITHOUT_ATTACHMENT')), 'value', 'text', $this->state->get('filter.attachment'), true);?>
				</select>
			</div>			
			<div class="btn-group pull-right hidden-phone">
				<select name="filter_verified" class="inputbox" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('COM_DJCLASSIFIEDS_SELECT_VERIFIED');?></option>
					<?php echo JHtml::_('select.options', array(JHtml::_('select.option', '1', 'COM_DJCLASSIFIEDS_VERIFIED_SELLER'),JHtml::_('select.option', '0', 'COM_DJCLASSIFIEDS_NOT_VERIFIED')), 'value', 'text', $this->state->get('filter.verified'), true);?>
				</select>
			</div>	
		</div>
		<div class="clr"> </div>
	    <table class="table table-striped djcf-items-table" width="100%">
	        <thead>
	            <tr>
	                <th width="5%">
	                    <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
	                </th>
	                <th width="5%">
						<?php echo JHtml::_('grid.sort', JText::_('COM_DJCLASSIFIEDS_ID'), 'u.id', $listDirn, $listOrder); ?>
	                </th>
	                <th width="20%" >
						<?php echo JHtml::_('grid.sort', JText::_('COM_DJCLASSIFIEDS_NAME'), 'u.name', $listDirn, $listOrder); ?>
	                </th>
					<th width="20%">
						<?php echo JHtml::_('grid.sort', JText::_('JGLOBAL_USERNAME'), 'u.username', $listDirn, $listOrder); ?>
	                </th>
	                 <th width="30%">
						<?php echo JHtml::_('grid.sort', JText::_('JGLOBAL_EMAIL'), 'u.email', $listDirn, $listOrder); ?>
	                </th>
	                <th width="10%">
						<?php echo JHtml::_('grid.sort', JText::_('COM_DJCLASSIFIEDS_ADVERTS'), 'i.u_items', $listDirn, $listOrder); ?>
	                </th>
	                 <th width="10%">
						<?php echo JHtml::_('grid.sort', JText::_('COM_DJCLASSIFIEDS_POINTS'), 'p.u_points', $listDirn, $listOrder); ?>
	                </th>
					 <th width="10%">
						<?php echo JText::_('COM_DJCLASSIFIEDS_JOOMLA_PROFILE'); ?>
	                </th>
	                 <th width="10%">
						<?php echo JText::_('COM_DJCLASSIFIEDS_EXPORT_USER_DETAILS'); ?>
	                </th>
	            </tr>
	        </thead>
	        <?php 
			$n = count($this->items);
		foreach($this->items as $i => $item){	
		?>
	        <tr>
	            <td>
	               <?php echo JHtml::_('grid.id', $i, $item->id); ?>
	            </td>
	            <td>
	                <?php echo $item->id; ?>
	            </td>
	            <td valign="center" >
	                <?php
					echo '<a href="index.php?option=com_djclassifieds&task=profile.edit&id='.(int) $item->id.'">';
						if($item->img_name){
							echo '<img src="'.JURI::root(true).$item->img_path.$item->img_name.'_ths.'.$item->img_ext.'" width="50px" /> ';	
						}
					echo $item->name.'</a> ';?>
					<?php /* if(!$item->active){ ?>
						<span class="icon icon-ban-circle" title="<?php echo JText::_('COM_DJCLASSIFIEDS_INACTIVE'); ?>"> </span>
					<?php } */ ?>
					<?php if($item->verified){ ?>
						<span class="icon icon-checkmark-circle" title="<?php echo JText::_('COM_DJCLASSIFIEDS_VERIFIED_SELLER'); ?>"> </span>
					<?php } ?>
					<?php if($item->attachment){ ?>
						<a href="index.php?option=com_djclassifieds&format=raw&task=download&fid=<?php echo $item->attachment; ?>" taget="_blank"><span class="icon icon-attachment" title="<?php echo JText::_('COM_DJCLASSIFIEDS_DOWNLOAD_LATEST_ATTACHMENT'); ?>"></span></a>&nbsp;
					<?php } ?>
				</td>	
	            <td>
	                <?php echo $item->username; ?>
	            </td> 
	            <td>
	                <?php echo $item->email; ?>
	            </td>
	            <td align="center" >
	                <?php 
	                echo '<a target="_blank" href="index.php?option=com_djclassifieds&view=items&filter_search='.$item->email.'">';
	                	echo ($item->u_items)? $item->u_items : '0' ; 
	                echo '</a>'; ?>
	            </td>
	            <td align="center" >
	            	<?php 
	            	echo '<a target="_blank" href="index.php?option=com_djclassifieds&view=userspoints&filter_search='.$item->email.'">';
	                	echo ($item->u_points)? $item->u_points : '0' ; 
	                echo '</a>'; ?>
	            </td> 
	            <td valign="center" >
	                <?php
					echo '<a href="index.php?option=com_users&task=user.edit&id='.(int) $item->id.'">'. JText::_('COM_DJCLASSIFIEDS_EDIT_JOOMLA_PROFILE').'</a> ';?>
				</td>	
				<td valign="center" >
					<button class="btn button" onclick="return listItemTask('cb<?php echo $i?>','profiles.exportUserData');"><?php echo JText::_('COM_DJCLASSIFIEDS_EXPORT'); ?></button>
				</td>
	        </tr>
	        <?php 
			} ?>
	    
	    <tfoot>
	        <td colspan="12">
	            <?php echo $this->pagination->getListFooter(); ?>
	        </td>
	    </tfoot>
		</table>
		<input type="hidden" name="task" value="profiles" />
		<input type="hidden" name="option" value="com_djclassifieds" />
		<input type="hidden" name="boxchecked" value="0" />
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
<script type="text/javascript">
    Joomla.submitbutton = function(task)
    {
        if (task == 'profiles.cleardata')
        {
            if (confirm('<?php echo DJClassifiedsTheme::sanitizePopupText(JText::_('COM_DJCLASSIFIEDS_CLEAR_PROFILE_DATA_CONFIMR')); ?>')) {                
				Joomla.submitform(task);
            } else {
                return false;
            }
        }else{
			Joomla.submitform(task);
		}
    };
</script>
<?php echo DJCFFOOTER; ?>