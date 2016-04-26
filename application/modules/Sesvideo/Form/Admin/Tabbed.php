<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Tabbed.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesvideo_Form_Admin_Tabbed extends Engine_Form {

  public function init() {
  
    $this->addElement('MultiCheckbox', "enableTabs", array(
        'label' => "Choose the View Type.",
        'multiOptions' => array(
            'list' => 'List View',
            'grid' => 'Grid View',
            'pinboard' => 'Pinboard View',
        ),
    ));
    $this->addElement('Select', "openViewType", array(
        'label' => " Default open View Type (apply if select more than one option in above tab)?",
        'multiOptions' => array(
            'list' => 'List View',
            'grid' => 'Grid View',
            'pinboard' => 'Pinboard View',
        ),
    ));
		$this->addElement('Select', "viewTypeStyle", array(
        'label' => "Show Data in this widget on mouse over/fixed (work in grid view only)?",
        'multiOptions' => array(
            'mouseover' => 'Yes,on mouse over',
            'fixed' => 'No,not on mouse over'
        ),
        'value' => 'fixed',
    ));
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
						'location'=>'Location',
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
        'label' => "Do you want the videos to be auto-loaded when users scroll down the page?",
        'multiOptions' => array(
            'button' => 'View more',
            'auto_load' => 'Auto Load',
            'pagging' => 'Pagination'
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
		
		$this->addElement('Text', "limit_data_pinboard", array(
        'label' => 'Pinboard count (number of videos to show).',
        'value' => 10,
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        )
    ));
		$this->addElement('Text', "limit_data_list", array(
        'label' => 'List count (number of videos to show).',
        'value' => 10,
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        )
    ));
		$this->addElement('Text', "limit_data_grid", array(
        'label' => 'Grid count (number of videos to show).',
        'value' => 10,
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        )
    ));
		$this->addElement('Select', "show_limited_data", array(
			'label' => 'Show only the number of videos entered in above setting. [If you choose No, then you can choose how do you want to show more videos in this widget in below setting.]',
			'multiOptions' => array(
            'yes' => 'Yes',
            'no' => 'No',
        ),
        'value' => 'no',
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
        'label' => "Choose from below the details that you want to show in this widget.",
        'multiOptions' => array(
            'recentlySPcreated' => 'Recently Created',
            'mostSPviewed' => 'Most Viewed',
            'mostSPliked' => 'Most Liked',
            'mostSPcommented' => 'Most Commented',
            'mostSPrated' => 'Most Rated',
            'mostSPfavourite' => 'Most Favourite',
            'hot' => 'Hot',
            'featured' => 'Featured',
            'sponsored' => 'Sponsored'
        ),
    ));
    // setting for Recently Updated
    $this->addElement('Text', "recentlySPcreated_order", array(
        'label' => "Enter The order & text for tabs to be shown in this widget. ",
        'value' => '1',
    ));
    $this->addElement('Text', "recentlySPcreated_label", array(
        'value' => 'Recently Created',
    ));
    // setting for Most Viewed
    $this->addElement('Text', "mostSPviewed_order", array(
        'label' => 'Most Viewed',
        'value' => '2',
    ));
    $this->addElement('Text', "mostSPviewed_label", array(
        'value' => 'Most Viewed',
    ));
    // setting for Most Liked
    $this->addElement('Text', "mostSPliked_order", array(
        'label' => 'Most Liked',
        'value' => '3',
    ));
    $this->addElement('Text', "mostSPliked_label", array(
        'value' => 'Most Liked',
    ));
    // setting for Most Commented
    $this->addElement('Text', "mostSPcommented_order", array(
        'label' => 'Most Commented',
        'value' => '4',
    ));
    $this->addElement('Text', "mostSPcommented_label", array(
        'value' => 'Most Commented',
    ));
    // setting for Most Rated
    $this->addElement('Text', "mostSPrated_order", array(
        'label' => 'Most Rated',
        'value' => '5',
    ));
    $this->addElement('Text', "mostSPrated_label", array(
        'value' => 'Most Rated',
    ));

    // setting for Most Favourite
    $this->addElement('Text', "mostSPfavourite_order", array(
        'label' => 'Most Favourite',
        'value' => '6',
    ));
    $this->addElement('Text', "mostSPfavourite_label", array(
        'value' => 'Most Favourite',
    ));
    // setting for Most Hot
    $this->addElement('Text', "hot_order", array(
        'label' => 'Most Hot',
        'value' => '7',
    ));
    $this->addElement('Text', "hot_label", array(
        'value' => 'Most Hot',
    ));

    // setting for Featured
    $this->addElement('Text', "featured_order", array(
        'label' => 'Featured',
        'value' => '6',
    ));
    $this->addElement('Text', "featured_label", array(
        'value' => 'Featured',
    ));
    // setting for Sponsored
    $this->addElement('Text', "sponsored_order", array(
        'label' => 'Sponsored',
        'value' => '7',
    ));
    $this->addElement('Text', "sponsored_label", array(
        'value' => 'Sponsored',
    ));
  }

}