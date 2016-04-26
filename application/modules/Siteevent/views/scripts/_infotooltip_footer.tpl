<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _infotooltip.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl .'application/modules/Siteevent/externals/scripts/core.js'); ?>
    <?php
//CASE: WHEN VIEWER IS OWNER OF THE EVENT

    if ($this->result->owner_id == $this->viewer_id) :

        //CHECK IF THE EVENT IS PAST EVENT OR UPCOMING
        $currenttime = time();
        $eventendtime = strtotime($this->result->endtime);
        if ($eventendtime <= $currenttime) {
            ?>
          
            <?php if ((!empty($info_values) && in_array("editevent", $info_values))) : ?>
            <?php if(!$flag):?>
                      <div class="info_tip_content_bottom" id="info_tip_content_bottom">
              <?php $flag = true;?>
            <?php endif;?>
                <a href="<?php echo $this->url(array('action' => 'edit', 'event_id' => $this->result->getIdentity(), 'occurrence_id' => $this->occurrence_id), 'siteevent_specific', true); ?>" class="icon_siteevent_dashboard"><?php echo $this->translate('Dashboard'); ?></a>
            <?php endif; ?>
         
        <?php } else { ?>

            <?php
            //if the event is past event then we will not show the invite link
            $endDate = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurenceEndDate($this->result->event_id, 'DESC', $this->occurrence_id);
            ?>
            <?php if ((!empty($info_values) && in_array("inviteevent", $info_values)) && strtotime($endDate) > time()) : ?>
                 <?php if(!$flag):?>
          <div class="info_tip_content_bottom" id="info_tip_content_bottom">
              <?php $flag = true;?>
            <?php endif;?>
             
                <!--       CHECK IF SITEEVENTINVITE PLGUIN IS INSTALLED THEN WE WILL REDIRECT USER TO AT THAT PAGE.-->
                <?php
                $siteeventinvite = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventinvite');
                if (!empty($siteeventinvite)):
                    ?>
                    <a href='<?php echo $this->url(array('user_id' => $this->result->owner_id, 'siteevent_id' => $this->result->event_id, 'occurrence_id' => $this->occurrence_id), "siteeventinvite_invite", true) ?>' class="icon_siteevents_inviteguests"><?php echo $this->translate('Invite Guests') ?></a>

                <?php else: ?>       
                    <a href ="javascript:void(0);" onclick='Smoothbox.open("<?php echo $this->url(array('controller' => 'member', 'action' => 'invite', 'event_id' => $this->result->event_id, 'occurrence_id' => $this->occurrence_id, 'format' => 'smoothbox'), 'siteevent_extended', true); ?>")' class="icon_siteevents_inviteguests"><?php echo $this->translate('Invite Guests') ?></a>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ((!empty($info_values) && in_array("editevent", $info_values))) : ?>
                     <?php if(!$flag):?>
          <div class="info_tip_content_bottom" id="info_tip_content_bottom">
              <?php $flag = true;?>
            <?php endif;?>
           
                <a href="<?php echo $this->url(array('action' => 'edit', 'event_id' => $this->result->getIdentity(), 'occurrence_id' => $this->occurrence_id), 'siteevent_specific', true); ?>" class="icon_siteevent_dashboard"><?php echo $this->translate('Dashboard'); ?></a>
            <?php endif; ?>

            <?php
        }
    else :

        //IF VIEWER IS NOT THE OWNER OF EVENT, THEY ARE EITHER INVITED OR JOINED THEIR FRIENDS EVENT
        ?>


        <?php
        //if the event is past event then we will not show the invite link
        $endDate = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurenceEndDate($this->result->event_id, 'DESC', $this->occurrence_id);
        $auth = Engine_Api::_()->authorization()->context;

        if ($auth->isAllowed($this->result, $this->viewer, "invite") && (!empty($info_values) && in_array("inviteevent", $info_values)) && strtotime($endDate) > time()) :
            ?>
            <span id="inviteguest_tooltip_<?php echo $this->occurrence_id; ?>">
               <?php if(!$flag):?>
          <div class="info_tip_content_bottom" id="info_tip_content_bottom">
              <?php $flag = true;?>
            <?php endif;?>
       
                <!--       CHECK IF SITEEVENTINVITE PLGUIN IS INSTALLED THEN WE WILL REDIRECT USER TO AT THAT PAGE.-->
                <?php
                $siteeventinvite = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventinvite');

                if (!empty($siteeventinvite)):
                    ?>
                    <a class="icon_siteevents_inviteguests" href='<?php echo $this->url(array('user_id' => $this->result->owner_id, 'siteevent_id' => $this->result->event_id, 'occurrence_id' => $this->occurrence_id), "siteeventinvite_invite", true) ?>'><?php echo $this->translate('Invite') ?></a>
                <?php else: ?>       
                    <a class="icon_siteevents_inviteguests" href ="javascript:void(0);" onclick='Smoothbox.open("<?php echo $this->url(array('controller' => 'member', 'action' => 'invite', 'event_id' => $this->result->event_id, 'occurrence_id' => $this->occurrence_id, 'format' => 'smoothbox'), 'siteevent_extended', true); ?>")'><?php echo $this->translate('Invite') ?></a>
            <?php endif; ?>
            </span>
        <?php endif; ?>

    <?php endif;
    ?>

    <?php if (Engine_Api::_()->siteevent()->isTicketBasedEvent()): ?>
                
        <?php if(Engine_Api::_()->siteeventticket()->bookNowButton($this->result) && ($this->result->isRepeatEvent() || (!$this->result->isRepeatEvent() && !$this->result->isEventFull(array('occurrence_id' => $this->view->occurrence_id))))): ?>
                
            <?php if(!$flag):?>
                <div class="info_tip_content_bottom" id="info_tip_content_bottom">
                <?php $flag = true;?>
            <?php endif;?>
                    
            <?php echo $this->htmlLink(array('route' => "siteeventticket_ticket", 'action' => 'buy', "event_id" => $this->result->event_id, 'occurrence_id' => $this->occurrence_id), $this->translate('Book'), array('class' => "icon_siteevents_tickets"));?>
        <?php endif; ?>
    <?php elseif ((!empty($info_values) && in_array("joinevent", $info_values)) && isset($this->result->rsvp) && $this->result->membership_userid != null && $this->result->rsvp != 3) : ?>
                 <?php if(!$flag):?>
          <div class="info_tip_content_bottom" id="info_tip_content_bottom">
              <?php $flag = true;?>
            <?php endif;?>

