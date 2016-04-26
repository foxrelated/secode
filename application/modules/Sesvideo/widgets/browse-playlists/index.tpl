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
<?php
//This forces every playlist to have a unique ID, so that a playlist can be displayed twice on the same page.
$random   = '';
for ($i=0; $i<6; $i++) { $d=rand(1,30)%2; $random .= ($d?chr(rand(65,90)):chr(rand(48,57))); }
?>
<script type="text/javascript">
  function showPopUp(url) {
    Smoothbox.open(url);
    parent.Smoothbox.close;
  }

  function loadMoreContent() {
  
    if ($('load_more'))
      $('load_more').style.display = "<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->count == 0 ? 'none' : '' ) ?>";

    if(document.getElementById('load_more'))
      document.getElementById('load_more').style.display = 'none';
    
    if(document.getElementById('underloading_image'))
      document.getElementById('underloading_image').style.display = '';

    en4.core.request.send(new Request.HTML({
      method: 'post',              
      'url': en4.core.baseUrl + 'widget/index/mod/sesvideo/name/browse-playlists',
      'data': {
        format: 'html',
        page: "<?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>",
        viewmore: 1,
        params: '<?php echo json_encode($this->all_params); ?>',        
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        document.getElementById('results_data').innerHTML = document.getElementById('results_data').innerHTML + responseHTML;
        
        if(document.getElementById('load_more'))
          document.getElementById('load_more').destroy();
        
        if(document.getElementById('underloading_image'))
         document.getElementById('underloading_image').destroy();
       
        if(document.getElementById('loadmore_list'))
         document.getElementById('loadmore_list').destroy();
               jqueryObjectOfSes(".sesbasic_custom_scroll").mCustomScrollbar({
          theme:"minimal-dark"
        });
      }
    }));
    return false;
  }
</script>

