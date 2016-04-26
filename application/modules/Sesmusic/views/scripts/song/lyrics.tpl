<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: lyrics.tpl 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesmusic/externals/scripts/favourites.js'); ?> 
<?php if( 0 == count($this->paginator) ): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are currently no songs uploaded yet.') ?>
    </span>
  </div>
<?php else: ?>
<ul class="clear sesmusic_songslist_container playlist">
  <?php foreach( $this->paginator as $song ): ?>
    <?php if( !empty($song) ): ?>
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
                <?php if($song->hot): ?>
                  <span class="sesmusic_label_hot"><?php echo $this->translate("HOT"); ?></span>
                <?php endif; ?>
                <?php if($song->featured): ?>
                <span class="sesmusic_label_featured"><?php echo $this->translate("FEATURED"); ?></span>
                <?php endif; ?>
                <?php if($song->sponsored): ?>
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
              <?php if($song->category_id): ?>
                <div class="sesmusic_list_category floatR">
                  <?php $catName = Engine_Api::_()->getDbTable('categories', 'sesmusic')->getColumnName(array('column_name' => 'category_name', 'category_id' => $song->category_id, 'param' => 'song')); ?>
                  <a href="<?php echo $this->url(array('action' => 'browse'), 'sesmusic_songs', true).'?category_id='.urlencode($song->category_id) ; ?>"><?php echo $catName; ?></a>
                </div>
              <?php endif; ?>
              <div class="sesmusic_songslist_songname">
                <?php echo $this->htmlLink($song->getHref(), $song->getTitle(), array('class' => 'music_player_tracks_url', 'type' => 'audio', 'rel' => $song->song_id)); ?>
              </div>
              <div class="sesmusic_songslist_author sesbasic_text_light">
                <?php $album = Engine_Api::_()->getItem('sesmusic_albums', $song->album_id); ?>
                <?php echo $this->translate('by %s', $this->htmlLink($album->getOwner(), $album->getOwner()->getTitle())) ?><?php echo $this->translate(' on %s', $this->timestamp($song->creation_date)); ?><?php echo $this->translate(' in %s', $this->htmlLink($album->getHref(), $album->getTitle())); ?>
              </div>
              <?php if($this->showAlbumSongRating): ?>
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

              <?php //if(!empty($this->information) && in_array('artists', $this->information)): ?>
                <div class="sesmusic_songslist_artist clear sesbasic_text_light">
                  <?php if($song->artists):
                   $artists = json_decode($song->artists); 
                   if($artists): ?>
                    <?php echo $this->translate("Artists:"); ?>
                    <?php
                         // if($artists):
                          $artists_array = Engine_Api::_()->getDbTable('artists', 'sesmusic')->getArtists($artists); ?>
                    <?php foreach($artists_array as $key => $artist):  ?>
                        <?php echo $this->htmlLink(array('module'=>'sesmusic', 'controller'=>'artist', 'action'=>'view', 'route'=>'default', 'artist_id' => $key), $artist); ?>
                        <?php //echo $artist;  ?>
                    <?php endforeach; endif; ?>
                  <?php endif; ?>
                </div>
              <?php //endif; ?>
            </div>
          </div>

          <div class="sesmusic_songslist_info_bottom">
            <div class="sesmusic_songslist_options sesmusic_options_buttons">
              <?php if( $this->viewer()->getIdentity()): ?>
                <?php if($this->canAddPlaylistAlbumSong): ?>
                  <a title="<?php echo $this->translate('Add to Playlist');?>" href="javascript:void(0);" onclick="showPopUp('<?php echo $this->escape($this->url(array('action'=>'append','albumsong_id' => $song->albumsong_id, 'format' => 'smoothbox'), 'sesmusic_albumsong_specific' , true)); ?>'); return false;" class="fa fa-plus"><?php echo $this->translate('Add to Playlist');?></a>
                <?php endif; ?>

                <?php if($song->download && !$song->track_id && !$song->song_url && $this->downloadAlbumSong): ?>
                  <?php echo $this->htmlLink(array('route' => 'sesmusic_albumsong_specific', 'action' => 'download-song', 'albumsong_id' => $song->albumsong_id), $this->translate("Download"), array('class' => ' fa fa-download')); ?>                                     
                <?php elseif($song->download && $this->downloadAlbumSong): ?>
                  <?php $file = Engine_Api::_()->getItem('storage_file', $song->file_id); ?>
                  <?php if($file->mime_minor): ?>
                  <?php $consumer_key =  Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmusic.scclientid');
                  $downloadURL = 'http://api.soundcloud.com/tracks/' . $song->track_id . '/download?client_id=' . $consumer_key;  ?>
                  <a class='fa fa-download' href='<?php echo $downloadURL; ?>' target="_blank"><?php echo $this->translate("Download");  ?></a>
                  <?php endif; ?>
                <?php endif; ?>

                <?php if(!empty($this->songlink) && in_array('share', $this->songlink)): ?>
                <?php echo $this->htmlLink(array('module'=>'activity', 'controller'=>'index', 'action'=>'share', 'route'=>'default', 'type'=>'sesmusic_albumsong', 'id' => $song->getIdentity(), 'format' => 'smoothbox'), $this->translate("Share"), array('class' => 'smoothbox fa fa-share')); ?>
                <?php endif; ?>

                <?php if(!empty($this->songlink) && in_array('report', $this->songlink)): ?>
                <?php echo $this->htmlLink(array('module'=>'core', 'controller'=>'report', 'action'=>'create', 'route'=>'default', 'subject'=> $song->getGuid(), 'format' => 'smoothbox'), $this->translate("Report"), array('class' => 'smoothbox fa fa-flag')); ?>
                <?php endif; ?>

              <?php //if($this->information && in_array('favourite', $this->information)): ?>
                <?php $isFavourite = Engine_Api::_()->getDbTable('favourites', 'sesmusic')->isFavourite(array('resource_type' => "sesmusic_albumsong", 'resource_id' => $song->albumsong_id)); ?>
                <a class="fa fa-heart sesmusic_favourite" id="sesmusic_albumsong_unfavourite_<?php echo $song->getIdentity(); ?>" style ='display:<?php echo $isFavourite ? "inline-block" : "none" ?>' href = "javascript:void(0);" onclick = "sesmusicFavourite('<?php echo $song->getIdentity(); ?>', 'sesmusic_albumsong');"><?php echo $this->translate("Remove from Favorites") ?></a>
                <a class="fa fa-heart" id="sesmusic_albumsong_favourite_<?php echo $song->getIdentity(); ?>" style ='display:<?php echo $isFavourite ? "none" : "inline-block" ?>' href = "javascript:void(0);" onclick = "sesmusicFavourite('<?php echo $song->getIdentity(); ?>', 'sesmusic_albumsong');"><?php echo $this->translate("Add to Favorite") ?></a>
                <input type="hidden" id="sesmusic_albumsong_favouritehidden_<?php echo $song->getIdentity(); ?>" value='<?php echo $isFavourite ? $isFavourite : 0; ?>' />
              <?php //endif; ?>        
              <?php endif; ?>    
            </div>

            <div class="sesmusic_songslist_songstats sesbasic_text_light">
              <?php 
               $information = '';   
              // if(!empty($this->information) && in_array('playCount', $this->information))
               $information .= '<span title="Plays"><i class="fa fa-play"></i>' .$song->play_count. '</span>';

               //if(!empty($this->information) && in_array('downloadCount', $this->information))
                 $information .= '<span title="Downloads"><i class="fa fa-download"></i>' .$song->download_count. '</span>';
              // if(!empty($this->information) && in_array('favouriteCount', $this->information))
                 $information .= '<span title="Favourites"><i class="fa fa-heart"></i>' .$song->favourite_count. '</span>';
              //if(!empty($this->information) && in_array('likeCount', $this->information))
                 $information .= '<span title="Likes"><i class="fa fa-thumbs-up"></i>' .$song->like_count. '</span>'; 
              // if(!empty($this->information) && in_array('commentCount', $this->information))
                 $information .= '<span title="Comments"><i class="fa fa-comment"></i>' .$song->comment_count. '</span>';
             //  if(!empty($this->information) && in_array('viewCount', $this->information))
                 $information .= '<span title="Views"><i class="fa fa-eye"></i>' .$song->view_count. '</span>';
               ?>
                <?php echo $information ?>
              </div>
          </div>
        </div>
      </li>
    <?php endif; ?>
  <?php endforeach; ?>
</ul>
<?php echo $this->paginationControl($this->paginator, null, null, array('pageAsQuery' => true, 'query' => $this->formValues)); ?>
<?php endif; ?>
<script type="text/javascript">
  $$('.core_main_sesmusic').getParent().addClass('active');
</script>