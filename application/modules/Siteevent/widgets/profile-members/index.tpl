<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/infotooltip.tpl'; ?>
<?php if ($this->loaded_by_ajax):
    ?>
    <script type="text/javascript">
        var params = {
            requestParams:<?php echo json_encode($this->params) ?>,
            responseContainer: $$('.layout_siteevent_profile_members')
        }
        
        en4.siteevent.ajaxTab.attachEvent('<?php echo $this->identity ?>', params);

    </script>
<?php endif; ?>
    
<?php $datetimeFormat = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.datetime.format', 'medium'); ?>
    
<?php if ($this->showContent): ?>
    <a id="siteevent_profile_guests_anchor"></a>

    <script type="text/javascript">
        var siteeventMemberSearch = <?php echo Zend_Json::encode($this->search) ?>;
        var siteeventMemberPage = Number('<?php echo $this->members->getCurrentPageNumber() ?>');
        var waiting = '<?php echo $this->waiting ?>';
        var rsvp = <?php echo $this->rsvp; ?>;
        var occurrence_id = '<?php echo $this->occurrence_id; ?>';
        var occurrenceid = '<?php echo $this->occurrence_id; ?>';

        en4.core.runonce.add(function() { 
            var url = en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>;
            $('siteevent_members_search_input').addEvent('keypress', function(e) {
                if (e.key != 'enter')
                    return;
                $$('.siteevent_profile_loading_image').setStyle('display', 'block');
                $$('.siteevent_profile_members_top').setStyle('display', 'none');
                $$('.siteevent_profile_list').setStyle('display', 'none');
                en4.core.request.send(new Request.HTML({
                    'url': en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
                    'data': $merge({
                        'format': 'html',
                        'subject': en4.core.subject.guid,
                        'search': this.value,
                        'rsvp': rsvp,
                        'is_ajax_load': 1,
                        defaultoccurrence_id: '<?php echo $this->defaultoccurrence_id; ?>',
                        event_occurrence: 0                        

                    }, params, {occurrence_id: occurrenceid}),
                    onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                        $$('.siteevent_profile_loading_image').setStyle('display', 'none');
                        $$('.siteevent_profile_list').setStyle('display', 'block');
                    }

                }), {
                    'element': $('siteevent_profile_guests_anchor').getParent()
                });
            });

        });

        function eventGuestsRsvp(memberRsvp) {            
            rsvp = memberRsvp;
            var search = $('siteevent_members_search_input').value;
            if (search == "<?php echo $this->translate("Search Guests") ?>")
            {
                search = null;
            }
            //CHECK EITHER TO SHOW ALL EVENTS OR ONLY EVENT OCCURENCE EVENT.


            var url = en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>;
            $$('.siteevent_profile_loading_image').setStyle('display', 'block');
            $$('.siteevent_profile_members_top').setStyle('display', 'none');
            $$('.siteevent_profile_list').setStyle('display', 'none');
            en4.core.request.send(new Request.HTML({
                'url': url,
                'data': $merge({
                    'format': 'html',
                    'subject': en4.core.subject.guid,
                    'search': search,
                    'rsvp': rsvp,
                    'is_ajax_load': 1,
                    defaultoccurrence_id: '<?php echo $this->defaultoccurrence_id; ?>',                   
                    totalEventGuest: '<?php echo $this->params['totalEventGuest']; ?>'
                }, params.requestParams, {occurrence_id: occurrenceid}),
                onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                    $$('.siteevent_profile_loading_image').setStyle('display', 'none');
                    $$('.siteevent_profile_list').setStyle('display', 'block');
                }

            }), {
                'element': $('siteevent_profile_guests_anchor').getParent()
            });
        }

        var paginateEventMembersGuests = function(page) {
            
        $('siteevent_profile_guests_anchor').getNext('.siteevent_profile_list').addClass('siteevent_carousel_loader');
    
            var url = en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>;
            en4.core.request.send(new Request.HTML({
                'url': url,
                'data': $merge({
                    'format': 'html',
                    'subject': en4.core.subject.guid,
                    'search': siteeventMemberSearch,
                    'page': page,
                    'rsvp': rsvp,
                    'is_ajax_load': 1,
                     waiting: waiting,
                    defaultoccurrence_id: '<?php echo $this->defaultoccurrence_id; ?>'                   
                }, params, {occurrence_id: occurrenceid}),
            }), {
                'element': $('siteevent_profile_guests_anchor').getParent()
            });
        }

        var showGuestList_Occurrence = function(occurrence_id) {
            SmoothboxSEAO.open({request: {
                    url: '<?php echo $this->url(array('module' => 'siteevent', 'controller' => 'index', 'action' => 'guest-list'), 'default', true); ?>',
                    requestParams: {
                        'subject': en4.core.subject.guid,
                        'occurrence_id': occurrence_id,
                         occurrenceid: occurrenceid,
                        'is_ajax_load': 1,
                        'rsvp': rsvp
                    }
                }
            });
        }
    </script>

    <?php if (!empty($this->waitingMembers) && $this->waitingMembers->getTotalItemCount() > 0): ?>
        <script type="text/javascript">
            var showWaitingMembers = function() {
                //var url = '<?php echo $this->url(array('module' => 'siteevent', 'controller' => 'widget', 'action' => 'profile-members', 'subject' => $this->subject()->getGuid(), 'format' => 'html'), 'default', true) ?>';
                var url = en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>;
                en4.core.request.send(new Request.HTML({
                    'url': url,
                    'data': {
                        'format': 'html',
                        'subject': en4.core.subject.guid,
                        'waiting': true,
                        'is_ajax_load': 1,
                        defaultoccurrence_id: '<?php echo $this->defaultoccurrence_id; ?>',
                        occurrence_id: occurrenceid
                    }
                }), {
                    'element': $('siteevent_profile_guests_anchor').getParent()
                });
            }

            var showRegisteredMembers = function() {
                //var url = '<?php echo $this->url(array('module' => 'siteevent', 'controller' => 'widget', 'action' => 'profile-members', 'subject' => $this->subject()->getGuid(), 'format' => 'html'), 'default', true) ?>';
                var url = en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>;
                en4.core.request.send(new Request.HTML({
                    'url': url,
                    'data': {
                        'format': 'html',
                        'subject': en4.core.subject.guid,
                        'is_ajax_load': 1,
                        defaultoccurrence_id: '<?php echo $this->defaultoccurrence_id; ?>',
                        occurrence_id: occurrenceid,                      
                        
                    }
                }), {
                    'element': $('siteevent_profile_guests_anchor').getParent()
                });
            }
        </script>
    <?php endif; ?>

    <?php if (!$this->waiting): ?>

        <div class="siteevent_members_search b_medium mbot10">
            <div class="siteevent_members_search_filters fleft">
                <a href="javascript:void(0);" onclick="eventGuestsRsvp(-1);" id='select_all' <?php if ($this->rsvp == -1) echo 'class="bold"'; ?>><?php echo $this->translate('All') ?></a>
                |
                <a href="javascript:void(0);" onclick="eventGuestsRsvp(2);" id='select_attending'<?php if ($this->rsvp == 2) echo 'class="bold"'; ?>><?php echo $this->translate('Attending'); ?></a>		
                |
                <a href="javascript:void(0);" onclick="eventGuestsRsvp(1);" id='select_maybeattending'<?php if ($this->rsvp == 1) echo 'class="bold"'; ?>><?php echo $this->translate('Maybe Attending'); ?></a>
                |
                <a href="javascript:void(0);" onclick="eventGuestsRsvp(0);" id='select_notattending'<?php if ($this->rsvp == 0) echo 'class="bold"'; ?>><?php echo $this->translate('Not Attending'); ?></a>
            </div>
            <div class="siteevent_members_search_right fright">
                <input id="siteevent_members_search_input" class="mright5" type="text" value="<?php if (!empty($this->search)) echo $this->search; ?>" placeholder="<?php echo $this->translate('Search Guests'); ?>">

                <?php //SHOW EVENT OCCURRENCE DATE DROP-DOWN FOR FILTERING GUESTS  ?>
                <?php if (!empty($this->datesInfo) && count($this->datesInfo) > 1): ?>
                    <span class="siteevent_members_search_label"><?php echo $this->translate('Filter'); ?></span>
                    <select onchange="occurrenceid = this.value;
                    eventGuestsRsvp(rsvp)" id='date_filter_occurrence'>
                            <?php
                            $filter_dates = Engine_Api::_()->siteevent()->getAllOccurrenceDate($this->datesInfo);
                            foreach ($filter_dates as $key => $date):
                                ?> 
                            <option value="<?php echo $key; ?>" <?php if ($this->occurrence_id == $key): ?> selected='selected' <?php endif; ?>><?php echo $date; ?></option>
                        <?php endforeach;
                        ?>
                    </select>
                <?php endif; ?>
            </div>
        </div>
        <?php if ($this->totalEventGuests > 0): ?>
        <div class="siteevent_profile_members_top mbot10">
        <div class="fleft">
            <a href="javascript:void(0);" onclick="showGuestList_Occurrence('all');" class="bold">
                <?php echo $this->translate(array('%1$s Total Guest', '%1$s Total Guests', $this->totalEventGuests), $this->locale()->toNumber($this->totalEventGuests)) ?>
            </a>
            <?php if ($this->event_Occurrence > 1): ?>
                | <?php echo $this->translate('This occurrence of the event has') . ' '; ?>
                <a href="javascript:void(0);" onclick= "occurrenceid ='<?php echo $this->current_occurrence; ?>';
                showGuestList_Occurrence(occurrenceid);
                $('date_filter_occurrence').value = occurrenceid">
                    <?php echo $this->translate(array('%1$s guest', '%1$s guests', $this->totalOccurrenceMembers), $this->locale()->toNumber($this->totalOccurrenceMembers)) ?></a>
            <?php endif; ?>    
        </div>
        <?php if ( ((!empty($this->waitingMembers) && $this->waitingMembers->getTotalItemCount() > 0) || (($this->level_id == 1 || $this->event->isOwner($this->viewer) || $this->canEdit) && $this->totalEventGuests > 0 ) || ($this->totalEventGuests > 0 && ($this->level_id == 1 || $this->event->isOwner($this->viewer) || $this->canEdit))) ) : ?>
            
                <?php //if (count($this->datesInfo) > 1) : ?>
                    
                <?php //endif; ?>
                <?php if ((!empty($this->waitingMembers) && $this->waitingMembers->getTotalItemCount() > 0) || (($this->level_id == 1 || $this->event->isOwner($this->viewer) || $this->canEdit) && $this->members->getTotalItemCount() > 0 ) || ($this->members->getTotalItemCount() > 0)) : ?>
                    <div class="fright siteevent_profile_members_top_links">
                        <?php if (!empty($this->waitingMembers) && $this->waitingMembers->getTotalItemCount() > 0): ?>
                            <span class="siteevent_link_wrap mright5">
                                <i class="siteevent_icon icon_siteevents_request"></i>
                                <?php echo $this->htmlLink('javascript:void(0);', $this->translate('See Waiting'), array('onclick' => 'showWaitingMembers(); return false;')) ?>
                            </span>
                        <?php endif; ?>
                        <?php if (($this->level_id == 1 || $this->event->isOwner($this->viewer) || $this->canEdit) && $this->members->getTotalItemCount() > 0): ?>
                            <span class="siteevent_link_wrap mright5">
                                <i class="siteevent_icon_strip siteevent_icon siteevent_icon_message"></i>
                                <?php 
                                     if($this->occurrence_id)
                                        echo $this->htmlLink(array('route' => 'default', 'module' => 'siteevent', 'controller' => 'member', 'action' => 'compose', 'event_id' => $this->event->event_id, 'occurrence_id' => $this->occurrence_id), $this->translate("Message Guests"), array('class' => 'smoothbox'));             else
                                        echo $this->htmlLink(array('route' => 'default', 'module' => 'siteevent', 'controller' => 'member', 'action' => 'compose', 'event_id' => $this->event->event_id), $this->translate("Message Guests"), array('class' => 'smoothbox'));     
                                     
                                     ?>       
                            </span>
                        <?php endif; ?>
                        <?php if (!Engine_Api::_()->siteevent()->isTicketBasedEvent() && $this->members->getTotalItemCount() > 0): ?>  
                            <?php if ($this->level_id == 1 || $this->event->isOwner($this->viewer) || $this->canEdit): ?>
                                <span class="siteevent_link_wrap">
                                    <i class="siteevent_icon_strip siteevent_icon siteevent_icon_download"></i>
                                    <?php 
                                         if($this->occurrence_id && $this->occurrence_id != 'all')
                                            echo $this->htmlLink(array('route' => 'siteevent_extended', 'controller' => 'member', 'action' => 'export-excel', 'event_id' => $this->event->getIdentity(), 'occurrence_id' => $this->occurrence_id), $this->translate('Download Guests List'));
                                         else
                                            echo $this->htmlLink(array('route' => 'siteevent_extended', 'controller' => 'member', 'action' => 'export-excel', 'event_id' => $this->event->getIdentity()), $this->translate('Download Guests List'));
                                         
                                          ?>
                                </span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
           
        <?php endif; ?>
        <?php endif;?>
               </div>
    <?php else: ?>
        <div class="siteevent_members_search b_medium mbot10">

            <span class="siteevent_link_wrap mright5">  
                <i class="siteevent_icon icon_siteevents_request"></i>
                <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View Guests'), array('onclick' => 'showRegisteredMembers(); return false;')) ?>  
            </span>

            <?php //SHOW EVENT OCCURRENCE DATE DROP-DOWN FOR FILTERING GUESTS ?>
            <?php if (!empty($this->datesInfo) && count($this->datesInfo) > 1): ?>
                <div class="siteevent_members_search_right fright">
                    <?php echo $this->translate('Filter'); ?>

                    <select onchange="occurrenceid = this.value;
                    showWaitingMembers()" id="date_filter_occurrence">
                            <?php
                            $filter_dates = Engine_Api::_()->siteevent()->getAllOccurrenceDate($this->datesInfo);
                            foreach ($filter_dates as $key => $date):
                                ?> 
                            <option value="<?php echo $key; ?>" <?php if ($this->occurrence_id == $key): ?> selected='selected' <?php endif; ?>><?php echo $date; ?></option>
                        <?php endforeach;
                        ?>
                    </select>
                </div>
            <?php endif; ?>
        </div>
        <div class="siteevent_profile_members_top mbot10">
            <?php echo $this->translate(array('This event has %1$s member waiting for approval.', 'This event has %1$s members waiting for approval.', $this->members->getTotalItemCount()), $this->locale()->toNumber($this->members->getTotalItemCount())) ?>
        </div>
    <?php endif; ?>

    <div class="siteevent_profile_loading_image" id="" style="display: none;"></div>
    <div class="siteevent_profile_list clr">
    <?php      
      if ($this->members->getTotalItemCount() > 0): ?>
        
            <ul>
                <?php
                foreach ($this->members as $member):
                    if (isset($member->resource_id) && !empty($member->resource_id)) {
                        $memberInfo = $member;
                        $member = $this->item('user', $memberInfo->user_id);
                    } else {

                        $memberInfo = $this->event->membership()->getMemberInfoCustom($member);
                    }
                    if(!isset($member->user_id)) continue;
                    $listItem = $this->list->get($member);
                    $isLeader = ( null !== $listItem );
                    ?>
                    <li>
                        <div class="siteevent_profile_list_photo b_medium">
                            <?php echo $this->htmlLink($member->getHref(), $this->itemPhoto($member, 'thumb.icon', '', array('align' => 'left')), array('class' => 'seao_common_add_tooltip_link', 'rel' => 'user' . ' ' . $member->user_id)) ?>            
                        </div>
                        <div class="siteevent_profile_list_info">
                            <div class="siteevent_profile_list_title f_small" id="siteevent_profile_list_title_<?php echo $member->user_id ?>">
                                <?php echo $this->htmlLink($member->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($member->getTitle(), 20), array('class' => 'item_photo seao_common_add_tooltip_link bold', 'title' => $member->getTitle(), 'target' => '_parent', 'rel' => 'user' . ' ' . $member->user_id)); ?>
                                <?php // We are commenting below code for now. we will use it later. ?>
                                <?php //if( $this->event->getParent()->getGuid() == ($member->getGuid())): ?>
                                <?php //echo $this->translate('(%s)', ( $memberInfo->title ? $memberInfo->title : $this->translate('Owner') )) ?>
                                <?php //elseif($isLeader):?>  
                                <?php //echo $this->translate('(%s)', ( $memberInfo->title ? $memberInfo->title : $this->translate('Leader') )) ?>
                                <?php //elseif($this->event->host_id == $memberInfo->user_id): ?>  
                                <?php //echo $this->translate('(%s)', ( $memberInfo->title ? $memberInfo->title : $this->translate('Host') )) ?>
                                <?php //endif;  ?> 
                            </div>

                            <div class="siteevent_profile_list_stats seaocore_txt_light">
                                <?php if ($memberInfo->rsvp == 0): ?>
                                    <?php echo $this->translate('Not Attending') ?>
                                <?php elseif ($memberInfo->rsvp == 1): ?>
                                    <?php echo $this->translate('Maybe Attending') ?>
                                <?php elseif ($memberInfo->rsvp == 2): ?>
                                    <?php echo $this->translate('Attending') ?>
                                <?php else: ?>
                                    <?php echo $this->translate('Awaiting Approval') ?>
                                <?php endif; ?>
                            </div>  
                            
                            <?php
                            $can_UserReview = Engine_Api::_()->getDbtable('userreviews', 'siteevent')->isGuestReviewAllowed(array('event_id' => $this->event->getIdentity(), 'user_id' => $member->getIdentity(), 'viewer_id' => $this->viewer->getIdentity())); ?>
                            <?php if($this->viewer->getIdentity() && $this->isGuestReviewAllowed && $member->getIdentity() != $this->viewer->getIdentity() && empty($can_UserReview) && $this->tempEndDate && $memberInfo->rsvp == 2): ?>
                                                                                                                                <div class="siteevent_profile_list_stats seaocore_txt_light">
																<?php echo $this->htmlLink(array('route' => 'siteevent_user_review', 'controller' => 'userreview', 'action' => 'view', 'event_id' => $this->event->getIdentity(), 'user_id' => $member->getIdentity(), 'tab_id' => $this->identity), 'Write a review'); ?>
															</div>
														<?php endif; ?>
														
														<?php $totalReviews = Engine_Api::_()->getDbtable('userreviews', 'siteevent')->totalReviews($this->event->getIdentity(), $member->getIdentity()); ?>
														<?php if($totalReviews): ?>
															<div class="siteevent_profile_list_stats seaocore_txt_light">
																<?php 	echo $this->htmlLink(array('route' => 'siteevent_user_review', 'controller' => 'userreview', 'action' => 'view', 'event_id' => $this->event->getIdentity(), 'user_id' => $member->getIdentity(), 'tab_id' => $this->identity), $this->translate(array('%s review', '%s reviews', $totalReviews), $this->locale()->toNumber($totalReviews))); ?>
															</div>
														<?php endif; ?>
														
														<div class="siteevent_profile_review_stars">
															<span class="siteevent_profile_review_rating">
																<span class="fleft">
																	<?php $averageUserReviews = Engine_Api::_()->getDbtable('userreviews', 'siteevent')->averageUserRatings(array('user_id' => $member->getIdentity(), 'event_id' => $this->event->getIdentity()));
																	echo $this->ShowRatingStarSiteevent($averageUserReviews, 'user', 'small-star',null, false, false); ?>
																</span>
															</span>
														</div>
                            
                            <div class="siteevent_profile_list_stats clr">
                                <?php if ($this->event->isOwner($this->viewer()) || (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.leader', 1) && Engine_Api::_()->getDbtable('listItems', 'siteevent')->checkLeader($this->event))): ?>
                                    <?php if (!$this->event->isOwner($member) && $memberInfo->active == true): ?>
                                        <?php
                                        echo $this->htmlLink(array('route' => 'siteevent_extended', 'controller' => 'member', 'action' => 'remove', 'event_id' => $this->event->getIdentity(), 'user_id' => $member->getIdentity(), 'occurrence_id' => $this->current_occurrence), '', array(
                                            //'class' => 'buttonlink smoothbox icon_friend_remove'
                                            'class' => 'smoothbox siteevent_icon icon_siteevents_remove mright5',
                                            'title' => $this->translate('Remove Guest')
                                        ))
                                        ?>
                                        <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.guestconfimation', 0) && $memberInfo->rsvp == 2 && !$this->event->approval):?>
                                            <?php if($memberInfo->confirm == 0):?>
                                                <?php
                                                echo $this->htmlLink(array('route' => 'siteevent_extended', 'controller' => 'member', 'action' => 'confirm', 'event_id' => $this->event->getIdentity(), 'user_id' => $member->getIdentity(), 'occurrence_id' => $this->current_occurrence), '', array(
                                                    //'class' => 'buttonlink smoothbox icon_friend_remove'
                                                    'class' => 'smoothbox siteevent_icon icon_siteevents_confirm mright5',
                                                    'title' => $this->translate('Confirmed / Rejected')
                                                ))
                                                ?>
                                            <?php else: ?>
                                              <?php if($memberInfo->confirm == 1) :?>
                                                <i class="siteevent_icon icon_siteevents_confirmed mright5" title="<?php echo $this->translate('confirmed');?>"></i>
                                              <?php elseif($memberInfo->confirm == 2):?>
                                                <i class="siteevent_icon icon_siteevents_unconfirmed mright5" title="<?php echo $this->translate('rejected');?>"></i>
                                                    
                                              <?php endif;?>
                                            <?php endif;?>
                                        <?php endif;?>
                                    <?php endif; ?>
                                <?php if ($memberInfo->active == false && $memberInfo->resource_approved == false): ?>
                                        <?php
                                        echo $this->htmlLink(array('route' => 'siteevent_extended', 'controller' => 'member', 'action' => 'approve', 'event_id' => $this->event->getIdentity(), 'user_id' => $member->getIdentity(), 'occurrence_id' => $this->current_occurrence), '', array(
                                            //'class' => 'buttonlink smoothbox icon_siteevent_accept'
                                            'class' => ' smoothbox siteevent_icon icon_siteevents_inviteaccept mright5',
                                            'title' => $this->translate('Approve Request')
                                        ))
                                        ?>

                                        <?php
                                        echo $this->htmlLink(array('route' => 'siteevent_extended', 'controller' => 'member', 'action' => 'remove', 'event_id' => $this->event->getIdentity(), 'user_id' => $member->getIdentity(), 'occurrence_id' => $this->current_occurrence), '', array(
                                            //'class' => 'buttonlink smoothbox icon_event_reject'
                                            'class' => 'smoothbox siteevent_icon icon_siteevents_invitereject mright5',
                                            'title' => $this->translate('Reject Request')
                                        ))
                                        ?>
                                    <?php endif; ?>
                                    <?php if ($memberInfo->active == false && $memberInfo->resource_approved == true): ?>
                                        <?php
                                        echo $this->htmlLink(array('route' => 'siteevent_extended', 'controller' => 'member', 'action' => 'cancel', 'event_id' => $this->event->getIdentity(), 'user_id' => $member->getIdentity(), 'occurrence_id' => $this->current_occurrence), '', array(
                                            //'class' => 'buttonlink smoothbox icon_siteevent_cancel'
                                            'class' => ' smoothbox siteevent_icon icon_siteevents_invitecancel mright5',
                                            'title' => $this->translate('Cancel Invite')
                                        ))
                                        ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php if ($this->event->isOwner($this->viewer())): ?>
                                <?php if ($memberInfo->active && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.leader', 1)): ?>
                                        <?php
                                        $endDate = $this->locale()->toEventDateTime(Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurenceEndDate($this->event->getIdentity(), 'DESC', $this->occurrence_id));
                                        $currentDate = $this->locale()->toEventDateTime(time());
                                        ?>
                                        <?php if ($isLeader): ?>
                                            <?php
                                            echo $this->htmlLink(array('route' => 'siteevent_extended', 'controller' => 'member', 'action' => 'demote', 'event_id' => $this->event->getIdentity(), 'user_id' => $member->getIdentity(), 'occurrence_id' => $this->current_occurrence), '', array(
                                                //'class' => 'buttonlink smoothbox icon_siteevent_demote'
                                                'class' => 'smoothbox siteevent_icon icon_siteevents_demote mright5',
                                                'title' => $this->translate('Remove as Leader')
                                            ))
                                            ?>
                                        <?php elseif (!$this->event->isOwner($member) && strtotime($endDate) > strtotime($currentDate)): ?>
                                            <?php
                                            echo $this->htmlLink(array('route' => 'siteevent_extended', 'controller' => 'member', 'action' => 'promote', 'event_id' => $this->event->getIdentity(), 'user_id' => $member->getIdentity(), 'occurrence_id' => $this->current_occurrence), '', array(
                                                'class' => ' smoothbox siteevent_icon icon_siteevents_promote',
                                                'title' => $this->translate('Make Event Leader')
                                            ))
                                            ?>
                                        <?php endif; ?>
                                    <?php endif; ?>                 
                                <?php endif; ?>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
      <?php else:?>
      <div class="tip"> 
                    <span>
      <?php echo $this->translate('Nobody has joined this event that matches your search criteria.'); ?>
                    </span>
      </div>
       
   <?php endif; ?>
      </div>
     <?php if ($this->members->count() > 1): ?>
            <div>
                <?php if ($this->members->getCurrentPageNumber() > 1): ?>
                    <div id="user_siteevent_members_previous" class="paginator_previous">
                        <?php
                        echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
                            'onclick' => 'paginateEventMembersGuests(siteeventMemberPage - 1)',
                            'class' => 'buttonlink icon_previous',
                            'style' => '',
                        ));
                        ?>
                    </div>
                <?php endif; ?>
                <?php if ($this->members->getCurrentPageNumber() < $this->members->count()): ?>
                    <div id="user_siteevent_members_next" class="paginator_next">
                        <?php
                        echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
                            'onclick' => 'paginateEventMembersGuests(siteeventMemberPage + 1)',
                            'class' => 'buttonlink icon_next'
                        ));
                        ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
  
<?php endif; ?>