<?php if(count($this->paginator) > 0): ?>
  <?php if (empty($this->viewmore)): ?>
    <h4><?php echo $this->translate(array('%s playlist found.', '%s playlists found.', $this->paginator->getTotalItemCount()), $this->locale()->toNumber($this->paginator->getTotalItemCount())); ?></h4>
    <ul class="sesvideo_browse_playlist sesbasic_bxs" id="results_data">
  <?php endif; ?>
  <?php foreach ($this->paginator as $item):  ?>
    <li  class="sesvideo_listing_list sesbasic_clearfix clear">
    <div class="sesvideo_browse_playlist_thumb sesvideo_list_thumb sesvideo_thumb">
      <a href="<?php echo $item->getHref(); ?>" class="sesvideo_thumb_nolightbox">
        <span style="background-image:url(<?php echo $item->getPhotoUrl(); ?>);"></span>
      </a>
     <?php if(!empty($this->information) && in_array('featuredLabel', $this->information) || in_array('sponsoredLabel', $this->information)){ ?>
      <p class="sesvideo_labels">
      <?php if(in_array('featuredLabel', $this->information) && $item->is_featured ){ ?>
        <span class="sesvideo_label_featured"><?php echo $this->translate('FEATURED'); ?></span>
      <?php } ?>
      <?php if(in_array('sponsoredLabel', $this->information) && $item->is_sponsored ){ ?>
        <span class="sesvideo_label_sponsored"><?php echo $this->translate("SPONSORED"); ?></span>
      <?php } ?>
      </p>
     <?php } ?>
     	<?php $urlencode = urlencode(((!empty($_SERVER["HTTPS"]) &&  strtolower($_SERVER["HTTPS"]) == 'on') ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $item->getHref()); ?>
     	<div class="sesvideo_thumb_btns"> 
      	<?php if(!empty($this->information) && in_array('socialSharing', $this->information)){ ?>
          <a href="<?php echo 'http://www.facebook.com/sharer/sharer.php?u=' . $urlencode . '&t=' . $item->getTitle(); ?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Facebook'); ?>')" class="sesbasic_icon_btn sesbasic_icon_facebook_btn"><i class="fa fa-facebook"></i></a>
          <a href="<?php echo 'http://twitthis.com/twit?url=' . $urlencode . '&title=' . $item->getTitle(); ?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Twitter')?>')" class="sesbasic_icon_btn sesbasic_icon_twitter_btn"><i class="fa fa-twitter"></i></a>
          <a href="<?php echo 'http://pinterest.com/pin/create/button/?url='.$urlencode; ?>&media=<?php echo urlencode((strpos($item->getPhotoUrl('thumb.main'),'http') === FALSE ? (((!empty($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"] == 'on')) ? "https://" : "http://") . $_SERVER['HTTP_HOST'].$item->getPhotoUrl() ) : $item->getPhotoUrl('thumb.main'))); ?>&description=<?php echo $item->getTitle();?>" onclick="return socialSharingPopUp(this.href,'<?php echo $this->translate('Pinterest'); ?>')" class="sesbasic_icon_btn sesbasic_icon_pintrest_btn"><i class="fa fa-pinterest"></i></a>
				<?php } ?>  
        <?php if($this->viewer_id): ?>
          <?php if($this->viewer_id && !empty($this->information) && in_array('share', $this->information)): ?>
          	<a  class="sesbasic_icon_btn" title='<?php echo $this->translate("Share") ?>' href="javascript:void(0);" onclick="showPopUp('<?php echo $this->escape($this->url(array('module'=>'activity', 'controller'=>'index', 'action'=>'share', 'route'=>'default', 'type'=>'sesvideo_playlist', 'id' => $item->playlist_id, 'format' => 'smoothbox'), 'default' , true)); ?>'); return false;" >
          		<i class="fa fa-share"></i>
          	</a>
        	<?php endif; ?>
        <?php endif; ?>
        <?php 
        if(Engine_Api::_()->user()->getViewer()->getIdentity() != 0 ){
            $itemtype = 'sesvideo_playlist';
            $getId = 'playlist_id';                                
            $canComment =  true;
            if(!empty($this->information) && in_array('likeButton', $this->information) && $canComment){
          ?>
          <!--Like Button-->
          <?php $LikeStatus = Engine_Api::_()->sesvideo()->getLikeStatusVideo($item->$getId,$item->getType()); ?>
            <a href="javascript:;" data-url="<?php echo $item->$getId ; ?>" class="sesbasic_icon_btn sesbasic_icon_btn_count sesbasic_icon_like_btn sesvideo_like_<?php echo $itemtype; ?> <?php echo ($LikeStatus) ? 'button_active' : '' ; ?>"> <i class="fa fa-thumbs-up"></i><span><?php echo $item->like_count; ?></span></a>
            <?php } ?>
             <?php if(!empty($this->information) && in_array('favouriteButton', $this->information) && isset($item->favourite_count)){ ?>
            
            <?php $favStatus = Engine_Api::_()->getDbtable('favourites', 'sesvideo')->isFavourite(array('resource_type'=>$itemtype,'resource_id'=>$item->$getId)); ?>
            <a href="javascript:;" class="sesbasic_icon_btn sesbasic_icon_btn_count sesbasic_icon_fav_btn sesvideo_favourite_<?php echo $itemtype; ?> <?php echo ($favStatus)  ? 'button_active' : '' ?>"  data-url="<?php echo $item->$getId ; ?>"><i class="fa fa-heart"></i><span><?php echo $item->favourite_count; ?></span></a>
          <?php } ?>
        <?php  } ?>
      </div>
    </div>
    <div class="sesvideo_list_info">
      <?php if(!empty($this->information) && in_array('title', $this->information)): ?>
        <div class="sesvideo_list_title">
          <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
        </div>
      <?php endif; ?>
      <?php if(!empty($this->information) && in_array('postedby', $this->information)): ?>
      <div class="sesvideo_list_date sesbasic_text_light">
        <?php echo $this->translate('Created By %s', $this->htmlLink($item->getOwner(), $item->getOwner()->getTitle())) ?>
      </div>
      <?php endif; ?>

      <div class="sesvideo_list_date sesvideo_list_stats sesbasic_text_light"> 
        <?php if(!empty($this->information) && in_array('viewCount', $this->information)): ?>
        	<span title="<?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count))?>"><i class="fa fa-eye"></i><?php echo $item->view_count; ?></span>
        <?php endif; ?>
        <?php if(!empty($this->information) && in_array('favouriteCount', $this->information)): ?>
        	<span title="<?php echo $this->translate(array('%s favourite', '%s favourites', $item->favourite_count), $this->locale()->toNumber($item->favourite_count))?>"><i class="fa fa-heart"></i><?php echo $item->favourite_count;?></span>
        <?php endif; ?>
        <?php if(!empty($this->information) && in_array('likeCount', $this->information)): ?>
        	<span title="<?php echo $this->translate(array('%s like', '%s likes', $item->like_count), $this->locale()->toNumber($item->like_count)); ?>"><i class="fa fa-thumbs-up"></i><?php echo $item->like_count; ?></span>
        <?php endif; ?>
      </div>

      <?php if(!empty($this->information) && in_array('description', $this->information)): ?>
        <div class="sesvideo_list_des">
            <?php if(strlen($item->description) > $this->description_truncation){ 
                $description = mb_substr($item->description,0,$this->description_truncation).'...';
                echo $title = nl2br($description);
               }else{ ?>
            <?php  echo nl2br($item->description);?>
            <?php } ?>
        </div>
      <?php endif; ?>
      
      <?php if(!empty($this->information) && in_array('showVideosList', $this->information)): ?>
      <?php $playlist = $item; 
      $videos = $item->getVideos();
      ?>   
      <?php if(count($videos) > 0): ?>
      <div class="clear sesbasic_clearfix sesvideo_videos_minilist_container sesbasic_custom_scroll sesbm">
        <ul class="clear sesvideo_videos_minilist sesbasic_bxs">
          <?php foreach( $videos as $videoItems ): ?>
          <?php $video = Engine_Api::_()->getItem('video', $videoItems->file_id); ?>
          <?php if( !empty($video) ): ?>
          <li class="sesbasic_clearfix sesbm">
            <div class="sesvideo_videos_minilist_photo">
           		<a class="sesvideo_thumb_img" data-url = "<?php echo $video->getType() ?>" href="<?php echo $video->getHref(array('type'=>'sesvideo_playlist','item_id'=>$item->playlist_id)); ?>">
              	<span style="background-image:url(<?php echo $video->getPhotoUrl() ?>);"></span>
              </a>
            </div>
            <?php if(!empty($this->information) && in_array('watchLater', $this->information)  && isset($videoItems->watchlater_id)){ ?>
              <div class="sesvideo_videos_minilist_buttons">
                <a href="javascript:;" class="sesbasic_icon_btn sesvideo_watch_later <?php echo !is_null($videoItems->watchlater_id)  ? 'selectedWatchlater' : '' ?>" title = "<?php echo !is_null($videoItems->watchlater_id)  ? $this->translate('Remove from Watch Later') : $this->translate('Add to Watch Later') ?>" data-url="<?php echo $video->video_id ; ?>"><i class="fa fa-clock-o"></i></a>
              </div>
            <?php } ?>
            <div class="sesvideo_videos_minilist_name" title="<?php echo $video->title; ?>">
                <?php echo $this->htmlLink($video->getHref(), $video->getTitle(), array('class' => 'sesbasic_linkinherit')); ?>
            </div>
          </li>
          <?php endif; ?>
          <?php endforeach; ?>
        </ul>
      </div>
      <?php endif; ?>
      <?php endif; ?>
       </div>
    </li>
  <?php endforeach; ?>

  <?php //if($this->paginationType == 1): ?>
    <?php if (!empty($this->paginator) && $this->paginator->count() > 1): ?>
      <?php if ($this->paginator->getCurrentPageNumber() < $this->paginator->count()): ?>
        <div class="clr" id="loadmore_list"></div>
        <div class="sesbasic_view_more" id="load_more" onclick="loadMoreContent();" style="display: block;">
          <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array('id' => 'feed_viewmore_link', 'class' => 'buttonlink icon_viewmore')); ?>
        </div>
        <div class="sesbasic_view_more_loading" id="underloading_image" style="display: none;">
          <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' style='margin-right: 5px;' />
          <?php echo $this->translate("Loading ...") ?>
        </div>
      <?php endif; ?>
     <?php endif; ?>
  <?php //else: ?>
    <?php //echo $this->paginationControl($this->paginator, null, null, array('pageAsQuery' => true, 'query' => $this->all_params)); ?>
  <?php //endif; ?>  
<?php if (empty($this->viewmore)): ?>
</ul>
<?php endif; ?>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are currently no playlists created yet.') ?>
    </span>
  </div>
<?php endif; ?>
<?php if($this->paginationType == 1): ?>
  <script type="text/javascript">    
     //Take refrences from: http://mootools-users.660466.n2.nabble.com/Fixing-an-element-on-page-scroll-td1100601.html
    //Take refrences from: http://davidwalsh.name/mootools-scrollspy-load
    en4.core.runonce.add(function() {
    
      var paginatorCount = '<?php echo $this->paginator->count(); ?>';
      var paginatorCurrentPageNumber = '<?php echo $this->paginator->getCurrentPageNumber(); ?>';
      function ScrollLoader() { 
        var scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
        if($('loadmore_list')) {
          if (scrollTop > 40)
            loadMoreContent();
        }
      }
      window.addEvent('scroll', function() { 
        ScrollLoader(); 
      });
    });    
  </script>
<?php endif; ?>