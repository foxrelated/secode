<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: _Player.tpl 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/styles/customscrollbar.css'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/jquery.min.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/customscrollbar.concat.min.js'); ?>
<?php
$playlist = $this->playlist;
$songs    = (isset($this->songs) && !empty($this->songs)) ? $this->songs : $playlist->getSongs();

// this forces every playlist to have a unique ID, so that a playlist can be displayed twice on the same page
$random   = '';
for ($i=0; $i<6; $i++) { $d=rand(1,30)%2; $random .= ($d?chr(rand(65,90)):chr(rand(48,57))); }
?>

<?php if (!$playlist->isViewable() && $this->message_view): ?>
<div class="tip">
  <?php echo $this->translate('This playlist is private.') ?>
</div>
<?php return; elseif (empty($songs) || empty($songs[0])): ?>
<br />
<div class="tip">
  <span>
    <?php echo $this->translate('There are no songs uploaded yet.') ?>
  </span>
</div>
<br />
<?php return; endif; ?>

<div id="sesmusic_player_<?php echo $random ?>" class="sesmusic_tracks_container sesbasic_custom_scroll">
  <ul class="clear sesmusic_tracks_list playlist_<?php echo $playlist->getIdentity() ?>">
    <?php foreach( $songs as $song ): ?>
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
          <a href="javascript:void(0);" onclick="play_music('<?php echo $song->albumsong_id ?>', '<?php echo $URL; ?>', '<?php echo $songTitle; ?>');" class="sesmusic_songslist_playbutton"><i class="fa fa-play"></i></a>
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