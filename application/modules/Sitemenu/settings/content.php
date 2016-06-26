<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
$URL = $view->baseUrl() . "/admin/files";
$click = '<a href="' . $URL . '" target="_blank">over here</a>';

$temp_show_in_footer_options = array(
    1 => 'None',
    2 => 'Social Links',
    3 => 'Global Search'
);
$temp_show_in_main_options = array(
    1 => 'None',
    2 => 'Language Editor (works only if you have created language pack from Language Manager. After enabling this option, caching of Main Menu will not work on your website.)',
    3 => 'Global Search'
);
$temp_show_in_mini_options = array(
    0 => 'None',
    1 => 'Global Search',
);

$isSitestoreproductModuleEnable = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreproduct');
if (!empty($isSitestoreproductModuleEnable)) {
  $temp_show_in_footer_options[4] = $temp_show_in_main_options[4]= $temp_show_in_mini_options[2] = 'Product Search';
}

$isSiteadvsearchModuleEnable = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteadvsearch');
if (!empty($isSiteadvsearchModuleEnable)) {
  $temp_show_in_footer_options[5] = $temp_show_in_main_options[5]= $temp_show_in_mini_options[3] = 'Advanced Search';
}

$isEnabledPeopleYouMayKnow = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('peopleyoumayknow');
$isEnabledSuggestions = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('suggestion');
$element_show_suggestion = array();
if(!empty ($isEnabledPeopleYouMayKnow) || !empty ($isEnabledSuggestions)){
  $element_show_suggestion = array(
      'Radio',
      'sitemenu_show_suggestion',
      array(
          'label' => 'Do you want to show suggestions in friend requests?',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => 0
      )
  );
}

