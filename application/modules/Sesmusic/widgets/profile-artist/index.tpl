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
$artist = $this->artists;
?>
<?php if($this->showRating): ?>
  <script type="text/javascript">
    
    en4.core.runonce.add(function() {
      var pre_rate = '<?php echo $this->artists->rating;?>';
      
      <?php if($this->viewer_id == 0){ ?>
      rated = 0;
      <?php } elseif($this->allowShowRating == 1 && $this->allowRating == 0) { ?>
      var rated = 3;
      <?php } elseif($this->allowRateAgain == 0 && $this->rated) { ?>
      var rated = 1;
      <?php } elseif($this->canRate == 0 && $this->viewer_id != 0) { ?>
      var rated = 4;
      <?php } elseif(!$this->allowMine) { ?>
      var rated = 2;
      <?php } else { ?>
      var rated = '90';
      <?php } ?>
    
      var resource_id = '<?php echo $this->artists->artist_id;?>';
      var total_votes = '<?php echo $this->rating_count;?>';
      var viewer = '<?php echo $this->viewer_id;?>';
      new_text = '';

      var rating_over = window.rating_over = function(rating) {
        if( rated == 1 ) {
          $('rating_text').innerHTML = "<?php echo $this->translate('You already rated.');?>";
          return;
          //set_rating();
        }
        <?php if(!$this->canRate) { ?>
          else if(rated == 4){
               $('rating_text').innerHTML = "<?php echo $this->translate('You are not allowed to rate.');?>";
               return;
          }
        <?php } ?>
        <?php if(!$this->allowMine) { ?>
          else if(rated == 2){
               $('rating_text').innerHTML = "<?php echo $this->translate('Rating on own album is not allowed.');?>";
               return;
          }
        <?php } ?>
        <?php if($this->allowShowRating == 1) { ?>
          else if(rated == 3){
               $('rating_text').innerHTML = "<?php echo $this->translate('You are not allowed to rate.');?>";
               return;
          }
        <?php } ?>
        else if( viewer == 0 ) {
          $('rating_text').innerHTML = "<?php echo $this->translate('Please login to rate.');?>";
          return;
        } else {
          $('rating_text').innerHTML = "<?php echo $this->translate('Click to rate.');?>";
          for(var x=1; x<=5; x++) {
            if(x <= rating) {
              $('rate_'+x).set('class', 'fa fa-star');
            } else {
              $('rate_'+x).set('class', 'fa fa-star-o star-disable');
            }
          }
        }
      }

      var rating_out = window.rating_out = function() {
        if (new_text != ''){
          $('rating_text').innerHTML = new_text;
        }
        else{
          $('rating_text').innerHTML = " <?php echo $this->translate(array('%s rating', '%s ratings', $this->rating_count),$this->locale()->toNumber($this->rating_count)) ?>";        
        }
        if (pre_rate != 0){
          set_rating();
        }
        else {
          for(var x=1; x<=5; x++) {
            $('rate_'+x).set('class', 'fa fa-star-o star-disable');
          }
        }
      }

      var set_rating = window.set_rating = function() {
        var rating = pre_rate;
        if (new_text != ''){
          $('rating_text').innerHTML = new_text;
        }
        else{
          $('rating_text').innerHTML = "<?php echo $this->translate(array('%s rating', '%s ratings', $this->rating_count),$this->locale()->toNumber($this->rating_count)) ?>";
        }
        for(var x=1; x<=parseInt(rating); x++) {
          $('rate_'+x).set('class', 'fa fa-star');
        }

        for(var x=parseInt(rating)+1; x<=5; x++) {
          $('rate_'+x).set('class', 'fa fa-star-o star-disable');
        }

        var remainder = Math.round(rating)-rating;
        if (remainder <= 0.5 && remainder !=0){
          var last = parseInt(rating)+1;
          $('rate_'+last).set('class', 'fa fa-star-half-o');
        }
      }

      var rate = window.rate = function(rating) {
        $('rating_text').innerHTML = "<?php echo $this->translate('Thanks for rating.');?>";
        <?php if($this->allowRateAgain == 0 && !$this->rated){ ?>
               for(var x=1; x<=5; x++) {
                  $('rate_'+x).set('onclick', '');
                }
            <?php } ?>

        (new Request.JSON({
          'format': 'json',
          'url' : '<?php echo $this->url(array('module' => 'sesmusic', 'controller' => 'album', 'action' => 'rate'), 'default', true) ?>',
          'data' : {
            'format' : 'json',
            'rating' : rating,
            'resource_id': resource_id,
            'resource_type':'<?php echo $this->rating_type; ?>'
          },
          'onSuccess' : function(responseJSON, responseText)
          {
            <?php if($this->allowRateAgain == 0 && !$this->rated){ ?>
                rated = 1;
            <?php } ?>
            total_votes = responseJSON[0].total;
            var rating_sum = responseJSON[0].rating_sum;
            pre_rate = rating_sum / total_votes;
            set_rating();
            $('rating_text').innerHTML = responseJSON[0].total+" ratings";
            new_text = responseJSON[0].total+" ratings";
          }
        })).send();
      }
      set_rating();
    });
  </script>
