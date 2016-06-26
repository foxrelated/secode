<style type='text/css'>
  .layout_page_footer {
    display: none;
  }
  .layout_page_header {
    display: none;
  }
  #global_content {
    margin-left: 0;
  }
  #im_container {
    display: none;
  }
  #store-cart-box {
    display: none;
  }
  #global_header_wrapper{
    display: none;
  }
  #global_content_wrapper{
    padding: 0 !important;
  }

  #global_content {
    margin-left: 0;
    width: 98% !important;
  }
</style>
<ul class='heevents_browse_admin events_browse' id="hevent_ticket_list">
    <?php foreach( $this->paginator as $card ): ?>
      <?php $type = 'event'; ?>
      <?php $event = Engine_Api::_()->getItem('event', $card['event_id']);
      if(!$event){
        continue;
      }
      ?>

      <li class="heevent-block card_<?php echo $card['card_id']?>"  id="<?php echo $card['ticked_code']?>" style="height: auto; width: 99%;">

        <div class="events-item-wrapper" style="position: relative">
          <div class="user_photo" style="background-image: url(<?php echo $event->getPhotoUrl()?>); height: 300px; width: 100%;">

            <a href="<?php echo $event->getHref() ?>">
              <?php
              $eventPhotoUrl = $event->getPhotoUrl();
              $owner = Engine_Api::_()->user()->getUser($card['user_id']);
              $viewer = Engine_Api::_()->user()->getViewer();
              ?>

            </a>

            <div class="events_author" >
              <a class="owner_icon wall_liketips wp_init" href="<?php echo $owner->getHref() ?>"
                 title="<?php echo $owner->getTitle() ?>"
                 style="background-image: url(<?php echo $owner->getPhotoUrl('thumb.normal') ? $owner->getPhotoUrl('thumb.normal') : 'application/modules/User/externals/images/nophoto_user_thumb_icon.png' ?>)"></a>
              <a class="owner_name" style="color: #fff;text-shadow: none; font-size: 30px" href="<?php echo $owner->getHref()?>">
                <?php echo $owner->getTitle() ?></a>
            </div>
            <div class="clr"></div>
            <div style="background-color: rgba(0, 0, 0, 0.6);top: 0;color: #fff;margin: 0;padding: 5px;position: absolute;width: 100%; font-size: 30px;;">
              <?php echo $event->getTitle()?></div>
          </div>
          <div class="events_info"  style="float: left; height: auto;">
            <div class="event_ticket_code">
              <?php
              $word = strtolower($this->search);
              echo $card['ticked_code'];?>
            </div>

          </div>
          <div class="ticket_status_button_container" id="ticket_status_button_container_<?php echo $card['card_id'];?>" style="display: inline-block;float: right;margin:30px;width: 150px; color:red;font-size: 20px?> ">
            <?php if($card['used']==1){?>
              <button id="button_change_status_<?php echo $card['card_id'];?>" onclick="status_chnage_ticket(<?php echo $card['card_id']?>,2)" class="btn card-used-button"> Make Unused</button>
            <?php
            }else{?>
              <button id="button_change_status_<?php echo $card['card_id'];?>" onclick="status_chnage_ticket(<?php echo $card['card_id']?>,1)" class="btn card-used-button">Used</button>
            <?php } ?>
          </div>        </div>
        <?php if($this->paginator->count()>1){?>
          <script>
          $('page_status').set('page',2);
          $('page_status').set('count',<?php echo $this->paginator->count()?>);
            if($('view_more_button')){
              $('view_more_button').setStyle('display','block');
            }

          </script>
        <?php }?>

        <div class="card-used-border-1 view-page" style="display<?php if($card['used']!=1){?>:none;<?php }else{?>:inline-block;<?php }?> ">
          <div class="card-used-border-2">
            <?php echo $this->translate('Used');?>
          </div>
        </div>
      </li>

    <?php endforeach; ?>
<script>
  function status_chnage_ticket(id,status){

    if (window.admin_search_interval) {

    }
    $('button_change_status_'+id).set('html','Loading...');
    var req = new Request({
      method: 'get',
      url: en4.core.baseUrl + 'admin/heevent/index/status',
      data: {
        'id': id,
        'status':status
      },
      evalScripts: true,
      onComplete: function (response) {
          location.reload();
        }


    }).send();


  }
  function massage(status){
    if(status == 1){
      $$('.ticket_status_button_container').set('html','<span style="color: #008000">You have success changed status to "Used"</span>');
      setTimeout(function(){
        location.reload();
      },3000);
    }else{
      $$('.ticket_status_button_container').set('html','<span style="color: red">error</span>')
      setTimeout(function(){
        location.reload();
      },3000);
    }
  }
</script>
  </ul>