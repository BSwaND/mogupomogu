<?php
/**
 * @package DJ-Classifieds
 * @copyright Copyright (C) DJ-Extensions.com, All rights reserved.
 * @license http://www.gnu.org/licenses GNU/GPL
 * @author url: http://dj-extensions.com
 * @author email contact@dj-extensions.com
 */
defined('_JEXEC') or die;
?>

<div class="container-fluid">
    <div class="row-fluid">
        <div class="control-group span6">
            <div class="controls">
                <label for="batch_cid"><?php echo JText::_('COM_DJCLASSIFIEDS_BATCH_PARENT_CATEGORY_LABEL');?></label>
                <select id="batch_cid" name="batch_cid" class="inputbox">
                    <option value="0"><?php echo JText::_('COM_DJCLASSIFIEDS_BATCH_SELECT_PARENT_CATEGORY');?></option>
                    <?php echo JHtml::_('select.options', DJClassifiedsCategory::getCatSelect(), 'value', 'text', $this->state->get('batch_cid'));?>
                </select>
            </div>
        </div>
	</div>
</div>