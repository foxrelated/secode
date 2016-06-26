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

<?php if(!$this->ajaxPaging){ ?>
<div class="heevent-block">
  <h3 class="heevent-widget"><?php echo $this->translate('HEEVENT_Most Popular Events') ?></h3>
  <h4 class="heepepc heepepc-prev" style="display: none;" ><a data-sgn="-1" href="<?php echo $this->url(array('module' => 'core', 'controller' => 'widget', 'action' => 'index', 'mod' => 'heevent', 'name' => 'list-popular-events', 'format' => 'html'), 'default') ?>"><i class="hei hei-double-angle-up"></i></a></h4>
  <ul class="heevent-widget-popular" data-index="1">
    <?php } ?>
    <?php foreach ($this->paginator as $event): ?>

<?php
      $eventPaymantCheck = $this->eventPaymantCheck->getEventTicketCount($event['event_id']);

      if($this->count_tickets)
      $count_ticket =  $this->count_tickets->getEventCardsCount($event['event_id'])->count;
      $count_of = $eventPaymantCheck->ticket_count;
      if($this->eventPrices)
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
      $this->restrictions=$restrictions;
      $this->free= $free;
      $this->eventPrice= $eventPrice;
      $this->count_ticket= $count_ticket;

      }
      ?>
    <?php $type = 'event'; ?>
    <?php if(isset($this->unite) && $this->unite){ ?>
      <?php $type = $event['type']; ?>
      <?php if ($event['type'] == 'event') : ?>
        <?php $event = Engine_Api::_()->getItem('event', $event['event_id']); ?>
      <?php else: ?>
        <?php $event = Engine_Api::_()->getItem('pageevent', $event['event_id']) ?>
      <?php endif; ?>
    <?php } ?>
    <li class="ewidget_item">
      <div class="events_photo" style="position: relative;">
        <a href="<?php echo $event->getHref() ?>">
          <?php
          $eventPhotoUrl = $event->getPhotoUrl();
          if (!$eventPhotoUrl)
            $eventPhotoUrl = $this->layout()->staticBaseUrl . "application/modules/Heevent/externals/images/event-list-nophoto.gif";
          $owner = $event->getOwner();
          $viewer = Engine_Api::_()->user()->getViewer();
          ?>
          <img class="fake-img"
               src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Heevent/externals/images/fake-4x3.gif"
               alt="" style="background-image: url(<?php echo $eventPhotoUrl?>)">
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
          <?php echo $this->htmlLink($event->getHref(), $event->getTitle()) ?>
        </div>
        <div class="events_details heevents_details">
          <div><i class="hei hei-time"></i><?php echo $this->locale()->toDateTime($event->starttime) ?></div>
          <?php if ($event->location) { ?>
          <div class="event-location"><i
            class="hei hei-map-marker"></i><?php echo $this->htmlLink('http://maps.google.com/?q=' . urlencode($event->location), $event->location, array('target' => 'blank')) ?>
          </div>
          <?php } ?>
          <?php

          if($count_ticket>0){
            $g =  $count_ticket;
          }else{
            $g =  $event->membership()->getMemberCount(true, Array('rsvp' => 2));
          }

          ?>
          <div>
            <i class="hei hei-user"></i>
            <?php

            ?>
            <span guest-count="<?php echo $g; ?>"  id="guests_<?php if(!$this->of) echo $event->getGuid(); if($this->eventPrice<0 && $restrictions>0) echo $event->getGuid(); ?>">


              <?php
              echo $this->translate(array('%s guest', '%s guests', $g), $this->locale()->toNumber($g));
              ?>

                </span>
          </div>
          <?php if ($type == 'page') { ?>
          <div><i
            class="hei hei-file-text"></i><span><?php echo $this->translate('on page ');echo $this->htmlLink($event->getPage()->getHref(), $event->getPage()->getTitle());?></span>
          </div>
          <?php } ?>
        </div>
      </div>
    </li>
    <?php endforeach; ?>
<?php if(!$this->ajaxPaging){ ?>
  </ul>
  <?php if($this->paginator->getTotalItemCount() > 2){ ?>
  <h4 class="heepepc heepepc-next"><a data-sgn="1" href="<?php echo $this->url(array('module' => 'core', 'controller' => 'widget', 'action' => 'index', 'mod' => 'heevent', 'name' => 'list-popular-events', 'format' => 'html'), 'default') ?>"><i class="hei hei-double-angle-down"></i></a></h4>
  <?php } ?>
</div>
<script type="text/javascript">
  window.addEvent('domready', function () {
    var prev = $$('.heepepc-prev a')[0];
    var next = $$('.heepepc-next a')[0];
    var ul = $$('.heevent-widget-popular')[0];
    var updatePageIndexes = function(index){
      if(index < 1) return;
      var pageCount = <?php echo $this->pageCount ?>;
      if(index < 2){
        prev.getParent().hide();
      } else if(index > 1) {
        prev.getParent().show();
      }
      if(pageCount == index){
        next.getParent().hide();
      } else if(1 == (pageCount - index)){
        next.getParent().show();
      }


    };
    $$('.heepepc a').addEvent('click', function(e){
      e.preventDefault();
      var self = this;
      var url = this.href;
      var page = parseInt(ul.get('data-index'));
      var sgn = parseInt(this.get('data-sgn'));
      page += sgn;
      updatePageIndexes(page);
      ul.setStyle('opacity', .4);
      ul.setStyle('filter', 'alpha(opacity=40)');
      new Request.HTML({
        url: url,
        method:'get',
        data: {
          page: page
        },
        onSuccess: function(a, b, c, d){
          ul.set('data-index', page);
          var el = new Element('div');
          el.innerHTML = c;
          ul.innerHTML = el.innerHTML;
          el.destroy();
          ul.setStyle('opacity', 1);
          ul.setStyle('filter', 'alpha(opacity=100)');
        }
      }).send();
      return false;
    });

  });
</script>
<style type="text/css">
  h4.heepepc a{
    display: block;
    text-align: center
  }
</style>
<?php } ?>
