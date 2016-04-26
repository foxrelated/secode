<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedslideshow
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSlidesController.php 2011-10-22 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Advancedslideshow_AdminSlidesController extends Core_Controller_Action_Admin {

  //ACTION FOR MANAGE SLIDES
  public function manageAction() {
    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                    ->getNavigation('advancedslideshow_admin_main', array(), 'advancedslideshow_admin_main_manage_slideshows');

    //GET SLIDESHOW OBJECT
    $this->view->advancedslideshow_id = $advancedslideshow_id = $this->_getParam('advancedslideshow_id');

    //GET MODEL FROM engine4_advancedslideshows TABLE
    $this->view->advancedslideshow = $advancedslideshow = Engine_Api::_()->getItem('advancedslideshow', $advancedslideshow_id);
    include_once APPLICATION_PATH . '/application/modules/Advancedslideshow/controllers/license/license3.php';
  }

  //ACTION FOR EDIT SLIDE DETAILS
  public function editAction() {
    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                    ->getNavigation('advancedslideshow_admin_main', array(), 'advancedslideshow_admin_main_manage_slideshows');

    //GET SLIDE OBJECT
    $image = Engine_Api::_()->getItem('advancedslideshow_image', $this->_getParam('image_id'));

    //GET SLIDESHOW ID AND IT'S OBJECT
    $this->view->advancedslideshow_id = $advancedslideshow_id = $image->advancedslideshow_id;
    $advancedslideshow = Engine_Api::_()->getItem('advancedslideshow', $advancedslideshow_id);

    //FORM GENERATION
    $form = $this->view->form = new Advancedslideshow_Form_Admin_Slides_Edit(array('item' => $advancedslideshow_id));
    if($advancedslideshow->slideshow_type=="noob" && !empty($image->slide_html) )
       $form->removeElement('url');
    $stat['url'] = $image->url;
    $stat['caption'] = $image->caption;

    //SHOW PREFIELD NETWORKS
    if ($advancedslideshow->network) {
      $networks = Engine_Api::_()->getDbtable('networks', 'network')->fetchAll();
      if ($networks) {
        if ($ad_networks = $form->getElement('slide_networks'))
          $ad_networks->setValue(Zend_Json_Decoder::decode($image->network));
      }
    }

    //SHOW PREFIELD LEVELS
    if ($advancedslideshow->level) {
      if ($levels = $form->getElement('slide_levels')) {
        $levels->setValue(Zend_Json_Decoder::decode($image->level));
      }
    } else {
      if ($show_public = $form->getElement('show_public')) {
        $show_public->setValue($image->show_public);
      }
    }

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();
      if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestore') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreproduct') && array_key_exists('category_id', $values)) {
        if($values['url_type'] == 1) {
          $params = array();
          $params['category_id'] = $advancedslideshow->resource_id;
          $params['subcategory_id'] = $values['subcategory_id'];
          $params['subsubcategory_id'] = $values['subsubcategory_id'];
          $values['params'] = Zend_Json::encode($params);
          $values['url'] = '';
        }
      }

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {
        include_once APPLICATION_PATH . '/application/modules/Advancedslideshow/controllers/license/license3.php';
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      return $this->_helper->redirector->gotoRoute(array('module' => 'advancedslideshow', 'controller' => 'slides', 'action' => 'manage', 'advancedslideshow_id' => $advancedslideshow_id));
    }

    if (!($image_id = $this->_getParam('image_id'))) {
      die('No identifier specified');
    }

    $form->setField($stat);
  }

  //ACTION FOR SHOW VISIBILITY OF SLIDE
  public function visibilityAction() {
    //LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //GET SLIDE OBJECT
    $image_id = $this->_getParam('image_id');
    $this->view->image = $image = Engine_Api::_()->getItem('advancedslideshow_image', $image_id);

    //GET SLIDE-SHOW OBJECT
    $slideshow_id = $this->_getParam('slideshow_id');
    $this->view->slideshow = $slideshow = Engine_Api::_()->getItem('advancedslideshow', $slideshow_id);

    //GET SELECTED LEVELS
    $levels_prepared = array();
    $selectedLevels = array();
    if ($slideshow->level) {
      $levels = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchAll();
      foreach ($levels as $level) {
        $levels_prepared[$level->getIdentity()] = $level->getTitle();
      }

      $getLevels = Zend_Json_Decoder::decode($image->level);
      foreach ($getLevels as $getLevel) {
        foreach ($levels_prepared as $key => $level) {
          if ($getLevel == $key) {
            $selectedLevels[$key] = $level;
          }
        }
      }
    }

    //GET SELECTED NETWORKS
    $networks_prepared = array();
    $selectedNetworks = array();
    if ($slideshow->network) {
      $networks = Engine_Api::_()->getDbtable('networks', 'network')->fetchAll();
      foreach ($networks as $network) {
        $networks_prepared[$network->getIdentity()] = $network->getTitle();
      }

      $getNetworks = Zend_Json_Decoder::decode($image->network);
      foreach ($getNetworks as $getNetwork) {
        foreach ($networks_prepared as $key => $network) {
          if ($getNetwork == $key) {
            $selectedNetworks[$key] = $network;
          }
        }
      }
    }

    if ($image->show_public) {
      $this->view->visitor_visibility = Zend_Registry::get('Zend_Translate')->_("Yes");
    } else {
      $this->view->visitor_visibility = Zend_Registry::get('Zend_Translate')->_("No");
    }

    $this->view->selectedLevels = $selectedLevels;
    $this->view->selectedNetworks = $selectedNetworks;

    //RENDER SCRIPT
    $this->renderScript('admin-slides/visibility.tpl');
  }

  //ACTION FOR MAKE SLIDE ENABLE/DIS-ABLE
  public function enabledAction() {
    //GET IMAGE ID
    $image_id = $this->_getParam('image_id');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {

      //GET SLIDE OBJECT
      $slide = Engine_Api::_()->getItem('advancedslideshow_image', $image_id);
      if ($slide->enabled == 0) {
        $slide->enabled = 1;
      } else {
        $slide->enabled = 0;
      }
      $slide->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'manage', 'advancedslideshow_id' => $slide->advancedslideshow_id));
  }

  //ACTION FOR DELETE SLIDES
  public function multiDeleteAction() {
    //GET SLIDESHOW ID
    $advancedslideshow_id = $this->_getParam('advancedslideshow_id');

    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();

      //IF ADMIN CLICK ON DELETE SELECTED BUTTON
      if (!empty($values['delete'])) {
        foreach ($values as $key => $value) {
          if ($key == 'delete_' . $value) {
            //GET IMAGE ID AND IT'S OBJECT
            $image_id = (int) $value;
            $image = Engine_Api::_()->getItem('advancedslideshow_image', $image_id);

            //$extension = $image->extension;
            $image->delete();
            //unlink(APPLICATION_PATH . "/public/advancedslideshow/1000000/1000/5/" . $image_id . 't.' . $extension);
          }
        }

        //CHANGE START INDEX IF ATLEAST ONE SLIDE IS DELETED
        if (!empty($advancedslideshow_id)) {

          //GET TOTAL IMAGES COUNT
          $total_images = Engine_Api::_()->getDbTable('images', 'advancedslideshow')->getTotalSlides($advancedslideshow_id);

          //GET SLIDESHOW OBJECT
          $advancedslideshow = Engine_Api::_()->getItem('advancedslideshow', $advancedslideshow_id);

          $start_index = $advancedslideshow->start_index;
          if ($start_index > $total_images - 1) {
            if ($total_images != 0) {
              $advancedslideshow->start_index = $total_images - 1;
              $advancedslideshow->save();
            } else {
              $advancedslideshow->start_index = 0;
              $advancedslideshow->save();
            }
          }
        }
      } else { //IF ADMIN CLICK ON SAVE ORDER BUTTON
        foreach ($values['image_id'] as $key => $value) {
          $image = Engine_Api::_()->getItem('advancedslideshow_image', (int) $value);
          $image->order = $key + 1;
          $image->save();
        }
      }
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'manage', 'advancedslideshow_id' => $advancedslideshow_id));
  }

}
?>