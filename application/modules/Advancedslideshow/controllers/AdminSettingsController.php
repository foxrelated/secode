<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedslideshow
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSettingsController.php 2011-10-22 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Advancedslideshow_AdminSettingsController extends Core_Controller_Action_Admin {

  //ACTION FOR GLOBAL SETTINGS
  public function indexAction() {
		if(!isset($_POST['advancedslideshow_lsettings'])){ $_POST['advancedslideshow_lsettings'] = null; }
    $mod_form_content = array('submit');
    
    $pluginName = 'advancedslideshow';
    if (!empty($_POST[$pluginName . '_lsettings']))
      $_POST[$pluginName . '_lsettings'] = @trim($_POST[$pluginName . '_lsettings']);
    
    include_once APPLICATION_PATH . '/application/modules/Advancedslideshow/controllers/license/license1.php';
  }

  //ACTION FOR DEMO OF SLIDESHOW
  public function demoAction() {
    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                    ->getNavigation('advancedslideshow_admin_main', array(), 'advancedslideshow_admin_main_demo');

		//CORE MODULE VERSION
		$coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
		$coreversion = $coremodule->version;
		$this->view->oldversion = 0;
		if($coreversion < '4.2.2') {
			$this->view->oldversion = 1;
		}

    //GENERATE FORM
    $this->view->form = $form = new Advancedslideshow_Form_Admin_Settings_Demo();

    //GET SLIDESHOW TYPE
    $slide_type = $this->_getParam('type');
    if (!empty($slide_type)) {
      $this->view->type = $slide_type;
    } else {
      $this->view->type = 'fadd';
    }

    $this->view->height = 275;
    $this->view->width = 938;
    $this->view->caption = 'true';

    if ($this->view->type == 'flom') {
      $this->view->blinds = 24;
      $this->view->interval = 8000;
      $this->view->progressbar = 'true';
    } else {
      //CHECHK THAT THUMBNAIL IS VISIBLE OR NOT
      $show_thumb = $this->_getParam('thumb');
      if (!empty($slide_type)) {
        if ($show_thumb)
          $this->view->thumb = 'false';
        else
          $this->view->thumb = 'true';
      }
      else {
        $this->view->thumb = 'true';
      }
      //SET OTHER DEFAULT SETTING
      $this->view->visibility = 2;
      $this->view->controller = 'true';
      $this->view->delay = 2000;
      $this->view->duration = 750;
      $this->view->title = 'true';
      $this->view->overlap = 'true';
      $this->view->random = 'false';
      $this->view->color1 = '#EC2415';
      $this->view->color2 = '#7EBBFF';
      $this->view->start_index = 0;
      $this->view->thumb_back_color = '#ffffff';
      $this->view->thumb_bord_color = '#DDDDDD';
      $this->view->thumb_bord_active = '#E9F4FA';
      $this->view->thumb_width = 188;
      $values['advancedslideshow_thumb'] = $this->_getParam('thumb');
    }
    //ADD THIS TWO VALUES IN $values ARRAY TO SHOW PREFIELD
    $values['advancedslideshow_type'] = $this->_getParam('type');

    //POPULATE FORM
    $form->populate($values);

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();

      //CHECK THAT WHICH TYPE OF SLIDESHOW WE HAVE TO SHOW AND THUMBNAIL IS VISIBLE OR NOT
      $type = $values['advancedslideshow_type'];
      if ($type == 'floom') {
        $this->_redirect('admin/advancedslideshow/settings/demo/type/' . $type);
      } else {
        $thumb = $values['advancedslideshow_thumb'];
        $this->_redirect('admin/advancedslideshow/settings/demo/type/' . $type . '/thumb/' . $thumb);
      }
    }
    
    if($slide_type == 'noob') {
       $form->removeElement('advancedslideshow_thumb');
      $noobContent = array();
      $noobContent['noob_effect'] = 'simple';
      $noobContent['noob_autoplay'] = 1;
      $noobContent['noob_walkIcon'] = 1;
      $noobContent['noob_walkSize'] = 10;
      $noobContent['noob_bulletcolor'] = '#000';
      $noobContent['noob_bulletactivecolor'] = '#808080';
      $noobContent['opacity'] = 0.75;
      $noobContent['noob_walkDiv'] = 1;
      $noobContent['noob_pauseAndplay'] = '';
      $noobContent['noob_nextAndprev'] = '';
      $noobContent['noob_walk'] = 1;
      $noobContent['noob_walk_position'] = 'right';
      $this->view->noob_elements = serialize($noobContent);
      $this->view->is_preview = true;
    }
  }

  //ACTION FOR GUIDELINES ABOUT NON-WIDGETIZED PAGES WIDGET
  public function guidelinesAction() {
    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                    ->getNavigation('advancedslideshow_admin_main', array(), 'advancedslideshow_admin_main_manage_slideshows');
  }

  //ACTION FOR FAQ
  public function faqAction() {
    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                    ->getNavigation('advancedslideshow_admin_main', array(), 'advancedslideshow_admin_main_faq');
  }

  public function readmeAction() {
    
  }
  
  public function transferSlidesAction() {
      
    $allowTransfer = $this->_getParam('allowTransfer', 0); 
    if(!empty($allowTransfer)) {  
        
        ini_set('memory_limit', '2048M');
        set_time_limit(0);
        ini_set('max_input_time', 600);   
        ini_set('max_execution_time', 600);        
        
        //GET SLIDESHOW TABLE
        $tableSlideshow = Engine_Api::_()->getDbTable('advancedslideshows', 'advancedslideshow');
        $tableSlideshowName = $tableSlideshow->info('name');

        //GET SLIDES TABLE NAME
        $tableImage = Engine_Api::_()->getDbTable('images', 'advancedslideshow');
        $tableImageName = $tableImage->info('name');     

        //MAKE QUERY
        $select = $tableImage->select()
                        ->setIntegrityCheck(false)
                        ->from($tableImageName, array('image_id', 'file_id'))
                        ->join($tableSlideshowName, "$tableSlideshowName.advancedslideshow_id = $tableImageName.advancedslideshow_id", array('advancedslideshow_id', 'height', 'width', 'slide_resize'))
                        ->where($tableImageName . '.file_id != ?', 0);

        //RETURN QUERY
        $imagesDatas = $tableImage->fetchAll($select);     
        
        foreach($imagesDatas as $imagesData) {  
            
            $fileStorage = Engine_Api::_()->getItem('storage_file', $imagesData->file_id);
            if(empty($fileStorage)) continue;
            
            //GET IMAGE INFO AND RESIZE
            $file['tmp_name'] = ltrim($fileStorage->storage_path, '/');
            $path_array = explode('/', $file['tmp_name']);
            $file['name'] = end($path_array);
            
            if(!file_exists($file['tmp_name'])) {
                continue;
            }
            
            $name = basename($file['tmp_name']);
            $path = dirname($file['tmp_name']);
            $extension = ltrim(strrchr($file['name'], '.'), '.');

            $mainName = $path . '/m_' . $name . '.' . $extension;
            $thumbName = $path . '/t_' . $name . '.' . $extension;

            //GET SLIDESHOW HEIGHT
            $height = $imagesData->height;

            //GET SLIDESHOW WIDTH
            $width = $imagesData->width;

            if($imagesData->slide_resize) {
              $image = Engine_Image::factory();
              $image->open($file['tmp_name'])
                      ->resize($width, $height)
                      ->write($mainName)
                      ->destroy();

              $image = Engine_Image::factory();
              $image->open($file['tmp_name'])
                      //->resize(80, 51)
                      ->resize($width, $height)
                      ->write($thumbName)
                      ->destroy();          
            }
            else {
              $image = Engine_Image::factory();
              $image->open($file['tmp_name'])
                     //->resize($width, $height)
                     ->write($mainName)
                     ->destroy();

              $image = Engine_Image::factory();
              $image->open($file['tmp_name'])
                     //->resize(self::THUMB_WIDTH, self::THUMB_HEIGHT)
                     ->write($thumbName)
                     ->destroy();         
            }

            $image_params = array(
                'parent_id' => 5,
                'parent_type' => 'advancedslideshow',
            );

            $imageFile = Engine_Api::_()->storage()->create($mainName, $image_params);
            $thumbFile = Engine_Api::_()->storage()->create($thumbName, $image_params);

            $imageFile->bridge($thumbFile, 'thumb.normal');

            if($imageFile->file_id) {
                $tableImage->update(array('file_id' => $imageFile->file_id), array('file_id = ?' => $imagesData->file_id));
                //$fileStorage->delete();
            }
        }
        
    }
    
		$redirect = $this->_getParam('redirect', false);
		if($redirect == 'install') {
			$this->_redirect('install/manage');
		} elseif($redirect == 'query') {
			$this->_redirect('install/manage/complete');
		}    
      
  }

}
?>