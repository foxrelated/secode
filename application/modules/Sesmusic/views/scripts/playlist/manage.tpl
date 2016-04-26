<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: manage.tpl 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/styles/customscrollbar.css'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/jquery.min.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/customscrollbar.concat.min.js'); ?>
<?php
// this forces every playlist to have a unique ID, so that a playlist can be displayed twice on the same page
$random   = '';
for ($i=0; $i<6; $i++) { $d=rand(1,30)%2; $random .= ($d?chr(rand(65,90)):chr(rand(48,57))); }
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesmusic/externals/scripts/favourites.js'); ?>
<?php if(count($this->paginator) > 0): ?>
    <ul class="sesmusic_list sesmusic_browse_playlist" id= "playlists_results">
  <?php foreach ($this->paginator as $item):  ?>
    <li id="music_playlist_item_<?php echo $item->getIdentity() ?>" class="sesbasic_clearfix">
    <div class="sesmusic_list_artwork">
      <div>
        <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.profile'), array('class' => 'thumb')) ?>
      </div>
    </div>
    <div class="sesmusic_list_info">
      <div class="sesmusic_list_info_title">
        <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
      </div>
      <div class="sesmusic_list_info_stats sesbasic_text_light">
        <?php echo $this->translate('Created By %s', $this->htmlLink($item->getOwner(), $item->getOwner()->getTitle())) ?>
      </div>
        <div class="sesmusic_list_info_stats sesbasic_text_light">
          <?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count)) ?>
        </div>
      <div class="sesmusic_listinfo_desc">
        <?php echo $this->viewMore(nl2br($item->description)); ?>
      </div>
      <?php if($this->viewer_id): ?>
      <div class="sesmusic_options_buttons">
          <?php if($this->viewer_id == $item->owner_id): ?>

          <?php echo $this->htmlLink($item->getHref(array('route' => 'sesmusic_playlist_specific', 'action' => 'edit')), $this->translate('Edit Playlist'), array('class'=>'fa fa-pencil')); ?>

          <?php echo $this->htmlLink(array('route' => 'sesmusic_playlist_specific', 'module' => 'sesmusic', 'controller' => 'playlist', 'action' => 'delete', 'playlist_id' => $item->getIdentity(), 'slug' => $item->getSlug(), 'format' => 'smoothbox'), $this->translate('Delete Playlist'), array('class' => 'smoothbox fa fa-trash')); ?>
          <?php endif; ?>          
          <?php $isFavourite = Engine_Api::_()->getDbTable('favourites', 'sesmusic')->isFavourite(array('resource_type' => "sesmusic_playlist", 'resource_id' => $item->getIdentity())); ?>
          <a class="fa fa-heart sesmusic_favourite" id="sesmusic_playlist_unfavourite_<?php echo $item->getIdentity(); ?>" style ='display:<?php echo $isFavourite ? "inline-block" : "none" ?>' href = "javascript:void(0);" onclick = "sesmusicFavourite('<?php echo $item->getIdentity(); ?>', 'sesmusic_playlist');" title="<?php echo $this->translate("Remove from Favorite") ?>"><?php echo $this->translate("Remove from Favorite") ?></a>
          <a class="fa fa-heart" id="sesmusic_playlist_favourite_<?php echo $item->getIdentity(); ?>" style ='display:<?php echo $isFavourite ? "none" : "inline-block" ?>' href = "javascript:void(0);" onclick = "sesmusicFavourite('<?php echo $item->getIdentity(); ?>', 'sesmusic_playlist');" title="<?php echo $this->translate("Add to Favorite") ?>"><?php echo $this->translate("Add to Favorite") ?></a>
          <input type="hidden" id="sesmusic_playlist_favouritehidden_<?php echo $item->getIdentity(); ?>" value='<?php echo $isFavourite ? $isFavourite : 0; ?>' />
          <?php if($this->viewer_id): ?>
          <a  class="fa fa-share" title='<?php echo $this->translate("Share") ?>' href="javascript:void(0);" onclick="showPopUp('<?php echo $this->escape($this->url(array('module'=>'activity', 'controller'=>'index', 'action'=>'share', 'route'=>'default', 'type'=>'sesmusic_playlist', 'id' => $item->playlist_id, 'format' => 'smoothbox'), 'default' , true)); ?>'); return false;" ><?php echo $this->translate("Share"); ?></a>
         <?php endif; ?>
      </div>
      <?php endif; ?>   
      <?php $playlist = $item; 
      $songs = $item->getSongs();
      ?>
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
       </div>
    </li>
  <?php endforeach; ?>

</ul>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are currently no playlists created yet.') ?>
    </span>
  </div>
<?php endif; ?>

<?php if (empty($this->viewmore)): ?>
  <script type="text/javascript">
    $$('.core_main_sesmusic').getParent().addClass('active');
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
<?php endif; ?>