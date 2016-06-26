<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manage.tpl 19.10.13 08:20 Bolot $
 * @author     Bolot
 */
$host = (isset($_SERVER['HTTPS']) ? "https" : "http");
$host_url = $host.'://'.str_ireplace('heevents','',$_SERVER['HTTP_HOST'].Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'heevent_extended')).'admin/heevent/index/ticketsview?id=';

?>
<h2>
  <?php echo $this->translate("All tickets") ?>
</h2>
<?php if (count($this->navigation)): ?>
  <div class='tabs'>

    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<?php


?>
<div style="width: 99.9%; padding: 1px">
  <input type="text" onkeyup="selectDiv(this)" placeholder="search" style="width: 100%; box-sizing: border-box"
         id="tickets_search_input"/>
</div>
<script>
  function selectDiv(inp) {
    if (window.admin_search_interval) {
      clearTimeout(window.admin_search_interval);
    }
    if($('view_more_button_heevent_admin')) $('view_more_button_heevent_admin').setStyle('display', 'none');
    $('hevent_ticket_list').set('html', '<div style="text-align: center; width: 100%;height: 300px"><img style="margin: 50px" src="' + en4.core.baseUrl + 'application/modules/Heevent/externals/images/admin/loader.gif">');
    window.admin_search_interval = setTimeout(function () {
      var req = new Request({
        method: 'get',
        url: en4.core.baseUrl + 'admin/heevent/index/ticketssearch',
        data: {
          'search': inp.value,
          'format': 'smoothbox'
        },
        evalScripts: true,
        onComplete: function (response) {
          var el = new Element('div');
          el.innerHTML = response;
          var script = el.getElement('script');
          $('hevent_ticket_list').set('html', '');
          var ele = el.getElement('#global_content_simple').getElements('li');
          var len = ele.length;
          if (len > 0) {
            for (var i = 0; i < len; i++) {
              ele[i].inject($('hevent_ticket_list'));
            }
          } else {
            $('hevent_ticket_list').set('html', 'Tickets no found');
          }
          $$('.event_ticket_code_admin').each(
            function(el){
              el.addEvent('mouseover', function(){
                var card = el.get('code');
                if(card){
                  $(card.trim()).getChildren('.card-used-border-1')[0].fade('out');
                }
              });
              el.addEvent('mouseout', function(){
                var card = el.get('code');
                if(card){
                  $(card.trim()).getChildren('.card-used-border-1')[0].fade('in');
                }
              });
            }
          );
        }
      }).send();
    }, 1000);
  }
  function setPageTickets() {
    var inp = $('tickets_search_input');
    if (window.admin_search_pagination == 1) {
      return;
    }
    var page = $('page_status').get('page');
    var count = $('page_status').get('count');
    if (count >= page) {
      if($('view_more_button_heevent_admin')) $('view_more_button_heevent_admin').setStyle('display', 'none');
    }
    window.admin_search_pagination == 1;
    var req = new Request({
      method: 'get',
      url: en4.core.baseUrl + 'admin/heevent/index/ticketssearch',
      data: {
        'search': inp.value,
        'page': page,
        'format': 'smoothbox'
      },
      onComplete: function (response) {
        var el = new Element('div');
        el.innerHTML = response;
        $('page_status').set('page', page.toInt() + 1);
        var ele = el.getElement('#global_content_simple').getElements('li');
        var len = ele.length;
        if (len > 0) {
          for (var i = 0; i < len; i++) {
            ele[i].inject($('hevent_ticket_list'));
          }
        }
        window.admin_search_pagination == 0;
        $$('.event_ticket_code_admin').each(
          function(el){
            el.addEvent('mouseover', function(){
              var card = el.get('code');
              if(card){
                if($(card.trim()))$(card.trim()).getChildren('.card-used-border-1')[0].fade('out');
              }
            });
            el.addEvent('mouseout', function(){
              var card = el.get('code');
              if(card){
               if($(card.trim()))$(card.trim()).getChildren('.card-used-border-1')[0].fade('in');
              }
            });
          }
        );
      }
    }).send();

  }
  window.addEvent('domready',function(){
    var opt =  $$('.event_ticket_code_admin').each(
      function(el){
        el.addEvent('mouseover', function(){
          var card = el.get('code');
          if(card){
            $(card.trim()).getChildren('.card-used-border-1')[0].fade('out');
          }
        });
        el.addEvent('mouseout', function(){
          var card = el.get('code');
          if(card){
            $(card.trim()).getChildren('.card-used-border-1')[0].fade('in');
          }
        });
      }
    );
  });

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
        var used =  $$('.card_'+id).getChildren('.card-used-border-1')[0];
        if(response.toInt() == 1){
          var status_used = '<button id="button_change_status_'+id+'" onclick="status_chnage_ticket('+id+',2)" class="btn card-used-button" > Make Unused</button>';
          used.setStyle('opacity','0');
          used.setStyle('display','block');
          used.fade('in');
         }else{
          var status_used = '<button id="button_change_status_'+id+'" onclick="status_chnage_ticket('+id+',1)" class="btn card-used-button">Used</button>';
          used.fade('out');
         setTimeout(function(){ used.setStyle('display','none');},2000);

        }


        $('ticket_status_button_container_'+id).set('html',status_used);
      }
    }).send();


  }
</script>
<div style="display: none" id="page_status" page="2" count="<?php echo $this->paginator->count() ?>"></div>
<?php if (count($this->paginator) > 0): ?>
  <ul class='heevents_browse_admin events_browse' id="hevent_ticket_list" style="display: inline-block;width: 100%;">
    <?php foreach ($this->paginator as $card):?>
      <?php $type = 'event'; ?>
      <?php $event = Engine_Api::_()->getItem('event', $card['event_id']);
      if (!$event) {
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
                 href="<?php echo $owner->getHref() ?>"><?php echo $owner->getTitle() ?></a>
            </div>
            <div class="clr"></div>
            <div class="userTilel"><?php echo $event->getTitle() ?></div>
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
          <div class="events_info" style="">
            <div class="event_ticket_code_admin" code="<?php echo $card['ticked_code'];?>">
              <?php echo $card['ticked_code'] ?>
            </div>

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
  <?php if ($this->paginator->count() > 1) { ?>
    <button onclick="setPageTickets()" id="view_more_button_heevent_admin" class="view_more_button_heevent_admin" > View more</button>
  <?php } ?>
<?php else: ?>
  <div class="tip">
  <span>
      <?php echo $this->translate('List is empty') ?>
  </span>
  </div>

<?php endif; ?>