return array(
    array(
        'title' => 'Advanced Main Menu',
        'description' => 'Shows the site-wide main menu. You can edit its contents in your menu editor.',
        'category' => 'Advanced Menus Plugin - Interactive and Attractive Navigation',
        'type' => 'widget',
        'name' => 'sitemenu.menu-main',
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'sitemenu_on_logged_out',
                    array(
                        'label' => 'Do you want to show this widget to non-logged-in users?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1
                )),
                array(
                    'Text',
                    'sitemenu_totalmenu',
                    array(
                        'label' => 'How many tabs do you want to show?',
                        'value' => 6
                )),
                array(
                    'Text',
                    'sitemenu_truncation_limit_content',
                    array(
                        'label' => "Enter the title truncation limit for the content.",
                        'value' => 20,
                    )
                ),
                array(
                    'Text',
                    'sitemenu_truncation_limit_category',
                    array(
                        'label' => "Enter the title truncation limit for categories.",
                        'value' => 20,
                    )
                ),
                array(
                    'Radio',
                    'sitemenu_is_more_link',
                    array(
                        'label' => 'Do you want to display "More" link in this widget?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1
                )),
                array(
                    'Radio',
                    'sitemenu_more_link_icon',
                    array(
                        'label' => 'Do you want to show icon for "More" link in this widget? [Note: Works only if you have selected yes for above setting.]',
                        'multiOptions' => array(
                           '1' => 'Yes',
                           '0' => 'No'
                        ),
                        'value' => 1
                )),
                array(
                    'Radio',
                    'sitemenu_is_arrow',
                    array(
                        'label' => 'Do you want to show arrow with menu name?',
                        'description' => '(Note: The arrow sign will come only if the menu contains sub menus.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1
                )),
                array(
                    'Radio',
                    'sitemenu_separator_style',
                    array(
                        'label' => 'Select the separator that you want to show?',
                        'multiOptions' => array(
                            0 => 'None',
                            1 => 'Dot(".")',
                            2 => "Smooth",
                            3 => "Sharp"
                        ),
                        'value' => 3
                )),
                array(
                    'Radio',
                    'sitemenu_show_cart',
                    array(
                        'label' => 'Do you want to show cart icon?',
                        'description' => '(Note: This setting will work only if you have our Stores / Marketplace - Ecommerce Plugin. After enabling this setting, caching of Main Menu will not work on your website.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1
                )),
                array(
                    'Radio',
                    'sitemenu_show_in_main_options',
                    array(
                        'label' => 'Select the option that you want to show in main menu?',
                        'multiOptions' => $temp_show_in_main_options,
                        'value' => 3
                    )
                ),
                array(
                    'Radio',
                    'changeMyLocation',
                    array(
                        'label' => "Do you want to enable 'Location' field, using which users can set their default location.",
                        'multiOptions' => array(1 => "Yes", 0 => "No"),
                        'value' => 0
                    )
                ),                
                array(
                    'Radio',
                    'sitemenu_is_fixed',
                    array(
                        'label' => 'Do you want to fix the main menu on scrolling?',
                        'multiOptions' => array(
                            0 => 'Yes',
                            1 => 'No'
                        ),
                        'value' => 0
                )),
                array(
                    'Text',
                    'sitemenu_fixed_height',
                    array(
                        'label' => 'Enter the height for which you want the main menu to be fix while scrolling up. [Note: Recommended height is 50px but, it may vary for different sites.] (Works only if you have selected yes for above setting. Enter 0 for automated height callibration.)',
                        'value' => 0
                )),
                array(
                    'Radio',
                    'sitemenu_show_extra_on',
                    array(
                        'label' => 'When do you want to show "Cart Icon" and "Language Bar" / "Global Search" /  ("Product Search" also if you have our Stores / Marketplace - Ecommerce Plugin)',
                        'multiOptions' => array(
                            1 => 'Always',
                            0 => 'On Scroll'
                        ),
                        'value' => 0
                )),
                array(
                    'Radio',
                    'sitemenu_style',
                    array(
                        'label' => 'Menu Style (If custom is enabled all the color schemes selected by you from the below options will be reflected in the widget.)',
                        'multiOptions' => array(
                            1 => 'Default',
                            0 => 'Custom'
                        ),
                        'value' => 1
                )),
                array(
                    'Radio',
                    'sitemenu_box_shadow',
                    array(
                        'label' => 'Do you want to show box shadow? (works only if you have selected “custom menu style” from the above setting.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1
                )),
                array(
                    'Radio',
                    'sitemenu_menu_corners_style',
                    array(
                        'label' => 'Select the corner roundings you want to enable for your main menu?',
                        'multiOptions' => array(
                            0 => 'None',
                            1 => "5px (easy soft)",
                            2 => "20px (very soft)"
                        ),
                        'value' => 0
                )),
                 array(
                    'Text',
                    'sitemenu_main_menu_height',
                    array(
                        'label' => "Enter the height for main menu bar.",
                        'description' =>"(Note: Recommended height 30px with icon and 20px without icon.)",
                        'value' => 20,
                    )
                ),                
                array(
                    'Dummy',
                    'Dummy_label',
                    array(
                        'label' => 'Select the color for custom menu style. (Click on the rainbow below to choose your color.)',
                )),
                array(
                      'Text',
                      'sitemenu_menu_link_color',
                      array(
                          'decorators' => array(array('ViewScript', array(
                                      'viewScript' => 'application/modules/Sitemenu/views/scripts/form-image-rainbow/_formImagerainbow1.tpl',
                                      'class' => 'form element',
                                  )))
               )),
               array(
                     'Text',
                      'sitemenu_menu_background_color',
                      array(
                          'decorators' => array(array('ViewScript', array(
                                      'viewScript' => 'application/modules/Sitemenu/views/scripts/form-image-rainbow/_formImagerainbow2.tpl',
                                      'class' => 'form element'
                                  )))
              )),
              array(
                    'Text',
                    'sitemenu_menu_hover_color',
                    array(
                        'decorators' => array(array('ViewScript', array(
                                    'viewScript' => 'application/modules/Sitemenu/views/scripts/form-image-rainbow/_formImagerainbow3.tpl',
                                    'class' => 'form element'
                                )))
              )),
              array(
                    'Text',
                    'sitemenu_menu_hover_background_color',
                    array(
                        'decorators' => array(array('ViewScript', array(
                                    'viewScript' => 'application/modules/Sitemenu/views/scripts/form-image-rainbow/_formImagerainbow7.tpl',
                                    'class' => 'form element'
                                )))
              )),
              array(
                    'Text',
                    'sitemenu_sub_link_color',
                    array(
                        'decorators' => array(array('ViewScript', array(
                                    'viewScript' => 'application/modules/Sitemenu/views/scripts/form-image-rainbow/_formImagerainbow4.tpl',
                                    'class' => 'form element'
                                )))
              )),
              array(
                    'Text',
                    'sitemenu_sub_background_color',
                    array(
                        'decorators' => array(array('ViewScript', array(
                                    'viewScript' => 'application/modules/Sitemenu/views/scripts/form-image-rainbow/_formImagerainbow5.tpl',
                                    'class' => 'form element'
                                )))
              )),
              array(
                    'Text',
                    'sitemenu_sub_hover_color',
                    array(
                        'decorators' => array(array('ViewScript', array(
                                    'viewScript' => 'application/modules/Sitemenu/views/scripts/form-image-rainbow/_formImagerainbow6.tpl',
                                    'class' => 'form element'
                                )))
              )),
              array(
                  'Text',
                  'sitemenu_totalmenu_mobile',
                  array(
                      'label' => 'How many tabs do you want to show in mobile devices?',
                      'value' => 4,
              )),
              array(
                  'Text',
                  'sitemenu_totalmenu_tablet',
                  array(
                      'label' => 'How many tabs do you want to show in tablet devices?',
                      'value' => 6,
              )),
            ),
        ),
    ),
    array(
        'title' => 'Advanced Footer Menu',
        'description' => sprintf('Shows the site-wide footer menu. You can edit its contents in your menu editor. 
Enter details for social links below. (Upload small icons for social links %s. The ideal dimensions of these icons are: 32 X 32 px). Once you upload a new icon at the above mentioned link, re-edit this widget to see them in the icon drop-down list. If you do not want to show your social links to any website, then simply leave the corresponding URL field empty.)', $click),
        'category' => 'Advanced Menus Plugin - Interactive and Attractive Navigation',
        'type' => 'widget',
        'name' => 'sitemenu.menu-footer',
        'requirements' => array(
            'header-footer',
        ),
        'adminForm' => 'Sitemenu_Form_FooterMenu'
    ),
    array(
        'title' => 'Advanced Mini Menu',
        'description' => 'Shows the mini menu. You can edit its contents in your menu editor.',
        'category' => 'Advanced Menus Plugin - Interactive and Attractive Navigation',
        'type' => 'widget',
        'name' => 'sitemenu.menu-mini',
        'autoEdit' => true,
        'requirements' => array(
            'header-footer',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'sitemenu_on_logged_out',
                    array(
                        'label' => 'Do you want to show this widget to non-logged-in users?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1
                )),
                array(
                    'Radio',
                    'sitemenu_show_icon',
                    array(
                        'label' => 'Select the design type you want to show for your mini menu.',
                        'description' => '(Note: Not all menus support icons. Also, you will be able to configure the icons only for the menus you add from the Menu Editor.)',
                        'multiOptions' => array(
                            1 => 'Icon Type',
                            0 => 'Label Type'
                        ),
                        'value' => 1
                )),
                array(
                    'Text',
                    'no_of_updates',
                    array(
                        'label' => 'Count',
                        'description'=> 'No. of Friend Requests, Messages and Notifications to show.',
                        'value' => 10
                    )
                ),
                array(
                    'Radio',
                    'sitemenu_show_in_mini_options',
                    array(
                        'label' => 'Select the searching type you want to show.',
                        'multiOptions' => $temp_show_in_mini_options,
                        'value' => 1
                    )
                ),
                array(
                    'Text',
                    'search_position',
                    array(
                        'label' => 'Enter position of the above menu to be placed in this widget.',
                        'description' => 'Enter 1 for the first position and 999 for last position.',
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'changeMyLocation',
                    array(
                        'label' => "Do you want to enable 'Location' field, using which users can set their default location.",
                        'multiOptions' => array(1 => "Yes", 0 => "No"),
                        'value' => 0
                    )
                ),
                array(
                    'Radio',
                    'showLocationBasedContent',
                    array(
                        'label' => 'Show results based on the location, saved in user’s browser cookie.',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0
                    )
                ),                                   
                array(
                    'Text',
                    'sitemenu_mini_search_width',
                    array(
                        'label' => 'Enter width for searchbox.',
                        'value' => 275,
                    )
                ),
                $element_show_suggestion,
                array(
                    'Radio',
                    'sitemenu_enable_login_lightbox',
                    array(
                        'label' => 'Enabled lightbox for Sign In?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'Value' => 1
                    )
                ),
                array(
                    'Radio',
                    'sitemenu_enable_signup_lightbox',
                    array(
                        'label' => 'Enabled lightbox for Sign Up?',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'Value' => 1
                    )
                ),
            )
        )
    )
);