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

<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesmusic/externals/scripts/favourites.js'); ?>
<script type="text/javascript">
  function showPopUp(url) {
    Smoothbox.open(url);
    parent.Smoothbox.close;
  }
</script>
<?php if($this->showType == 'gridview'): ?>
<?php if(count($this->results) > 0) :?>
  <ul class="sesmusic_playlist_grid_listing sesbasic_clearfix">
    <?php foreach( $this->results as $item ):  ?>
      
      <li class="sesmusic_playlist_grid" style="width:<?php echo $this->width ?>px;">
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
        <?php $songs = $item->getSongs(); ?>
        <?php if($songs && !empty($this->information) && in_array('songsListShow', $this->information)):  ?>
        <div class="clear sesbasic_clearfix sesmusic_tracks_container sesbasic_custom_scroll">
          <ul class="clear sesmusic_tracks_list">
            
            <?php foreach( $songs as $song ):  ?>
             <?php $song = Engine_Api::_()->getItem('sesmusic_albumsong', $song->albumsong_id); ?>
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
                <div class="sesmusic_tracks_list_name" title="<?php echo $song->getTitle() ?>">
                    <?php echo $this->htmlLink($song->getFilePath(), $this->htmlLink($song->getHref(), $song->getTitle()), array('class' => 'music_player_tracks_url', 'type' => 'audio', 'rel' => $song->song_id)); ?>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php endif; ?>
      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>
<?php elseif($this->showType == 'carouselview'): ?>

  <?php $randonNumber = $this->identity; ?>
  <?php $baseUrl = $this->layout()->staticBaseUrl; ?>
  <script type="text/javascript" src="<?php echo $baseUrl; ?>application/modules/Sesbasic/externals/scripts/PeriodicalExecuter.js"></script>
  <script type="text/javascript" src="<?php echo $baseUrl; ?>application/modules/Sesbasic/externals/scripts/Carousel.js"></script>
  <script type="text/javascript" src="<?php echo $baseUrl; ?>application/modules/Sesbasic/externals/scripts/Carousel.Extra.js"></script>
  <?php $this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sesmusic/externals/styles/carousel.css'); ?>

  <style>
    #playlistslide_<?php echo $randonNumber; ?> {
      position: relative;
      height:<?php echo $this->height ?>px;
      overflow: hidden;
    }
  </style>

  <div class="slide sesmusic_carousel_wrapper clearfix <?php if($this->viewType == 'horizontal'): ?> sesmusic_carousel_h_wrapper <?php else: ?> sesmusic_carousel_v_wrapper <?php endif; ?>">
    <div id="playlistslide_<?php echo $randonNumber; ?>">
    <?php foreach( $this->results as $item ):  ?>
      <div class="sesmusic_playlist_grid sesbasic_bxs" style="height:<?php echo $this->height ?>px;width:<?php echo $this->width ?>px;">
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
        <?php $songs = $item->getSongs(); ?>
        <?php if($songs && !empty($this->information) && in_array('songsListShow', $this->information)):  ?>
        <div class="clear sesbasic_clearfix sesmusic_tracks_container sesbasic_custom_scroll">
          <ul class="clear sesmusic_tracks_list">
            
            <?php foreach( $songs as $song ):  ?>
             <?php $song = Engine_Api::_()->getItem('sesmusic_albumsong', $song->albumsong_id); ?>
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
                <div class="sesmusic_tracks_list_name" title="<?php echo $song->getTitle() ?>">
                    <?php echo $this->htmlLink($song->getFilePath(), $this->htmlLink($song->getHref(), $song->getTitle()), array('class' => 'music_player_tracks_url', 'type' => 'audio', 'rel' => $song->song_id)); ?>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
    </div>
    <?php if($this->viewType == 'horizontal'): ?>
      <div class="tabs_<?php echo $randonNumber; ?> sesmusic_carousel_nav">
        <a class="sesmusic_carousel_nav_pre" href="#page-p"><i class="fa fa-caret-left"></i></a>
        <a class="sesmusic_carousel_nav_nxt" href="#page-p"><i class="fa fa-caret-right"></i></a>
      </div>  
    <?php else: ?>
      <div class="tabs_<?php echo $randonNumber; ?> sesmusic_carousel_nav">
        <a class="sesmusic_carousel_nav_pre" href="#page-p"><i class="fa fa-caret-up"></i></a>
        <a class="sesmusic_carousel_nav_nxt" href="#page-p"><i class="fa fa-caret-down"></i></a>
      </div>  
    <?php endif; ?>

  </div>
  <script type="text/javascript">
  window.addEvent('domready', function() {
    var duration = 150,
    div = document.getElement('div.tabs_<?php echo $randonNumber; ?>');
    links = div.getElements('a'),
    carousel = new Carousel.Extra({
      activeClass: 'selected',
      container: 'playlistslide_<?php echo $randonNumber; ?>',
      circular: true,
      current: 1,
      previous: links.shift(),
      next: links.pop(),
      tabs: links,
      mode: '<?php echo $this->viewType; ?>',
      fx: {
        duration: duration
      }
    })
  });
  </script>
<?php endif; ?>


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