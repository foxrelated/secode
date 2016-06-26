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
  .events_author .owner_icon {
    background-position: top;
    background-size: cover;
    display: block;
    float: left;
    height: 48px;
    overflow: hidden;
    width: 48px;
    border-radius: 2px 2px 0 0;
    border-top: 1px solid rgba(255, 255, 255, 0.5);
    box-shadow: 1px 0 7px rgba(65, 65, 65, 0.5);
  }
  .events_author .owner_name {
    color: #FFFFFF;
    display: inline-block;
    font-size: 13px;
    line-height: 28px;
    margin-top: 20px;
    text-indent: 0.5em;
    text-shadow: 1px 1px 0 #111111;
    vertical-align: bottom;
  }
  .events_author{
    padding-left: 5px;
    position: absolute;
    left: 0;
    bottom: 0;
    width: 100%;
    -moz-box-sizing: border-box;
    -webkit-box-sizing: border-box;

  }
  .heevents_details{
    line-height: 145%;
    /*height: 61px;*/
  }
  .heevent-block .events_photo>a {
    display: block;
  }
  body ul.heevents_browse>li {
    border: medium none;
    clear: none !important;
    float: left;
    margin: 0 1.5% 3%;
    min-height: 420px;
    padding: 0 !important;
    position: relative;
    width: 30.3333%;
  }
  a:link, a:visited {
    text-decoration: none;
  }
  .card-used-border-2 {
    border: 2px dotted red;
    color: red;
    font-family: Courier,New;
    font-size: 30px;
    font-weight: 700;
    padding: 5px 20px;
  }
  ul.heevents_browse>li {
    border: medium none;
    clear: none !important;
    float: left;
    margin: 0 1.5% 3%;
    min-height: 420px;
    padding: 0 !important;
    position: relative;
    width: 30.3333%;
  }
  .heevent-block {
    background-color: #fff;
    overflow: hidden;
    margin: 4px;
    box-shadow: 0 1px 4px 0 rgba(0, 0, 0,.3);
    border-radius: 2px 2px 2px 2px;
  }
  ul {
    list-style-type: none;
  }
  .heevents_info {
    height: 100%;
    float: left;
    position: relative;
    width: 360px;
    padding-left: 10px;
  }
  .heevents_details {
    line-height: 145%;
  }
  .heevents_details>div {
    cursor: default;
    font-size: 12px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }
  .heevents_details i {
    display: inline-block;
    font-size: 14px;
    filter: alpha(opacity=60);
    opacity: 0.6;
    text-align: center;
    width: 1.25em;
  }
  [class^="hei-"], [class*=" hei-"], [class^="hei-"]:before, [class*=" hei-"]:before {
    font-family: FontAwesome;
    font-weight: normal;
    font-style: normal;
    text-decoration: inherit;
    -webkit-font-smoothing: antialiased;
  }
  [class^="hei-"], [class*=" hei-"], [class^="hei-"]:before, [class*=" hei-"]:before {
    font-family: FontAwesome;
    font-weight: normal;
    font-style: normal;
    text-decoration: inherit;
    -webkit-font-smoothing: antialiased;
  }

  .hei, .hei:before {
    display: inline-block;
    font-family: FontAwesome;
    font-style: normal;
    font-weight: normal;
    line-height: 1;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
  }
  .event_ticket_code {
    background-color: #FFF;
    clear: both;
    display: block;
    font-family: monospace;
    font-size: 36px;
    font-weight: 700;
    left: -10px;
    padding: 5px;
    position: relative;
    top: 40px;
    width: 101%;
  }
  .card-used-border-1 {
    background: none;
    border: 3px solid red;
    left: 500px;
    padding: 1px;
    position: absolute;
    top: 100px;
    transform: rotate(-15deg);
    -webkit-transform: rotate(-15deg);
    -moz-transform: rotate(-15deg);
    -o-transform: rotate(-15deg);
    -ms-transform: rotate(-15deg);
  }
  .fake-img {
    background-position: center;
    background-size: cover;
    display: block !important;
    width: 300px !important;
  }
  ul.heevents_browse .events_photo img {
    max-height: none;
    max-width: none;
  }
  .heevent-block {
    background-color: #fff;
    overflow: hidden;
    margin: 4px;
    box-shadow: 0 1px 4px 0 rgba(0, 0, 0,.3);
    border-radius: 2px 2px 2px 2px;
  }
