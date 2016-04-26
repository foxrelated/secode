<?php 
  /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Grouppoll
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: manifest.php 6590 2010-12-08 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
return array(
  // Package -------------------------------------------------------------------
  'package' => array(
    'type' => 'module',
    'name' => 'grouppoll',
    'version' => '4.2.7',
    'path' => 'application/modules/Grouppoll',
		'repository' => 'null',
		'title' => 'Groups Polls Extension Plugin',
		'description' => 'Groups Polls Extension Plugin',
		'author' => 'SocialEngineAddOns',
		'date' => 'Wednesday, 08 December 2010 18:33:08 +0000',
		'copyright' => 'Copyright 2009-2010 BigStep Technologies Pvt. Ltd.',
    'actions' => array(
       'install',
       'upgrade',
       'refresh',
       'enable',
       'disable',
    ),
    'callback' => array(
      'path' => 'application/modules/Grouppoll/settings/install.php',
      'class' => 'Grouppoll_Installer',
    ),
    'directories' => array(
      'application/modules/Grouppoll',
    ),
    'files' => array(
      'application/languages/en/grouppoll.csv',
    ),
  ),
  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'Grouppoll_Plugin_Core',
    ),

		array(
      'event' => 'addActivity',
      'resource' => 'Grouppoll_Plugin_Core'
    ),
   
// 		array(
//       'event' => 'onRenderLayoutDefault',
//       'resource' => 'Grouppoll_Plugin_Core'
//     ),

		array(
      'event' => 'onItemDeleteBefore',
      'resource' => 'Grouppoll_Plugin_Core'
    ),

  ),
  // Items ---------------------------------------------------------------------
  'items' => array(
    'grouppoll_poll',
  ),
  // Routes --------------------------------------------------------------------
  'routes' => array(
    'grouppoll_create' => array(
      'route' => 'grouppolls/create/:group_id',
      'defaults' => array(
        'module' => 'grouppoll',
        'controller' => 'index',
        'action' => 'create'
      ),
			'reqs' => array(
        'group_id' => '\d+'
      )
    ),
    
    'grouppoll_delete' => array(
      'route' => 'grouppolls/delete/:poll_id/:group_id',
      'defaults' => array(
        'module' => 'grouppoll',
        'controller' => 'index',
        'action' => 'delete'
      ),
      'reqs' => array(
        'poll_id' => '\d+',
				'group_id' => '\d+'
      )
    ),
    
    'grouppoll_detail_view' => array(
      'route' => 'grouppolls/:user_id/:poll_id/:slug',
      'defaults' => array(
        'module' => 'grouppoll',
        'controller' => 'index',
        'action' => 'view',
        'slug' => '',
      ),
      'reqs' => array(
        'user_id' => '\d+',
        'poll_id' => '\d+'
      )
    ),
    
    'grouppoll_admin_manage_level' => array(
      'route' => 'admin/grouppoll/level/:id',
      'defaults' => array(
        'module' => 'grouppoll',
        'controller' => 'admin-level',
        'action' => 'index',
        'level_id' => 1
      )
    ), 
   
    'grouppoll_specific' => array(
			'route' => 'grouppolls/close/:poll_id/*',
			'defaults' => array(
					'module' => 'grouppoll',
					'controller' => 'index',
					'action' => 'close',
			),
			'reqs' => array(
					'poll_id' => '\d+'
			)
    ),
  ) 
)
?>
