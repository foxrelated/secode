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
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/styles/customscrollbar.css'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/jquery.min.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/customscrollbar.concat.min.js'); ?>

<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesvideo/externals/styles/styles.css'); ?>
<script type="text/javascript">
  function showPopUp(url) {
    Smoothbox.open(url);
    parent.Smoothbox.close;
  }
</script>
<?php if($this->showType == 'gridview'): ?>
<?php if(count($this->results) > 0) :?>
  <ul class="sesvideo_playlist_grid_listing sesbasic_clearfix sesbasic_bxs">
    <?php foreach( $this->results as $item ):  ?>
      <li class="sesvideo_playlist_grid sesbm" style="width:<?php echo $this->width ?>px;">
        <div class="sesvideo_playlist_grid_top sesbasic_clearfix">
          <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon')) ?>
          <div>
            <div class="sesvideo_playlist_grid_title">
              <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
            </div>
            <?php if(!empty($this->information) && in_array('postedby', $this->information)): ?>
            <div class="sesvideo_playlist_grid_stats  sesbasic_text_light">
              <?php echo $this->translate('by');?> <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>     
            </div>
            <?php endif; ?>
            <div class="sesvideo_playlist_grid_stats sesvideo_list_stats sesbasic_text_light">
              <?php if (!empty($this->information) && in_array('favouriteCount', $this->information)): ?>
                <span title="<?php echo $this->translate(array('%s favorite', '%s favorites', $item->favourite_count), $this->locale()->toNumber($item->favourite_count)); ?>"><i class="fa fa-heart"></i><?php echo $item->favourite_count; ?></span>
              <?php endif; ?>
              <?php if (!empty($this->information) && in_array('viewCount', $this->information)): ?>
                <span title="<?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count)); ?>"><i class="fa fa-eye"></i><?php echo $item->view_count; ?></span>
              <?php endif; ?>
              <?php if (!empty($this->information) && in_array('likeCount', $this->information)): ?>
                <span title="<?php echo $this->translate(array('%s like', '%s likes', $item->like_count), $this->locale()->toNumber($item->like_count)); ?>"><i class="fa fa-thumbs-up"></i><?php echo $item->like_count; ?></span>
              <?php endif; ?>
              <?php $videoCount = Engine_Api::_()->getDbtable('playlistvideos', 'sesvideo')->playlistVideosCount(array('playlist_id' => $item->playlist_id));  ?>
              <?php if (!empty($this->information) && in_array('videoCount', $this->information)): ?>
                <span title="<?php echo $this->translate(array('%s video', '%s videos', $videoCount), $this->locale()->toNumber($videoCount)); ?>"><i class="fa fa-video-camera"></i><?php echo $videoCount; ?></span>
              <?php endif; ?>
            </div>
          </div>
        </div>
               
        <?php $songs = $item->getVideos(); ?>
        <?php if($songs && !empty($this->information) && in_array('songsListShow', $this->information)):  ?>
        <div class="clear sesbasic_clearfix sesvideo_videos_minilist_container sesbm sesbasic_custom_scroll">
          <ul class="clear sesvideo_videos_minilist sesbasic_bxs">            
            <?php foreach( $songs as $song ):  ?>
             <?php $song = Engine_Api::_()->getItem('sesvideo_video', $song->file_id); ?>
              <li class="sesbasic_clearfix sesbm">
                <div class="sesvideo_videos_minilist_photo">
                  <a class="sesvideo_thumb_img" data-url = "<?php echo $song->getType() ?>" href="<?php echo $song->getHref(array('type'=>'sesvideo_playlist','item_id'=>$item->playlist_id)); ?>">
              			<span style="background-image:url(<?php echo $song->getPhotoUrl() ?>);"></span>
              		</a>
                </div>
                <div class="sesvideo_videos_minilist_name" title="<?php echo $song->getTitle() ?>">
                    <?php echo $this->htmlLink($song->getHref(), $song->getTitle(), array('class' => 'sesbasic_linkinherit')); ?>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>        
        <?php endif; ?>
      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>
