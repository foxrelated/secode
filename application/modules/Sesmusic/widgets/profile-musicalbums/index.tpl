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
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/styles/customscrollbar.css'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/jquery.min.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/customscrollbar.concat.min.js'); ?>
<?php $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();  ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/jquery1.11.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesmusic/externals/scripts/favourites.js'); ?> 
<?php 
  if(isset($this->identityForWidget) && !empty($this->identityForWidget)):
    $randonNumber = $this->identityForWidget;
  else:
    $randonNumber = $this->identity; 
  endif;
?>
<?php if(!$this->is_ajax): ?>
  <div class="sesbasic_profile_subtabs clear sesbasic_clearfix">
    <ul>
      <?php $tabsOptions = array_intersect($this->defaultOptions, $this->defaultOptionsShow); ?>
      <?php foreach($tabsOptions as $valueOptions): ?>
        <?php 
          if($this->profile == 'own' ) {
              if($valueOptions == 'favouriteSong')
                $value = 'Favorite Songs';
              else if($valueOptions == 'songofyou')
                $value = 'Songs';
              else if($valueOptions == 'playlists')
                $value = 'Playlists';
              else if($valueOptions == 'favouriteArtist')
                $value = 'Favorite Artists';
              else 
                $value = 'Music Albums';
           } else {
              if($valueOptions == 'favouriteSong')
                $value = $this->translate('Favorite Songs of %s', ucwords($this->profile));
              else if($valueOptions == 'songofyou')
                $value = $this->translate("%s 's Songs", ucwords($this->profile));
              else if($valueOptions == 'playlists')
                $value = $this->translate("%s 's Playlists", ucwords($this->profile));
              elseif($valueOptions == 'favouriteArtist')
                $value = $this->translate('Favorite Artists of %s', ucwords($this->profile));
              else 
                $value = $this->translate('Music Albums');
           }
         ?>
         <li <?php if($this->defaultOpenTab == $valueOptions){ ?>class="sesbasic_tab_selected"<?php } ?> id="sesTabContainer_<?php echo $randonNumber; ?>_<?php echo $valueOptions; ?>"><a href="javascript:;" onclick="changeTabSes_<?php echo $randonNumber; ?>('<?php echo $valueOptions; ?>')"><?php echo $this->translate(ucwords($value)); ?></a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
  <div class="clear sesbasic_clearfix" id="scrollHeightDivSes_<?php echo $randonNumber; ?>">
          <?php if($this->albumPhotoOption == 'playlist'): ?>
            <ul class="sesmusic_list sesmusic_browse_playlist" id="tabbed-widget_<?php echo $randonNumber; ?>">
          <?php else: ?>
            <ul class="clear sesbasic_clearfix" id="tabbed-widget_<?php echo $randonNumber; ?>">
          <?php endif; ?>
