<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteadvsearch
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: browse-album.tpl 2014-08-06 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
  <div id="list_view">
    <ul class="thumbs">
      <?php foreach( $this->paginator as $album ): ?>
        <li>
          <a class="thumbs_photo" href="<?php echo $album->getHref(); ?>">
            <span style="background-image: url(<?php echo $album->getPhotoUrl('thumb.normal'); ?>);"></span>
          </a>
          <p class="thumbs_info">
            <span class="thumbs_title">
              <?php echo $this->htmlLink($album, $this->string()->chunk($this->string()->truncate($album->getTitle(), 45), 10)) ?>
            </span>
            <?php echo $this->translate('By');?>
            <?php echo $this->htmlLink($album->getOwner()->getHref(), $album->getOwner()->getTitle(), array('class' => 'thumbs_author')) ?>
            <br />
            <?php echo $this->translate(array('%s photo', '%s photos', $album->count()),$this->locale()->toNumber($album->count())) ?>
          </p>
        </li>
      <?php endforeach;?>
    </ul>
  </div>
  <?php if( $this->paginator->count() > 1 ): ?>
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
	 <?php endif; ?>
<?php elseif( $this->searchParams['category_id'] ): ?>
	 <div class="tip">
		  <span>
			   <?php echo $this->translate('Nobody has created an album with that criteria.');?>
			   <?php if( $this->canCreate ): ?>
				    <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="'.$this->url(array('action' => 'upload')).'">', '</a>'); ?>
			   <?php endif; ?>
		  </span>
	 </div>    
<?php else: ?>
	 <div class="tip">
		  <span>
			   <?php echo $this->translate('Nobody has created an album yet.');?>
			   <?php if( $this->canCreate ): ?>
				    <?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="'.$this->url(array('action' => 'upload')).'">', '</a>'); ?>
			   <?php endif; ?>
		  </span>
	 </div>
<?php endif; ?>
<script>
  var url = en4.core.baseUrl + 'siteadvsearch/index/browse-album';
  var ulClass = '.thumbs';
</script>
<?php include APPLICATION_PATH . "/application/modules/Siteadvsearch/views/scripts/viewmoreresuls.tpl"; ?>
 