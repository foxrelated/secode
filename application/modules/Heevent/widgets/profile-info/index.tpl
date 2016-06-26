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
<?php
$event = $this->subject;
?>
<div class="heevent-block heevent-widget">
  <div class="heevent-wg-title-options">
    <div class="heevents-options events_action heevent-widget-inner">
      <?php if( $this->viewer()->getIdentity() && ($event->isOwner($this->viewer()) || $this->viewer()->level_id < 4)):?>
      <button title="<?php echo $this->translate('Edit Event') ?>"  value="edit" name="option" class="edit option_btn" onclick="window.open('<?php echo $this->url(array('action' => 'edit', 'event_id' => $event->getIdentity()), 'event_specific') ?>', '_blank');"><i class="hei hei-edit"></i></button>
      <?php endif; ?>
    </div>
    <h3>
      <?php echo $this->translate('Event Details') ?>
    </h3>
  </div>
  <div class="heevent-widget-inner">
    <?php if( !empty($this->subject->host) ): ?>
    <span><?php echo $this->translate('Led by') ?></span> - <?php echo $this->htmlLink($event->getParent()->getHref(), $event->getParent()->getTitle()) ?><br/>
      <?php if( $this->subject->host != $this->subject->getParent()->getTitle()): ?>
    <span><?php echo $this->translate('Host') ?></span> - <?php echo $event->host ?><br/>
      <?php endif ?>
    <?php endif ?>
    <?php if( !empty($this->subject->category_id) ): ?>
      <span><?php echo $this->translate('Category')?></span> - <?php echo $this->htmlLink(array(
          'route' => 'event_general',
          'action' => 'browse',
          'category_id' => $this->subject->category_id,
        ), $this->translate((string)$this->subject->categoryName())) ?>
    <?php endif ?>
  </div>
  <div class="events_details heevents_details heevent-widget-inner">
    <div><i class="hei hei-time"></i><?php echo $this->locale()->toDateTime($event->starttime) ?></div>
    <?php if($event->location) {?>
    <div class="event-location"><i class="hei hei-map-marker"></i><?php echo $this->htmlLink('http://maps.google.com/?q='.urlencode($event->location), $event->location, array('target' => 'blank')) ?></div>
      <?php } ?>
<!--    <div><i class="hei hei-user"></i><span guest-count="--><?php //echo $event->membership()->getMemberCount(); ?><!--" id="guests_--><?php //echo $event->getGuid(); ?><!--">--><?php //echo $this->translate(array('%s guest', '%s guests', $event->membership()->getMemberCount()), $this->locale()->toNumber($event->membership()->getMemberCount())) ?><!--</span></div>-->
  </div>
<?php if($event->location) {?>
  <div class="heevent-widget-outer">
    <button onclick="window.open('https://maps.google.com/maps?daddr=<?php echo $event->location ?>', '_blank')" class="heevent-hover-fadein heevent-abs-btn-right heevent-abs-btn" title="<?php echo $this->translate('HEEVENT_Get Directions') ?>"><i class="hei hei-location-arrow"></i></button>
    <img id="hev-details-smap" class="fake-img" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Heevent/externals/images/fake-4x3.gif" alt="<?php echo $event->location ?>">
  </div>
  <?php } ?>
  <div class="heevent-widget-inner">
    <br/>
  <?php if( !empty($event->description) ){ ?>
    <span>
      <?php echo nl2br($this->subject->description) ?>
    </span>
  <?php } ?>
  </div>
</div>
<?php if($event->location) {?>
<script data-cfasync="false" data-cfasync="false" type="text/javascript">
  (function(w){
    w.addEventListener('load', function(){
      var smapEl = document.getElementById('hev-details-smap');
      var w = smapEl.width;
      var h = smapEl.height;
      smapEl.setAttribute('src', 'http://maps.googleapis.com/maps/api/staticmap?center=<?php echo urlencode($event->location) ?>&zoom=<?php echo $event->getMapZoom(); ?>&size='+w+'x'+h+'&markers=color:red|<?php echo urlencode($event->location) ?>&sensor=false');
    });
  })(window);
  </script>
<?php } ?>