<?php endif; ?>
          <?php $limit = $this->limit; ?>
          <?php if($this->paginator->getTotalItemCount() > 0): ?>
    			<?php foreach( $this->paginator as $photo ): ?>
          <?php if($this->albumPhotoOption == 'photo') { ?>
            <?php 
              if(isset($this->makeObjectOfPhoto)) { 
               $photo =  Engine_Api::_()->getItem('sesmusic_albumsong', $photo->resource_id);
              }
            ?>
            <?php 
              $width = $this->width;
              $height = $this->height; 
            ?>
            <li id="thumbs-photo-<?php echo $photo->album_id ?>" class="sesmusic_item_grid" style="width:<?php echo is_numeric($this->width) ? $this->width.'px' : $this->width ?>;">
              <div class="sesmusic_item_artwork" style="height:<?php echo is_numeric($this->height) ? $this->height.'px' : $this->height ?>;">
                <?php if($photo->photo_id): ?>
                  <?php echo $this->itemPhoto($photo, 'thumb.profile'); ?>
                <?php else: ?>
                 <?php $album = Engine_Api::_()->getItem('sesmusic_albums', $photo->album_id); ?>
                 <?php echo $this->itemPhoto($photo, 'thumb.normal'); ?>
                <?php endif; ?>
                <a href="<?php echo $photo->getHref(); ?>" class="transparentbg"></a>                
                <div class="sesmusic_item_info">
                  
                  <div class="sesmusic_item_info_title">
                    <?php echo $this->htmlLink($photo->getHref(), $photo->getTitle()) ?>
                    <?php if(!empty($this->information) && in_array('title', $this->information)): ?>
                    <?php if($album): ?>
                      <i><?php echo $this->translate("in "); ?><?php echo $this->htmlLink($album->getHref(), $album->getTitle()) ?></i>
                    <?php endif; ?>
                    <?php endif; ?>
                  </div>
                  
                  
                  <?php if(!empty($this->information) && in_array('postedBy', $this->information)): ?>
                  <div class="sesmusic_item_info_owner">
                    <?php echo $this->translate('by');?> <?php echo $this->htmlLink($photo->getOwner()->getHref(), $photo->getOwner()->getTitle()) ?>
                  </div>
                  <?php endif; ?>

                  <div class="sesmusic_item_info_stats">
                    <?php if(!empty($this->information) && in_array('commentCount', $this->information)): ?>
                    <span>
                      <?php echo $photo->comment_count; ?>
                      <i class="fa fa-comment"></i>
                    </span>
                    <?php endif; ?>
                    <?php if(!empty($this->information) && in_array('likeCount', $this->information)): ?>
                    <span>
                      <?php echo $photo->like_count; ?>
                      <i class="fa fa-thumbs-up"></i>
                    </span>
                    <?php endif; ?>
                    <?php if(!empty($this->information) && in_array('viewCount', $this->information)): ?>
                    <span>
                      <?php echo $photo->view_count; ?>
                      <i class="fa fa-eye"></i>
                    </span>
                    <?php endif; ?>
                    <?php if(!empty($this->information) && in_array('downloadCount', $this->information)): ?>
                    <span>
                      <?php echo $photo->download_count; ?>
                      <i class="fa fa-download"></i>
                    </span>
                    <?php endif; ?>
                    <?php if(!empty($this->information) && in_array('playCount', $this->information)): ?>
                    <span>
                      <?php echo $photo->play_count; ?>
                      <i class="fa fa-play"></i>
                    </span>
                    <?php endif; ?>
                  </div>

                  <?php if ($this->showAlbumSongRating && !empty($this->information) && in_array('ratingStars', $this->information)) : ?>
                    <div class="sesmusic_item_info_rating">
                      <?php if( $photo->rating > 0 ): ?>
                      <?php for( $x=1; $x<= $photo->rating; $x++ ): ?>
                      <span class="sesbasic_rating_star_small fa fa-star"></span>
                      <?php endfor; ?>
                      <?php if( (round($photo->rating) - $photo->rating) > 0): ?>
                      <span class="sesbasic_rating_star_small fa fa-star-half"></span>
                      <?php endif; ?>
                      <?php endif; ?>
                    </div>
                  <?php endif; ?>

                  <?php //Featured and Sponsored and Hot Label Icon ?>
                  <div class="sesmusic_item_info_label">
                    <?php if(!empty($photo->hot)  && !empty($this->information) && in_array('hot', $this->information)): ?>
                    <span class="sesmusic_label_hot"><?php echo $this->translate("HOT"); ?></span>
                    <?php endif; ?>
                    <?php if(!empty($photo->featured)  && !empty($this->information) && in_array('featured', $this->information)): ?>
                    <span class="sesmusic_label_featured"><?php echo $this->translate("FEATURED"); ?></span>
                    <?php endif; ?>
                    <?php if(!empty($photo->sponsored)  && !empty($this->information) && in_array('sponsored', $this->information)): ?>
                    <span class="sesmusic_label_sponsored"><?php echo $this->translate("SPONSORED"); ?></span>
                    <?php endif; ?>
                  </div>
                </div>
                <div class="hover_box">
                  <a title="<?php echo $photo->getTitle(); ?>" class="sesmusic_grid_link" href="<?php echo $photo->getHref(); ?>"></a>
 					        <?php if($photo->track_id): ?>
						        <?php $uploadoption = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmusic.uploadoption', 'myComputer');
						        $consumer_key =  Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmusic.scclientid'); ?>          
						        <?php if(($uploadoption == 'both' || $uploadoption == 'soundCloud') && $consumer_key): ?>
						        <?php $URL = "http://api.soundcloud.com/tracks/$photo->track_id/stream?consumer_key=$consumer_key"; ?>
						          <a class="sesmusic_play_button" href="javascript:void(0);" onclick="play_music('<?php echo $photo->albumsong_id ?>', '<?php echo $URL; ?>', '<?php echo preg_replace("/[^A-Za-z0-9\-]/", "", $photo->getTitle()); ?>');"><i class="fa fa-play-circle"></i></a>
						        <?php else: ?>
						          <a class="sesmusic_play_button" href="javascript:void(0);"><i class="fa fa-play-circle"></i></a>
						        <?php endif; ?>
						      <?php else: ?>
						        <a class="sesmusic_play_button" href="javascript:void(0);" onclick="play_music('<?php echo $photo->albumsong_id ?>', '<?php echo $photo->getFilePath(); ?>', '<?php echo $photo->getTitle(); ?>');"><i class="fa fa-play-circle"></i></a>
						      <?php endif; ?>
                  <div class="hover_box_options">
                    <?php $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
                    if($viewer_id): ?>
                      <?php if($this->addfavouriteAlbumSong && !empty($this->information) && in_array('favourite', $this->information)): ?>
                        <?php $isFavourite = Engine_Api::_()->getDbTable('favourites', 'sesmusic')->isFavourite(array('resource_type' => "sesmusic_albumsong", 'resource_id' => $photo->albumsong_id)); ?>              
                        <a title='<?php echo $this->translate("Remove from Favorite") ?>' class="favorite-white favorite" id="sesmusic_albumsong_unfavourite_<?php echo $photo->albumsong_id; ?>" style ='display:<?php echo $isFavourite ? "inline-block" : "none" ?>' href = "javascript:void(0);" onclick = "sesmusicFavourite('<?php echo $photo->albumsong_id; ?>', 'sesmusic_albumsong');"><i class="fa fa-heart sesmusic_favourite"></i></a>
                        <a title='<?php echo $this->translate("Add to Favorite") ?>' class="favorite-white favorite" id="sesmusic_albumsong_favourite_<?php echo $photo->albumsong_id; ?>" style ='display:<?php echo $isFavourite ? "none" : "inline-block" ?>' href = "javascript:void(0);" onclick = "sesmusicFavourite('<?php echo $photo->albumsong_id; ?>', 'sesmusic_albumsong');"><i class="fa fa-heart"></i></a>
                        <input type ="hidden" id = "sesmusic_albumsong_favouritehidden_<?php echo $photo->albumsong_id; ?>" value = '<?php echo $isFavourite ? $isFavourite : 0; ?>' />
                      <?php endif; ?>
                      <?php if($this->canAddPlaylistAlbumSong && !empty($this->information) && in_array('addplaylist', $this->information)): ?>
                        <a title="<?php echo $this->translate('Add to Playlist');?>" href="javascript:void(0);" onclick="showPopUp('<?php echo $this->escape($this->url(array('action'=>'append','albumsong_id' => $photo->albumsong_id, 'format' => 'smoothbox'), 'sesmusic_albumsong_specific' , true)); ?>'); return false;" class="add-white"><i class="fa fa-plus"></i></a>
                      <?php endif; ?>                   
                    <?php if($this->songlink && in_array('share', $this->songlink)  && !empty($this->information) && in_array('share', $this->information)): ?>
                    <a class="share-white" title='<?php echo $this->translate("Share") ?>' href="javascript:void(0);" onclick="showPopUp('<?php echo $this->escape($this->url(array('module'=>'activity', 'controller'=>'index', 'action'=>'share', 'route'=>'default', 'type'=>'sesmusic_albumsong', 'id' => $photo->albumsong_id, 'format' => 'smoothbox'), 'default' , true)); ?>'); return false;" ><i class="fa fa-share"></i></a>
                    <?php endif; ?>
                    <?php endif; ?>
                  </div>
                </div>
              </div>       
            </li>
          <?php } elseif($this->albumPhotoOption == 'album') { ?>
            <li id="thumbs-photo-<?php echo $photo->album_id ?>" class="sesmusic_item_grid" style="width:<?php echo is_numeric($this->width) ? $this->width.'px' : $this->width ?>;">
              <div class="sesmusic_item_artwork" style="height:<?php echo is_numeric($this->height) ? $this->height.'px' : $this->height ?>;">
                <?php echo $this->htmlLink($photo, $this->itemPhoto($photo, 'thumb.main') ) ?>
                <a href="<?php echo $photo->getHref(); ?>" class="transparentbg"></a>
                <div class="sesmusic_item_info">
                  <div class="sesmusic_item_info_title">
                    <?php echo $this->htmlLink($photo->getHref(), $photo->getTitle()) ?>
                  </div>
                  <?php if(!empty($this->informationAlbum) && in_array('postedBy', $this->informationAlbum)): ?>
                    <div class="sesmusic_item_info_owner">
                      <?php echo $this->translate('by %s', $this->htmlLink($photo->getOwner(), $photo->getOwner()->getTitle())) ?>
                    </div>
                  <?php endif; ?>
                  <div class="sesmusic_item_info_stats">
                    <?php if(!empty($this->informationAlbum) && in_array('commentCount', $this->informationAlbum)): ?>
                      <span>
                        <?php echo $this->translate(array($photo->comment_count), $this->locale()->toNumber($photo->comment_count)) ?>
                        <i class="fa fa-comment"></i>
                      </span>
                    <?php endif; ?>
                    <?php if(!empty($this->informationAlbum) && in_array('likeCount', $this->informationAlbum)): ?>
                      <span>
                        <?php echo $this->translate(array($photo->like_count), $this->locale()->toNumber($photo->like_count)) ?>
                        <i class="fa fa-thumbs-up"></i>
                      </span>
                    <?php endif; ?>
                    <?php if(!empty($this->informationAlbum) && in_array('viewCount', $this->informationAlbum)): ?>
                      <span>
                        <?php echo $this->translate(array($photo->view_count), $this->locale()->toNumber($photo->view_count)) ?>
                        <i class="fa fa-eye"></i>
                      </span>
                    <?php endif; ?>
                    <?php if(!empty($this->informationAlbum) && in_array('songCount', $this->informationAlbum)): ?>
                      <span>
                        <?php echo $this->translate(array($photo->song_count), $this->locale()->toNumber($photo->song_count)) ?>
                        <i class="fa fa-music"></i>
                      </span>
                    <?php endif; ?>
                  </div>
                  <?php if ($this->showRating && !empty($this->informationAlbum) && in_array('ratingStars', $this->informationAlbum)) : ?>
                    <div class="sesmusic_item_info_rating">
                      <?php if( $photo->rating > 0 ): ?>
                      <?php for( $x=1; $x<= $photo->rating; $x++ ): ?>
                      <span class="sesbasic_rating_star_small fa fa-star"></span>
                      <?php endfor; ?>
                      <?php if( (round($photo->rating) - $photo->rating) > 0): ?>
                      <span class="sesbasic_rating_star_small fa fa-star-half"></span>
                      <?php endif; ?>
                      <?php endif; ?>
                    </div>
                  <?php endif; ?>
                  <div class="sesmusic_item_info_label">
                    <?php if($photo->hot && !empty($this->informationAlbum) && in_array('hot', $this->informationAlbum)): ?>
                      <span class="sesmusic_label_hot"><?php echo $this->translate("HOT"); ?></span>
                    <?php endif; ?>
                    <?php if($photo->featured && !empty($this->informationAlbum) && in_array('featured', $this->informationAlbum)): ?>
                    <span class="sesmusic_label_featured"><?php echo $this->translate("FEATURED"); ?></span>
                    <?php endif; ?>
                    <?php if($photo->sponsored && !empty($this->informationAlbum) && in_array('sponsored', $this->informationAlbum)): ?>
                    <span class="sesmusic_label_sponsored"><?php echo $this->translate("SPONSORED"); ?></span>
                    <?php endif; ?>
                  </div>
                </div>
                <div class="hover_box">
                  <a title="<?php echo $photo->getTitle(); ?>" class="sesmusic_grid_link" href="<?php echo $photo->getHref(); ?>"></a>
                  <div class="hover_box_options">
                  <?php 
                  if($viewer_id): ?>
                      <?php $isFavourite = Engine_Api::_()->getDbTable('favourites', 'sesmusic')->isFavourite(array('resource_type' => "sesmusic_album", 'resource_id' => $photo->album_id)); ?>

                      <?php if($this->addfavouriteAlbumSong && !empty($this->informationAlbum) && in_array('favourite', $this->informationAlbum)): ?>
                      <a title='<?php echo $this->translate("Remove from Favorite") ?>' class="favorite-white favorite" id="sesmusic_album_unfavourite_<?php echo $photo->album_id; ?>" style ='display:<?php echo $isFavourite ? "inline-block" : "none" ?>' href = "javascript:void(0);" onclick = "sesmusicFavourite('<?php echo $photo->album_id; ?>', 'sesmusic_album');"><i class="fa fa-heart sesmusic_favourite"></i></a>
                      <a title='<?php echo $this->translate("Add to Favorite") ?>' class="favorite-white favorite" id="sesmusic_album_favourite_<?php echo $photo->album_id; ?>" style ='display:<?php echo $isFavourite ? "none" : "inline-block" ?>' href = "javascript:void(0);" onclick = "sesmusicFavourite('<?php echo $photo->album_id; ?>', 'sesmusic_album');"><i class="fa fa-heart"></i></a>
                      <input type="hidden" id="sesmusic_album_favouritehidden_<?php echo $photo->album_id; ?>" value='<?php echo $isFavourite ? $isFavourite : 0; ?>' />
                      <?php endif; ?>                
                      <?php if($this->canAddPlaylistAlbumSong && !empty($this->informationAlbum) && in_array('addplaylist', $this->informationAlbum)): ?>
                      <a class="add-white" title='<?php echo $this->translate("Add to Playlist") ?>' href="javascript:void(0);" onclick="showPopUp('<?php echo $this->escape($this->url(array('module' =>'sesmusic', 'controller' => 'song', 'action'=>'append - songs','album_id' => $photo->album_id, 'format' => 'smoothbox'), 'default' , true)); ?>'); return false;" ><i class="fa fa-plus"></i></a>
                      <?php endif; ?>
                    <?php if(in_array('share', $this->albumlink) && !empty($this->informationAlbum) && in_array('share', $this->informationAlbum)): ?>
                    <a class="share-white" title='<?php echo $this->translate("Share") ?>' href="javascript:void(0);" onclick="showPopUp('<?php echo $this->escape($this->url(array('module'=>'activity', 'controller'=>'index', 'action'=>'share', 'route'=>'default', 'type'=>'sesmusic_album', 'id' => $photo->album_id, 'format' => 'smoothbox'), 'default' , true)); ?>'); return false;" ><i class="fa fa-share"></i></a>
                    <?php endif; ?>
                    <?php endif; ?>
                    <?php  ?>
                  </div>
                </div>
              </div>            
            </li>
          <?php  } elseif($this->albumPhotoOption == 'playlist') {
          $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity(); ?>
              <li id="music_playlist_item_<?php echo $photo->getIdentity() ?>" class="sesbasic_clearfix">
              <div class="sesmusic_list_artwork">
                <div>
                  <?php echo $this->htmlLink($photo->getHref(), $this->itemPhoto($photo, 'thumb.profile'), array('class' => 'thumb')) ?>
                </div>
              </div>
              <div class="sesmusic_list_info">
                <div class="sesmusic_list_info_title">
                  <?php echo $this->htmlLink($photo->getHref(), $photo->getTitle()) ?>
                </div>
                <?php if(!empty($this->informationPlaylist) && in_array('postedByPl', $this->informationPlaylist)): ?>
                <div class="sesmusic_list_info_stats sesbasic_text_light">
                  <?php echo $this->translate('Created By %s', $this->htmlLink($photo->getOwner(), $photo->getOwner()->getTitle())) ?>
                </div>
                <?php endif; ?>
                <?php if(!empty($this->informationPlaylist) && in_array('viewCountPl', $this->informationPlaylist)): ?>
                  <div class="sesmusic_list_info_stats sesbasic_text_light">
                    <?php echo $this->translate(array('%s view', '%s views', $photo->view_count), $this->locale()->toNumber($photo->view_count)) ?>
                  </div>
                <?php endif; ?>
                <?php if(!empty($this->informationPlaylist) && in_array('description', $this->informationPlaylist)): ?>
                <div class="sesmusic_listinfo_desc">
                  <?php echo $this->viewMore(nl2br($photo->description)); ?>
                </div>
                <?php endif; ?>
                <?php if($viewer_id): ?>
                <div class="sesmusic_options_buttons">
                  <?php if(!empty($this->informationPlaylist) && in_array('addFavouriteButtonPl', $this->informationPlaylist)): ?>
                    <?php $isFavourite = Engine_Api::_()->getDbTable('favourites', 'sesmusic')->isFavourite(array('resource_type' => "sesmusic_playlist", 'resource_id' => $photo->getIdentity())); ?>
                    <a class="fa fa-heart sesmusic_favourite" id="sesmusic_playlist_unfavourite_<?php echo $photo->getIdentity(); ?>" style ='display:<?php echo $isFavourite ? "inline-block" : "none" ?>' href = "javascript:void(0);" onclick = "sesmusicFavourite('<?php echo $photo->getIdentity(); ?>', 'sesmusic_playlist');" title="<?php echo $this->translate("Remove from Favorite") ?>"><?php echo $this->translate("Remove from Favorite") ?></a>
                    <a class="fa fa-heart" id="sesmusic_playlist_favourite_<?php echo $photo->getIdentity(); ?>" style ='display:<?php echo $isFavourite ? "none" : "inline-block" ?>' href = "javascript:void(0);" onclick = "sesmusicFavourite('<?php echo $photo->getIdentity(); ?>', 'sesmusic_playlist');" title="<?php echo $this->translate("Add to Favorite") ?>"><?php echo $this->translate("Add to Favorite") ?></a>
                    <input type="hidden" id="sesmusic_playlist_favouritehidden_<?php echo $photo->getIdentity(); ?>" value='<?php echo $isFavourite ? $isFavourite : 0; ?>' /> 
                    <?php endif; ?>
                    <?php if($viewer_id && !empty($this->informationPlaylist) && in_array('sharePl', $this->informationPlaylist)): ?>
                    <a  class="fa fa-share" title='<?php echo $this->translate("Share") ?>' href="javascript:void(0);" onclick="showPopUp('<?php echo $this->escape($this->url(array('module'=>'activity', 'controller'=>'index', 'action'=>'share', 'route'=>'default', 'type'=>'sesmusic_playlist', 'id' => $photo->playlist_id, 'format' => 'smoothbox'), 'default' , true)); ?>'); return false;" ><?php echo $this->translate("Share"); ?></a>
                   <?php endif; ?>
                </div>
                <?php endif; ?>
                <?php $playlist = $photo; 
                $songs = $photo->getSongs();
                ?>
                <?php if(!empty($this->informationPlaylist) && in_array('showSongsList', $this->informationPlaylist)): ?>
                <?php if(count($songs) > 0): ?>
                <div id="sesmusic_player_<?php echo $random ?>" class="clear sesbasic_clearfix sesmusic_tracks_container sesbasic_custom_scroll">
                  <ul class="clear sesmusic_tracks_list playlist_<?php echo $playlist->getIdentity() ?>">
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
          <?php } elseif($this->albumPhotoOption == 'artist') { 
          $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity(); 
          $photo = Engine_Api::_()->getItem('sesmusic_artist', $photo->resource_id);  ?>
            <li id="music_playlist_item_<?php echo $photo->getIdentity() ?>" class="sesmusic_item_grid" style="width:<?php echo $this->width ?>px;">
              <div class="sesmusic_item_artwork" style="height:<?php echo $this->height ?>px;">
                <?php if($photo->artist_photo): ?>
                <?php $img_path = Engine_Api::_()->storage()->get($photo->artist_photo, '')->getPhotoUrl();
                $path = $img_path; 
                ?>
                <img src="<?php echo $path ?>">
                <?php else: ?>
                <img src="<?php //echo $path ?>">
                <?php endif; ?>
                <div class="hover_box">           
                  <div class="hover_box_options">
                    <?php if($viewer_id): ?>
                    <?php if($this->artistlink && in_array('favourite', $this->artistlink) && !empty($this->informationArtist) && in_array('favourite', $this->informationArtist)): ?>
                    <?php $isFavourite = Engine_Api::_()->getDbTable('favourites', 'sesmusic')->isFavourite(array('resource_type' => "sesmusic_artist", 'resource_id' => $photo->getIdentity())); ?>
                    <a title='<?php echo $this->translate("Remove from Favorites") ?>' class="favorite-white favorite" id="sesmusic_artist_unfavourite_<?php echo $photo->getIdentity(); ?>" style ='display:<?php echo $isFavourite ? "inline-block" : "none" ?>' href = "javascript:void(0);" onclick = "sesmusicFavourite('<?php echo $photo->getIdentity(); ?>', 'sesmusic_artist');"><i class="fa fa-heart sesmusic_favourite"></i></a>
                    <a title='<?php echo $this->translate("Add to Favorite") ?>' class="favorite-white favorite" id="sesmusic_artist_favourite_<?php echo $photo->getIdentity(); ?>" style ='display:<?php echo $isFavourite ? "none" : "inline-block" ?>' href = "javascript:void(0);" onclick = "sesmusicFavourite('<?php echo $photo->getIdentity(); ?>', 'sesmusic_artist');"><i class="fa fa-heart"></i></a>
                    <input type="hidden" id="sesmusic_artist_favouritehidden_<?php echo $photo->getIdentity(); ?>" value='<?php echo $isFavourite ? $isFavourite : 0; ?>' />
                    <?php endif; ?>
                    <?php endif; ?>
                  </div>
                  <a class="transparentbg" href="<?php echo $this->escape($this->url(array('module'=>'sesmusic', 'controller'=>'artist', 'action'=>'view', 'artist_id' => $photo->artist_id), 'default' , true)); ?>"></a>
                </div>
              </div>
              <div class="sesmusic_browse_artist_info">
                <div class="sesmusic_browse_artist_title">
                  <?php echo $this->htmlLink(array('module'=>'sesmusic', 'controller'=>'artist', 'action'=>'view', 'route'=>'default', 'artist_id' => $photo->artist_id), $photo->name); ?>
                </div>
                <div class="sesmusic_browse_artist_stats sesbasic_text_light">
                  <?php if($this->showArtistRating && !empty($this->informationArtist) && in_array('ratingCount', $this->informationArtist)): ?>
                  <?php echo $this->translate(array('%s rating', '%s ratings', $photo->rating), $this->locale()->toNumber($photo->rating)) ?>
                  <?php endif; ?>
                  <?php if($this->showArtistRating && !empty($this->informationArtist) &&  in_array('favouriteCount', $this->informationArtist) && in_array('ratingCount', $this->informationArtist)): ?>
                  &nbsp;|&nbsp;
                  <?php endif; ?>
                  <?php if(!empty($this->informationArtist) && in_array('favouriteCount', $this->informationArtist)): ?>
                  <?php echo $this->translate(array('%s favorite', '%s favorites', $photo->favourite_count), $this->locale()->toNumber($photo->favourite_count)) ?>
                  <?php endif; ?>
                </div>
              </div>
            </li>
          
          <?php } ?>
          <?php $limit++; ?>
      <?php endforeach;?>
      <?php endif; ?>
      <?php if($this->paginator->getTotalItemCount() == 0): ?>
        <div class="tip">
          <span>
            <?php if($this->albumPhotoOption == 'artist'): ?>
              <?php echo $this->translate("There are currently no artists.");?>
            <?php elseif($this->albumPhotoOption == 'album'):?>
              <?php echo $this->translate("There are currently no albums");?>
            <?php elseif($this->albumPhotoOption == 'playlist'): ?>
              <?php echo $this->translate("There are currently no playlists.");?>
            <?php else: ?>
              <?php echo $this->translate("There are currently no songs.");?>
            <?php endif; ?>
            <?php //$profilemusicalbums = $this->type == 'profilemusicalbums' ? 'albums.' : 'songs.'; ?>
            <?php //echo $this->translate("There are currently no ".$profilemusicalbums."");?>
          </span>
        </div>    
      <?php endif; ?>
    <?php if(!$this->is_ajax) { ?>
  </ul>
  <div class="sesbasic_view_more" id="view_more_<?php echo $randonNumber; ?>" onclick="viewMore_<?php echo $randonNumber; ?>();" > <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array('id' => "feed_viewmore_link_$randonNumber", 'class' => 'buttonlink icon_viewmore')); ?> </div>
  <div class="sesbasic_view_more_loading" id="loading_image_<?php echo $randonNumber; ?>" style="display: none;"> <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' style='margin-right: 5px;' /> <?php echo $this->translate("Loading ...") ?> </div>
