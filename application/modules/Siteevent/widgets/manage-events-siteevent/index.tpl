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
<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/scripts/_commonFunctions.js');
$hasPackageEnable = Engine_Api::_()->siteevent()->hasPackageEnable();
?>
<script type="text/javascript">
    var rsvp = '<?php echo $this->rsvp; ?>';
    var viewType = '<?php echo $this->viewType; ?>';
var showFriendList = function(event_id, occurrence_id) {

        SmoothboxSEAO.open('<center><div class="siteevent_profile_loading_image"></div></center>');

        en4.core.request.send(new Request.HTML({
            'url': '<?php echo $this->url(array('module' => 'siteevent', 'controller' => 'index', 'action' => 'guest-list'), 'default', true); ?>',
            'data': {
                'format': 'html',
                'subject': 'siteevent_event_' + event_id,
                'rsvp': -1,
                'is_ajax_load': 1,
                occurrence_id: occurrence_id,
                friendsonly: true
            },
            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {

                if ($$('.seao_smoothbox_lightbox_overlay').isVisible() == 'true') {
                    SmoothboxSEAO.close();
                    SmoothboxSEAO.open('<div style="height:400px;">' + responseHTML + '</div>');
                }
            }
        }));
    }
</script>
<?php include_once APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/infotooltip.tpl'; ?>
<?php $datetimeFormat = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.datetime.format', 'medium'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl .'application/modules/Siteevent/externals/scripts/core.js'); ?>
<?php
$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteevent.css')
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteevent_dashboard.css')
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
?>
<?php $siteevent_approved = true;
if (!$this->isajax):
    ?>
    <?php include APPLICATION_PATH . '/application/modules/Siteevent/views/scripts/_managequicklinks.tpl'; ?>
<?php endif; ?>

