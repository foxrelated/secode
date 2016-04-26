<?php
// YouNet Responsive Metro
return array(
  array(
    'title' => 'Mini Menu',
    'description' => 'Shows the site-wide mini menu. You can edit its contents in YouNet Responsive Plugin.',
    'category' => 'YouNet Responsive Metro',
    'type' => 'widget',
    'name' => 'ynresponsivemetro.metro-mini-menu',
    'requirements' => array( 'header-footer'
    ),
  ),
  array(
    'title' => 'Logo & Main Menu',
    'description' => 'Shows the site-wide main menu and (main logo or title). You can edit its contents in YouNet Responsive Plugin. Images are uploaded via the <a href="admin/files" target="_blank">File Media Manager</a>.',
    'category' => 'YouNet Responsive Metro',
    'type' => 'widget',
    'name' => 'ynresponsivemetro.metro-main-menu',
    'adminForm' => 'Ynresponsivemetro_Form_Admin_Metro_LogoMainMenu',
    'requirements' => array( 'header-footer'
    ),
  ),
  array(
    'title' => 'Metro Blocks',
    'description' => 'Shows metro blocks.  You can manage blocks in YouNet Responsive Plugin. Images are uploaded via the <a href="admin/files" target="_blank">File Media Manager</a>.',
    'category' => 'YouNet Responsive Metro',
    'type' => 'widget',
    'name' => 'ynresponsivemetro.metro-blocks',
    'adminForm' => 'Ynresponsivemetro_Form_Admin_Metro_Blocks',
    'requirements' => array(),
  ),
  array(
    'title' => 'Introduction',
    'description' => 'Shows introduction. You can edit introduction block in YouNet Responsive Plugin. Images are uploaded via the <a href="admin/files" target="_blank">File Media Manager',
    'category' => 'YouNet Responsive Metro',
    'type' => 'widget',
    'name' => 'ynresponsivemetro.metro-introduction',
    'adminForm' => 'Ynresponsivemetro_Form_Admin_Metro_Introduction',
    'autoEdit' => true,
    'requirements' => array(),
    'defaultParams' => array(
      'title' => 'What can we do for you?',
    ),
  ),
   array(
    'title' => 'Members ',
    'description' => 'Shows members.',
    'category' => 'YouNet Responsive Metro',
    'type' => 'widget',
    'name' => 'ynresponsivemetro.metro-members',
    'requirements' => array(),
    'defaultParams' => array(
      'title' => '',
      'max' => 6
    ),
    'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title',
          )
        ),
        array(
          'Select',
          'max',
          array(
            'label' => 'Number of members',
            'default' => 6,
            'multiOptions' => array(
              1 => 1,
              2 => 2,
              3 => 3,
              4 => 4,
              5 => 5,
              6 => 6,
            )
          )
        ),
      )
    ),
  ),
  array(
    'title' => 'Featured Albums/Photos',
    'description' => 'Shows albums/photos. You can edit photos in YouNet Responsive Plugin.',
    'category' => 'YouNet Responsive Metro',
    'type' => 'widget',
    'name' => 'ynresponsivemetro.metro-featured-photos',
    'requirements' => array(),
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => '',
      'itemCountPerPage' => 10
    ),
  ),
  array(
    'title' => 'Video/Album Info',
    'description' => 'Shows videos/albums information. Images are uploaded via the <a href="admin/files" target="_blank">File Media Manager</a>. If you do not want to set color in color textbox, please kindly copy `transparent` and paste it into color textbox. Please kindly remove `transparent` before select color.',
    'category' => 'YouNet Responsive Metro',
    'type' => 'widget',
    'name' => 'ynresponsivemetro.metro-va-info',
    'requirements' => array(),
    'adminForm' => 'Ynresponsivemetro_Form_Admin_Metro_VideoAlbumInfo',
  ),
  array(
    'title' => 'Groups',
    'description' => 'Shows random groups.',
    'category' => 'YouNet Responsive Metro',
    'type' => 'widget',
    'name' => 'ynresponsivemetro.metro-groups',
    'requirements' => array(),
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Groups',
      'itemCountPerPage' => 3
    ),
  ),
  array(
    'title' => 'Blogs',
    'description' => 'Shows random blogs.',
    'category' => 'YouNet Responsive Metro',
    'type' => 'widget',
    'name' => 'ynresponsivemetro.metro-blogs',
    'requirements' => array(),
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Blogs',
      'itemCountPerPage' => 4
    ),
  ),
  array(
    'title' => 'Events',
    'description' => 'Shows random events.',
    'category' => 'YouNet Responsive Metro',
    'type' => 'widget',
    'name' => 'ynresponsivemetro.metro-events',
    'requirements' => array(),
    'defaultParams' => array(
      'title' => 'Events',
      'itemCountPerPage' => 5
    ),
  ),
  array(
    'title' => 'Footer Menu',
    'description' => 'Shows footer menu.',
    'category' => 'YouNet Responsive Metro',
    'type' => 'widget',
    'name' => 'ynresponsivemetro.metro-footer-menu',
    'requirements' => array(),
  ),
);
