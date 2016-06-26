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
$host = (isset($_SERVER['HTTPS']) ? "https" : "http");
$host_url = $host.'://'.str_ireplace('heevents','',$_SERVER['HTTP_HOST'].Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'heevent_extended')).'admin/heevent/index/ticketsview?id=';

?>
<div class="my_events_navigation">
  <ul>
    <li><a class="menu_event_main upcoming_button <?php if($this->active_upcoming==1){echo 'active';}?>  wp_init" href="<?php echo $this->url(array('action'=>'tickets','type'=> 'upcoming'), 'heevent_general', true);?>">Upcoming</a></li>
    <li><a class="menu_event_main past_button <?php if($this->active_past==1){echo 'active';}?> wp_init" href="<?php echo $this->url(array('action'=>'tickets','type'=> 'past'), 'heevent_general', true);?>">Past</a></li>
  </ul>
</div>
<?php if( count($this->paginator) > 0 ): ?>
  <ul class='heevents_browse events_browse'>
    <?php foreach( $this->paginator as $card ): ?>
      <?php $type = 'event'; ?>
          <?php $event = Engine_Api::_()->getItem('event', $card['event_id']); ?>
      <li class="heevent-block card_<?php echo $card['card_id']?>" style="width: 98%;margin-top: 5px;min-height: 214px" id="">
        <img style="height:214px" class="fake-img" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Heevent/externals/images/fake-2x3.gif">
        <div class="events-item-wrapper">
          <div class="events_photo" style="height: 214px;    position: relative;    width: 33.3%;">
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
          <div class="heevents_info">
            <div class="heevents_title">
              <h3><?php echo $this->htmlLink($event->getHref(), $event->getTitle()) ?></h3>
              <?php if($card['card_id']>0){?>
                <a href="<?php echo $this->url(array('id'=>$card['card_id'] ), 'heevent_print', true);?>" style="background-image: url('./application/modules/Heevent/externals/images/print_event.png'); width: 100px; float: left;margin: 10px 10px 0 0;" class="buttonlink"  align="right"><?php echo $this->translate('Print Ticket') ?></a>
              <?php }?>
            </div>
            <div class="clr"></div>
            <div class="events_details heevents_details" style=" float: left;    width: 300px;">
              <div><i class="hei hei-time"></i><?php echo $this->locale()->toDateTime($event->starttime) ?></div>
              <?php if($event->location) {?>
                <div class="event-location"><i class="hei hei-map-marker"></i><?php echo $this->htmlLink('http://maps.google.com/?q='.urlencode($event->location), $event->location, array('target' => 'blank')) ?></div>
              <?php } ?>
              <div><i class="hei hei-user"></i><span guest-count="<?php echo $event->membership()->getMemberCount(); ?>" id="guests_<?php echo $event->getGuid(); ?>"><?php echo $this->translate(array('%s guest', '%s guests', $event->membership()->getMemberCount()), @$this->locale()->toNumber($event->membership()->getMemberCount())) ?></span></div>
              <?php if($type == 'page'){ ?>
                <div><i class="hei hei-file-text"></i><span><?php echo $this->translate('on page ');echo $this->htmlLink($event->getPage()->getHref(), $event->getPage()->getTitle());?></span></div>
              <?php } ?>

            </div>

            <div class="event_ticket_code">
               <?php echo $card['ticked_code']?>
            </div>

          </div>
          <div style="float: right">
            <img src="https://chart.googleapis.com/chart?chs=220x220&cht=qr&chl=<?php echo $host_url.$card['card_id'] ?>&choe=UTF-8" title="<?php echo $event->getTitle(); ?>" />
          </div>

        </div>
        <div class="card-used-border-1" style="<?php if($card['used']!=1){?>display:none;<?php }?>">
          <div class="card-used-border-2">
            <?php echo $this->translate('Used');?>
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
      <?php echo $this->translate('You have not buy any events yet.') ?>

  </span>
  </div>

<?php endif; ?>


<script type="text/javascript">
  _hem.ajaxPagination($$('.paginationControl'), $('global_content').getElement('.layout_core_content'));
  <?php if($this->format == 'html'){ ?>
  _hem.initActionsOn($(document.body));
  <?php } ?>
</script>