<?php if (!$this->pagination): ?>
    <div class='siteevent_manage_event' id="siteevent_manage_event">
        <?php $renew_date = date('Y-m-d', mktime(0, 0, 0, date("m"), date('d', time()) + (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.renew.email', 2)))); ?>
    <?php if ($this->current_count >= $this->quota && !empty($this->quota)): ?>
            <div class="tip"> 
                <span><?php echo $this->translate("You have already created the maximum number of events allowed. If you would like to create a new event, please delete an old one first."); ?> </span> 
            </div>
            <br/>
    <?php endif; ?>
        <div class="siteevent_myevents_top_links o_hidden b_medium">
            <div class="fleft siteevent_myevents_view_links">
                <span class="seaocore_button fleft seaocore_button_selected">
                    <a href='<?php echo $this->url(array('action' => 'manage', 'ref' => 'list'), "siteevent_general", true); ?>' >
                        <span><?php echo $this->translate('List'); ?></span>
                    </a>
                </span>
                <span class="seaocore_button fleft">
                    <a href='<?php echo $this->url(array('action' => 'manage', 'ref' => 'calendar'), "siteevent_general", true); ?>' >
                        <span><?php echo $this->translate('Calendar'); ?></span>
                    </a>
                </span>     
            </div>
            
            <?php if($this->showEventUpcomingPastCount) :?>
                <div class="fright p5">
                    <a href="javascript:void(0);" onclick="rsvp = <?php echo $this->rsvp;?>;
                viewType = 'upcoming';
                filter_rsvp(-1);" <?php if ($this->viewType == 'upcoming') echo 'class="bold"'; ?>><?php echo $this->translate('Upcoming'); ?>&nbsp;(<?php echo $this->locale()->toNumber($this->totalUpcomingEventCount); ?>)</a>

                    |

                    <a href="javascript:void(0);" onclick="rsvp = <?php echo $this->rsvp;?>;
                viewType = 'past';
                filter_rsvp(-1);" <?php if ($this->viewType == 'past') echo 'class="bold"'; ?>><?php echo $this->translate('Past'); ?>&nbsp;(<?php echo $this->locale()->toNumber($this->totalPastEventCount); ?>)</a>

                </div>
            <?php else:?>
                <div class="fright p5">
                    <a href="javascript:void(0);" onclick="rsvp = <?php echo $this->rsvp;?>;
                viewType = 'upcoming';
                filter_rsvp(-1);" <?php if ($this->viewType == 'upcoming') echo 'class="bold"'; ?>><?php echo $this->translate('Upcoming'); ?></a>

                    |

                    <a href="javascript:void(0);" onclick="rsvp = <?php echo $this->rsvp;?>;
                viewType = 'past';
                filter_rsvp(-1);" <?php if ($this->viewType == 'past') echo 'class="bold"'; ?>><?php echo $this->translate('Past'); ?></a>
                </div>
            <?php endif;?>
            
            <div class="siteevent_myevents_top_filter_links o_hidden txt_center">
                <a href="javascript:void(0);" onclick="rsvp = -1;
            filter_rsvp(-1);" <?php if ($this->rsvp == -1) echo 'class="bold"'; ?>><?php echo $this->translate('All'); ?></a> |
                <a href="javascript:void(0);" onclick="rsvp = -4;
            filter_rsvp(-4);" <?php if ($this->rsvp == -4) echo 'class="bold"'; ?>><?php echo $this->translate('Leading'); ?></a> |
                <a href="javascript:void(0);" onclick="rsvp = -2;
            filter_rsvp(-2);" <?php if ($this->rsvp == -2) echo 'class="bold"'; ?>><?php echo $this->translate('Hosting'); ?></a> |
                <?php if(!Engine_Api::_()->siteevent()->isTicketBasedEvent()): ?>
                    <a href="javascript:void(0);" onclick="rsvp = 2;
                filter_rsvp(2);" <?php if ($this->rsvp == 2) echo 'class="bold"'; ?>><?php echo $this->translate('Attending'); ?></a> |
                    <a href="javascript:void(0);" onclick="rsvp = 1;
                filter_rsvp(1);" <?php if ($this->rsvp == 1) echo 'class="bold"'; ?>><?php echo $this->translate('Maybe Attending'); ?></a> |
                    <a href="javascript:void(0);" onclick="rsvp = 0;
                filter_rsvp(0);" <?php if ($this->rsvp == 0) echo 'class="bold"'; ?>><?php echo $this->translate('Not Attending'); ?></a> |
                <?php endif; ?>
                <a href="javascript:void(0);" onclick="rsvp = -3;
            filter_rsvp(-3);" <?php if ($this->rsvp == -3) echo 'class="bold"'; ?>><?php echo $this->translate('Liked'); ?></a>
            </div>
        </div>
        <div id="manage_events">
            <ul class="siteevent_browse_list" id="siteevent_browse_list">
            <?php endif; ?>    
            <?php if ($this->paginator->getTotalItemCount() > 0): ?>

                <?php $prev_date = 0; ?>
                <?php foreach ($this->paginator as $item): ?>
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

                    
                       
                       <?php $prev_date = $next_datetime;
                    endif;
                    ?>
                    
                     <?php if(!empty($this->dateTimeDisplayed)):?>  
                         <li class='bold siteevent_browse_list_sep b_medium f_small'>
                                        <?php
                                            if ($datetimeFormat != 'full')
                                                echo $this->locale()->toDate($item->starttime, array('format' => 'EEEE')) . ', ' . $this->locale()->toDate($item->starttime, array('size' => $datetimeFormat));
                                            else
                                                echo $this->locale()->toDate($item->starttime, array('size' => $datetimeFormat));
                                        ?>
                        </li>
                    <?php endif;?>

                    <li class="<?php if (!$item->approved): ?>siteevent_list_highlighted<?php endif; ?>  b_medium <?php if ($item->closed && $item->owner_id != $this->viewer()->getIdentity()): ?>siteevent_disabled<?php endif; ?>" id="userlist_list_<?php echo $item->occurrence_id; ?>">
                        <div class='siteevent_browse_list_photo b_medium'>
                            <?php if (!empty($this->eventInfo) && in_array('featuredLabel', $this->eventInfo) && $item->featured): ?>
                                <i class="siteevent_list_featured_label" title="<?php echo $this->translate('Featured'); ?>"></i>
                            <?php endif; ?>
        <?php if (!empty($this->eventInfo) && in_array('newLabel', $this->eventInfo) && $item->newlabel): ?>
                                <i class="siteevent_list_new_label" title="<?php echo $this->translate('New'); ?>"></i>

                            <?php endif; ?>

                            <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.main', '', array('align' => 'center'))) ?>

                                <?php if (!empty($this->eventInfo) && in_array('sponsoredLabel', $this->eventInfo) && !empty($item->sponsored)): ?>
                                <div class="siteevent_list_sponsored_label" style="background: <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.sponsoredcolor', '#FC0505'); ?>">
                                <?php echo $this->translate('SPONSORED'); ?>                 
                                </div>
        <?php endif; ?>
                        </div>
                        <div class='siteevent_browse_list_info'>
                      <div class='siteevent_browse_list_info_header o_hidden'>
                                <div class="siteevent_list_title_small">
                                  <?php if (empty($item->approved)): ?>
                                  <i title="<?php echo $this->translate('Not approved');?>" class="siteevent_icon seaocore_icon_disapproved fright mleft5"></i>
                                <?php endif; ?>
                                    <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
                                    <span class="fright f_small" title="<?php echo $this->translate('Start Time') ?>">                      
                                       <?php if(empty($this->dateTimeDisplayed)):?>  
                                        <?php
                                            if ($datetimeFormat != 'full')
                                                echo $this->locale()->toDate($item->starttime, array('format' => 'EEEE')) . ', ' . $this->locale()->toDate($item->starttime, array('size' => $datetimeFormat));
                                            else
                                                echo $this->locale()->toDate($item->starttime, array('size' => $datetimeFormat));
                                        ?>
                                        <?php endif;?>
                                        <?php echo $this->locale()->toEventTime($item->starttime, array('size' => $datetimeFormat)); ?>
                                    </span> 
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
                                <?php echo $this->htmlLink(array('route' => 'seaocore_viewmap', "id" => $item->event_id, 'resouce_type' => 'siteevent_event'), $item->location, array('class' => 'smoothbox')); ?>
                                </div>
                            <?php endif; ?>
                              <?php
                              //SHOW FRIENDS WHO ARE ALSO GOING FOR THIS EVENT.
                              if ($this->viewType == 'upcoming') :
                                $params = array('count' => true, 'limit' => 2, 'occurrence_id' => $item->occurrence_id);
                                $friendsInfo = $item->membership()->getEventFriends($params);
                                if (!empty($friendsInfo) && $friendsInfo['friends_count'] > 0) {
                                  $friends = $friendsInfo['friends'];
                                  ?> 
                                  <div class='siteevent_browse_list_info_stat seaocore_txt_light'>
                                    <?php
                                    foreach ($friends as $key => $friend) {
                                      $member = Engine_Api::_()->getItem('user', $friend['resource_id']);
                                      if ($friendsInfo['friends_count'] > 2) {
                                        if ($key == 0)
                                          echo $this->htmlLink($member->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($member->getTitle(), 20), array('class' => 'item_photo seao_common_add_tooltip_link', 'title' => $member->getTitle(), 'target' => '_parent', 'rel' => 'user' . ' ' . $member->user_id)) . ' ' . $this->translate('and') . ' <a href="javascript:void(0);" onclick="showFriendList(' . $item->event_id . ',' . $item->occurrence_id . ' );">' . $this->translate('%s other friends', $friendsInfo['friends_count'] - 1) . '</a> ' . $this->translate('are attending.');
                                      } else {
                                        if ($key == 1)
                                          echo ' ' . $this->translate('and') . ' ';

                                        echo $this->htmlLink($member->getHref(), Engine_Api::_()->seaocore()->seaocoreTruncateText($member->getTitle(), 20), array('class' => 'item_photo seao_common_add_tooltip_link', 'title' => $member->getTitle(), 'target' => '_parent', 'rel' => 'user' . ' ' . $member->user_id));
                                        if ($key == 1)
                                          echo ' ' . $this->translate('are attending.');
                                        elseif ($friendsInfo['friends_count'] == 1)
                                          echo ' ' . $this->translate('is attending.');
                                      }
                                    }
                                    ?>
                                  </div>  

                                <?php
                                }
                              endif;
                              ?>
                            <?php
                            //CHECK IF THE VIEWER IS LEADER
                            $list = $item->getLeaderList();
                            $leaderRow = $list->get($this->viewer());
                            $hostText = '';
                            if ($this->viewer()->getIdentity() == $item->owner_id)
                                $hostText = $this->viewType == 'upcoming' ? "You are owner." : "You were owner.";
                            if ($leaderRow != null && empty($hostText))
                                $hostText = $this->viewType == 'upcoming' ? "You are leader." : "You were leader.";
                            if (($this->viewer()->getIdentity() == (int) $item->host_id) && $item->host_type == 'user' && empty($hostText))
                                $hostText = $this->viewType == 'upcoming' ? "You are host." : "You were host.";
                            if (!Engine_Api::_()->siteevent()->isTicketBasedEvent() && empty($hostText) && isset($item->rsvp) && $item->membership_userid == $this->viewer()->getIdentity())
                                $hostText = $this->viewType == 'upcoming' ? ($item->rsvp == 3 ? "You are invited." : "You are guest.") : ($item->rsvp == 3 ? "You were invited." : "You were guest.");
                            if (empty($hostText))
                                $hostText = 'You like this.';
                            ?>
                            <?php if (!empty($hostText)) : ?>
                                <div class='siteevent_browse_list_info_stat seaocore_txt_light'>
                                    <?php echo $this->translate($hostText); ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($hasPackageEnable): ?>
                              <div class='seaocore_browse_list_info_date clr'>
                                <?php echo $this->translate('Package: ') ?>           
                                <a href='<?php echo $this->url(array("action" => "detail", 'id' => $item->package_id), "siteevent_package", true) ?>' onclick="owner(this);return false;" class="smoothbox" title="<?php echo $this->translate(ucfirst($item->getPackage()->title)) ?>"><?php echo $this->translate(ucfirst($item->getPackage()->title)); ?>
                                </a>
                              </div>
                            <?php endif; ?>
                            <div class='seaocore_browse_list_info_date'>
                              <?php if ($hasPackageEnable): ?>
                                <?php if (!$item->getPackage()->isFree()): ?>
                                  <span>
                                    <?php echo $this->translate('Payment: ') ?>
                                    <?php
                                    if ($item->status == "initial"):
                                      echo $this->translate("Not made");
                                    elseif ($item->status == "active"):
                                      echo $this->translate("Yes");
                                    else:
                                      echo $this->translate(ucfirst($item->status));
                                    endif;
                                    ?>
                                  </span>
                                  <?php if (!empty($item->approved_date)): ?>
                                    |
                                  <?php endif; ?>
                                <?php endif; ?>
                              <?php endif; ?>
                              <?php if ($hasPackageEnable): ?>
                                <?php if (!empty($item->approved_date)): ?>
                                  <span style="color: chocolate;"><?php echo $this->translate('First Approved on ') . $this->timestamp(strtotime($item->approved_date)) ?></span>
                                  <?php if ($item->expiration_date && $item->expiration_date !== "0000-00-00 00:00:00"): ?>
                                    <span style="color: chocolate;"> | </span>
                                    <span style="color: green;">
                                      <?php
                                      $expiry = $item->getExpiryDate();
                                      if ($expiry !== "Expired" && $expiry !== $this->translate('Never Expires')):
                                        echo $this->translate("Expiration Date: ");
                                      endif;
                                      echo $expiry;
                                      ?>
                                    </span>
                                  <?php endif; ?>
                                <?php endif; ?>
                              <?php endif ?>
                            </div>

                            <div class='siteevent_manage_list_options'>
                                <?php if ($item->owner_id == $this->viewer()->getIdentity() || $leaderRow != null) : ?>
                                        <?php if ($this->can_edit) : ?>
                                            <a href='<?php echo $this->url(array('action' => 'edit', 'event_id' => $item->event_id), "siteevent_specific", true) ?>' class='buttonlink icon_siteevent_dashboard'><?php
                                            if (!empty($siteevent_approved)) {
                                                echo $this->translate("Dashboard");
                                            } else {
                                                //echo $this->translate($this->event_manage);
                                            }
                                            ?></a>
                                        <?php endif; ?>

                                    <?php
                                    if ($item->draft == 1 && $this->can_edit)
                                        echo $this->htmlLink(array('route' => "siteevent_specific", 'action' => 'publish', 'event_id' => $item->event_id), $this->translate("Publish Event"), array(
                                            'class' => 'buttonlink smoothbox icon_siteevent_publish'))
                                        ?> 

                                    <?php if (empty($item->draft)): ?>
                                        <?php if (!$item->closed && $this->can_edit): ?>
                                            <a href='<?php echo $this->url(array('action' => 'close', 'event_id' => $item->event_id), "siteevent_specific", true) ?>' class='buttonlink smoothbox icon_siteevent_cancel'><?php echo $this->translate("Cancel Event"); ?></a>
                                        <?php elseif ($this->can_edit): ?>
                                            <a href='<?php echo $this->url(array('action' => 'close', 'event_id' => $item->event_id), "siteevent_specific", true) ?>' class='buttonlink smoothbox icon_siteevent_publish'><?php echo $this->translate("Re-publish Event"); ?></a>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if ($this->can_delete): ?>
                                        <a href='<?php echo $this->url(array('action' => 'delete', 'event_id' => $item->event_id), "siteevent_specific", true) ?>' class='buttonlink seaocore_icon_delete'><?php echo $this->translate("Delete Event"); ?></a>
                                    <?php endif; ?> 
                                    
                                    <?php
                                    $auth = Engine_Api::_()->authorization()->context;

                                    if ($this->viewType != 'past' && $auth->isAllowed($item, $this->viewer(), "invite") && (!isset($item->rsvp) || $item->rsvp == null || (isset($item->rsvp) && $item->membership_userid == $this->viewer()->getIdentity()))):
                                        ?>


                                        <!--       CHECK IF SITEEVENTINVITE PLGUIN IS INSTALLED THEN WE WILL REDIRECT USER TO AT THAT PAGE.-->
                                        <?php
                                        $siteeventinvite = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventinvite');
                                        if (!empty($siteeventinvite)):
                                            ?>
                                            <a href='<?php echo $this->url(array('user_id' => $item->owner_id, 'siteevent_id' => $item->event_id, 'occurrence_id' => $item->occurrence_id), "siteeventinvite_invite", true) ?>' class="buttonlink icon_siteevents_inviteguests"><?php echo $this->translate('Invite Guests') ?></a>

                                        <?php else: ?>       
                                            <a href ="javascript:void(0);" onclick='Smoothbox.open("<?php echo $this->url(array('controller' => 'member', 'action' => 'invite', 'event_id' => $item->event_id, 'occurrence_id' => $item->occurrence_id, 'format' => 'smoothbox'), 'siteevent_extended', true); ?>")' class="buttonlink icon_siteevents_inviteguests"><?php echo $this->translate('Invite Guests') ?></a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php if ($this->viewType != 'past' && !$item->closed): ?>
                                    <div id="join_list_<?php echo $item->occurrence_id; ?>" <?php if ($item->rsvp != 3): ?> style="display:none;" <?php endif; ?>>
                                        <a href='javascript:void(0);' onclick="en4.siteevent.member.acceptInvite(<?php echo $item->event_id; ?>, <?php echo $item->occurrence_id; ?>, 'join', 'list')" class='buttonlink icon_siteevents_inviteaccept'>
                                            <?php echo $this->translate("Accept"); ?>
                                        </a>

                                        <a href='javascript:void(0);' onclick="en4.siteevent.member.saveRSVP(0, <?php echo $item->event_id; ?>, <?php echo $item->occurrence_id; ?>, 'reject', 'list')" class='buttonlink icon_siteevents_invitereject'>
                                    <?php echo $this->translate("Ignore"); ?>
                                        </a>
                                    </div>
                                    <?php
                                    $auth = Engine_Api::_()->authorization()->context;

                                    if ($auth->isAllowed($this->result, $this->viewer, "invite") && $item->owner_id != $this->viewer()->getIdentity() && $leaderRow == null && isset($item->rsvp) && $item->membership_userid == $this->viewer()->getIdentity()) :
                                        ?>           

                                        <span <?php if ($item->rsvp == 3): ?> style="display:none;"<?php endif; ?> id="inviteguest_list_<?php echo $item->occurrence_id; ?>">


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
                                            
                                    <?php if(Engine_Api::_()->siteevent()->isTicketBasedEvent() && Engine_Api::_()->siteeventticket()->bookNowButton($item)): ?>
                                        
                                    <?php elseif(!Engine_Api::_()->siteevent()->isTicketBasedEvent()): ?>        
                                            
                                        <?php if (isset($item->rsvp) && $item->membership_userid == $this->viewer()->getIdentity()) : ?>           
                                            <div id="rsvp_list_<?php echo $item->occurrence_id; ?>" <?php if ($item->rsvp == 3): ?> style="display:none;"<?php endif; ?>>

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
                                                    <?php if(Engine_Api::_()->siteevent()->isTicketBasedEvent()): ?>
                                                        
                                                    <?php else: ?>
                                                        <select onchange="en4.siteevent.member.saveRSVP(this.value, <?php echo $item->event_id; ?>,<?php echo $item->occurrence_id; ?>, 'rsvp', 'list');
                                $('filtered_selected_<?php echo $item->event_id; ?>').innerHTML = this.options[this.options.selectedIndex].text;" id="selected_rsvp_list_<?php echo $item->occurrence_id; ?>">
                                                            <option value="2" <?php echo $item->rsvp == 2 ? "selected=selected" : ''; ?>><?php echo $this->translate('Attending'); ?></option>
                                                            <option value="1" <?php echo $item->rsvp == 1 ? "selected=selected" : ''; ?>><?php echo $this->translate('Maybe Attending'); ?></option>
                                                            <option value="0" <?php echo $item->rsvp == 0 ? "selected=selected" : ''; ?>><?php echo $this->translate('Not Attending'); ?></option>
                                                        </select>    
                                                    <?php endif; ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?> 
                                    <?php if ($hasPackageEnable): ?>
                                      <?php if (Engine_Api::_()->siteeventpaid()->canShowPaymentLink($item->event_id)): ?>
                                        <div>
                                          <span class="seaocore_button">
                                            <a href='javascript:void(0);' onclick="submitSession(<?php echo $item->event_id ?>)"><?php echo $this->translate('Make Payment'); ?></a>
                                          </span>
                                        </div>
                                      <?php endif; ?>

                                      <?php if (Engine_Api::_()->siteeventpaid()->canShowRenewLink($item->event_id)): ?>
                                        <div>
                                          <span class="seaocore_button">
                                            <a href='javascript:void(0);' onclick="submitSession(<?php echo $item->event_id ?>)"><?php echo $this->translate("Renew Event"); ?></a>
                                          </span>
                                        </div>
                                      <?php endif; ?>
                                    <?php endif; ?>
                                               
                                <?php elseif ($item->closed && $item->owner_id != $this->viewer()->getIdentity()): ?>
                                    <div class="tip"> 
                                        <span>
                                            <?php echo $this->translate('This event has been cancelled.'); ?>
                                        </span>
                                    </div>
                                <?php endif; ?>   
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>

            <?php elseif ($this->rsvp != -1): ?>
                <div class="tip"> 
                    <span>
                        <?php
                        if (!empty($siteevent_approved)) {
                            echo $this->translate('You do not have any event that match your search criteria.');
                        } else {
                            echo $this->translate($this->event_manage_msg);
                        }
                        ?> 
                    </span> 
                </div>
            <?php else: ?>
                <div class="tip">
                    <span> 
                        <?php
                        if (!empty($siteevent_approved)) { 
                            if($this->viewType != 'past')
                              echo $this->translate('You do not have any event.');
                            else
                              echo $this->translate('You do not have any event that match your search criteria.');
                        } else {
                            echo $this->translate($this->event_manage_msg);
                        }
                        ?>
                        <?php if($this->viewType != 'past'):?>
                          <?php if ($hasPackageEnable):?>
			<?php echo $this->translate('Get started by %1$screating%2$s a new event.', '<a href="' . $this->url(array('action' => 'index'), "siteevent_package") . '">', '</a>'); ?>
		<?php else:?>
		  <?php echo $this->translate('Get started by %1$screating%2$s a new event.', '<a href="' . $this->url(array('action' => 'create'), "siteevent_general") . '">', '</a>'); ?>
		<?php endif;?>
                        <?php endif;?>
                    </span> 
                </div>
            <?php endif; ?>

            <?php if (!$this->pagination): ?> 
            </ul>
        </div>     

        <div id="join_form_options" style="display:none;">
            <?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this); ?>
        </div>

        <?php if ($this->paginator->count() > 1 && $this->paginator->count() > $this->page_id): ?>
            <div id="pagination_container">
                <div class="seaocore_view_more" id="list_viewmore" style="display: none;">
                    <?php
                    echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
                        'id' => 'list_viewmore_link',
                        'class' => 'buttonlink icon_viewmore'
                    ))
                    ?>
                </div>

                <div id="list_loading" style="display: none;" class="seaocore_view_more">
                    <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' alt="Loading" />
                    <?php echo $this->translate("Loading ...") ?>
                </div>
                <div class="seaocore_view_more" id="list_noviewmore" style="display: none;">
                    <?php echo $this->translate('There are no more events.'); ?>
                </div>
            </div>
        <?php endif; ?> 
        <?php if($hasPackageEnable):?>
          <form name="setSession_form" method="post" id="setSession_form" action="<?php echo $this->url(array(), "siteevent_session_payment", true) ?>">
              <input type="hidden" name="event_id_session" id="event_id_session" />
          </form>
        <?php endif;?>
    </div>
