<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'ynidea',
    'version' => '4.03p3',
    'path' => 'application/modules/Ynidea',
    'title' => 'YN - Ideas Box',
    'description' => 'Ideas Box',
    'author' => '<a href="http://socialengine.younetco.com/" title="YouNet Company" target="_blank">YouNet Company</a>',
    'dependencies' => array(
      array(
         'type' => 'module',
         'name' => 'younet-core',
         'minVersion' => '4.02',
      ),
    ),
    'callback' => 
    array (
      'path' => 'application/modules/Ynidea/settings/install.php',
      'class' => 'Ynidea_Installer',
    ),
    'actions' => 
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'enable',
      4 => 'disable',
    ),
    'directories' => 
    array (
      0 => 'application/modules/Ynidea',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/ynidea.csv',
    ),
  ),
  // Items ---------------------------------------------------------------------
    'items' => array(
        'ynidea_idea',
        'ynidea_ideavote',
        'ynidea_version',
        'ynidea_trophy',
        'ynidea_trophyvote',
        'ynidea_nominee',        
        'ynidea_judge',
        'ynidea_coauthor',
        'ynidea_report',
        'ynidea_category',        
    ),
  // Routes --------------------------------------------------------------------
  'routes' => array(
    // User - General
    'ynidea_extended' => array(
      'route' => 'ideas/:controller/:action/*',
      'defaults' => array(
        'module' => 'ynidea',
        'controller' => 'index',
        'action' => 'index'
      ),
      'reqs' => array(
        'controller' => '\D+',
        'action' => '\D+',
      )
    ),
    'ynidea_general' => array(
      'route' => 'ideas/:action/*',
      'defaults' => array(
        'module' => 'ynidea',
        'controller' => 'index',
        'action' => 'index'
      ),
      'reqs' => array(
        'action' => '(index|create|detail|favourite|un-favourite|assign|download-pdf|report|give-award-ajax|upload-photo)',
      )
    ),
    'ynidea_trophies' => array(
      'route' => 'trophies/:action/*',
      'defaults' => array(
        'module' => 'ynidea',
        'controller' => 'trophies',
        'action' => 'index'
      ),
      'reqs' => array(
        'action' => '(index|create|edit|delete|detail|assign|favourite|un-favourite|judge-vote|download-pdf|judge|delete-judge|manage-nominees|add-nominees|reset-votes|ajax|upload-photo|suggest-ideas|remove-nominee)',
      )
    ),    
    'ynidea_myideas' => array(
      'route' => 'my-ideas/:action/*',
      'defaults' => array(
        'module' => 'ynidea',
        'controller' => 'my-ideas',
        'action' => 'index'
      ),
      'reqs' => array(
      )
    ),    
    'ynidea_mytrophies' => array(
      'route' => 'my-trophies/:action/*',
      'defaults' => array(
        'module' => 'ynidea',
        'controller' => 'my-trophies',
        'action' => 'index'
      ),
      'reqs' => array(
      )
    ),  
    'ynidea_viewallideas' => array(
      'route' => 'ideas/view-all/*',
      'defaults' => array(
        'module' => 'ynidea',
        'controller' => 'ideas',
        'action' => 'view-all'
      ),
      'reqs' => array(
      )
    ),    
    
    // User - Specific
    'ynidea_specific' => array(
      'route' => 'ideas/:action/:id/*',
      'defaults' => array(
        'module' => 'ynidea',
        'controller' => 'index',
        'action' => 'index'
      ),
      'reqs' => array(
        'id'=>'\d+',
        'action' => '\D+',
      )
    ),
  )
); ?>