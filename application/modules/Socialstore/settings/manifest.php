<?php 
$route = "socialstore";
$module='';
$controller='';
$action='';
$request = Zend_Controller_Front::getInstance()->getRequest();
if (!empty($request)) {
 	$module = $request->getModuleName(); 
 	$action = $request->getActionName();
	$controller = $request->getControllerName();
}
if (empty($request) || !($module == "default" && $controller == "sdk" && $action == "build")) {
	$route = Engine_Api::_()->getApi('settings', 'core')->getSetting('store.pathname', "socialstore");
}
?>
<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'socialstore',
    'version' => '4.03p4',
    'path' => 'application/modules/Socialstore',
    'title' => 'YN - Store',
    'description' => 'Store Description.',
    'author' => '<a href="http://socialengine.younetco.com/" title="YouNet Company" target="_blank">YouNet Company</a>',
    'callback' => 
    array (
      'path' => 'application/modules/Socialstore/settings/install.php',
      'class' => 'Socialstore_Installer',
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
      0 => 'application/modules/Socialstore',
      1 => 'application/libraries/Html2Pdf',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/socialstore.csv',
    ),
    'dependencies' => 
    array (
      0 => 
      array (
        'type' => 'module',
        'name' => 'younet-core',
        'minVersion' => '4.02p1',
      ),
    ),
  ),
  'hooks' => 
  array (
    0 => 
    array (
      'event' => 'onUserLoginAfter',      
      'resource' => 'Socialstore_Plugin_User',
    ),
    1 => 
    array (
      'event' => 'onUserLogoutBefore',      
      'resource' => 'Socialstore_Plugin_User',
    ),
  ),
  'items' => 
  array (
    0 => 'social_store',
    1 => 'social_product',
    2 => 'socialstore_location',
    3 => 'social_template',
    4 => 'socialstore_store_album',
    5 => 'socialstore_store_photo',
    6 => 'socialstore_product_album',
    7 => 'socialstore_product_photo',
    8 => 'socialstore_gdarequest',
  ),
  'routes' => 
  array (
    'socialstore_detail' => 
    array (
      'route' => $route.'/detail/:store_id/:slug/*',
      'defaults' => 
      array (
        'module' => 'socialstore',
        'controller' => 'index',
        'action' => 'detail',
      ),
      'reqs' => 
      array (
        'store_id' => '\\d+',
      ),
    ),
    'socialstore_front' => 
    array (
      'route' => $route.'/store-front/:store_id/:slug/*',
      'defaults' => 
      array (
        'module' => 'socialstore',
        'controller' => 'store',
        'action' => 'front',
      ),
      'reqs' => 
      array (
        'store_id' => '\\d+',
      ),
    ),
    'socialproduct_detail' => 
    array (
      'route' => 'product/:product_id/:slug/*',
      'defaults' => 
      array (
        'module' => 'socialstore',
        'controller' => 'product',
        'action' => 'detail',
      ),
      'reqs' => 
      array (
        'product_id' => '\\d+',
      ),
    ),
    'socialstore_discount' =>
    array (
    	'route' => $route.'/product-discount/:action/*',
    	'defaults' =>
    	array (
    		'module' => 'socialstore',
    		'controller' => 'product-discount',
    		'action' => 'index',
    	),
    ),
    'socialstore_extended' => 
    array (
      'route' => $route.'/:controller/:action/*',
      'defaults' => 
      array (
        'module' => 'socialstore',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => 
      array (
      	'controller' => '\D+',
        'action' => '\D+',
      ),
    ),
    'socialstore_general' => 
    array (
      'route' => $route.'/:action/*',
      'defaults' => 
      array (
        'module' => 'socialstore',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => 
      array (
        'action' => '(index|listing)',
      ),
    ),
    'socialstore_mystore_general' => 
    array (
      'route' => $route.'/my-store/:action/*',
      'defaults' => 
      array (
        'module' => 'socialstore',
        'controller' => 'my-store',
        'action' => 'index',
      ),
    ),
    'socialstore_store_general' => 
    array (
      'route' => $route.'/store/:action/*',
      'defaults' => 
      array (
        'module' => 'socialstore',
        'controller' => 'store',
        'action' => 'index',
      ),
      'reqs' => 
      array (
        'action' => '(index|change-multi-level|rate-store|approve-store|deny-store|delete-store|listing-store|edit-store|edit-product|store-detail)',
      ),
    ),
    'socialstore_product_general' => 
    array (
      'route' => $route.'/product/:action/*',
      'defaults' => 
      array (
        'module' => 'socialstore',
        'controller' => 'product',
        'action' => 'index',
      ),
      'reqs' => 
      array (
        'action' => '(index|rate-product|approve-product|deny-product|delete-product|listing-product|store-list-product|get-adjust-price)',
      ),
    ),
    'socialstore_help' => 
    array (
      'route' => $route.'/help/*',
      'defaults' => 
      array (
        'module' => 'socialstore',
        'controller' => 'help',
        'action' => 'detail',
      ),
    ),
    'socialstore_cart' => 
    array (
      'route' => $route.'/my-cart/:action/*',
      'defaults' => 
      array (
        'module' => 'socialstore',
        'controller' => 'my-cart',
        'action' => 'index',
      ),
    ),
    'socialstore_click' => 
    array (
      'route' => 'ss/:href/*',
      'defaults' => 
      array (
        'module' => 'socialstore',
        'controller' => 'index',
        'action' => 'click',
        'href' => '',
      ),
    ),
  ),
) ; ?>