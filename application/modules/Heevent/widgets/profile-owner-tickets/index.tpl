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
  <input type="text" onkeyup="selectDiv(this)" placeholder="search" class="heevent_search_widged" />
  <script>
    function setPageTicketsHeevent () {
      var inp = $('tickets_search_input');
      if (window.admin_search_pagination == 1) {
        return;
      }
      $('view_more_button_heevent').set('html','<?php echo $this->translate('Loading...')?>');
      var page = $('page_status').get('page');
      var count = $('page_status').get('count');
      if (count >= page) {
        $('view_more_button_heevent').setStyle('display', 'none');
      }
      window.admin_search_pagination == 1;
      var req = new Request({
        method: 'get',
        url: en4.core.baseUrl + 'he-events/moretickets',
        data: {
          'event_id': <?php echo $this->event->getIdentity()?>,
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
          $('view_more_button_heevent').set('html','<?php echo $this->translate('View more')?>');
          window.admin_search_pagination == 0;
        }
      }).send();

    }
    function selectDiv(inp) {
      if(inp.value != '') {
        $$('.events_browse')[0].getChildren('li').each(function (e) {
          e.setStyle('display', 'none');
        });

        $$("li[id^=" + inp.value + "]").each(function (e) {
          e.setStyle('display', 'block');
        });
      }else{
        $$('.events_browse')[0].getChildren('li').each(function (e) {
          e.setStyle('display', 'block');
        });
      }


    }


  </script>
  <div style="display: none" id="page_status" page="2" count="<?php echo $this->paginator->count() ?>"></div>
  <?php $event = Engine_Api::_()->getItem('event', $this->event->getIdentity()); ?>
  <ul class='heevents_browse events_browse' id="hevent_ticket_list">
    <?php foreach( $this->paginator as $card ): ?>
      <?php $type = 'event'; ?>

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
  </ul>
<?php if ($this->paginator->count() > 1) { ?>
  <button onclick="setPageTicketsHeevent()" id="view_more_button_heevent" class="view_more_button_heevent" ><?php echo $this->translate('View more')?> </button>
  <?php } ?>


<?php else: ?>
  <div class="tip">
  <span>
      <?php echo $this->translate('List is empty') ?>

  </span>
  </div>

<?php endif; ?>