</div>
<script type="text/javascript">
var valueTabData ;

// globally define available tab array
	var availableTabs_<?php echo $randonNumber; ?>;
	var requestTab_<?php echo $randonNumber; ?>;
  <?php $tabsOptions = array_intersect($this->defaultOptions, $this->defaultOptionsShow); ?>
  <?php foreach($tabsOptions as $tabsOption): ?>
  <?php $tabsOptions_array[] = $tabsOption; ?>
  <?php endforeach; ?>
  availableTabs_<?php echo $randonNumber; ?> = <?php echo json_encode($tabsOptions_array); ?>;
<?php if($this->loadOptionData == 'auto_load'){ ?>
		window.addEvent('domready', function() {
		 sesBasicAutoScroll(window).scroll( function() {
			  var heightOfContentDiv_<?php echo $randonNumber; ?> = sesBasicAutoScroll('#scrollHeightDivSes_<?php echo $randonNumber; ?>').offset().top;
        var fromtop_<?php echo $randonNumber; ?> = sesBasicAutoScroll(this).scrollTop();
        if(fromtop_<?php echo $randonNumber; ?> > heightOfContentDiv_<?php echo $randonNumber; ?> - 100 && sesBasicAutoScroll('#view_more_<?php echo $randonNumber; ?>').css('display') == 'block' ){
						document.getElementById('feed_viewmore_link_<?php echo $randonNumber; ?>').click();
				}
     });
	});
<?php } ?>
</script>
<?php } ?>

