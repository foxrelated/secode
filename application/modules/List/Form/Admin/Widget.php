<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Widget.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class list_Form_Admin_Widget extends Engine_Form {

  public function init() {

    $this
        ->setTitle('General Settings')
        ->setDescription('Configure the general settings for the various widgets available with this plugin.');

		$settings = Engine_Api::_()->getApi('settings', 'core');

    $this->addElement('Text', 'list_feature_widgets', array(
            'label' => 'Featured Listings Slideshow Widget',
            'maxlength' => '3',
            'description' => 'How many listings will be shown in featured listings slideshow widget? Note that from all the featured listings, these many listings will be picked up randomly to be shown in the slideshow. (value can not be empty or zero).',
            'required' => true,
            'value' => $settings->getSetting('list.feature.widgets', 10),
            'validators' => array(
                    array('Int', true),
                    array('GreaterThan', true, array(0)),
            ),
    ));

    $this->addElement('Text', 'list_sponserdlist_widgets', array(
            'label' => 'Sponsored Listings Carousel Widget',
            'maxlength' => '3',
            'description' => 'How many listings will be shown in one view of the sponsored listings carousel widget? Note that this carousel is AJAX based and users will be able to browse through all the sponsored listings. (value can not be empty or zero).',
            'required' => true,
            'value' => $settings->getSetting('list.sponserdlist.widgets', 4),
            'validators' => array(
                    array('Int', true),
                    array('GreaterThan', true, array(0)),
            ),
    ));

    $this->addElement('Text', 'list_sponsored_interval', array(
            'label' => 'Sponsored Curosal Speed',
            'maxlength' => '3',
            'allowEmpty' => false,
            'required' => true,
            'description' => 'What maximum Curosal Speed should be applied to the Sponsored Widget.',
            'value' => $settings->getSetting('list.sponsored.interval', 300),
            'validators' => array(
                    array('Int', true),
                    array('GreaterThan', true, array(0)),
            ),
    ));

    $this->addElement('Text', 'list_rate_widgets', array(
            'label' => 'Top Rated Listings Widget',
            'maxlength' => '3',
            'description' => 'How many listings will be shown in the top rated listings widget (value can not be empty or zero) ?',
            'required' => true,
            'value' => $settings->getSetting('list.rate.widgets', 3),
            'validators' => array(
                    array('Int', true),
                    array('GreaterThan', true, array(0)),
            ),
    ));

    $this->addElement('Text', 'list_comment_widgets', array(
            'label' => 'Most Commented Listings Widget',
            'maxlength' => '3',
            'description' => 'How many listings will be shown in the most commented listings widget (value can not be empty or zero) ?',
            'required' => true,
            'value' => $settings->getSetting('list.comment.widgets', 3),
            'validators' => array(
                    array('Int', true),
                    array('GreaterThan', true, array(0)),
            ),
    ));

    $this->addElement('Text', 'list_likes_widgets', array(
            'label' => 'Most Liked Listings Widget',
            'maxlength' => '3',
            'description' => 'How many listings will be shown in the most liked listings widget (value can not be empty or zero) ?',
            'required' => true,
            'value' => $settings->getSetting('list.likes.widgets', 3),
            'validators' => array(
                    array('Int', true),
                    array('GreaterThan', true, array(0)),
            ),
    ));

    $this->addElement('Text', 'list_userlist_widgets', array(
            'label' => 'Owner Listings Widget',
            'maxlength' => '3',
            'description' => 'How many listings will be shown in the user listings  widget (value can not be empty or zero) ?',
            'required' => true,
            'value' => $settings->getSetting('list.userlist.widgets', 3),
            'validators' => array(
                    array('Int', true),
                    array('GreaterThan', true, array(0)),
            ),
    ));

    $this->addElement('MultiCheckbox', 'list_ajax_widgets_layout', array(
            'description' => "Choose the view types that you want to be available for listings on the listings home and browse pages.",
            'label' => 'Views on Listings Home and Browse pages',
            'multiOptions' => array("1" => "List View", "2" => "Grid View", "3" => "Map View"),
            'value' => $settings->getSetting('list.ajax.widgets.layout', array("0" => "1", "1" => "2", "2" => "3")),
    ));

    $this->addElement('Radio', 'list_ajax_layouts_oder', array(
            'description' => 'Select a default view type for Listings on the Main Listings Home Widget and Browse Listings.',
            'label' => 'Default View on Listings Home Widget and Browse Listings',
            'multiOptions' => array("1" => "List View", "2" => "Grid View", "3" => "Map View"),
            'value' => $settings->getSetting('list.ajax.layouts.oder', 1),
    ));

    $this->addElement('MultiCheckbox', 'list_ajax_widgets_list', array(
            'description' => "Choose the ajax tabs that you want to be there in the Main Listings Home Widget.",
            'label' => 'Ajax Tabs of Main Listings Home Widget',
            'multiOptions' => array("1" => "Recent", "2" => "Most Popular", "3" => "Random", "4" => "Featured", "5" => "Sponsored"),
            'value' => $settings->getSetting('list.ajax.widgets.list', array("0" => "1", "1" => "2", "2" => "3", "3" => "4", "4" => "5")),
    ));

    $this->addElement('Text', 'list_popular_widgets', array(
            'label' => 'Popular Listings Widget',
            'maxlength' => '3',
            'description' => 'How many listings will be shown in the popular listings widget (value can not be empty or zero) ?',
            'required' => true,
            'value' => $settings->getSetting('list.popular.widgets', 10),
            'validators' => array(
                    array('Int', true),
                    array('GreaterThan', true, array(0)),
            ),
    ));

    $this->addElement('Text', 'list_popular_thumbs', array(
            'label' => 'Popular Listings Widget Grid View',
            'maxlength' => '3',
            'description' => 'How many listings will be shown in the popular listings widget in image view (value can not be empty or zero) ?',
            'required' => true,
            'value' => $settings->getSetting('list.popular.thumbs', 15),
            'validators' => array(
                    array('Int', true),
                    array('GreaterThan', true, array(0)),
            ),
    ));

    $this->addElement('Text', 'list_recent_widgets', array(
            'label' => 'Recent Listings Widget',
            'maxlength' => '3',
            'description' => 'How many listings will be shown in the recent listings widget (value can not be empty or zero) ?',
            'required' => true,
            'value' => $settings->getSetting('list.recent.widgets', 10),
            'validators' => array(
                    array('Int', true),
                    array('GreaterThan', true, array(0)),
            ),
    ));

    $this->addElement('Text', 'list_recent_thumbs', array(
            'label' => 'Recent Listings Widget Grid View',
            'maxlength' => '3',
            'description' => 'How many listings will be shown in the recent listings widget in image view (value can not be empty or zero) ?',
            'required' => true,
            'value' => $settings->getSetting('list.recent.thumbs', 15),
            'validators' => array(
                    array('Int', true),
                    array('GreaterThan', true, array(0)),
            ),
    ));

    $this->addElement('Text', 'list_random_widgets', array(
            'label' => 'Random Listings Widget',
            'maxlength' => '3',
            'description' => 'How many listings will be shown in the random listings  widget (value can not be empty or zero) ?',
            'required' => true,
            'value' => $settings->getSetting('list.random.widgets', 10),
            'validators' => array(
                    array('Int', true),
                    array('GreaterThan', true, array(0)),
            ),
    ));

    $this->addElement('Text', 'list_random_thumbs', array(
            'label' => 'Random Listings Widget Grid View',
            'maxlength' => '3',
            'description' => 'How many listings will be shown in the random listings  widget in image view (value can not be empty or zero) ?',
            'required' => true,
            'value' => $settings->getSetting('list.random.thumbs', 15),
            'validators' => array(
                    array('Int', true),
                    array('GreaterThan', true, array(0)),
            ),
    ));

    $this->addElement('Text', 'list_featured_list', array(
            'label' => 'Featured Listings Widget',
            'maxlength' => '3',
            'description' => 'How many listings will be shown in the featured listings widget (value can not be empty or zero) ?',
            'required' => true,
            'value' => $settings->getSetting('list.featured.list', 10),
            'validators' => array(
                    array('Int', true),
                    array('GreaterThan', true, array(0)),
            ),
    ));

    $this->addElement('Text', 'list_featured_thumbs', array(
            'label' => 'Featured Listings Widget Grid View',
            'maxlength' => '3',
            'description' => 'How many listings will be shown in the featured listings widget in image view (value can not be empty or zero) ?',
            'required' => true,
            'value' => $settings->getSetting('list.featured.thumbs', 15),
            'validators' => array(
                    array('Int', true),
                    array('GreaterThan', true, array(0)),
            ),
    ));

    $this->addElement('Text', 'list_sponsored_list', array(
            'label' => 'Sponsored Pages Widget',
            'maxlength' => '3',
            'description' => 'How many listings will be shown in the sponsored listings widget (value can not be empty or zero) ?',
            'required' => true,
            'value' => $settings->getSetting('list.sponsored.list', 10),
            'validators' => array(
                    array('Int', true),
                    array('GreaterThan', true, array(0)),
            ),
    ));

    $this->addElement('Text', 'list_sponosred_thumbs', array(
            'label' => 'Sponsored Pages Widget Grid View',
            'maxlength' => '3',
            'description' => 'How many listings will be shown in the sponsored listings widget in image view (value can not be empty or zero) ?',
            'required' => true,
            'value' => $settings->getSetting('list.sponosred.thumbs', 15),
            'validators' => array(
                    array('Int', true),
                    array('GreaterThan', true, array(0)),
            ),
    ));

    $this->addElement('Text', 'list_suggest_lists', array(
            'label' => 'Suggest Listings Widget',
            'maxlength' => '3',
            'description' => 'How many Listings will be shown in the suggest listings  widget (value can not be empty or zero) ?',
            'required' => true,
            'value' => $settings->getSetting('list.suggest.lists', 5),
            'validators' => array(
                    array('Int', true),
                    array('GreaterThan', true, array(0)),
            ),
    ));

    $this->addElement('Text', 'list_recently_view', array(
            'label' => 'Recently Viewed Listings Widget',
            'maxlength' => '3',
            'description' => 'How many listings will be shown in the recently view listings  widget in image view (value can not be empty or zero) ?',
            'required' => true,
            'value' => $settings->getSetting('list.recently.view', 3),
            'validators' => array(
                    array('Int', true),
                    array('GreaterThan', true, array(0)),
            ),
    ));

    $this->addElement('Text', 'list_recentlyfriend_view', array(
            'label' => 'Recently Friend View Listings Widget',
            'maxlength' => '3',
            'description' => 'How many listings will be shown in the recently friend view listings  widget in image view (value can not be empty or zero) ?',
            'required' => true,
            'value' => $settings->getSetting('list.recentlyfriend_view', 3),
            'validators' => array(
                    array('Int', true),
                    array('GreaterThan', true, array(0)),
            ),
    ));

    $this->addElement('Text', 'list_listinglike_view', array(
            'label' => 'Listing Likes',
            'maxlength' => '3',
            'description' => 'How many listings will be shown in the  view listings  widget in image view (value can not be empty or zero) ?',
            'required' => true,
            'value' => $settings->getSetting('list.listinglike.view', 3),
            'validators' => array(
                    array('Int', true),
                    array('GreaterThan', true, array(0)),
            ),
    ));

    $this->addElement('Text', 'list_mostdiscussed_widgets', array(
            'label' => 'Most Discussed Listings Widget',
            'maxlength' => '3',
            'description' => 'How many listings will be shown in the most discussed listings widget (value can not be empty or zero) ?',
            'required' => true,
            'value' => $settings->getSetting('list.mostdiscussed.widgets', 10),
            'validators' => array(
                    array('Int', true),
                    array('GreaterThan', true, array(0)),
            ),
    ));

    $this->addElement('Text', 'list_popular_locations', array(
            'label' => 'Popular Locations Widget',
            'maxlength' => '3',
            'description' => 'How many locations will be shown in the popular locations widget (value can not be empty or zero) ?',
            'required' => true,
            'value' => $settings->getSetting('list.popular.locations', 10),
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