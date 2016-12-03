<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Mcard
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminLevelController.php 2010-10-13 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Mcard_AdminLevelController extends Core_Controller_Action_Admin {

    public function indexAction() {
        $temp_level_id = $level_id = $this->_getParam('level_id', null);
        $temp_map_id = $map_id = $this->_getParam('mptype_id', null);
      
        // INSERT MISSING MEMBER LEVEL & PROFILE TYPES INTO DATABASE.
        // Prepare user levels
        $table = Engine_Api::_()->getDbtable('levels', 'authorization');
        $select = $table->select();
        $user_levels = $table->fetchAll($select);

        // Prepare Profile type
        $fieldtable = Engine_Api::_()->getItemTable('mcard_option');
        $fieldselect = $fieldtable->select()
                ->where("field_id = ?", 1);
        $mp_levels = $fieldtable->fetchAll($fieldselect);

        foreach ($user_levels as $user_level) {
            foreach ($mp_levels as $mp_level) {
              if(empty($level_id))
                $level_id = $user_level->level_id;
              
              if(empty($map_id))
                $map_id = $mp_level->option_id;
              
                $getStatus = Engine_Api::_()->getItemTable('mcard_info')->getVal($user_level->level_id, $mp_level->option_id);
                if (!empty($user_level->level_id) && !empty($mp_level->option_id) && empty($getStatus)) {
                    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                    $db->query('INSERT IGNORE INTO `engine4_mcard_info` (`level_id`, `mp_id`, `values`) VALUES (' . $user_level->level_id . ', ' . $mp_level->option_id . ', \' {"Profile Photo":"profile_photo","card_label":"Membership Card","Display Name":"displayname","Joining Date":"doj","Profile Type":1,"card_status":"1","logo":null,"card_bg_image":null,"label_color":"#061822","info_color":"#061822","label_font":"Courier New, Courier, monospace","info_font":"Courier New, Courier, monospace","logo_select":"1"}  \');');
                }
            }
        }
                
        // check the 'level' or 'mp_id' is available or not in table if not then insert that row.
        $this->view->preview_level_id = $level_id;
        $this->view->preview_mp_id = $map_id;
        
        if (!empty($level_id) && !empty($map_id)) {
            $infoTable = Engine_Api::_()->getItemTable('mcard_info');
            // It will empty only if 'Row' is not available in owr table then insert row and then redirect again in this link.
            $origSettings = $infoTable->getVal($level_id, $map_id);
            if (empty($origSettings)) {
                $info = $infoTable->createRow();
                $info->level_id = $level_id;
                $info->mp_id = $map_id;
                $info->values = '{"Profile Photo":"profile_photo","card_label":"Membership Card","Display Name":"displayname","Joining Date":"doj","Profile Type":1,"card_status":"1","logo":null,"card_bg_image":null,"label_color":"#061822","info_color":"#061822","label_font":"Courier New, Courier, monospace","info_font":"Courier New, Courier, monospace","logo_select":"1"}';
                $info->save();
            }
        }



        $sessionNamespace = new Zend_Session_Namespace();
        $cardData = array();
        //$levelViewer = '';
        $viewer = $this->_helper->api()->user()->getViewer();
        $user_id = $viewer->getIdentity();
        $this->view->preview_base_url = Zend_Controller_Front::getInstance()->getBaseUrl(); // Find out the "Base Url".
        // Level information
        if (!empty($level_id)) {
            $level = Engine_Api::_()->getItem('authorization_level', $level_id);
        } else {
            $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
        }
        if (!$level instanceof Authorization_Model_Level) {
            throw new Engine_Exception('Missing level');
        }
//        $level_id = $level->level_id;
        $this->view->level = $level;

        // Get mptype_id
        if (($mp_id = $this->_getParam('mptype_id')) == null) {
            $mp_id = 1;
        }

        // Get userinfo
        $sample = Engine_Api::_()->user()->getUser($user_id);


        // Make form
        if(empty($temp_level_id))
          $this->_setParam('level_id', $level_id);
        
        if(empty($temp_map_id))
          $this->_setParam('mptype_id', $map_id);
          
        $this->view->form = $form = new Mcard_Form_Admin_Level(array('levelId' => $level_id, 'mapId' => $map_id));
        //Sets the current level_id in drop down list
        $form->level_id->setValue($level_id);
        //Sets the default mptype_id in drop down list
        $form->mptype_id->setValue($mp_id);

        $infoTable = Engine_Api::_()->getItemTable('mcard_info');
        $origSettings = $infoTable->getVal($level_id, $mp_id);
        $form->card_status->setValue($origSettings['card_status']);

        // Check post - Default values retrived from DB and set on the form. This form is using for levl and profile type form submission
        if (!$this->getRequest()->isPost()) {
            if (isset($origSettings['label_color'])) {
                $form->label_color->setValue($origSettings['label_color']);
            }
            if (isset($origSettings['label_font'])) {
                $form->label_font->setValue($origSettings['label_font']);
            }
            if (isset($origSettings['info_color'])) {
                $form->info_color->setValue($origSettings['info_color']);
            }
            if (isset($origSettings['info_font'])) {
                $form->info_font->setValue($origSettings['info_font']);
            }
            if (isset($origSettings['Profile Photo'])) {
                $form->profile_photo->setValue(1);
            } else {
                $form->profile_photo->setValue(0);
            }
            if (!empty($origSettings['card_label'])) {
                $form->show_card_label->setValue(1);
                $form->card_label->setValue($origSettings['card_label']);
            } else {
                $form->show_card_label->setValue(0);
                $form->card_label->setValue("Membership Card");
            }
            if (($origSettings['logo_select'])) {
                $form->logo_select->setValue(1);
            } else {
                $form->logo_select->setValue(0);
            }
        }

        $this->view->preview_value = '';

        // This form is using for making the changes in level settings
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();
            $mcard_admin_tab = 'member_level_settings';
            // If upload the logo then "logo will be upload in temporary file".
            if (!empty($values['upload_logo_indication'])) {
                move_uploaded_file($_FILES["logo"]["tmp_name"], APPLICATION_PATH . '/public/temporary/' . $_FILES["logo"]["name"]);
                exit();
            }
            if (!empty($values['upload_card_bg_image'])) {
                move_uploaded_file($_FILES["card_bg_image"]["tmp_name"], APPLICATION_PATH . '/public/temporary/' . $_FILES["card_bg_image"]["name"]);
                exit();
            }
            // Insert in "core_settings" table empty fields value.
            Engine_Api::_()->getApi('settings', 'core')->setSetting('empty_fields', $values['empty_fields']);
            if ($values['logo_select'] == 1) {
                if (!empty($values['logo'])) {
                    // Saves the photo and returns the photo number saved in memory
                    $values['logo'] = Engine_Api::_()->mcard()->savePhoto($form->logo, $level_id);
                } elseif (!empty($values['logo_hidden'])) {
                    $values['logo'] = $values['logo_hidden'];
                }
            }
            if ($values['shiny_plastic_look'] == 0) {
                if ($values['card_bg_image'] != "") {
                    // Saves the photo and returns the photo number saved in memory

                    $values['card_bg_image'] = Engine_Api::_()->mcard()->savePhoto($form->card_bg_image, $level_id);
                } elseif (!empty($values['card_bg_image_hidden'])) {
                    $values['card_bg_image'] = $values['card_bg_image_hidden'];
                }
                $values['card_bg_color'] = null;
            }
            if ($values['shiny_plastic_look'] == 1) {
                $values['card_bg_image'] = null;
                $cardData['card_bg_color'] = $values['card_bg_color'];
            }
            if ($values['profile_photo'] == 1) {
                $cardData['Profile Photo'] = 'profile_photo';
            }
            if ($values['show_card_label'] == 1) {
                $cardData['card_label'] = $values['card_label'];
            }
            // Check Boxes to be processed here
            foreach ($values['select_option'] as $key => $field_value) {

                /* Below if $field value is assigned 3,4,.. data is stored
                 * in db. to process it further like [First Name] => 'Admin First Name'
                 */
                $cardData[$sessionNamespace->selectOptionFormValues[$field_value]] = $field_value;
                if ($field_value == 'doj') {
                    $varDate = gmdate("F d,Y", strtotime($sample->creation_date));
                    $cardData[$sessionNamespace->selectOptionFormValues[$field_value]] = $field_value;
                }
                if ($field_value == 'displayname') {
                    $cardData[$sessionNamespace->selectOptionFormValues[$field_value]] = $field_value;
                }
                if ($field_value == 'mlevel_id') {
                    $cardData[$sessionNamespace->selectOptionFormValues[$field_value]] = $level->title;
                }
                if ($field_value == 'mptype') {
                    $cardData[$sessionNamespace->selectOptionFormValues[$field_value]] = $mp_id;
                }
                if ($field_value == 'username') {
                    $cardData[$sessionNamespace->selectOptionFormValues[$field_value]] = $field_value;
                }
            }
            $cardData['card_status'] = $values['card_status'];
            $cardData['logo'] = $values['logo'];
            $cardData['card_bg_image'] = $values['card_bg_image'];
            $cardData['label_color'] = $values['label_color'];
            $cardData['info_color'] = $values['info_color'];
            $cardData['label_font'] = $values['label_font'];
            $cardData['info_font'] = $values['info_font'];
            $cardData['logo_select'] = $values['logo_select'];
            include_once APPLICATION_PATH . '/application/modules/Mcard/controllers/license/license2.php';

            $this->_helper->redirector->gotoRoute(array('action' => 'index', 'level_id' => $this->_getParam('level_id'), 'mptype_id' => $this->_getParam('mptype_id')));
        }
        $form->level_id->setValue($level_id);
        $form->mptype_id->setValue($mp_id);
        //Naviation code
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('mcard_admin_main', array(), 'mcard_admin_main_level');
        // Userdata
        $this->view->userCard = $data = Engine_Api::_()->mcard()->showCard($level_id, $user_id, $mp_id);
    }

}