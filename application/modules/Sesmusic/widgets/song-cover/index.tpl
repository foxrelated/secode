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

<?php if($this->information && in_array('addFavouriteButton', $this->information)): ?>
  <?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesmusic/externals/scripts/favourites.js'); ?>
<?php endif; ?>

<?php $album = $this->album; ?>

<?php if($this->showRating): ?>
  <script type="text/javascript">
    
    en4.core.runonce.add(function() {
      var pre_rate = '<?php echo $this->albumsong->rating;?>';
      
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
      var resource_id = '<?php echo $this->albumsong->albumsong_id;?>';
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
               $('rating_text').innerHTML = "<?php echo $this->translate('Rating on own song is not allowed.');?>";
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

<?php if($this->songCover): ?>
<?php if($this->albumsong->song_cover): ?>
  <?php $storage = Engine_Api::_()->storage()->get($this->albumsong->song_cover, '')->getPhotoUrl(); 
  $photoUrl = $storage;
  //$songCoverPhoto = "<img src='$photoUrl' height='" . $this->height. "px;' alt='' align='left' />";
  ?>
<?php else: ?>
  <?php if($this->songCoverPhoto): ?>
  <?php 
  $photoUrl = $this->baseUrl() . '/' . $this->songCoverPhoto;
  $songCoverPhoto = "<img src='$photoUrl' height='" . $this->height. "px;' alt='' align='left' />";
  ?>
  <?php else: ?>
  <?php 
  $photoUrl = $this->baseUrl() . '/application/modules/Sesmusic/externals/images/banner/cover.jpg';
  $songCoverPhoto = "<img src='$photoUrl' height='" . $this->height. "px;' alt='' align='left' />";
  ?>
<?php endif; ?>
<?php endif; ?>
  <div class="sesmusic_cover" style="background-image:url(<?php echo $photoUrl ?>); height:<?php echo $this->height; ?>px;">
    <div class="sesmusic_cover_inner">
      <?php if($this->information && in_array('photo', $this->information)): ?>
        <div class="sesmusic_cover_music_artwork" style="height:<?php echo $this->mainPhotoHeight; ?>px;width:<?php echo $this->mainPhotowidth; ?>px;">
          <?php if($this->albumsong->photo_id): ?>
            <?php $img_path = Engine_Api::_()->storage()->get($this->albumsong->photo_id, '')->getPhotoUrl();
            $path = $img_path; 
            ?>
            <span style="background-image:url(<?php echo $path ?>)"></span>
          <?php elseif(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmusic.songdefaultphoto')): ?>
            <?php $defaultPhoto = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmusic.songdefaultphoto');
            $path = $this->baseUrl() . '/' . $defaultPhoto;
            ?>
            <span style="background-image:url(<?php echo $path ?>)"></span>
          <?php else: ?>
            <?php $path = $this->baseUrl() . '/application/modules/Sesmusic/externals/images/nophoto_albumsong_thumb_main.png';  ?>
            <span style="background-image:url(<?php echo $path ?>)"></span>
          <?php endif; ?>
          <?php if($this->albumsong->featured || $this->albumsong->sponsored || $this->albumsong->hot ): ?>
            <div class="sesmusic_item_info_label">
              <?php if(in_array('hot', $this->information)): ?>
                <span class="sesmusic_label_hot"><?php echo $this->translate("HOT"); ?></span>
              <?php endif; ?>
              <?php if(in_array('featured', $this->information)): ?>
                <span class="sesmusic_label_featured"><?php echo $this->translate("FEATURED"); ?></span>
              <?php endif; ?>
              <?php if(in_array('sponsored', $this->information)): ?>
               <span class="sesmusic_label_sponsored"><?php echo $this->translate("SPONSORED"); ?></span>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        </div>
      <?php endif; ?>
      <div class="sesmusic_cover_content">
        <div class="sesmusic_cover_title">
          <?php echo $this->albumsong->getTitle() ?>
        </div>
        <p class="sesmusic_item_view_stats">
          <?php if($this->information && in_array('postedBy', $this->information)): ?>
            <?php echo $this->translate('Album By %s', $this->htmlLink($album->getOwner(), $album->getOwner()->getTitle())) ?><?php echo $this->translate(' in %s', $this->htmlLink($album->getHref(), $album->getTitle())) ?>
          <?php endif ;?>
          <?php if($this->information && in_array('creationDate', $this->information)): ?>|
            <?php echo $this->translate('Released %s', $album->creation_date); ?>
          <?php endif ;?>
        </p>
        <?php if(!empty($this->information) && in_array('category', $this->information) && $this->albumsong->category_id) :?>
          <p class="sesmusic_item_view_stats">
            <?php $catName = $this->categoriesTable->getColumnName(array('column_name' => 'category_name', 'category_id' => $this->albumsong->category_id, 'param' => 'song')); ?>
            <a href="<?php echo $this->url(array('action' => 'browse'), 'sesmusic_songs', true).'?category_id='.urlencode($this->albumsong->category_id) ; ?>"><?php echo $catName; ?></a>
            <?php if($this->albumsong->subcat_id): ?>
            <?php $subcatName = $this->categoriesTable->getColumnName(array('column_name' => 'category_name', 'category_id' => $this->albumsong->subcat_id, 'param' => 'song')); ?>
            &nbsp;&raquo;
            <a href="<?php echo $this->url(array('action' => 'browse'), 'sesmusic_songs', true).'?subcat_id='.urlencode($this->albumsong->subcat_id) ; ?>"><?php echo $subcatName; ?></a>
            <?php endif; ?>
            <?php if($this->albumsong->subsubcat_id): ?>
            <?php $subsubcatName = $this->categoriesTable->getColumnName(array('column_name' => 'category_name', 'category_id' => $this->albumsong->subsubcat_id, 'param' => 'song')); ?>
            &nbsp;&raquo;
            <a href="<?php echo $this->url(array('action' => 'browse'), 'sesmusic_songs', true).'?subsubcat_id='.urlencode($this->albumsong->subsubcat_id) ; ?>"><?php echo $subsubcatName; ?></a>
            <?php endif; ?>
          </p>
        <?php endif; ?>
        <p class="sesmusic_item_view_stats">
         <?php 
            $information = '';   
            if ($this->showRating && !empty($this->information) && in_array('ratingCount', $this->information))
              $information .= $this->translate(array('%s rating', '%s ratings', $this->albumsong->rating), $this->locale()->toNumber($this->albumsong->rating)) . ' | ';

            if (!empty($this->information) && in_array('likeCount', $this->information))
             $information .= $this->translate(array('%s like', '%s likes', $this->albumsong->like_count), $this->locale()->toNumber($this->albumsong->like_count)) . ' | '; 

            if (!empty($this->information) && in_array('commentCount', $this->information))
              $information .= $this->translate(array('%s comment', '%s comments', $this->albumsong->comment_count), $this->locale()->toNumber($this->albumsong->comment_count)) . ' | ';

            if (!empty($this->information) && in_array('viewCount', $this->information))
             $information .= $this->translate(array('%s view', '%s views', $this->albumsong->view_count), $this->locale()->toNumber($this->albumsong->view_count)) . ' | ';

            if (!empty($this->information) && in_array('favouriteCount', $this->information))
              $information .= $this->translate(array('%s favorite', '%s favorites', $this->albumsong->favourite_count), $this->locale()->toNumber($this->albumsong->favourite_count)) . ' | ';

            if (!empty($this->information) && in_array('playCount', $this->information))
              $information .= $this->translate(array('%s play', '%s plays', $this->albumsong->play_count), $this->locale()->toNumber($this->albumsong->play_count)) . ' | ';

            $information = trim($information);
            $information = rtrim($information, '|');
          ?>
          <?php echo $information; ?>
        </p>

        <?php if($this->showRating == 1 && !empty($this->information) && in_array('ratingStars', $this->information)):  ?>
          <div id="album_rating" class="sesbasic_rating_star sesmusic_cover_rating" onmouseout="rating_out();">
            <span id="rate_1" class="fa fa-star" <?php  if ($this->viewer_id && $this->ratedAgain && $this->allowMine && $this->allowRating ):?>onclick="rate(1);"<?php  endif; ?> onmouseover="rating_over(1);"></span>
            <span id="rate_2" class="fa fa-star" <?php if ( $this->viewer_id && $this->ratedAgain && $this->allowMine && $this->allowRating):?>onclick="rate(2);"<?php endif; ?> onmouseover="rating_over(2);"></span>
            <span id="rate_3" class="fa fa-star" <?php if ( $this->viewer_id && $this->ratedAgain  && $this->allowMine && $this->allowRating):?>onclick="rate(3);"<?php endif; ?> onmouseover="rating_over(3);"></span>
            <span id="rate_4" class="fa fa-star" <?php if ($this->viewer_id && $this->ratedAgain && $this->allowMine && $this->allowRating):?>onclick="rate(4);"<?php endif; ?> onmouseover="rating_over(4);"></span>
            <span id="rate_5" class="fa fa-star" <?php if ($this->viewer_id && $this->ratedAgain  && $this->allowMine && $this->allowRating):?>onclick="rate(5);"<?php endif; ?> onmouseover="rating_over(5);"></span>
            <span id="rating_text" class="sesbasic_rating_text"><?php echo $this->translate('click to rate');?></span>
          </div>
        <?php endif; ?>
      </div>
      <?php //Album Song Like Users ?>
      <div class="sesmusic_cover_content">
        <?php //foreach( $this->results as $item ): ?>
            <?php //$user = Engine_Api::_()->getItem('user', $item['poster_id']); ?>
            <?php //echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'), array('title' => $user->getTitle(), 'target' => '_parent')); ?>
        <?php //endforeach; ?>
      </div>
      <div class="sesmusic_cover_options sesmusic_options_buttons">
        <?php if($this->information && in_array('playButton', $this->information)): ?>
          <?php $songTitle = preg_replace('/[^a-zA-Z0-9\']/', ' ', $this->albumsong->getTitle()); ?>
          <?php $songTitle = str_replace("'", '', $songTitle); ?>
          <?php if($this->albumsong->track_id): ?>
            <?php $uploadoption = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmusic.uploadoption', 'myComputer');
            $consumer_key =  Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmusic.scclientid'); ?>          
            <?php if(($uploadoption == 'both' || $uploadoption == 'soundCloud') && $consumer_key): ?>
              <?php $track_id = $this->albumsong->track_id;
              $URL = "http://api.soundcloud.com/tracks/$track_id/stream?consumer_key=$consumer_key";  ?>
              <a class="fa fa-play" href="javascript:void(0);" onclick="play_music('<?php echo $this->albumsong->albumsong_id ?>', '<?php echo $URL; ?>', '<?php echo $songTitle; ?>');">&nbsp;&nbsp;<?php echo $this->translate("Play"); ?></a>
            <?php else: ?>
             <a class="fa fa-play" href="javascript:void(0);">&nbsp;&nbsp;<?php echo $this->translate("Play"); ?></a>
            <?php endif; ?>
          <?php else: ?>
            <a class="fa fa-play"  href="javascript:void(0);" onclick="play_music('<?php echo $this->albumsong->albumsong_id ?>', '<?php echo $this->albumsong->getFilePath(); ?>', '<?php echo $songTitle; ?>');">&nbsp;&nbsp;<?php echo $this->translate("Play"); ?></a>
          <?php endif; ?>
        <?php endif; ?>
        <?php if($this->album->owner_id == $this->viewer_id): ?>
          <?php  if ($this->canEditSong && $this->information && in_array('editButton', $this->information))
          echo $this->htmlLink($album->getHref(array('route' => 'sesmusic_albumsong_specific', 'action' => 'edit', 'albumsong_id' => $this->albumsong->albumsong_id)), $this->translate(''), array('class'=>'fa fa-pencil', 'title' => $this->translate('Edit Song'))); ?> 
          <?php if($this->canDeleteSong && $this->information && in_array('deleteButton', $this->information))
          echo $this->htmlLink(array('route' => 'sesmusic_albumsong_specific', 'module' => 'sesmusic', 'controller' => 'song', 'action' => 'delete', 'albumsong_id' => $this->albumsong->albumsong_id, 'format' => 'smoothbox'), $this->translate(''), array('class' => 'smoothbox fa fa-trash', 'title' => $this->translate('Delete Song'))); ?>
        <?php endif; ?>
        
        <?php if($this->canAddPlaylistAlbumSong && $this->viewer()->getIdentity() && $this->information && in_array('addplaylist', $this->information)): ?>
          <?php if($this->information && in_array('addplaylist', $this->information)): ?>
            <?php echo $this->htmlLink(array('route' => 'sesmusic_albumsong_specific', 'action' => 'append', 'albumsong_id' => $this->albumsong->albumsong_id), $this->translate(''), array('class' => 'smoothbox fa fa-plus', 'title' => $this->translate('Add to Playlist'))); ?>
          <?php endif; ?>
        <?php endif; ?>
        <?php if($this->albumsong->download && !$this->albumsong->track_id && !$this->albumsong->song_url && $this->downloadAlbumSong && $this->viewer()->getIdentity() && $this->information && in_array('downloadButton', $this->information)): ?>
          <?php echo $this->htmlLink(array('route' => 'sesmusic_albumsong_specific', 'action' => 'download-song', 'albumsong_id' => $this->albumsong->albumsong_id), $this->translate(""), array('class' => 'fa fa-download', 'title' => $this->translate("Download"))); ?>                             
        <?php elseif($this->albumsong->download && $this->viewer()->getIdentity() && $this->information && in_array('downloadButton', $this->information)): ?>
          <?php $file = Engine_Api::_()->getItem('storage_file', $this->albumsong->file_id); ?>
          <?php if($file->mime_minor && $this->downloadAlbumSong): ?>
          <?php $consumer_key =  Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmusic.scclientid');
          $downloadURL = 'http://api.soundcloud.com/tracks/' . $this->albumsong->track_id . '/download?client_id=' . $consumer_key;  ?>
          <a class='fa fa-download' href='<?php echo $downloadURL; ?>' target="_blank"></a>
          <?php endif; ?>
        <?php endif; ?>

        <?php if($this->viewer()->getIdentity() && $this->albumsong->lyrics && $this->information && in_array('printButton', $this->information)): ?>
          <?php echo $this->htmlLink(array('action' => 'print', 'route' => 'sesmusic_albumsong_specific', 'albumsong_id' => $this->albumsong->albumsong_id), $this->translate(''), array('class' => 'fa fa-print', 'title' => $this->translate('Print'))); ?>         
        <?php endif; ?>

        <?php if($this->viewer()->getIdentity() && $this->songlink && in_array('share', $this->songlink) && $this->information && in_array('share', $this->information)): ?>
          <?php echo $this->htmlLink(array('module'=>'activity', 'controller'=>'index', 'action'=>'share', 'route'=>'default', 'type'=>'sesmusic_albumsong', 'id' => $this->albumsong->getIdentity(), 'format' => 'smoothbox'), $this->translate(""), array('class' => 'smoothbox fa fa-share', 'title' => $this->translate("Share"))); ?>
        <?php endif; ?>
        <?php if($this->viewer()->getIdentity() && $this->songlink && in_array('report', $this->songlink) && $this->information && in_array('report', $this->information)): ?>
          <?php echo $this->htmlLink(array('module'=>'core', 'controller'=>'report', 'action'=>'create', 'route'=>'default', 'subject'=> $this->albumsong->getGuid(), 'format' => 'smoothbox'), $this->translate(""), array('class' => 'smoothbox fa fa-flag', 'title' => $this->translate("Report"))); ?>
        <?php endif; ?>
        <?php if ($this->addfavouriteAlbumSong && !empty($this->viewer_id) && $this->information && in_array('addFavouriteButton', $this->information)): ?>
          <a class="fa fa-heart sesmusic_favourite" id="sesmusic_albumsong_unfavourite_<?php echo $this->albumsong->getIdentity(); ?>" style ='display:<?php echo $this->isFavourite ? "inline-block" : "none" ?>' href = "javascript:void(0);" onclick = "sesmusicFavourite('<?php echo $this->albumsong->getIdentity(); ?>', 'sesmusic_albumsong');" title="<?php echo $this->translate('Remove from Favorites') ?>"></a>
          <a class="fa fa-heart" id="sesmusic_albumsong_favourite_<?php echo $this->albumsong->getIdentity(); ?>" style ='display:<?php echo $this->isFavourite ? "none" : "inline-block" ?>' href = "javascript:void(0);" onclick = "sesmusicFavourite('<?php echo $this->albumsong->getIdentity(); ?>', 'sesmusic_albumsong');" title="<?php echo $this->translate('Add to Favorite') ?>"></a>
          <input type="hidden" id="sesmusic_albumsong_favouritehidden_<?php echo $this->albumsong->getIdentity(); ?>" value='<?php echo $this->isFavourite ? $this->isFavourite : 0; ?>' />
        <?php endif; ?> 
      </div>
    </div>
  </div>
<?php else: ?>
  <div class="sesmusic_item_view_top">
    <div class="sesmusic_item_view_artwork">
      <?php if($this->albumsong->photo_id): ?>
        <?php echo $this->itemPhoto($this->albumsong, 'thumb.profile'); ?>
      <?php else: ?>
        <?php echo $this->itemPhoto($this->album, 'thumb.normal'); ?>
      <?php endif; ?>
      <div class="sesmusic_item_info_label">
        <span class="sesmusic_label_hot">HOT</span>
        <span class="sesmusic_label_featured">FEATURED</span>
        <span class="sesmusic_label_sponsored">SPONSORED</span>
      </div>
    </div>
    <div class="sesmusic_item_view_info">
      <div class="sesmusic_item_view_title">
        <?php echo $this->albumsong->getTitle() ?>
      </div>
      <p class="sesmusic_item_view_stats sesbasic_text_light">
        <?php if($this->information && in_array('postedBy', $this->information)): ?>
          <?php echo $this->translate('Album By %s', $this->htmlLink($album->getOwner(), $album->getOwner()->getTitle())) ?>
        <?php endif ;?>
        <?php if($this->information && in_array('creationDate', $this->information)): ?>|
          <?php echo $this->translate('Released %s', $album->creation_date); ?>
        <?php endif ;?>
      </p>
      <?php if(!empty($this->information) && in_array('category', $this->information) && $this->albumsong->category_id) :?>
        <p class="sesmusic_item_view_stats sesbasic_text_light">
          <?php $catName = $this->categoriesTable->getColumnName(array('column_name' => 'category_name', 'category_id' => $this->albumsong->category_id, 'param' => 'song')); ?>
          <a href="<?php echo $this->url(array('action' => 'browse'), 'sesmusic_songs', true).'?category_id='.urlencode($this->albumsong->category_id) ; ?>"><?php echo $catName; ?></a>
          <?php if($this->albumsong->subcat_id): ?>
          <?php $subcatName = $this->categoriesTable->getColumnName(array('column_name' => 'category_name', 'category_id' => $this->albumsong->subcat_id, 'param' => 'song')); ?>
          &nbsp;&raquo;
          <a href="<?php echo $this->url(array('action' => 'browse'), 'sesmusic_songs', true).'?subcat_id='.urlencode($this->albumsong->subcat_id) ; ?>"><?php echo $subcatName; ?></a>
          <?php endif; ?>
          <?php if($this->albumsong->subsubcat_id): ?>
          <?php $subsubcatName = $this->categoriesTable->getColumnName(array('column_name' => 'category_name', 'category_id' => $this->albumsong->subsubcat_id, 'param' => 'song')); ?>
          &nbsp;&raquo;
          <a href="<?php echo $this->url(array('action' => 'browse'), 'sesmusic_songs', true).'?subsubcat_id='.urlencode($this->albumsong->subsubcat_id) ; ?>"><?php echo $subsubcatName; ?></a>
          <?php endif; ?>
        </p>
      <?php endif; ?>
      <p class="sesmusic_item_view_stats sesbasic_text_light">
       <?php 
          $information = '';   
          if ($this->showRating && !empty($this->information) && in_array('ratingCount', $this->information))
            $information .= $this->translate(array('%s rating', '%s ratings', $this->albumsong->rating), $this->locale()->toNumber($this->albumsong->rating)) . '| ';

          if (!empty($this->information) && in_array('likeCount', $this->information))
           $information .= $this->translate(array('%s like', '%s likes', $this->albumsong->like_count), $this->locale()->toNumber($this->albumsong->like_count)) . ' | '; 

          if (!empty($this->information) && in_array('commentCount', $this->information))
            $information .= $this->translate(array('%s comment', '%s comments', $this->albumsong->comment_count), $this->locale()->toNumber($this->albumsong->comment_count)) . ' | ';

          if (!empty($this->information) && in_array('viewCount', $this->information))
           $information .= $this->translate(array('%s view', '%s views', $this->albumsong->view_count), $this->locale()->toNumber($this->albumsong->view_count)) . ' | ';

          if (!empty($this->information) && in_array('favouriteCount', $this->information))
            $information .= $this->translate(array('%s favorite', '%s favorites', $this->albumsong->favourite_count), $this->locale()->toNumber($this->albumsong->favourite_count)) . ' | ';

          if (!empty($this->information) && in_array('playCount', $this->information))
            $information .= $this->translate(array('%s play', '%s plays', $this->albumsong->play_count), $this->locale()->toNumber($this->albumsong->play_count)) . ' | ';

          $information = trim($information);
          $information = rtrim($information, '|');
        ?>
        <?php echo $information; ?>
      </p>

      <?php if($this->showRating == 1 && !empty($this->information) && in_array('ratingStars', $this->information)):  ?>
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

        <?php if($this->information && in_array('playButton', $this->information)): ?>
          <?php if($this->albumsong->track_id): ?>
            <?php $uploadoption = Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmusic.uploadoption', 'myComputer');
            $consumer_key =  Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmusic.scclientid'); ?>          
            <?php if(($uploadoption == 'both' || $uploadoption == 'soundCloud') && $consumer_key): ?>
              <?php $track_id = $this->albumsong->track_id;
              $URL = "http://api.soundcloud.com/tracks/$track_id/stream?consumer_key=$consumer_key";  ?>
              <a class="fa fa-play" href="javascript:void(0);" onclick="play_music('<?php echo $this->albumsong->albumsong_id ?>', '<?php echo $URL; ?>', '<?php echo preg_replace("/[^A-Za-z0-9\-]/", "", $this->albumsong->getTitle()); ?>');"><?php echo $this->translate("Play"); ?></a>
            <?php else: ?>
             <a class="fa fa-play" href="javascript:void(0);"><?php echo $this->translate("Play"); ?></a>
            <?php endif; ?>
          <?php else: ?>
            <a class="fa fa-play"  href="javascript:void(0);" onclick="play_music('<?php echo $this->albumsong->albumsong_id ?>', '<?php echo $this->albumsong->getFilePath(); ?>', '<?php echo $this->albumsong->getTitle(); ?>');"><?php echo $this->translate("Play"); ?></a>
          <?php endif; ?>
        <?php endif; ?>

        <?php if($this->album->owner_id == $this->viewer_id || $this->viewer->level_id == 1): ?>
          <?php  if ($this->canEditSong && $this->information && in_array('editButton', $this->information))
          echo $this->htmlLink($album->getHref(array('route' => 'sesmusic_albumsong_specific', 'action' => 'edit', 'albumsong_id' => $this->albumsong->albumsong_id)), $this->translate('Edit Song'), array('class'=>'fa fa-pencil' )); ?> 

          <?php if($this->canDeleteSong && $this->information && in_array('deleteButton', $this->information))
          echo $this->htmlLink(array('route' => 'sesmusic_albumsong_specific', 'module' => 'sesmusic', 'controller' => 'song', 'action' => 'delete', 'albumsong_id' => $this->albumsong->albumsong_id, 'format' => 'smoothbox'), $this->translate('Delete Song'), array('class' => 'smoothbox fa fa-trash')); ?>
        <?php endif; ?>

        <?php if($this->canAddPlaylistAlbumSong && $this->viewer()->getIdentity() && $this->information && in_array('addplaylist', $this->information)): ?>
          <?php if($this->information && in_array('addplaylist', $this->information)): ?>
            <?php echo $this->htmlLink(array('route' => 'sesmusic_albumsong_specific', 'action' => 'append', 'albumsong_id' => $this->albumsong->albumsong_id), $this->translate('Add to Playlist'), array('class' => 'smoothbox fa fa-plus')); ?>
          <?php endif; ?>
        <?php endif; ?>

        <?php if(!$this->albumsong->track_id && !$this->albumsong->song_url && $this->downloadAlbumSong && $this->viewer()->getIdentity() && $this->information && in_array('downloadButton', $this->information)): ?>
        <?php echo $this->htmlLink(array('route' => 'sesmusic_albumsong_specific', 'action' => 'download-song', 'albumsong_id' => $this->albumsong->albumsong_id), $this->translate("Download"), array('class' => ' fa fa-download')); ?>
        <?php else: ?>
          <?php $file = Engine_Api::_()->getItem('storage_file', $this->albumsong->file_id); ?>
          <?php if($file->mime_minor && $this->downloadAlbumSong): ?>
          <?php $consumer_key =  Engine_Api::_()->getApi('settings', 'core')->getSetting('sesmusic.scclientid');
          $downloadURL = 'http://api.soundcloud.com/tracks/' . $this->albumsong->track_id . '/download?client_id=' . $consumer_key;  ?>
          <a class='fa fa-download' href='<?php echo $downloadURL; ?>' target="_blank"><?php echo $this->translate("Download");  ?></a>
          <?php endif; ?>
        <?php endif; ?>

        <?php if($this->albumsong->lyrics && $this->information && in_array('printButton', $this->information)): ?>
          <?php echo $this->htmlLink(array('action' => 'print', 'route' => 'sesmusic_albumsong_specific', 'albumsong_id' => $this->albumsong->albumsong_id), $this->translate('Print'), array('class' => '')); ?>         
        <?php endif; ?>

        <?php if($this->songlink && in_array('share', $this->songlink) && $this->information && in_array('share', $this->information)): ?>
          <?php echo $this->htmlLink(array('module'=>'activity', 'controller'=>'index', 'action'=>'share', 'route'=>'default', 'type'=>'sesmusic_albumsong', 'id' => $this->albumsong->getIdentity(), 'format' => 'smoothbox'), $this->translate("Share"), array('class' => 'smoothbox fa fa-share')); ?>
        <?php endif; ?>

        <?php if($this->songlink && in_array('report', $this->songlink) && $this->information && in_array('report', $this->information)): ?>
          <?php echo $this->htmlLink(array('module'=>'core', 'controller'=>'report', 'action'=>'create', 'route'=>'default', 'subject'=> $this->albumsong->getGuid(), 'format' => 'smoothbox'), $this->translate("Report"), array('class' => 'smoothbox fa fa-flag')); ?>
        <?php endif; ?>

        <?php if ($this->addfavouriteAlbumSong && !empty($this->viewer_id) && $this->information && in_array('addFavouriteButton', $this->information)): ?>
          <a class="fa fa-heart sesmusic_favourite" id="sesmusic_albumsong_unfavourite_<?php echo $this->albumsong->getIdentity(); ?>" style ='display:<?php echo $this->isFavourite ? "inline-block" : "none" ?>' href = "javascript:void(0);" onclick = "sesmusicFavourite('<?php echo $this->albumsong->getIdentity(); ?>', 'sesmusic_albumsong');"><?php echo $this->translate("Remove from Favorites") ?></a>
          <a class="fa fa-heart" id="sesmusic_albumsong_favourite_<?php echo $this->albumsong->getIdentity(); ?>" style ='display:<?php echo $this->isFavourite ? "none" : "inline-block" ?>' href = "javascript:void(0);" onclick = "sesmusicFavourite('<?php echo $this->albumsong->getIdentity(); ?>', 'sesmusic_albumsong');"><?php echo $this->translate("Add to Favorite") ?></a>
          <input type="hidden" id="sesmusic_albumsong_favouritehidden_<?php echo $this->albumsong->getIdentity(); ?>" value='<?php echo $this->isFavourite ? $this->isFavourite : 0; ?>' />
        <?php endif; ?> 
      </div>
    </div>
  </div>
<?php endif; ?>
