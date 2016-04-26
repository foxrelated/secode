<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Widget.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class sitegroup_Form_Admin_Widget extends Engine_Form {

  public function init() {
    $this
            ->setTitle('General Settings')
            ->setDescription('Configure the general settings for various widgets available with this plugin.');

    // VALUE FOR FEATURE group IN SLIDESHOW
    $this->addElement('Text', 'sitegroup_feature_widgets', array(
        'label' => 'Featured Groups Slideshow
 Widget',
        'maxlength' => '3',
        'description' => 'How many groups will be shown in the featured groups slideshow widget? Note that out of all the featured groups, these many groups will be picked up randomly to be shown in the slideshow (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.feature.widgets', 10),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR SPONSORED group IN Carousel
    $this->addElement('Text', 'sitegroup_sponserdsitegroup_widgets', array(
        'label' => 'Sponsored Groups Carousel Widget',
        'maxlength' => '3',
        'description' => 'How many groups will be shown in one view of the sponsored groups carousel widget? Note that this carousel is AJAX based and users will be able to browse through all the sponsored groups (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.sponserdsitegroup.widgets', 4),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));
    // VALUE FOR Sponsored Interval
    $this->addElement('Text', 'sitegroup_sponsored_interval', array(
        'label' => 'Sponsored Carousel Speed',
        'allowEmpty' => false,
        'required' => true,
        'maxlength' => '3',
        'description' => 'What maximum Carousel Speed should be applied to the sponsored widget?',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.sponsored.interval', 300),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR TRUNCATION
    $this->addElement('Text', 'sitegroup_title_truncationsponsored', array(
        'label' => 'Title Truncation Limit For Sponsored Items Widget',
        'allowEmpty' => false,
        'maxlength' => '3',
        'required' => true,
        'description' => 'What maximum limit should be applied to the number of characters in the titles of items in the Sponsored widgets? (Enter a number between 1 and 999. Titles having more characters than this limit will be truncated. Complete titles will be shown on mouseover.)',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.title.truncationsponsored', 18),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR MOSTCOMMENT
    $this->addElement('Text', 'sitegroup_comment_widgets', array(
        'label' => 'Most Commented Groups Widget',
        'maxlength' => '3',
        'description' => 'How many groups will be shown in the most commented groups widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.comment.widgets', 3),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR MOSTLIKE
    $this->addElement('Text', 'sitegroup_likes_widgets', array(
        'label' => 'Most Liked Groups Widget',
        'maxlength' => '3',
        'description' => 'How many groups will be shown in the most liked groups widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.likes.widgets', 3),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR USER group GROUP
    $this->addElement('Text', 'sitegroup_usersitegroup_widgets', array(
        'label' => 'Group Profile Owner Groups Widget',
        'maxlength' => '3',
        'description' => 'How many groups will be shown in the group profile owner groups widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.usersitegroup.widgets', 3),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR AJAX LAYOUT
    $this->addElement('MultiCheckbox', 'sitegroup_ajax_widgets_layout', array(
        'description' => 'Choose the view types that you want to be available for groups on the groups home and browse groups.',
        'label' => 'Views on Groups Home and Browse Groups',
        'multiOptions' => array("1" => "List View", "2" => "Grid View", "3" => "Map View"),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.ajax.widgets.layout', array("0" => "1", "1" => "2", "2" => "3")),
    ));

    // VALUE FOR AJAX LAYOUT ORDER
    $this->addElement('Radio', 'sitegroup_ajax_layouts_oder', array(
        'description' => 'Select a default view type for Groups on the Groups Home Widget and Browse Groups.',
        'label' => 'Default View on Groups Home Widget and Browse Groups',
        'multiOptions' => array("1" => "List View", "2" => "Grid View", "3" => "Map View"),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.ajax.layouts.oder', 1),
    ));

    // VALUE FOR LIST SHOW IN AJAX WIDGETS
    $this->addElement('MultiCheckbox', 'sitegroup_ajax_widgets_list', array(
        'description' => 'Choose the ajax tabs that you want to be there in the Main Groups Home Widget.',
        'label' => 'Ajax Tabs of Main Groups Home Widget',
        // 'required' => true,
        'multiOptions' => array("1" => "Recent", "2" => "Most Popular", "3" => "Random", "4" => "Featured", "5" => "Sponsored"),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.ajax.widgets.list', array("0" => "1", "1" => "2", "2" => "3", "3" => "4", "4" => '5')),
    ));


    // VALUE FOR POPULAR IN SITEGROUP VIEW
    $this->addElement('Text', 'sitegroup_popular_widgets', array(
        'label' => 'Popular Groups Widget',
        'maxlength' => '3',
        'description' => 'How many groups will be shown in the popular groups widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.popular.widgets', 10),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR POPULAR IN GRID VIEW
    $this->addElement('Text', 'sitegroup_popular_thumbs', array(
        'label' => 'Popular Groups Widget Grid View',
        'maxlength' => '3',
        'description' => 'How many groups will be shown in the popular groups widget in image view (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.popular.thumbs', 15),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR RECENT IN SITEGROUP VIEW
    $this->addElement('Text', 'sitegroup_recent_widgets', array(
        'label' => 'Recent Groups Widget',
        'maxlength' => '3',
        'description' => 'How many groups will be shown in the recent groups widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.recent.widgets', 10),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR RECENT IN GRID VIEW
    $this->addElement('Text', 'sitegroup_recent_thumbs', array(
        'label' => 'Recent Groups Widget Grid View',
        'maxlength' => '3',
        'description' => 'How many groups will be shown in the recent groups widget in image view (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.recent.thumbs', 15),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR RANDOM IN SITEGROUP VIEW
    $this->addElement('Text', 'sitegroup_random_widgets', array(
        'label' => 'Random Groups Widget',
        'maxlength' => '3',
        'description' => 'How many groups will be shown in the random groups widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.random.widgets', 10),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR RANDOM IN GRID VIEW
    $this->addElement('Text', 'sitegroup_random_thumbs', array(
        'label' => 'Random Groups Widget Grid View',
        'maxlength' => '3',
        'description' => 'How many groups will be shown in the random groups widget in image view (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.random.thumbs', 15),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));


    // VALUE FOR FETURED IN SITEGROUP VIEW
    $this->addElement('Text', 'sitegroup_featured_list', array(
        'label' => 'Featured Groups Widget',
        'maxlength' => '3',
        'description' => 'How many groups will be shown in the featured groups widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.featured.list', 10),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR FETURED IN GRID VIEW
    $this->addElement('Text', 'sitegroup_featured_thumbs', array(
        'label' => 'Featured Groups  Widget Grid View',
        'maxlength' => '3',
        'description' => 'How many groups will be shown in the featured groups widget in image view (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.featured.thumbs', 15),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR SPONSORED IN SITEGROUP VIEW
    $this->addElement('Text', 'sitegroup_sponsored_list', array(
        'label' => 'Sponsored Groups Widget',
        'maxlength' => '3',
        'description' => 'How many groups will be shown in the sponsored groups widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.sponsored.list', 10),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR SPONSORED IN GRID VIEW
    $this->addElement('Text', 'sitegroup_sponosred_thumbs', array(
        'label' => 'Sponsored Groups Widget Grid View',
        'maxlength' => '3',
        'description' => 'How many groups will be shown in the sponsored groups widget in image view (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.sponosred.thumbs', 15),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));
    $this->addElement('Text', 'sitegroup_favourite_groups', array(
        'label' => 'Favourites groups Widget',
        'maxlength' => '3',
        'description' => 'How many groups will be shown in the Favourites Groups widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.favourite.groups', 3),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));


    // VALUE FOR RANDOM IN GRID VIEW
    $this->addElement('Text', 'sitegroup_suggest_sitegroups', array(
        'label' => 'You May Also Like Widget',
        'maxlength' => '3',
        'description' => 'How many groups will be shown in the you may also like widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.suggest.sitegroups', 5),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR RANDOM IN GRID VIEW
    $this->addElement('Text', 'sitegroup_recently_view', array(
        'label' => 'Recently Viewed Groups Widget',
        'maxlength' => '3',
        'description' => 'How many groups will be shown in the recently viewed groups widget in image view (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.recently.view', 3),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR RANDOM IN GRID VIEW
    $this->addElement('Text', 'sitegroup_recentlyfriend_view', array(
        'label' => 'Recently Viewed By Friends Widget',
        'maxlength' => '3',
        'description' => 'How many groups will be shown in the recently viewed by friends widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.recentlyfriend_view', 3),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));
    // VALUE FOR RANDOM IN GRID VIEW
    $this->addElement('Text', 'sitegroup_grouplike_view', array(
        'label' => 'Group Profile Likes Widget',
        'maxlength' => '3',
        'description' => 'How many users will be shown in the group profile likes widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.grouplike.view', 3),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    // VALUE FOR Discusion IN SITEGROUP VIEW
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupdiscussion')) {
      $this->addElement('Text', 'sitegroup_mostdiscussed_widgets', array(
          'label' => 'Most Discussed Groups Widget',
          'maxlength' => '3',
          'description' => 'How many groups will be shown in the most discussed Groups widget (value can not be empty or zero) ?',
          'required' => true,
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.mostdiscussed.widgets', 3),
          'validators' => array(
              array('Int', true),
              array('GreaterThan', true, array(0)),
          ),
      ));
    }

    $this->addElement('Text', 'sitegroup_popular_locations', array(
        'label' => 'Popular Locations Widget',
        'maxlength' => '3',
        'description' => 'How many locations will be shown in the popular locations widget (value can not be empty or zero) ?',
        'required' => true,
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.popular.locations', 10),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));


    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}

?>