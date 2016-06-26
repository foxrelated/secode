<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _richContent.tpl 19.10.13 08:20 jungar $
 * @author     Jungar
 */
?>
<?php
/**
* @var $event Heevent_Model_Event
*/
$event = $this->event;
$desc = $event->getDescription();
$this->headScript()
  ->appendFile('application/modules/Heevent/externals/scripts/manager.js');
$eventPhotoUrl = $event->getPhotoUrl('thumb.normal');
$owner = $event->getOwner();
$viewer = Engine_Api::_()->user()->getViewer();
$view = $this;
$helperPath = APPLICATION_PATH
    . DIRECTORY_SEPARATOR
    . "application"
    . DIRECTORY_SEPARATOR
    . "modules"
    . DIRECTORY_SEPARATOR
    . 'Heevent'
    . DIRECTORY_SEPARATOR
    . 'views'
    . DIRECTORY_SEPARATOR
    . 'helpers'
    . DIRECTORY_SEPARATOR;

$view->addHelperPath($helperPath, 'Heevent_View_Helper_');
$member_count_free = $event->membership()->getMemberCount(true, Array('rsvp' => 2));
$c = 0;

if(!$eventPhotoUrl)
  $eventPhotoUrl = $this->layout()->staticBaseUrl ."application/modules/Heevent/externals/images/event-list-nophoto.gif";
