<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

$contentSuggWidgets[] = array(
    'title' => $view->translate('People you may know'),
    'description' => $view->translate('This widget shows the Friend Suggestions.'),
    'category' => $view->translate('Suggestions'),
    'type' => 'widget',
    'autoEdit' => true,
    'name' => 'suggestion.suggestion-friend',
    'defaultParams' => array(
        'title' => $view->translate('People you may know')
    ),
    'adminForm' => array(
        'elements' => array(
            array(
                'Text',
                'getWidLimit',
                array(
                    'label' => 'Friend Suggestions Widget',
                    'description' => 'How many suggestions do you want to display in the Friend Suggestions Widget ?',
                    'value' => 3
                )
            ),
            array(
                'Text',
                'friendMaxLimit',
                array(
                    'label' => 'Maximum Limit Of Friends.',
                    'description' => 'Suggestions will be displayed on Member home page if user has friends less than or equal to the "Maximum limit of friends".',
                    'default' => '100',
                    'value' => '100'
                )
            ),
            array(
                'Radio',
                'suggestionView',
                array(
                    'label' => $view->translate('Select the display type for Suggestions.'),
                    'multiOptions' => array(
                        'list' => $view->translate('List View'),
                        'grid' => $view->translate('Grid View'),
                    ),
                    'default' => 'list',
                    'value' => 'list',
                )
            ),
            array(
                'Radio',
                'carouselView',
                array(
                    'label' => $view->translate('Do you want Carousel View (Sliding effect) for Suggestions ? (Note: This Carousel View can work only when you select Grid View.)'),
                    'multiOptions' => array(
                        '1' => $view->translate('Yes'),
                        '0' => $view->translate('No'),
                    ),
                    'default' => '0',
                    'value' => '0',
                )
            )
        ),
    ),
);

$contentSuggWidgets[] = array(
    'title' => $view->translate('Recommendations'),
    'description' => $view->translate('The suggestions shown in this widget are a mix of the various suggestion types. You may configure settings for this widget from the Mixed Suggestions tab of the Suggestions section of Admin Panel.'),
    'category' => $view->translate('Suggestions'),
    'type' => 'widget',
    'name' => 'suggestion.suggestion-mix',
    'defaultParams' => array(
        'title' => $view->translate('Recommendations')
    ),
    'adminForm' => array(
        'elements' => array(
            array(
                'Text',
                'getWidLimit',
                array(
                    'label' => 'Display Content Limit',
                    'value' => 3
                )
            ),
            array(
                'Radio',
                'recommendationView',
                array(
                    'label' => $view->translate('Select the display type for Recommendations.'),
                    'multiOptions' => array(
                        'list' => $view->translate('List View'),
                        'grid' => $view->translate('Grid View'),
                    ),
                    'default' => 'list',
                    'value' => 'list',
                )
            ),
             array(
                'Radio',
                'carouselView',
                array(
                    'label' => $view->translate('Do you want Carousel View (Sliding effect) for Recommendations ? (Note: This Carousel View can work only when you select Grid View.)'),
                    'multiOptions' => array(
                        '1' => $view->translate('Yes'),
                        '0' => $view->translate('No'),
                    ),
                    'default' => '0',
                    'value' => '0',
                )
            )
        ),
    )
);

$mixSettingsResults = Engine_Api::_()->getDbtable('modinfos', 'suggestion')->getModContent(array('modinfo_id', 'module', 'item_title', 'enabled'));
$getModArray = array();
if (!empty($mixSettingsResults)) {
    foreach ($mixSettingsResults as $modName) {
        //We dont want Friend type selected content.
        if (!empty($modName['item_title']) && !empty($modName['enabled']) && $modName['item_title'] != 'Friend') {
            $tempKey = $modName['module'];
            if (strstr($tempKey, "sitereview")) {
                $tempKey = $tempKey . '_' . $modName['modinfo_id'];
            }
            if (!in_array($tempKey, array("messagefriend", "friendfewfriend", "friendphoto", "friend", "photo"))) {
                $isModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($modName['module']);
                if (empty($isModEnabled))
                    continue;
            }
            $getModArray[$tempKey] = $modName['item_title'];
        }
    }
}
$contentSuggWidgets[] = array(
    'title' => $view->translate('Recommendations (selected content)'),
    'description' => $view->translate('Displays suggestions for the content type that you select for this widget. The content types available are the ones enabled from the "Manage Modules" section of Suggestions / Recommendations Plugin. You can place this widget multiple times on a page with different content type chosen for each placement.'),
    'category' => $view->translate('Suggestions'),
    'type' => 'widget',
    'autoEdit' => true,
    'name' => 'suggestion.common-suggestion',
    'defaultParams' => array(
        'title' => $view->translate('Recommendations (selected content)'),
    ),
    'adminForm' => array(
        'elements' => array(
            array(
                'select',
                'resource_type',
                array(
                    'label' => $view->translate('Select the content'),
                    'multiOptions' => $getModArray,
                )
            ),
            array(
                'Text',
                'getWidLimit',
                array(
                    'label' => 'Display Content Limit',
                    'value' => 3
                )
            ),
            array(
                'Radio',
                'recommendationView',
                array(
                    'label' => $view->translate('Select the display type for Recommendations.'),
                    'multiOptions' => array(
                        'list' => $view->translate('List View'),
                        'grid' => $view->translate('Grid View'),
                    ),
                    'default' => 'list',
                    'value' => 'list',
                )
            ),
             array(
                'Radio',
                'carouselView',
                array(
                    'label' => $view->translate('Do you want Carousel View (Sliding effect) for Recommendations ? ( Note: This Carousel View can work only when you select Grid View.)'),
                    'multiOptions' => array(
                        '1' => $view->translate('Yes'),
                        '0' => $view->translate('No'),
                    ),
                    'default' => '0',
                    'value' => '0',
                )
            )
        ),
    )
);

$contentSuggWidgets[] = array(
    'title' => $view->translate('Requests'),
    'description' => $view->translate('This widget display the friend requests.'),
    'category' => $view->translate('Suggestions'),
    'type' => 'widget',
    'name' => 'suggestion.sitemobile-suggestion-request',
    'defaultParams' => array(
    //'title' => $view->translate('Requests')
    )
);

return $contentSuggWidgets;
?>