<!--        <span id = "join_event_tooltip_<?php echo $this->occurrence_id; ?>" <?php if ($this->result->rsvp != 3) : ?> style="display:none;" <?php endif; ?>>
            <a class="icon_siteevents_inviteaccept"  href = "javascript:void(0);" onclick="en4.siteevent.member.acceptInvite(<?php echo $this->result->event_id; ?>, <?php echo $this->occurrence_id; ?>, 'join', 'tooltip')" ><?php echo $this->translate('Accept');
            ?></a>        

            <a class="icon_siteevents_invitereject" href = "javascript:void(0);" onclick="en4.siteevent.member.saveRSVP(0, <?php echo $this->result->event_id; ?>, <?php echo $this->occurrence_id; ?>, 'reject', 'tooltip')" ><?php echo $this->translate('Ignore'); ?></a>
        </span>-->

        <div class="seaocore_selectmenu fright" id="joined_event_tooltip_<?php echo $this->occurrence_id; ?>" <?php if ($this->result->rsvp == 3) : ?> style="display:none;" <?php endif; ?>>





            <a style="display:block;" href="javascript:void(0);">
                <span id="filtered_selected_<?php echo $this->occurrence_id; ?>">
                    <?php
                    if ($this->result->rsvp == 2)
                        echo $this->translate('Attending');
                    elseif ($this->result->rsvp == 1)
                        echo $this->translate('Maybe');
                    elseif (!$this->result->rsvp)
                        echo $this->translate('Not Attending');
                    ?>
                </span>
                <i class="icon_down"></i>
            </a>
            <ul class="seaocore_selectmenu_cont">
                <li <?php if ($this->result->rsvp == 2): ?> class="uiselect_selected rsvp_2" <?php endif; ?> data-ref="2 <?php echo $this->result->event_id; ?> <?php echo $this->occurrence_id; ?>" onclick="javascript:changeSiteeventRSVP(this);"><a href="javascript:void(0);"><?php echo $this->translate('Attending'); ?></a></li>
                <li <?php if ($this->result->rsvp == 1): ?> class="uiselect_selected rsvp_1" <?php endif; ?> data-ref="1 <?php echo $this->result->event_id; ?> <?php echo $this->occurrence_id; ?>" onclick="javascript:changeSiteeventRSVP(this);"><a href="javascript:void(0);"><?php echo $this->translate('Maybe'); ?></a></li>
                <li <?php if ($this->result->rsvp == 0): ?> class="uiselect_selected rsvp_0" <?php endif; ?> data-ref="0 <?php echo $this->result->event_id; ?> <?php echo $this->occurrence_id; ?>" onclick="javascript:changeSiteeventRSVP(this);"><a href="javascript:void(0);"><?php echo $this->translate('Not Attending'); ?></a></li>
            </ul>
            <script>
            var prev_selectedrsvp = '<?php echo $this->result->rsvp; ?>';
            </script>

        </div> 
