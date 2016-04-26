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
<?php $categoryTable = Engine_Api::_()->getDbTable('categories', 'sesmusic'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesmusic/externals/scripts/favourites.js'); ?> 
<script type="text/javascript">
  function showPopUp(url) {
    Smoothbox.open(url);
    parent.Smoothbox.close;
  }
</script>

<script type="text/javascript">
  function loadMore() {
  
    if ($('view_more'))
      $('view_more').style.display = "<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() || $this->count == 0 ? 'none' : '' ) ?>";

    if(document.getElementById('view_more'))
      document.getElementById('view_more').style.display = 'none';
    
    if(document.getElementById('loading_image'))
     document.getElementById('loading_image').style.display = '';

    en4.core.request.send(new Request.HTML({
      method: 'post',              
      'url': en4.core.baseUrl + 'widget/index/mod/sesmusic/name/manage-music-albums',
      'data': {
        format: 'html',
        page: "<?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>",
        viewmore: 1,
        params: '<?php echo json_encode($this->all_params); ?>',
        
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        document.getElementById('artists_results').innerHTML = document.getElementById('artists_results').innerHTML + responseHTML;
        
        if(document.getElementById('view_more'))
          document.getElementById('view_more').destroy();
        
        if(document.getElementById('loading_image'))
         document.getElementById('loading_image').destroy();
               if(document.getElementById('loadmore_list'))
         document.getElementById('loadmore_list').destroy();
      }
    }));
    return false;
  }
</script>

<?php if(count($this->paginator) > 0): ?>
  <?php if (empty($this->viewmore)): ?>
    
    <?php if($this->viewType == 'gridview'): ?>
     <ul class="sesmusic_browse_listing clear" id= "artists_results">
    <?php else: ?>
      <ul class="sesmusic_list" id= "artists_results">
    <?php endif; ?>
  <?php endif; ?>
  <?php if($this->viewType == 'gridview'): ?>
  <?php foreach ($this->paginator as $album): ?>
    <?php $album = Engine_Api::_()->getItem('sesmusic_album', $album->resource_id); if($album): ?>
    <li id="music_playlist_item_<?php echo $album->getIdentity() ?>" class="sesmusic_item_grid" style="width:<?php echo $this->width ?>px;">
      <div class="sesmusic_item_artwork" style="height:<?php echo $this->height ?>px;">
        <?php echo $this->htmlLink($album, $this->itemPhoto($album, 'thumb.profile') ) ?>
        <a href="<?php echo $album->getHref(); ?>" class="transparentbg"></a>
        <div class="sesmusic_item_info">
          <?php if(!empty($this->information) && in_array('title', $this->information)): ?>
            <div class="sesmusic_item_info_title">
              <?php echo $this->htmlLink($album->getHref(), $album->getTitle()) ?>
            </div>
          <?php endif; ?>          
          <?php if(!empty($this->information) && in_array('postedby', $this->information)): ?>
            <div class="sesmusic_item_info_owner">
              <?php echo $this->translate('by %s', $this->htmlLink($album->getOwner(), $album->getOwner()->getTitle())) ?>
            </div>
          <?php endif; ?>
          <div class="sesmusic_item_info_stats">
            <?php if(!empty($this->information) && in_array('commentCount', $this->information)): ?>
              <span>
                <?php echo $this->translate(array($album->comment_count), $this->locale()->toNumber($album->comment_count)) ?>
                <i class="fa fa-comment"></i>
              </span>
            <?php endif; ?>
            <?php if(!empty($this->information) && in_array('likeCount', $this->information)): ?>
              <span>
                <?php echo $this->translate(array($album->like_count), $this->locale()->toNumber($album->like_count)) ?>
                <i class="fa fa-thumbs-up"></i>
              </span>
            <?php endif; ?>
            <?php if(!empty($this->information) && in_array('viewCount', $this->information)): ?>
              <span>
                <?php echo $this->translate(array($album->view_count), $this->locale()->toNumber($album->view_count)) ?>
                <i class="fa fa-eye"></i>
              </span>
            <?php endif; ?>            
            <?php if (!empty($this->information) && in_array('songCount', $this->information)) : ?>
              <span>
              <?php echo $this->translate(array($album->song_count), $this->locale()->toNumber($album->song_count)) ?>
              <i class="fa fa-music"></i>
              </span>
            <?php endif; ?>            
          </div>          
          <?php if ($this->showRating && !empty($this->information) && in_array('ratingStars', $this->information)) : ?>
            <div class="sesmusic_item_info_rating">
              <?php if( $album->rating > 0 ): ?>
                <?php for( $x=1; $x<= $album->rating; $x++ ): ?>
                  <span class="sesbasic_rating_star_small fa fa-star"></span>
                <?php endfor; ?>
                <?php if( (round($album->rating) - $album->rating) > 0): ?>
                  <span class="sesbasic_rating_star_small fa fa-star-half"></span>
                <?php endif; ?>
              <?php endif; ?>
            </div>
          <?php endif; ?>          
          <div class="sesmusic_item_info_label">
            <?php if($album->hot && !empty($this->information) && in_array('hot', $this->information)): ?>
              <span class="sesmusic_label_hot"><?php echo $this->translate("HOT"); ?></span>
            <?php endif; ?>
            <?php if($album->featured && !empty($this->information) && in_array('featured', $this->information)): ?>
            <span class="sesmusic_label_featured"><?php echo $this->translate("FEATURED"); ?></span>
            <?php endif; ?>
            <?php if($album->sponsored && !empty($this->information) && in_array('sponsored', $this->information)): ?>
            <span class="sesmusic_label_sponsored"><?php echo $this->translate("SPONSORED"); ?></span>
            <?php endif; ?>
          </div>
        </div>
        <div class="hover_box">
          <a title="<?php echo $album->getTitle(); ?>" href="<?php echo $album->getHref(); ?>" class="sesmusic_grid_link"></a>
          <div class="hover_box_options">
            <?php if($this->viewer_id): ?>
              <?php $isFavourite = Engine_Api::_()->getDbTable('favourites', 'sesmusic')->isFavourite(array('resource_type' => "sesmusic_album", 'resource_id' => $album->album_id)); ?>
              <?php if($this->canAddFavourite && !empty($this->information) && in_array('favourite', $this->information)): ?>
                <a title='<?php echo $this->translate("Remove from Favorite") ?>' class="favorite-white favorite" id="sesmusic_album_unfavourite_<?php echo $album->album_id; ?>" style ='display:<?php echo $isFavourite ? "inline-block" : "none" ?>' href = "javascript:void(0);" onclick = "sesmusicFavourite('<?php echo $album->album_id; ?>', 'sesmusic_album');"><i class="fa fa-heart sesmusic_favourite"></i></a>
                <a title='<?php echo $this->translate("Add to Favorite") ?>' class="favorite-white favorite" id="sesmusic_album_favourite_<?php echo $album->album_id; ?>" style ='display:<?php echo $isFavourite ? "none" : "inline-block" ?>' href = "javascript:void(0);" onclick = "sesmusicFavourite('<?php echo $album->album_id; ?>', 'sesmusic_album');"><i class="fa fa-heart"></i></a>
                <input type="hidden" id="sesmusic_album_favouritehidden_<?php echo $album->album_id; ?>" value='<?php echo $isFavourite ? $isFavourite : 0; ?>' />
              <?php endif; ?>                

              <?php if($this->canAddPlaylist && !empty($this->information) && in_array('addplaylist', $this->information)): ?>
              <a class="add-white" title='<?php echo $this->translate("Add to Playlist"); ?>' href="javascript:void(0);" onclick="showPopUp("<?php echo $this->escape($this->url(array('module' =>'sesmusic', 'controller' => 'song', 'action'=>'append - songs','album_id' => $album->album_id, 'format' => 'smoothbox'), 'default' , true)); ?>"); return false;" ><i class="fa fa-plus"></i></a>
              <?php endif; ?>
              <?php if($this->albumlink && in_array('share', $this->albumlink)  && !empty($this->information) && in_array('share', $this->information)): ?>
              <a class="share-white" title='<?php echo $this->translate("Share") ?>' href="javascript:void(0);" onclick="showPopUp("<?php echo $this->escape($this->url(array('module'=>'activity', 'controller'=>'index', 'action'=>'share', 'route'=>'default', 'type'=>'sesmusic_album', 'id' => $album->album_id, 'format' => 'smoothbox'), 'default' , true)); ?>"); return false;" ><i class="fa fa-share"></i></a>
              <?php endif; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </li>
	<?php endif; ?>
  <?php endforeach; ?>
  <?php elseif($this->viewType == 'listView'): ?>
      <?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/styles/customscrollbar.css'); ?>
      <?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/jquery.min.js'); ?>
      <?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/customscrollbar.concat.min.js'); ?>
      <?php foreach ($this->paginator as $album): ?>
      <?php $album = Engine_Api::_()->getItem('sesmusic_album', $album->resource_id); ?>
      <?php if($album): ?>
      <li id="music_playlist_item_<?php echo $album->getIdentity() ?>" class="sesbasic_clearfix">
        <div class="sesmusic_list_artwork">
          <?php echo $this->htmlLink($album->getHref(), $this->itemPhoto($album, 'thumb.normal'), array('class' => 'thumb')) ?>
          <div class="sesmusic_item_info_label">
            <?php if($album->hot && !empty($this->information) && in_array('hot', $this->information)): ?>
              <span class="sesmusic_label_hot"><?php echo $this->translate('HOT'); ?></span>
            <?php endif; ?>
            <?php if($album->featured && !empty($this->information) && in_array('featured', $this->information)): ?>
            <span class="sesmusic_label_featured"><?php echo $this->translate('FEATURED'); ?></span>
            <?php endif; ?>
            <?php if($album->sponsored && !empty($this->information) && in_array('sponsored', $this->information)): ?>
            <span class="sesmusic_label_sponsored"><?php echo $this->translate('SPONSORED'); ?></span>
            <?php endif; ?>
          </div>
        </div>
        <div class="sesmusic_list_info">
          <?php if(!empty($this->information) && in_array('title', $this->information)): ?>
            <div class="sesmusic_list_info_title">
              <?php echo $this->htmlLink($album->getHref(), $album->getTitle()) ?>
            </div>
          <?php endif; ?>
          
          <?php if(!empty($this->information) && in_array('postedby', $this->information)): ?>
            <div class="sesmusic_list_info_stats sesbasic_text_light">
              <?php echo $this->translate('Album By %s', $this->htmlLink($album->getOwner(), $album->getOwner()->getTitle())) ?>
              <?php echo $this->translate('Released %s ', $album->creation_date) ?>
            </div>
          <?php endif; ?>
          
          <?php if($album->category_id && !empty($this->information) && in_array('category', $this->information)): ?>
            <div class="sesmusic_list_info_stats sesbasic_text_light">
              <?php $catName = $categoryTable->getColumnName(array('column_name' => 'category_name', 'category_id' => $album->category_id)); ?>
              <?php echo $this->translate("Category:"); ?>
              <a href="<?php echo $this->url(array('action' => 'browse'), 'sesmusic_general', true).'?category_id='.urlencode($album->category_id) ; ?>"><?php echo $catName; ?></a>
              <?php if($album->subcat_id): ?>
              <?php $subcatName = $categoryTable->getColumnName(array('column_name' => 'category_name', 'category_id' => $album->subcat_id)); ?>
              &nbsp;&raquo;
              <a href="<?php echo $this->url(array('action' => 'browse'), 'sesmusic_general', true).'?subcat_id='.urlencode($album->subcat_id) ; ?>"><?php echo $subcatName; ?></a>
              <?php endif; ?>
              <?php if($album->subsubcat_id): ?>
              <?php $subsubcatName = $categoryTable->getColumnName(array('column_name' => 'category_name', 'category_id' => $album->subsubcat_id)); ?>
              &nbsp;&raquo;
              <a href="<?php echo $this->url(array('action' => 'browse'), 'sesmusic_general', true).'?subsubcat_id='.urlencode($album->subsubcat_id) ; ?>"><?php echo $subsubcatName; ?></a>
              <?php endif; ?>
              
            </div>
          <?php endif; ?>
          <?php 
            $information = '';   
            if (!empty($this->information) && in_array('favouriteCount', $this->information))
              $information .= $this->translate(array('%s favorite', '%s favorites', $album->favourite_count), $this->locale()->toNumber($album->favourite_count)) . ' | ';
              
            if (!empty($this->information) && in_array('songsCount', $this->information))
              $information .= $this->translate(array('%s song', '%s songs', $album->song_count), $this->locale()->toNumber($album->song_count)) . ' | ';
              
            if (!empty($this->information) && in_array('likeCount', $this->information))
             $information .= $this->translate(array('%s like', '%s likes', $album->like_count), $this->locale()->toNumber($album->like_count)) . ' | '; 
             
            if (!empty($this->information) && in_array('commentCount', $this->information))
             $information .= $this->translate(array('%s comment', '%s comments', $album->comment_count), $this->locale()->toNumber($album->comment_count)) . ' | ';
             
            if (!empty($this->information) && in_array('viewCount', $this->information))
             $information .= $this->translate(array('%s view', '%s views', $album->view_count), $this->locale()->toNumber($album->view_count)) . ' | ';
             
             if (!empty($this->information) && in_array('ratingCount', $this->information))
             $information .= $this->translate(array('%s rating', '%s ratings', $album->rating), $this->locale()->toNumber($album->rating)) . ' | ';
             
            $information = trim($information);
            $information = rtrim($information, '|');
          ?>
          
            <div class="sesmusic_list_info_stats sesbasic_text_light">
              <?php echo $information; ?>
            </div>
          
          <?php if($this->showRating && !empty($this->information) && in_array('ratingStars', $this->information)): ?>
            <div class="sesmusic_list_info_stats">
              <?php if( $album->rating > 0 ): ?>
                <?php for( $x=1; $x<= $album->rating; $x++ ): ?>
                  <span class="sesbasic_rating_star_small fa fa-star"></span>
                <?php endfor; ?>
                <?php if( (round($album->rating) - $album->rating) > 0): ?>
                  <span class="sesbasic_rating_star_small fa fa-star-half"></span>
                <?php endif; ?>
              <?php endif; ?>      
            </div>
          <?php endif; ?>
          <?php if(!empty($this->information) && in_array('description', $this->information)): ?>
          <div class="sesmusic_listinfo_desc">
            <?php echo $this->viewMore($album->description); ?>
          </div>
          <?php endif; ?>
          
          <div class="sesmusic_options_buttons">
            <?php if ($album->isDeletable() || $album->isEditable()): ?>         
              <?php if($this->viewer_id): ?>
                <?php if($this->canAddPlaylist && !empty($this->information) && in_array('addplaylist', $this->information)): ?>
                
                  <a class="fa fa-plus" title='<?php echo $this->translate("Add to Playlist"); ?>' href="javascript:void(0);" onclick="showPopUp('<?php echo $this->escape($this->url(array('module' =>'sesmusic', 'controller' => 'song', 'action'=>'append-songs','album_id' => $album->album_id, 'format' => 'smoothbox'), 'default' , true)); ?>'); return false;" ><?php echo $this->translate("Add to Playlist"); ?></a>
                  
                  <?php //echo $this->htmlLink(array('route' => 'default', 'module' => 'sesmusic', 'controller' => 'song', 'action' => 'append-songs', 'album_id' => $album->album_id), $this->translate('Add to Playlist'), array('class' => 'smoothbox fa fa-plus')); ?>
                  <?php endif; ?>

                  <?php if($this->canAddFavourite && !empty($this->information) && in_array('favourite', $this->information)): ?>
                    <?php $isFavourite = Engine_Api::_()->getDbTable('favourites', 'sesmusic')->isFavourite(array('resource_type' => "sesmusic_album", 'resource_id' => $album->album_id)); ?>
                    <a title='<?php echo $this->translate("Remove from Favorite") ?>' class="fa fa-heart sesmusic_favourite" id="sesmusic_album_unfavourite_<?php echo $album->album_id; ?>" style ='display:<?php echo $isFavourite ? "inline-block" : "none" ?>' href = "javascript:void(0);" onclick = "sesmusicFavourite('<?php echo $album->album_id; ?>', 'sesmusic_album');"><?php echo $this->translate("Remove from Favorite") ?></a>
                    <a title='<?php echo $this->translate("Add to Favorite") ?>' class="fa fa-heart" id="sesmusic_album_favourite_<?php echo $album->album_id; ?>" style ='display:<?php echo $isFavourite ? "none" : "inline-block" ?>' href = "javascript:void(0);" onclick = "sesmusicFavourite('<?php echo $album->album_id; ?>', 'sesmusic_album');"><?php echo $this->translate("Add to Favorite") ?></a>
                    <input type ="hidden" id = "sesmusic_album_favouritehidden_<?php echo $album->album_id; ?>" value = '<?php echo $isFavourite ? $isFavourite : 0; ?>' /> 
                  <?php endif; ?>
                  <?php if($this->albumlink && in_array('share', $this->albumlink)  && !empty($this->information) && in_array('share', $this->information)): ?>
                    <a class="fa fa-share" title='<?php echo $this->translate("Share") ?>' href="javascript:void(0);" onclick="showPopUp('<?php echo $this->escape($this->url(array('module'=>'activity', 'controller'=>'index', 'action'=>'share', 'route'=>'default', 'type'=>'sesmusic_album', 'id' => $album->album_id, 'format' => 'smoothbox'), 'default' , true)); ?>'); return false;" ><?php echo $this->translate("Share"); ?></a>
                <?php endif; ?>
               <?php endif; ?>
            <?php endif; ?>
          </div>
          
          <?php if(!empty($this->information) && in_array('showSongsList', $this->information)): ?>
          <?php $songs = $album->getSongs(); ?>
          <?php if(count($songs) > 0): ?>
          <div id="sesmusic_player_<?php echo $this->identity ?>" class="clear sesbasic_clearfix sesmusic_tracks_container sesbasic_custom_scroll">
            <ul class="clear sesmusic_tracks_list playlist_<?php echo $album->getIdentity() ?>">
              <?php foreach( $songs as $song ): ?>
              <?php $song = Engine_Api::_()->getItem('sesmusic_albumsong', $song->albumsong_id); ?>
              <?php if( !empty($song) ): ?>
              <li class="sesbasic_clearfix">
                <div class="sesmusic_tracks_list_photo">
                  <?php echo $this->htmlLink($song, $this->itemPhoto($song, 'thumb.icon') ) ?>
                  <?php $songTitle = preg_replace('/[^a-zA-Z0-9\']/', ' ', $song->getTitle()); ?>
                    <?php $songTitle = str_replace("'", '', $songTitle); ?>
                  <?php if($song->track_id): ?>
                    
                    <?php $uploadoption = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmusic.uploadoption', 'myComputer');
                    $consumer_key =  Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmusic.scclientid'); ?>          
                    <?php if(($uploadoption == 'both' || $uploadoption == 'soundCloud') && $consumer_key): ?>
                    <?php $URL = "http://api.soundcloud.com/tracks/$song->track_id/stream?consumer_key=$consumer_key"; ?>
                    <a href="javascript:void(0);" onclick="play_music('<?php echo $song->albumsong_id ?>', '<?php echo $URL; ?>', '<?php echo $songTitle; ?>');" class="sesmusic_songslist_playbutton"><i class="fa fa-play"></i></a>
                    <?php elseif($consumer_key): ?>                
                      <?php $URL = "http://api.soundcloud.com/tracks/$song->track_id/stream?consumer_key=$consumer_key"; ?>
                      <a href="javascript:void(0);" onclick="play_music('<?php echo $song->albumsong_id ?>', '<?php echo $URL; ?>', '<?php echo $songTitle; ?>');"><i class="fa fa-play"></i></a>
                    <?php endif; ?>
                  <?php else: ?>
                  <a href="javascript:void(0);" onclick="play_music('<?php echo $song->albumsong_id ?>', '<?php echo $song->getFilePath(); ?>', '<?php echo $songTitle; ?>');" class="sesmusic_songslist_playbutton"><i class="fa fa-play"></i></a>
                  <?php endif; ?>

                </div>
                <div class="sesmusic_tracks_list_stats sesbasic_text_light" title="<?php echo $song->playCountLanguagefield() ?>">
                  <i class="fa fa-play"></i><?php echo $song->play_count; ?>
                </div>
                <div class="sesmusic_tracks_list_name" title="<?php echo $song->getTitle() ?>">
                    <?php echo $this->htmlLink($song->getFilePath(), $this->htmlLink($song->getHref(), $song->getTitle()), array('class' => 'music_player_tracks_url', 'type' => 'audio', 'rel' => $song->song_id)); ?>
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
      <?php endif; ?>
    <?php endforeach; ?>
  <?php endif; ?>

  <?php //if($this->paginationType == 1): ?>
    <?php if (!empty($this->paginator) && $this->paginator->count() > 1): ?>
      <?php if ($this->paginator->getCurrentPageNumber() < $this->paginator->count()): ?>
        <div class="clr" id="loadmore_list"></div>
        <div class="sesbasic_view_more" id="view_more" onclick="loadMore();" style="display: block;">
          <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array('id' => 'feed_viewmore_link', 'class' => 'buttonlink icon_viewmore')); ?>
        </div>
        <div class="sesbasic_view_more_loading" id="loading_image" style="display: none;">
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
      <?php echo $this->translate('Nobody has created a music album with that criteria.') ?>
      <?php if($this->canCreate): ?>
        <?php echo $this->htmlLink(array('route' => 'sesmusic_general', 'action' => 'create'), $this->translate('Why don\'t you add some?')) ?>
      <?php endif; ?>
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
