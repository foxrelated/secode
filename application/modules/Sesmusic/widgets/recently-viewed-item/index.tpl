<?php

?>

<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesmusic/externals/scripts/favourites.js'); ?>
<script type="text/javascript">
  function showPopUp(url) {
    Smoothbox.open(url);
    parent.Smoothbox.close;
  }
</script>

<?php if($this->content_type == 'sesmusic_album'): ?>
<?php if(count($this->results) > 0) :?>
<ul class="sesmusic_side_block sesmusic_browse_listing">
  <?php foreach( $this->results as $album ): ?>
  <?php $item = Engine_Api::_()->getItem($album['resource_type'], $album['resource_id']);  ?>
  <?php if($item && $this->viewType == 'listView'): ?>
  <li class="sesmusic_sidebar_list">
    <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon'), array('class' => 'sesmusic_sidebar_list_thumb')) ?>
    <div class="sesmusic_sidebar_list_info">
      <div class="sesmusic_sidebar_list_title">
        <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
      </div>
      <?php if(!empty($this->information) && in_array('postedby', $this->information)): ?>
        <div class="sesmusic_sidebar_list_stats sesbasic_text_light">
          <?php echo $this->translate('by');?> <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>
        </div>
      <?php endif; ?>    
      <?php if($this->showRating && !empty($this->information) && in_array('ratingCount', $this->information)): ?>
        <div class="sesmusic_sidebar_list_stats sesbasic_text_light">
          <?php if( $item->rating > 0 ): ?>
          <?php for( $x=1; $x<= $item->rating; $x++ ): ?>
          <span class="sesbasic_rating_star_small fa fa-star"></span>
          <?php endfor; ?>
          <?php if( (round($item->rating) - $item->rating) > 0): ?>
          <span class="sesbasic_rating_star_small fa fa-star-half"></span>
          <?php endif; ?>
          <?php endif; ?>
        </div>
      <?php endif; ?>
      <div class="sesmusic_sidebar_list_stats sesmusic_list_stats sesbasic_text_light">
        <?php if (!empty($this->information) && in_array('commentCount', $this->information)) :?>
          <span title="<?php echo $this->translate(array('%s comment', '%s comments', $item->comment_count), $this->locale()->toNumber($item->comment_count)); ?>">
            <i class="fa fa-comment"></i>
            <?php echo $item->comment_count; ?>
          </span>
        <?php endif; ?>
        <?php if (!empty($this->information) && in_array('likeCount', $this->information)) : ?>
          <span title="<?php echo $this->translate(array('%s like', '%s likes', $item->like_count), $this->locale()->toNumber($item->like_count)); ?>">
            <i class="fa fa-thumbs-up"></i>
            <?php echo $item->like_count; ?>
          </span>
        <?php endif; ?>
        <?php if (!empty($this->information) && in_array('viewCount', $this->information)) : ?>
          <span title="<?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count))?>">
            <i class="fa fa-eye"></i>
            <?php echo $item->view_count; ?>
          </span>
        <?php endif; ?>
        <?php if (!empty($this->information) && in_array('songsCount', $this->information)) : ?>
          <span title="<?php echo $this->translate(array('%s song', '%s songs', $item->song_count), $this->locale()->toNumber($item->song_count)); ?>">
            <i class="fa fa-music"></i>
            <?php echo $item->song_count; ?>
          </span>
        <?php endif; ?>
      </div>
    </div>
  </li>
  <?php elseif($item && $this->viewType == 'gridview'): ?>
  <li class="sesmusic_item_grid" style="width:<?php echo $this->width ?>px;">
    <div class="sesmusic_item_artwork" style="height:<?php echo $this->height ?>px;">
      <?php echo $this->itemPhoto($item, 'thumb.profile'); ?>
      <a href="<?php echo $item->getHref(); ?>" class="transparentbg"></a>
      <div class="sesmusic_item_info">
          <div class="sesmusic_item_info_title">
            <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
          </div>
        <?php if(!empty($this->information) && in_array('postedby', $this->information)): ?>
          <div class="sesmusic_item_info_owner">
            <?php echo $this->translate('by');?> <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>
          </div>
        <?php endif; ?>

        <div class="sesmusic_item_info_stats">
          <?php if (!empty($this->information) && in_array('commentCount', $this->information)) :?>
            <span>
              <?php echo $item->comment_count; ?>
              <i class="fa fa-comment"></i>
            </span>
          <?php endif; ?>
          <?php if (!empty($this->information) && in_array('likeCount', $this->information)) : ?>
            <span>
              <?php echo $item->like_count; ?>
              <i class="fa fa-thumbs-up"></i>
            </span>
          <?php endif; ?>
          <?php if (!empty($this->information) && in_array('viewCount', $this->information)) : ?>
            <span>
              <?php echo $item->view_count; ?>
              <i class="fa fa-eye"></i>
            </span>
          <?php endif; ?>
          <?php if (!empty($this->information) && in_array('songsCount', $this->information)) : ?>
            <span>
              <?php echo $item->song_count; ?>
              <i class="fa fa-music"></i>
            </span>
          <?php endif; ?>
        </div>

        <?php if ($this->showRating && !empty($this->information) && in_array('ratingCount', $this->information)) : ?>
          <div class="sesmusic_item_info_rating">
            <?php if( $item->rating > 0 ): ?>
            <?php for( $x=1; $x<= $item->rating; $x++ ): ?>
            <span class="sesbasic_rating_star_small fa fa-star"></span>
            <?php endfor; ?>
            <?php if( (round($item->rating) - $item->rating) > 0): ?>
            <span class="sesbasic_rating_star_small fa fa-star-half"></span>
            <?php endif; ?>
            <?php endif; ?>
          </div>
        <?php endif; ?>

        <?php // Featured and Sponsored and Hot Label Icon ?>
        <div class="sesmusic_item_info_label">
          <?php if(!empty($item->hot) && !empty($this->information) && in_array('hot', $this->information)): ?>
          <span class="sesmusic_label_hot"><?php echo $this->translate("HOT"); ?></span>
          <?php endif; ?>
          <?php if(!empty($item->featured) && !empty($this->information) && in_array('featured', $this->information)): ?>
          <span class="sesmusic_label_featured"><?php echo $this->translate("FEATURED"); ?></span>
          <?php endif; ?>
          <?php if(!empty($item->sponsored) && !empty($this->information) && in_array('sponsored', $this->information)): ?>
          <span class="sesmusic_label_sponsored"><?php echo $this->translate("SPONSORED"); ?></span>
          <?php endif; ?>
        </div>
      </div>
      <div class="hover_box">
        <a title="<?php echo $item->getTitle(); ?>" href="<?php echo $item->getHref(); ?>" class="sesmusic_grid_link"></a>
        <div class="hover_box_options">
          <?php if($this->viewer_id): ?>
            <?php if($this->canAddFavourite && !empty($this->information) && in_array('favourite', $this->information)): ?>
              <?php $isFavourite = Engine_Api::_()->getDbTable('favourites', 'sesmusic')->isFavourite(array('resource_type' => "sesmusic_album", 'resource_id' => $item->album_id)); ?>
              <a title='<?php echo $this->translate("Remove from Favorite") ?>' class="favorite-white favorite" id="sesmusic_album_unfavourite_<?php echo $item->album_id; ?>" style ='display:<?php echo $isFavourite ? "inline-block" : "none" ?>' href = "javascript:void(0);" onclick = "sesmusicFavourite('<?php echo $item->album_id; ?>', 'sesmusic_album');"><i class="fa fa-heart sesmusic_favourite"></i></a>
              <a title='<?php echo $this->translate("Add to Favorite") ?>' class="favorite-white favorite" id="sesmusic_album_favourite_<?php echo $item->album_id; ?>" style ='display:<?php echo $isFavourite ? "none" : "inline-block" ?>' href = "javascript:void(0);" onclick = "sesmusicFavourite('<?php echo $item->album_id; ?>', 'sesmusic_album');"><i class="fa fa-heart"></i></a>
              <input type="hidden" id="sesmusic_album_favouritehidden_<?php echo $item->album_id; ?>" value='<?php echo $isFavourite ? $isFavourite : 0; ?>' />
            <?php endif; ?>
            <?php if($this->canAddPlaylist && !empty($this->information) && in_array('addplaylist', $this->information)): ?>
              <a class="add-white" title='<?php echo $this->translate("Add to Playlist") ?>' href="javascript:void(0);" onclick="showPopUp('<?php echo $this->escape($this->url(array('module' =>'sesmusic', 'controller' => 'song', 'action'=>'append - songs','album_id' => $item->album_id, 'format' => 'smoothbox'), 'default' , true)); ?>'); return false;" ><i class="fa fa-plus"></i></a>
            <?php endif; ?>
            <?php if($this->albumlink && in_array('share', $this->albumlink) && !empty($this->information) && in_array('share', $this->information)): ?>
              <a class="share-white" title='<?php echo $this->translate("Share") ?>' href="javascript:void(0);" onclick="showPopUp('<?php echo $this->escape($this->url(array('module'=>'activity', 'controller'=>'index', 'action'=>'share', 'route'=>'default', 'type'=>'sesmusic_album', 'id' => $item->album_id, 'format' => 'smoothbox'), 'default' , true)); ?>'); return false;" ><i class="fa fa-share"></i></a>
            <?php endif; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </li>
  <?php endif; ?>
  <?php endforeach; ?>
