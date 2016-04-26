<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: AdminCategoriesController.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmusic_AdminSongCategoriesController extends Core_Controller_Action_Admin {

  public function indexAction() {

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesmusic_admin_main', array(), 'sesmusic_admin_main_categories');
    $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesmusic_admin_main_categories', array(), 'sesmusic_admin_main_subsongcategories');

    //Get all categories
    $this->view->categories = Engine_Api::_()->getDbtable('categories', 'sesmusic')->getCategory(array('column_name' => '*', 'param' => 'song'));
  }

  //Add category
  public function addCategoryAction() {

    $this->_helper->layout->setLayout('admin-simple');
    $this->view->category_id = $this->_getParam('category_id');
    $this->view->subcat_id = $this->_getParam('subcat_id');

    //Generate and assign form
    $this->view->form = $form = new Sesmusic_Form_Admin_Category_Add();
    if (empty($this->view->category_id)) {
      $form->setTitle('Add New Category');
      $form->category_name->setLabel('Category Name');
    } elseif ($this->view->category_id && empty($this->view->subcat_id)) {
      $form->setTitle('Add New 2nd-level Category');
      $form->category_name->setLabel('2nd-level Category Name');
    } else {
      $form->setTitle('Add 3rd-level Category');
      $form->category_name->setLabel('3rd-level Category Name');
    }

    //Check post
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      $values = $form->getValues();
      if (empty($values['cat_icon']))
        unset($values['cat_icon']);

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        //Create row in categories table
        $row = Engine_Api::_()->getDbtable('categories', 'sesmusic')->createRow();

        //Subcategory and third level category work
        if ($this->view->category_id && empty($this->view->subcat_id))
          $values['subcat_id'] = $this->view->category_id;
        elseif ($this->view->category_id && $this->view->subcat_id)
          $values['subsubcat_id'] = $this->view->category_id;

        $row->setFromArray($values);
        $row->param = 'song';
        $row->save();

        //Upload categories icon
        if (isset($_FILES['cat_icon'])) {
          $Icon = $this->setPhoto($form->cat_icon, $row->category_id);
          if (!empty($Icon->file_id))
            $row->cat_icon = $Icon->file_id;
        }

        $row->save();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      return $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => 10,
                  'parentRefresh' => 10,
                  'messages' => array('You have successfully create music album category.')
      ));
    }
  }

  //Edit Category
  public function editCategoryAction() {

    $this->_helper->layout->setLayout('admin-simple');
    $this->view->form = $form = new Sesmusic_Form_Admin_Category_Edit();
    $catparam = $this->_getParam('catparam');
    if ($catparam == 'maincat') {
      $form->setTitle('Edit this Category');
      $form->category_name->setLabel('Category Name');
    } elseif ($catparam == 'sub') {
      $form->setTitle('Edit this 2nd-level Category');
      $form->category_name->setLabel('2nd-level Category Name');
    } elseif ($catparam == 'subsub') {
      $form->setTitle('Edit this 3rd-level Category');
      $form->category_name->setLabel('3rd-level Category Name');
    }

    $cat_id = $this->_getParam('id');
    $category = Engine_Api::_()->getItem('sesmusic_categories', $cat_id);
    $form->populate($category->toArray());

    //Check post
    if (!$this->getRequest()->isPost())
      return;

    //Check 
    if (!$form->isValid($this->getRequest()->getPost())) {
      if (empty($_POST['category_name'])) {
        $form->addError($this->view->translate("Category Name * Please complete this field - it is required."));
      }
      return;
    }

    $values = $form->getValues();
    if (empty($values['cat_icon']))
      unset($values['cat_icon']);

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {

      $category->category_name = $values['category_name'];
      $category->save();

      //Upload categories icon
      if (isset($_FILES['cat_icon']) && !empty($_FILES['cat_icon']['name'])) {
        $previousCatIcon = $category->cat_icon;
        $Icon = $this->setPhoto($form->cat_icon, $cat_id);
        if (!empty($Icon->file_id)) {
          if ($previousCatIcon) {
            $catIcon = Engine_Api::_()->getItem('storage_file', $previousCatIcon);
            $catIcon->delete();
          }
          $category->cat_icon = $Icon->file_id;
          $category->save();
        }
      }

      if (isset($values['remove_cat_icon']) && !empty($values['remove_cat_icon'])) {
        //Delete categories icon
        $catIcon = Engine_Api::_()->getItem('storage_file', $category->cat_icon);
        $category->cat_icon = 0;
        $category->save();
        $catIcon->delete();
      }

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    return $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('You have successfully edit music album category.')
    ));
  }

  //Delete category
  public function deleteCategoryAction() {

    $this->_helper->layout->setLayout('admin-simple');

    $this->view->id = $id = $this->_getParam('id');
    $this->view->catparam = $catparam = $this->_getParam('catparam');

    $categoryTable = Engine_Api::_()->getDbtable('categories', 'sesmusic');

    $this->view->subcategory = $categoryTable->getModuleSubcategory(array('column_name' => "*", 'category_id' => $id));
    $this->view->subsubcategory = $categoryTable->getModuleSubsubcategory(array('column_name' => "*", 'category_id' => $id));

    $category = $categoryTable->find($id)->current();

    //Check post
    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        //Delete category then we have empty corrosponding value in main table of contents.
        if ($catparam == 'main') {
          $categoryTable->update(array('category_id' => 0), array('category_id = ?' => $id));
        } elseif ($catparam == 'sub') {
          $categoryTable->update(array('subcat_id' => 0), array('subcat_id = ?' => $id));
        } elseif ($catparam == 'subsub') {
          $categoryTable->update(array('subsubcat_id' => 0), array('subsubcat_id = ?' => $id));
        }
        $category->delete();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      return $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => 10,
                  'parentRefresh' => 10,
                  'messages' => array('You have successfully delete album category.')
      ));
    }
    //Output
    $this->renderScript('admin-song-categories/delete.tpl');
  }

  //Delete category icon
  public function deleteIconAction() {

    $this->_helper->layout->setLayout('admin-simple');
    $this->view->id = $this->_getParam('category_id');
    $catparam = $this->_getParam('catparam');

    //Check post
    if ($this->getRequest()->isPost()) {

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        $mainPhoto = Engine_Api::_()->getItemTable('storage_file')->getFile($this->_getParam('file_id'));
        $mainPhoto->delete();

        if ($catparam == 'maincat' || $catparam == 'sub' || $catparam == 'subsub')
          Engine_Api::_()->getDbtable('categories', 'sesmusic')->update(array('cat_icon' => 0), array('category_id = ?' => $this->view->id));
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      return $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => 10,
                  'parentRefresh' => 10,
                  'messages' => array('You have successfully delete category icon.')
      ));
    }
    //Output
    $this->renderScript('admin-song-categories/delete-icon.tpl');
  }

  public function setPhoto($photo, $cat_id) {

    if ($photo instanceof Zend_Form_Element_File)
      $catIcon = $photo->getFileName();
    else if (is_array($photo) && !empty($photo['tmp_name']))
      $catIcon = $photo['tmp_name'];
    else if (is_string($photo) && file_exists($photo))
      $catIcon = $photo;
    else
      return;

    if (empty($catIcon))
      return;

    $mainName = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary' . '/' . basename($catIcon);
    $photo_params = array(
        'parent_id' => $cat_id,
        'parent_type' => "sesmusic_category",
    );

    //Resize category icon
    $image = Engine_Image::factory();
    $image->open($catIcon);

    $size = min($image->height, $image->width);
    $x = ($image->width - $size) / 2;
    $y = ($image->height - $size) / 2;

    $image->open($catIcon)
            ->resample($x, $y, $size, $size, 16, 16)
            ->write($mainName)
            ->destroy();
    try {
      $photoFile = Engine_Api::_()->storage()->create($mainName, $photo_params);
    } catch (Exception $e) {
      if ($e->getCode() == Storage_Api_Storage::SPACE_LIMIT_REACHED_CODE) {
        echo $e->getMessage();
        exit();
      }
    }
    //Delete temp file.
    @unlink($mainName);
    return $photoFile;
  }

}