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
                <label for="batch_verified"><?php echo JText::_('COM_DJCLASSIFIEDS_BATCH_VERIFIED_LABEL');?></label>
                <select id="batch_verified" name="batch_verified">
                    <option value=""><?php echo JText::_('COM_DJCLASSIFIEDS_BATCH_SELECT_VERIFIED');?></option>
                    <option value="1"><?php echo JText::_('COM_DJCLASSIFIEDS_YES');?></option>
                    <option value="0"><?php echo JText::_('COM_DJCLASSIFIEDS_NO');?></option>
                </select>
            </div>
        </div>
        <div class="control-group span6">
            <div class="controls">
                <label for="batch_ugid"><?php echo JText::_('COM_DJCLASSIFIEDS_BATCH_USERGROUP_LABEL');?></label>
                <select id="batch_ugid" name="batch_ugid">
                    <option value=""><?php echo JText::_('COM_DJCLASSIFIEDS_BATCH_SELECT_USERGROUP');?></option>
                    <?php echo JHtml::_('select.options', $this->batch_usergroups, 'value', 'text', $this->state->get('batch_ugid'));?>
                </select>
            </div>
        </div>
    </div>
    <div class="row-fluid">
        <div class="control-group span6">
            <div class="controls">
                <label for="batch_email"><?php echo JText::_('COM_DJCLASSIFIEDS_BATCH_EMAIL_LABEL');?></label>
                <textarea id="batch_email" name="batch_email" placeholder="<?php echo JText::_('COM_DJCLASSIFIEDS_BATCH_PROFILES_SEND_EMAIL_PLACEHOLDER');?>"></textarea>
            </div>
        </div>
    </div>
</div>