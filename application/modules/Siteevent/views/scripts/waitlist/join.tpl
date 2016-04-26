<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: join.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class='global_form_popup'>

  <?php if (!empty($this->isLeader) || !empty($this->isHost)): ?>
    <div class="tip">
      <span>
        <?php echo $this->translate("You can not joined this event in waitlist as you are the leader or host of this event.") ?>
        <div class="buttons mtop10">
          <button type="button" name="cancel" onclick="javascript:parent.Smoothbox.close();"><?php echo $this->translate("Close") ?></button>
        </div>
      </span>
    </div>
  <?php return; ?>      
  <?php elseif (!empty($this->waitlist_id)): ?>
    <div class="tip">
      <span>
        <?php echo $this->translate("You have already joined this event in waitlist.") ?>
      </span> 
    </div>
    <div class="buttons mtop10">
      <button type="button" name="cancel" onclick="javascript:parent.Smoothbox.close();"><?php echo $this->translate("Close") ?></button>
    </div>
  <?php return; else:?>  
    <form method="post" class="global_form">
        <div>
            <div>
                <h3><?php echo $this->translate("Join Event in Waitlist?"); ?></h3>
                <p>
                    <?php echo $this->translate('Are you sure you want to join this event in waitlist. After joining in waitlist, event owner will contact you for further action.'); ?>
                </p>
                
                <p>
                    <input type="hidden" name="confirm" value="true"/>
                    <button type='submit'><?php echo $this->translate('Join'); ?></button>
                    <?php echo $this->translate('or'); ?> <a href='javascript:void(0);' onclick="javascript:parent.Smoothbox.close();"><?php echo $this->translate('cancel'); ?></a>
                </p>
            </div>
        </div>
    </form>
 <?php endif;?>
</div>