<script type="text/javascript">
	function changeTabSes_<?php echo $randonNumber; ?>(valueTab) {
      if(valueTab == 'playlists') {
       $("tabbed-widget_<?php echo $randonNumber; ?>").removeClass("sesbasic_clearfix").addClass("sesmusic_list sesmusic_browse_playlist");
      } else {
        $("tabbed-widget_<?php echo $randonNumber; ?>").removeClass("sesmusic_list sesmusic_browse_playlist").addClass("sesbasic_clearfix");
      }
      
			if(sesBasicAutoScroll("#sesTabContainer_<?php echo $randonNumber ?>_"+valueTab).hasClass('sesbasic_tab_selected'))
				return;
			var id = '_<?php echo $randonNumber; ?>';
			var length = availableTabs_<?php echo $randonNumber; ?>.length;
			for (var i = 0; i < length; i++) {
					if(availableTabs_<?php echo $randonNumber; ?>[i] == valueTab)
						document.getElementById('sesTabContainer'+id+'_'+availableTabs_<?php echo $randonNumber; ?>[i]).addClass('sesbasic_tab_selected');
					else
						document.getElementById('sesTabContainer'+id+'_'+availableTabs_<?php echo $randonNumber; ?>[i]).removeClass('sesbasic_tab_selected');
			}
		if(valueTab){
				document.getElementById("tabbed-widget_<?php echo $randonNumber; ?>").innerHTML = "<div class='clear sesbasic_loading_container'></div>";
				document.getElementById("view_more_<?php echo $randonNumber; ?>").style.display = 'none';
			 if (typeof(requestTab_<?php echo $randonNumber; ?>) != 'undefined') {
				 requestTab_<?php echo $randonNumber; ?>.cancel();
 			 }
			 valueTabData = valueTab;
			 requestTab_<?php echo $randonNumber; ?> = new Request.HTML({
				method: 'post',
				'url': en4.core.baseUrl + 'widget/index/mod/sesmusic/name/profile-musicalbums/openTab/' + valueTab,
				'data': {
					format: 'html',
					page:  1,    
					params :'<?php echo json_encode($this->params); ?>', 
					is_ajax : 1,
					identityObject : '<?php echo $this->identityObject; ?>',
					identity : '<?php echo $randonNumber; ?>',
				},
				onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
					document.getElementById('tabbed-widget_<?php echo $randonNumber; ?>').innerHTML = '';
					document.getElementById('tabbed-widget_<?php echo $randonNumber; ?>').innerHTML = document.getElementById('tabbed-widget_<?php echo $randonNumber; ?>').innerHTML + responseHTML;
       jqueryObjectOfSes(".sesbasic_custom_scroll").mCustomScrollbar({
          theme:"minimal-dark"
        });

				}
    	});
		requestTab_<?php echo $randonNumber; ?>.send();
    return false;			
		}
	}
	viewMoreHide_<?php echo $randonNumber; ?>();
  function viewMoreHide_<?php echo $randonNumber; ?>() {
    if ($('view_more_<?php echo $randonNumber; ?>'))
      $('view_more_<?php echo $randonNumber; ?>').style.display = "<?php echo ($this->paginator->count() == 0 ? 'none' : ($this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' )) ?>";
  }
  function viewMore_<?php echo $randonNumber; ?> (){
    var openTab_<?php echo $randonNumber; ?> = '<?php echo $this->defaultOpenTab; ?>';
    document.getElementById('view_more_<?php echo $randonNumber; ?>').style.display = 'none';
    document.getElementById('loading_image_<?php echo $randonNumber; ?>').style.display = '';    
    en4.core.request.send(new Request.HTML({
      method: 'post',
      'url': en4.core.baseUrl + 'widget/index/mod/sesmusic/name/profile-musicalbums/openTab/' + openTab_<?php echo $randonNumber; ?>,
      'data': {
        format: 'html',
        page: <?php echo $this->page + 1; ?>,    
				params :'<?php echo json_encode($this->params); ?>', 
				is_ajax : 1,
				identity : '<?php echo $randonNumber; ?>',
				identityObject : '<?php echo $this->identityObject; ?>',
      },
      onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
        document.getElementById('tabbed-widget_<?php echo $randonNumber; ?>').innerHTML = document.getElementById('tabbed-widget_<?php echo $randonNumber; ?>').innerHTML + responseHTML;
				document.getElementById('loading_image_<?php echo $randonNumber; ?>').style.display = 'none';
        jqueryObjectOfSes(".sesbasic_custom_scroll").mCustomScrollbar({
          theme:"minimal-dark"
        });
      }
    }));
    return false;
  }
</script>