<?php endif; ?>

<script type="text/javascript">

    function filter_rsvp() {
        if ($('pagination_container'))
            $('pagination_container').style.display = 'none';
        $('manage_events').innerHTML = '<div class="seaocore_content_loader"></div>';
        var url = en4.core.baseUrl + 'widget/index/mod/siteevent/name/manage-events-siteevent';
        var request = new Request.HTML({
            url: url,
            data: {
                format: 'html',
                subject: en4.core.subject.guid,
                isajax: true,
                pagination: 0,
                rsvp: rsvp,
                viewType: viewType,
                page: 1,
                dateTimeDisplayed: '<?php echo $this->dateTimeDisplayed;?>'
            },
            evalScripts: true,
            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                $('siteevent_manage_event').innerHTML = responseHTML;
                Smoothbox.bind($('siteevent_manage_event'))
                en4.core.runonce.trigger();
            }
        });
        request.send();
    }

    en4.core.runonce.add(function() {
        <?php if ($this->paginator->count() > 1 && $this->paginator->count() > $this->page_id): ?>
            if ($('list_viewmore')) {
                window.onscroll = doOnScrollLoadActivity;
                $('list_viewmore').style.display = '';
                //$('feed_viewmore').style.display = 'none';
                $('list_loading').style.display = 'none';

                $('list_viewmore_link').removeEvents('click').addEvent('click', function(event) {
                    event.stop();
                    paginateMyEvents(<?php echo $this->page_id++; ?>);
                });
            }

        <?php else: ?>
            window.onscroll = '';
            <?php if ($this->page_id > 1) : ?>
                $('list_noviewmore').style.display = 'block';
                $('list_loading').style.display = 'none';
                $('list_viewmore').style.display = 'none';
            <?php endif; ?>
        <?php endif; ?>
    });

    var doOnScrollLoadActivity = function()
    {
        if ($('list_viewmore')) {
            if (typeof($('list_viewmore').offsetParent) != 'undefined') {
                var elementPostionY = $('list_viewmore').offsetTop;
            } else {
                var elementPostionY = $('list_viewmore').y;
            }
            if (elementPostionY <= window.getScrollTop() + (window.getSize().y - 40)) {
                paginateMyEvents(<?php echo $this->page_id++; ?>);
            }
        }
    }

    var paginateMyEvents = function(page) {
        var url = en4.core.baseUrl + 'widget/index/mod/siteevent/name/manage-events-siteevent';
        $('list_viewmore').style.display = 'none';
        $('list_loading').style.display = 'block';
        en4.core.request.send(new Request.HTML({
            'url': url,
            'data': {
                'format': 'html',
                'subject': en4.core.subject.guid,
                'page': page,
                'isajax': 1,
                pagination: true,
                viewType: viewType,
                rsvp: rsvp,
                dateTimeDisplayed: '<?php echo $this->dateTimeDisplayed;?>'

            },
            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {

                Elements.from(responseHTML).inject($('siteevent_browse_list'), 'bottom');
                Smoothbox.bind($('siteevent_manage_event'))
                en4.core.runonce.trigger();
            }
        }));
    }
    
  function submitSession(id){
    
    document.getElementById("event_id_session").value=id;
    document.getElementById("setSession_form").submit();
  }
</script>