<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedslideshow
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminSlideshowsController.php 2011-10-22 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Advancedslideshow_AdminSlideshowsController extends Core_Controller_Action_Admin {

  //ACTION FOR MANAGE SLIDESHOWS
  public function manageAction() {
    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                    ->getNavigation('advancedslideshow_admin_main', array(), 'advancedslideshow_admin_main_manage_slideshows');

    //FORM GENERATION
    $this->view->formFilter = $formFilter = new Advancedslideshow_Form_Admin_Filter();

		$this->view->getSlideManage = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteslideshow.manage', null);

    //GET PAGE COUNT
    $page = $this->_getParam('page', 1);

    //MAKE QUERY
    $tablePageName = Engine_Api::_()->getDbTable('pages', 'core')->info('name');
    $tableSlideshow = Engine_Api::_()->getDbTable('advancedslideshows', 'advancedslideshow');
    $tableSlideshowName = $tableSlideshow->info('name');
    $select = $tableSlideshow->select()
                    ->setIntegrityCheck(false)
                    ->from($tableSlideshowName)
                    ->joinLeft($tablePageName, "$tableSlideshowName.widget_page = $tablePageName.page_id", 'displayname');

    $values = array();
    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }

    foreach ($values as $key => $value) {
      if (null === $value) {
        unset($values[$key]);
      }
    }

    $values = array_merge(array(
                'order' => 'advancedslideshow_id',
                'order_direction' => 'DESC',
                    ), $values);

    $this->view->assign($values);

    $select->order((!empty($values['order']) ? $values['order'] : 'advancedslideshow_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

    //GET PAGINATOR
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $this->view->paginator->setItemCountPerPage(50);
    $this->view->paginator = $paginator->setCurrentPageNumber($page);
  }

  public function createAction() {
    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                    ->getNavigation('advancedslideshow_admin_main', array(), 'advancedslideshow_admin_main_manage_slideshows');

    //GET SLIDESHOW TYPE
    $type = $this->_getParam('slidetype');
    if (!empty($type)) {
      $slide_type = $type;
    } else {
      $slide_type = 'fadd';
    }

    //GET BASE URL
    $base = Zend_Controller_Front::getInstance()->getBaseUrl();
    $this->view->preview_base_url = "http://" . $_SERVER['HTTP_HOST'] . $base . "/";

    //FORM GENERATION
    $this->view->form = $form = new Advancedslideshow_Form_Admin_Slideshows_Create(array('slidetype' => $slide_type));

    if (!empty($form->slideshow_type)) {
      $form->slideshow_type->setValue($slide_type);
    }

    $widget_page = $this->_getParam('widget');
    if (!empty($form->widget_page)) {
      $form->widget_page->setValue($widget_page);
    }

    //REMOVE flash_color1 AND flash_color2 FIELD FROM FORM IF SLIDE TYPE IS NOT EQUAL TO FLASH
    if ($slide_type != 'flas') {
      $form->removeElement('flash_color1');
      $form->removeElement('flash_color2');
    }

    //REMOVE transition FIELD FROM FORM IF SLIDE TYPE IS NOT EQUAL TO PUSH
    if ($slide_type != 'push' && $slide_type != 'fold') {
      $form->removeElement('transition');
      $form->removeElement('overlap');
    }

    if ($slide_type == 'flom') {
      $form->removeElement('transition');
      $form->removeElement('delay');
      $form->removeElement('duration');
      $form->removeElement('flash_color1');
      $form->removeElement('flash_color2');
      $form->removeElement('overlap');
      $form->removeElement('random');
      //$form->removeElement('start_index');
      $form->removeElement('slide_title');
      $form->removeElement('controller');
      $form->removeElement('thumbnail');
      $form->removeElement('thumb_backcolor');
      $form->removeElement('thumb_bordcolor');
      $form->removeElement('thumb_bordactivecolor');
    } else {
      $form->removeElement('blinds');
      $form->removeElement('interval');
      $form->removeElement('progressbar');
    }

    $mod_form_content = array('submit', 'is_globalTab');
		$string_exe = '';
    include_once APPLICATION_PATH . '/application/modules/Advancedslideshow/controllers/license/license2.php';
  }

  public function editAction() {
    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                    ->getNavigation('advancedslideshow_admin_main', array(), 'advancedslideshow_admin_main_manage_slideshows');

    //CHECK USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET BASE URL
    $base = Zend_Controller_Front::getInstance()->getBaseUrl();
    $this->view->preview_base_url = "http://" . $_SERVER['HTTP_HOST'] . $base . "/";
    $isSlideShowEdit = 0;

    $this->view->advancedslideshow_id = $advancedslideshow_id = $this->_getParam('advancedslideshow_id');
    
    

    //GET SLIDESHOW INFO
    $advancedslideshowTable = $slideshow = Engine_Api::_()->getItem('advancedslideshow', $advancedslideshow_id);
    if (!Engine_Api::_()->core()->hasSubject('advancedslideshow')) {
      Engine_Api::_()->core()->setSubject($slideshow);
    }   
    
    $slide_type = $this->_getParam('slideshowtype');

    //GENERATE EDIT FORM
    $this->view->form = $form = new Advancedslideshow_Form_Admin_Slideshows_Edit(array('slidetype' => $slide_type));
    include_once APPLICATION_PATH . '/application/modules/Advancedslideshow/controllers/license/license3.php';

    //POPULATE FORM
    $previous_settings = $slideshow->toArray();
    $previous_settings['slideshow_type'] = $slide_type;
    if($slide_type == 'noob'){
    if( !empty($previous_settings['noob_elements']) ) {
      $noobElementsArray = @unserialize($previous_settings['noob_elements']);
      unset($previous_settings['noob_elements']);
      $previous_settings = @array_merge($previous_settings, $noobElementsArray);
    }
    }
    $form->populate($previous_settings);

    //REMOVE flash_color1 AND flash_color2 FIELD FROM FORM IF SLIDE TYPE IS NOT EQUAL TO FLASH
    if ($slide_type != 'flas') {
      $form->removeElement('flash_color1');
      $form->removeElement('flash_color2');
    }

    //REMOVE transition FIELD FROM FORM IF SLIDE TYPE IS NOT EQUAL TO PUSH
    if ($slide_type != 'push' && $slide_type != 'fold') {
      $form->removeElement('transition');
      $form->removeElement('overlap');
    }

    if ($slide_type == 'flom') {
      $form->removeElement('transition');
      $form->removeElement('delay');
      $form->removeElement('duration');
      $form->removeElement('flash_color1');
      $form->removeElement('flash_color2');
      $form->removeElement('overlap');
      $form->removeElement('random');
      $form->removeElement('start_index');
      $form->removeElement('slide_title');
      $form->removeElement('controller');
      $form->removeElement('thumbnail');
      $form->removeElement('thumb_backcolor');
      $form->removeElement('thumb_bordcolor');
      $form->removeElement('thumb_bordactivecolor');
    } else {
      $form->removeElement('blinds');
      $form->removeElement('interval');
      $form->removeElement('progressbar');
    }

    //CHECK POST FORM
    if ((!$this->getRequest()->isPost()) || empty($isSlideShowEdit)) {
      return;
    }
    if ((!$form->isValid($this->getRequest()->getPost())) || empty($isSlideShowEdit)) {
      return;
    }

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      //IF ADMIN CLICK ON SET DEFAUTL BUTTON THAN SET DEFATULT VALUES
      if (isset($_POST['default']) == 1) {
// 				$form->removeElement('flash_color1');
// 				$form->removeElement('flash_color2');
// 				$form->removeElement('transition');
// 				$form->removeElement('overlap');

        $values['target'] = 0;
        $values['slideshow_type'] = 'fadd';
        $values['caption_backcolor'] = '#ffffff';
        $values['thumb_bordcolor'] = '#DDDDDD';
        $values['thumb_bordactivecolor'] = '#E9F4FA';
        $values['slide_caption'] = 1;
        $values['caption_position'] = 1;
        $values['caption_backcolor'] = '#000000';
        $values['controller'] = 1;
        $values['delay'] = 2000;
        $values['duration'] = 750;
        $values['thumbnail'] = 1;
        $values['slide_title'] = 1;
        $values['random'] = 0;
        $values['start_index'] = 0;
        $values['level'] = 0;
        $values['network'] = 0;

        $slideshow_position = $slideshow->widget_position;
        if ($slideshow_position == 'full_width1' || $slideshow_position == 'full_width2' || $slideshow_position == 'full_width3' || $slideshow_position == 'full_width4'  || $slideshow_position == 'full_width5') {
          $values['height'] = 275;
          $values['width'] = 938;
        } elseif ($slideshow_position == 'middle_column1' || $slideshow_position == 'middle_column2' || $slideshow_position == 'middle_column3') {
          $values['height'] = 250;
          $values['width'] = 517;
        } elseif ($slideshow_position == 'right_column1' || $slideshow_position == 'right_column2' || $slideshow_position == 'right_column3' || $slideshow_position == 'left_column1' || $slideshow_position == 'left_column2' || $slideshow_position == 'left_column3') {
          $values['height'] = 180;
          $values['width'] = 174;
        } elseif ($slideshow_position == 'extreme1' || $slideshow_position == 'extreme2' || $slideshow_position == 'extreme3') {
          $values['height'] = 275;
          $values['width'] = 728;
        }
      } else { //ELSE GET SELECTED VALUES
        $values = $this->getRequest()->getPost();
if(isset ($values['slideshow_type'])){
        if ($values['slideshow_type'] != 'flom') {
          $is_error = 0;
          $total_images = Engine_Api::_()->getDbTable('images', 'advancedslideshow')->getTotalSlides($advancedslideshow_id);

          if ($total_images > 0) {
            if ($values['random'] == 0 && (($values['start_index']) > ($total_images - 1) || ($values['start_index']) < 0))
              $is_error = 1;

            if ($is_error == 1) {
              $error = $this->view->translate('The starting index cannot be negative and can at maximum be one less that the number of slides.');
              $this->view->status = false;
              $error = Zend_Registry::get('Zend_Translate')->_($error);

              $form->getDecorator('errors')->setOption('escape', false);
              $form->addError($error);
              return;
            }
          }
        }
}
        //POPULATE FORM
        $form->populate($values);
        //SAVE VALUES IN DATABASE
        $values = $this->getRequest()->getPost();
        if(isset ($values['noob_effect']))
          $noob_params['noob_effect'] = $values['noob_effect']; unset($values['noob_effect']);
        if(isset ($values['noob_autoplay']))  
          $noob_params['noob_autoplay'] = $values['noob_autoplay']; unset($values['noob_autoplay']);
        if(isset ($values['noob_walkIcon']))  
          $noob_params['noob_walkIcon'] = $values['noob_walkIcon']; unset($values['noob_walkIcon']);
        if(isset ($values['noob_walkSize']))  
          $noob_params['noob_walkSize'] = $values['noob_walkSize']; unset($values['noob_walkSize']);
        if(isset ($values['noob_bulletcolor']))  
          $noob_params['noob_bulletcolor'] = $values['noob_bulletcolor']; unset($values['noob_bulletcolor']);
        if(isset ($values['noob_bulletactivecolor']))  
          $noob_params['noob_bulletactivecolor'] = $values['noob_bulletactivecolor']; unset($values['noob_bulletactivecolor']);
        if(isset ($values['opacity']))  
          $noob_params['opacity'] = $values['opacity']; unset($values['opacity']);
        if(isset ($values['noob_walkDiv']))  
          $noob_params['noob_walkDiv'] = $values['noob_walkDiv']; unset($values['noob_walkDiv']);
         if(isset ($values['noob_pauseAndplay']))
          $noob_params['noob_pauseAndplay'] = $values['noob_pauseAndplay']; unset($values['noob_pauseAndplay']);
          if(isset ($values['noob_nextAndprev']))
          $noob_params['noob_nextAndprev'] = $values['noob_nextAndprev']; unset($values['noob_nextAndprev']);
          if(isset ($values['noob_walk']))
          $noob_params['noob_walk'] = $values['noob_walk']; unset($values['noob_walk']);
          if(isset ($values['noob_walk_position']))
          $noob_params['noob_walk_position'] = $values['noob_walk_position']; unset($values['noob_walk_position']);             
          $values['noob_elements'] = @serialize($noob_params);
        
        $values['widget_page'] = $slideshow->widget_page;
        $values['widget_position'] = $slideshow->widget_position;
        if (isset($values['transition1']) && isset($values['transition2'])) {
          $values['transition'] = $values['transition1'] . $values['transition2'];
        }
        $slideshow->setFromArray($values);
        $slideshow->save();
      }
      $slideshow->setFromArray($values);
      $slideshow->save();
      $this->_redirect("admin/advancedslideshow/slideshows/manage");
    }
  }

  //ACTION FOR MAKE SLIDESHOW ENABLE/DIS-ABLE
  public function enabledAction() {
    //GET SLIDESHOW ID
    $advancedslideshow_id = $this->_getParam('advancedslideshow_id');
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      //GET SLIDESHOW OBJECT
      $slideshow = Engine_Api::_()->getItem('advancedslideshow', $advancedslideshow_id);
      if ($slideshow->enabled == 0) {
        $slideshow->enabled = 1;
      } else {
        $slideshow->enabled = 0;
      }
      $slideshow->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $this->_redirect("admin/advancedslideshow/slideshows/manage");
  }

  //ACTION TO SHOW SLIDESHOW PREVIEW
  public function previewmanageAction() {
    //GET SLIDESHOW ID
    $this->view->advancedslideshow_id = $advancedslideshow_id = (int) $this->_getParam('advancedslideshow_id');
    $this->view->is_preview = true;
    
    $this->_helper->layout->setLayout('admin-simple');
    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('images', 'advancedslideshow')->getPreviewImages($advancedslideshow_id);
    $this->view->total_images = $paginator->getTotalItemCount();
    $paginator->setItemCountPerPage(5000);

		//CORE MODULE VERSION
		$coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
		$coreversion = $coremodule->version;
		$this->view->oldversion = 0;
		if($coreversion < '4.2.2') {
			$this->view->oldversion = 1;
		}

    if ($this->view->total_images == 0) {
      $this->view->error = 2;
      return;
    }

    //GET SLIDESHOW OBJECT
    $this->view->advancedslideshow = $advancedslideshow = Engine_Api::_()->getItem('advancedslideshow', $advancedslideshow_id);

    //GET THUMBNAIL START INDEX
    $this->view->start_index = $advancedslideshow->start_index;

    //GET RANDOM SETTING
    $random = $advancedslideshow->random;
    if ($random && $this->view->total_images > 1) {
      $this->view->random = 'true';
    } else {
      $this->view->random = 'false';
    }

    //GET SLIDESHOW HIGHT
    $this->view->height = $advancedslideshow->height;

    //GET SLIDESHOW WIDTH
    $this->view->width = $advancedslideshow->width;

    //GET TARGET SETTING
    $this->view->target = $advancedslideshow->target;

    $this->view->thumb_width = (int) ($this->view->width / $this->view->total_images);

    //GET CAPTION SETTING
    $caption = $advancedslideshow->slide_caption;
    if ($caption) {
      $this->view->caption = 'true';
    } else {
      $this->view->caption = 'false';
    }

    //GET CAPTION POSITION
    $this->view->position_caption = $advancedslideshow->caption_position;

    //GET CAPTION BACKGROUND COLOR
    $this->view->colorback_caption = $advancedslideshow->caption_backcolor;
    
    //GET TYPE OF SLIDE SHOW
    $this->view->type = $advancedslideshow->slideshow_type;

    $this->view->blinds = $advancedslideshow->blinds;
    $this->view->interval = $advancedslideshow->interval;
    $progressbar = $advancedslideshow->progressbar;
    if ($progressbar) {
      $this->view->progressbar = 'true';
    } else {
      $this->view->progressbar = 'false';
    }

    //GET THUMBNAIL BACKGROUND COLOR
    $this->view->thumb_back_color = $advancedslideshow->thumb_backcolor;

    //GET THUMBNAIL BORDER COLOR
    $this->view->thumb_bord_color = $advancedslideshow->thumb_bordcolor;

    //GET ACTIVE THUMBNAIL BOARDER COLOR
    $this->view->thumb_bord_active = $advancedslideshow->thumb_bordactivecolor;

    //IF SLIDETYPE IS FLASH THAN GET COLOR ALSO
    $this->view->color1 = $advancedslideshow->flash_color1;
    $this->view->color2 = $advancedslideshow->flash_color2;

    //IF SLIDETYPE IS PUSH AND FOLD THAN ADD TRANSITION TYPE
    $this->view->transition = $advancedslideshow->transition;

    //GET OVERLAP SETTING
    $overlap = $advancedslideshow->overlap;
    if ($overlap) {
      $this->view->overlap = 'true';
    } else {
      $this->view->overlap = 'false';
    }

    //GET CONTROLLER SETTING
    $controller = $advancedslideshow->controller;
    if ($controller) {
      $this->view->controller = 'true';
    } else {
      $this->view->controller = 'false';
    }

    //GET DEALY TIME
    $this->view->delay = $advancedslideshow->delay;

    //GET DURATION TIME
    $this->view->duration = $advancedslideshow->duration;

    //GET THUMBNAIL SETTING
    $thumb = $advancedslideshow->thumbnail;
    if ($thumb) {
      $this->view->thumb = 'true';
    } else {
      $this->view->thumb = 'false';
    }

    //GET TITLE SETTING
    $title = $advancedslideshow->slide_title;
    if ($title) {
      $this->view->title = 'true';
    } else {
      $this->view->title = 'false';
    }
          $getSlideshowType = $this->view->type;
        if (!empty($advancedslideshow_id) && ($getSlideshowType == 'noob')) {
          $this->view->noob_elements = $advancedslideshow->noob_elements;
      $this->view->getContentArray = Engine_Api::_()->advancedslideshow()->getNoobSlidesArray($advancedslideshow);
    }
    
  }

  //ACTION TO SHOW SLIDESHOW PREVIEW
  public function previeweditAction() {
    //GET SLIDESHOW ID
    $this->view->advancedslideshow_id = $advancedslideshow_id = $this->_getParam('advancedslideshow_id');

    $this->_helper->layout->setLayout('admin-simple');
    $this->view->paginator = $paginator = Engine_Api::_()->getDbTable('images', 'advancedslideshow')->getPreviewImages($advancedslideshow_id);
    $this->view->total_images = $paginator->getTotalItemCount();
    $paginator->setItemCountPerPage(5000);

		//CORE MODULE VERSION
		$coremodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('core');
		$coreversion = $coremodule->version;
		$this->view->oldversion = 0;
		if($coreversion < '4.2.2') {
			$this->view->oldversion = 1;
		}

    if ($this->view->total_images <= 0) {
      $this->view->error = 2;
      return;
    }

    if ($_GET['type'] != 'flom') {
      //GET THUMBNAIL BORDER COLOR
      $this->view->start_index = $_GET['start_index'];

      //GET RANDOM SETTING
      $random = $_GET['random'];
      if ($random && $this->view->total_images > 1) {
        $this->view->random = 'true';
      } else {
        $this->view->random = 'false';
      }

      if ($random == 0 && (($this->view->total_images - 1) < ($this->view->start_index) || $this->view->start_index < 0)) {
        $this->view->error = 1;
        return;
      }
    }

    //GET SLIDESHOW OBJECT
    $this->view->advancedslideshow = $advancedslideshow = Engine_Api::_()->getItem('advancedslideshow', $advancedslideshow_id);

    //GET SLIDESHOW HIGHT
    $this->view->height = $_GET['height'];

    //GET SLIDESHOW WIDTH
    $this->view->width = $_GET['width'];

    //GET TARGET SETTING
    $this->view->target = $_GET['target'];

    $this->view->thumb_width = (int) ($this->view->width / $this->view->total_images);

    //GET CAPTION SETTING
    $caption = $_GET['caption'];
    if ($caption) {
      $this->view->caption = 'true';
    } else {
      $this->view->caption = 'false';
    }

    //GET CAPTION POSITION
    $this->view->position_caption = $_GET['position_caption'];

    //GET CAPTION BACKGROUND COLOR
    $this->view->colorback_caption = urldecode($_GET['colorback_caption']);

    //GET TYPE OF SLIDE SHOW
    $this->view->type = $_GET['type'];

    if ($this->view->type == 'flom') {
      $this->view->blinds = $_GET['blinds'];
      $this->view->interval = $_GET['interval'];
      $progressbar = $_GET['progressbar'];
      if ($progressbar) {
        $this->view->progressbar = 'true';
      } else {
        $this->view->progressbar = 'false';
      }
    } 
		else {

			//GET THUMBNAIL BACKGROUND COLOR
			$this->view->thumb_back_color = urldecode($_GET['thumb_back_color']);

			//GET THUMBNAIL BORDER COLOR
			$this->view->thumb_bord_color = urldecode($_GET['thumb_bord_color']);

			//GET ACTIVE THUMBNAIL BOARDER COLOR
			$this->view->thumb_bord_active = urldecode($_GET['thumb_bord_active']);

      //IF SLIDETYPE IS FLASH THAN GET COLOR ALSO
      if ($_GET['type'] == 'flas') {
        $this->view->color1 = urldecode($_GET['color1']);
        $this->view->color2 = urldecode($_GET['color2']);
      }

      //IF SLIDETYPE IS PUSH AND FOLD THAN ADD TRANSITION TYPE
      if ($_GET['type'] == 'fold' || $_GET['type'] == 'push') {
        $this->view->transition = $_GET['transition'];

        //GET OVERLAP SETTING
        $overlap = $_GET['overlap'];
        if ($overlap) {
          $this->view->overlap = 'true';
        } else {
          $this->view->overlap = 'false';
        }
      }

      //GET CONTROLLER SETTING
      $controller = $_GET['controller'];
      if ($controller) {
        $this->view->controller = 'true';
      } else {
        $this->view->controller = 'false';
      }

      //GET DEALY TIME
      $this->view->delay = $_GET['delay'];

      //GET DURATION TIME
      $this->view->duration = $_GET['duration'];

      //GET THUMBNAIL SETTING
      $thumb = $_GET['thumb'];
      if ($thumb) {
        $this->view->thumb = 'true';
      } else {
        $this->view->thumb = 'false';
      }

      //GET TITLE SETTING
      $title = $_GET['title'];
      if ($title) {
        $this->view->title = 'true';
      } else {
        $this->view->title = 'false';
      }
    }
    if($this->view->type == 'noob') {
      $noobContent = array();
      $noobContent['noob_effect'] = $_GET['noob_effect'];
      $noobContent['noob_autoplay'] = $_GET['noob_autoplay'];
      $noobContent['noob_walkIcon'] = $_GET['noob_walkIcon'];
      $noobContent['noob_walkSize'] = $_GET['noob_walkSize'];
      $noobContent['noob_bulletcolor'] = urldecode($_GET['noob_bulletcolor']);
      $noobContent['noob_bulletactivecolor'] = urldecode($_GET['noob_bulletactivecolor']);
      $noobContent['opacity'] = $_GET['opacity'];
      $noobContent['noob_walkDiv'] = $_GET['noob_walkDiv'];
      $noobContent['noob_walk'] = $_GET['noob_walk'];
      $noobContent['noob_walk_position'] = $_GET['noob_walk_position'];
      $this->view->noob_elements = serialize($noobContent);
      $this->view->getContentArray = Engine_Api::_()->advancedslideshow()->getNoobSlidesArray($advancedslideshow);
      $this->view->is_preview = true;
    }
    
    
  }

  //ACTION FOR DELETE SLIDESHOW
  public function codeAction() {
    //LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //GET SLIDESHOW ID AND IT'S OBJECT
    $advancedslideshow_id = $this->_getParam('advancedslideshow_id');
    $advancedslideshow = Engine_Api::_()->getItem('advancedslideshow', $advancedslideshow_id);

    $widget_name = $advancedslideshow->widget_position;

    if ($advancedslideshow->widget_page == -1) {
      switch ($widget_name) {
        case 'full_width3':
          $this->view->code = '&lt;div class="layout_middle"&gt;&lt;?php echo $this->content()->renderWidget("advancedslideshow.fullwidth3-advancedslideshows"); ?&gt;&lt;/div&gt;';
          break;
        case 'middle_column3':
          $this->view->code = '&lt;div class="layout_middle"&gt;&lt;?php echo $this->content()->renderWidget("advancedslideshow.middle3-advancedslideshows");?&gt;&lt;/div&gt;';
          break;
        case 'right_column3':
          $this->view->code = '&lt;div class="layout_right"&gt;&lt;?php echo $this->content()->renderWidget("advancedslideshow.right3-advancedslideshows");?&gt;&lt;/div&gt;';
          break;
        case 'left_column3':
          $this->view->code = '&lt;div class="layout_left"&gt;&lt;?php echo $this->content()->renderWidget("advancedslideshow.left3-advancedslideshows");?&gt;&lt;/div&gt;';
          break;
        case 'extreme3':
          $this->view->code = '&lt;div class="layout_middle"&gt;&lt;?php echo $this->content()->renderWidget("advancedslideshow.extended3-advancedslideshows");?&gt;&lt;/div&gt;';
          break;
      }
    } else {
      $this->view->code = 0;
    }
  }

  //ACTION FOR DELETE SLIDESHOW
  public function deleteAction() {
    //LAYOUT
    $this->_helper->layout->setLayout('admin-simple');

    //GET SLIDESHOW ID
    $this->view->advancedslideshow_id = $advancedslideshow_id = $this->_getParam('advancedslideshow_id');

    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        Engine_Api::_()->advancedslideshow()->deleteSlideshow($advancedslideshow_id);
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array('')
      ));
    }
    $this->renderScript('admin-slideshows/delete.tpl');
  }

  //ACTION FOR DELETE SLIDESHOW AND THEIR BELONGINGS
  public function multiDeleteAction() {
    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();

      //IF ADMIN CLICK ON DELETE SELECTED BUTTON
      if (!empty($values['delete'])) {
        foreach ($values as $key => $value) {
          if ($key == 'delete_' . $value) {
            $advancedslideshow_id = (int) $value;
            Engine_Api::_()->advancedslideshow()->deleteSlideshow($advancedslideshow_id);
          }
        }
      }
    }
    return $this->_helper->redirector->gotoRoute(array('action' => 'manage'));
  }

   public function uploadPhotoAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    $this->_helper->layout->disableLayout();

   

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }
     $fileName = Engine_Api::_()->seaocore()->tinymceEditorPhotoUploadedFileName();
    if( !isset($_FILES[$fileName]) || !is_uploaded_file($_FILES[$fileName]['tmp_name']) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
      return;
    }
  $this->view->advancedslideshow_id = $advancedslideshow_id = $this->_getParam('advancedslideshow_id');

    //GET MODEL FROM engine4_advancedslideshows TABLE
    $this->view->advancedslideshow = $advancedslideshow = Engine_Api::_()->getItem('advancedslideshow', $advancedslideshow_id);
  
     // $advancedslideshow = Engine_Api::_()->getDbtable('photos', 'album');

      $file= $advancedslideshow->setEditorPhoto($_FILES[$fileName]);

      $this->view->status = true;
      $this->view->name = $_FILES[$fileName]['name'];
      $this->view->photo_url = $file->map();
 
  }

  
}