<?php elseif($this->showType == 'carouselview'):  ?>

  <?php $randonNumber = $this->identity; ?>
  <?php $baseUrl = $this->layout()->staticBaseUrl; ?>
   <?php $this->headScript()->appendFile($baseUrl . 'application/modules/Sesbasic/externals/scripts/PeriodicalExecuter.js')
 												 ->appendFile($baseUrl . 'application/modules/Sesbasic/externals/scripts/Carousel.js')
                         ->appendFile($baseUrl . 'application/modules/Sesbasic/externals/scripts/Carousel.Extra.js'); 
      $this->headLink()->appendStylesheet($baseUrl . 'application/modules/Sesbasic/externals/styles/carousel.css'); 
 ?>

  <style>
    #playlistslide_<?php echo $randonNumber; ?> {
      position: relative;
      height:<?php echo $this->height ?>px;
      overflow: hidden;
    }
  </style>

  <div class="slide sesbasic_carousel_wrapper sesbm clearfix sesbasic_bxs <?php if($this->viewType == 'horizontal'): ?> sesbasic_carousel_h_wrapper <?php else: ?> sesbasic_carousel_v_wrapper <?php endif; ?>">
    <div id="playlistslide_<?php echo $randonNumber; ?>">
    <?php foreach( $this->results as $item ):  ?>
      <div class="sesvideo_playlist_grid sesbm" style="height:<?php echo $this->height ?>px;width:<?php echo $this->width ?>px;">
        <div class="sesvideo_playlist_grid_top sesbasic_clearfix">
          <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon')) ?>
          <div>
            <div class="sesvideo_playlist_grid_title">
              <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
            </div>
            <?php if(!empty($this->information) && in_array('postedby', $this->information)): ?>
            <div class="sesvideo_playlist_grid_stats sesbasic_text_light">
              <?php echo $this->translate('by');?> <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>     
            </div>
            <?php endif; ?>
            <div class="sesvideo_playlist_grid_stats sesvideo_list_stats sesbasic_text_light">
              <?php if (!empty($this->information) && in_array('favouriteCount', $this->information)): ?>
                <span title="<?php echo $this->translate(array('%s favorite', '%s favorites', $item->favourite_count), $this->locale()->toNumber($item->favourite_count)); ?>"><i class="fa fa-heart"></i><?php echo $item->favourite_count; ?></span>
              <?php endif; ?>
              <?php if (!empty($this->information) && in_array('viewCount', $this->information)): ?>
                <span title="<?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count)); ?>"><i class="fa fa-eye"></i><?php echo $item->view_count; ?></span>
              <?php endif; ?>
              <?php if (!empty($this->information) && in_array('likeCount', $this->information)): ?>
                <span title="<?php echo $this->translate(array('%s like', '%s likes', $item->like_count), $this->locale()->toNumber($item->like_count)); ?>"><i class="fa fa-thumbs-up"></i><?php echo $item->like_count; ?></span>
              <?php endif; ?>
              <?php $videoCount = Engine_Api::_()->getDbtable('playlistvideos', 'sesvideo')->playlistVideosCount(array('playlist_id' => $item->playlist_id));  ?>
              <?php if (!empty($this->information) && in_array('videoCount', $this->information)): ?>
                <span title="<?php echo $this->translate(array('%s video', '%s videos', $videoCount), $this->locale()->toNumber($videoCount)); ?>"><i class="fa fa-video-camera"></i><?php echo $videoCount; ?></span>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <?php $songs = $item->getVideos(); ?>
        <?php if($songs && !empty($this->information) && in_array('songsListShow', $this->information)):  ?>
        <div class="clear sesbasic_clearfix sesvideo_videos_minilist_container sesbm sesbasic_custom_scroll">
          <ul class="clear sesvideo_videos_minilist sesbasic_bxs">            
            <?php foreach( $songs as $song ): ?>
             <?php $song = Engine_Api::_()->getItem('sesvideo_video', $song->file_id); ?>
              <li class="sesbasic_clearfix sesbm">
                <div class="sesvideo_videos_minilist_photo">
                  <a class="sesvideo_thumb_img" data-url = "<?php echo $song->getType() ?>" href="<?php echo $song->getHref(array('type'=>'sesvideo_playlist','item_id'=>$item->playlist_id)); ?>">
              			<span style="background-image:url(<?php echo $song->getPhotoUrl() ?>);"></span>
              		</a>
                </div>
                <div class="sesvideo_videos_minilist_name" title="<?php echo $song->getTitle() ?>">
                   <?php echo $this->htmlLink($song->getHref(), $song->getTitle(), array('class' => 'sesbasic_linkinherit')); ?>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
    </div>
    <?php if($this->viewType == 'horizontal'): ?>
      <div class="tabs_<?php echo $randonNumber; ?> sesbasic_carousel_nav">
        <a class="sesbasic_carousel_nav_pre" href="#page-p"><i class="fa fa-caret-left"></i></a>
        <a class="sesbasic_carousel_nav_nxt" href="#page-p"><i class="fa fa-caret-right"></i></a>
      </div>  
    <?php else: ?>
      <div class="tabs_<?php echo $randonNumber; ?> sesbasic_carousel_nav">
        <a class="sesbasic_carousel_nav_pre" href="#page-p"><i class="fa fa-caret-up"></i></a>
        <a class="sesbasic_carousel_nav_nxt" href="#page-p"><i class="fa fa-caret-down"></i></a>
      </div>  
    <?php endif; ?>

  </div>
  <script type="text/javascript">
  window.addEvent('domready', function() {
    var duration = 150,
    div = document.getElement('div.tabs_<?php echo $randonNumber; ?>');
    links = div.getElements('a'),
    carousel = new Carousel.Extra({
      activeClass: 'selected',
      container: 'playlistslide_<?php echo $randonNumber; ?>',
      circular: true,
      current: 1,
      previous: links.shift(),
      next: links.pop(),
      tabs: links,
      mode: '<?php echo $this->viewType; ?>',
      fx: {
        duration: duration
      }
    })
  });
  </script>
<?php endif; ?>


<script>
	jqueryObjectOfSes(document).ready(function() {
		(function($){
			jqueryObjectOfSes(window).load(function(){
				jqueryObjectOfSes(".sesbasic_custom_scroll").mCustomScrollbar({
					theme:"minimal-dark"
				});
				
			});
		})(jQuery);
	});
</script>