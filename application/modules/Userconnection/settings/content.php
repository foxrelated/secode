<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    User Connection
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Content.php 2010-07-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
return array(
    array(
        'title' => 'Network Span',
        'description' => "Displays the power of logged-in member's Social Graph by showing their Network Connections Span.",
        'category' => 'User Connections',
        'type' => 'widget',
        'name' => 'Userconnection.userhome-userconnection',
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'tempTitle',
                    array(
                        'label' => 'Title',
                        'value' => 'Network Span'
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
                    'Hidden',
                    'title',
                    array('value' => '')
                )
            ),
        ),
    ),
    array(
        'title' => 'Connection Path',
        'description' => 'Displays the Connection Paths and Relationship Types between the logged-in member and the profile owner. (Note: Please enable this widget on the Member Profile page only in the Sidebar or Tabbed Blocks area and choose the respective setting on the Global Settings page for User Connections.)',
        'category' => 'User Connections',
        'type' => 'widget',
        'name' => 'Userconnection.profile-userconnections',
        'defaultParams' => array(
            'title' => 'Connection Path',
        ),
    )
        )
?>