<?php defined("_ENGINE") or die("access denied"); return array (
  'menus' => 
  array (
  ),
  'menuitems' => 
  array (
    0 => 
    array (
      'id' => 310,
      'name' => 'socialpublisher_admin_main_manage',
      'module' => 'socialpublisher',
      'label' => 'Social Publisher',
      'plugin' => '',
      'params' => '{"route":"admin_default","module":"socialpublisher","controller":"manage"}',
      'menu' => 'core_admin_main_plugins',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 999,
    ),
    1 => 
    array (
      'id' => 311,
      'name' => 'socialbridge_main_socialpublishers',
      'module' => 'socialpublisher',
      'label' => 'Social Publisher',
      'plugin' => 'Socialpublisher_Plugin_Menus::showSocialpublisher',
      'params' => '{"route":"socialpublisher_general","controller":"index","action":"settings"}',
      'menu' => 'socialbridge_main',
      'submenu' => '',
      'enabled' => 1,
      'custom' => 0,
      'order' => 2,
    ),
  ),
  'mails' => 
  array (
  ),
  'jobtypes' => 
  array (
  ),
  'notificationtypes' => 
  array (
  ),
  'actiontypes' => 
  array (
  ),
  'permissions' => 
  array (
  ),
  'pages' => 
  array (
    'socialpublisher_index_settings' => 
    array (
      'page_id' => 71,
      'name' => 'socialpublisher_index_settings',
      'displayname' => 'Social Publisher',
      'url' => NULL,
      'title' => 'Social Publisher',
      'description' => 'Social Publisher',
      'keywords' => '',
      'custom' => 1,
      'fragment' => 0,
      'layout' => '',
      'levels' => NULL,
      'provides' => 'no-subject',
      'view_count' => 0,
      'search' => 0,
      'ynchildren' => 
      array (
        0 => 
        array (
          'content_id' => 1170,
          'page_id' => 71,
          'type' => 'container',
          'name' => 'top',
          'parent_content_id' => NULL,
          'order' => 1,
          'params' => '[]',
          'attribs' => NULL,
          'ynchildren' => 
          array (
            0 => 
            array (
              'content_id' => 1171,
              'page_id' => 71,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 1170,
              'order' => 6,
              'params' => '[]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 1172,
                  'page_id' => 71,
                  'type' => 'widget',
                  'name' => 'socialbridge.browse-menu',
                  'parent_content_id' => 1171,
                  'order' => 3,
                  'params' => '[]',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
          ),
        ),
        1 => 
        array (
          'content_id' => 1168,
          'page_id' => 71,
          'type' => 'container',
          'name' => 'main',
          'parent_content_id' => NULL,
          'order' => 2,
          'params' => '[]',
          'attribs' => NULL,
          'ynchildren' => 
          array (
            0 => 
            array (
              'content_id' => 1169,
              'page_id' => 71,
              'type' => 'container',
              'name' => 'middle',
              'parent_content_id' => 1168,
              'order' => 6,
              'params' => '[]',
              'attribs' => NULL,
              'ynchildren' => 
              array (
                0 => 
                array (
                  'content_id' => 1173,
                  'page_id' => 71,
                  'type' => 'widget',
                  'name' => 'core.content',
                  'parent_content_id' => 1169,
                  'order' => 6,
                  'params' => '[]',
                  'attribs' => NULL,
                  'ynchildren' => 
                  array (
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
    ),
  ),
);?>