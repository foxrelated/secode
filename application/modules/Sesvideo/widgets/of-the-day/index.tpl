<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
 
?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesvideo/externals/styles/styles.css'); ?>
<?php if($this->type == 'video' || $this->type == 'chanel'): ?>
<ul class="sesbasic_sidebar_block sesbasic_bxs sesbasic_clearfix">
	<?php include APPLICATION_PATH . '/application/modules/Sesvideo/views/scripts/_showVideoListGrid.tpl'; ?>
</ul>
<?php elseif($this->type == 'artist'): 
	$items = $this->paginator;
    foreach($items as $item){
      $item = Engine_Api::_()->getItem('sesvideo_artist', $item->artist_id);;
    }
?>
<ul class="sesbasic_sidebar_block sesbasic_bxs sesbasic_clearfix">
  <li class="sesvideo_artist_list"  style="height:<?php echo $this->height ?>px;width:<?php echo $this->width ?>px;">
    <div class="sesvideo_artist_list_photo">
      <?php if($item->artist_photo): ?>
        <?php $img_path = Engine_Api::_()->storage()->get($item->artist_photo, '')->getPhotoUrl(); ?>
        <img src="<?php echo $img_path ?>">
      <?php endif; ?>
    </div>
    <div class="sesvideo_browse_artist_info">
      <?php if(isset($this->titleActive)): ?>
        <div class="sesvideo_browse_artist_title">
          <?php echo $this->htmlLink(array('module'=>'sesvideo', 'controller'=>'artist', 'action'=>'view', 'route'=>'default', 'artist_id' => $item->artist_id), $item->name); ?>
        </div>
      <?php endif; ?>
      <div class="sesvideo_browse_artist_stats sesvideo_list_stats">
        <?php if (isset($this->favouriteActive)) : ?>
          <span title="<?php echo $this->translate(array('%s favorite', '%s favorites', $item->favourite_count), $this->locale()->toNumber($item->favourite_count)) ?>">
          	<i class="fa fa-heart"></i><?php echo $this->locale()->toNumber($item->favourite_count);?>
        	</span>
        <?php endif; ?>
        <?php if (isset($this->ratingActive)) : ?>
          <span  title="<?php echo $this->translate(array('%s rating', '%s ratings', round($item->rating,1)), $this->locale()->toNumber(round($item->rating,1)))?>">
               <i class="fa fa-star"></i><?php echo round($item->rating,1).'/5';?>
              </span>
        <?php endif; ?>
      </div>
    </div>
  </li>
</ul>
<?php elseif($this->type == 'playlist'): ?>
  <ul class="sesbasic_sidebar_block sesvideo_playlist_grid_listing sesbasic_clearfix sesbasic_bxs">
    <?php $items = $this->paginator;
    foreach($items as $item){
      $item = Engine_Api::_()->getItem('sesvideo_playlist', $item->playlist_id);;
    }
     ?>
    <li class="sesvideo_playlist_grid sesbm sesbasic_clearfix " style="width:<?php echo $this->width ?>px;">
      <div class="sesvideo_playlist_grid_top sesbasic_clearfix">
        <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon')) ?>
        <div>
          <div class="sesvideo_playlist_grid_title">
            <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
          </div>
          <?php if(isset($this->byActive)): ?>
          <div class="sesvideo_playlist_grid_stats  sesbasic_text_light">
            <?php echo $this->translate('by');?> <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>     
          </div>
          <?php endif; ?>
          <div class="sesvideo_playlist_grid_stats sesvideo_list_stats sesbasic_text_light">
            <?php if (isset($this->favouriteActive)): ?>
              <span title="<?php echo $this->translate(array('%s favorite', '%s favorites', $item->favourite_count), $this->locale()->toNumber($item->favourite_count)); ?>"><i class="fa fa-heart"></i><?php echo $item->favourite_count; ?></span>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php $videos = $item->getVideos();  ?>
      <?php if($videos && isset($this->videoListShowActive)):  ?>
      <div class="clear sesbasic_clearfix sesvideo_videos_minilist_container sesbm sesbasic_custom_scroll">
       <ul class="clear sesvideo_videos_minilist sesbasic_bxs">   
          <?php foreach( $videos as $video ): ?>
           <?php $video = Engine_Api::_()->getItem('sesvideo_video', $video->file_id); ?>
            <li class="sesbasic_clearfix sesbm">
              <div class="sesvideo_videos_minilist_photo">
                <a class="sesvideo_thumb_img" data-url = "<?php echo $video->getType() ?>" href="<?php echo $video->getHref(array('type'=>'sesvideo_playlist','item_id'=>$item->playlist_id)); ?>">
                  <span style="background-image:url(<?php echo $video->getPhotoUrl() ?>);"></span>
                </a>
              </div>
              <div class="sesvideo_videos_minilist_name" title="<?php echo $video->getTitle() ?>">
                  <?php echo $this->htmlLink($video->getHref(), $video->getTitle(), array('class' => 'sesbasic_linkinherit')); ?>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>        
      <?php endif; ?>
    </li>
  </ul>
<?php endif; ?>