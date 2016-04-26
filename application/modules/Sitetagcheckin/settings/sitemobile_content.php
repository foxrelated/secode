<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: sitemobile_content.php 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
return array(
    array(
        'title' => $view->translate('User: Map & Location Feeds'),
        'description' => $view->translate('Displays on user profile a Map with location markers for the various check-ins, geo-tagging and other location related actions done by users and their friends. It also shows the related activity feeds. Map markers are placed in an aggregated manner. Clicking on any map-marker shows its respective feed and content in attractive tooltip. Users can switch between viewing map and feeds. This widget should be placed on the Member Profile page in the Tab Container.'),
        'category' => $view->translate('Geo-Location, Geo-Tagging, Check-Ins & Proximity Search'),
        'type' => 'widget',
        'name' => 'sitetagcheckin.sitemobile-map-sitetagcheckin',
        'defaultParams' => array(
            'title' => $view->translate('Map'),
            'titleCount' => false,
        ),
        'adminForm' => array(
            'elements' => array(
               array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => $view->translate('Count'),
                        'description' => $view->translate('(number of photos to show in the photo strip which is displayed on clicking the "Add Photos To Map" link.)'),
                        'value' => 7,
												'validators' => array(
													array('Int', true),
													array('GreaterThan', true, array(0)),
												),
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => $view->translate('Content Check-in button & stats'),
        'description' => $view->translate('Displays Check-in button on various content profile / view pages to allow users to check-in into them. Also shows check-in statistics for the content. Highly configurable widget with multiple settings.'),
        'category' => $view->translate('Geo-Location, Geo-Tagging, Check-Ins & Proximity Search'),
        'type' => 'widget',
        'name' => 'sitetagcheckin.sitemobilecheckinbutton-sitetagcheckin',
        'adminForm' => 'Sitetagcheckin_Form_Admin_Widget_SitemobileCheckinButton',
        'defaultParams' => array(
            'title' => $view->translate(''),
            'titleCount' => true,
        ),
        'autoEdit' => true       
    ),
    array(
        'title' => $view->translate('Checked-in Users'),
        'description' => $view->translate('Displays the checked-in users for a content. The value for maximum time for checked-in status in a content can be configured from the Check-Ins tab. Multiple settings are available for this widget. This widget should be placed on the content profile / view pages.'),
        'category' => $view->translate('Geo-Location, Geo-Tagging, Check-Ins & Proximity Search'),
        'type' => 'widget',
        'name' => 'sitetagcheckin.sitemobile-checkinuser-sitetagcheckin',
        'defaultParams' => array(
            'title' => $view->translate(''),
            'titleCount' => true,
        ),
		)
  )
?>