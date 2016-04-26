<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedslideshow
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 2011-10-22 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
return array(
    // Package -------------------------------------------------------------------
    'package' => array(
        'type' => 'module',
        'name' => 'advancedslideshow',
        'version' => '4.8.6',
        'path' => 'application/modules/Advancedslideshow',
        'repository' => 'null',
        'title' => 'Advancedslideshow',
        'description' => 'Advanced Slideshow Plugin enables you to introduce your site to users and create lasting impressions through attractive slideshow. This easy to create slideshow can be created from multiple available slideshow types and effects. It has numerous advantages like: multiple configuration options, SEO, easy navigation, light-weight, and more!',
'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
        'date' => 'Tuesday, 1 Nov 2011 18:33:08 +0000',
        'copyright' => 'Copyright 2009-2010 BigStep Technologies Pvt. Ltd.',
        'actions' => array(
            'install',
            'upgrade',
            'refresh',
            'enable',
            'disable',
        ),
        'callback' => array(
            'path' => 'application/modules/Advancedslideshow/settings/install.php',
            'class' => 'Advancedslideshow_Installer',
        ),
        'directories' => array(
            'application/modules/Advancedslideshow',
        ),
        'files' => array(
            'application/languages/en/advancedslideshow.csv',
        ),
    ),
		// Hooks ---------------------------------------------------------------------
		'hooks' => array(
			array(
				'event' => 'onRenderLayoutDefault',
				'resource' => 'Advancedslideshow_Plugin_Core',
			),
		),
    // Items ---------------------------------------------------------------------
    'items' => array(
        'advancedslideshow',
        'advancedslideshow_image',
    ),
    // Routes --------------------------------------------------------------------

    'routes' => array(

    )
)
?>
