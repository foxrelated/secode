<?php
$host = (isset($_SERVER['HTTPS']) ? "https" : "http");
$host_url = $host.'://'.str_ireplace('heevents','',$_SERVER['HTTP_HOST'].Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'heevent_extended')).'admin/heevent/index/ticketsview?id=';


foreach( $this->paginator as $card ): ?>
  <?php $type = 'event'; ?>
  <?php $event = Engine_Api::_()->getItem('event', $card['event_id']);
  if(!$event){
    continue;
  }
  ?>

  <li class="heevent-block card_<?php echo $card['card_id']?>" style="width: 98%;margin-top: 5px;min-height: 50px" id="<?php echo $card['ticked_code']?>">

    <div class="events-item-wrapper">
      <div class="events_photo" style="height: 50px;    position: relative;    width: 30%;">

        <a href="<?php echo $event->getHref() ?>">
          <?php
          $eventPhotoUrl = $event->getPhotoUrl();
          $owner = Engine_Api::_()->user()->getUser($card['user_id']);
          $viewer = Engine_Api::_()->user()->getViewer();
          ?>

        </a>

        <div class="events_author" style="background: none;">
          <a class="owner_icon wall_liketips wp_init" href="<?php echo $owner->getHref() ?>"
             title="<?php echo $owner->getTitle() ?>"
             style="background-image: url(<?php echo $owner->getPhotoUrl('thumb.normal') ? $owner->getPhotoUrl('thumb.normal') : 'application/modules/User/externals/images/nophoto_user_thumb_icon.png' ?>)"></a>
          <a class="owner_name" style="color: #000;text-shadow: none;" href="<?php echo $owner->getHref()?>"><?php echo $owner->getTitle() ?></a>
        </div>
      </div>
      <div class="events_info" style="height: 100%;">
        <div class="event_ticket_code" style="top: 0px">
          <?php echo $card['ticked_code']?>
        </div>

      </div>
    </div>

    <div class="card-used-border-1" style="<?php if($card['used']!=1){?>display:none;<?php }?> left: 613px;top: 10px;transform: rotate(-5deg);">
      <div class="card-used-border-2" style="font-size: 14px">
        <?php echo $this->translate('Used');?>
      </div>
    </div>

  </li>

<?php endforeach; ?>