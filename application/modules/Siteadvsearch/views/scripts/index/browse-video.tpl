<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteadvsearch
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: browse-video.tpl 2014-08-06 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if( $this->tag ): ?>
  <h3>
    <?php echo $this->translate('Videos using the tag') ?>
    #<?php echo $this->tag ?>
    <a href="<?php echo $this->url(array('module' => 'video', 'controller' => 'index', 'action' => 'browse'), 'default', true) ?>">(x)</a>
  </h3>
<?php endif; ?>

<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
<div id="list_view">
	<ul class="videos_browse">
		<?php foreach( $this->paginator as $item ): ?>
			<li>
				<div class="video_thumb_wrapper">
					<?php if( $item->duration ): ?>
					<span class="video_length">
						<?php
							if( $item->duration >= 3600 ) {
								$duration = gmdate("H:i:s", $item->duration);
							} else {
								$duration = gmdate("i:s", $item->duration);
							}
							echo $duration;
						?>
					</span>
					<?php endif ?>
					<?php
						if( $item->photo_id ) {
							echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal'));
						} else {
							echo '<img alt="" src="' . $this->escape($this->layout()->staticBaseUrl) . 'application/modules/Video/externals/images/video.png">';
						}
					?>
				</div>
				<?php echo $this->htmlLink($item->getHref(), $item->getTitle(), array('class' => 'video_title')) ?>
				<div class="video_author">
					<?php echo $this->translate('By') ?>
					<?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>
				</div>
				<div class="video_stats">
					<span class="video_views">
						<?php echo $this->translate(array('%1$s view', '%1$s views', $item->view_count), $this->locale()->toNumber($item->view_count)) ?>
					</span>
					<?php if( $item->rating > 0 ): ?>
						<?php for( $x=1; $x<=$item->rating; $x++ ): ?>
							<span class="rating_star_generic rating_star"></span>
						<?php endfor; ?>
						<?php if( (round($item->rating) - $item->rating) > 0): ?>
							<span class="rating_star_generic rating_star_half"></span>
						<?php endif; ?>
					<?php endif; ?>
				</div>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
<?php elseif( $this->category || $this->tag || $this->text ):?>
  <div class="tip">
    <span>
      <?php echo $this->translate('Nobody has posted a video with that criteria.');?>
      <?php if ($this->can_create):?>
        <?php echo $this->translate('Be the first to %1$spost%2$s one!', '<a href="'.$this->url(array('action' => 'create'), "video_general").'">', '</a>'); ?>
      <?php endif; ?>
    </span>
  </div>
<?php else:?>
  <div class="tip">
    <span>
      <?php echo $this->translate('Nobody has created a video yet.');?>
      <?php if ($this->can_create):?>
        <?php echo $this->translate('Be the first to %1$spost%2$s one!', '<a href="'.$this->url(array('action' => 'create'), "video_general").'">', '</a>'); ?>
      <?php endif; ?>
    </span>
  </div>
<?php endif; ?>

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

<script>
  var url = en4.core.baseUrl + 'siteadvsearch/index/browse-video';
  var ulClass = '.videos_browse';
</script>
<?php include APPLICATION_PATH . "/application/modules/Siteadvsearch/views/scripts/viewmoreresuls.tpl"; ?>

