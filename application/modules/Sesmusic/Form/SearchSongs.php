<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: SearchSongs.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmusic_Form_SearchSongs extends Engine_Form {

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
            ->where('name = ?', 'sesmusic.songs-browse-search')
            ->query()
            ->fetchColumn();
    $params = Zend_Json_Decoder::decode($params);

    $this->setAttribs(array(
                'id' => 'filter_form',
                'class' => 'global_form_box',
            ))
            ->setMethod('GET');

    if ($module == 'sesmusic' && $controller == 'song' && $action == 'browse') {
      $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
    } else {
      $this->setAction($view->url(array('module' => 'sesmusic', 'controller' => 'song', 'action' => 'browse'), 'default', true));
    }

    parent::init();

    if (!empty($params['searchOptionsType']) && in_array('searchBox', $params['searchOptionsType'])) {
      $this->addElement('Text', 'title_song', array(
          'label' => 'Search Songs',
          'placeholder' => 'Enter Song Name',
      ));
    }


    if (!empty($params['searchOptionsType']) && in_array('category', $params['searchOptionsType'])) {
      //Category Work
      $categories = Engine_Api::_()->getDbtable('categories', 'sesmusic')->getCategory(array('column_name' => '*', 'param' => 'song'));
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
    }

    if (!empty($params['searchOptionsType']) && in_array('show', $params['searchOptionsType'])) {
      $this->addElement('Select', 'popularity', array(
          'label' => 'List By',
          'multiOptions' => array(
              '' => 'Select Popularity',
              'creation_date' => 'Most Recent',
              'like_count' => 'Most Liked',
              'view_count' => 'Most Viewed',
              'comment_count' => 'Most Commented',
              'download_count' => 'Most Downloaded',
              'favourite_count' => 'Most Favorite',
              'play_count' => 'Most Played',
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