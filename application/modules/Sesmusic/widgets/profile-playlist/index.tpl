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
<?php

//This is done to make these links more uniform with other viewscripts
$playlist = $this->playlist;
$songs = $playlist->getSongs();
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesmusic/externals/scripts/favourites.js'); ?> 

<div class="sesmusic_item_view_wrapper sesmusic_playlist_view clear">
  <div class="sesmusic_item_view_top">
    <div class="sesmusic_item_view_artwork">
      <div>
        <?php echo $this->itemPhoto($playlist, 'thumb.profile'); ?>
      </div>
    </div>
    <div class="sesmusic_item_view_info">
      <div class="sesmusic_item_view_title">
        <?php echo $playlist->getTitle() ?>
      </div>
      <p class="sesmusic_item_view_stats sesbasic_text_light">
        <?php if(!empty($this->informationPlaylist) && in_array('postedByPl', $this->informationPlaylist)): ?>
        <?php echo $this->translate('Created %s by ', $this->timestamp($playlist->creation_date)) ?>
        
        <?php echo $this->htmlLink($playlist->getOwner(), $playlist->getOwner()->getTitle()) ?>
        <?php endif; ?>
        <?php if(!empty($this->informationPlaylist) && in_array('viewCountPl', $this->informationPlaylist)): ?>
        &nbsp;|&nbsp;
        <?php echo $this->translate(array('%s view', '%s views', $this->playlist->view_count), $this->locale()->toNumber($this->playlist->view_count)) ?>
        <?php endif; ?>
      </p>
      <?php if(!empty($this->informationPlaylist) && in_array('description', $this->informationPlaylist) && $playlist->description): ?>
        <p class="sesmusic_item_view_des">
          <?php echo $this->viewMore(nl2br($playlist->description)); ?>
        </p>
      <?php endif; ?>

    <?php //if( empty($this->hideStats) ): ?>
      <div class="sesmusic_options_buttons sesmusic_item_view_options">
        
        <?php $viewer = Engine_Api::_()->user()->getViewer(); ?>
        <?php if($this->viewer_id): 
        if ($playlist->getOwner()->isSelf($viewer) || $viewer->level_id == 1): ?>
        <?php if(!empty($this->informationPlaylist) && in_array('editButton', $this->informationPlaylist)): ?>
        <?php  echo $this->htmlLink($playlist->getHref(array('route' => 'sesmusic_playlist_specific', 'action' => 'edit')), $this->translate('Edit Playlist'), array('class'=>'fa fa-pencil')); ?>
        <?php endif; ?>
          <?php if(!empty($this->informationPlaylist) && in_array('deleteButton', $this->informationPlaylist)): ?>
          <?php echo $this->htmlLink(array('route' => 'sesmusic_playlist_specific', 'module' => 'sesmusic', 'controller' => 'playlist', 'action' => 'delete', 'playlist_id' => $playlist->getIdentity(), 'slug' => $playlist->getSlug(), 'format' => 'smoothbox'), $this->translate('Delete Playlist'), array('class' => 'smoothbox fa fa-trash')); ?>
          <?php endif; ?>
          <?php endif; ?>
          <?php endif; ?>
        
        <?php if($this->viewer_id): ?>
        <?php if(!empty($this->informationPlaylist) && in_array('sharePl', $this->informationPlaylist)): ?>
        <?php echo $this->htmlLink(array('module'=>'activity', 'controller'=>'index', 'action'=>'share', 'route'=>'default', 'type'=>'sesmusic_playlist', 'id' => $this->playlist->getIdentity(), 'format' => 'smoothbox'), $this->translate("Share"), array('class' => 'smoothbox fa fa-share')); ?>
        <?php endif; ?>
        <?php if(!empty($this->informationPlaylist) && in_array('reportPl', $this->informationPlaylist)): ?>
        <?php echo $this->htmlLink(array('module'=>'core', 'controller'=>'report', 'action'=>'create', 'route'=>'default', 'subject'=> $this->playlist->getGuid(), 'format' => 'smoothbox' ), $this->translate("Report"), array('class' => 'smoothbox fa fa-flag')); ?>  
        <?php endif; ?>
          
         <?php if(!empty($this->informationPlaylist) && in_array('addFavouriteButtonPl', $this->informationPlaylist)): ?>
          <?php $isFavourite = Engine_Api::_()->getDbTable('favourites', 'sesmusic')->isFavourite(array('resource_type' => "sesmusic_playlist", 'resource_id' => $playlist->getIdentity())); ?>
          <a class="fa fa-heart sesmusic_favourite" id="sesmusic_playlist_unfavourite_<?php echo $playlist->getIdentity(); ?>" style ='display:<?php echo $isFavourite ? "inline-block" : "none" ?>' href = "javascript:void(0);" onclick = "sesmusicFavourite('<?php echo $playlist->getIdentity(); ?>', 'sesmusic_playlist');" title="<?php echo $this->translate("Remove from Favorite") ?>"><?php echo $this->translate("Remove from Favorite") ?></a>
          <a class="fa fa-heart" id="sesmusic_playlist_favourite_<?php echo $playlist->getIdentity(); ?>" style ='display:<?php echo $isFavourite ? "none" : "inline-block" ?>' href = "javascript:void(0);" onclick = "sesmusicFavourite('<?php echo $playlist->getIdentity(); ?>', 'sesmusic_playlist');" title="<?php echo $this->translate("Add to Favorite") ?>"><?php echo $this->translate("Add to Favorite") ?></a>
          <input type="hidden" id="sesmusic_playlist_favouritehidden_<?php echo $playlist->getIdentity(); ?>" value='<?php echo $isFavourite ? $isFavourite : 0; ?>' /> 
          <?php endif; ?>
        <?php endif; ?>

      </div>
    <?php //endif; ?>
    </div>
  </div>
  <ul class="clear sesmusic_songslist_container playlist_<?php echo $this->playlist->getIdentity() ?>">
    <?php if(count($songs) > 0 && !empty($this->information)): ?>
    <?php foreach( $songs as $song ): ?>
      <?php if(!empty($song) ): ?>
      <?php $song = Engine_Api::_()->getItem('sesmusic_albumsong', $song->albumsong_id); ?>
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
                <?php if($song->hot && in_array('hot', $this->information)): ?>
                  <span class="sesmusic_label_hot"><?php echo $this->translate("HOT"); ?></span>
                <?php endif; ?>
                <?php if($song->featured && in_array('featured', $this->information)): ?>
                <span class="sesmusic_label_featured"><?php echo $this->translate("FEATURED"); ?></span>
                <?php endif; ?>
                <?php if($song->sponsored && in_array('sponsored', $this->information)): ?>
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
                <?php if($song->category_id && in_array('category', $this->information)): ?>
                  <div class="sesmusic_list_category floatR">
                    <?php $catName = Engine_Api::_()->getDbTable('categories', 'sesmusic')->getColumnName(array('column_name' => 'category_name', 'category_id' => $song->category_id, 'param' => 'song')); ?>
                    <a href="<?php echo $this->url(array('action' => 'browse'), 'sesmusic_songs', true).'?category_id='.urlencode($song->category_id) ; ?>"><?php echo $catName; ?></a>
                  </div>
                <?php endif; ?>
	              <div class="sesmusic_songslist_songname">
                  <?php echo $this->htmlLink($song->getHref(), $song->getTitle(), array('class' => 'music_player_tracks_url', 'type' => 'audio', 'rel' => $song->song_id)); ?>
	              </div>
                <?php if(!empty($this->information) && in_array('postedBy', $this->information)): ?>
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
	                    <?php
	                         // if($artists):
	                          $artists_array = Engine_Api::_()->getDbTable('artists', 'sesmusic')->getArtists($artists); ?>
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
	                <?php if($this->canAddPlaylistAlbumSong && !empty($this->information) && in_array('addplaylist', $this->information)): ?>
	                  <a title="<?php echo $this->translate('Add to Playlist');?>" href="javascript:void(0);" onclick="showPopUp('<?php echo $this->escape($this->url(array('action'=>'append','albumsong_id' => $song->albumsong_id, 'format' => 'smoothbox'), 'sesmusic_albumsong_specific' , true)); ?>'); return false;" class="fa fa-plus"><?php echo $this->translate('Add to Playlist');?></a>
	                <?php endif; ?>
                  
	                <?php if($song->download && !$song->track_id && !$song->song_url && $this->downloadAlbumSong  && !empty($this->information) && in_array('downloadButton', $this->information)): ?>
	                  <?php echo $this->htmlLink(array('route' => 'sesmusic_albumsong_specific', 'action' => 'download-song', 'albumsong_id' => $song->albumsong_id), $this->translate("Download"), array('class' => ' fa fa-download')); ?>                                       
                  <?php elseif($song->download && !empty($this->information) && in_array('downloadButton', $this->information)): ?>
                    <?php $file = Engine_Api::_()->getItem('storage_file', $song->file_id); ?>
                    <?php if($file->mime_minor && $this->downloadAlbumSong): ?>
                    <?php $consumer_key =  Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmusic.scclientid');
                    $downloadURL = 'http://api.soundcloud.com/tracks/' . $song->track_id . '/download?client_id=' . $consumer_key;  ?>
                    <a class='fa fa-download' href='<?php echo $downloadURL; ?>' target="_blank"><?php echo $this->translate("Download");  ?></a>
                    <?php endif; ?>
                  <?php endif; ?>

	                <?php if(!empty($this->songlink) && in_array('share', $this->songlink) && !empty($this->information) && in_array('share', $this->information)): ?>
	                <?php echo $this->htmlLink(array('module'=>'activity', 'controller'=>'index', 'action'=>'share', 'route'=>'default', 'type'=>'sesmusic_albumsong', 'id' => $song->getIdentity(), 'format' => 'smoothbox'), $this->translate("Share"), array('class' => 'smoothbox fa fa-share')); ?>
	                <?php endif; ?>

	                <?php if(!empty($this->songlink) && in_array('report', $this->songlink) && !empty($this->information) && in_array('report', $this->information)): ?>
	                <?php echo $this->htmlLink(array('module'=>'core', 'controller'=>'report', 'action'=>'create', 'route'=>'default', 'subject'=> $song->getGuid(), 'format' => 'smoothbox'), $this->translate("Report"), array('class' => 'smoothbox fa fa-flag')); ?>
	                <?php endif; ?>

	              <?php if($this->canAddFavouriteAlbumSong && !empty($this->information) && in_array('addplaylist', $this->information)): ?>
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
   <?php else: ?>
      <li class="sesmusic_songslist">
        <div class="tip">
          <span>
            <?php echo $this->translate('There are no songs yet.') ?>
          </span>
        </div>
      </li>
    <?php endif; ?>
  </ul>
</div>
<script type="text/javascript">
  $$('.core_main_sesmusic').getParent().addClass('active');
</script>