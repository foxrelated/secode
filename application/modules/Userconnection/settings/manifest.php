<?php 
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    User Connection
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 2010-07-27 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

return array(

  // Package -------------------------------------------------------------------
  
  'package' => array(
    'type' => 'module',
    'name' => 'userconnection',
    'version' => '4.8.10',
    'path' => 'application/modules/Userconnection',
    'repository' => 'null',
		'title' => 'Userconnection',
		'description' => 'The User Connections plugin shows to users the web of their Social Graph, their shortest connection paths with other users and also how users know each other.',
'author' => '<a href="http://www.socialengineaddons.com" style="text-decoration:underline;" target="_blank">SocialEngineAddOns</a>',
		'date' => 'Friday, 27 Jul 2010 18:33:08 +0000',
		'copyright' => 'Copyright 2009-2010 BigStep Technologies Pvt. Ltd.',
    'actions' => array(
    	'install',
      'upgrade',
      'refresh',
      'enable',
      'disable',
    ),
    'callback' => array(
      'path' => 'application/modules/Userconnection/settings/install.php',
      'class' => 'Userconnection_Installer',
    ),
    'directories' => array(
    	'application/modules/Userconnection',
    ),
    'files' => array(
      'application/languages/en/userconnection.csv',
    ),
  ),
   
  // Hooks ---------------------------------------------------------------------
  
  'hooks' => array(
    array(
    	'event' => 'onRenderLayoutDefault',
      'resource' => 'Userconnection_Plugin_Core',
    ),
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'Userconnection_Plugin_Core',
    )
  ),
  
  // Items ---------------------------------------------------------------------
  
  'items' => array(
    'userconnection', 'content'
  ),
  
  // Routes --------------------------------------------------------------------
  
  'routes' => array(
    
  'secondlevelfriends' => array(
  	'route' => 'userconnection/index/secondlevelfriends',
  	'module' => 'Userconnection',
        'controller' => 'index',
        'action' => 'view',
  ),
   'thirdlevelfriends' => array(
  	'route' => 'userconnection/index/thirdlevelfriends',
  	'module' => 'Userconnection',
        'controller' => 'index',
        'action' => 'view',
  ),

   'connection' => array(
  	'route' => 'userconnection/index/index',
  	'module' => 'Userconnection',
        'controller' => 'index'
  ), 

	  'userconnection_friend_profile' => array(
	  'route' => 'profile/:id/*',
	  'defaults' => array(
	    'module' => 'user',
	    'controller' => 'profile',
	    'action' => 'index'
	  )
	),

	 'userconnection_invite' => array(
		'route' => 'invite/',
		'module' => 'invite',
	      'controller' => 'index',
	      'action' => 'index',
			),
  )
)?>
