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
	'name' => 'Suggestion.suggestion-friend',
	'defaultParams' => array(
		'title' => $view->translate('People you may know')
	),
	'adminForm' => array(
		'elements' => array(
			array(
				'Radio',
				'getLayout',
				array(
					'label' => 'Set Layout Position',
					'multiOptions' => array(
						'1' => 'Middle',
						'0' => 'Left/Right',
					),
					'value' => 0,
				)
			),
			array(
				'Radio',
				'getWidAjaxEnabled',
				array(
					'label' => 'Widget Content Loading',
					'description' => 'Do you want the content of this widget to be loaded via AJAX, after the loading of main webpage content? (Enabling this can improve webpage loading speed. Disabling this would load content of this widget along with the page content.)',
					'multiOptions' => array(
						'1' => 'Yes',
						'0' => 'No',
					),
					'value' => 1,
				)
			),
			array(
				'Text',
				'getWidLimit',
				array(
					'label' => 'Friend Suggestions Widget',
					'description' => 'How many suggestions do you want to display in the Friend Suggestions widget ?',
					'value' => 3
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
	'name' => 'Suggestion.suggestion-mix',
	'defaultParams' => array(
		'title' => $view->translate('Recommendations')
	)
);

$description = '';
if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
  $description = 'Note: Ajax will not work for "Welcome Tab" widgetized page.';
}
$contentSuggWidgets[] = array(
	'title' => $view->translate('Explore Suggestions'),
	'description' => $view->translate('This widget shows the Mixed Suggestions to a user. The suggestions shown in this widget are a mix of the various suggestion types. You may configure settings for this widget from the Mixed Suggestions tab of the Suggestions section of Admin Panel. The primary place for this widget is at the left side of Explore Suggestions Page.'),
	'category' => $view->translate('Suggestions'),
	'type' => 'widget',
	'isPaginated' => true,
	'name' => 'Suggestion.explore-friend',
	'defaultParams' => array(
		'title' => $view->translate('Explore Suggestions'),
		'itemCountPerPage' => 30
	),
	'adminForm' => array(
		'elements' => array(
			array(
				'Radio',
				'isAjaxEnabled',
				array(
					'label' => 'AJAX based widgets',
					'description' => $description,
					'multiOptions' => array(
						'1' => 'Yes',
						'0' => 'No',
					),
					'value' => 1,
				)
			),
		)
	),
);


$contentSuggWidgets[] = array(
	'title' => 'Invite Friends',
	'description' => $view->translate('This widget enables users to invite their contacts to become members of the site and their friends on it, by importing contacts from various services or manually entering email IDs. Those who are already site members get friend request. Users can thus quickly and easily grow their network on your site.'),
	'category' => $view->translate('Suggestions'),
	'type' => 'widget',
	'name' => 'suggestion.suggestion-invites',
	'defaultParams' => array(
		'title' => ''
	),
	'requirements' => array(
		'subject',
	),
);

$mixSettingsResults = Engine_Api::_()->getDbtable('modinfos', 'suggestion')->getModContent(array('modinfo_id', 'module', 'item_title', 'enabled'));
$getModArray = array();
if (!empty($mixSettingsResults)) {
  foreach ($mixSettingsResults as $modName) {
    if( !empty($modName['item_title']) && !empty($modName['enabled']) ) {
      $tempKey = $modName['module'];
      if( strstr($tempKey, "sitereview") ) {
        $tempKey = $tempKey . '_' . $modName['modinfo_id'];
      }
      if(!in_array($tempKey, array("messagefriend", "friendfewfriend", "friendphoto", "friend", "photo"))){
        $isModEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($modName['module']);
        if(empty($isModEnabled))
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
				'Radio',
				'getWidAjaxEnabled',
				array(
					'label' => 'AJAX based widgets',
					'multiOptions' => array(
						'1' => 'Yes',
						'0' => 'No',
					),
					'value' => 1,
				)
			),
			array(
				'Text',
				'getWidLimit',
				array(
					'label' => 'Display Content Limit',
					'value' => 5
				)
			)
		),
	)
);



$contentSuggWidgets[] = array(
	'title' => $view->translate('Suggest to Friend Link'),
	'description' => $view->translate('Displays ‘Suggest to Friend’ link on the widgetized view page of the integrated plugin.'),
	'category' => $view->translate('Suggestions'),
	'type' => 'widget',
	'name' => 'Suggestion.suggestion-link',
);

$contentSuggWidgets[] = array(
	'title' => $view->translate('Find More Friends'),
	'description' => $view->translate("This widget enables users to find their friends on the site and invite their contacts to become members of the site. This widget should be placed on Member Home Page."),
	'category' => $view->translate('Suggestions'),
	'type' => 'widget',
	'name' => 'Suggestion.find-morefriends',
	'defaultParams' => array(
		'title' => 'Find More Friends'
	)
);
return $contentSuggWidgets;
?>
