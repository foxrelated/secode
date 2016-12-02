<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Form_Admin_Global extends Engine_Form {
    
    // IF YOU WANT TO SHOW CREATED ELEMENT ON PLUGIN ACTIVATION THEN INSERT THAT ELEMENT NAME IN THE BELOW ARRAY.
    public $_SHOWELEMENTSBEFOREACTIVATE = array(
        "submit_lsetting", "environment_mode"
    );
    
  public function init() {
    $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    $this
            ->setTitle('Global Settings')
            ->setDescription('These settings affect all members in your community.');

    $this->addElement('Text', 'sitegroup_lsettings', array(
        'label' => 'Enter License key',
        'description' => "Please enter your license key that was provided to you when you purchased this plugin. If you do not know your license key, please contact the Support Team of SocialEngineAddOns from the Support section of your Account Area.(Key Format: XXXXXX-XXXXXX-XXXXXX )",
        'value' => $coreSettings->getSetting('sitegroup.lsettings'),
    ));

    if( APPLICATION_ENV == 'production' ) {
      $this->addElement('Checkbox', 'environment_mode', array(
          'label' => 'Your community is currently in "Production Mode". We recommend that you momentarily switch your site to "Development Mode" so that the CSS of this plugin renders fine as soon as the plugin is installed. After completely installing this plugin and visiting few groups of your site, you may again change the System Mode back to "Production Mode" from the Admin Panel Home. (In Production Mode, caching prevents CSS of new plugins to be rendered immediately after installation.)',
          'description' => 'System Mode',
//          'value' => 1,
      ));
    } else {
      $this->addElement('Hidden', 'environment_mode', array('order' => 990, 'value' => 0));
    }

    //Add submit button
    $this->addElement('Button', 'submit_lsetting', array(
        'label' => 'Activate Your Plugin Now',
        'type' => 'submit',
        'ignore' => true
    ));


    $this->addElement('Text', 'language_phrases_group', array(
        'label' => 'Singular Group Title',
        'description' => 'Please enter the Singular Title for group. This text will come in places like feeds generated, widgets etc.',
        'allowEmpty' => FALSE,
        'validators' => array(
            array('NotEmpty', true),
        ),
        'value'=> Engine_Api::_()->getApi('settings', 'core')->getSetting( "language.phrases.group", "group"),

    ));
    
    $this->addElement('Text', 'language_phrases_groups', array(
        'label' => 'Plural Group Title',
        'description' => 'Please enter the Plural Title for groups. This text will come in places like Main Navigation Menu, Group Navigation Menu, widgets etc.',
        'allowEmpty' => FALSE,
        'validators' => array(
            array('NotEmpty', true),
        ),
      'value'=> Engine_Api::_()->getApi('settings', 'core')->getSetting( "language.phrases.groups", "groups"),
    ));



    $this->addElement('Text', 'sitegroup_manifestUrlP', array(
        'label' => 'Groups URL alternate text for "groupitems"',
        'allowEmpty' => false,
        'required' => true,
        'description' => 'Please enter the text below which you want to display in place of "groupitems" in the URLs of this plugin.',
        'value' => $coreSettings->getSetting('sitegroup.manifestUrlP', "groupitems"),
    ));

    $this->addElement('Text', 'sitegroup_manifestUrlS', array(
        'label' => 'Groups URL alternate text for "groupitem"',
        'allowEmpty' => false,
        'required' => true,
        'description' => 'Please enter the text below which you want to display in place of "groupitem" in the URLs of this plugin.',
        'value' => $coreSettings->getSetting('sitegroup.manifestUrlS', "groupitem"),
    ));

    //VALUE FOR ENABLE/DISABLE PACKAGE
    $this->addElement('Radio', 'sitegroup_package_enable', array(
        'label' => 'Packages',
        'description' => 'Do you want Packages to be activated for Groups / Communities? Packages can vary based on the features available to the groups created under them. If enabled, users will have to select a package in the first step while creating a new group. Group admins will be able to change their package later. To manage group packages, go to Manage Group Packages section. (Note: If packages are enabled, then feature settings for groups will depend on packages, and member levels based feature settings will be off. If packages are disabled, then feature settings for groups could be configured for member levels.)',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'onclick' => 'showpackageOption(this.value)',
        'value' => $coreSettings->getSetting('sitegroup.package.enable', 1),
    ));
    
     $this->addElement('Radio', 'sitegroup_package_view', array(
            'label' => 'Package View',
            'description' => 'Select the view type of packages that will be shown in the first step of group creation.',
            'multiOptions' => array(
                1 => 'Vertical',
                0 => 'Horizontal'
            ),
            'value' => $coreSettings->getSetting('sitegroup.package.view', 1),
        ));
     
    $packageInfoArray = array('price' => 'Price', 'billing_cycle' => 'Billing Cycle', 'duration' => 'Duration', 'featured' => 'Featured', 'sponsored' => 'Sponsored', 'tellafriend' => 'Tell a friend', 'print' => 'Print', 'overview' => 'Rich Overview', 'map' => 'Map', 'insights' => 'Insights', 'contactdetails' => 'Contact Details', 'sendanupdate' => 'Send an Update', 'apps' => 'Apps available', 'description' => 'Description');

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouptwitter')) {
        $packageInfoArray = array_merge($packageInfoArray, array('twitterupdates' => 'Display Twitter Updates'));
    }

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')) {
        $packageInfoArray = array_merge($packageInfoArray, array('ads' => 'Ads Display'));
    }

    $this->addElement('MultiCheckbox', 'sitegroup_package_information', array(
        'label' => 'Package Information',
        'description' => 'Select the information options that you want to be available in package details.',
        'multiOptions' => $packageInfoArray,
        'value' => $coreSettings->getSetting('sitegroup.package.information', array_keys($packageInfoArray)),
    ));
        
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $localeObject = Zend_Registry::get('Locale');
    $currencyCode = $coreSettings->getSetting('payment.currency', 'USD');
    $currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);
    $this->addElement('Dummy', 'sitegroup_currency', array(
        'label' => 'Currency',
        'description' => "<b>" . $currencyName . "</b> <br class='clear' /> <a href='" . $view->url(array('module' => 'payment', 'controller' => 'settings'), 'admin_default', true)."' target='_blank'>" . Zend_Registry::get('Zend_Translate')->_('edit currency') . "</a>",
    ));
    $this->getElement('sitegroup_currency')->getDecorator('Description')->setOptions(array('placement', 'APPEND', 'escape' => false));

    $this->addElement('Radio', 'sitegroup_payment_benefit', array(
        'label' => 'Payment Status for Directory Item / Group Activation',
        'description' => "Do you want to activate directory items / groups immediately after payment, before the payment passes the gateways' fraud checks? This may take any time from 20 minutes to 4 days, depending on the circumstances and the gateway. (Note: If you want to manually activate groups, then you can set this while creating a group package.)",
        'multiOptions' => array(
            'all' => 'Activate group immediately.',
            'some' => 'Activate if member has an existing successful transaction, wait if this is their first.',
            'none' => 'Wait until the gateway signals that the payment has completed successfully.',
        ),
        'value' => $coreSettings->getSetting('sitegroup.payment.benefit', 'all'),
    ));

    $this->addElement('Radio', 'sitegroup_manageadmin', array(
        'label' => 'Group Admins',
        'description' => 'Do you want there to be multiple admins for directory items / groups on your site? (If enabled, then every Group will be able to have multiple administrators who will be able to manage that Group. Group Admins will have the authority to add other users as administrators of their Group.)',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $coreSettings->getSetting('sitegroup.manageadmin', 1),
    ));
