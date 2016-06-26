<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 19.10.13 08:20 jungar $
 * @author     Jungar
 */
?>
<?php
$this->headScript()
  ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Heevent/externals/scripts/manager.js');
$this->headTranslate(array('%s guest'));

?>
<div class="heevent-block heevent-widget">
  <?php
  $event = $this->event;
  $rsvp = $this->rsvp;
  $row = $this->row;
  $count = $this->restrictions-$this->card_ticket;

  $member_count_free = $event->membership()->getMemberCount();

  if($this->of){
    if($this->eventPrice > 0 ){
  ?>
       <div id="heevent-buy-form<?php echo $event->getIdentity()?>" class="heevent-buy-form  heevent-form global_form"  >
      <?php
        echo $this->heevent()->getTicketForm($event,$this->eventPrice);
      ?>
      </div>
      <div id="background_buy_form" onclick="hideBuy_form('<?php echo $event->getIdentity() ?>');" class="background_buy_form<?php echo $event->getIdentity()?>"></div>
  <?php
    }
  }
  ?>
  <h3>
    <?php echo $this->past ? $this->translate('HEEVENT_Did you go?'): $this->translate('HEEVENT_Are you going?') ?>
  </h3>
  <div event-id="<?php echo $event->getIdentity() ?>" event-guid="<?php echo $event->getGuid() ?>" class="<?php echo $this->isMember ? 'member': '';  ?> events_action button-animate heevent-widget-inner" style="position: relative;">
    <?php
    if( $this->of && $this->eventPrice>0){
    if( null === $row ) {
      if( $event->membership()->isResourceApprovalRequired() ) {
//                 Render Request Invite Button
        ?>
        <button value="invite" toggle-text="<?php echo $this->translate('HEEVENT_Request Sent') ?>" name="rsvp" class="invite_btn" href="<?php echo $this->url(array('controller' => 'member', 'action' => 'request','event_id' => $event->getIdentity()), 'event_extended') ?>"><?php echo $this->translate('HEEVENT_Request Invite') ?></button>
        <button value="cancel" name="rsvp" disabled="disabled" class="disabled invite_btn" href="<?php echo $this->url(array('controller' => 'member', 'action' => 'cancel','event_id' => $event->getIdentity()), 'event_extended') ?>"><?php echo $this->translate('HEEVENT_Cancel Request') ?></button>
      <?php
      } else {
//                 Joining Event
        if($count>0){?>
        <button value="join" name="rsvp" class="join_btn"><?php echo $this->filter == 'past' ? $this->translate('HEEVENT_Did you go?'): $this->translate('HEEVENT_Are you going?') ?></button>

          <button class="yes ticket_btn" value="2" data-id="<?php echo $event->getIdentity(); ?>" name="rsvp" href="<?php echo $this->url(array('controller' => 'member', 'action' => 'hejoin','event_id' => $event->getIdentity()), 'event_extended') ?>"><?php echo $this->translate('Yes') ?></button>
          <button class="no rsvp_btn" value="0" name="rsvp" href="<?php echo $this->url(array('controller' => 'member', 'action' => 'join','event_id' => $event->getIdentity()), 'event_extended') ?>"><?php echo $this->translate('No') ?></button>
        <?php } else{
          echo $this->translate('Tickets is empty');
        }?>

      <?php

      }
    } else if($row->active) {
//                 Change RSVP
      $rsvp = 0;
     if($count>0){?>
      <div><?php echo $event->isPast() ? $this->translate('HEEVENT_Did you go?'): $this->translate('HEEVENT_Are you going?') ?></div>

        <button data-id="<?php echo $event->getIdentity(); ?>" class="yes <?php if($this->eventPrice>0) { ?>ticket_btn<?php } else { ?>rsvp_btn <?php } ?>" value="2" name="rsvp" href="<?php echo $this->url(array('module' => 'heevent', 'controller' => 'widget', 'action'=>'heprofile-rsvp', 'subject' => $event->getGuid()), 'default', true); ?>"><?php echo $this->translate('Yes') ?></button>
        <button class="<?php if($rsvp == 0){?>active disabled <?php } ?> active disabled no rsvp_btn" value="0" name="rsvp" href="<?php echo $this->url(array('module' => 'heevent', 'controller' => 'widget', 'action'=>'profile-rsvp', 'subject' => $event->getGuid()), 'default', true); ?>"><?php echo $this->translate('No') ?></button>
      <?php }
      else{
        echo $this->translate('Tickets is empty');
      }
      ?>

    <?php
    } else if( !$row->resource_approved && $row->user_approved ) {
//                 Render Cancel Invite Request Button
      ?>
      <button value="invite" toggle-text="<?php echo $this->translate('HEEVENT_Request Invite') ?>" disabled="disabled" name="rsvp" class="disabled invite_btn" href="<?php echo $this->url(array('controller' => 'member', 'action' => 'request','event_id' => $event->getIdentity()), 'event_extended') ?>"><?php echo $this->translate('HEEVENT_Request Sent') ?></button>
      <button value="cancel" name="rsvp" class="invite_btn" href="<?php echo $this->url(array('controller' => 'member', 'action' => 'cancel','event_id' => $event->getIdentity()), 'event_extended') ?>"><?php echo $this->translate('HEEVENT_Cancel Request') ?></button>
    <?php
    } else if( !$row->user_approved && $row->resource_approved ) {
//                 Render Accept Event Invite Button
//                 Render Ignore Event Invite Button
      ?>
      <div><?php echo $this->translate('HEEVENT_You have been invited to join the event')?></div>
      <button value="accept" name="rsvp" class="confirm_btn" href="<?php echo $this->url(array('controller' => 'member', 'action' => 'accept','event_id' => $event->getIdentity()), 'event_extended') ?>"><?php echo $this->translate('HEEVENT_Accept') ?></button>
      <button value="reject" name="rsvp" class="confirm_btn" href="<?php echo $this->url(array('controller' => 'member', 'action' => 'reject','event_id' => $event->getIdentity()), 'event_extended') ?>"><?php echo $this->translate('HEEVENT_Reject') ?></button>
      <?php if($count>0){?>

        <button disabled="disabled" data-id="<?php echo $event->getIdentity(); ?>" class="disabled yes <?php if($this->eventPrice>0) { ?>ticket_btn<?php } else { ?>rsvp_btn <?php } ?>" value="2" name="rsvp" href="<?php echo $this->url(array('module' => 'heevent', 'controller' => 'widget', 'action'=>'heprofile-rsvp', 'subject' => $event->getGuid()), 'default', true); ?>"><?php echo $this->translate('Yes') ?></button>
        <button disabled="disabled" class="disabled no rsvp_btn" value="0" name="rsvp" href="<?php echo $this->url(array('module' => 'heevent', 'controller' => 'widget', 'action'=>'profile-rsvp', 'subject' => $event->getGuid()), 'default', true); ?>"><?php echo $this->translate('No') ?></button>
      <?php }else{
        echo $this->translate('Tickets is empty');
      }?>

    <?php
    }

    ?>

  <?php
    }elseif($this->restrictions<=$member_count_free && $this->of ){
      echo $this->translate('Tickets is empty');
    }else{
     if( null === $row ) {
       if( $event->membership()->isResourceApprovalRequired() ) {
  //                 Render Request Invite Button
         ?>
         <button value="invite" toggle-text="<?php echo $this->translate('HEEVENT_Request Sent') ?>" name="rsvp" class="invite_btn" href="<?php echo $this->url(array('controller' => 'member', 'action' => 'request','event_id' => $event->getIdentity()), 'event_extended') ?>"><?php echo $this->translate('HEEVENT_Request Invite') ?></button>
         <button value="cancel" name="rsvp" disabled="disabled" class="disabled invite_btn" href="<?php echo $this->url(array('controller' => 'member', 'action' => 'cancel','event_id' => $event->getIdentity()), 'event_extended') ?>"><?php echo $this->translate('HEEVENT_Cancel Request') ?></button>
         <?php
       } else {
        $joinHref = $this->url(array('controller' => 'member', 'action' => 'join','event_id' => $event->getIdentity()), 'event_extended');
         ?>

         <button value="join" name="rsvp" class="join_btn"><?php echo $this->past == 'past' ? $this->translate('HEEVENT_Did you go?'): $this->translate('HEEVENT_Are you going?') ?></button>
         <button class="yes rsvp_btn" value="2" name="rsvp" href="<?php echo $this->url(array('controller' => 'member', 'action' => 'join','event_id' => $event->getIdentity()), 'event_extended') ?>"><?php echo $this->translate('Yes') ?></button>
         <button class="maybe rsvp_btn" value="1" name="rsvp" href="<?php echo $this->url(array('controller' => 'member', 'action' => 'join','event_id' => $event->getIdentity()), 'event_extended') ?>"><?php echo $this->translate('HEEVENT_Maybe') ?></button>
         <button class="no rsvp_btn" value="0" name="rsvp" href="<?php echo $this->url(array('controller' => 'member', 'action' => 'join','event_id' => $event->getIdentity()), 'event_extended') ?>"><?php echo $this->translate('No') ?></button>
         <?php
       }
     } else if($this->isMember) {
  //                 Change RSVP
         $rsvp = $row->rsvp;
       ?>
       <?php if($event->approval){?>
       <?php } ?>
       <button class="<?php if($rsvp == 2){?>active disabled <?php } ?>yes rsvp_btn" value="2" name="rsvp" href="<?php echo $this->url(array('module' => 'event', 'controller' => 'widget', 'action'=>'profile-rsvp', 'subject' => $event->getGuid()), 'default', true); ?>"><?php echo $this->translate('Yes') ?></button>
       <button class="<?php if($rsvp == 1){?>active disabled <?php } ?>maybe rsvp_btn" value="1" name="rsvp" href="<?php echo $this->url(array('module' => 'event', 'controller' => 'widget', 'action'=>'profile-rsvp', 'subject' => $event->getGuid()), 'default', true); ?>"><?php echo $this->translate('HEEVENT_Maybe') ?></button>
       <button class="<?php if($rsvp == 0){?>active disabled <?php } ?>no rsvp_btn" value="0" name="rsvp" href="<?php echo $this->url(array('module' => 'event', 'controller' => 'widget', 'action'=>'profile-rsvp', 'subject' => $event->getGuid()), 'default', true); ?>"><?php echo $this->translate('No') ?></button>
       <?php
     } else if( !$row->resource_approved && $row->user_approved ) {
  //                 Render Cancel Invite Request Button
       ?>
       <button value="invite" toggle-text="<?php echo $this->translate('HEEVENT_Request Invite') ?>" disabled="disabled" name="rsvp" class="disabled invite_btn" href="<?php echo $this->url(array('controller' => 'member', 'action' => 'request','event_id' => $event->getIdentity()), 'event_extended') ?>"><?php echo $this->translate('HEEVENT_Request Sent') ?></button>
       <button value="cancel" name="rsvp" class="invite_btn" href="<?php echo $this->url(array('controller' => 'member', 'action' => 'cancel','event_id' => $event->getIdentity()), 'event_extended') ?>"><?php echo $this->translate('HEEVENT_Cancel Request') ?></button>
       <?php
     } else if( !$row->user_approved && $row->resource_approved ) {
  //                 Render Accept Event Invite Button
  //                 Render Ignore Event Invite Button
       ?>
       <div><?php echo $this->translate('HEEVENT_You have been invited to join the event')?></div>
       <button value="accept" name="rsvp" class="confirm_btn" href="<?php echo $this->url(array('controller' => 'member', 'action' => 'accept','event_id' => $event->getIdentity()), 'event_extended') ?>"><?php echo $this->translate('HEEVENT_Accept') ?></button>
       <button value="reject" name="rsvp" class="confirm_btn" href="<?php echo $this->url(array('controller' => 'member', 'action' => 'reject','event_id' => $event->getIdentity()), 'event_extended') ?>"><?php echo $this->translate('HEEVENT_Reject') ?></button>
       <button disabled="disabled" class="disabled yes rsvp_btn" value="2" name="rsvp" href="<?php echo $this->url(array('module' => 'event', 'controller' => 'widget', 'action'=>'profile-rsvp', 'subject' => $event->getGuid()), 'default', true); ?>"><?php echo $this->translate('Yes') ?></button>
       <button disabled="disabled" class="disabled maybe rsvp_btn" value="1" name="rsvp" href="<?php echo $this->url(array('module' => 'event', 'controller' => 'widget', 'action'=>'profile-rsvp', 'subject' => $event->getGuid()), 'default', true); ?>"><?php echo $this->translate('HEEVENT_Maybe') ?></button>
       <button disabled="disabled" class="disabled no rsvp_btn" value="0" name="rsvp" href="<?php echo $this->url(array('module' => 'event', 'controller' => 'widget', 'action'=>'profile-rsvp', 'subject' => $event->getGuid()), 'default', true); ?>"><?php echo $this->translate('No') ?></button>
       <?php
     }
    }
    ?>
  </div>
</div>

