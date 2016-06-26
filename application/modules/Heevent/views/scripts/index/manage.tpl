<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manage.tpl 19.10.13 08:20 jungar $
 * @author     Jungar
 */
?>
<?php
$this->headScript()
  ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Heevent/externals/scripts/manager.js');
$this->headTranslate(array('%s guest'));
if($this->unite)
  $this->headScript()
      ->appendFile( $this->layout()->staticBaseUrl . 'application/modules/Pageevent/externals/scripts/Pageevent.js');
?>
<?php if( count($this->paginator) > 0 ): ?>
  <ul class='heevents_browse events_browse'>
    <?php
    ?>
    <?php foreach( $this->paginator as $event ): ?>

    <?php

      $type = 'event'; ?>
    <?php if(isset($this->unite) && $this->unite){ ?>
      <?php $type = $event['type']; ?>
        <?php if ($event['type'] == 'event') : ?>
          <?php $event = Engine_Api::_()->getItem('event', $event['event_id']); ?>
        <?php else: ?>

          <?php $event = Engine_Api::_()->getItem('pageevent', $event['event_id']) ?>
        <?php endif; ?>
      <?php } ?>
      <li class="heevent-block">
        <img class="fake-img" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Heevent/externals/images/fake-2x3.gif">
        <div class="events-item-wrapper">
          <div class="events_photo">
            <?php if($event->authorization()->isAllowed(null, 'photo')){ ?>
              <button class="heevent-hover-fadein heevent-abs-btn" onclick="Smoothbox.open('<?php echo $this->url(array('controller' => 'photo', 'action' => 'upload','subject' => $event->getGuid(), 'format' => 'smoothbox'), 'event_extended') ?>', '_blank')"><?php echo $this->translate('HEEVENT_Add photos'); ?></button>
            <?php } ?>
            <button class="share heevent-abs-btn" onclick="Smoothbox.open('<?php echo $this->url(array('module' => 'activity','controller' => 'index','action' => 'share','type' => $event->getType(),'id' => $event->getIdentity(),'format' => 'smoothbox'), 'default', true) ?>')"><?php echo $this->translate('Share'); ?></button>
            <a href="<?php echo $event->getHref() ?>">
              <?php
                $eventPhotoUrl = $event->getPhotoUrl('thumb.pin');
                if(!$eventPhotoUrl)
                  $eventPhotoUrl = $this->layout()->staticBaseUrl ."application/modules/Heevent/externals/images/event-list-nophoto.gif";
                $owner = $event->getOwner();
              $viewer = Engine_Api::_()->user()->getViewer();
              ?>
            <img class="fake-img" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Heevent/externals/images/fake-4x3.gif" alt="" style="background-image: url(<?php echo $eventPhotoUrl?>)">
            </a>

            <div class="events_author">
              <a class="owner_icon wall_liketips wp_init" href="<?php echo $owner->getHref() ?>"
                 title="<?php echo $owner->getTitle() ?>"
                 style="background-image: url(<?php echo $owner->getPhotoUrl('thumb.normal') ? $owner->getPhotoUrl('thumb.normal') : 'application/modules/User/externals/images/nophoto_user_thumb_icon.png' ?>)"></a>
              <a class="owner_name" href="<?php echo $owner->getHref()?>"><?php echo $owner->getTitle() ?></a>
            </div>
          </div>
          <div class="events_info">
            <div class="events_title">
              <h3><?php echo $this->htmlLink($event->getHref(), $event->getTitle()) ?></h3>
            </div>
            <div class="events_details heevents_details">
              <div><i class="hei hei-time"></i><?php echo $this->locale()->toDateTime($event->starttime) ?></div>
              <?php if($event->location) {?>
              <div class="event-location"><i class="hei hei-map-marker"></i><?php echo $this->htmlLink('http://maps.google.com/?q='.urlencode($event->location), $event->location, array('target' => 'blank')) ?></div>
                <?php } ?>
              <div><i class="hei hei-user"></i><span guest-count="<?php echo $event->membership()->getMemberCount(); ?>" id="guests_<?php echo $event->getGuid(); ?>"><?php echo $this->translate(array('%s guest', '%s guests', $event->membership()->getMemberCount()), @$this->locale()->toNumber($event->membership()->getMemberCount())) ?></span></div>
              <?php if($type == 'page'){ ?>
              <div><i class="hei hei-file-text"></i><span><?php echo $this->translate('on page ');echo $this->htmlLink($event->getPage()->getHref(), $event->getPage()->getTitle());?></span></div>
              <?php } ?>
            </div>
            <div class="heevents-options events_action button-animate">
              <?php
              $editHref = $this->url(array('action' => 'edit', 'event_id' => $event->getIdentity()), 'event_specific');
              $deleteHref = $this->url(array('module' => 'event', 'controller' => 'event', 'action' => 'delete', 'event_id' => $event->getIdentity(), 'format' => 'html'), 'default', true);
              $inviteClick = 'Smoothbox.open(\'' . $this->url(array('controller'=>'member', 'action' => 'invite', 'event_id' => $event->getIdentity(), 'format' => 'smoothbox'), 'event_extended', true) .'\')';
              $joinHref = $this->url(array('controller'=>'member', 'action' => 'join', 'event_id' => $event->getIdentity(), 'format' => 'smoothbox'), 'event_extended', true);
              $leaveClick = 'Smoothbox.open(\''. $this->url(array('controller'=>'member', 'action' => 'leave', 'event_id' => $event->getIdentity(), 'format' => 'smoothbox'), 'event_extended', true). '\')';

              if($type == 'page'){
                $editHref = $event->getHref();
                $deleteHref = $this->url(array('action' => 'remove', 'id' => $event->getIdentity(), 'page_id' => $event->getPage()->getIdentity(), 'format' => 'json'), 'page_event');
                $inviteClick = 'Pageevent.invite('.$event->getPage()->getIdentity().');';
                $joinHref = $this->url(array('action' => 'rsvp','id' => $event->getIdentity(), 'rsvp' => 2, 'format' => 'json'), 'page_event');
                $leaveClick = 'window.open(\''. $event->getHref() .'\', \'_blank\')';
                $leaveHref = $this->url(array('action' => 'rsvp','id' => $event->getIdentity(), 'rsvp' => 0, 'format' => 'json'), 'page_event');
              }
              ?>
              <?php if( $this->viewer()->getIdentity() && ($event->isOwner($this->viewer()) || $this->viewer()->level_id < 4)):?>
              <button title="<?php echo $this->translate('Edit Event') ?>"  value="edit" name="option" class="edit option_btn" onclick="window.open('<?php echo $editHref ?>', '_blank');"><i class="hei hei-edit"></i></button>
              <button confirm="<?php echo $this->translate('Are you sure you want to delete this event?') ?>" title="<?php echo $this->translate('Delete Event') ?>" value="delete" name="option" class="delete option_btn" href="<?php echo $deleteHref ?>"><i class="hei hei-times"></i></button>
              <?php endif; ?>
              <?php if($event->authorization()->isAllowed($viewer, 'invite')){ ?>
              <button title="<?php echo $this->translate('Invite Guests') ?>"  value="leave" name="option" class="leave option_btn" onclick="<?php echo $inviteClick ?>"><?php echo $this->translate('Invite') ?> <i class="hei hei-arrow-down"></i></button>
              <?php } ?>
              <?php if( $this->viewer() && !$event->membership()->isMember($this->viewer(), null) ): ?>
              <button title="<?php echo $this->translate('Join Event') ?>"  value="join" name="option" class="join option_btn" href="<?php echo $joinHref ?>"><i class="hei hei-plus"></i> <?php echo $this->translate('HEEVENT_Join') ?></button>
              <?php elseif( $this->viewer() && $event->membership()->isMember($this->viewer()) && !$event->isOwner($this->viewer())): ?>
              <button title="<?php echo $this->translate('Leave Event') ?>"  href="<?php echo $leaveHref ?>" value="leave" name="option" class="leave option_btn" onclick="<?php echo $leaveClick ?>"><i class="hei hei-reply"></i> <?php echo $this->translate('HEEVENT_Leave') ?></button>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>

  <?php if( $this->paginator->count() > 1 ): ?>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
      'query' => $this->formValues,
    )); ?>
  <?php endif; ?>


<?php else: ?>
<div class="tip">
  <span>
      <?php echo $this->translate('You have not joined any events yet.') ?>
      <?php if( $this->canCreate): ?>
        <?php echo $this->translate('Why don\'t you %1$screate one%2$s?',
          '<a href="'.$this->url(array('action' => 'create'), 'event_general').'">', '</a>') ?>
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