if(!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemenu')){
    $this->addElement('Radio', 'sitegroup_show_menu', array(
        'label' => 'Groups Link',
        'description' => 'Select the location of the main link for Groups.',
        'multiOptions' => array(
            3 => 'Main Navigation Menu',
            2 => 'Mini Navigation Menu',
            1 => 'Footer Menu',
            0 => 'Member Home Group Left side Navigation'
        ),
        'value' => $coreSettings->getSetting('sitegroup.show.menu', 3),
    ));
}
    //VALUE FOR ENABLE/DISABLE REPORT
    $this->addElement('Radio', 'sitegroup_report', array(
        'label' => 'Report as Inappropriate',
        'description' => 'Do you want to allow logged-in members to be able to report groups as inappropriate? (Members will also be able to mention the reason why they find the group inappropriate.)',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $coreSettings->getSetting('sitegroup.report', 1),
    ));

    //VALUE FOR ENABLE /DISABLE SHARE
    $this->addElement('Radio', 'sitegroup_share', array(
        'label' => 'Community Sharing',
        'description' => 'Do you want to allow members to share directory items / groups within your community?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $coreSettings->getSetting('sitegroup.share', 1),
    ));

    //VALUE FOR ENABLE /DISABLE SHARE
    $this->addElement('Radio', 'sitegroup_socialshare', array(
        'label' => 'Social Sharing',
        'description' => 'Do you want social sharing to be enabled for directory items / groups? (If enabled, social sharing buttons will be shown on the Profile Group of directory items / groups.)',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $coreSettings->getSetting('sitegroup.socialshare', 1),
    ));

    //VALUE FOR CAPTCHA
    $this->addElement('Radio', 'sitegroup_captcha_post', array(
        'label' => 'CAPTCHA For Tell a friend',
        'description' => 'Do you want visitors to enter a validation code in Tell a friend form?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $coreSettings->getSetting('sitegroup.captcha.post', 1),
    ));

    //VALUE FOR ENABLE /DISABLE LOCATION FIELD
    $this->addElement('Radio', 'sitegroup_locationfield', array(
        'label' => 'Location Field',
        'description' => 'Do you want the Location field to be enabled for directory items / groups?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'onclick' => 'showlocationOption(this.value)',
        'value' => $coreSettings->getSetting('sitegroup.locationfield', 1),
    ));

    
    $this->addElement('Radio', 'sitegroup_multiple_location', array(
        'label' => 'Allow Multiple Locations',
        'description' => 'Do you want to allow group admins to enter multiple locations for their Groups? (If you select ‘Yes’, then users will be able to add multiple locations for their Groups from their Group Dashboards.)',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $coreSettings->getSetting('sitegroup.multiple.location', 0),
    ));

    //VALUE FOR ENABLE /DISABLE MAP
    $this->addElement('Radio', 'sitegroup_location', array(
        'label' => 'Maps Integration',
        'description' => ' Do you want Maps Integration to be enabled for directory items / groups? (With this enabled, items / groups having location information could also be seen on Map. The "Groups Home" and "Browse Groups" also enable you to see the items plotted on Map.)',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'onclick' => 'showMapOptions(this.value)',
        'value' => $coreSettings->getSetting('sitegroup.location', 1),
    ));

    //VALUE FOR ENABLE /DISABLE Bouncing Animation
    $this->addElement('Radio', 'sitegroup_map_sponsored', array(
        'label' => 'Sponsored Items with a Bouncing Animation',
        'description' => 'Do you want the sponsored directory items / groups to be shown with a bouncing animation in the Map?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $coreSettings->getSetting('sitegroup.map.sponsored', 1),
    ));

    $this->addElement('Text', 'sitegroup_map_city', array(
        'label' => 'Centre Location for Map at Groups Home and Browse Groups',
        'description' => 'Enter the location which you want to be shown at centre of the map which is shown on Groups Home and Browse Groups when Map View is chosen to view Groups / Communities.(To show the whole world on the map, enter the word "World" below.)',
        'required' => true,
        'value' => $coreSettings->getSetting('sitegroup.map.city', "World"),
    ));

    $this->addElement('Select', 'sitegroup_map_zoom', array(
        'label' => "Default Zoom Level for Map at Groups Home and Browse Groups",
        'description' => 'Select the default zoom level for the map which is shown on Groups Home and Browse Groups when Map View is chosen to view Groups / Communities. (Note that as higher zoom level you will select, the more number of surrounding cities/locations you will be able to see.)',
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
        'value' => $coreSettings->getSetting('sitegroup.map.zoom', 1),
        'disableTranslator' => 'true'
    ));

    //VALUE FOR ENABLE /DISABLE Proximity Search
    $this->addElement('Radio', 'sitegroup_proximitysearch', array(
        'label' => 'Proximity Search',
        'description' => 'Do you want proximity search to be enabled for directory items / groups? (Proximity search will enable users to search for items / groups within a certain distance from a location.)',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'onclick' => 'showlocationKM(this.value)',
        'value' => $coreSettings->getSetting('sitegroup.proximitysearch', 1),
    ));

    //VALUE FOR ENABLE /DISABLE Proximity Search IN Kilometer
    $this->addElement('Radio', 'sitegroup_proximity_search_kilometer', array(
        'label' => 'Proximity Search Metric',
        'description' => 'What metric do you want to be used for proximity search?',
        'multiOptions' => array(
            0 => 'Miles',
            1 => 'Kilometers'
        ),
        'value' => $coreSettings->getSetting('sitegroup.proximity.search.kilometer', 0),
    ));
    
    $this->addElement('Radio', 'sitegroup_multiple_location', array(
        'label' => 'Allow Multiple Locations',
        'description' => 'Do you want to allow group admins to enter multiple locations for their Groups? (If you select ‘Yes’, then users will be able to add multiple locations for their Groups from their Group Dashboards.)',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $coreSettings->getSetting('sitegroup.multiple.location', 0),
    ));
    
    //VALUE FOR COMMENT
    $this->addElement('Radio', 'sitegroup_checkcomment_widgets', array(
        'label' => 'Comments',
        'description' => 'Do you want comments to be enabled for directory items / groups? (If enabled, then users will be able to comment on items / groups on their Info tabs.)',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $coreSettings->getSetting('sitegroup.checkcomment.widgets', 1),
    ));

    //VALUE FOR CAPTCHA
    $this->addElement('Radio', 'sitegroup_sponsored_image', array(
        'label' => 'Sponsored Label',
        'description' => 'Do you want to show "SPONSORED" label on the main profile of sponsored directory items / groups above the profile picture?',
        'onclick' => 'showsponsored(this.value)',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $coreSettings->getSetting('sitegroup.sponsored.image', 1),
    ));

    //COLOR VALUE FOR SPONSORED
    $this->addElement('Text', 'sitegroup_sponsored_color', array(
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formImagerainbowSponsred.tpl',
                    'class' => 'form element'
            )))
    ));

    //VALUE FOR CAPTCHA
    $this->addElement('Radio', 'sitegroup_feature_image', array(
        'label' => 'Featured Label',
        'description' => 'Do you want to show "FEATURED" label on the main profile of featured directory items / groups below the profile picture?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'onclick' => 'showfeatured(this.value)',
        'value' => $coreSettings->getSetting('sitegroup.feature.image', 1),
    ));

    //COLOR VALUE FOR FEATURED
    $this->addElement('Text', 'sitegroup_featured_color', array(
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formImagerainbowFeatured.tpl',
                    'class' => 'form element'
            )))
    ));

    //VALUE FOR CAPTCHA
    $this->addElement('Radio', 'sitegroup_fs_markers', array(
        'label' => 'Featured & Sponsored Markers',
        'description' => 'On Groups Home, Browse Groups and My Groups how do you want a Group to be indicated as featured and sponsored ?',
        'multiOptions' => array(
            1 => 'Using Labels (See FAQ for customizing the labels)',
            0 => 'Using Icons (See FAQ for customizing the icons)',
        ),
        'value' => $coreSettings->getSetting('sitegroup.fs.markers', 1),
    ));

    $this->addElement('Radio', 'sitegroup_network', array(
        'label' => 'Browse by Networks',
        'description' => "Do you want to show directory items / groups according to viewer's network if he has selected any? (If set to no, all the items / groups will be shown.)",
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'onclick' => 'showDefaultNetwork(this.value)',
        'value' => $coreSettings->getSetting('sitegroup.network', 0),
    ));

    //VALUE FOR Group Dispute Link.
    $this->addElement('Radio', 'sitegroup_default_show', array(
        'label' => 'Set Only My Networks as Default in search',
        'description' => 'Do you want to set "Only My Networks" option as default for Show field in the search form widget? (This widget appears on the groups browse and home groups, and enables users to search and filter groups.)',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'onclick' => 'showDefaultNetworkType(this.value)',
        'value' => $coreSettings->getSetting('sitegroup.default.show', 0),
    ));

    $this->addElement('Radio', 'sitegroup_networks_type', array(
        'label' => 'Network selection for Groups',
        'description' => "You have chosen that viewers should only see Groups of their network(s). How should a Group's network(s) be decided?",
        'multiOptions' => array(
            0 => "Group Owner's network(s) [If selected, only members belonging to group owner's network(s) will see the Groups.]",
            1 => "Selected Networks [If selected, group admins will be able to choose the networks of which members will be able to see their Group.]"
        ),
        'value' => $coreSettings->getSetting('sitegroup.networks.type', 0),
    ));
    
    $this->addElement('Radio', 'sitegroup_networkprofile_privacy', array(
        'label' => 'Display Profile Group only to Network Users',
        'description' => "Do you want to show the Directory Item / Group Profile group only to users of the same network. (If set to yes and \"Browse By Networks\" is enabled then users would not be able to view the profile group of those groups which does not belong to their networks.)",
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        // 'onclick' => 'showviewablewarning(this.value);',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.networkprofile.privacy', 0),
    ));
    $this->addElement('Radio', 'sitegroup_privacybase', array(
        'label' => 'Display of All Groups in widgets',
        'description' => "Do you want to show all the groups to the user in the widgets and browse groups of this plugin irrespective of privacy? [Note: If you select 'No', then only those groups will be shown in the widgets and browse groups which are viewable to the current logged-in user. But this may slightly affect the loading speed of your website. To avoid such loading delay to the best possible extent, we are also using caching based display.)",
        'multiOptions' => array(
            0 => 'Yes',
            1 => 'No'
        ),
        // 'onclick' => 'showviewablewarning(this.value);',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.privacybase', 0),
    ));

    //Order of browse group
    $this->addElement('Radio', 'sitegroup_browseorder', array(
        'label' => 'Default ordering on Browse Groups',
        'description' => 'Select the default ordering of groups on the browse groups.',
        'multiOptions' => array(
            1 => 'All groups in descending order of creation.',
            2 => 'All groups in descending order of views.',
            3 => 'All groups in alphabetical order.',
            4 => 'Sponsored groups followed by others in descending order of creation.',
            5 => 'Featured groups followed by others in descending order of creation.',
            6 => 'Sponsored & Featured groups followed by Sponsored groups followed by Featured groups followed by others in descending order of creation.',
            7 => 'Featured & Sponsored groups followed by Featured groups followed by Sponsored groups followed by others in descending order of creation.',
        ),
        'value' => $coreSettings->getSetting('sitegroup.browseorder', 1),
    ));

    $this->addElement('Radio', 'sitegroup_addfavourite_show', array(
        'label' => 'Linking Groups',
        'description' => 'Do you want members to be able to Link their Groups to other Groups? (Linking is useful to show related Groups. For example, a Chef\'s Group can be linked to the Restaurant\'s Group where he works, or a Store\'s Group can be linked to the Groups of the Brands that it sells. If enabled, a "Link to your Group" link will appear on Groups.)',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $coreSettings->getSetting('sitegroup.addfavourite.show', 1),
    ));

    $this->addElement('Radio', 'sitegroup_layoutcreate', array(
        'label' => 'Edit Group Layout',
        'description' => 'Do you want to enable group admins to alter the block positions / add new available blocks on the directory item / group profile? (If enabled, then group admins will also be able to add HTML blocks on their directory item / group profile.)',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $coreSettings->getSetting('sitegroup.layoutcreate', 0),
    ));

    $this->addElement('Radio', 'sitegroup_category_edit', array(
        'label' => 'Edit Group Category',
        'description' => 'Do you want to allow group admins to edit category of their groups?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'onclick' => 'showcategoryblock(this.value);',
        'value' => $coreSettings->getSetting('sitegroup.category.edit', 0),
    ));

    //$description = Zend_Registry::get('Zend_Translate')->_('Do you want to show categories, subcategories and 3%s level categories with slug in the url.');
    //$description = sprintf($description, "<sup>rd</sup>");
    $this->addElement('Radio', 'sitegroup_categorywithslug', array(
        'label' => 'Slug URL',
        'description' => 'Do you want to replace blank-space in your category name by "-" in URL?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $coreSettings->getSetting('sitegroup.categorywithslug', 1),
    ));

    //$this->sitegroup_categorywithslug->getDecorator('Description')->setOptions(array('placement'=> 'PREPEND', 'escape' => false));

    $this->addElement('Radio', 'sitegroup_claimlink', array(
        'label' => 'Claim a Group Listing',
        'description' => 'Do you want users to be able to file claims for directory items / groups ? (Claims filed by users can be managed from the Manage Claims section.)',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'onclick' => 'showclaim(this.value)',
        'value' => $coreSettings->getSetting('sitegroup.claimlink', 1),
    ));

    if(!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemenu')){
      $this->addElement('Radio', 'sitegroup_claim_show_menu', array(
          'label' => 'Claim a Group link',
          'description' => 'Select the position for the "Claim a Group" link.',
          'multiOptions' => array(
              2 => 'Show this link on Groups Navigation Menu.',
              1 => 'Show this link on Footer Menu.',
              0 => 'Do not show this link.'
          ),
          'value' => $coreSettings->getSetting('sitegroup.claim.show.menu', 2),
      ));
    }
    $this->addElement('Radio', 'sitegroup_claim_email', array(
        'label' => 'Notification for Group Claim',
        'description' => 'Do you want to receive e-mail notification when a member claims a group?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $coreSettings->getSetting('sitegroup.claim.email', 1),
    ));

    $this->addElement( 'Radio' , 'sitegroup_automatically_like' , array (
      'label' => 'Automatic Like',
      'description' => "Do you want members to automatically Like a group they create?",
      'multiOptions' => array (
        1 => 'Yes' ,
        0 => 'No'
      ) ,
      'value' => $coreSettings->getSetting( 'sitegroup.automatically.like' , 1),
    )) ;

		$this->addElement('Radio', 'sitegroup_hide_left_container', array(
				'label' => 'Hide Left / Right Column on Group Profile',
				'description' => sprintf(Zend_Registry::get('Zend_Translate')->_('When you have "Advertisements / Community Ads" enabled to be shown on Group Profile from "%1$sAd Settings%2$s" section, then do you want the left / right column on Group Profile to be hidden when users click on the Group tabs other than Updates, Info and Overview?'), "<a href='" . $view->url(array('module' => 'sitegroup', 'controller' => 'settings', 'action' =>'adsettings'), 'admin_default', true)."' target='_blank'>", '</a>'),
				'multiOptions' => array(
						1 => 'Yes',
						0 => 'No'
				),
				'value' => $coreSettings->getSetting( 'sitegroup.hide.left.container', 0),
		));
		$this->sitegroup_hide_left_container->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false)); 

		$this->addElement('Radio', 'sitegroup_show_tabs_without_content', array(
				'label' => 'Show Tabs with no Respective Content',
				'description' => 'When there are content types in a Group (like Albums, Videos, etc.) with no respective content, then do you want their tabs to appear on Group profile to users who do not have permission to add that content?',
				'multiOptions' => array(
						1 => 'Yes',
						0 => 'No'
				),
				'value' => $coreSettings->getSetting( 'sitegroup.show.tabs.without.content', 0),
		));

		$this->addElement('Radio', 'sitegroup_slding_effect', array(
				'label' => 'Enable Sliding Effect on Tabs',
				'description' => 'Do you want to enable sliding effect when tabs on Group Profile are clicked?',
				'multiOptions' => array(
						1 => 'Yes',
						0 => 'No'
				),
				'value' => $coreSettings->getSetting( 'sitegroup.slding.effect', 1),
		));

    $this->addElement('Radio', 'sitegroup_mylike_show', array(
        'label' => 'Groups I Like Link',
        'description' => 'Do you want to show the "Groups I Like" link to users? This link appears on "My Groups" and enables users to see the list of Groups that they have Liked.',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $coreSettings->getSetting('sitegroup.mylike.show', 1),
    ));

    $this->addElement('Text', 'sitegroup_group', array(
        'label' => 'Groups / Communities Per Page',
        'description' => 'How many groups / communities will be shown per page in "Browse Groups" and "My Groups" groups?',
        'allowEmpty' => false,
        'maxlength' => '3',
        'required' => true,
        'filters' => array(
            new Engine_Filter_Censor(),
            'StripTags',
            new Engine_Filter_StringLength(array('max' => '3'))
        ),
        'value' => $coreSettings->getSetting('sitegroup.group', 24),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));
    
    $this->addElement('Radio', 'sitegroup_redirection', array(
        'label' => 'Redirection of Groups link',
        'description' => 'Please select the redirection page for Groups, when user click on "Groups" link at Main Navigation Menu.',
        "multiOptions" => array(
            'home' => 'Groups Home',
            'index' => 'Browse Groups'
        ),
        'value' => $coreSettings->getSetting('sitegroup.redirection', 'home'),
    ));     

    $this->addElement('Text', 'sitegroup_showmore', array(
        'label' => 'Tabs / Links',
        'allowEmpty' => false,
        'maxlength' => '3',
        'required' => true,
        'description' => 'How many tabs / links do you want to show on directory item / group profile by default? (Note that if there are more tabs / links than the limit entered by you then a "More" tab / link will appear, clicking on which will show the remaining hidden tabs / links. Tabs are available in the tabbed layout, and links in the non-tabbed layout. To choose the layout for Groups on your site, visit the "Group Layout" section.)',
        'value' => $coreSettings->getSetting('sitegroup.showmore', 8),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    $this->addElement('Text', 'sitegroupshow_navigation_tabs', array(
        'label' => 'Tabs in Groups navigation bar',
        'allowEmpty' => false,
        'maxlength' => '3',
        'required' => true,
        'description' => 'How many tabs do you want to show on Groups main navigation bar by default? (Note: If number of tabs exceeds the limit entered by you then a "More" tab will appear, clicking on which will show the remaining hidden tabs. To choose the tab to be shown in this navigation menu, and their sequence, please visit: "Layout" > "Menu Editor")',
        'value' => $coreSettings->getSetting('sitegroupshow.navigation.tabs', 8),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    $this->addElement('Radio', 'sitegroup_postedby', array(
        'label' => 'Created By',
        'description' => "Do you want to enable Created by option for the Groups on your site? (Selecting Yes here will display the member's name who has created the group.)",
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $coreSettings->getSetting('sitegroup.postedby', 1),
    ));

    $advfeedmodule = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity');
    $adddescription = '';
    if (!$advfeedmodule)
		$adddescription = "and requires it to be installed and enabled on your site. Please install this plugin after downloading it from your Client Area on SocialEngineAddOns. You may purchase this plugin <a href='http://www.socialengineaddons.com/socialengine-advanced-activity-feeds-wall-plugin' target='_blank'>over here</a>";
    $this->addElement('Radio', 'sitegroup_postfbgroup', array(
        'label' => 'Allow Facebook Page Linking',
        'description' => "Do you want to allow users to link their Facebook Pages with their Groups on your website? If you select 'Yes' over here, then users will see a new block in the 'Marketing' section of their Group Dashboard which will enable them to enter the URL of their Facebook Page. With this, the updates made by users on their Group on your site will also be published on their Facebook Page. Also, the Facebook Like Box for the Facebook Page will be displayed on Group Profile. The Facebook Like Box will:<br /><br /><ul style='margin-left: 20px;'><li>Show the recent posts from the Facebook Page.</li><li>Show how many people already like the Facebook Page.</li><li>Enable visitors to Like the Facebook Page from your site.</li></ul><br /><br />If you do not want to show the Facebook Like Box on Groups with linked Facebook Pages, then simply remove the widget from the 'Layout Editor'. With linked Facebook Page, if Group Admins select 'Publish this on Facebook' option while posting 
their updates, then these updates will be published on their Facebook Profile as well as Facebook Page. (Note: Publishing updates on Facebook Pages via this linking is dependent on the <a href='http://www.socialengineaddons.com/socialengine-advanced-activity-feeds-wall-plugin' target='_blank'> Advanced Activity Feeds / Wall Plugin</a> ".$adddescription.".)",
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $coreSettings->getSetting('sitegroup.postfbgroup', 1),
    ));
		$this->sitegroup_postfbgroup->addDecorator('Description', array('placement' => 'PREPEND','class' => 'description', 'escape' => false));

		$publish_fb_places = array('0' => 1, '1' => 2);
    $publish_fb_places = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.publish.facebook', serialize($publish_fb_places));
    if(!empty($publish_fb_places) && !is_array($publish_fb_places)) {
      $publish_fb_places = unserialize($publish_fb_places);
    }
    $this->addElement('MultiCheckbox', 'sitegroup_publish_facebook', array(
        'label' => 'Publishing Updates on Facebook',
        'description' => "Choose the places on Facebook where users will be able to publish their updates that they post on Groups of your site.",
        'multiOptions' => array(            
            '1' => 'Publish this post on Facebook Page linked with this Group. [Note: This setting will only work if you choose \'Yes\' option for the setting "Allow Facebook Page Linking".]',
            '2' => 'Publish this post on my Facebook Timeline',
        ),
        'value' => $publish_fb_places
    )); 
    
    $this->addElement('Radio', 'sitegroup_tinymceditor', array(
        'label' => 'Tinymce Editor',
        'description' => 'Allow TinyMCE editor for discussion message of Groups.',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $coreSettings->getSetting('sitegroup.tinymceditor', 1),
    ));

    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $field = 'sitegroup_code_share';
    $this->addElement('Dummy', "$field", array(
        'label' => 'Social Share Widget Code',
        'description' => "<a class='smoothbox' href='". $view->url(array('module' => 'seaocore', 'controller' => 'settings', 'action' => 'social-share', 'field' => "$field"), 'admin_default', true) ."'>Click here</a> to add your social share code.",
        'ignore' => true,
    ));
    $this->$field->addDecorator('Description', array('placement' => 'PREPEND', 'class' => 'description', 'escape' => false));

    $this->addElement('Textarea', 'sitegroup_defaultgroupcreate_email', array(
      'label' => 'Alerted by Email',
      'description' => 'Please enter comma-separated list, or one-email-per-line. Email is sent to the below email ids when members create new Groups.',
      'value' => $coreSettings->getSetting('sitegroup.defaultgroupcreate.email', Engine_API::_()->seaocore()->getSuperAdminEmailAddress()),
    ));

    $this->addElement('Text', 'sitegroup_title_truncation', array(
        'label' => 'Title Truncation Limit',
        'allowEmpty' => false,
        'maxlength' => '3',
        'required' => true,
        'description' => 'What maximum limit should be applied to the number of characters in the title of items in the widgets? (Enter a number between 1 and 999. Titles having more characters than this limit will be truncated. Complete titles will be shown on mouseover.)',
        'value' => $coreSettings->getSetting('sitegroup.title.truncation', 18),
        'validators' => array(
            array('Int', true),
            array('GreaterThan', true, array(0)),
        ),
    ));

    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusiness') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepage')) {
    
    $description = 'Do you want to have a common CSS (/application/modules/Sitepage/externals/styles/common_style_page_business_group.css) on your website for Directory / Pages Plugin and Directory / Businesses Plugin and  Groups / Communities Plugin? (Note: As these 3 are similar plugins, it is recommended to have a common CSS loaded for 3 plugins for good webpage performance. You might select the No option here if you have different custom styling for these 3 plugins on your website.)';
		}
		elseif(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepage')) {
    $description = 'Do you want to have a common CSS (/application/modules/Sitepage/externals/styles/common_style_page_group.css) on your website for Directory / Pages Plugin and Groups / Communities Plugin? (Note: As these are both similar plugins, it is recommended to have a common CSS loaded for both plugins for good webpage performance. You might select the No option here if you have different custom styling for these 2 plugins on your website.)';
		} 
		elseif(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusiness')) {
    $description = 'Do you want to have a common CSS (/application/modules/Sitebusiness/externals/styles/common_style_business_group.css) on your website for Groups / Communities Plugin and Directory / Businesses Plugin? (Note: As these are both similar plugins, it is recommended to have a common CSS loaded for both plugins for good webpage performance. You might select the No option here if you have different custom styling for these 2 plugins on your website.)';

		}

		if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusiness') || Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepage')) {
		
			// VALUE FOR ENABLE /DISABLE SHARE
			$this->addElement('Radio', 'seaocore_common_css', array(
				'label' => 'Common CSS',
				'description' => $description,
				'multiOptions' => array(
					1 => 'Yes',
					0 => 'No'
				),
				'value' => $coreSettings->getSetting('seaocore.common.css', 0),
			));
		}
		
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}
