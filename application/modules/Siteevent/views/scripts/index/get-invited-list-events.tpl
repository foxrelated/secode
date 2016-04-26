<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manage.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $datetimeFormat = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.datetime.format', 'medium'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/scripts/core.js'); ?>
<?php
$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteevent.css')
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteevent_dashboard.css')
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
?>

<div id="manage_events_<?php echo $this->list_type; ?>">

    <?php
    if ($this->invite_count == 0):
        echo $this->translate('You do not have any invitation.');
        return;

    endif;
    ?>

    <?php if ($this->list_type == 'popup'): ?>

        <div class="siteevent_events_listing_popup">
            <div class="o_hidden siteevent_events_listing_popup_head b_medium">
                <span class="bold mtop5 fleft"><?php echo $this->translate('Invites'); ?></span> 
                <a class="fright" href="javascript:void(0);" onclick="SmoothboxSEAO.close()">
                    <img   src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteevent/externals/images/closebox.png' alt='close'/>
                </a>
            </div>

            <div class="siteevent_events_listing_popup_cont">
                <ul class="siteevent_browse_list">
                    <?php $prev_date = 0; ?>
                    <?php foreach ($this->results as $item): ?>
                        <?php
                        $startDateObject = new Zend_Date(strtotime($item->starttime));
                        //$endDateObject = new Zend_Date(strtotime($siteevent->endtime));
                        if ($this->viewer() && $this->viewer()->getIdentity()) {
                            $tz = $this->viewer()->timezone;
                            $startDateObject->setTimezone($tz);
                            //$endDateObject->setTimezone($tz);
                        }
                        $next_datetime = strtotime(date("Y-m-d", strtotime($startDateObject)));
                        if ($next_datetime != $prev_date) :?>                          
                            
                            <li class='bold siteevent_browse_list_sep b_medium f_small'>
                              <?php
                              if ($datetimeFormat != 'full')
                                  echo $this->locale()->toDate($item->starttime, array('format' => 'EEEE')) . ', ' . $this->locale()->toDate($item->starttime, array('size' => $datetimeFormat));
                              else
                                echo $this->locale()->toDate($item->starttime, array('size' => $datetimeFormat));
                       ?>
                          </li>
                            <?php $prev_date = $next_datetime;
                        endif;
                        ?>

                        <li class="b_medium" id="userlist_invite_<?php echo $item->occurrence_id; ?>">
                            <div class='siteevent_browse_list_photo b_medium'>

                                <?php if ($item->featured): ?>
                                    <i class="siteevent_list_featured_label" title="<?php echo $this->translate('Featured'); ?>"></i>
                                <?php endif; ?>
                                <?php if ($item->newlabel): ?>
                                    <i class="siteevent_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
                                <?php endif; ?>


                                <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.main', '', array('align' => 'center'))) ?>


                                <?php if (!empty($item->sponsored)): ?>
                                    <div class="siteevent_list_sponsored_label" style="background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.sponsoredcolor', '#FC0505'); ?>">
                                        <?php echo $this->translate('SPONSORED'); ?>                 
                                    </div>
                                <?php endif; ?>

                            </div>

                            <div class='siteevent_browse_list_info'>
                                <div class='siteevent_browse_list_info_header o_hidden'>
                                    <div class="siteevent_list_title_small"> 
                                        <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
                                    </div>	
                                </div>

                                <div class='siteevent_browse_list_info_stat seaocore_txt_light'>
                                    <?php echo $this->translate('led by'); ?>
                                    <?php echo $item->getLedBys(); ?>,
                                    <?php if(!Engine_Api::_()->siteevent()->isTicketBasedEvent()): ?>
                                        <?php echo $this->translate(array('%s guest', '%s guests', $item->member_count), $this->locale()->toNumber($item->member_count)) ?>,  
                                    <?php endif; ?>
                                    <?php echo $this->translate(array('%s comment', '%s comments', $item->comment_count), $this->locale()->toNumber($item->comment_count)) ?>,

                                    <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) == 3 || Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) == 2): ?>
                                        <?php echo $this->translate(array('%s review', '%s reviews', $item->review_count), $this->locale()->toNumber($item->review_count)) ?>,
                                    <?php endif; ?>        

                                    <?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count)) ?>,
                                    <?php echo $this->translate(array('%s like', '%s likes', $item->like_count), $this->locale()->toNumber($item->like_count)) ?>
                                </div>

                                <?php if (!empty($item->location)): ?>
                                    <div class='siteevent_browse_list_info_stat seaocore_txt_light'>
                                        <?php //echo $this->translate($item->location); ?>
                                        <?php echo $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $item->event_id, 'resouce_type' => 'siteevent_event'), $item->location, array('target' => "_blank")); ?>
                                    </div>
                                <?php endif; ?>
                                <?php if(!Engine_Api::_()->siteevent()->isTicketBasedEvent()): ?>
                                <div class='siteevent_manage_list_options'>
                                    <div id="join_invite_<?php echo $item->occurrence_id; ?>">
                                      <!--                                   IF THE EVENT IS PAST THEN WE WILL ONLY SHOW THE IGNORE LINK TO THE INVITEES.-->
                                    <?php if(strtotime($item->endtime) > time()) :?>
                                        <a href='javascript:void(0);' onclick="en4.siteevent.member.acceptInvite(<?php echo $item->event_id; ?>, <?php echo $item->occurrence_id; ?>, 'join', 'invite')" class='buttonlink icon_siteevents_inviteaccept'><?php echo $this->translate("Accept"); ?></a>
                                        <?php endif;?>
                                        <a href='javascript:void(0);' onclick="en4.siteevent.member.saveRSVP(0, <?php echo $item->event_id; ?>, <?php echo $item->occurrence_id; ?>, 'reject', 'invite')" class='buttonlink icon_siteevents_invitereject'><?php echo $this->translate("Ignore"); ?></a>
                                    </div>
                                  <?php if(!Engine_Api::_()->siteevent()->isTicketBasedEvent() && strtotime($item->endtime) > time()) :?>
                                    <div id="rsvp_invite_<?php echo $item->occurrence_id; ?>" style="display:none;">
                                        <span class="seaocore_button prelative o_hidden fleft">
                                            <a style="display:block;" href="javascript:void(0);">
                                                <span id="filtered_selected_<?php echo $item->event_id; ?>">
                                                    <?php
                                                    if ($item->rsvp == 2)
                                                        echo $this->translate('Attending');
                                                    elseif ($item->rsvp == 1)
                                                        echo $this->translate('Maybe Attending');
                                                    else
                                                        echo $this->translate('Not Attending');
                                                    ?>
                                                </span>
                                                <i class="icon_down"></i>
                                            </a>
                                            <select onchange="en4.siteevent.member.saveRSVP(this.value, <?php echo $item->event_id; ?>,<?php echo $item->occurrence_id; ?>, 'rsvp', 'invite');
                            $('filtered_selected_<?php echo $item->event_id; ?>').innerHTML = this.options[this.options.selectedIndex].text;" id="selected_rsvp_invite_<?php echo $item->occurrence_id; ?>">
                                                <option value="2" <?php echo $item->rsvp == 2 ? "selected=selected" : ''; ?>><?php echo $this->translate('Attending'); ?></option>
                                                <option value="1" <?php echo $item->rsvp == 1 ? "selected=selected" : ''; ?>><?php echo $this->translate('Maybe Attending'); ?></option>
                                                <option value="0" <?php echo $item->rsvp == 0 ? "selected=selected" : ''; ?>><?php echo $this->translate('Not Attending'); ?></option>
                                            </select>
                                        </span>
                                    </div>
                                  
                                  
                                  <?php endif;?>
                                    <?php
                                    //if the event is past event then we will not show the invite link
                                    $endDate = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurenceEndDate($item->event_id, 'DESC', $item->occurrence_id);
                                    $auth = Engine_Api::_()->authorization()->context;

                                    if ($auth->isAllowed($this->result, $this->viewer, "invite") && strtotime($endDate) > time()) :
                                        ?>
                                        <span style="display:none;" id="inviteguest_invite_<?php echo $item->occurrence_id; ?>">

                                            <!--       CHECK IF SITEEVENTINVITE PLGUIN IS INSTALLED THEN WE WILL REDIRECT USER TO AT THAT PAGE.-->
                                            <?php
                                            $siteeventinvite = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventinvite');
                                            if (!empty($siteeventinvite)):
                                                ?>
                                                <a href='<?php echo $this->url(array('user_id' => $item->owner_id, 'siteevent_id' => $item->event_id, 'occurrence_id' => $item->occurrence_id), "siteeventinvite_invite", true) ?>' class="buttonlink icon_siteevents_inviteguests"><?php echo $this->translate('Invite Guests') ?></a>

                                            <?php else: ?>       
                                                <a href ="javascript:void(0);" onclick='Smoothbox.open("<?php echo $this->url(array('controller' => 'member', 'action' => 'invite', 'event_id' => $item->event_id, 'occurrence_id' => $item->occurrence_id, 'format' => 'smoothbox'), 'siteevent_extended', true); ?>")' class="buttonlink icon_siteevents_inviteguests"><?php echo $this->translate('Invite Guests') ?></a>
                                            <?php endif; ?>
                                        </span>
                                    <?php endif; ?>

                                </div>
                                <?php endif; ?>
                            <?php if(strtotime($item->endtime) <= time()) :?>
                              <?php $lastOccurrenceEndDate = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurenceEndDate($item->event_id, 'DESC');?>
                                    <?php
                                      //CHECK IF THE OCCURRENCES WIDGET EXIST ON THE EVENT PROFILE PAGE.
                                      $occurrenceIdentity = Engine_Api::_()->siteevent()->existWidget('occurrences');
                                      if($occurrenceIdentity)
                                        $eventProfileUrl = $item->getHref(array('tab' => $occurrenceIdentity));
                                      else
                                        $eventProfileUrl = $item->getHref();
                                      $s1 = '<a href="' . $eventProfileUrl . '">';
                                      $s2 = '</a>';?>
                                      <div class="seaocore_txt_red mtop10"> 
                                      <?php if($lastOccurrenceEndDate > time()):?> 
                                      <?php echo $this->translate('You can not join this occurrence now as it has been ended. To view all upcoming occurrences of this event, please %1s click here%2s.', array($s1, $s2 ));?>
                                       <?php else:?>
                                          <?php echo $this->translate('You can not join this event now as it has been ended.');?>
                                        <?php endif;?>
                                         
                                      </div>
                              <?php endif;?>
                                
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php elseif ($this->list_type == 'calendar'): ?>
        <div class="siteevent_main_calendar b_medium br_body_bg">
            <?php if ($this->invite_count > 3) : ?>
                <div class="siteevent_main_calendar_mname b_medium o_hidden">
                    <a href="javascript:void(0);" onclick="getInvitedList('popup', <?php echo $this->invite_count; ?>)"><?php echo $this->translate('Invites') . '(' . $this->invite_count . ')'; ?></a>
                    &middot;
                    <a href="javascript:void(0);" onclick="getInvitedList('popup', <?php echo $this->invite_count; ?>)"><?php echo $this->translate('View All Invites'); ?></a>

                </div>
            <?php else: ?>
                <div class="siteevent_main_calendar_mname b_medium">
                    <?php echo $this->translate('Invites'); ?>        
                </div>
            <?php endif; ?>

            <ul class="siteevent_browse_list">
                <?php $prev_date = 0; ?>
                <?php foreach ($this->results as $item): ?>
                    <?php
                    $startDateObject = new Zend_Date(strtotime($item->starttime));
                    //$endDateObject = new Zend_Date(strtotime($siteevent->endtime));
                    if ($this->viewer() && $this->viewer()->getIdentity()) {
                        $tz = $this->viewer()->timezone;
                        $startDateObject->setTimezone($tz);
                        //$endDateObject->setTimezone($tz);
                    }
                    $next_datetime = strtotime(date("Y-m-d", strtotime($startDateObject)));
                    if ($next_datetime != $prev_date) :?>
                        <li class='bold siteevent_browse_list_sep b_medium f_small'>
                            <?php
                         if ($datetimeFormat != 'full')
                             echo $this->locale()->toDate($item->starttime, array('format' => 'EEEE')) . ', ' . $this->locale()->toDate($item->starttime, array('size' => $datetimeFormat));
                         else
                           echo $this->locale()->toDate($item->starttime, array('size' => $datetimeFormat));
                        ?>
                        </li>

                       
                        <?php $prev_date = $next_datetime;
                    endif;
                    ?>

                    <li class="b_medium" id="userlist_invite_<?php echo $item->occurrence_id; ?>">
                        <div class='siteevent_browse_list_photo b_medium'>

                            <?php if ($item->featured): ?>
                                <i class="siteevent_list_featured_label" title="<?php echo $this->translate('Featured'); ?>"></i>
                            <?php endif; ?>
                            <?php if ($item->newlabel): ?>
                                <i class="siteevent_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>
                            <?php endif; ?>

                            <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.main', '', array('align' => 'center'))) ?>

                            <?php if (!empty($item->sponsored)): ?>
                                <div class="siteevent_list_sponsored_label" style="background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.sponsoredcolor', '#FC0505'); ?>">
                                    <?php echo $this->translate('SPONSORED'); ?>                 
                                </div>
                            <?php endif; ?>

                        </div>

                        <div class='siteevent_browse_list_info'>
                            <div class='siteevent_browse_list_info_header o_hidden'>
                                <div class="siteevent_list_title_small"> 
                                    <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
                                </div>	
                            </div>

                            <div class='siteevent_browse_list_info_stat seaocore_txt_light'>
                                <?php echo $this->translate('led by'); ?>
                                <?php echo $item->getLedBys(); ?>,
                                <?php echo $this->translate(array('%s guest', '%s guests', $item->member_count), $this->locale()->toNumber($item->member_count)) ?>,  
                                <?php echo $this->translate(array('%s comment', '%s comments', $item->comment_count), $this->locale()->toNumber($item->comment_count)) ?>,

                                <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) == 3 || Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) == 2): ?>
                                    <?php echo $this->translate(array('%s review', '%s reviews', $item->review_count), $this->locale()->toNumber($item->review_count)) ?>,
                                <?php endif; ?>        

                                <?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count)) ?>,
                                <?php echo $this->translate(array('%s like', '%s likes', $item->like_count), $this->locale()->toNumber($item->like_count)) ?>
                            </div>

                            <?php if (!empty($item->location)): ?>
                                <div class='siteevent_browse_list_info_stat seaocore_txt_light'>
                                    <?php //echo $this->translate($item->location);  ?>
                                    <?php echo $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $item->event_id, 'resouce_type' => 'siteevent_event'), $item->location, array('class' => 'smoothbox')); ?>
                                </div>
                            <?php endif; ?>

                            <div class="siteevent_browse_list_info_footer clr o_hidden">
                                <span class="siteevent_browse_list_info_footer_icons">
                                    <?php if (empty($item->approved)): ?>
                                        <i title="<?php echo $this->translate('Not approved'); ?>" class="siteevent_icon seaocore_icon_disapproved"></i>
                                    <?php endif; ?>
                                    <?php if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.fs.markers', 1)) : ?>   
                                        <?php if (!empty($item->sponsored)): ?>
                                            <i title="<?php echo $this->translate('Sponsored'); ?>" class="siteevent_icon seaocore_icon_sponsored"></i>
                                        <?php endif; ?>

                                        <?php if (!empty($item->featured)): ?>
                                            <i title="<?php echo $this->translate('Featured'); ?>" class="siteevent_icon seaocore_icon_featured"></i>
                                        <?php endif; ?>
                                    <?php endif; ?>	

                                    <?php if ($item->closed): ?>
                                        <i title="<?php echo $this->translate('Cancelled'); ?>" class="siteevent_icon icon_siteevents_close"></i>
                                    <?php endif; ?>
                                </span>
                            </div>

                            <div class='siteevent_manage_list_options'>
                                <div id="join_calendar_<?php echo $item->occurrence_id; ?>">
