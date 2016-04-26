<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Manage.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesvideo_Form_Admin_Manage extends Engine_Form {

  public function init() {
  
    $this->addElement('MultiCheckbox', "enableTabs", array(
        'label' => "Choose the View Type.",
        'multiOptions' => array(
            'list' => 'List View',
            'grid' => 'Grid View',
            'pinboard' => 'Pinboard View',
        ),
        'value' => 'list',
    ));
    
    $this->addElement('Select', "openViewType", array(
        'label' => " Default open View Type (apply if select more than one option in above tab)?",
        'multiOptions' => array(
            'list' => 'List View',
            'grid' => 'Grid View',
            'pinboard' => 'Pinboard View',
        ),
        'value' => 'list',
    ));
    /*$this->addElement('Select', "viewTypeStyle", array(
        'label' => "Show Data in this widget on mouse over/fixed (work in grid view only)?",
        'multiOptions' => array(
            'mouseover' => 'Yes,on mouse over',
            'fixed' => 'No,not on mouse over'
        ),
        'value' => 'fixed',
    ));*/
    $this->addElement('Radio', "showTabType", array(
        'label' => 'Show Tab Type?',
        'multiOptions' => array(
            '0' => 'Default',
            '1' => 'Custom'
        ),
        'value' => 1,
    ));
    $this->addElement('MultiCheckbox', "show_criteria", array(
        'label' => "Choose from below the details that you want to show in this widget.",
        'multiOptions' => array(
            'featuredLabel' => 'Featured Label',
            'sponsoredLabel' => 'Sponsored Label',
            'hotLabel' => 'Hot Label',
            'watchLater' => 'Watch Later Button',
            'favouriteButton' => 'Favourite Button',
            'playlistAdd' => 'Playlist Add Button',
            'likeButton' => 'Like Button',
            'socialSharing' => 'Social Sharing Button',
            'like' => 'Like Counts',
            'favourite' => 'Favourite Counts',
            'comment' => 'Comment Counts',
            'rating' => 'Rating Starts',
            'view' => 'View Counts',
						'location'=>'Location',
            'title' => 'Titles',
            'category' => 'Category',
            'by' => 'Item Owner Name',
            'duration' => 'Duration',
            'descriptionlist' => 'Description (List View)',
						'descriptiongrid' => 'Description (Grid View)',
						'descriptionpinboard' => 'Description (Pinboard View)',
						'enableCommentPinboard'=>'Enable comment on pinboard',
        ),
    ));

    $this->addElement('Radio', "pagging", array(
        'label' => "Do you want the videos to be auto-loaded when users scroll down the page?",
        'multiOptions' => array(
            'button' => 'View more',
            'auto_load' => 'Auto Load',
            'pagging' => 'Pagination'
        ),
        'value' => 'auto_load',
    ));
        $this->addElement('Text', "limit_data", array(
        'label' => 'count (number of items to show).',
        'value' => 20,
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        )
    ));
    $this->addElement('Text', "title_truncation_grid", array(
        'label' => 'Title truncation limit for Grid View.',
        'value' => 45,
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        )
    ));
    $this->addElement('Text', "title_truncation_list", array(
        'label' => 'Title truncation limit for List View.',
        'value' => 45,
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        )
    ));
		$this->addElement('Text', "title_truncation_pinboard", array(
        'label' => 'Title truncation limit for Pinboard View.',
        'value' => 45,
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        )
    ));
    $this->addElement('Text', "description_truncation_list", array(
        'label' => 'Description truncation limit for List View.',
        'value' => 45,
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        )
    ));
		 $this->addElement('Text', "description_truncation_grid", array(
        'label' => 'Description truncation limit for Grid View.',
        'value' => 45,
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        )
    ));
		 $this->addElement('Text', "description_truncation_pinboard", array(
        'label' => 'Description truncation limit for Pinboard View.',
        'value' => 45,
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        )
    ));
   $this->addElement('Text', "height_grid", array(
        'label' => 'Enter the height of one block Grid(in pixels).',
        'value' => '270',
    ));
    $this->addElement('Text', "width_grid", array(
        'label' => 'Enter the width of one block Grid(in pixels).',
        'value' => '389',
    ));
		$this->addElement('Text', "height_list", array(
        'label' => 'Enter the height of one block List(in pixels).',
        'value' => '230',
    ));
    $this->addElement('Text', "width_list", array(
        'label' => 'Enter the width of one block List(in pixels).',
        'value' => '260',
    ));
    $this->addElement('Text', "width_pinboard", array(
        'label' => 'Enter the width of one block Pinboard(in pixels).',
        'value' => '300',
    ));
    $this->addElement('MultiCheckbox', "manage_video_tabbed_option", array(
        'label' => "Default open tab ?",
        'multiOptions' => array(
            'videos' => 'My Videos',
            'likedSPvideos' => 'Liked Videos',
            'ratedSPvideos' => 'Rated Videos',
            'favouriteSPvideos' => 'Favourite Videos',
            'featuredSPvideos' => 'Featured Videos',
            'sponsoredSPvideos' => 'Sponsord Videos',
            'hotSPvideos' => 'Hot Videos',
            'watchSPlaterSPvideos' => 'Watch Later Videos',
            'mySPchannels' => 'My Channels',
            'followedSPchannels' => 'Followed Channels',
            'likedSPchannels' => 'Liked Chanels',
            'favouriteSPchannels' => 'Favourite Chanels',
            'featuredSPchannels' => 'Featured Chanels',
            'sponsoredSPchannels' => 'Sponsord Chanels',
            'hotSPchannels' => 'Hot Chanels',
            'mySPplaylists' => 'My Playlists',
            'featuredSPplaylists' => 'Featured Playlists',
            'sponsoredSPplaylists' => 'Sponsored Playlists',
        ),
    ));
    
    $limit = 1;
    // setting for my videos
    $this->addElement('Text', "videos_label", array(
    'label' => "Enter The order & text for tabs to be shown in this widget. ",
        'value' => 'Videos',
    ));
    $this->addElement('Text', "videos_order", array(
        
        'value' => $limit++,
    ));

    // setting for liked videos
    $this->addElement('Text', "likedSPvideos_label", array(
    'label' => 'Liked Videos',
        'value' => 'Liked Videos',
    ));
    $this->addElement('Text', "likedSPvideos_order", array(
        
        'value' => $limit++,
    ));

    // setting for rated videos
        $this->addElement('Text', "ratedSPvideos_label", array(
        'label' => 'Rated Videos',
        'value' => 'Rated Videos',
    ));
    $this->addElement('Text', "ratedSPvideos_order", array(
        
        'value' => $limit++,
    ));


    // setting for favourite videos
        $this->addElement('Text', "favouriteSPvideos_label", array(
        'label' => 'Favourite Videos',
        'value' => 'Favourite Videos',
    ));
    $this->addElement('Text', "favouriteSPvideos_order", array(
        
        'value' => $limit++,
    ));


    // setting for Featured videos
        $this->addElement('Text', "featuredSPvideos_label", array(
        'label' => 'Featured Videos',
        'value' => 'Featured Videos',
    ));
    $this->addElement('Text', "featuredSPvideos_order", array(
        
        'value' => $limit++,
    ));

    
    // setting for Sponsored videos
        $this->addElement('Text', "sponsoredSPvideos_label", array(
        'label' => 'Sponsored Videos',
        'value' => 'Sponsored Videos',
    ));
    $this->addElement('Text', "sponsoredSPvideos_order", array(
        
        'value' => $limit++,
    ));

    
    // setting for hot videos
        $this->addElement('Text', "hotSPvideos_label", array(
        'label' => 'Hot Videos',
        'value' => 'Hot Videos',
    ));
    $this->addElement('Text', "hotSPvideos_order", array(
        
        'value' => $limit++,
    ));


    // setting for watch later videos
        $this->addElement('Text', "watchSPlaterSPvideos_label", array(
        'label' => 'Watch Later Videos',
        'value' => 'Watch Later Videos',
    ));
    $this->addElement('Text', "watchSPlaterSPvideos_order", array(
        
        'value' => $limit++,
    ));


    // setting for my channels
        $this->addElement('Text', "mySPchannels_label", array(
        'label' => 'My Channels',
        'value' => 'My Channels',
    ));
    $this->addElement('Text', "mySPchannels_order", array(
        
        'value' => $limit++,
    ));


    // setting for follow channels
        $this->addElement('Text', "followedSPchannels_label", array(
        'label' => 'Followed Channels',
        'value' => 'Followed Channels',
    ));
    $this->addElement('Text', "followedSPchannels_order", array(
        
        'value' => $limit++,
    ));

    
    // setting for liked channels    
    $this->addElement('Text', "likedSPchannels_label", array(
    'label' => 'Liked Channels',
        'value' => 'Liked Channels',
    ));
    $this->addElement('Text', "likedSPchannels_order", array(
        
        'value' => $limit++,
    ));

    
    // setting for Favourite channels
        $this->addElement('Text', "favouriteSPchannels_label", array(
        'label' => 'Favourite Channels',
        'value' => 'Favourite Channels',
    ));
    $this->addElement('Text', "favouriteSPchannels_order", array(
        
        'value' => $limit++,
    ));


    // setting for Featured channels
        $this->addElement('Text', "featuredSPchannels_label", array(
        'label' => 'Featured Channels',
        'value' => 'Featured Channels',
    ));
    $this->addElement('Text', "featuredSPchannels_order", array(
        
        'value' => $limit++,
    ));

    
    // setting for Sponsored channels
        $this->addElement('Text', "sponsoredSPchannels_label", array(
        'label' => 'Sponsored Channels',
        'value' => 'Sponsored Channels',
    ));
    $this->addElement('Text', "sponsoredSPchannels_order", array(
        
        'value' => $limit++,
    ));


    // setting for Hot channels
        $this->addElement('Text', "hotSPchannels_label", array(
        'label' => 'Hot Channels',
        'value' => 'Hot Channels',
    ));
    $this->addElement('Text', "hotSPchannels_order", array(
        
        'value' => $limit++,
    ));


    // setting for my Playlist
        $this->addElement('Text', "mySPplaylists_label", array(
        'label' => 'My Playlists',
        'value' => 'My Playlists',
    ));
    $this->addElement('Text', "mySPplaylists_order", array(
        
        'value' => $limit++,
    ));


    // setting for featured Playlist
        $this->addElement('Text', "featuredSPplaylists_label", array(
        'label' => 'Featured Playlists',
        'value' => 'Featured Playlists',
    ));
    $this->addElement('Text', "featuredSPplaylists_order", array(
        
        'value' => $limit++,
    ));


    // setting for sponsored Playlist
        $this->addElement('Text', "sponsoredSPplaylists_label", array(
        'label' => 'Sponsored Playlists',
        'value' => 'Sponsored Playlists',
    ));
    $this->addElement('Text', "sponsoredSPplaylists_order", array(
        
        'value' => $limit++,
    ));

    $this->addDisplayGroup(array( 'videos_label', 'videos_order','likedSPvideos_label', 'likedSPvideos_order', 'ratedSPvideos_label', 'ratedSPvideos_order', 'favouriteSPvideos_label', 'favouriteSPvideos_order', 'featuredSPvideos_label', 'featuredSPvideos_order', 'sponsoredSPvideos_label', 'sponsoredSPvideos_order', 'hotSPvideos_label', 'hotSPvideos_order', 'watchSPlaterSPvideos_label', 'watchSPlaterSPvideos_order', 'mySPchannels_label', 'mySPchannels_order', 'followedSPchannels_label', 'followedSPchannels_order', 'likedSPchannels_label', 'likedSPchannels_order', 'favouriteSPchannels_label', 'favouriteSPchannels_order', 'featuredSPchannels_label', 'featuredSPchannels_order', 'sponsoredSPchannels_label', 'sponsoredSPchannels_order', 'hotSPchannels_label', 'hotSPchannels_order', 'mySPplaylists_label', 'mySPplaylists_order', 'featuredSPplaylists_label', 'featuredSPplaylists_order', 'sponsoredSPplaylists_label', 'sponsoredSPplaylists_order'), 'likevideos', array('disableLoadDefaultDecorators' => true));
    $likevideos = $this->getDisplayGroup('likevideos');
    $likevideos->setDecorators(array('FormElements', 'Fieldset', array('HtmlTag', array('tag' => 'div', 'id' => 'likevideos'))));
  }

}
