<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Mcard
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 2010-10-13 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

return array(
    'package' =>
    array(
        'type' => 'module',
        'name' => 'mcard',
        'version' => '4.8.10',
        'path' => 'application/modules/Mcard',
				'title' => 'Membership Card',
				'description' => 'Create a plugin for Social Engine that gives members nice looking Membership Cards',
'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
				'date' => 'Friday, 09 Jul 2010 18:33:08 +0000',
				'copyright' => 'Copyright 2009-2010 BigStep Technologies Pvt. Ltd.',
        'callback' =>
        array(
            'class' => 'Engine_Package_Installer_Module',
        ),
        'actions' =>
        array(
            0 => 'install',
            1 => 'upgrade',
            2 => 'refresh',
            3 => 'enable',
            4 => 'disable',
        ),
        'callback' => array(
      'path' => 'application/modules/Mcard/settings/install.php',
      'class' => 'Mcard_Installer',
    ),
    'directories' => array(
      'application/modules/Mcard',
    ),
    'files' => array(
      'application/languages/en/mcard.csv',
    ),
    ),
    'sitemobile_compatible' =>true,
    // Items ---------------------------------------------------------------------
    'items' => array(
        'mcard_option',
        'mcard_meta',
        'mcard_map',
        'mcard_value',
        'mcard_info'
    ),
    // Routes --------------------------------------------------------------------
    'routes' => array(
        'mcard_admin_main_level' => array(
            'route' => 'admin/mcard/level/:level_id/:mptype_id',
            'defaults' => array(
                'module' => 'mcard',
                'controller' => 'admin-level',
                'action' => 'index',
                'level_id' => 1,
                'mptype_id' => 1
            )
        ),
    )
);
?>