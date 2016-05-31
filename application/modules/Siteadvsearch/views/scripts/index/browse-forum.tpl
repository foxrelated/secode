<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteadvsearch
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: browse-forum.tpl 2014-08-06 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if( count($this->paginator) > 0 ): ?>
<div id="list_view">
  <ul class="forum_topics">
    <?php foreach( $this->paginator as $i => $topic ):
      $last_post = $topic->getLastCreatedPost();
      if( $last_post ) {
        $last_user = $this->user($last_post->user_id);
      } else {
        $last_user = $this->user($topic->user_id);
      }
      ?>
      <li class="forum_nth_<?php echo $i % 2 ?>">
        <div class="forum_topics_icon">
         <?php echo $this->htmlLink($topic->getHref(), "<img  class='thumb_normal' src='". $this->layout()->staticBaseUrl . "application/modules/Siteadvsearch/externals/images/forum_default_photo.png' alt='' />") ?>
          </div>
        <div class="forum_topics_views">
          <span>
            <?php echo $this->translate(array('%1$s %2$s view', '%1$s %2$s views', $topic->view_count), $this->locale()->toNumber($topic->view_count), '</span><span>') ?>
          </span>
        </div>
      <div class="forum_topics_replies">
        <span>
          <?php echo $this->translate(array('%1$s %2$s reply', '%1$s %2$s replies', $topic->post_count-1), $this->locale()->toNumber($topic->post_count-1), '</span><span>') ?>
        </span>
      </div>
      <div class="forum_topics_title">
        <h3<?php if( $topic->closed ): ?> class="closed"<?php endif; ?><?php if( $topic->sticky ): ?> class="sticky"<?php endif; ?>>
          <?php echo $this->htmlLink($topic->getHref(), $topic->getTitle());?>
        </h3>
        <?php echo $topic->getDescription();?>
        <?php echo $this->pageLinks($topic, $this->forum_topic_pagelength, null, 'forum_pagelinks') ?>
      </div>
    </li>
  <?php endforeach; ?>
  </ul>
</div>
<?php elseif( preg_match("/search=/", $_SERVER['REQUEST_URI'] )): ?>
<div class="tip">
  <span>
    <?php echo $this->translate('Nobody has created a forum with that criteria.');?>
  </span>
</div>   
<?php else: ?>
  <div class="tip">
    <span>
    <?php echo $this->translate('There are no forums yet.') ?>
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
<?php endif;?>
  
<script>
  var url = en4.core.baseUrl + 'siteadvsearch/index/browse-forum';
  var ulClass = '.forum_topics';
</script>
<?php include APPLICATION_PATH . "/application/modules/Siteadvsearch/views/scripts/viewmoreresuls.tpl"; ?>