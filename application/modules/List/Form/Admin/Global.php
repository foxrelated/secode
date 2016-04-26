<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Global.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_Form_Admin_Global extends Engine_Form {

  public function init() {

    $this
        ->setTitle('Global Settings')
        ->setDescription('These settings affect all members in your community.');

		$settings = Engine_Api::_()->getApi('settings', 'core');

    $this->addElement('Text', 'list_lsettings', array(
            'label' => 'Enter License key',
            'description' => "Please enter your license key that was provided to you when you purchased this plugin. If you do not know your license key, please contact the Support Team of SocialEngineAddOns from the Support section of your Account Area.(Key Format: XXXXXX-XXXXXX-XXXXXX )",
            'value' => $settings->getSetting('list.lsettings'),
    ));

    if( APPLICATION_ENV == 'production' ) {
      $this->addElement('Checkbox', 'environment_mode', array(
              'label' => 'Your community is currently in "Production Mode". We recommend that you momentarily switch your site to "Development Mode" so that the CSS of this plugin renders fine as soon as the plugin is installed. After completely installing this plugin and visiting few pages of your site, you may again change the System Mode back to "Production Mode" from the Admin Panel Home. (In Production Mode, caching prevents CSS of new plugins to be rendered immediately after installation.)',
              'description' => 'System Mode',
              'value' => 1,
      ));
    } else {
      $this->addElement('Hidden', 'environment_mode', array('order' => 990, 'value' => 0));
    }

    $this->addElement('Button', 'submit_lsetting', array(
            'label' => 'Activate Your Plugin Now',
            'type' => 'submit',
            'ignore' => true
    ));

    $this->addElement('Radio', 'list_locationfield', array(
        'label' => 'Location Field',
        'description' => 'Do you want the Location field to be enabled for listings?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
				'onclick' => 'showlocationOption(this.value)',
        'value' => $settings->getSetting('list.locationfield', 1),
    ));

    $this->addElement('Radio', 'list_location', array(
            'label' => 'Maps Integration',
            'description' => 'Do you want Maps Integration to be enabled for listing items? (With this enabled, items having location information could also be seen on Map. Pages like the Listing Home and Browse Listing also enable you to see the items plotted on Map.)',
            'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
            ),
            'onclick' => 'showMapOptions(this.value)',
            'value' => $settings->getSetting('list.location', 1),
    ));

    $this->addElement('Radio', 'list_map_sponsored', array(
            'label' => 'Sponsored Listings with a Bouncing Animation',
            'description' => 'Do you want the sponsored listings to be shown with a bouncing animation in the Map?',
            'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
            ),
            'value' => $settings->getSetting('list.map.sponsored', 1),
    ));

    $this->addElement('Text', 'list_map_city', array(
            'label' => 'Centre Location for Map at Listings Home  and Browse Listings',
            'description' => 'Enter the location which you want to be shown at centre of the map which is shown on Listings Home  and Browse Listings when Map View is chosen to view Listings.(To show the whole world on the map, enter the word "World" below.)',
            'required' => true,
            'value' => $settings->getSetting('list.map.city', "World"),
    ));

    $this->addElement('Select', 'list_map_zoom', array(
            'label' => "Default Zoom Level for Map at Listings Home  and Browse Listings",
            'description' => 'Select the default zoom level for the map which is shown on Listings Home  and Browse Listings when Map View is chosen to view Listings. (Note that as higher zoom level you will select, the more number of surrounding cities/locations you will be able to see.)',
            'multiOptions' => array(
                    '1' => "1",
                    "2" => "2",
                    "4" => "4",
                    "6" => "6",
                    "8" => "8",
                    "10" => "10",
                    "12" => "12",
                    "14" => "14",
                    "16" => "16"
            ),
            'value' => $settings->getSetting('list.map.zoom', 1),
            'disableTranslator' => 'true'
    ));

    $this->addElement('Radio', 'list_report', array(
            'label' => 'Report as Inappropriate',
            'description' => 'Do you want to allow logged-in members to be able to report listings as inappropriate? (Members will also be able to mention the reason why they find the listing inappropriate.)',
            'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
            ),
            'value' => $settings->getSetting('list.report', 1),
    ));

    $this->addElement('Radio', 'list_share', array(
            'label' => 'Community Sharing',
            'description' => 'Do you want to allow members to share listings within your community?',
            'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
            ),
            'value' => $settings->getSetting('list.share', 1),
    ));

    $this->addElement('Radio', 'list_socialshare', array(
            'label' => 'Social Sharing',
            'description' => 'Do you want social sharing to be enabled for the listings? (Social sharing buttons will be shown on the main listing pages.)',
            'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
            ),
            'value' => $settings->getSetting('list.socialshare', 1),
    ));

    $this->addElement('Radio', 'list_rating', array(
            'label' => 'Rating',
            'description' => 'Do you want logged-in members to be able to rate listings?',
            'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
            ),
            'value' => $settings->getSetting('list.rating', 1),
    ));

    $this->addElement('Radio', 'list_printer', array(
            'label' => 'Printing',
            'description' => 'Do you want users to be able to print listing information?',
            'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
            ),
            'value' => $settings->getSetting('list.printer', 1),
    ));

    $this->addElement('Radio', 'list_tellafriend', array(
            'label' => 'Tell a friend',
            'description' => 'Do you want to show the "Tell a friend" link on the main listing pages? (Using this feature, users will be able to email a listing to their friends )',
            'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
            ),
            'value' => $settings->getSetting('list.tellafriend', 1),
    ));

    $this->addElement('Radio', 'list_captcha_post', array(
            'label' => 'CAPTCHA For Tell a friend',
            'description' => 'Do you want visitors to enter validation code in Tell a friend form?',
            'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
            ),
            'value' => $settings->getSetting('list.captcha.post', 1),
    ));

    $this->addElement('Radio', 'list_description_allow', array(
        'label' => 'Allow Description',
        'description' =>  'Do you want to allow listing owners to write description for their listings?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $settings->getSetting('list.description.allow', 1),
        'onclick' => 'showDescription(this.value)'
    ));

    $this->addElement('Radio', 'list_requried_description', array(
            'label' => 'Description Required',
            'description' => 'Do you want to make Description a mandatory field for listings?',
            'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
            ),
            'value' => $settings->getSetting('list.requried.description', 1),
    ));

    $this->addElement('Radio', 'list_status_show', array(
            'label' => 'Open / Closed status in Search',
            'description' => 'Do you want the Status field (Open / Closed) in the search form widget? (This widget appears on the listings browse and home pages, and enables users to search and filter listings.)',
            'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
            ),
            'value' => $settings->getSetting('list.status.show', 1),
    ));

    $this->addElement('Radio', 'list_network', array(
            'label' => 'Browse by Networks',
            'description' => "Do you want to show listings according to viewer's network if he has selected any? (If set to no, all the listings will be shown.)",
            'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
            ),
            'onclick' => 'showDefaultNetwork(this.value)',
            'value' => $settings->getSetting('list.network', 0),
    ));

    $this->addElement('Radio', 'list_default_show', array(
            'label' => 'Set Only My Networks as Default in search',
            'description' => 'Do you want to set "Only My Networks" option as default for Show field in the search form widget? (This widget appears on the listings browse and home pages, and enables users to search and filter listings.)',
            'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
            ),
            'value' => $settings->getSetting('list.default.show', 0),
    ));

    $this->addElement('Radio', 'list_proximitysearch', array(
            'label' => 'Proximity Search',
            'description' => 'Do you want proximity search to be enabled for listings? (Proximity search will enable users to search for listings within a certain distance from a location. Proximity search will work only if you have created a custom Location type field in Listing Questions.)',
            'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
            ),
            'onclick' => 'showlocationKM(this.value)',
            'value' => $settings->getSetting('list.proximitysearch', 1),
    ));

    $this->addElement('Radio', 'list_proximity_search_kilometer', array(
            'label' => 'Proximity Search Metric',
            'description' => 'What metric do you want to be used for proximity search?',
            'multiOptions' => array(
                    0 => 'Miles',
                    1 => 'Kilometers'
            ),
            'value' => $settings->getSetting('list.proximity.search.kilometer', 0),
    ));

    $this->addElement('Radio', 'list_checkcomment_widgets', array(
            'label' => 'Comments',
            'description' => 'Do you want comments to be enabled for listings on the Info pages?',
            'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
            ),
            'value' => $settings->getSetting('list.checkcomment.widgets', 1),
    ));

    $this->addElement('Radio', 'list_sponsored_image', array(
            'label' => 'Sponsored Label',
            'description' => 'Do you want to show the "SPONSORED" label on the main pages of sponsored listings above the listing profile picture ?',
            'onclick' => 'showsponsored(this.value)',
            'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
            ),
            'value' => $settings->getSetting('list.sponsored.image', 1),
    ));

    $this->addElement('Text', 'list_sponsored_color', array(
            'decorators' => array(array('ViewScript', array(
                                    'viewScript' => '_formImagerainbowSponsred.tpl',
                                    'class' => 'form element'
                    )))
    ));

    $this->addElement('Radio', 'list_feature_image', array(
            'label' => 'Featured Label',
            'description' => 'Do you want to show the "FEATURED" label on the main pages of featured listings below the listing profile picture ?',
            'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
            ),
            'onclick' => 'showfeatured(this.value)',
            'value' => $settings->getSetting('list.feature.image', 1),
    ));

    $this->addElement('Text', 'list_featured_color', array(
            'decorators' => array(array('ViewScript', array(
                                    'viewScript' => '_formImagerainbowFeatured.tpl',
                                    'class' => 'form element'
                    )))
    ));

    $this->addElement('Radio', 'list_browseorder', array(
            'label' => 'Default ordering on Browse Listings',
            'description' => 'Select the default ordering of listings on the browse listings page.',
            'multiOptions' => array(
                    1 => 'All listings in descending order of creation.',
                    2 => 'All listings in descending order of views.',
                    3 => 'All listings in alphabetical order.',
                    4 => 'Sponsored listings followed by others in descending order of creation.',
                    5 => 'Featured listings followed by others in descending order of creation.',
                    6 => 'Sponsored & Featured listings followed by Sponsored listings followed by Featured listings followed by others in descending order of creation.',
                    7 => 'Featured & Sponsored listings followed by Featured listings followed by Sponsored listings followed by others in descending order of creation.',
            ),
            'value' => $settings->getSetting('list.browseorder', 1),
    ));

    $this->addElement('Radio', 'list_tinymceditor', array(
            'label' => 'TinyMCE Editor',
            'description' => 'Allow TinyMCE editor for discussion message of listings.',
            'multiOptions' => array(
                    1 => 'Yes',
                    0 => 'No'
            ),
            'value' => $settings->getSetting('list.tinymceditor', 1),
    ));

    $this->addElement('Text', 'list_page', array(
            'label' => 'Listings Per Page',
            'description' => 'How many listings will be shown per page on the Browse Listings page?',
            'allowEmpty' => false,
            'maxlength' => '3',
            'required' => true,
            'filters' => array(
                    new Engine_Filter_Censor(),
                    'StripTags',
                    new Engine_Filter_StringLength(array('max' => '3'))
            ),
            'value' => $settings->getSetting('list.page', 10),
    ));

    $this->addElement('Radio', 'list_categorywithslug', array(
        'label' => 'Slug URL',
        'description' => 'Do you want to replace blank-space in your category name by "-" in URL?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('list.categorywithslug', 1),
    ));

    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $field = 'list_code_share';
    $this->addElement('Dummy', "$field", array(
        'label' => 'Social Share Widget Code',
        'description' => "<a class='smoothbox' href='". $view->url(array('module' => 'seaocore', 'controller' => 'settings', 'action' => 'social-share', 'field' => "$field"), 'admin_default', true) ."'>Click here</a> to add your social share code.",
        'ignore' => true,
    ));
    $this->$field->addDecorator('Description', array('placement' => 'PREPEND', 'class' => 'description', 'escape' => false));

    $this->addElement('Radio', 'list_expirydate_enabled', array(
        'label' => 'Listings Duration',
        'description' => 'Do you want fixed duration listings on your website? (Fixed Duration listings will get expired after certain time and will not appear in home, browse pages and widgets.)',
        'multiOptions' => array(
            0 => 'No',
            1 => 'Yes, Listing owners will be able to choose if their listings should get expired along with expiry time.',
            2 => 'Yes, make all listings expire after a fixed duration. (You can choose the duration below.)'
        ),
        'onchange' => 'showExpiryDuration(this.value)',
        'value' => $settings->getSetting('list.expirydate.enabled', 0),
    ));

    $this->addElement('Duration', 'list_expirydate_duration', array(
        'label' => 'Duration',
        'description' => 'Select the duration after which listings will expire. (This count will start from the listings approval dates. Users will see this duration while creating their listings.)',
        'value' => $settings->getSetting('list.expirydate.duration', array('1', 'week')),
    ));
    $multiOptions = array(
        'day' => 'Day(s)',
        'week' => 'Week(s)',
        'month' => 'Month(s)',
        'year' => 'Year(s)');
    $this->getElement('list_expirydate_duration')
            ->setMultiOptions($multiOptions);
    //->setDescription('-')
    
    $this->addElement('Text', 'list_title_turncationsponsored', array(
            'label' => 'Title Truncation Limit For Sponsored ',
            'allowEmpty' => false,
            'maxlength' => '3',
            'required' => true,
            'description' => 'What maximum limit should be applied to the number of characters in the titles of items in the Sponsored widgets? (Enter a number between 1 and 999. Titles having more characters than this limit will be truncated. Complete titles will be shown on mouseover.)',
            'value' => $settings->getSetting('list.title.turncationsponsored', 18),
    ));

    $this->addElement('Text', 'list_title_turncation', array(
            'label' => 'Title Truncation Limit',
            'allowEmpty' => false,
            'maxlength' => '3',
            'required' => true,
            'description' => 'What maximum limit should be applied to the number of characters in the titles of items in the widgets? (Enter a number between 1 and 999. Titles having more characters than this limit will be truncated. Complete titles will be shown on mouseover.)',
            'value' => $settings->getSetting('list.title.turncation', 18),
    ));

    $this->addElement('Text', 'list_manifestUrlP', array(
            'label' => 'Listings URL alternate text for "listingitems"',
            'allowEmpty' => false,
            'required' => true,
            'description' => 'Please enter the text below which you want to display in place of "listingitems" in the URLs of this plugin.',
            'value' => $settings->getSetting('list.manifestUrlP', "listingitems"),
    ));

    $this->addElement('Text', 'list_manifestUrlS', array(
            'label' => 'Listings URL alternate text for "listingitem"',
            'allowEmpty' => false,
            'required' => true,
            'description' => 'Please enter the text below which you want to display in place of "listingitem" in the URLs of this plugin.',
            'value' => $settings->getSetting('list.manifestUrlS', "listingitem"),
    ));

    $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
    ));
  }

}
