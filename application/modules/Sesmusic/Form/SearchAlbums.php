<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: SearchAlbums.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmusic_Form_SearchAlbums extends Engine_Form {

  public function init() {

    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $front = Zend_Controller_Front::getInstance();
    $module = $front->getRequest()->getModuleName();
    $controller = $front->getRequest()->getControllerName();
    $action = $front->getRequest()->getActionName();

    $content_table = Engine_Api::_()->getDbtable('content', 'core');
    $params = $content_table->select()
            ->from($content_table->info('name'), array('params'))
            //->where('page_id = ?', $id)
            ->where('name = ?', 'sesmusic.browse-search')
            ->query()
            ->fetchColumn();
    $params = Zend_Json_Decoder::decode($params);

    $this->setAttribs(array(
                'id' => 'filter_form',
                'class' => 'global_form_box',
            ))
            ->setMethod('GET');

    if ($module == 'sesmusic' && $controller == 'index' && $action == 'browse') {
      $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
    } else {
      $this->setAction($view->url(array('module' => 'sesmusic', 'controller' => 'index', 'action' => 'browse'), 'default', true));
    }


    parent::init();

    if (!empty($params['searchOptionsType']) && in_array('searchBox', $params['searchOptionsType'])) {
      $this->addElement('Text', 'title_name', array(
          'label' => 'Search Music Album',
          'placeholder' => 'Enter Album Name',
          
      ));
    }



    if (!empty($params['searchOptionsType']) && in_array('category', $params['searchOptionsType'])) {

      //Category Work
      $categories = Engine_Api::_()->getDbtable('categories', 'sesmusic')->getCategory(array('column_name' => '*', 'param' => 'album'));
      $data[] = 'Select Category';
      foreach ($categories as $category) {
        $data[$category['category_id']] = $category['category_name'];
      }
      if (count($data) > 1) {

        //Add Element: Category
        $this->addElement('Select', 'category_id', array(
            'label' => 'Category',
            'multiOptions' => $data,
            'onchange' => "ses_subcategory(this.value)",
        ));

        //Add Element: Sub Category
        $this->addElement('Select', 'subcat_id', array(
            'label' => "2nd-level Category",
            'allowEmpty' => true,
            'required' => false,
            'registerInArrayValidator' => false,
            'onchange' => "sessubsubcat_category(this.value)"
        ));

        //Add Element: Sub Sub Category
        $this->addElement('Select', 'subsubcat_id', array(
            'label' => "3rd-level Category",
            'allowEmpty' => true,
            'registerInArrayValidator' => false,
            'required' => false,
        ));
      }

      $this->addElement('Hidden', 'search_params', array(
          'order' => 200
      ));
    }


    if (!empty($params['searchOptionsType']) && in_array('view', $params['searchOptionsType'])) {
      $this->addElement('Select', 'show', array(
          'label' => 'View',
          'multiOptions' => array(
              '1' => 'Everyone\'s Music Albums',
              '2' => 'Only My Friends\' Music Albums',
          ),
      ));
    }

    if (!empty($params['searchOptionsType']) && in_array('show', $params['searchOptionsType'])) {

      $this->addElement('Select', 'popularity', array(
          'label' => 'List By',
          'multiOptions' => array(
              '' => 'Select Popularity',
              'creation_date' => 'Most Recent',
              'upcoming' => 'Latest',
              'comment_count' => 'Most Commented',
              'like_count' => 'Most Liked',
              'view_count' => 'Most Viewed',
              'song_count' => 'Most Song Albums',
              'favourite_count' => 'Most Favorite',
              'rating' => 'Most Rated',
          ),
      ));
    }
    $this->addElement('Hidden', 'user');

    if (!empty($params['searchOptionsType']) && in_array('artists', $params['searchOptionsType'])) {

      $artistArray = array('' => 'Select Artist');
      $artistsTable = Engine_Api::_()->getDbTable('artists', 'sesmusic');
      $select = $artistsTable->select()->order('order ASC');
      $artists = $artistsTable->fetchAll($select);
      foreach ($artists as $artist) {
        $artistArray[$artist->artist_id] = $artist->name;
      }
      if (count($artistArray) > 1) {
        $this->addElement('Select', 'artists', array(
            'label' => 'By Artists',
            'multiOptions' => $artistArray,
        ));
      }
    }


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