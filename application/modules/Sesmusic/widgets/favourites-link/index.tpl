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
<div class="quicklinks">
  <ul class="navigation">
    <?php if(!empty($this->information) && in_array('favAlbums', $this->information)): ?>
      <li <?php if($this->action == 'favourite-albums'): ?> class="active" <?php endif; ?>>
        <?php echo $this->htmlLink(array('route' => 'sesmusic_album_default', 'action' => 'favourite-albums'), $this->translate('Favorite Music Albums'), array('class' => 'buttonlink sesmusic_icon_fav_album')); ?>  
      </li>
    <?php endif; ?>
    <?php if(!empty($this->information) && in_array('likedAlbums', $this->information)): ?>
    <li <?php if($this->action == 'like-albums'): ?> class="active" <?php endif; ?>>
      <?php echo $this->htmlLink(array('route' => 'sesmusic_album_default', 'action' => 'like-albums'), $this->translate('Liked Music Albums'), array('class' => 'buttonlink sesmusic_icon_like_album')); ?>  
    </li>
        <?php endif; ?>
    <?php if(!empty($this->information) && in_array('ratedAlbums', $this->information)): ?>
    <li <?php if($this->action == 'rated-albums'): ?> class="active" <?php endif; ?>>
      <?php echo $this->htmlLink(array('route' => 'sesmusic_album_default', 'action' => 'rated-albums'), $this->translate('Rated Music Albums'), array('class' => 'buttonlink sesmusic_icon_rate_album')); ?>  
    </li>
        <?php endif; ?>
    <?php if(!empty($this->information) && in_array('favSongs', $this->information)): ?>
    <li <?php if($this->action == 'favourite-songs'): ?> class="active" <?php endif; ?>>
      <?php echo $this->htmlLink(array('route' => 'sesmusic_albumsong_default', 'action' => 'favourite-songs'), $this->translate('Favorite Songs'), array('class' => 'buttonlink sesmusic_icon_fav_song')); ?>  
    </li>
        <?php endif; ?>
    <?php if(!empty($this->information) && in_array('likedSongs', $this->information)): ?>
    <li <?php if($this->action == 'like-songs'): ?> class="active" <?php endif; ?>>
      <?php echo $this->htmlLink(array('route' => 'sesmusic_albumsong_default', 'action' => 'like-songs'), $this->translate('Liked Songs'), array('class' => 'buttonlink sesmusic_icon_like_song')); ?>  
    </li>
        <?php endif; ?>
    <?php if(!empty($this->information) && in_array('ratedSongs', $this->information)): ?>
    <li <?php if($this->action == 'rated-songs'): ?> class="active" <?php endif; ?>>
      <?php echo $this->htmlLink(array('route' => 'sesmusic_albumsong_default', 'action' => 'rated-songs'), $this->translate('Rated Songs'), array('class' => 'buttonlink sesmusic_icon_rate_song')); ?>  
    </li>
        <?php endif; ?>
    <?php if(!empty($this->information) && in_array('playlists', $this->information)): ?>
    <li <?php if($this->action == 'playlist/manage'): ?> class="active" <?php endif; ?>>
      <?php echo $this->htmlLink(array('route' => 'sesmusic_playlists', 'action' => 'manage'), $this->translate('Playlists'), array('class' => 'buttonlink sesmusic_icon_playlists')); ?>
    </li>
        <?php endif; ?>
    <?php if(!empty($this->information) && in_array('favArtists', $this->information)): ?>
    <li <?php if($this->action == 'favourite-artists'): ?> class="active" <?php endif; ?>>
      <?php echo $this->htmlLink(array('route' => 'sesmusic_artists', 'action' => 'favourite-artists'), $this->translate('Favorite Artists'), array('class' => 'buttonlink sesmusic_icon_fav_artist')); ?>
    </li>
        <?php endif; ?>
  </ul>
</div>