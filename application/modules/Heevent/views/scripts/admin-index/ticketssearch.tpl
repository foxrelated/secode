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
      <li class="heevent-block card_<?php echo $card['card_id'] ?>" style="" id="<?php echo $card['ticked_code'] ?>">
        <div class="events-item-wrapper">
          <div class="user_photo" style=" background-image: url(<?php echo $event->getPhotoUrl() ?>); ">
            <a href="<?php echo $event->getHref() ?>">
              <?php
              $eventPhotoUrl = $event->getPhotoUrl();
              $owner = Engine_Api::_()->user()->getUser($card['user_id']);
              $viewer = Engine_Api::_()->user()->getViewer();
              ?>

            </a>
            <div class="events_author">
              <a class="owner_icon wall_liketips wp_init" href="<?php echo $owner->getHref() ?>"
                 title="<?php echo $owner->getTitle() ?>"
                 style="background-image: url(<?php echo $owner->getPhotoUrl('thumb.normal') ? $owner->getPhotoUrl('thumb.normal') : 'application/modules/User/externals/images/nophoto_user_thumb_icon.png' ?>)"></a>
              <a class="owner_name" style="color: #fff;text-shadow: none;"
                 href="<?php echo $owner->getHref() ?>"><?php echo str_ireplace($this->search,"<b style='font-weight: bold;'>".$this->search."</b>",$owner->getTitle()) ?></a>
            </div>
            <div class="clr"></div>
            <div class="userTilel"><?php echo str_ireplace($this->search,"<b style='font-weight: bold;'>".$this->search."</b>",$event->getTitle())?></div>
          </div>
          <div style="float: left">
            <img src="https://chart.googleapis.com/chart?chs=100x100&cht=qr&chl=<?php echo $host_url.$card['card_id'] ?>&choe=UTF-8" title="<?php echo $event->getTitle(); ?>" />
          </div>
          <div class="ticket_status_button_container" id="ticket_status_button_container_<?php echo $card['card_id'];?>" style="display: inline-block;float: right;margin:30px;width: 150px; color:red;font-size: 20px?> ">
            <?php if($card['used']==1){?>
              <button id="button_change_status_<?php echo $card['card_id'];?>" onclick="status_chnage_ticket(<?php echo $card['card_id']?>,2)" class="btn card-used-button"> Make Unused</button>
            <?php
            }else{?>
              <button id="button_change_status_<?php echo $card['card_id'];?>" onclick="status_chnage_ticket(<?php echo $card['card_id']?>,1)" class="btn card-used-button">Used</button>
            <?php } ?>
          </div>
          <div class="events_info" style="" >
            <div class="event_ticket_code_admin" code="<?php echo $card['ticked_code'];?>">
              <?php
              $word = strtolower($this->search);
              echo str_replace($this->search,"<b style='font-weight: bold;color: #000;'>".$word."</b>",$card['ticked_code']); ?>
            </div>


          </div>
        </div>
        <div class="card-used-border-1" style="<?php if($card['used']!=1){?>display:none;<?php }?>">
          <div class="card-used-border-2">
            <?php echo $this->translate('Used');?>
          </div>
        </div>

          <script>
            <?php if($this->paginator->count()>1){?>

          $('page_status').set('page',2);
          $('page_status').set('count',<?php echo $this->paginator->count()?>);
            if($('view_more_button_heevent_admin')){
              $('view_more_button_heevent_admin').setStyle('display','block');
            }
            <?php }?>

          </script>

    </li>

    <?php endforeach; ?>