</style>

<script type="text/javascript">
  function print_page() {
    $('print_EVENT').setStyle('display', 'none');
    window.print();
    window.titlename = document.title;
    document.title = " ";
    setTimeout("show_button(window.titlename)", 60000);
  }
  function show_button(title) {
    $('print_EVENT').setStyle('display', 'block');
    document.title = title;
  }
</script>
<div class="print_EVENT_preview">
  <div id="print_EVENT" class="print_EVENT_button">
    <a href="javascript:void(0);" style="background-image: url('./application/modules/Heevent/externals/images/print_event.png'); width: 100px;" class="buttonlink" onclick="print_page()" align="right"><?php echo $this->translate('Take Print') ?></a>
  </div>
</div>
<?php


?>
<?php if( count($this->card) > 0 ):
  $host = (isset($_SERVER['HTTPS']) ? "https" : "http");
  $host_url = $host.'://'.str_ireplace('heevents','',$_SERVER['HTTP_HOST'].Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'heevent_extended')).'admin/heevent/index/ticketsview?id=';
  $host_urls = $host.'://'.str_ireplace('heevents','',$_SERVER['HTTP_HOST'].Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'heevent_extended'));

  ?>
  <ul class='heevents_browse events_browse' style="">
    <?php foreach( $this->card as $card ): ?>

      <?php $type = 'event'; ?>
      <?php $event = Engine_Api::_()->getItem('event', $card['event_id']);  ?>
      <li class="heevent-block card_<?php echo $card['card_id']?>" style="width: 900px;margin-top: 5px;  border: 1px solid #666;min-height: 214px" id="">
        <img style="height:214px" class="fake-img" src="<?php echo $eventPhotoUrl?>">
        <div class="events-item-wrapper" style="  height: 100%;position: absolute;  top: 0;width: 100%;">
          <div class="events_photo" style="height: 214px;    position: relative;    width: 300px;  float: left;overflow: hidden;">
            <a href="<?php echo $event->getHref() ?>" style="height: 100%">
              <?php
              $eventPhotoUrl = $event->getPhotoUrl('thumb.pin');
              if(!$eventPhotoUrl)
                $eventPhotoUrl = $host_urls ."application/modules/Heevent/externals/images/event-list-nophoto.gif";
              $owner = $event->getOwner();
              $viewer = Engine_Api::_()->user()->getViewer();
              ?>
              <img class="fake-img" src="<?php echo $eventPhotoUrl?>" alt="" style="height: 100%;background-image: url(<?php echo $eventPhotoUrl?>)">
            </a>

            <div class="events_author" style=" ">
              <a class="owner_icon wall_liketips wp_init" href="<?php echo $viewer->getHref() ?> "
                 title="<?php echo $viewer->getTitle() ?>"
                 style="background-image: url(<?php echo $viewer->getPhotoUrl('thumb.normal') ? $owner->getPhotoUrl('thumb.normal') : $host_urls.'application/modules/User/externals/images/nophoto_user_thumb_icon.png' ?>)">
                <img style="height: 100%"src="<?php echo $viewer->getPhotoUrl('thumb.normal') ? $owner->getPhotoUrl('thumb.normal') : $host_urls.'application/modules/User/externals/images/nophoto_user_thumb_icon.png' ?>" alt="" >

                 </a>
              <a class="owner_name" href="<?php echo $owner->getHref()?>"><?php echo $owner->getTitle() ?></a>
            </div>
          </div>
          <div class="heevents_info">
            <div class="heevents_title">
              <h3><?php echo $this->htmlLink($event->getHref(), $event->getTitle()) ?></h3>
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
            <img src="https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=<?php echo $host_url.$card['card_id'] ?>&choe=UTF-8" title="<?php echo $event->getTitle(); ?>" />
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



<?php endif; ?>