<?php endif; ?>

<div class="sesmusic_item_view_wrapper clear">
  <div class="sesmusic_item_view_top">
    <div class="sesmusic_item_view_artwork">
      <?php if($artist->artist_photo): ?>
      <?php $img_path = Engine_Api::_()->storage()->get($artist->artist_photo, '')->getPhotoUrl();
      $path = $img_path; 
      ?>
      <img src="<?php echo $path ?>">
      <?php else: ?>
       <img src="<?php //echo $path ?>">
      <?php endif; ?>
    </div>
    <div class="sesmusic_item_view_info">
      <div class="sesmusic_item_view_title">
        <?php echo $artist->name ?>
      </div>
      <?php if(!empty($this->information)): ?>
      <?php if(in_array('favouriteCountAr', $this->informationArtist) || in_array('ratingCountAr', $this->informationArtist)): ?>
        <p class="sesmusic_item_view_stats sesbasic_text_light">
          <?php if(in_array('favouriteCountAr', $this->information)): ?>
           <?php echo $this->translate(array('%s favorite', '%s favorites', $this->artists->favourite_count), $this->locale()->toNumber($this->artists->favourite_count)) ?>
          <?php endif; ?>
          <?php if(in_array('ratingCountAr', $this->informationArtist) && $this->showRating && in_array('favouriteCountAr', $this->information)): ?>
          &nbsp;|&nbsp;
          <?php endif; ?>
          <?php if(in_array('ratingCountAr', $this->informationArtist) && $this->showRating): ?>
            <?php echo $this->translate(array('%s rating', '%s ratings', $this->artists->rating), $this->locale()->toNumber($this->artists->rating)); ?>
          <?php endif; ?>
        </p>
      <?php endif; ?>
      <?php if(in_array('description', $this->informationArtist)): ?>
        <p class="sesmusic_item_view_des">
          <?php echo $this->viewMore($this->artists->overview) ?>
        </p>
      <?php endif; ?>
      
      <?php if(in_array('ratingStarsAr', $this->informationArtist) && $this->showRating):  ?>
        <div id="album_rating" class="sesbasic_rating_star" onmouseout="rating_out();">
          <span id="rate_1" class="fa fa-star" <?php  if ($this->viewer_id && $this->ratedAgain && $this->allowMine && $this->allowRating ):?>onclick="rate(1);"<?php  endif; ?> onmouseover="rating_over(1);"></span>
          <span id="rate_2" class="fa fa-star" <?php if ( $this->viewer_id && $this->ratedAgain && $this->allowMine && $this->allowRating):?>onclick="rate(2);"<?php endif; ?> onmouseover="rating_over(2);"></span>
          <span id="rate_3" class="fa fa-star" <?php if ( $this->viewer_id && $this->ratedAgain  && $this->allowMine && $this->allowRating):?>onclick="rate(3);"<?php endif; ?> onmouseover="rating_over(3);"></span>
          <span id="rate_4" class="fa fa-star" <?php if ($this->viewer_id && $this->ratedAgain && $this->allowMine && $this->allowRating):?>onclick="rate(4);"<?php endif; ?> onmouseover="rating_over(4);"></span>
          <span id="rate_5" class="fa fa-star" <?php if ($this->viewer_id && $this->ratedAgain  && $this->allowMine && $this->allowRating):?>onclick="rate(5);"<?php endif; ?> onmouseover="rating_over(5);"></span>
          <span id="rating_text" class="sesbasic_rating_text"><?php echo $this->translate('click to rate');?></span>
        </div>
      <?php endif; ?>

      <div class="sesmusic_options_buttons sesmusic_item_view_options">
         <?php if (in_array('addFavouriteButtonAr', $this->informationArtist) && !empty($this->viewer_id)): ?>
         <?php if($this->artistlink && in_array('favourite', $this->artistlink)): ?>
           <a class="fa fa-heart sesmusic_favourite" id="sesmusic_artist_unfavourite_<?php echo $this->artists->getIdentity(); ?>" style ='display:<?php echo $this->isFavourite ? "inline-block" : "none" ?>' href = "javascript:void(0);" onclick = "sesmusicFavourite('<?php echo $this->artists->getIdentity(); ?>', 'sesmusic_artist');"><?php echo $this->translate("Remove from Favorite") ?></a>
            <a class="fa fa-heart" id="sesmusic_artist_favourite_<?php echo $this->artists->getIdentity(); ?>" style ='display:<?php echo $this->isFavourite ? "none" : "inline-block" ?>' href = "javascript:void(0);" onclick = "sesmusicFavourite('<?php echo $this->artists->getIdentity(); ?>', 'sesmusic_artist');"><?php echo $this->translate("Add to Favorite") ?></a>
          <input type="hidden" id="sesmusic_artist_favouritehidden_<?php echo $this->artists->getIdentity(); ?>" value='<?php echo $this->isFavourite ? $this->isFavourite : 0; ?>' />
          <?php endif; ?>
        <?php endif; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
  
  
  <ul class="clear sesmusic_songslist_container playlist_<?php echo $artist->getIdentity() ?>">
    <?php if(count($this->artistSongs) > 0 && !empty($this->information)): ?>
    <?php foreach( $this->artistSongs as $song ):  ?>
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
                
                <?php if(in_array('postedBy', $this->information)): ?>
                  <div class="sesmusic_songslist_author sesbasic_text_light">
                    <?php $album = Engine_Api::_()->getItem('sesmusic_albums', $song->album_id); ?>
                    <?php echo $this->translate('by %s', $this->htmlLink($album->getOwner(), $album->getOwner()->getTitle())) ?><?php echo $this->translate(' on %s', $this->timestamp($song->creation_date)); ?><?php echo $this->translate(' in %s', $this->htmlLink($album->getHref(), $album->getTitle())); ?>
                  </div>
                <?php endif; ?>
                <?php if($this->showAlbumSongRating && in_array('ratingStars', $this->information)): ?>
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
                  <?php if($this->canAddPlaylistAlbumSong && in_array('addplaylist', $this->information)): ?>
                    <a title="<?php echo $this->translate('Add to Playlist');?>" href="javascript:void(0);" onclick="showPopUp('<?php echo $this->escape($this->url(array('action'=>'append','albumsong_id' => $song->albumsong_id, 'format' => 'smoothbox'), 'sesmusic_albumsong_specific' , true)); ?>'); return false;" class="fa fa-plus"><?php echo $this->translate('Add to Playlist');?></a>
                  <?php endif; ?>

                  <?php if($song->download && !$song->track_id && !$song->song_url && $this->downloadAlbumSong  && in_array('downloadButton', $this->information)): ?>
                    <?php echo $this->htmlLink(array('route' => 'sesmusic_albumsong_specific', 'action' => 'download-song', 'albumsong_id' => $song->albumsong_id), $this->translate("Download"), array('class' => ' fa fa-download')); ?>                   
                  <?php elseif($song->download && in_array('downloadButton', $this->information)): ?>
                    <?php $file = Engine_Api::_()->getItem('storage_file', $song->file_id); ?>
                    <?php if($file->mime_minor && $this->downloadAlbumSong): ?>
                    <?php $consumer_key =  Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmusic.scclientid');
                    $downloadURL = 'http://api.soundcloud.com/tracks/' . $song->track_id . '/download?client_id=' . $consumer_key;  ?>
                    <a class='fa fa-download' href='<?php echo $downloadURL; ?>' target="_blank"><?php echo $this->translate("Download");  ?></a>
                    <?php endif; ?>
                  <?php endif; ?>

                  <?php if(!empty($this->songlink) && in_array('share', $this->songlink) && in_array('share', $this->information)): ?>
                  <?php echo $this->htmlLink(array('module'=>'activity', 'controller'=>'index', 'action'=>'share', 'route'=>'default', 'type'=>'sesmusic_albumsong', 'id' => $song->getIdentity(), 'format' => 'smoothbox'), $this->translate("Share"), array('class' => 'smoothbox fa fa-share')); ?>
                  <?php endif; ?>

                  <?php if(!empty($this->songlink) && in_array('report', $this->songlink)   && in_array('report', $this->information)): ?>
                  <?php echo $this->htmlLink(array('module'=>'core', 'controller'=>'report', 'action'=>'create', 'route'=>'default', 'subject'=> $song->getGuid(), 'format' => 'smoothbox'), $this->translate("Report"), array('class' => 'smoothbox fa fa-flag')); ?>
                  <?php endif; ?>

                <?php if(in_array('addFavouriteButton', $this->information)): ?>
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
                    if(in_array('playCount', $this->information))
                    $information .= '<span title="Play"><i class="fa fa-play"></i>' .$song->play_count. '</span>';
                    if(in_array('downloadCount', $this->information))
                    $information .= '<span title="Download"><i class="fa fa-download"></i>' .$song->download_count. '</span>';
                    if(in_array('favouriteCount', $this->information))
                    $information .= '<span title="Favourite"><i class="fa fa-heart"></i>' .$song->favourite_count. '</span>';
                    if(in_array('likeCount', $this->information))
                    $information .= '<span title="Like"><i class="fa fa-thumbs-up"></i>' .$song->like_count. '</span>'; 
                    if(in_array('commentCount', $this->information))
                    $information .= '<span title="Comment"><i class="fa fa-comment"></i>' .$song->comment_count. '</span>';
                    if(in_array('viewCount', $this->information))
                    $information .= '<span title="View"><i class="fa fa-eye"></i>' .$song->view_count. '</span>';
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
<script type="text/javascript">
  $$('.core_main_sesmusic').getParent().addClass('active');
</script>
</div>
