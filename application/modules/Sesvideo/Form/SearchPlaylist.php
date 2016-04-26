<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: SearchPlaylist.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesvideo_Form_SearchPlaylist extends Engine_Form {

  public function init() {

    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $front = Zend_Controller_Front::getInstance();
    $module = $front->getRequest()->getModuleName();
    $controller = $front->getRequest()->getControllerName();
    $action = $front->getRequest()->getActionName();

    $content_table = Engine_Api::_()->getDbtable('content', 'core');
    $params = $content_table->select()
            ->from($content_table->info('name'), array('params'))
            ->where('name = ?', 'sesvideo.playlist-browse-search')
            ->query()
            ->fetchColumn();
    $params = Zend_Json_Decoder::decode($params);

    $this->setAttribs(array(
                'id' => 'filter_form',
                'class' => 'global_form_box',
            ))
            ->setMethod('GET');

    if ($module == 'sesvideo' && $controller == 'playlist' && $action == 'browse') {
      $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
    } else {
      $this->setAction($view->url(array('module' => 'sesvideo', 'controller' => 'playlist', 'action' => 'browse'), 'default', true));
    }


    parent::init();

    if (!empty($params['searchOptionsType']) && in_array('searchBox', $params['searchOptionsType'])) {
      $this->addElement('Text', 'title_name', array(
          'label' => 'Search Playlist',
          'placeholder' => 'Enter Playlist Name',
      ));
    }

    if (!empty($params['searchOptionsType']) && in_array('view', $params['searchOptionsType'])) {
      $this->addElement('Select', 'show', array(
          'label' => 'View',
          'multiOptions' => array(
              '1' => 'Everyone\'s Playlists',
              '2' => 'Only My Friends\' Playlists',
          ),
      ));
    }

    if (!empty($params['searchOptionsType']) && in_array('show', $params['searchOptionsType'])) {
      $this->addElement('Select', 'popularity', array(
          'label' => 'List By',
          'multiOptions' => array(
              '' => 'Select Popularity',
              'creation_date' => 'Most Recent',
              'featured' => "Only Featured",
							'sponsored' => "Only Sponsored",
              'view_count' => 'Most Viewed',
              'video_count' => 'Most Video Playlist',
              'favourite_count' => 'Most Favorite',
							'like_count' => 'Most Liked',
          ),
      ));
    }
    $this->addElement('Hidden', 'user');

    //Element: execute
    $this->addElement('Button', 'execute', array(
        'label' => 'Search',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array(
            'ViewHelper',
        ),
    ));
  }

}
