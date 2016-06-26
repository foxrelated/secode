<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: browse.tpl 19.10.13 08:20 jungar $
 * @author     Jungar
 */
?>
<?php
$this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Heevent/externals/scripts/manager.js');
$this->headTranslate(array('%s guest'));
/*echo $this->heevent()->getTicketForm();*/
?>
<?php if (count($this->paginator) > 0): ?>
    <ul class='heevents_browse events_browse'>
    <?php foreach ($this->paginator as $event): ?>

        <?php

        $eventPaymantCheck = $this->eventPaymantCheck->getEventTicketCount($event['event_id']);

        if ($this->count_tickets)
            $count_ticket = $this->count_tickets->getEventCardsCount($event['event_id'])->count;
        $count_of = $eventPaymantCheck->ticket_count;
        if ($this->eventPrices)
            $eventPrice = $this->eventPrices->getEventTickets($event['event_id'])->ticket_price;

        $of = false;
        if ($count_of && is_numeric($count_of)) {
            $of = true;
            if ($count_of == -1) {
                $restrictions = false;
            } else {
                $restrictions = $count_of;
            }

            if ($eventPrice == -1) {
                $free = false;
            } else {
                $free = $eventPrice;
            }
        }

        $this->of = $of;
        if ($of) {
            $this->restrictions = $restrictions;
            $this->free = $free;
            $this->eventPrice = $eventPrice;
            $this->count_ticket = $count_ticket;

        }

        ?>



        <?php $type = 'event'; ?>
        <?php if (isset($this->unite) && $this->unite) { ?>
            <?php $type = $event['type']; ?>
            <?php if ($event['type'] == 'event') : ?>
                <?php $event = Engine_Api::_()->getItem('event', $event['event_id']); ?>
            <?php else: ?>
                <?php $event = Engine_Api::_()->getItem('pageevent', $event['event_id']) ?>
            <?php endif; ?>
        <?php } ?>
        <li class="heevent-block">
        <img class="fake-img"
             src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Heevent/externals/images/fake-2x3.gif">

        <div class="events-item-wrapper">
        <div class="events_photo">
            <!--            --><?php //if($event->authorization()->isAllowed(null, 'photo')){ ?>
            <!--              <button class="add-photos heevent-abs-btn" href="-->
            <?php //echo $this->url(array('controller' => 'photo', 'action' => 'upload','subject' => $event->getGuid(), 'format' => 'smoothbox'), 'event_extended') ?><!--">-->
            <?php //echo $this->translate('HEEVENT_Add photos'); ?><!--</button>-->
            <!--            --><?php //} ?>
            <button class="share heevent-abs-btn"
                    onclick="Smoothbox.open('<?php echo $this->url(array('module' => 'activity', 'controller' => 'index', 'action' => 'share', 'type' => $event->getType(), 'id' => $event->getIdentity(), 'format' => 'smoothbox'), 'default', true) ?>')"><?php echo $this->translate('Share'); ?></button>
            <a href="<?php echo $event->getHref() ?>">
                <?php

                $eventPhotoUrl = $event->getPhotoUrl('thumb.pin');
                if (!$eventPhotoUrl)
                    $eventPhotoUrl = $this->layout()->staticBaseUrl . "application/modules/Heevent/externals/images/event-list-nophoto.gif";
                $owner = $event->getOwner();
                $viewer = Engine_Api::_()->user()->getViewer();
                ?>
                <img class="fake-img"
                     src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Heevent/externals/images/fake-4x3.gif"
                     alt="" style="background-image: url(<?php echo $eventPhotoUrl ?>)">
            </a>

            <div class="events_author">
                <a class="owner_icon wall_liketips wp_init" href="<?php echo $owner->getHref() ?>"
                   title="<?php echo $owner->getTitle() ?>"
                   style="background-image: url(<?php echo $owner->getPhotoUrl('thumb.normal') ? $owner->getPhotoUrl('thumb.normal') : 'application/modules/User/externals/images/nophoto_user_thumb_icon.png' ?>)"></a>
                <a class="owner_name" href="<?php echo $owner->getHref() ?>"><?php echo $owner->getTitle() ?></a>
            </div>
        </div>
        <div class="events_info">
        <div class="events_title">
            <h3><?php echo $this->htmlLink($event->getHref(), $event->getTitle()) ?></h3>
        </div>
        <div class="events_details heevents_details">
            <div><i class="hei hei-clock-o"></i> <?php echo $this->locale()->toDateTime($event->starttime) ?></div>
            <?php if ($event->location) { ?>
                <div>
                    <i class="hei hei-map-marker"></i> <?php echo ' ';?><?php echo $this->htmlLink('http://maps.google.com/?q=' . urlencode($event->location), $event->location, array('target' => 'blank')) ?>
                </div>
            <?php } ?>

            <?php

            if ($count_ticket > 0) {
                $g = $count_ticket;
            } else {
                $g = $event->membership()->getMemberCount(true, Array('rsvp' => 2));
            }

            ?>
            <div>
                <i class="hei hei-user"></i>
                <?php

                ?>
                <span guest-count="<?php echo $g; ?>" id="guests_<?php if (!$this->of) echo $event->getGuid();
                if ($this->eventPrice < 0 && $restrictions > 0) echo $event->getGuid(); ?>">


              <?php
              echo $this->translate(array('%s guest', '%s guests', $g), @$this->locale()->toNumber($g));
              ?>

                </span>
            </div>
            <?php if ($type == 'page') { ?>
                <div><i class="hei hei-file-text"></i><span><?php echo $this->translate('on page ');
                        echo $this->htmlLink($event->getPage()->getHref(), $event->getPage()->getTitle()); ?></span>
                </div>
            <?php } ?>
        </div>
        <?php
        if ($type == 'event') {
            $member_count_free = $event->membership()->getMemberCount(true, Array('rsvp' => 2));
            if ($this->of && $type == 'event') {
                if ($this->eventPrice > 0) {

                    ?>
                    <div id="heevent-buy-form<?php echo $event->getIdentity() ?>"
                         class="heevent-buy-form  heevent-form global_form">

                        <?php
                        echo $this->heevent()->getTicketForm($event);
                        ?>
                    </div>
                    <div id="background_buy_form" onclick="hideBuy_form('<?php echo $event->getIdentity() ?>');"
                         class="background_buy_form<?php echo $event->getIdentity() ?>"></div>


                    <div class="events_details heevents_details">
                        <div>
                            <i class="hei hei-tag"></i>
                            <?php echo $this->translate('Price') . ' ';
                            echo $event->getCurentPriceList($this->eventPrice) ?>
                        </div>
                    </div>

                <?php
                }

                if ($this->restrictions && $this->eventPrice > 0) {

                    ?>
                    <div class="events_details heevents_details">
                        <div><i class="hei hei-ticket"></i>  <?php echo $this->translate('Available') . ' ';
                            echo $count = $this->restrictions - $this->count_ticket ?>
                            of <?php echo $this->restrictions ?></div>
                    </div>
                <?php
                } else {
                    ?>
                    <div class="events_details heevents_details">
                        <div><span guest-count="<?php echo $this->restrictions - $member_count_free; ?>"
                                   id="2guests_<?php if (!$this->of) echo $event->getGuid();
                                   if ($this->eventPrice < 0 && $this->restrictions > 0) echo $event->getGuid() ?>"><?php echo $this->translate('Available') . ' ';
                                echo $count = $this->restrictions - $member_count_free ?></span>
                            of <?php echo $this->restrictions ?></div>
                    </div>
                <?php
                }
            }
        }
        ?>
        <?php
        if ($viewer->getIdentity()) {
        $row = $event->membership()->getRow($viewer);
        $rsvp = -1;
        ?>

        <div event-id="<?php echo $event->getIdentity() ?>" event-guid="<?php echo $event->getGuid() ?>"
             class="<?php echo ($row && $row->active) ? 'member' : ''; ?> events_action button-animate">
            <?php
            if ($this->of && $type == 'event') {
            if (null === $row) {
                if ($event->membership()->isResourceApprovalRequired()) {
//                 Render Request Invite Button
                    ?>
                    <button value="invite" toggle-text="<?php echo $this->translate('HEEVENT_Request Sent') ?>"
                            name="rsvp" class="invite_btn"
                            href="<?php echo $this->url(array('controller' => 'member', 'action' => 'request', 'event_id' => $event->getIdentity()), 'event_extended') ?>"><?php echo $this->translate('HEEVENT_Request Invite') ?></button>
                    <button value="cancel" name="rsvp" disabled="disabled" class="disabled invite_btn"
                            href="<?php echo $this->url(array('controller' => 'member', 'action' => 'cancel', 'event_id' => $event->getIdentity()), 'event_extended') ?>"><?php echo $this->translate('HEEVENT_Cancel Request') ?></button>
                <?php
                } else {
//                 Joining Event
                    if ($count > 0) {
                        ?>
                        <button value="join" name="rsvp"
                                class="join_btn"><?php echo $this->filter == 'past' ? $this->translate('HEEVENT_Did you go?') : $this->translate('HEEVENT_Are you going?') ?></button>

                        <button class="yes ticket_btn" value="2" data-id="<?php echo $event->getIdentity(); ?>"
                                name="rsvp"
                                href="<?php echo $this->url(array('controller' => 'member', 'action' => 'hejoin', 'event_id' => $event->getIdentity()), 'event_extended') ?>"><?php echo $this->translate('Yes') ?></button>
                        <button class="no rsvp_btn" value="0" name="rsvp"
                                href="<?php echo $this->url(array('controller' => 'member', 'action' => 'join', 'event_id' => $event->getIdentity()), 'event_extended') ?>"><?php echo $this->translate('No') ?></button>
                    <?php
                    } else {
                        echo $this->translate('Tickets is empty');
                    }?>

                <?php

                }
            } else if ($row->active) {
//                 Change RSVP
                $rsvp = 0;
                if ($count > 0) {
                    ?>
                    <div><?php echo $event->isPast() ? $this->translate('HEEVENT_Did you go?') : $this->translate('HEEVENT_Are you going?') ?></div>

                    <button data-id="<?php echo $event->getIdentity(); ?>"
                            class="<?php if ($rsvp == 2) { ?>active disabled <?php } ?>yes <?php if ($this->eventPrice > 0) { ?>ticket_btn<?php } else { ?>rsvp_btn <?php } ?>"
                            value="2" name="rsvp"
                            href="<?php echo $this->url(array('module' => 'heevent', 'controller' => 'widget', 'action' => 'heprofile-rsvp', 'subject' => $event->getGuid()), 'default', true); ?>"><?php echo $this->translate('Yes') ?></button>
                    <button class="<?php if ($rsvp == 0) { ?>active disabled <?php } ?>no rsvp_btn" value="0"
                            name="rsvp"
                            href="<?php echo $this->url(array('module' => 'heevent', 'controller' => 'widget', 'action' => 'profile-rsvp', 'subject' => $event->getGuid()), 'default', true); ?>"><?php echo $this->translate('No') ?></button>
                <?php
                } else {
                    echo $this->translate('Tickets is empty');
                }
                ?>

            <?php
            } else if (!$row->resource_approved && $row->user_approved) {
//                 Render Cancel Invite Request Button
                ?>
                <button value="invite" toggle-text="<?php echo $this->translate('HEEVENT_Request Invite') ?>"
                        disabled="disabled" name="rsvp" class="disabled invite_btn"
                        href="<?php echo $this->url(array('controller' => 'member', 'action' => 'request', 'event_id' => $event->getIdentity()), 'event_extended') ?>"><?php echo $this->translate('HEEVENT_Request Sent') ?></button>
                <button value="cancel" name="rsvp" class="invite_btn"
                        href="<?php echo $this->url(array('controller' => 'member', 'action' => 'cancel', 'event_id' => $event->getIdentity()), 'event_extended') ?>"><?php echo $this->translate('HEEVENT_Cancel Request') ?></button>
            <?php
            } else if (!$row->user_approved && $row->resource_approved) {
//                 Render Accept Event Invite Button
//                 Render Ignore Event Invite Button
                ?>
                <div><?php echo $this->translate('HEEVENT_You have been invited to join the event') ?></div>
                <button value="accept" name="rsvp" class="confirm_btn"
                        href="<?php echo $this->url(array('controller' => 'member', 'action' => 'accept', 'event_id' => $event->getIdentity()), 'event_extended') ?>"><?php echo $this->translate('HEEVENT_Accept') ?></button>
                <button value="reject" name="rsvp" class="confirm_btn"
                        href="<?php echo $this->url(array('controller' => 'member', 'action' => 'reject', 'event_id' => $event->getIdentity()), 'event_extended') ?>"><?php echo $this->translate('HEEVENT_Reject') ?></button>
                <?php if ($count > 0) { ?>

                    <button disabled="disabled" data-id="<?php echo $event->getIdentity(); ?>"
                            class="disabled yes <?php if ($this->eventPrice > 0) { ?>ticket_btn<?php } else { ?>rsvp_btn <?php } ?>"
                            value="2" name="rsvp"
                            href="<?php echo $this->url(array('module' => 'heevent', 'controller' => 'widget', 'action' => 'heprofile-rsvp', 'subject' => $event->getGuid()), 'default', true); ?>"><?php echo $this->translate('Yes') ?></button>
                    <button disabled="disabled" class="disabled no rsvp_btn" value="0" name="rsvp"
                            href="<?php echo $this->url(array('module' => 'heevent', 'controller' => 'widget', 'action' => 'profile-rsvp', 'subject' => $event->getGuid()), 'default', true); ?>"><?php echo $this->translate('No') ?></button>
                <?php
                } else {
                    echo $this->translate('Tickets is empty');
                }?>

            <?php
            }

            ?>
        </div>
        <?php


        } elseif ($this->restrictions <= $member_count_free && $this->of) {
            echo $this->translate('Tickets is empty');
        }
        else{

        if (null === $row) {
            if ($event->membership()->isResourceApprovalRequired()) {
//                 Render Request Invite Button
                ?>
                <button value="invite" toggle-text="<?php echo $this->translate('HEEVENT_Request Sent') ?>" name="rsvp"
                        class="invite_btn"
                        href="<?php echo $this->url(array('controller' => 'member', 'action' => 'request', 'event_id' => $event->getIdentity()), 'event_extended') ?>"><?php echo $this->translate('HEEVENT_Request Invite') ?></button>
                <button value="cancel" name="rsvp" disabled="disabled" class="disabled invite_btn"
                        href="<?php echo $this->url(array('controller' => 'member', 'action' => 'cancel', 'event_id' => $event->getIdentity()), 'event_extended') ?>"><?php echo $this->translate('HEEVENT_Cancel Request') ?></button>
            <?php
            } else {
//                 Joining Event
                ?>
                <button value="join" name="rsvp"
                        class="join_btn"><?php echo $this->filter == 'past' ? $this->translate('HEEVENT_Did you go?') : $this->translate('HEEVENT_Are you going?') ?></button>
                <button class="yes rsvp_btn" value="2" name="rsvp"
                        href="<?php echo $this->url(array('controller' => 'member', 'action' => 'join', 'event_id' => $event->getIdentity()), 'event_extended') ?>"><?php echo $this->translate('Yes') ?></button>
                <button class="maybe rsvp_btn" value="1" name="rsvp"
                        href="<?php echo $this->url(array('controller' => 'member', 'action' => 'join', 'event_id' => $event->getIdentity()), 'event_extended') ?>"><?php echo $this->translate('HEEVENT_Maybe') ?></button>
                <button class="no rsvp_btn" value="0" name="rsvp"
                        href="<?php echo $this->url(array('controller' => 'member', 'action' => 'join', 'event_id' => $event->getIdentity()), 'event_extended') ?>"><?php echo $this->translate('No') ?></button>
            <?php
            }
        } else if ($row->active) {
//                 Change RSVP
            $rsvp = $row->rsvp;
            ?>
            <div><?php echo $this->filter == 'past' ? $this->translate('HEEVENT_Did you go?') : $this->translate('HEEVENT_Are you going?') ?></div>
            <button class="<?php if ($rsvp == 2) { ?>active disabled <?php } ?>yes rsvp_btn" value="2" name="rsvp"
                    href="<?php echo $this->url(array('module' => 'heevent', 'controller' => 'widget', 'action' => 'profile-rsvp', 'subject' => $event->getGuid()), 'default', true); ?>"><?php echo $this->translate('Yes') ?></button>
            <button class="<?php if ($rsvp == 1) { ?>active disabled <?php } ?>maybe rsvp_btn" value="1" name="rsvp"
                    href="<?php echo $this->url(array('module' => 'heevent', 'controller' => 'widget', 'action' => 'profile-rsvp', 'subject' => $event->getGuid()), 'default', true); ?>"><?php echo $this->translate('HEEVENT_Maybe') ?></button>
            <button class="<?php if ($rsvp == 0) { ?>active disabled <?php } ?>no rsvp_btn" value="0" name="rsvp"
                    href="<?php echo $this->url(array('module' => 'heevent', 'controller' => 'widget', 'action' => 'profile-rsvp', 'subject' => $event->getGuid()), 'default', true); ?>"><?php echo $this->translate('No') ?></button>
        <?php
        } else if (!$row->resource_approved && $row->user_approved) {
//                 Render Cancel Invite Request Button
            ?>
            <button value="invite" toggle-text="<?php echo $this->translate('HEEVENT_Request Invite') ?>"
                    disabled="disabled" name="rsvp" class="disabled invite_btn"
                    href="<?php echo $this->url(array('controller' => 'member', 'action' => 'request', 'event_id' => $event->getIdentity()), 'event_extended') ?>"><?php echo $this->translate('HEEVENT_Request Sent') ?></button>
            <button value="cancel" name="rsvp" class="invite_btn"
                    href="<?php echo $this->url(array('controller' => 'member', 'action' => 'cancel', 'event_id' => $event->getIdentity()), 'event_extended') ?>"><?php echo $this->translate('HEEVENT_Cancel Request') ?></button>
        <?php
        } else if (!$row->user_approved && $row->resource_approved) {
//                 Render Accept Event Invite Button
//                 Render Ignore Event Invite Button
            ?>
            <div><?php echo $this->translate('HEEVENT_You have been invited to join the event') ?></div>
            <button value="accept" name="rsvp" class="confirm_btn"
                    href="<?php echo $this->url(array('controller' => 'member', 'action' => 'accept', 'event_id' => $event->getIdentity()), 'event_extended') ?>"><?php echo $this->translate('HEEVENT_Accept') ?></button>
            <button value="reject" name="rsvp" class="confirm_btn"
                    href="<?php echo $this->url(array('controller' => 'member', 'action' => 'reject', 'event_id' => $event->getIdentity()), 'event_extended') ?>"><?php echo $this->translate('HEEVENT_Reject') ?></button>
            <button disabled="disabled" class="disabled yes rsvp_btn" value="2" name="rsvp"
                    href="<?php echo $this->url(array('module' => 'heevent', 'controller' => 'widget', 'action' => 'profile-rsvp', 'subject' => $event->getGuid()), 'default', true); ?>"><?php echo $this->translate('Yes') ?></button>
            <button disabled="disabled" class="disabled maybe rsvp_btn" value="1" name="rsvp"
                    href="<?php echo $this->url(array('module' => 'heevent', 'controller' => 'widget', 'action' => 'profile-rsvp', 'subject' => $event->getGuid()), 'default', true); ?>"><?php echo $this->translate('HEEVENT_Maybe') ?></button>
            <button disabled="disabled" class="disabled no rsvp_btn" value="0" name="rsvp"
                    href="<?php echo $this->url(array('module' => 'heevent', 'controller' => 'widget', 'action' => 'profile-rsvp', 'subject' => $event->getGuid()), 'default', true); ?>"><?php echo $this->translate('No') ?></button>
        <?php
        }

        ?>
        </div>
        <?php
        }
        }
        ?>
        </div>
        </div>
        </li>
    <?php endforeach; ?>
    </ul>

    <?php if ($this->paginator->count() > 1): ?>
        <?php echo $this->paginationControl($this->paginator, null, null, array(
            'query' => $this->formValues,
        )); ?>
    <?php endif; ?>


<?php elseif (preg_match("/category_id=/", $_SERVER['REQUEST_URI'])): ?>
    <div class="tip">
    <span>
    <?php echo $this->translate('Nobody has created an event with that criteria.'); ?>
    <?php if ($this->canCreate): ?>
        <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="' . $this->url(array('action' => 'create'), 'event_general') . '">', '</a>'); ?>
    <?php endif; ?>
    </span>
    </div>


<?php
else: ?>
    <div class="tip">
    <span>
    <?php if ($this->filter != "past"): ?>
        <?php echo $this->translate('Nobody has created an event yet.') ?>
        <?php if ($this->canCreate): ?>
            <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="' . $this->url(array('action' => 'create'), 'event_general') . '">', '</a>'); ?>
        <?php endif; ?>
    <?php else: ?>
        <?php echo $this->translate('There are no past events yet.') ?>
    <?php endif; ?>
    </span>
    </div>

<?php endif; ?>
<script type="text/javascript">
    _hem.ajaxPagination($$('.paginationControl'), $('global_content').getElement('.layout_core_content'));
    <?php if($this->format == 'html'){ ?>
    _hem.initActionsOn($(document.body));
    <?php } ?>
</script>