<?php endif; ?> 
 <script>
  var changeSiteeventRSVP = function(self) {
     var params = self.get('data-ref').split(' ');
    saveRSVPTooltip(params[0], params[1], params[2], 'tooltip', 'tooltip');
    $$('.rsvp_' + prev_selectedrsvp).removeClass('uiselect_selected');
    $$('.rsvp_' + params[0]).addClass('uiselect_selected');
    prev_selectedrsvp = params[0];
    if(params[0] == 2)
      $('filtered_selected_' + params[2]).innerHTML= en4.core.language.translate('Attending');
    else if(params[0] == 1)
      $('filtered_selected_' + params[2]).innerHTML= en4.core.language.translate('Maybe');
    else
      $('filtered_selected_' + params[2]).innerHTML= en4.core.language.translate('Not Attending');
   
   
  }
  
  var saveRSVPTooltip = function(rsvp, event_id, occurrence_id, action, element_id) {
    if (action == 'reject')
      var url = en4.core.baseUrl + 'siteevent/member-ajax-based/reject?occurrence_id=' + occurrence_id;
    else
      var url = en4.core.baseUrl + 'siteevent/member-ajax-based/join?occurrence_id=' + occurrence_id;
  
    var eventCapacity = isEventFull(occurrence_id);
    if(eventCapacity != 0) {
        return false;
    }  
    
    if(rsvp == 2) {
        isEventFull(occurrence_id, selectToolTipRSVP, {event_id:event_id, rsvp:rsvp,occurrence_id:occurrence_id});
    }
    else {
        selectToolTipRSVP({event_id:event_id, rsvp:rsvp,occurrence_id:occurrence_id});
    }        
  }
  
    function selectToolTipRSVP(options) {
        var rsvp = options.rsvp;
        var event_id = options.event_id;
        var occurrence_id = options.occurrence_id;
        en4.core.request.send(new Request.JSON({
          url: url,
          data: {
            format: 'json',
            'event_id': event_id,
            'option_id': rsvp,
            'occurrence_id': occurrence_id,
            ismanagepage: true
          },
          onComplete: function(responseJSON, responseText)
          { 
            if(typeof el_siteevent != 'undefined')
              el_siteevent.store('tip-loaded', false)
          }
        }));
    }  


</script>  
