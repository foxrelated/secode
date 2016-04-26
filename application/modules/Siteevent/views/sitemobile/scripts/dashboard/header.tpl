<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: header.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$isEnabledPackage = Engine_Api::_()->siteevent()->hasPackageEnable();
$event_id = $this->siteevent->event_id;
?>

      <?php if($isEnabledPackage):?>
<?php 
$redirectUrl = $this->url(array(), "siteevent_session_payment", true)."?event_id=$event_id"

?>
            <?php if (Engine_Api::_()->siteeventpaid()->canShowPaymentLink($event_id)): ?>
                  <div class="tip center mtop5">
                    <span class="db_payment_link">
                      <a href='javascript:void(0);' onclick="submitSession(<?php echo $event_id ?>)"><?php echo $this->translate('Make Payment'); ?></a>
                      <form name="setSession_form" method="post" id="setSession_form" action="<?php echo $redirectUrl ?>">
                        <input type="hidden" name="event_id_session" id="event_id_session" />
                      </form>
                    </span>
                  </div>
            <?php endif; ?>
            <?php if (Engine_Api::_()->siteeventpaid()->canShowRenewLink($this->siteevent->event_id)): ?>
                  <div class="tip mtop5">
                    <span style="margin:0px;"> <?php echo $this->translate("Please click "); ?>
                      <a href='javascript:void(0);' onclick="submitSession(<?php echo $event_id ?>)"><?php echo $this->translate('here'); ?></a><?php echo $this->translate(" to renew event."); ?>
                      <form name="setSession_form" method="post" id="setSession_form" action="<?php echo $redirectUrl ?>">
                        <input type="hidden" name="event_id_session" id="event_id_session" />
                      </form>
                    </span>
                  </div>
            <?php endif; ?>
          <?php endif;?>


<div class="dashboard-header o_hidden b_medium">
  <h3>
   <b> <?php echo $this->translate('Dashboard'); ?>:</b> 
   <?php echo $this->htmlLink($this->siteevent->getHref(), $this->siteevent->getTitle()) ?>
  </h3>
   <?php echo $this->htmlLink($this->siteevent->getHref(), $this->siteevent->getTitle()) ?>
</div>
<script type="text/javascript">
function submitSession(id) {
$("#event_id_session").value=id;
	$("#setSession_form").submit();
}
</script>