<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Profilevideos.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesvideo_Form_Admin_Profilevideos extends Engine_Form {

  public function init() {

    $this->addElement('MultiCheckbox', "enableTabs", array(
        'label' => "Choose the View Type for videos.",
        'multiOptions' => array(
            'list' => 'List View',
            'grid' => 'Grid View',
            'pinboard' => 'Pinboard View'
        ),
        'value' => 'list',
    ));
		
    $this->addElement('Select', "viewTypeStyle", array(
        'label' => "Show Data in this widget on mouse over/fixed (work in grid view only)?",
        'multiOptions' => array(
            'mouseover' => 'Yes,on mouse over',
            'fixed' => 'No,not on mouse over'
        ),
        'value' => 'fixed',
    ));
    $this->addElement('Select', "openViewType", array(
        'label' => 'Default open View Type (apply if select Both View option in above tab)?',
        'multiOptions' => array(
            'list' => 'List View',
            'grid' => 'Grid View',
            'pinboard' => 'Pinboard View',
        ),
        'value' => 'list',
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
        'label' => "Do you want the photos / albums to be auto-loaded when users scroll down the page?",
        'multiOptions' => array(
            'button' => 'View more',
            'auto_load' => 'Yes, Auto Load.',
            'pagging' => 'No, show \'Pagination\'.'
        ),
        'value' => 'auto_load',
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

    $this->addElement('MultiCheckbox', "search_type", array(
        'label' => "Choose from below the Tabs that you want to show in this widget.",
        'multiOptions' => array(
            'mySPvideo' => 'My Videos',
            'mySPchanel' => 'My Chanels',
            'mySPplaylist' => 'My Playlist',
        ),
    ));
    $this->addElement('Text', "limit_data", array(
        'label' => 'count (number of videos / chanels / playlists to show).',
        'value' => 20,
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        )
    ));
    $this->addElement('Dummy', "dummy", array(
        'label' => "Enter the order of the Tabs to be shown in this widget. ",
    ));
    $this->addElement('Text', "mySPvideo_order", array(
        'label' => "Profile Owner\'s Videos",
        'value' => '1',
    ));

    /* $this->addElement('Text', "mySPvideo_label", array(
      'value' => 'Videos',
      )); */

    // setting for Most Viewed
    $this->addElement('Text', "mySPchanel_order", array(
        'label' => 'Profile Owner\'s Chanel',
        'value' => '2',
    ));
    /* 	$this->addElement('Text', "mySPchanel_label", array(
      'value' => 'Chanels',
      )); */

    // setting for Most Liked
    $this->addElement('Text', "mySPplaylist_order", array(
        'label' => 'Profile Owner\'s Playlist',
        'value' => '3',
    ));
    /* 	$this->addElement('Text', "mySPplaylist_label", array(
      'value' => 'Playlists',
      )); */
  }

}
