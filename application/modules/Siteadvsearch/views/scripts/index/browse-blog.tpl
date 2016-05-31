<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteadvsearch
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: browse-blog.tpl 2014-08-06 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if ($this->paginator->getTotalItemCount() > 0): ?>
  <div id="list_view">
    <ul class="blogs_browse">
      <?php foreach ($this->paginator as $item): ?>
        <li>
          <div class='blogs_browse_photo'>
            <?php //echo $this->htmlLink($item->getOwner()->getHref(), $this->itemPhoto($item->getOwner(), 'thumb.icon')) ?>
            <?php echo $this->htmlLink($item->getOwner()->getHref(), "<img  class='thumb_normal' src='" . $this->layout()->staticBaseUrl . "application/modules/Siteadvsearch/externals/images/blog_default_photo.png' alt='' />") ?>
          </div>
          <div class='blogs_browse_info'>
            <span class='blogs_browse_info_title'>
              <h3><?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?></h3>
            </span>
            <p class='blogs_browse_info_date'>
              <?php echo $this->translate('Posted'); ?>
              <?php echo $this->timestamp(strtotime($item->creation_date)) ?>
              <?php echo $this->translate('by'); ?>
              <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>
            </p>
            <p class='blogs_browse_info_blurb'>
              <?php echo $this->string()->truncate($this->string()->stripTags($item->body), 300) ?>
            </p>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php elseif ($this->category || $this->show == 2 || $this->search): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('Nobody has posted an entry with that criteria.'); ?>
      <?php if (TRUE): // @todo check if user is allowed to create a poll ?>
        <?php echo $this->translate('Be the first to %1$swrite%2$s one!', '<a href="' . $this->url(array('action' => 'create'), 'blog_general') . '">', '</a>'); ?>
      <?php endif; ?>
    </span>
  </div>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('Nobody has written a blog entry yet.'); ?>
      <?php if ($this->canCreate): ?>
        <?php echo $this->translate('Be the first to %1$swrite%2$s one!', '<a href="' . $this->url(array('action' => 'create'), 'blog_general') . '">', '</a>'); ?>
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
  var url = en4.core.baseUrl + 'siteadvsearch/index/browse-blog';
  var ulClass = '.blogs_browse';
</script>
<?php include APPLICATION_PATH . "/application/modules/Siteadvsearch/views/scripts/viewmoreresuls.tpl"; ?>
	  

