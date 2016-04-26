<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Edit.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmusic_Form_Edit extends Sesmusic_Form_Create {

  public function init() {

    $album_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('album_id');
    if ($album_id)
      $album = Engine_Api::_()->getItem('sesmusic_album', $album_id);

    parent::init();

    $this->setAttrib('id', 'form-upload-music')
            ->setAttrib('name', 'playlist_edit')
            ->setAttrib('enctype', 'multipart/form-data')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

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
          'registerInArrayValidator' => false,
      ));

      //Subcategory
      $subcat = array();
      $subcategory = Engine_Api::_()->getDbtable('categories', 'sesmusic')->getModuleSubcategory(array('column_name' => "*", 'category_id' => $album->category_id, 'param' => 'album'));
      $count_subcat = count($subcategory->toarray());

      $subcat[] = "Select 2nd-level Category";
      foreach ($subcategory as $subcategory) {
        $subcat[$subcategory['category_id']] = $subcategory['category_name'];
      }

      //Add Element: Sub Category
      $this->addElement('Select', 'subcat_id', array(
          'label' => '2nd-level Category',
          'allowEmpty' => true,
          'required' => false,
          'multiOptions' => $subcat,
          'registerInArrayValidator' => false,
          'onchange' => "sessubsubcat_category(this.value)"
      ));
      if (!empty($album->subcat_id)) {
        $this->subcat_id->setValue($album->subcat_id);
      }

      //SubSubcategory
      $subsubcat = array();
      $subsubcategory = Engine_Api::_()->getDbtable('categories', 'sesmusic')->getModuleSubsubcategory(array('column_name' => "*", 'category_id' => $album->subcat_id, 'param' => 'album'));
      $count_subcat = count($subsubcategory->toarray());
      $subsubcat[] = "Select 3rd-level Category";
      foreach ($subsubcategory as $subsubcategory) {
        $subsubcat[$subsubcategory['category_id']] = $subsubcategory['category_name'];
      }
      //Add Element: Sub Sub Category
      $this->addElement('Select', 'subsubcat_id', array(
          'label' => '3rd-level Category',
          'allowEmpty' => true,
          'multiOptions' => $subsubcat,
          'registerInArrayValidator' => false,
          'required' => false,
      ));
      if (!empty($group['subsubcat_id'])) {
        $this->subsubcat_id->setValue($album->subcat_id);
      }
    }

    //Pre-fill form values
    $this->addElement('Hidden', 'album_id');
    $this->removeElement('fancyuploadfileids');

    
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
        'label' => 'Cancel',
        'link' => true,
        'prependText' => ' or ',
        'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'view', 'album_id' => $album_id, 'slug' => $album->getSlug()), 'sesmusic_album_view', true),
        'decorators' => array(
            'ViewHelper'
        )
    ));
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }

  public function populate($album) {

    $this->setTitle('Edit Album');

    foreach (array('album_id' => $album->getIdentity(), 'title' => $album->getTitle(), 'description' => $album->description, 'search' => $album->search) as $key => $value) {
      if ($value)
        $this->getElement($key)->setValue($value);
    }

    //If this is THE profile playlist, hide the title/desc fields
    if ($album->special) {
      $this->removeElement('title');
      $this->removeElement('description');
      $this->removeElement('label');
      $this->removeElement('category_id');
      $this->removeElement('search');
    }

    //AUTHORIZATIONS
    $auth = Engine_Api::_()->authorization()->context;

    $auth_view = $this->getElement('auth_view');
    if ($auth_view) {
      $lowest_viewer = array_pop(array_keys($this->_roles));
      foreach (array_reverse(array_keys($this->_roles)) as $role) {
        if ($auth->isAllowed($album, $role, 'view')) {
          $lowest_viewer = $role;
        }
      }
      $auth_view->setValue($lowest_viewer);
    }

    $auth_comment = $this->getElement('auth_comment');
    if ($auth_comment) {
      $lowest_commenter = array_pop(array_keys($this->_roles));
      foreach (array_reverse(array_keys($this->_roles)) as $role) {
        if ($auth->isAllowed($album, $role, 'comment')) {
          $lowest_commenter = $role;
        }
      }
      $auth_comment->setValue($lowest_commenter);
    }
  }

  public function saveValues() {

    $album = parent::saveValues();
    $values = $this->getValues();
    if ($album && $album->isEditable()) {
      if (!$album->special) {
        $album->title = $values['title'];
        $album->description = $values['description'];
        $album->search = $values['search'];
        if (isset($values['category_id']))
          $album->category_id = $values['category_id'];
        if (isset($values['subcat_id']))
          $album->subcat_id = $values['subcat_id'];
        if (isset($values['subsubcat_id']))
          $album->subsubcat_id = $values['subsubcat_id'];
        if (isset($values['label']))
          $album->label = $values['label'];
      }
      $album->save();

      //Rebuild privacy
      $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
      foreach ($actionTable->getActionsByObject($album) as $action) {
        $actionTable->resetActivityBindings($action);
      }

      return $album;
    } else {
      return false;
    }
  }

}