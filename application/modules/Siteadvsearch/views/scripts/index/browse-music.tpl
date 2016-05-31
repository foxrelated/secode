<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteadvsearch
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: browse-music.tpl 2014-08-06 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php $this->headLink()
					->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteadvsearch/externals/styles/style_siteadvsearch.css') ?>
          
<?php if( 0 == count($this->paginator) ): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There is no music uploaded yet.') ?>
      <?php if( $this->canCreate ): ?>
        <?php echo $this->htmlLink(array(
          'route' => 'music_general',
          'action' => 'create'
        ), $this->translate('Why don\'t you add some?')) ?>
      <?php endif; ?>
    </span>
  </div>
<?php else: ?>
  <div id="list_view">
    <ul class="music_browse">
      <?php foreach ($this->paginator as $playlist): ?>
        <li id="music_playlist_item_<?php echo $playlist->getIdentity() ?>">
          <div class="music_browse_author_photo">
            <?php echo $this->htmlLink($playlist->getOwner(),
                     $this->itemPhoto($playlist->getOwner(), 'thumb.normal') ) ?>
          </div>
          <div class="music_browse_info">
            <div class="music_browse_info_title">
              <h3>
                <?php echo $this->htmlLink($playlist->getHref(), $playlist->getTitle()) ?>
              </h3>
            </div>
            <div class="music_browse_info_date">
              <?php echo $this->translate('Created %s by ', $this->timestamp($playlist->creation_date)) ?>
              <?php echo $this->htmlLink($playlist->getOwner(), $playlist->getOwner()->getTitle()) ?>
            -
              <?php echo $this->htmlLink($playlist->getHref(),  $this->translate(array('%s comment', '%s comments', $playlist->getCommentCount()), $this->locale()->toNumber($playlist->getCommentCount()))) ?>
            </div>
            <div class="music_browse_info_desc">
              <?php echo $playlist->description ?>
            </div>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif;?>

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
<script>
  var url = en4.core.baseUrl + 'siteadvsearch/index/browse-music';
  var ulClass = '.music_browse';
</script>
<?php include APPLICATION_PATH . "/application/modules/Siteadvsearch/views/scripts/viewmoreresuls.tpl"; ?>
    
