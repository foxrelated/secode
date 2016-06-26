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
<input type="text" onkeyup="selectDiv(this)" placeholder="search" />
<script>
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
<?php if( count($this->paginator) > 0 ): ?>
  <ul class='heevents_browse events_browse'>
    <?php foreach( $this->paginator as $card ): ?>
      <?php $type = 'event'; ?>
      <?php $event = Engine_Api::_()->getItem('event', $card['event_id']); ?>
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
      <?php echo $this->translate('List is empty') ?>

  </span>
  </div>

<?php endif; ?>


<script type="text/javascript">
  _hem.ajaxPagination($$('.paginationControl'), $('global_content').getElement('.layout_core_content'));
  <?php if($this->format == 'html'){ ?>
  _hem.initActionsOn($(document.body));
  <?php } ?>
</script>
