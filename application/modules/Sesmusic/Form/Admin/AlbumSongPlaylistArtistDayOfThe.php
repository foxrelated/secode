<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: AlbumSongPlaylistArtistDayOfThe.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmusic_Form_Admin_AlbumSongPlaylistArtistDayOfThe extends Engine_Form {

  public function init() {

    $this->setTitle('Album / Song / Artist of the Day')
            ->setDescription('Displays the Album / Song / Artist of The day as selected by the admin from the edit setting of this widget.');

    $this->addElement('Select', 'contentType', array(
        'label' => 'Choose content type to be shown in this widget.',
        'multiOptions' => array(
            'album' => 'Music Album',
            'albumsong' => 'Album Song',
            'artist' => 'Artist',
            'playlist' => 'Playlist',
        ),
        'value' => 'album',
    ));

    $this->addElement('MultiCheckbox', 'information', array(
        'label' => 'Choose the options that you want to be displayed in this widget.',
        'multiOptions' => array(
            "featured" => "Featured Label",
            "sponsored" => "Sponsored Label",
            "hot" => "Hot Label",
            "likeCount" => "Likes Count [This will only show for 'Music Album' and 'Songs' content types]",
            "commentCount" => "Comments Count [This will only show for 'Music Album' and 'Songs' content types]",
            "viewCount" => "Views Count",
            "ratingCount" => "Rating Stars",
            "title" => "Content Title",
            "postedby" => "Content Owner's Name",
            "songsCount" => "Songs Count [This will only show for 'Music Album' content type]",
            "favouriteCount" => "Favorite Count",
            "downloadCount" => "Downloaded Count [This will only show for 'Album Song' content type]",
            "songsListShow" => "Show songs of each playlist"
        ),
        'value' => array("featured", "sponsored", "hot", "viewCount", "likeCount", "commentCount", "ratingCount", "songsCount", "title", "postedby", "favouriteCount"),
    ));
  }

}