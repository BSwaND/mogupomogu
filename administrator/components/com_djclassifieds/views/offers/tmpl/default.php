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
jimport( 'joomla.html.editor' );
JHTML::_('behavior.calendar');
JHTML::_('behavior.modal');
JHtml::_('bootstrap.modal');
$editor 	= JFactory::getEditor();
//$limit = JRequest::getVar('limit', 25, '', 'int');
//$limitstart = JRequest::getVar('limitstart', 0, '', 'int');

$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');

$canoffer	= true; //$user->authorise('core.edit.state', 'com_contact.category');
/*
if($listOrder == 'i.offering' && $this->state->get('filter.category')>0){
	$saveoffer	= true;	
}else{
	$saveoffer	= false;
}*/
$saveoffer	= $listOrder == 'i.id'; 
$par = JComponentHelper::getParams( 'com_djclassifieds' );
?>
<form action="index.php?option=com_djclassifieds&view=offers" method="post" name="adminForm" id="adminForm">
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
				<select name="filter_status" class="inputbox" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('COM_DJCLASSIFIEDS_SELECT_STATUS');?></option>
					<?php echo JHtml::_('select.options', array(
						JHtml::_('select.option', '0', 'COM_DJCLASSIFIEDS_WAITING_FOR_USER_RESPONSE'),
						JHtml::_('select.option', '1', 'COM_DJCLASSIFIEDS_ACCEPTED_WAITING_FOR_USER_PAYMENT'),
						JHtml::_('select.option', '2', 'COM_DJCLASSIFIEDS_REJECTED_BY_USER'),
						JHtml::_('select.option', '3', 'COM_DJCLASSIFIEDS_PAID_WAITING_FOR_CONFIRMATION'),
						JHtml::_('select.option', '4', 'COM_DJCLASSIFIEDS_CONFIRMED_WAITING_FOR_REQUEST'),
						JHtml::_('select.option', '5', 'COM_DJCLASSIFIEDS_REQUEST_FOR_PAYMENT'),
						JHtml::_('select.option', '6', 'COM_DJCLASSIFIEDS_COMPLETED')), 'value', 'text', $this->state->get('filter.status'), true);?>
				</select> 
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
					<?php echo JHtml::_('grid.sort', JText::_('COM_DJCLASSIFIEDS_ID'), 'o.id', $listDirn, $listOrder); ?>
                </th>
                <th width="20%">
					<?php echo JHtml::_('grid.sort', JText::_('COM_DJCLASSIFIEDS_ADVERT'), 'i.name', $listDirn, $listOrder); ?>					
                </th>                
                <th width="10%" >
					<?php echo JHtml::_('grid.sort', JText::_('COM_DJCLASSIFIEDS_BUYER'), 'u.name', $listDirn, $listOrder); ?>
                </th>       
                <th width="10%" >
					<?php echo JHtml::_('grid.sort', JText::_('COM_DJCLASSIFIEDS_SELLER'), 'i.ui_name', $listDirn, $listOrder); ?>
                </th>    
                <th width="10%" >
					<?php echo JHtml::_('grid.sort', JText::_('COM_DJCLASSIFIEDS_PRICE'), 'o.price', $listDirn, $listOrder); ?>
                </th>      
                <th width="10%">
					<?php echo JText::_( 'COM_DJCLASSIFIEDS_OFFER_MESSAGE' ); ?>					
                </th>
                <th width="10%">
					<?php echo JText::_( 'COM_DJCLASSIFIEDS_OFFER_RESPONSE' ); ?>					
                </th>                                                                                  
                <th width="10%" >
					<?php echo JHtml::_('grid.sort', JText::_('COM_DJCLASSIFIEDS_DATE'), 'o.date', $listDirn, $listOrder); ?>
                </th>
                <th width="10%" >
					<?php echo JText::_( 'COM_DJCLASSIFIEDS_STATUS' ); ?>			
                </th>
             </tr>
        </thead>
        <?php 
		$n = count($this->offers);
	foreach($this->offers as $i => $offer){
	?>
        <tr>
            <td>
               <?php echo JHtml::_('grid.id', $i, $offer->id); ?>
            </td>
            <td>
               <?php echo $offer->id; ?>
            </td>
            <td >
                <?php  					
					echo '<a href="index.php?option=com_djclassifieds&task=item.edit&id='.(int) $offer->item_id.'">'.$offer->i_name.'</a>';
				?>
			</td>
            <td >
                <?php echo $offer->u_name.' ( id '.$offer->user_id.' )<br />'.$offer->u_email; ?>                
				<button type="button" class="btn btn-primary" data-toggle="modal" data-target=".djmodal_buyer<?php echo $offer->id; ?>"><?php echo JText::_('COM_DJCLASSIFIEDS_SEND_MESSAGE');?></button>                                                               
			</td>
			<td >
                <?php echo $offer->ui_name.'( id '.$offer->ui_id.' ) <br />'.$offer->ui_email; ?>                
				<button type="button" class="btn btn-primary" data-toggle="modal" data-target=".djmodal_seller<?php echo $offer->id; ?>"><?php echo JText::_('COM_DJCLASSIFIEDS_SEND_MESSAGE');?></button>                               
			</td>
			<td>
                <?php  echo $offer->price; ?>
            </td>			
			<td>
                <?php  echo $offer->message; ?>
            </td>
            <td>
                <?php  echo $offer->response; ?>
            </td>
            <td>
                <?php  echo $offer->date; ?>
            </td>								
            <td>               	
            	<?php
            	$admin_status = 0;  
                	if($offer->admin_paid==1){
                		//echo JText::_('COM_DJCLASSIFIEDS_COMPLETED');
						$admin_status = 6;
                	}elseif($offer->request==1){
                		//echo JText::_('COM_DJCLASSIFIEDS_REQUEST_FOR_PAYMENT');
						$admin_status = 5;
                	}else if($offer->confirmed==1){
                		//echo JText::_('COM_DJCLASSIFIEDS_CONFIRMED_WAITING_FOR_REQUEST');
						$admin_status = 4;
                	}else if($offer->paid==1){
                		//echo JText::_('COM_DJCLASSIFIEDS_PAID_WAITING_FOR_CONFIRMATION');
						$admin_status = 3;
                	}else if($offer->status==2){
                		//echo JText::_('COM_DJCLASSIFIEDS_REJECTED_BY_USER');
						$admin_status = 2;
                	}else if($offer->status==1){
                		$admin_status = 1;
                		//echo JText::_('COM_DJCLASSIFIEDS_ACCEPTED_WAITING_FOR_USER_PAYMENT');
                	}
                ?>
            	                              
                <select name="change_status_<?php echo $offer->id; ?>" class="inputbox" autocomplete="off">			
					<?php echo JHtml::_('select.options', array(
						JHtml::_('select.option', '0', 'COM_DJCLASSIFIEDS_WAITING_FOR_USER_RESPONSE'),
						JHtml::_('select.option', '1', 'COM_DJCLASSIFIEDS_ACCEPTED_WAITING_FOR_USER_PAYMENT'),
						JHtml::_('select.option', '2', 'COM_DJCLASSIFIEDS_REJECTED_BY_USER'),
						JHtml::_('select.option', '3', 'COM_DJCLASSIFIEDS_PAID_WAITING_FOR_CONFIRMATION'),
						JHtml::_('select.option', '4', 'COM_DJCLASSIFIEDS_CONFIRMED_WAITING_FOR_REQUEST'),
						JHtml::_('select.option', '5', 'COM_DJCLASSIFIEDS_REQUEST_FOR_PAYMENT'),
						JHtml::_('select.option', '6', 'COM_DJCLASSIFIEDS_COMPLETED')
					
					), 'value', 'text', $admin_status, true); ?>
				</select>
				<a title="Change status" onclick="return listItemTask('cb<?php echo $i;?>','offers.changeStatus')" href="javascript:void(0);" class="jgrid"><span class="button"><?php echo JText::_('COM_DJCLASSIFIEDS_SELECT_CHANGE_STATUS');?></span></a>	
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
	<input type="hidden" name="task" value="offers" />
	<input type="hidden" name="option" value="com_djclassifieds" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

<?php	foreach($this->offers as $i => $offer){ ?>
					<div class="modal hide fade djmodal_buyer<?php echo $offer->id; ?>">
				  <div class="modal-header">
				    <button type="button" role="presentation" class="close" data-dismiss="modal">x</button>
				    <h3><?php echo JText::_('COM_DJCLASSIFIEDS_MESSAGE_TO_USER')?></h3>
				  </div>
				  <form action="index.php?option=com_djclassifieds&task=offers.sendMessage" method="post" name="adminFormEmail" enctype='multipart/form-data'>
					  <div class="modal-body form-horizontal" style="overflow:scroll;">	  		  	  
					  		<div class="control-group">
								<div class="control-label"><?php echo JText::_('JGLOBAL_EMAIL'); ?></div>
								<div class="controls">
									<input type="text" name="djmsg_email" value="<?php echo $offer->u_email;?>"  />
								</div>
							</div>
					  
					  		<div class="control-group">
								<div class="control-label"><?php echo JText::_('COM_DJCLASSIFIEDS_TITLE'); ?></div>
								<div class="controls">
									<input type="text" name="djmsg_title" value=""  />
								</div>
							</div>
							
							<div class="control-group">
								<div class="control-label"><?php echo JText::_('COM_DJCLASSIFIEDS_EMAIL_BODY'); ?></div>
								<div class="controls">
									<?php echo $editor->display( 'djmsg_description', '' , '100%', '150', '50', '20',true ); ?> 
								</div>
							</div>
					  				   	  		    
					  </div>
					  <div class="modal-footer">
					  	<input type="submit" value="<?php echo JText::_('COM_DJCLASSIFIEDS_SEND'); ?>" class="btn" />
					    <button class="btn" type="button" data-dismiss="modal">
					      <?php echo JText::_('JCANCEL'); ?>
					    </button>
					  </div>
					  <input type="hidden" name="djmsg_id" value="<?php echo $offer->id; ?>" />
				  </form>
				</div> 
				
				
				
				<div class="modal hide fade djmodal_seller<?php echo $offer->id; ?>">
				  <div class="modal-header">
				    <button type="button" role="presentation" class="close" data-dismiss="modal">x</button>
				    <h3><?php echo JText::_('COM_DJCLASIFIEDS_MESSAGE_TO_USER')?></h3>
				  </div>
				  <form action="index.php?option=com_djclassifieds&task=offers.sendMessage" method="post" name="adminFormEmail" enctype='multipart/form-data'>
					  <div class="modal-body form-horizontal" style="overflow:scroll;">	  		  	  
					  		<div class="control-group">
								<div class="control-label"><?php echo JText::_('JGLOBAL_EMAIL'); ?></div>
								<div class="controls">
									<input type="text" name="djmsg_email" value="<?php echo $offer->ui_email;?>"  />
								</div>
							</div>
					  
					  		<div class="control-group">
								<div class="control-label"><?php echo JText::_('COM_DJCLASSIFIEDS_TITLE'); ?></div>
								<div class="controls">
									<input type="text" name="djmsg_title" value=""  />
								</div>
							</div>
							
							<div class="control-group">
								<div class="control-label"><?php echo JText::_('COM_DJCLASSIFIEDS_EMAIL_BODY'); ?></div>
								<div class="controls">
									<?php echo $editor->display( 'djmsg_description', '' , '100%', '150', '50', '20',true ); ?> 
								</div>
							</div>
					  				   	  		    
					  </div>
					  <div class="modal-footer">
					  	<input type="submit" value="<?php echo JText::_('COM_DJCLASSIFIEDS_SEND'); ?>" class="btn" />
					    <button class="btn" type="button" data-dismiss="modal">
					      <?php echo JText::_('JCANCEL'); ?>
					    </button>
					  </div>
					  <input type="hidden" name="djmsg_id" value="<?php echo $offer->id; ?>" />
				  </form>
				</div>   				
				
				
<?php }?>

<?php echo DJCFFOOTER; ?>