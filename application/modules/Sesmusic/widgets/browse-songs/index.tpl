<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesmusic/externals/scripts/favourites.js'); ?> 
<script type="text/javascript">
  
  function showPopUp(url) {
    Smoothbox.open(url);
    parent.Smoothbox.close;
  }

  function loadMore() {
  
    if ($('load_more'))
      $('load_more').style.display = "<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->count == 0 ? 'none' : '' ) ?>";

    if(document.getElementById('load_more'))
      document.getElementById('load_more').style.display = 'none';
    
    if(document.getElementById('underloading_image'))
     document.getElementById('underloading_image').style.display = '';

    en4.core.request.send(new Request.HTML({
      method: 'post',              
      'url': en4.core.baseUrl + 'widget/index/mod/sesmusic/name/browse-songs',
      'data': {
        format: 'html',
        page: "<?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>",
        viewmore: 1,
        params: '<?php echo json_encode($this->params); ?>',
        
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        document.getElementById('results_data').innerHTML = document.getElementById('results_data').innerHTML + responseHTML;
        
        if(document.getElementById('load_more'))
          document.getElementById('load_more').destroy();
        
        if(document.getElementById('underloading_image'))
         document.getElementById('underloading_image').destroy();
       
        if(document.getElementById('loadmore_list'))
         document.getElementById('loadmore_list').destroy();
      }
    }));
    return false;
  }
</script>

