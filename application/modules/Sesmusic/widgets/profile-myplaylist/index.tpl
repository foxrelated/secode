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
<ul class="sesmusic_side_block">
  <?php if(count($this->paginator) > 0): ?>
    <?php $item = Engine_Api::_()->getItem('sesmusic_playlist', $this->subject->infomusic_playlist); ?>
    <li class="sesmusic_profile_playlist_select">
      <?php if($this->viewer_id == $this->subject->getIdentity() && !empty($this->viewer_id)): ?>
        <span><b><?php echo  $this->translate("My Playlists") ?></b></span>
      <?php endif; ?>
      <?php if($this->viewer_id == $this->subject->getIdentity() && !empty($this->viewer_id)): ?>
        <form id="sesmusicplaylist" method="post" class="" action="<?php echo $this->url(array('module' => 'user', 'controller' => 'profile', 'action' => 'index', 'id' => $this->subject->getIdentity()), 'user_profile', true) ?>">
          <select onchange="sesmusicplaylist(this.value)" id="playlist_id" name="playlist_id">
            <option value="0" ></option>
            <?php foreach ( $this->paginator as $package): ?>
              <option value="<?php echo $package->playlist_id ?>" <?php if( $this->subject->infomusic_playlist == $package->playlist_id) echo "selected";?>> <?php echo $package->title ?></option>
            <?php  endforeach; ?>
          </select>
        </form>
      <?php endif; ?>
      <?php if($item): ?>
       <div class="sesmusic_playlist_grid_top sesbasic_clearfix">
          <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon')) ?>
          <div>
            <div class="sesmusic_playlist_grid_title">
              <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
            </div>
            <?php if(!empty($this->information) && in_array('postedby', $this->information)): ?>
            <div class="sesmusic_playlist_grid_stats  sesbasic_text_light">
              <?php echo $this->translate('by');?> <?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>     
            </div>
            <?php endif; ?>
            <div class="sesmusic_playlist_grid_stats sesmusic_list_stats sesbasic_text_light">
              <?php if (!empty($this->information) && in_array('favouriteCount', $this->information)): ?>
                <span title="<?php echo $this->translate(array('%s favorite', '%s favorites', $item->favourite_count), $this->locale()->toNumber($item->favourite_count)); ?>"><i class="fa fa-heart"></i><?php echo $item->favourite_count; ?></span>
              <?php endif; ?>
              <?php if (!empty($this->information) && in_array('viewCount', $this->information)): ?>
                <span title="<?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count)); ?>"><i class="fa fa-eye"></i><?php echo $item->view_count; ?></span>
              <?php endif; ?>
              <?php $songCount = Engine_Api::_()->getDbtable('playlistsongs', 'sesmusic')->playlistSongsCount(array('playlist_id' => $item->playlist_id));  ?>
              <?php if (!empty($this->information) && in_array('songCount', $this->information)): ?>
                <span title="<?php echo $this->translate(array('%s song', '%s song', $songCount), $this->locale()->toNumber($songCount)); ?>"><i class="fa fa-music"></i><?php echo $songCount; ?></span>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endif; ?>
      <?php $random   = '';
      for ($i=0; $i<6; $i++) { $d=rand(1,30)%2; $random .= ($d?chr(rand(65,90)):chr(rand(48,57))); }
      if(!empty($this->subject->infomusic_playlist)): 
      $playlist = Engine_Api::_()->getItem('sesmusic_playlist', $this->subject->infomusic_playlist);
      $songs = $playlist->getSongs(); ?>
        <?php if(count($songs) > 0): ?>
        <div id="sesmusic_player_<?php echo $random ?>" class="sesbasic_clearfix clear sesmusic_tracks_container sesbasic_custom_scroll">
          <ul class="sesmusic_tracks_list playlist_<?php echo $playlist->getIdentity() ?>">
            <?php foreach( $songs as $song ): ?>
            <?php if( !empty($song) ): ?>
            <?php $albumSongs = Engine_Api::_()->getItem('sesmusic_albumsong', $song->albumsong_id); ?>
            <li class="sesbasic_clearfix">     
              <div class="sesmusic_tracks_list_photo">
                <?php echo $this->htmlLink($albumSongs, $this->itemPhoto($albumSongs, 'thumb.icon') ) ?>
                <?php $songTitle = preg_replace('/[^a-zA-Z0-9\']/', ' ', $albumSongs->getTitle()); ?>
                <?php $songTitle = str_replace("'", '', $songTitle); ?>
                <?php if($albumSongs->track_id): ?>
                
                <?php $uploadoption = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmusic.uploadoption', 'both');
                $consumer_key =  Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmusic.scclientid'); ?>         
                <?php if(($uploadoption == 'both' || $uploadoption == 'soundCloud') && $consumer_key): ?>
                <?php $URL = "http://api.soundcloud.com/tracks/$albumSongs->track_id/stream?consumer_key=$consumer_key"; ?>
                <a href="javascript:void(0);" onclick="play_music('<?php echo $albumSongs->albumsong_id ?>', '<?php echo $URL; ?>', '<?php echo $songTitle; ?>');" class="sesmusic_songslist_playbutton"><i class="fa fa-play"></i></a>
                <?php else: ?>
                <a href="javascript:void(0);" class="sesmusic_songslist_playbutton"><i class="fa fa-play"></i></a>
                <?php endif; ?>
                <?php else: ?>
                <a href="javascript:void(0);" onclick="play_music('<?php echo $albumSongs->albumsong_id ?>', '<?php echo $albumSongs->getFilePath(); ?>', '<?php echo $songTitle; ?>');" class="sesmusic_songslist_playbutton"><i class="fa fa-play"></i></a>
                <?php endif; ?>
              </div>
              <div class="sesmusic_tracks_list_name" title="<?php echo $song->getTitle() ?>">
                <?php echo $this->htmlLink($albumSongs->getFilePath(), $this->htmlLink($albumSongs->getHref(), $albumSongs->getTitle()), array('class' => 'music_player_tracks_url', 'type' => 'audio', 'rel' => $albumSongs->song_id)); ?>
              </div>
            </li>
            <?php endif; ?>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php elseif($this->viewer_id && $this->viewer_id == $this->subject->getIdentity()): ?>
          <div class="tip">
            <span>
              <?php echo $this->translate('There are no songs in this playlist. Please add some song in this playlist.') ?>
            </span>
          </div>
       <?php endif; ?>
       <?php elseif($this->viewer_id && $this->viewer_id == $this->subject->getIdentity()): ?>
          <div class="tip">
            <span>
              <?php echo $this->translate('Choose a playlist to be shown on your Profile.') ?>
            </span>
          </div>
      <?php endif; ?>
    </span>
  </li>
  <?php else: ?>
    <li class="sesmusic_profile_playlist_select">
      <span>
        <label>
          <b><?php echo $this->translate("My Playlist") ?></b>
        </label>
      </span>
      <span>
        <div class="tip">
          <span>
            <?php $link = $this->htmlLink(array('route' => 'sesmusic_general', 'action' => 'home'), $this->translate('Click Here')) ?>
            <?php echo $this->translate('There are no playlists yet. %s to go to music and click on "+" icon to add desired songs or music albums to your playlists. After creating a playlist comeback here to choose your playlist.', $link) ?>
          </span>
        </div>
      </span>
    </li>
  <?php endif; ?>
</ul>
<script type="text/javascript">

function sesmusicplaylist(value) {
  $("sesmusicplaylist").submit();
  window.opener.location.reload();
}
</script>
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