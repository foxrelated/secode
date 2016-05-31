<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteadvsearch
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: browse-event.tpl 2014-08-06 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if( count($this->paginator) > 0 ): ?>
  <div id="list_view">
    <ul class='events_browse'>
      <?php foreach( $this->paginator as $event ): ?>
        <li>
          <div class="events_photo">
            <?php echo $this->htmlLink($event->getHref(), $this->itemPhoto($event, 'thumb.normal')) ?>
          </div>
          <div class="events_options">
          </div>
          <div class="events_info">
            <div class="events_title">
              <h3><?php echo $this->htmlLink($event->getHref(), $event->getTitle()) ?></h3>
            </div>
        <div class="events_members">
          <?php echo $this->locale()->toDateTime($event->starttime) ?>
        </div>
            <div class="events_members">
              <?php echo $this->translate(array('%s guest', '%s guests', $event->membership()->getMemberCount()),$this->locale()->toNumber($event->membership()->getMemberCount())) ?>
              <?php echo $this->translate('led by') ?>
              <?php echo $this->htmlLink($event->getOwner()->getHref(), $event->getOwner()->getTitle()) ?>
            </div>
            <div class="events_desc">
              <?php echo $event->getDescription() ?>
            </div>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
  <div class="clr" id="scroll_bar_height"></div>
  <?php if (empty($this->is_ajax)) : ?>
    <div class = "seaocore_view_more mtop10" id="seaocore_view_more" style="display: none;">
      <?php
      echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
          'id' => '',
          'class' => 'buttonlink icon_viewmore'
      ))
      ?>
    </div>
    <div class="seaocore_view_more" id="loding_image" style="display: none;">
      <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' style='margin-right: 5px;' />
      <?php echo $this->translate("Loading ...") ?>
    </div>
    <div id="hideResponse_div"> </div>
  <?php endif; ?>
<?php elseif( preg_match("/category_id=/", $_SERVER['REQUEST_URI'] )): ?>
	<div class="tip">
		<span>
		<?php echo $this->translate('Nobody has created an event with that criteria.');?>
		<?php if( $this->canCreate ): ?>
			<?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="'.$this->url(array('action'=>'create'), 'event_general').'">', '</a>'); ?>
		<?php endif; ?>
		</span>
	</div>   
<?php else: ?>
  <div class="tip">
    <span>
    <?php if( $this->filter != "past" ): ?>
      <?php echo $this->translate('Nobody has created an event yet.') ?>
      <?php if( $this->canCreate ): ?>
        <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="'.$this->url(array('action'=>'create'), 'event_general').'">', '</a>'); ?>
      <?php endif; ?>
    <?php else: ?>
      <?php echo $this->translate('There are no past events yet.') ?>
    <?php endif; ?>
    </span>
  </div>
<?php endif; ?>
    
<script>
  var url = en4.core.baseUrl + 'siteadvsearch/index/browse-event';
  var ulClass = '.events_browse';
</script>
<?php include APPLICATION_PATH . "/application/modules/Siteadvsearch/views/scripts/viewmoreresuls.tpl"; ?>

<script type="text/javascript">
  $$('.core_main_event').getParent().addClass('active');
</script>