<!--                                   IF THE EVENT IS PAST THEN WE WILL ONLY SHOW THE IGNORE LINK TO THE INVITEES.-->
                                    <?php if(strtotime($item->endtime) > time()) :?>
                                    <a href='javascript:void(0);' onclick="en4.siteevent.member.acceptInvite(<?php echo $item->event_id; ?>, <?php echo $item->occurrence_id; ?>, 'join', 'calendar')" class='buttonlink icon_siteevents_inviteaccept'><?php echo $this->translate("Accept"); ?></a>
                                    <?php endif;?>  
                                    <a href='javascript:void(0);' onclick="en4.siteevent.member.saveRSVP(0, <?php echo $item->event_id; ?>, <?php echo $item->occurrence_id; ?>, 'reject', 'calendar')" class='buttonlink icon_siteevents_invitereject'><?php echo $this->translate("Ignore"); ?></a>
                                </div>
                                <?php if(!Engine_Api::_()->siteevent()->isTicketBasedEvent() && strtotime($item->endtime) > time()) :?>
                                <div id="rsvp_calendar_<?php echo $item->occurrence_id; ?>" style="display:none;">
                                    <span class="seaocore_button prelative o_hidden fleft">
                                        <a style="display:block;" href="javascript:void(0);">
                                            <span id="filtered_selected_<?php echo $item->event_id; ?>">
                                                <?php
                                                if ($item->rsvp == 2)
                                                    echo $this->translate('Attending');
                                                elseif ($item->rsvp == 1)
                                                    echo $this->translate('Maybe Attending');
                                                else
                                                    echo $this->translate('Not Attending');
                                                ?>
                                            </span>
                                            <i class="icon_down"></i>
                                        </a>
                                        <select onchange="en4.siteevent.member.saveRSVP(this.value, <?php echo $item->event_id; ?>,<?php echo $item->occurrence_id; ?>, 'rsvp', 'calendar');
                            $('filtered_selected_<?php echo $item->event_id; ?>').innerHTML = this.options[this.options.selectedIndex].text;" id="selected_rsvp_calendar_<?php echo $item->occurrence_id; ?>">
                                            <option value="2" ><?php echo $this->translate('Attending'); ?></option>
                                            <option value="1" ><?php echo $this->translate('Maybe Attending'); ?></option>
                                            <option value="0" ><?php echo $this->translate('Not Attending'); ?></option>

                                        </select>
                                    </span>
                                </div>
                               
                               <?php endif;?>
                                <?php
                                //if the event is past event then we will not show the invite link
                                $endDate = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurenceEndDate($item->event_id, 'DESC', $item->occurrence_id);
                                $auth = Engine_Api::_()->authorization()->context;

                                if ($auth->isAllowed($this->result, $this->viewer, "invite") && strtotime($endDate) > time()) :
                                    ?>
                                    <span style="display:none;" id="inviteguest_calendar_<?php echo $item->occurrence_id; ?>">

                                        <!--       CHECK IF SITEEVENTINVITE PLGUIN IS INSTALLED THEN WE WILL REDIRECT USER TO AT THAT PAGE.-->
                                        <?php
                                        $siteeventinvite = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventinvite');
                                        if (!empty($siteeventinvite)):
                                            ?>
                                            <a href='<?php echo $this->url(array('user_id' => $item->owner_id, 'siteevent_id' => $item->event_id, 'occurrence_id' => $item->occurrence_id), "siteeventinvite_invite", true) ?>' class="buttonlink icon_siteevents_inviteguests"><?php echo $this->translate('Invite Guests') ?></a>

                                        <?php else: ?>       
                                            <a href ="javascript:void(0);" onclick='Smoothbox.open("<?php echo $this->url(array('controller' => 'member', 'action' => 'invite', 'event_id' => $item->event_id, 'occurrence_id' => $item->occurrence_id, 'format' => 'smoothbox'), 'siteevent_extended', true); ?>")' class="buttonlink icon_siteevents_inviteguests"><?php echo $this->translate('Invite Guests') ?></a>
                                        <?php endif; ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                          
                           <?php if(strtotime($item->endtime) <= time()) :?>
                          <?php $lastOccurrenceEndDate = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurenceEndDate($item->event_id, 'DESC');?>
                                    <?php
                                      //CHECK IF THE OCCURRENCES WIDGET EXIST ON THE EVENT PROFILE PAGE.
                                      $occurrenceIdentity = Engine_Api::_()->siteevent()->existWidget('occurrences');
                                      if($occurrenceIdentity)
                                        $eventProfileUrl = $item->getHref(array('tab' => $occurrenceIdentity));
                                      else
                                        $eventProfileUrl = $item->getHref();
                                      $s1 = '<a href="' . $eventProfileUrl . '">';
                                      $s2 = '</a>';?>
                                      <div class="seaocore_txt_red mtop10"> 
                                  
                                       <?php if($lastOccurrenceEndDate > time()):?> 
                                      <?php echo $this->translate('You can not join this occurrence now as it has been ended. To view all upcoming occurrences of this event, please %1s click here%2s.', array($s1, $s2 ));?>
                                       <?php else:?>
                                          <?php echo $this->translate('You can not join this event now as it has been ended.');?>
                                        <?php endif;?>
                                         
                                      </div>
                              <?php endif;?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>     