<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Seshtmlbackground
 * @package    Seshtmlbackground
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: manifest.php 2015-10-22 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
 return array (
    'package' => array(
        'type' => 'module',
        'name' => 'seshtmlbackground',
        'version' => '4.8.9p6',
        'path' => 'application/modules/Seshtmlbackground',
        'title' => 'HTML5 Videos & Photos Background Plugin',
        'description' => 'HTML5 Videos & Photos Background Plugin',
        'author' => '<a href="http://www.socialenginesolutions.com" style="text-decoration:underline;" target="_blank">SocialEngineSolutions</a>',
        'actions' => array(
            'install',
            'upgrade',
            'refresh',
            'enable',
            'disable',
        ),
        'callback' => array(
            'path' => 'application/modules/Seshtmlbackground/settings/install.php',
            'class' => 'Seshtmlbackground_Installer',
        ),
        'directories' => array(
            'application/modules/Seshtmlbackground',
        ),
        'files' => array(
            'application/languages/en/seshtmlbackground.csv',
        ),
    ),
		// Items ---------------------------------------------------------------------
    'items' => array(
        'seshtmlbackground_slide',
				'seshtmlbackground_gallery',
    ),	
); ?>