<?php if(count($this->paginator) > 0): ?>
  <?php if (empty($this->viewmore)): ?>
    <h4><?php echo $this->translate(array('%s song found.', '%s songs found.', $this->paginator->getTotalItemCount()), $this->locale()->toNumber($this->paginator->getTotalItemCount())); ?></h4>
    <?php if($this->viewType == 'listview'): ?>
      <ul class="clear sesmusic_songslist_container playlist" id= "results_data">
    <?php else: ?>
      <ul class="sesmusic_browse_listing" id= "results_data">
    <?php endif; ?>
  <?php endif; ?>
  <?php if($this->viewType == 'listview'): ?>
  <?php foreach ($this->paginator as $song): ?>
    <?php if(!empty($song)): ?>
    <li class="sesmusic_songslist sesbasic_clearfix">
      <div class="sesmusic_songslist_photo">
         <?php if($song->photo_id): ?>
           <?php echo $this->htmlLink($song->getHref(), $this->itemPhoto($song, 'thumb.profile'), array()); ?>
         <?php else: ?>
          <?php $albumItem = Engine_Api::_()->getItem('sesmusic_albums', $song->album_id); ?>
          <?php echo $this->htmlLink($song->getHref(), $this->itemPhoto($song, 'thumb.normal'), array()); ?>
         <?php endif; ?>
         <?php if($song->hot || $song->featured || $song->sponsored): ?>
          <div class="sesmusic_item_info_label">
            <?php if($song->hot && !empty($this->information) && in_array('hot', $this->information)): ?>
              <span class="sesmusic_label_hot"><?php echo $this->translate("HOT"); ?></span>
            <?php endif; ?>
            <?php if($song->featured && !empty($this->information) && in_array('featured', $this->information)): ?>
            <span class="sesmusic_label_featured"><?php echo $this->translate("FEATURED"); ?></span>
            <?php endif; ?>
            <?php if($song->sponsored && !empty($this->information) && in_array('sponsored', $this->information)): ?>
            <span class="sesmusic_label_sponsored"><?php echo $this->translate("SPONSORED"); ?></span>
            <?php endif; ?>
          </div>
         <?php endif; ?>
       </div>
      <div class="sesmusic_songslist_info">
        <div class="sesmusic_songslist_info_top sesbasic_clearfix">
          <div class="sesmusic_songslist_playbutton">
            <?php $songTitle = preg_replace('/[^a-zA-Z0-9\']/', ' ', $song->getTitle()); ?>
              <?php $songTitle = str_replace("'", '', $songTitle); ?>
            <?php if($song->track_id): ?>
              
              <?php $uploadoption = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmusic.uploadoption', 'myComputer');
              $consumer_key =  Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmusic.scclientid'); ?>          
              <?php if(($uploadoption == 'both' || $uploadoption == 'soundCloud') && $consumer_key): ?>
              <?php $URL = "http://api.soundcloud.com/tracks/$song->track_id/stream?consumer_key=$consumer_key"; ?>
                <a href="javascript:void(0);" onclick="play_music('<?php echo $song->albumsong_id ?>', '<?php echo $URL; ?>', '<?php echo $songTitle; ?>');"><i class="fa fa-play"></i></a>
                <?php elseif($consumer_key): ?>                
                  <?php $URL = "http://api.soundcloud.com/tracks/$song->track_id/stream?consumer_key=$consumer_key"; ?>
                  <a href="javascript:void(0);" onclick="play_music('<?php echo $song->albumsong_id ?>', '<?php echo $URL; ?>', '<?php echo $songTitle; ?>');"><i class="fa fa-play"></i></a>
                <?php endif; ?>
            <?php else: ?>
              <a href="javascript:void(0);" onclick="play_music('<?php echo $song->albumsong_id ?>', '<?php echo $song->getFilePath(); ?>', '<?php echo $songTitle; ?>');"><i class="fa fa-play"></i></a>
            <?php endif; ?>  
          </div> 
          <div class="sesmusic_songslist_songdetail">
            <?php if(!empty($this->information) && in_array('category', $this->information) && $song->category_id): ?>
              <div class="sesmusic_list_category floatR">
                <?php $catName = Engine_Api::_()->getDbTable('categories', 'sesmusic')->getColumnName(array('column_name' => 'category_name', 'category_id' => $song->category_id, 'param' => 'song')); ?>
                <a href="<?php echo $this->url(array('action' => 'browse'), 'sesmusic_songs', true).'?category_id='.urlencode($song->category_id) ; ?>"><?php echo $catName; ?></a>
              </div>
            <?php endif; ?>
            <?php if(!empty($this->information) && in_array('title', $this->information)): ?>
              <div class="sesmusic_songslist_songname">
                <?php echo $this->htmlLink($song->getHref(), $song->getTitle(), array('class' => 'music_player_tracks_url', 'type' => 'audio', 'rel' => $song->song_id)); ?>
              </div>
            <?php endif; ?>
            <?php if(!empty($this->information) && in_array('postedby', $this->information)): ?>
              <div class="sesmusic_songslist_author sesbasic_text_light">
                <?php $album = Engine_Api::_()->getItem('sesmusic_albums', $song->album_id); ?>
                <?php echo $this->translate('by %s', $this->htmlLink($album->getOwner(), $album->getOwner()->getTitle())) ?><?php echo $this->translate(' on %s', $this->timestamp($song->creation_date)); ?><?php echo $this->translate(' in %s', $this->htmlLink($album->getHref(), $album->getTitle())); ?>
              </div>
            <?php endif; ?>
            <?php if($this->showAlbumSongRating && !empty($this->information) && in_array('ratingStars', $this->information)): ?>
              <div class="sesmusic_songslist_rating" title="<?php echo $this->translate(array('%s rating', '%s ratings', $song->rating), $this->locale()->toNumber($song->rating)); ?>">
                <?php if( $song->rating > 0 ): ?>
                  <?php for( $x=1; $x<= $song->rating; $x++ ): ?>
                    <span class="sesbasic_rating_star_small fa fa-star"></span>
                  <?php endfor; ?>
                  <?php if( (round($song->rating) - $song->rating) > 0): ?>
                    <span class="sesbasic_rating_star_small fa fa-star-half"></span>
                  <?php endif; ?>
                <?php endif; ?>      
              </div>
            <?php endif; ?>
            <?php if(!empty($this->information) && in_array('artists', $this->information)): ?>
              <div class="sesmusic_songslist_artist clear sesbasic_text_light">
                <?php if($song->artists): 
                $artists = json_decode($song->artists); 
                if($artists): ?>
                  <?php echo $this->translate("Artists:"); ?>
                  <?php $artists_array = Engine_Api::_()->getDbTable('artists', 'sesmusic')->getArtists($artists); ?>
                  <?php $artist_name = ''; ?>
                  <?php foreach($artists_array as $key => $artist):  ?>
                      <?php $artist_name .= $this->htmlLink(array('module'=>'sesmusic', 'controller'=>'artist', 'action'=>'view', 'route'=>'default', 'artist_id' => $key), $artist) . ', '; ?>
                  <?php endforeach; ?> 
                  <?php $artist_name = trim($artist_name); $artist_name = rtrim($artist_name, ','); echo $artist_name; ?>
                  <?php endif; ?>
                <?php endif; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
        
        <div class="sesmusic_songslist_info_bottom">
          <div class="sesmusic_songslist_options sesmusic_options_buttons">
            <?php if( $this->viewer()->getIdentity()): ?>
              <?php if($this->canAddPlaylistAlbumSong && $this->information && in_array('addplaylist', $this->information)): ?>
                <?php //echo $this->htmlLink(array('route' => 'sesmusic_albumsong_specific', 'action' => 'append', 'albumsong_id' => $song->albumsong_id), '', array('class' => 'smoothbox fa fa-plus')); ?>
                <a title="<?php echo $this->translate('Add to Playlist');?>" href="javascript:void(0);" onclick="showPopUp('<?php echo $this->escape($this->url(array('action'=>'append','albumsong_id' => $song->albumsong_id, 'format' => 'smoothbox'), 'sesmusic_albumsong_specific' , true)); ?>'); return false;" class="fa fa-plus"><?php echo $this->translate('Add to Playlist');?></a>
              <?php endif; ?>

              <?php if($song->download && !$song->track_id && !$song->song_url && $this->downloadAlbumSong && $this->information && in_array('downloadIcon', $this->information)): ?>
                <?php echo $this->htmlLink(array('route' => 'sesmusic_albumsong_specific', 'action' => 'download-song', 'albumsong_id' => $song->albumsong_id), $this->translate("Download"), array('class' => ' fa fa-download')); ?>                                   
              <?php elseif($song->download && $this->downloadAlbumSong && $this->information && in_array('downloadIcon', $this->information)): ?>
                <?php $file = Engine_Api::_()->getItem('storage_file', $this->albumsong->file_id); ?>
                <?php if($file->mime_minor): ?>
                <?php $consumer_key =  Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmusic.scclientid');
                $downloadURL = 'http://api.soundcloud.com/tracks/' . $this->albumsong->track_id . '/download?client_id=' . $consumer_key;  ?>
                <a class='fa fa-download' href='<?php echo $downloadURL; ?>' target="_blank"></a>
                <?php endif; ?>
              <?php endif; ?>
              
              <?php if(!empty($this->songlink) && in_array('share', $this->songlink) && $this->information && in_array('share', $this->information)): ?>
              <a class="fa fa-share" title='<?php echo $this->translate("Share") ?>' href="javascript:void(0);" onclick="showPopUp('<?php echo $this->escape($this->url(array('module'=>'activity', 'controller'=>'index', 'action'=>'share', 'route'=>'default', 'type'=>'sesmusic_albumsong', 'id' => $song->getIdentity(), 'format' => 'smoothbox'), 'default' , true)); ?>'); return false;" ><?php echo $this->translate("Share"); ?></a>
              <?php endif; ?>
              
              <?php if(!empty($this->songlink) && in_array('report', $this->songlink) && $this->information && in_array('report', $this->information)): ?>
              <a class="fa fa-flag" title='<?php echo $this->translate("Share") ?>' href="javascript:void(0);" onclick="showPopUp('<?php echo $this->escape($this->url(array('module'=>'core', 'controller'=>'report', 'action'=>'create', 'route'=>'default', 'subject'=> $song->getGuid(), 'format' => 'smoothbox'), 'default' , true)); ?>'); return false;" ><?php echo $this->translate("Report"); ?></a>
              <?php endif; ?>
             
            <?php if($this->canAddFavouriteAlbumSong && $this->information && in_array('favourite', $this->information)): ?>
              <?php $isFavourite = Engine_Api::_()->getDbTable('favourites', 'sesmusic')->isFavourite(array('resource_type' => "sesmusic_albumsong", 'resource_id' => $song->albumsong_id)); ?>
              <a class="fa fa-heart sesmusic_favourite" id="sesmusic_albumsong_unfavourite_<?php echo $song->getIdentity(); ?>" style ='display:<?php echo $isFavourite ? "inline-block" : "none" ?>' href = "javascript:void(0);" onclick = "sesmusicFavourite('<?php echo $song->getIdentity(); ?>', 'sesmusic_albumsong');"><?php echo $this->translate("Remove from Favorites") ?></a>
              <a class="fa fa-heart" id="sesmusic_albumsong_favourite_<?php echo $song->getIdentity(); ?>" style ='display:<?php echo $isFavourite ? "none" : "inline-block" ?>' href = "javascript:void(0);" onclick = "sesmusicFavourite('<?php echo $song->getIdentity(); ?>', 'sesmusic_albumsong');"><?php echo $this->translate("Add to Favorite") ?></a>
              <input type="hidden" id="sesmusic_albumsong_favouritehidden_<?php echo $song->getIdentity(); ?>" value='<?php echo $isFavourite ? $isFavourite : 0; ?>' />
            <?php endif; ?>
            <?php endif; ?>    
          </div>
          
          <div class="sesmusic_songslist_songstats sesbasic_text_light">
            <?php 
             $information = '';   
             if(!empty($this->information) && in_array('playCount', $this->information))
             $information .= '<span title="Plays"><i class="fa fa-play"></i>' .$song->play_count. '</span>';
             
             if(!empty($this->information) && in_array('downloadCount', $this->information))
               $information .= '<span title="Downloads"><i class="fa fa-download"></i>' .$song->download_count. '</span>';
               
             if(!empty($this->information) && in_array('favouriteCount', $this->information))
               $information .= '<span title="Favourites"><i class="fa fa-heart"></i>' .$song->favourite_count. '</span>';
               
             if(!empty($this->information) && in_array('likeCount', $this->information))
               $information .= '<span title="Likes"><i class="fa fa-thumbs-up"></i>' .$song->like_count. '</span>'; 
               
             if(!empty($this->information) && in_array('commentCount', $this->information))
               $information .= '<span title="Comments"><i class="fa fa-comment"></i>' .$song->comment_count. '</span>';
               
             if(!empty($this->information) && in_array('viewCount', $this->information))
               $information .= '<span title="Views"><i class="fa fa-eye"></i>' .$song->view_count. '</span>';
             ?>
              <?php echo $information ?>
            </div>
        </div>
      </div>
    </li>
    <?php endif; ?>
  <?php endforeach; ?>
  <?php elseif($this->viewType == 'gridview'): ?>
    <?php foreach( $this->paginator as $item ):  ?>
      <?php $album = Engine_Api::_()->getItem('sesmusic_albums', $item->album_id); ?>
      <li id="thumbs-photo-<?php echo $item->photo_id ?>" class="sesmusic_item_grid" style="width:<?php echo str_replace('px','',$this->width).'px'; ?>;">            
          <div class="sesmusic_item_artwork" style="height:<?php echo str_replace('px','',$this->height).'px'; ?>;">
            <?php if($item->photo_id): ?>
              <?php echo $this->itemPhoto($item, 'thumb.profile'); ?>
            <?php else: ?>
             <?php $album = Engine_Api::_()->getItem('sesmusic_albums', $item->album_id); ?>
             <?php echo $this->itemPhoto($item, 'thumb.profile'); ?>
            <?php endif; ?>
            <a href="<?php echo $item->getHref(); ?>" class="transparentbg"></a>
            <div class="sesmusic_item_info">     
              <div class="sesmusic_item_info_title">
                <?php if(!empty($this->information) && in_array('title', $this->information)): ?>
                <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
                
                <?php $album = Engine_Api::_()->getItem('sesmusic_albums', $item->album_id); ?>
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

              <?php if ($this->showAlbumSongRating && !empty($this->information) && in_array('ratingStars', $this->information)) : ?>
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
                  <?php if($this->canaddfavouriteAlbumSong && !empty($this->information) && in_array('favourite', $this->information)): ?>
                  <?php $isFavourite = Engine_Api::_()->getDbTable('favourites', 'sesmusic')->isFavourite(array('resource_type' => "sesmusic_albumsong", 'resource_id' => $item->albumsong_id)); ?>              
                  <a title='<?php echo $this->translate("Remove from Favorite") ?>' class="favorite-white favorite" id="sesmusic_albumsong_unfavourite_<?php echo $item->albumsong_id; ?>" style ='display:<?php echo $isFavourite ? "inline-block" : "none" ?>' href = "javascript:void(0);" onclick = "sesmusicFavourite('<?php echo $item->albumsong_id; ?>', 'sesmusic_albumsong');"><i class="fa fa-heart"></i></a>
                  <a title='<?php echo $this->translate("Add to Favorite") ?>' class="favorite-white favorite" id="sesmusic_albumsong_favourite_<?php echo $item->albumsong_id; ?>" style ='display:<?php echo $isFavourite ? "none" : "inline-block" ?>' href = "javascript:void(0);" onclick = "sesmusicFavourite('<?php echo $item->albumsong_id; ?>', 'sesmusic_albumsong');"><i class="fa fa-heart"></i></a>
                  <input type ="hidden" id = "sesmusic_albumsong_favouritehidden_<?php echo $item->albumsong_id; ?>" value = '<?php echo $isFavourite ? $isFavourite : 0; ?>' />
                <?php endif; ?>

                <?php if($this->canAddPlaylistAlbumSong && !empty($this->information) && in_array('addplaylist', $this->information)): ?>
                <a title="<?php echo $this->translate('Add to Playlist');?>" href="javascript:void(0);" onclick="showPopUp('<?php echo $this->escape($this->url(array('action'=>'append','albumsong_id' => $item->albumsong_id, 'format' => 'smoothbox'), 'sesmusic_albumsong_specific' , true)); ?>'); return false;" class="add-white"><i class="fa fa-plus"></i></a>
                <?php endif; ?>
                <?php if(!empty($this->songlink) && in_array('share', $this->songlink) && !empty($this->information) && in_array('share', $this->information)): ?>
                <a class="share-white" title="Share" href="javascript:void(0);" onclick="showPopUp('<?php echo $this->escape($this->url(array('module'=>'activity', 'controller'=>'index', 'action'=>'share', 'route'=>'default', 'type'=>'sesmusic_albumsong', 'id' => $item->albumsong_id, 'format' => 'smoothbox'), 'default' , true)); ?>'); return false;" ><i class="fa fa-share"></i></a>
                <?php endif; ?>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </li>
      <?php endforeach;?>  
  <?php endif; ?>

  <?php //if($this->paginationType == 1): ?>
    <?php if (!empty($this->paginator) && $this->paginator->count() > 1): ?>
      <?php if ($this->paginator->getCurrentPageNumber() < $this->paginator->count()): ?>
        <div class="clr" id="loadmore_list"></div>
        <div class="sesbasic_view_more" id="load_more" onclick="loadMore();" style="display: block;">
          <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array('id' => 'feed_viewmore_link', 'class' => 'buttonlink icon_viewmore')); ?>
        </div>
        <div class="sesbasic_view_more_loading" id="underloading_image" style="display: none;">
          <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' style='margin-right: 5px;' />
          <?php echo $this->translate("Loading ...") ?>
        </div>
      <?php endif; ?>
     <?php endif; ?>
  <?php //else: ?>
    <?php //echo $this->paginationControl($this->paginator, null, null, array('pageAsQuery' => true, 'query' => $this->params)); ?>
  <?php //endif; ?>  
<?php if (empty($this->viewmore)): ?>
</ul>
<?php endif; ?>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are currently no songs uploaded yet.') ?>
    </span>
  </div>
<?php endif; ?>

<?php if (empty($this->viewmore)): ?>
  <script type="text/javascript">
    $$('.core_main_sesmusic').getParent().addClass('active');
  </script>
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
            loadMore();
        }
      }
      window.addEvent('scroll', function() { 
        ScrollLoader(); 
      });
    });    
  </script>
<?php endif; ?>