</ul>
<?php endif; ?>
<?php elseif($this->content_type == 'sesmusic_albumsong'): ?>
<?php if(count($this->results) > 0) :?>
<ul class="sesmusic_side_block sesmusic_browse_listing">
  <?php foreach( $this->results as $album ): ?>
  <?php $item = Engine_Api::_()->getItem($album['resource_type'], $album['resource_id']); ?>
  <?php if($item && $this->viewType == 'listView'): ?>
  <li class="sesmusic_sidebar_list">
    <div class="sesmusic_sidebar_list_thumb">
      <?php if($item->photo_id): ?>
        <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon'), array('class' => 'sesmusic_sidebar_list_thumb')) ?>
      <?php else: ?>
       <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon'), array('class' => 'sesmusic_sidebar_list_thumb')) ?>
      <?php endif; ?>
    </div>
    <div class="sesmusic_sidebar_list_info">
      <div class="sesmusic_sidebar_list_title sesmusic_sidebar_list_song_title">
        <?php $songTitle = preg_replace('/[^a-zA-Z0-9\']/', ' ', $item->getTitle()); ?>
                  <?php $songTitle = str_replace("'", '', $songTitle); ?>
        <?php if($item->track_id): ?>
           <?php $uploadoption = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmusic.uploadoption', 'myComputer');
           $consumer_key =  Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmusic.scclientid'); ?>          
           <?php if(($uploadoption == 'both' || $uploadoption == 'soundCloud') && $consumer_key): ?>
           <?php $URL = "http://api.soundcloud.com/tracks/$item->track_id/stream?consumer_key=$consumer_key"; ?>
           <a class="sesmusic_sidebar_list_playbutton sesmusic_songslist_playbutton" href="javascript:void(0);" onclick="play_music('<?php echo $item->albumsong_id ?>', '<?php echo $URL; ?>', '<?php echo $songTitle; ?>');"><i class="fa fa-play"></i></a>
           <?php else: ?>
           <a class="sesmusic_sidebar_list_playbutton sesmusic_songslist_playbutton" href="javascript:void(0);"><i class="fa fa-play"></i></a>
           <?php endif; ?>
         <?php else: ?>
           <a class="sesmusic_sidebar_list_playbutton sesmusic_songslist_playbutton"  href="javascript:void(0);" onclick="play_music('<?php echo $item->albumsong_id ?>', '<?php echo $item->getFilePath(); ?>', '<?php echo $songTitle; ?>');"><i class="fa fa-play"></i></a>
         <?php endif; ?>
        <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
      </div>

      <?php if(!empty($this->information) && in_array('postedby', $this->information)): ?>
      <div class="sesmusic_sidebar_list_stats sesbasic_text_light">
        <?php echo $this->translate('by');?> <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>
      </div>
      <?php endif; ?>

      <?php if(!empty($this->information) && in_array('title', $this->information)): ?>
      <div class="sesmusic_sidebar_list_stats sesbasic_text_light">
        <i><?php echo $this->translate("in "); ?><?php echo $this->htmlLink($album->getHref(), $album->getTitle()) ?></i>
      </div>
      <?php endif; ?>      

      <?php if($this->showAlbumSongRating && !empty($this->information) && in_array('ratingCount', $this->information)): ?>
        <div class="sesmusic_sidebar_list_stats sesbasic_text_light" title="<?php echo $this->translate(array('%s rating', '%s ratings', $item->rating), $this->locale()->toNumber($item->rating)); ?>">
          <?php if( $item->rating > 0 ): ?>
          <?php for( $x=1; $x<= $item->rating; $x++ ): ?>
          <span class="sesbasic_rating_star_small fa fa-star"></span>
          <?php endfor; ?>
          <?php if( (round($item->rating) - $item->rating) > 0): ?>
          <span class="sesbasic_rating_star_small fa fa-star-half"></span>
          <?php endif; ?>
          <?php endif; ?>
        </div>
      <?php endif; ?>
      <div class="sesmusic_sidebar_list_stats sesmusic_list_stats sesbasic_text_light">
        <?php if (!empty($this->information) && in_array('commentCount', $this->information)) :?>
          <span title="<?php echo $this->translate(array('%s comment', '%s comments', $item->comment_count), $this->locale()->toNumber($item->comment_count)); ?>">
            <i class="fa fa-comment"></i>
            <?php echo $item->comment_count; ?>
          </span>
        <?php endif; ?>
        <?php if (!empty($this->information) && in_array('likeCount', $this->information)) : ?>
          <span title="<?php echo $this->translate(array('%s like', '%s likes', $item->like_count), $this->locale()->toNumber($item->like_count)); ?>">
            <i class="fa fa-thumbs-up"></i>
            <?php echo $item->like_count; ?>
          </span>
        <?php endif; ?>
        <?php if (!empty($this->information) && in_array('viewCount', $this->information)) : ?>
          <span title="<?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count)); ?>">
            <i class="fa fa-eye"></i>
            <?php echo $item->view_count; ?>
          </span>
        <?php endif; ?>
        <?php if (!empty($this->information) && in_array('downloadCount', $this->information)) : ?>
          <span title="<?php echo $this->translate(array('%s download', '%s downloads', $item->download_count), $this->locale()->toNumber($item->download_count)); ?>">
            <i class="fa fa-download"></i>
            <?php echo $item->download_count; ?>
          </span>
        <?php endif; ?>
      </div>
    </div>
  </li>
  <?php elseif($this->viewType == 'gridview'): ?>
  <li class="sesmusic_item_grid" style="width:<?php echo $this->width ?>px;">
    <div class="sesmusic_item_artwork" style="height:<?php echo $this->height ?>px;">
      <?php if($item->photo_id): ?>
        <?php echo $this->itemPhoto($item, 'thumb.profile'); ?>
      <?php else: ?>
       <?php echo $this->itemPhoto($item, 'thumb.normal'); ?>
      <?php endif; ?>
      <a href="<?php echo $item->getHref(); ?>" class="transparentbg"></a>
      <div class="sesmusic_item_info">     

        <div class="sesmusic_item_info_title">
          <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
          <?php if(!empty($this->information) && in_array('title', $this->information)): ?>
          <i><?php echo $this->translate("in "); ?><?php echo $this->htmlLink($album->getHref(), $album->getTitle()) ?></i>
          <?php endif; ?>
        </div>          

        <?php if(!empty($this->information) && in_array('postedby', $this->information)): ?>
        <div class="sesmusic_item_info_owner">
          <?php echo $this->translate('by');?> <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>
        </div>
        <?php endif; ?>

        <div class="sesmusic_item_info_stats">
          <?php if (!empty($this->information) && in_array('commentCount', $this->information)) :?>
            <span>
              <?php echo $item->comment_count; ?>
              <i class="fa fa-comment"></i>
            </span>
          <?php endif; ?>
          <?php if (!empty($this->information) && in_array('likeCount', $this->information)) : ?>
            <span>
              <?php echo $item->like_count; ?>
              <i class="fa fa-thumbs-up"></i>
            </span>
          <?php endif; ?>
          <?php if (!empty($this->information) && in_array('viewCount', $this->information)) : ?>
            <span>
              <?php echo $item->view_count; ?>
              <i class="fa fa-eye"></i>
            </span>
          <?php endif; ?>
          <?php if (!empty($this->information) && in_array('downloadCount', $this->information)) : ?>
            <span>
              <?php echo $item->download_count; ?>
              <i class="fa fa-download"></i>
            </span>
          <?php endif; ?>
        </div>

        <?php if ($this->showAlbumSongRating && !empty($this->information) && in_array('ratingCount', $this->information)) : ?>
          <div class="sesmusic_item_info_rating">
            <?php if( $item->rating > 0 ): ?>
            <?php for( $x=1; $x<= $item->rating; $x++ ): ?>
            <span class="sesbasic_rating_star_small fa fa-star"></span>
            <?php endfor; ?>
            <?php if( (round($item->rating) - $item->rating) > 0): ?>
            <span class="sesbasic_rating_star_small fa fa-star-half"></span>
            <?php endif; ?>
            <?php endif; ?>
          </div>
        <?php endif; ?>

        <?php // Featured and Sponsored and Hot Label Icon ?>
        <div class="sesmusic_item_info_label">
          <?php if(!empty($item->hot) && !empty($this->information) && in_array('hot', $this->information)): ?>
          <span class="sesmusic_label_hot"><?php echo $this->translate("HOT"); ?></span>
          <?php endif; ?>
          <?php if(!empty($item->featured) && !empty($this->information) && in_array('featured', $this->information)): ?>
          <span class="sesmusic_label_featured"><?php echo $this->translate("FEATURED"); ?></span>
          <?php endif; ?>
          <?php if(!empty($item->sponsored) && !empty($this->information) && in_array('sponsored', $this->information)): ?>
          <span class="sesmusic_label_sponsored"><?php echo $this->translate("SPONSORED"); ?></span>
          <?php endif; ?>
        </div>
      </div>
      <div class="hover_box">
        <?php if($item->track_id): ?>
	        <?php $uploadoption = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmusic.uploadoption', 'myComputer');
	        $consumer_key =  Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmusic.scclientid'); ?>          
	        <?php if(($uploadoption == 'both' || $uploadoption == 'soundCloud') && $consumer_key): ?>
	        <?php $URL = "http://api.soundcloud.com/tracks/$item->track_id/stream?consumer_key=$consumer_key"; ?>
	          <a class="sesmusic_play_button" href="javascript:void(0);" onclick="play_music('<?php echo $item->albumsong_id ?>', '<?php echo $URL; ?>', '<?php echo preg_replace("/[^A-Za-z0-9\-]/", "", $item->getTitle()); ?>');"><i class="fa fa-play-circle"></i></a>
	        <?php else: ?>
	          <a class="sesmusic_play_button" href="javascript:void(0);"><i class="fa fa-play-circle"></i></a>
	        <?php endif; ?>
	      <?php else: ?>
	        <a class="sesmusic_play_button" href="javascript:void(0);" onclick="play_music('<?php echo $item->albumsong_id ?>', '<?php echo $item->getFilePath(); ?>', '<?php echo $item->getTitle(); ?>');"><i class="fa fa-play-circle"></i></a>
	      <?php endif; ?>
        <div class="hover_box_options">
          <?php if($this->viewer_id): ?>
            <?php if($this->addfavouriteAlbumSong): ?>
              <?php $isFavourite = Engine_Api::_()->getDbTable('favourites', 'sesmusic')->isFavourite(array('resource_type' => "sesmusic_albumsong", 'resource_id' => $item->albumsong_id)); ?>                            <a title='<?php echo $this->translate("Remove from Favorite") ?>' class="favorite-white favorite" id="sesmusic_albumsong_unfavourite_<?php echo $item->albumsong_id; ?>" style ='display:<?php echo $isFavourite ? "inline-block" : "none" ?>' href = "javascript:void(0);" onclick = "sesmusicFavourite('<?php echo $item->albumsong_id; ?>', 'sesmusic_albumsong');"><i class="fa fa-heart"></i></a>
              <a title='<?php echo $this->translate("Add to Favorite") ?>' class="favorite-white favorite" id="sesmusic_albumsong_favourite_<?php echo $item->albumsong_id; ?>" style ='display:<?php echo $isFavourite ? "none" : "inline-block" ?>' href = "javascript:void(0);" onclick = "sesmusicFavourite('<?php echo $item->albumsong_id; ?>', 'sesmusic_albumsong');"><i class="fa fa-heart"></i></a>
              <input type ="hidden" id = "sesmusic_albumsong_favouritehidden_<?php echo $item->albumsong_id; ?>" value = '<?php echo $isFavourite ? $isFavourite : 0; ?>' />
            <?php endif; ?>
            <?php if($this->canAddPlaylistAlbumSong): ?>
              <a class="add-white" title='<?php echo $this->translate("Add to Playlist") ?>' href="javascript:void(0);" onclick="showPopUp('<?php echo $this->escape($this->url(array('module' =>'sesmusic', 'controller' => 'song', 'action'=>'append-songs','album_id' => $item->albumsong_id, 'format' => 'smoothbox'), 'default' , true)); ?>'); return false;" ><i class="fa fa-plus"></i></a>
            <?php endif; ?>
            <?php if($this->songlink && in_array('share', $this->songlink)): ?>
            <a class="share-white" title='<?php echo $this->translate("Share") ?>' href="javascript:void(0);" onclick="showPopUp('<?php echo $this->escape($this->url(array('module'=>'activity', 'controller'=>'index', 'action'=>'share', 'route'=>'default', 'type'=>'sesmusic_albumsong', 'id' => $item->albumsong_id, 'format' => 'smoothbox'), 'default' , true)); ?>'); return false;" ><i class="fa fa-share"></i></a>
            <?php endif; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </li>
  <?php endif; ?>
  <?php endforeach; ?>
</ul>
<?php endif; ?>
<?php endif; ?>
