<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventinvite
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: invitepreview.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteeventinvite/externals/styles/style_siteeventinvite.css');
?>
<div class="global_form_popup">
  <h3><?php echo $this->translate("Invite to your Event") ?></h3>


  <div class="siteeventinvite_popup_heading"><?php echo $this->translate("Preview your invitation") ?></div>


  <?php if (!empty($this->is_suggenabled)) : ?>

    <?php echo $this->translate("To the invitees who are already on %s, a suggestion for your Event will be sent.", $this->site_title) ?>
    <?php echo '<div class="siteeventinvite_popup_suggestion"><span class="siteevent_notification"><a href="javascript:void(0);" >' . $this->viewer_name . '</a>' . $this->translate(" has suggested you to visit and explore the Event: ") . '<a href="javascript:void(0);" >' . $this->siteevent->title . '</a>.</span></div>'; ?>
    <div class="siteeventinvite_popup_suggestion_preview">
      <ul class="requests">
        <li style="margin-bottom:0px;">
          <?php echo $this->itemPhoto($this->siteevent, 'thumb.icon', ''); ?>
          <div>
            <div>
              <?php echo '<a href="javascript:void(0);">' . $this->viewer_name . '</a>' . $this->translate(" has sent you a event suggestion:") ?> <a href="javascript:void(0);"><?php echo $this->siteevent->title; ?> </a>
            </div>
            <div>
              <button type="submit">
                <?php echo $this->translate('View this Event'); ?>
              </button>
              <?php echo $this->translate('or'); ?> <a href="javascript:void(0);"> <?php echo $this->translate('ignore request'); ?>  </a>
            </div>
          </div>	
        </li>
      </ul>
    </div>	
  <?php else : ?>

    <?php echo $this->translate("To the invitees who are already on %s, a suggestion notification for your Event will be sent.", $this->site_title) ?>
    <?php echo '<div class="siteeventinvite_popup_suggestion"><span class="siteevent_notification"><a href="javascript:void(0);" >' . $this->viewer_name . '</a>' . $this->translate(" has suggested you to visit and explore the Event: ") . '<a href="javascript:void(0);" >' . $this->siteevent->title . '</a>.</span></div>'; ?>

  <?php endif; ?>
  <br />
  <?php echo $this->translate("Additionally, an email will also be sent to the invitees who are not on %s.", $this->site_title) ?>
  <div class="siteeventinvite_popup_email_preview">
    <?php echo $this->bodyHtmlTemplate; ?>
  </div>
  <div class="buttons" >
    <button name="invitepreview"  id="invitepreview" onclick="parent.inviteFriends('<?php echo $this->servicetype;?>');" ><?php echo $this->translate("Send"); ?></button> 
    <?php echo $this->translate(" or"); ?>  <a onclick="parent.Smoothbox.close();" href="javascript:void(0);" id="cancel" name="cancel"><?php echo $this->translate("cancel"); ?></a>
  </div>
</div>	
