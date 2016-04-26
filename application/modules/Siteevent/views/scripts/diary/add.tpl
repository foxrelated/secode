<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: add.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteevent.css');
?>
<?php if ($this->success == 1): ?>
    <div class="siteevent_diary_popup_list">
        <div class='siteevent_diary_popup_item'>
            <?php echo $this->htmlLink($this->siteevent->getHref(), $this->itemPhoto($this->siteevent, 'thumb.normal'), array('target' => '_blank')); ?>
        </div>
        <div class="siteevent_diary_popup_item_detail">
            <div class="siteevent_diary_popup_item_title">		
                <?php echo $this->htmlLink($this->siteevent->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($this->siteevent->getTitle(), 99), array('class' => 'siteevent_diary_popup_item_title', 'target' => '_blank', 'title' => $this->siteevent->getTitle())) ?>
            </div>
            <?php if (Count($this->diaryNewDatas)): ?>
                <b><?php echo $this->translate("You have added this event to the diaries:"); ?></b>
                <ul class="clr">
                    <?php foreach ($this->diaryNewDatas as $diaryNewData): ?>
                        <li><?php echo $this->htmlLink($diaryNewData->getHref(), $diaryNewData->getTitle(), array('target' => '_blank')) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <?php if (Count($this->diaryOldDatas)): ?>
                <b><?php echo $this->translate("You have removed this event from the diaries:"); ?></b>
                <ul class="clr">
                    <?php foreach ($this->diaryOldDatas as $diaryOldData): ?>
                        <li><?php echo $this->htmlLink($diaryOldData->getHref(), $diaryOldData->getTitle(), array('target' => '_blank')) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>     
        <div class="clr mtop10 fleft widthfull">
            <table width="100%">
                <tr>
                    <td align="left"><button onclick="javascript:parent.Smoothbox.close()"><?php echo $this->translate('Close'); ?></button></td>
                    <td class="siteevent_diary_popup_item_detail_more" align="right">
                        <?php echo $this->htmlLink(array('route' => "siteevent_diary_general", 'action' => 'browse'), $this->translate('Browse Diaries &raquo;'), array('target' => '_blank')) ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>
<?php else: ?>
    <?php if (empty($this->can_add)): ?>
        <div class="global_form_popup">	
            <div class="tip">
                <span>
                    <?php echo $this->translate("Oops! Something went wrong and you can not add this event to your diary. Please try again after sometime."); ?>
                </span>
            </div><br />
            <button onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate("Close"); ?></button>
        </div>
        <?php return; ?>
    <?php endif; ?> 
    <div class='siteevent_diary_popup'>
        <?php echo $this->form->render($this) ?>
    </div>  
<?php endif; ?> 

<?php if (@$this->closeSmoothbox): ?>
    <script type="text/javascript">
                        TB_close();
    </script>
<?php endif; ?>  