?>
<div id='heevent-item-<?php echo $event->getIdentity(); ?>-rich-content' class="heevent-item-rich-content">
  <div class="heevent-description">
    <?php echo $this->string()->truncate($desc, 350) ?>
  </div>
  <a class="heevent-cover" style="display: block;" href="<?php echo $event->getHref(); ?>">
    <img class="fake-img" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Heevent/externals/images/fake-4x3.gif" style="<?php echo $event->getCoverBgStyle() ?>background-image: url(<?php echo $eventPhotoUrl?>)">
  </a>
  <div class="events_info">
    <div class="events_title">
      <h3><?php echo $this->htmlLink($event->getHref(), $event->getTitle()) ?></h3>
    </div>
    <div class="events_details heevents_details">
      <div><i class="hei hei-time"></i><?php echo $this->locale()->toDateTime($event->starttime) ?></div>
      <?php if($event->location) {?>
      <div class="event-location"><i class="hei hei-map-marker"></i><?php echo $this->htmlLink('http://maps.google.com/?q='.urlencode($event->location), $event->location, array('target' => 'blank')) ?></div>
        <?php } ?>

          <?php
          if($this->card_ticket>0){
            $g =  $this->card_ticket;
          }else{
            $g =  $event->membership()->getMemberCount(true, Array('rsvp' => 2));
          }?>
           <div><i class="hei hei-user"></i>
             <span guest-count="<?php echo $g; ?>" id="guests_<?php if(!$this->of) echo $event->getGuid(); if($this->eventPrice<0 && $this->restrictions>0)echo $event->getGuid() ?>">
          <?php
          echo $this->translate(array('%s guest', '%s guests', $g), @$this->locale()->toNumber($g));
          ?>
        </span></div>
        <?php

        if($this->of){
            if($this->eventPrice > 0){

                ?>
                <div id="heevent-buy-form<?php echo $event->getIdentity()?>" class="heevent-buy-form  heevent-form global_form"  >

                    <?php
                    echo $this->heevent()->getTicketForm($event,$this->eventPrice);
                    ?>
                </div>
                <div id="background_buy_form" onclick="hideBuy_form('<?php echo $event->getIdentity() ?>');" class="background_buy_form<?php echo $event->getIdentity()?>"></div>


                <div><i class="hei hei-tag"></i><?php echo $this->translate('Price').' ';  echo $event->getCurentPrice($this->eventPrice); ?></div>

            <?php
            }
            if($this->restrictions && $this->eventPrice > 0){

            ?>
            <div><i class="hei hei-ticket"></i><?php echo $this->translate('Available').' ';  echo $count = $this->restrictions-$this->card_ticket ?>  of <?php  echo $this->restrictions ?></div>
        <?php
            }else{
              ?>
              <div><i class="hei hei-ticket"></i>
                <span guest-count="<?php echo $this->restrictions-$member_count_free; ?>"  id="2guests_<?php if(!$this->of) echo $event->getGuid(); if($this->eventPrice<0 && $this->restrictions>0)echo $event->getGuid() ?>"><?php echo $this->translate('Available').' ';  echo $count = $this->restrictions-$member_count_free ?></span>  of <?php  echo $this->restrictions ?></div>
            <?php
            }
        }

        ?>

    </div>
    <?php
    if($viewer->getIdentity() ) {
      $row = $event->membership()->getRow($viewer);
      $rsvp = -1;
      ?>

    <div event-id="<?php echo $event->getIdentity() ?>" event-guid="<?php echo $event->getGuid() ?>" class="<?php echo ($row && $row->active) ? 'member': '';  ?> events_action button-animate">
      <?php

        if($this->of && $this->eventPrice>0) {

      if($this->maxTicket>=5){
        $count= -1;
      }
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
              if($this->maxTicket>=5){
                echo $this->translate('Exhausted  limit of 5 Tickets');
              }else{
                echo $this->translate('Tickets is empty');
              }

              }?>

          <?php

          }
      } else if($row->active) {
//                 Change RSVP
          $rsvp = 0;

          if($count>0){?>
          <div><?php echo $event->isPast() ? $this->translate('HEEVENT_Did you go?'): $this->translate('HEEVENT_Are you going?') ?></div>

          <button data-id="<?php echo $event->getIdentity(); ?>" class="<?php if($rsvp == 2){?>active disabled <?php } ?>yes <?php if($this->eventPrice>0) { ?>ticket_btn<?php } else { ?>rsvp_btn <?php } ?>" value="2" name="rsvp" href="<?php echo $this->url(array('module' => 'heevent', 'controller' => 'widget', 'action'=>'heprofile-rsvp', 'subject' => $event->getGuid()), 'default', true); ?>"><?php echo $this->translate('Yes') ?></button>
              <button class="<?php if($rsvp == 0){?>active disabled <?php } ?>no rsvp_btn" value="0" name="rsvp" href="<?php echo $this->url(array('module' => 'heevent', 'controller' => 'widget', 'action'=>'profile-rsvp', 'subject' => $event->getGuid()), 'default', true); ?>"><?php echo $this->translate('No') ?></button>
          <?php }
          else{
            if($this->maxTicket>=5){
              echo $this->translate('Exhausted  limit of 5 Tickets');
            }else{
              echo $this->translate('Tickets is empty');
            }
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
          if($this->maxTicket>=5){
            echo $this->translate('Exhausted  limit of 5 Tickets');
          }else{
            echo $this->translate('Tickets is empty');
          }
          }?>

      <?php
      }

      ?>
    </div>
      <?php


        }elseif($this->restrictions<=$member_count_free && $this->of){
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
//                 Joining Event
           ?>
           <button value="join" name="rsvp" class="join_btn"><?php echo $this->filter == 'past' ? $this->translate('HEEVENT_Did you go?'): $this->translate('HEEVENT_Are you going?') ?></button>
           <button class="yes rsvp_btn" value="2" name="rsvp" href="<?php echo $this->url(array('controller' => 'member', 'action' => 'join','event_id' => $event->getIdentity()), 'event_extended') ?>"><?php echo $this->translate('Yes') ?></button>
           <button class="maybe rsvp_btn" value="1" name="rsvp" href="<?php echo $this->url(array('controller' => 'member', 'action' => 'join','event_id' => $event->getIdentity()), 'event_extended') ?>"><?php echo $this->translate('HEEVENT_Maybe') ?></button>
           <button class="no rsvp_btn" value="0" name="rsvp" href="<?php echo $this->url(array('controller' => 'member', 'action' => 'join','event_id' => $event->getIdentity()), 'event_extended') ?>"><?php echo $this->translate('No') ?></button>
           <?php
         }
       } else if($row->active) {
//                 Change RSVP
           $rsvp = $row->rsvp;
         ?>
         <div><?php echo $this->filter == 'past' ? $this->translate('HEEVENT_Did you go?'): $this->translate('HEEVENT_Are you going?') ?></div>
         <button class="<?php if($rsvp == 2){?>active disabled <?php } ?>yes rsvp_btn" value="2" name="rsvp" href="<?php echo $this->url(array('module' => 'heevent', 'controller' => 'widget', 'action'=>'profile-rsvp', 'subject' => $event->getGuid()), 'default', true); ?>"><?php echo $this->translate('Yes') ?></button>
         <button class="<?php if($rsvp == 1){?>active disabled <?php } ?>maybe rsvp_btn" value="1" name="rsvp" href="<?php echo $this->url(array('module' => 'heevent', 'controller' => 'widget', 'action'=>'profile-rsvp', 'subject' => $event->getGuid()), 'default', true); ?>"><?php echo $this->translate('HEEVENT_Maybe') ?></button>
         <button class="<?php if($rsvp == 0){?>active disabled <?php } ?>no rsvp_btn" value="0" name="rsvp" href="<?php echo $this->url(array('module' => 'heevent', 'controller' => 'widget', 'action'=>'profile-rsvp', 'subject' => $event->getGuid()), 'default', true); ?>"><?php echo $this->translate('No') ?></button>
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
         <button disabled="disabled" class="disabled yes rsvp_btn" value="2" name="rsvp" href="<?php echo $this->url(array('module' => 'heevent', 'controller' => 'widget', 'action'=>'profile-rsvp', 'subject' => $event->getGuid()), 'default', true); ?>"><?php echo $this->translate('Yes') ?></button>
         <button disabled="disabled" class="disabled maybe rsvp_btn" value="1" name="rsvp" href="<?php echo $this->url(array('module' => 'heevent', 'controller' => 'widget', 'action'=>'profile-rsvp', 'subject' => $event->getGuid()), 'default', true); ?>"><?php echo $this->translate('HEEVENT_Maybe') ?></button>
         <button disabled="disabled" class="disabled no rsvp_btn" value="0" name="rsvp" href="<?php echo $this->url(array('module' => 'heevent', 'controller' => 'widget', 'action'=>'profile-rsvp', 'subject' => $event->getGuid()), 'default', true); ?>"><?php echo $this->translate('No') ?></button>
         <?php
       }

      ?>
    </div>
    <?php
    }
    }
    ?>
  </div>
<?php if($this->format == 'html'){ ?>
<script type="text/javascript">
  (function(rc){_hem.initActionsOn(rc);})($('heevent-item-<?php echo $event->getIdentity(); ?>-rich-content'));
</script>
<?php } ?>
</div>
