<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: AdminSettingsController.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesvideo_AdminSettingsController extends Core_Controller_Action_Admin {

  protected $_pluginName = 'Advanced Videos & Channels Plugin';

  public function indexAction() {
		
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesvideo_admin_main', array(), 'sesvideo_admin_main_settings');

    // Check ffmpeg path for correctness
    if (function_exists('exec')) {
      $ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->video_ffmpeg_path;

      $output = null;
      $return = null;
      if (!empty($ffmpeg_path)) {
        exec($ffmpeg_path . ' -version', $output, $return);
      }
      // Try to auto-guess ffmpeg path if it is not set correctly
      $ffmpeg_path_original = $ffmpeg_path;
      if (empty($ffmpeg_path) || $return > 0 || stripos(join('', $output), 'ffmpeg') === false) {
        $ffmpeg_path = null;
        // Windows
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
          // @todo
        }
        // Not windows
        else {
          $output = null;
          $return = null;
          @exec('which ffmpeg', $output, $return);
          if (0 == $return) {
            $ffmpeg_path = array_shift($output);
            $output = null;
            $return = null;
            exec($ffmpeg_path . ' -version 2>&1', $output, $return);
            if ($output == null) {
              $ffmpeg_path = null;
            }
          }
        }
      }
      if ($ffmpeg_path != $ffmpeg_path_original) {
        Engine_Api::_()->getApi('settings', 'core')->video_ffmpeg_path = $ffmpeg_path;
      }
    }

    // Make form
    $this->view->form = $form = new Sesvideo_Form_Admin_Global();
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();
      
      if($values['sesvideoset_landingpage'] == 1) {
	      $db->query('DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`page_id` = 3');
	      $this->landingPageSetup();
      }

      if (isset($values['sesvideo_artistlink']))
        $values['sesvideo_artistlink'] = serialize($values['sesvideo_artistlink']);
      else
        $values['sesvideo_artistlink'] = serialize(array());
      $oldYoutubeApikey = Engine_Api::_()->getApi('settings', 'core')->getSetting('video.youtube.apikey');

      if (!empty($values['video_youtube_apikey']) && $values['video_youtube_apikey'] != $oldYoutubeApikey) {
        $response = $this->verifyYotubeApiKey($values['video_youtube_apikey']);
        if (!empty($response['errors'])) {
          $error_message = array('Invalid API Key');
          foreach ($response['errors'] as $error) {
            $error_message[] = "Error Reason (" . $error['reason'] . '): ' . $error['message'];
          }
          return $form->video_youtube_apikey->addErrors($error_message);
        }
      }

      // Check ffmpeg path
      if (!empty($values['video_ffmpeg_path'])) {
        if (function_exists('exec')) {
          $ffmpeg_path = $values['video_ffmpeg_path'];
          $output = null;
          $return = null;
          exec($ffmpeg_path . ' -version', $output, $return);

          if ($return > 0 && $output != NULL) {
            $form->video_ffmpeg_path->addError('FFMPEG path is not valid or does not exist');
            $values['video_ffmpeg_path'] = '';
          }
        } else {
          $form->video_ffmpeg_path->addError('The exec() function is not available. The ffmpeg path has not been saved.');
          $values['video_ffmpeg_path'] = '';
        }
      }
      include_once APPLICATION_PATH . "/application/modules/Sesvideo/controllers/License.php";
      if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sesvideo.pluginactivated')) {
        foreach ($values as $key => $value) {
          if (is_null($value) || $value == '')
            continue;
          Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
        }
        $form->addNotice('Your changes have been saved.');
        $this->_helper->redirector->gotoRoute(array());
      }
    }
  }

 // for default installation
  function setCategoryPhoto($file, $cat_id, $resize = false) {
    $fileName = $file;
    $name = basename($file);
    $extension = ltrim(strrchr($fileName, '.'), '.');
    $base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $params = array(
        'parent_type' => 'sesvideo_category',
        'parent_id' => $cat_id,
        'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
        'name' => $name,
    );

    // Save
    $filesTable = Engine_Api::_()->getDbtable('files', 'storage');
    if ($resize) {
      // Resize image (main)
      $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_poster.' . $extension;
      $image = Engine_Image::factory();
      $image->open($file)
              ->resize(800, 800)
              ->write($mainPath)
              ->destroy();

      // Resize image (normal) make same image for activity feed so it open in pop up with out jump effect.
      $normalPath = $path . DIRECTORY_SEPARATOR . $base . '_thumb.' . $extension;
      $image = Engine_Image::factory();
      $image->open($file)
              ->resize(500, 500)
              ->write($normalPath)
              ->destroy();
    } else {
      $mainPath = $path . DIRECTORY_SEPARATOR . $base . '_poster.' . $extension;
      copy($file, $mainPath);
    }
    if ($resize) {
      // normal main  image resize
      $normalMainPath = $path . DIRECTORY_SEPARATOR . $base . '_icon.' . $extension;
      $image = Engine_Image::factory();
      $image->open($file)
              ->resize(100, 100)
              ->write($normalMainPath)
              ->destroy();
    } else {
      $normalMainPath = $path . DIRECTORY_SEPARATOR . $base . '_icon.' . $extension;
      copy($file, $normalMainPath);
    }
    // Store
    try {
      $iMain = $filesTable->createFile($mainPath, $params);
      if ($resize) {
        $iIconNormal = $filesTable->createFile($normalPath, $params);
        $iMain->bridge($iIconNormal, 'thumb.thumb');
      }
      $iNormalMain = $filesTable->createFile($normalMainPath, $params);
      $iMain->bridge($iNormalMain, 'thumb.icon');
    } catch (Exception $e) {
      die;
      // Remove temp files
      @unlink($mainPath);
      if ($resize) {
        @unlink($normalPath);
      }
      @unlink($normalMainPath);
      // Throw
      if ($e->getCode() == Storage_Model_DbTable_Files::SPACE_LIMIT_REACHED_CODE) {
        throw new Sesvideo_Model_Exception($e->getMessage(), $e->getCode());
      } else {
        throw $e;
      }
    }
    // Remove temp files
    @unlink($mainPath);
    if ($resize) {
      @unlink($normalPath);
    }
    @unlink($normalMainPath);
    // Update row
    // Delete the old file?
    if (!empty($tmpRow)) {
      $tmpRow->delete();
    }
    return $iMain->file_id;
  }

  private function verifyYotubeApiKey($key) {
    $option = array(
        'part' => 'id',
        'key' => $key,
        'maxResults' => 1
    );
    $url = "https://www.googleapis.com/youtube/v3/search?" . http_build_query($option, 'a', '&');
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $json_response = curl_exec($curl);
    curl_close($curl);
    $responseObj = Zend_Json::decode($json_response);
    if (empty($responseObj['error'])) {
      return array('success' => 1);
    }
    return $responseObj['error'];
  }

  //Manage profile field mapping for video categories data .
  public function manageAction() {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesvideo_admin_main', array(), 'sesvideo_admin_main_categories');
    $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesvideo_admin_categories', array(), 'sesvideo_admin_main_subprofilemaps');
    $options_table = Engine_Api::_()->getDbtable('options', 'sesvideo');
    $categories_table = Engine_Api::_()->getDbtable('categories', 'sesvideo');
    //Refer: application/modules/User/Plugin/Signup/Fields.php
    $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('video');
    $this->view->totalProfileTypes = 1;
    if (Count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
      $profileTypeField = $topStructure[0]->getChild();
      $options = $profileTypeField->getOptions();
      $this->view->totalProfileTypes = Count($options);
    }
    $sescategories = array();
    $select = $categories_table->select()->from($categories_table->info('name'), array('category_id', 'category_name', 'profile_type'))->where('parent_id = ?', 0)->where('subcat_id = ?', 0)->where('subsubcat_id = ?', 0);
    $category_info = $categories_table->fetchAll($select);
    foreach ($category_info as $value) {
      $seslabel = '-----';
      if (!empty($value->profile_type)) {
        $seslabel = $options_table->getOptionsLabel($value->profile_type);
      }
      $sescategories[] = $category_array = array(
          'profile_type_id' => $value->profile_type,
          'category_id' => $value->category_id,
          'profile_type_label' => $seslabel,
          'category_name' => $value->category_name,
      );
    }
    $this->view->sescategories = $sescategories;
  }

  public function catmappingAction() {
    $this->_helper->layout->setLayout('admin-simple');
    $this->view->category_id = $sescategory_id = $this->_getParam('category_id');
    $this->view->form = $sesform = new Sesvideo_Form_Admin_Settings_Catmap();
    $sescategory = Engine_Api::_()->getItem('sesvideo_category', $sescategory_id);
    if ($this->getRequest()->isPost() && $sesform->isValid($this->getRequest()->getPost())) {
      $sesvalues = $sesform->getValues();
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        $sescategory->profile_type = $sesvalues['profile_type'];
        $sescategory->save();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('You have successfully mapped this category with porfile type.'))
      ));
    }
    $this->renderScript('admin-settings/catmapping.tpl');
  }

  public function removemappingAction() {
    $this->_helper->layout->setLayout('admin-simple');
    $this->view->category_id = $sescategory_id = $this->_getParam('category_id');
    $this->view->form = $form = new Sesvideo_Form_Admin_Settings_Removemapping();
    $this->view->category = $sescategory = Engine_Api::_()->getItem('sesvideo_category', $sescategory_id);
    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        if (!empty($sescategory_id))
          Engine_Api::_()->getDbtable('categories', 'sesvideo')->update(array('profile_type' => 0), array('category_id = ?' => $sescategory_id));
        $sescategory->profile_type = 0;
        $sescategory->save();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Yuou have successfully deleted mapping with categories.'))
      ));
    }
    $this->renderScript('admin-settings/removemapping.tpl');
  }

  public function levelChanelAction() {
    $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesvideo_admin_level', array(), 'sesvideo_admin_main_level_chanel');
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesvideo_admin_main', array(), 'sesvideo_admin_main_level');
    // Get level id
    if (null !== ($id = $this->_getParam('id'))) {
      $level = Engine_Api::_()->getItem('authorization_level', $id);
    } else {
      $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
    }
    if (!$level instanceof Authorization_Model_Level) {
      throw new Engine_Exception('missing level');
    }
    $level_id = $id = $level->level_id;
    // Make form
    $this->view->form = $form = new Sesvideo_Form_Admin_Settings_Levelchanel(array(
        'public' => ( in_array($level->type, array('public')) ),
        'moderator' => ( in_array($level->type, array('admin', 'moderator')) ),
    ));
    $form->level_id->setValue($id);
    // Populate values
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    $form->populate($permissionsTable->getAllowed('sesvideo_chanel', $id, array_keys($form->getValues())));
    // Check post
    if (!$this->getRequest()->isPost()) {
      return;
    }
    // Check validitiy
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }
    // Process
    $values = $form->getValues();
    $db = $permissionsTable->getAdapter();
    $db->beginTransaction();
    try {
      // Set permissions
      $permissionsTable->setAllowed('sesvideo_chanel', $id, $values);
      // Commit
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $form->addNotice('Your changes have been saved.');
  }

  public function levelChanelphotoAction() {
    $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesvideo_admin_level', array(), 'sesvideo_admin_main_level_chanelphoto');
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesvideo_admin_main', array(), 'sesvideo_admin_main_level');
    // Get level id
    if (null !== ($id = $this->_getParam('id'))) {
      $level = Engine_Api::_()->getItem('authorization_level', $id);
    } else {
      $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
    }
    if (!$level instanceof Authorization_Model_Level) {
      throw new Engine_Exception('missing level');
    }
    $level_id = $id = $level->level_id;
    // Make form
    $this->view->form = $form = new Sesvideo_Form_Admin_Settings_Levelchanelphoto(array(
        'public' => ( in_array($level->type, array('public')) ),
        'moderator' => ( in_array($level->type, array('admin', 'moderator')) ),
    ));
    $form->level_id->setValue($id);
    // Populate values
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    $form->populate($permissionsTable->getAllowed('sesvideo_chanelphoto', $id, array_keys($form->getValues())));
    // Check post
    if (!$this->getRequest()->isPost()) {
      return;
    }
    // Check validitiy
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }
    // Process
    $values = $form->getValues();
    $db = $permissionsTable->getAdapter();
    $db->beginTransaction();
    try {
      // Set permissions
      $permissionsTable->setAllowed('sesvideo_chanelphoto', $id, $values);
      // Commit
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $form->addNotice('Your changes have been saved.');
  }

  public function levelAction() {
    // Make navigation
    $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesvideo_admin_level', array(), 'sesvideo_admin_main_level_video');
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesvideo_admin_main', array(), 'sesvideo_admin_main_level');
    // Get level id
    if (null !== ($id = $this->_getParam('id'))) {
      $level = Engine_Api::_()->getItem('authorization_level', $id);
    } else {
      $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
    }
    if (!$level instanceof Authorization_Model_Level) {
      throw new Engine_Exception('missing level');
    }
    $level_id = $id = $level->level_id;
    // Make form
    $this->view->form = $form = new Sesvideo_Form_Admin_Settings_Level(array(
        'public' => ( in_array($level->type, array('public')) ),
        'moderator' => ( in_array($level->type, array('admin', 'moderator')) ),
    ));
    $form->level_id->setValue($id);
    // Populate values
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    $form->populate($permissionsTable->getAllowed('video', $id, array_keys($form->getValues())));
    // Check post
    if (!$this->getRequest()->isPost()) {
      return;
    }
    // Check validitiy
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }
    // Process
    $values = $form->getValues();
    $db = $permissionsTable->getAdapter();
    $db->beginTransaction();
    try {
      // Set permissions
      $permissionsTable->setAllowed('video', $id, $values);
      // Commit
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
    $form->addNotice('Your changes have been saved.');
  }

  public function utilityAction() {
    if (defined('_ENGINE_ADMIN_NEUTER') && _ENGINE_ADMIN_NEUTER) {
      return $this->_helper->redirector->gotoRoute(array(), 'admin_default', true);
    }
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sesvideo_admin_main', array(), 'sesvideo_admin_main_utility');
    $ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->video_ffmpeg_path;
    if (function_exists('shell_exec')) {
      // Get version
      $this->view->version = $version = shell_exec(escapeshellcmd($ffmpeg_path) . ' -version 2>&1');
      $command = "$ffmpeg_path -formats 2>&1";
      $this->view->format = $format = shell_exec(escapeshellcmd($ffmpeg_path) . ' -formats 2>&1')
              . shell_exec(escapeshellcmd($ffmpeg_path) . ' -codecs 2>&1');
    }
  }

  //site statis for sesvideo plugin 
  public function statisticAction() {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sesvideo_admin_main', array(), 'sesvideo_admin_main_statistic');

    $videoTable = Engine_Api::_()->getDbtable('videos', 'sesvideo');
    $videoTableName = $videoTable->info('name');

    //Total Videos
    $select = $videoTable->select()->from($videoTableName, 'count(*) AS totalvideo');
    $this->view->totalvideo = $select->query()->fetchColumn();

    //Total featured video
    $select = $videoTable->select()->from($videoTableName, 'count(*) AS totalfeatured')->where('is_featured =?', 1);
    $this->view->totalvideofeatured = $select->query()->fetchColumn();

    //Total sponsored video
    $select = $videoTable->select()->from($videoTableName, 'count(*) AS totalsponsored')->where('is_sponsored =?', 1);
    $this->view->totalvideosponsored = $select->query()->fetchColumn();

    //Total favourite video
    $select = $videoTable->select()->from($videoTableName, 'count(*) AS totalfavourite')->where('favourite_count <>?', 0);
    $this->view->totalvideofavourite = $select->query()->fetchColumn();

    //Total rated video
    $select = $videoTable->select()->from($videoTableName, 'count(*) AS totalrated')->where('rating <>?', 0);
    $this->view->totalvideorated = $select->query()->fetchColumn();

    //Video Chanels
    $chanelTable = Engine_Api::_()->getDbtable('chanels', 'sesvideo');
    $chanelTableName = $chanelTable->info('name');

    //Total chanels
    $select = $chanelTable->select()->from($chanelTableName, 'count(*) AS totalchanels')->where('chanel_id != ?', '');
    $this->view->totalchanel = $select->query()->fetchColumn();

    //Total featured chanel
    $select = $chanelTable->select()->from($chanelTableName, 'count(*) AS totalfeaturedchanel')->where('is_featured =?', 1);
    $this->view->totalchanelfeatured = $select->query()->fetchColumn();

    //Total sponsored chanel
    $select = $chanelTable->select()->from($chanelTableName, 'count(*) AS totalsponsoredchanel')->where('is_sponsored =?', 1);
    $this->view->totalchanelsponsored = $select->query()->fetchColumn();

    //Total favourite chanel
    $select = $chanelTable->select()->from($chanelTableName, 'count(*) AS totalfavourite')->where('favourite_count <>?', 0);
    $this->view->totalchanelfavourite = $select->query()->fetchColumn();

    //Video Playlists
    $playlistTable = Engine_Api::_()->getDbtable('playlists', 'sesvideo');
    $playlistTableName = $playlistTable->info('name');

    //Total chanels
    $select = $playlistTable->select()->from($playlistTable, 'count(*) AS totalplaylist')->where('playlist_id != ?', '');
    $this->view->totalplaylist = $select->query()->fetchColumn();

    //Total featured chanel
    $select = $playlistTable->select()->from($playlistTable, 'count(*) AS totalfeaturedplaylist')->where('is_featured =?', 1);
    $this->view->totalplaylistfeatured = $select->query()->fetchColumn();

    //Total sponsored chanel
    $select = $playlistTable->select()->from($playlistTable, 'count(*) AS totalsponsoredplaylist')->where('is_sponsored =?', 1);
    $this->view->totalplaylistsponsored = $select->query()->fetchColumn();
    $this->view->plugin_name = $this->_pluginName;
  }

  //Manage all artists
  public function artistsAction() {

    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesvideo_admin_main', array(), 'sesvideo_admin_main_artist');
    $artistsTable = Engine_Api::_()->getDbTable('artists', 'sesvideo');
    $select = $artistsTable->select()->order('order ASC');
    $this->view->paginator = $artistsTable->fetchAll($select);
  }

  //Add new artist
  public function addArtistAction() {

    //Set Layout
    $this->_helper->layout->setLayout('admin-simple');

    //Render Form
    $this->view->form = $form = new Sesvideo_Form_Admin_AddArtist();

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      $values = $form->getValues();
      if (empty($values['artist_photo'])) {
        $error = Zend_Registry::get('Zend_Translate')->_('Artist Photo * Please choose a photo for artist. it is required.');
        $form->getDecorator('errors')->setOption('escape', false);
        $form->addError($error);
        return;
      }

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        $row = Engine_Api::_()->getDbtable('artists', 'sesvideo')->createRow();
        $row->name = $values["name"];
        $row->overview = $values["overview"];
        $row->owner_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $row->save();

        //Upload categories photo
        if (isset($_FILES['artist_photo'])) {
          $photoFileIcon = $this->setPhoto($form->artist_photo, $row->artist_id);
          if (!empty($photoFileIcon->file_id))
            $row->artist_photo = $photoFileIcon->file_id;
        }
        $row->save();
        $db->commit();

        if ($row->artist_id)
          $db->update('engine4_sesvideo_artists', array('order' => $row->artist_id), array('artist_id = ?' => $row->artist_id));
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('You have successfully add artist.'))
      ));
    }
  }

  //Edit Artist
  public function editArtistAction() {

    $this->_helper->layout->setLayout('admin-simple');

    //Get artist id
    $artistTable = Engine_Api::_()->getItem('sesvideo_artists', $this->_getParam('artist_id'));

    $this->view->form = $form = new Sesvideo_Form_Admin_EditArtist();
    $form->button->setLabel("Save");

    //Populate the form values
    $form->populate($artistTable->toArray());

    //Check post
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

      $values = $form->getValues();
      if (empty($values['artist_photo']))
        unset($values['artist_photo']);

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

        $artistTable->name = $values["name"];
        $artistTable->overview = $values["overview"];
        $artistTable->save();

        if (isset($_FILES['artist_photo']) && !empty($_FILES['artist_photo']['name'])) {
          $previous_artist_photo = $artistTable->artist_photo;
          $photoFileIcon = $this->setPhoto($form->artist_photo, $artistTable->artist_id);
          if (!empty($photoFileIcon->file_id)) {
            if ($previous_artist_photo) {
              $file = Engine_Api::_()->getItem('storage_file', $previous_artist_photo);
              $file->delete();
            }
            $artistTable->artist_photo = $photoFileIcon->file_id;
            $artistTable->save();
          }
        }

        if (isset($values['remove_artist_photo']) && !empty($values['remove_artist_photo'])) {
          $file = Engine_Api::_()->getItem('storage_file', $artistTable->artist_photo);
          $artistTable->artist_photo = 0;
          $artistTable->save();
          $file->delete();
        }
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      return $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => 10,
                  'parentRefresh' => 10,
                  'messages' => array('You have successfully edit artist entry.')
      ));
    }
    //Output
    $this->renderScript('admin-settings/edit-artist.tpl');
  }

  //Delete artist
  public function deleteArtistAction() {

    $this->_helper->layout->setLayout('admin-simple');

    $this->view->form = $form = new Sesbasic_Form_Admin_Delete();
    $form->setTitle('Delete Artist?');
    $form->setDescription('Are you sure that you want to delete this artist? It will not be recoverable after being deleted. ');
    $form->submit->setLabel('Delete');

    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {
        Engine_Api::_()->getDbtable('artists', 'sesvideo')->delete(array('artist_id =?' => $this->_getParam('artist_id')));
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('You have successfully delete entry'))
      ));
    }
  }

  public function multiDeleteArtistsAction() {

    if ($this->getRequest()->isPost()) {
      $values = $this->getRequest()->getPost();
      foreach ($values as $key => $value) {
        if ($key == 'delete_' . $value) {
          $explodedKey = explode('_', $key);
          $artists = Engine_Api::_()->getItem('sesvideo_artists', $explodedKey[1]);
          $artists->delete();
        }
      }
    }
    $this->_helper->redirector->gotoRoute(array('action' => 'artists'));
  }

  public function orderAction() {

    if (!$this->getRequest()->isPost())
      return;

    $artistsTable = Engine_Api::_()->getDbtable('artists', 'sesvideo');
    $artists = $artistsTable->fetchAll($artistsTable->select());
    foreach ($artists as $artist) {
      $order = $this->getRequest()->getParam('artists_' . $artist->artist_id);
      if (!$order)
        $order = 999;
      $artist->order = $order;
      $artist->save();
    }
    return;
  }

  public function setPhoto($photo, $cat_id) {


    if ($photo instanceof Zend_Form_Element_File)
      $file = $photo->getFileName();
    else if (is_array($photo) && !empty($photo['tmp_name']))
      $file = $photo['tmp_name'];
    else if (is_string($photo) && file_exists($photo))
      $file = $photo;
    else if ($photo)
      $file = $photo;
    else
      return;

    if (empty($file))
      return;

    //Get photo details 
    $name = basename($file);
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    $mainName = $path . '/' . $name;

    //Get viewer id
    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $photo_params = array(
        'parent_id' => $cat_id,
        'parent_type' => "sesvideo_artist",
    );

    //Resize image work
    $image = Engine_Image::factory();
    $image->open($file);
    $image->open($file)
            ->resample(0, 0, $image->width, $image->height, $image->width, $image->height)
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

  public function manageWidgetizePageAction() {

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesvideo_admin_main', array(), 'sesvideo_admin_main_managepages');

    $this->view->pagesArray = array('sesvideo_index_welcome','sesvideo_index_tags', 'sesvideo_artist_view', 'sesvideo_artist_browse', 'sesvideo_index_locations', 'sesvideo_playlist_view', 'sesvideo_playlist_browse', 'sesvideo_chanel_view', 'sesvideo_chanel_index', 'sesvideo_index_home', 'sesvideo_index_manage', 'sesvideo_index_create', 'sesvideo_chanel_create', 'sesvideo_category_index', 'sesvideo_category_browse', 'sesvideo_chanel_category', 'sesvideo_chanel_browse', 'sesvideo_index_browse', 'sesvideo_index_view', 'sesvideo_index_browse-pinboard', 'sesvideo_index_edit');
  }
  
    
  public function defaultArtists() {
  
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();		
		$db->query('INSERT IGNORE INTO `engine4_sesvideo_artists` (`artist_id`, `name`, `overview`, `artist_photo`,`owner_id` , `order`, `rating`, `favourite_count`, `offtheday`, `starttime`, `endtime`) VALUES(1, "Enrique Iglesias", "Enrique Miguel Iglesias Preysler, simply known as Enrique Iglesias or King Of Latin Pop, is a Spanish singer, songwriter, actor, and record producer.", 0,1, 999, 0, 0, 0, "0000-00-00", "0000-00-00"),
		(2, "Backstreet Boys", "The Backstreet Boys are an American vocal group, formed in Orlando, Florida in 1993. The group consists of A. J. McLean, Howie Dorough, Nick Carter, Kevin Richardson, and Brian Littrell.", 0,1, 999, 0, 0, 0, "0000-00-00", "0000-00-00"),
		(3, "Britney Spears", "Britney Jean Spears is an American singer and actress. Born in McComb, Mississippi, and brought up in Kentwood, Louisiana, she performed acting roles in stage productions and television shows as a child before signing with Jive Records in 1997.", 0,1, 999, 0, 0, 0, "0000-00-00", "0000-00-00"),
		(4, "Shakira", "Shakira Isabel Mebarak Ripoll, is a Colombian singer, songwriter, dancer, record producer, choreographer, and model.", 0,1, 999, 0, 0, 0, "0000-00-00", "0000-00-00"),
		(5, "Michael Jackson", "Michael Joseph Jackson (August 29, 1958 – June 25, 2009) was an American singer, songwriter, record producer, dancer, and actor. Called the King of Pop, his contributions to music and dance, along with his publicized personal life, made him a global figure in popular culture for over four decades.", 0,1, 999, 0, 0, 0, "0000-00-00", "0000-00-00"),
		(6, "Celine Dion", "Cline Marie Claudette Dion, CC OQ ChLD is a Canadian singer, songwriter, entrepreneur and occasional actress.", 0, 999,1, 0, 0, 0, "0000-00-00", "0000-00-00"),
		(7, "Chris Brown", "Christopher Maurice Chris Brown is an American recording artist, dancer, and actor. Born in Tappahannock, Virginia, he taught himself to sing and dance at a young age and was involved in his church choir and several local talent shows.", 0, 999,1, 0, 0, 0, "0000-00-00", "0000-00-00"),
		(8, "Taylor Swift", "Taylor Alison Swift is an American singer songwriter and actress. Raised in Wyomissing, Pennsylvania, Swift moved to Nashville, Tennessee, at the age of 14 to pursue a career in country music.", 0, 999,1, 0, 0, 0, "0000-00-00", "0000-00-00"),
		(9, "Selena Marie Gomez", "Selena Marie Gomez is an American actress and singer. Born and raised in Grand Prairie, Texas, she was first featured on the childrens series Barney Friends in the early 2000s.", 0, 999,1, 0, 0, 0, "0000-00-00", "0000-00-00"),
		(10, "Demi Lovato", "Demetria Devonne Demi Lovato is an American actress, singer, and songwriter who made her debut as a child actress in Barney & Friends.", 0,1, 999, 0, 0, 0, "0000-00-00", "0000-00-00"),
		(11, "Katy Perry", "Katheryn Elizabeth Hudson (born October 25, 1984), better known by her stage name Katy Perry, is an American singer and songwriter. She had limited exposure to secular music during her childhood and pursued a career in gospel music as a teenager. Perry signed with Red Hill Records, and released her debut studio album, Katy Hudson, in 2001. She moved to Los Angeles the following year to venture into secular music. After being dropped by The Island Def Jam Music Group and Columbia Records, she signed a deal with Capitol Records in April 2007.", 0,1, 999, 0, 0, 0, "0000-00-00", "0000-00-00"),
		(12, "Victoria Justice", "Victoria Dawn Justice is an American actress and singer-songwriter. She debuted as an actress at the age of 10 and has since appeared in several films and television series including the Nickelodeon series Zoey 101 and Victorious.", 0,1, 999, 0, 0, 0, "0000-00-00", "0000-00-00"),
		(13, "Beyonce", "", 0,1, 999, 0, 0, 0, "0000-00-00", "0000-00-00"),
		(14, "Rihanna", "", 0,1, 999, 0, 0, 0, "0000-00-00", "0000-00-00"),
		(15, "Miley Cyrus", "", 0,1, 999, 0, 0, 0, "0000-00-00", "0000-00-00"),
		(16, "Carrie Underwood", "", 0,1, 999, 0, 0, 0, "0000-00-00", "0000-00-00"),
		(17, "Usher", "",0, 1, 999, 0, 0, 0, "0000-00-00", "0000-00-00"),
		(18, "Bruno Mars", "", 0,1, 999, 0, 0, 0, "0000-00-00", "0000-00-00"),
		(19, "Adam Levine", "", 0,1, 999, 0, 0, 0, "0000-00-00", "0000-00-00"),
		(20, "Drake", "", 0,1, 999, 0, 0, 0, "0000-00-00", "0000-00-00"),
		(21, "Taio Cruz", "", 0,1, 999, 0, 0, 0, "0000-00-00", "0000-00-00"),
		(22, "Pitbull", "", 0,1, 999, 0, 0, 0, "0000-00-00", "0000-00-00"),
		(23, "Akon", "", 0,1, 999, 0, 0, 0, "0000-00-00", "0000-00-00"),
		(24, "Cheryl Cole", "", 0,1, 999, 0, 0, 0, "0000-00-00", "0000-00-00");');
  }
	
	public function landingPageSetup() {
	
		$page_id = 3;
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$widgetOrder = 1;
		// Insert top
	  $db->insert('engine4_core_content', array(
	    'type' => 'container',
	    'name' => 'top',
	    'page_id' => $page_id,
	    'order' => 1,
	  ));
	  $top_id = $db->lastInsertId();
	  
	  // Insert main
	  $db->insert('engine4_core_content', array(
	    'type' => 'container',
	    'name' => 'main',
	    'page_id' => $page_id,
	    'order' => 2,
	  ));
	  $main_id = $db->lastInsertId();
	  
	  // Insert top-middle
	  $db->insert('engine4_core_content', array(
	    'type' => 'container',
	    'name' => 'middle',
	    'page_id' => $page_id,
	    'parent_content_id' => $top_id,
	  ));
	  $top_middle_id = $db->lastInsertId();
	  
	  // Insert main-middle
	  $db->insert('engine4_core_content', array(
	    'type' => 'container',
	    'name' => 'middle',
	    'page_id' => $page_id,
	    'parent_content_id' => $main_id,
	    'order' => 2,
	  ));
	  $main_middle_id = $db->lastInsertId();
	  
		//Insert menu
		$db->insert('engine4_core_content', array(
		'type' => 'widget',
		'name' => 'sesvideo.slideshow',
		'page_id' => $page_id,
		'parent_content_id' => $top_middle_id,
		'order' => $widgetOrder++,
		'params' => '{"gallery_id":"1","full_width":"1","logo":"1","main_navigation":"1","mini_navigation":"1","autoplay":"1","thumbnail":"1","searchEnable":"1","height":"670","title":"","nomobile":"0","name":"sesvideo.slideshow"}',
		));

		//Insert content
		$db->insert('engine4_core_content', array(
		'type' => 'widget',
		'name' => 'sesbasic.simple-html-block',
		'page_id' => $page_id,
		'parent_content_id' => $main_middle_id,
		'order' => $widgetOrder++,
		'params' => '{"bodysimple":"<div style=\"text-align: center;font-size: 34px;  margin: 30px; 30px\">Watch the world\u2019s best videos from our <br \/>passionate community<\/div>","show_content":"1","title":"","nomobile":"0","name":"sesbasic.simple-html-block"}',
		));

		$db->insert('engine4_core_content', array(
		'type' => 'widget',
		'name' => 'sesvideo.tabbed-widget-video',
		'page_id' => $page_id,
		'parent_content_id' => $main_middle_id,
		'order' => $widgetOrder++,
		'params' => '{"enableTabs":["grid"],"openViewType":"grid","viewTypeStyle":"mouseover","showTabType":"1","show_criteria":["watchLater","favouriteButton","location","playlistAdd","likeButton","socialSharing","like","favourite","comment","rating","view","title","category","by","duration"],"pagging":"button","title_truncation_grid":"39","title_truncation_list":"45","title_truncation_pinboard":"45","limit_data_pinboard":"10","limit_data_list":"10","limit_data_grid":"6","show_limited_data":"yes","description_truncation_list":"45","description_truncation_grid":"45","description_truncation_pinboard":"45","height_grid":"270","width_grid":"386","height_list":"230","width_list":"260","width_pinboard":"250","search_type":["recentlySPcreated"],"recentlySPupdated_order":"1","recentlySPcreated_label":"Recently Created","mostSPviewed_order":"2","mostSPviewed_label":"Most Viewed","mostSPliked_order":"3","mostSPliked_label":"Most Liked","mostSPcommented_order":"4","mostSPcommented_label":"Most Commented","mostSPrated_order":"5","mostSPrated_label":"Most Rated","mostSPfavourite_order":"6","mostSPfavourite_label":"Most Favourite","hot_order":"7","hot_label":"Most Hot","featured_order":"6","featured_label":"Featured","sponsored_order":"7","sponsored_label":"Sponsored","title":"","nomobile":"0","name":"sesvideo.tabbed-widget-video"}',
		));

		$array['bodysimple'] = '<div style="text-align: center;margin-top:50px; box-shadow:inset 0 1px 0 rgba(255,255,255,.1),0 1px 0 rgba(8,32,84,.1);padding-bottom: 70px;"><a class="sesbasic_animation" href="/videos/browse/" onmouseover="this.style.backgroundColor=\'#00a8f2\'" onmouseout="this.style.backgroundColor=\'#345\'" style="padding:.9em;font-size: 18px;border: 1px solid #345;text-align: center;background:#345;color:#fff;border-radius:4px;cursor:pointer;text-decoration:none;">Watch All Videos</a></div>';
		$array['show_content'] = 1;
		$array['title'] = '';
		$array['nomobile'] = 0;
		$array['name'] = 'sesbasic.simple-html-block';
		$db->insert('engine4_core_content', array(
		'type' => 'widget',
		'name' => 'sesbasic.simple-html-block',
		'page_id' => $page_id,
		'parent_content_id' => $main_middle_id,
		'order' => $widgetOrder++,
		'params' => json_encode($array),
		));



		$db->insert('engine4_core_content', array(
		'type' => 'widget',
		'name' => 'sesbasic.simple-html-block',
		'page_id' => $page_id,
		'parent_content_id' => $main_middle_id,
		'order' => $widgetOrder++,
		'params' => '{"bodysimple":"<div style=\"font-size: 34px;margin-bottom: 50px;  margin-top: 50px;text-align: center;\">Here, people share your passions for Public Interest<\/span><\/div>","show_content":"1","title":"","nomobile":"0","name":"sesbasic.simple-html-block"}',
		));

		$db->insert('engine4_core_content', array(
		'type' => 'widget',
		'name' => 'sesvideo.video-category',
		'page_id' => $page_id,
		'parent_content_id' => $main_middle_id,
		'order' => $widgetOrder++,
		'params' => '{"height":"230","width":"290","limit":"8","video_required":"0","criteria":"alphabetical","show_criteria":["title","icon"],"mouse_over_title":"1","title":"","nomobile":"0","name":"sesvideo.video-category"}',
		));
		$array['bodysimple'] = '<div style="text-align: center;margin-top:50px; box-shadow:inset 0 1px 0 rgba(255,255,255,.1),0 1px 0 rgba(8,32,84,.1);padding-bottom: 70px;"><a class="sesbasic_animation" href="/videos/categories/" onmouseover="this.style.backgroundColor=\'#00a8f2\'" onmouseout="this.style.backgroundColor=\'#345\'" style="padding:.9em;font-size:18px;border:1px solid #345;text-align: center;background:#345;color:#fff;border-radius:4px;cursor:pointer;text-decoration:none;"> Browse All Categories</a></div><div style="font-size: 34px;text-align: center;margin-top:50px;">We are reimagined ... as a collection of interest-specific social channels.<br /><br /></div>';

		$array['show_content'] = 1;
		$array['title'] = '';
		$array['nomobile'] = 0;
		$array['name'] = 'sesbasic.simple-html-block';

		$db->insert('engine4_core_content', array(
		'type' => 'widget',
		'name' => 'sesbasic.simple-html-block',
		'page_id' => $page_id,
		'parent_content_id' => $main_middle_id,
		'order' => $widgetOrder++,
		'params' => json_encode($array),
		));


		$db->insert('engine4_core_content', array(
		'type' => 'widget',
		'name' => 'sesvideo.popular-artists',
		'page_id' => $page_id,
		'parent_content_id' => $main_middle_id,
		'order' => $widgetOrder++,
		'params' => '{"popularity":"favourite_count","viewType":"gridview","viewTypeStyle":"mouseover","height":"150","width":"188","limit":"12","title":"","nomobile":"0","name":"sesvideo.popular-artists"}',
		));
		$artists['bodysimple'] = '<div style="text-align: center;margin-top:50px;"><a class="sesbasic_animation" href="/videos/artists/" onmouseover="this.style.backgroundColor=\'#00a8f2\'" onmouseout="this.style.backgroundColor=\'#345\'" style="padding:.9em;font-size:18px;border:1px solid #345;text-align:center;background:#345;color:#fff;border-radius:4px;cursor:pointer;text-decoration:none;">Browse All Artists</a><br ><br ></div>';
		$artists['show_content'] = 1;
		$artists['title'] = '';
		$artists['nomobile'] = 0;
		$artists['name'] = 'sesbasic.simple-html-block';
		$db->insert('engine4_core_content', array(
		'type' => 'widget',
		'name' => 'sesbasic.simple-html-block',
		'page_id' => $page_id,
		'parent_content_id' => $main_middle_id,
		'order' => $widgetOrder++,
		'params' => json_encode($artists),
		));
	}
	
	public function handleThumbnail($type, $code = null) {
    switch ($type) {
      //youtube
      case "1":
        return "http://img.youtube.com/vi/$code/maxresdefault.jpg";
      //vimeo
      case "2":
        //thumbnail_medium
        $data = unserialize(file_get_contents("http://vimeo.com/api/v2/video/$code.php"));
        $thumbnail = $data[0]['thumbnail_large'];
        return $thumbnail;
      case "4":
        $data = @file_get_contents("https://api.dailymotion.com/video/$code?fields=thumbnail_url");
        if ($data != '') {
          $data = json_decode($data, true);
          $thumbnail_url = (isset($data['thumbnail_url']) && $data['thumbnail_url']) ? $data['thumbnail_url'] : '';
          return $thumbnail_url;
        }
    }
  }
	public function getVideoThumbnail($video,$thumb_splice,$file = false){
		$tmpDir = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary' . DIRECTORY_SEPARATOR . 'video';
		$thumbImage = $tmpDir . DIRECTORY_SEPARATOR . $video -> getIdentity() . '_thumb_image.jpg';
		$ffmpeg_path = Engine_Api::_() -> getApi('settings', 'core') -> video_ffmpeg_path;
		if (!@file_exists($ffmpeg_path) || !@is_executable($ffmpeg_path))
		{
			$output = null;
			$return = null;
			exec($ffmpeg_path . ' -version', $output, $return);
			if ($return > 0)
			{
				return 0;
			}
		}
		$file = Engine_Api::_()->getItemTable('storage_file')->getFile($video->file_id, null);
		$fileExe = (_ENGINE_SSL ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].$file->map();
		$output = PHP_EOL;
		$output .= $fileExe . PHP_EOL;
		$output .= $thumbImage . PHP_EOL;
		$thumbCommand = $ffmpeg_path . ' ' . '-i ' . escapeshellarg($fileExe) . ' ' . '-f image2' . ' ' . '-ss ' . $thumb_splice . ' ' . '-vframes ' . '1' . ' ' . '-v 2' . ' ' . '-y ' . escapeshellarg($thumbImage) . ' ' . '2>&1';
		// Process thumbnail
		$thumbOutput = $output . $thumbCommand . PHP_EOL . shell_exec($thumbCommand);
		// Check output message for success
		$thumbSuccess = true;
		if (preg_match('/video:0kB/i', $thumbOutput))
		{
			$thumbSuccess = false;
		}
		// Resize thumbnail
		if ($thumbSuccess && is_file($thumbImage))
		{
			try
			{
				$image = Engine_Image::factory();
				$image->open($thumbImage)->resize(500, 500)->write($thumbImage)->destroy();
				$thumbImageFile = Engine_Api::_()->storage()->create($thumbImage, array(
					'parent_id' => $video -> getIdentity(),
					'parent_type' => $video -> getType(),
					'user_id' => $video -> owner_id
					)
				);
				$video->photo_id = $thumbImageFile->file_id;
				$video->save();
				@unlink($thumbImage);
				return true;
			}
			catch (Exception $e)
			{
				throw $e;
				@unlink($thumbImage);
			}
		}
		 @unlink(@$thumbImage);
		 return false;
	}
	public function getVideoDuration($video,$file = false)
	{
		$duration = 0;
		if ($video)
		{
				$ffmpeg_path = Engine_Api::_() -> getApi('settings', 'core') -> video_ffmpeg_path;
				if (!@file_exists($ffmpeg_path) || !@is_executable($ffmpeg_path))
				{
					$output = null;
					$return = null;
					exec($ffmpeg_path . ' -version', $output, $return);
					if ($return > 0)
					{
						return 0;
					}
				}
				$file = Engine_Api::_()->getItemTable('storage_file')->getFile($video->file_id, null);
				$fileExe = (_ENGINE_SSL ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].$file->map();
				// Prepare output header
				$fileCommand = $ffmpeg_path . ' ' . '-i ' . escapeshellarg($fileExe) . ' ' . '2>&1';
				// Process thumbnail
				$fileOutput = shell_exec($fileCommand);
				// Check output message for success
				$infoSuccess = true;
				if (preg_match('/video:0kB/i', $fileOutput))
				{
					$infoSuccess = false;
				}
				// Resize thumbnail
				if ($infoSuccess)
				{ 
					// Get duration of the video to caculate where to get the thumbnail
					if (preg_match('/Duration:\s+(.*?)[.]/i', $fileOutput, $matches))
					{
						list($hours, $minutes, $seconds) = preg_split('[:]', $matches[1]);
						$duration = ceil($seconds + ($minutes * 60) + ($hours * 3600));
					}
				}
		}
		return $duration;
	}
	public function importThumbnailsAction() {
    $db = Engine_Db_Table::getDefaultAdapter();
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('sesvideo_admin_main', array(), 'sesvideo_admin_main_imoprtthumbnails');
    $setting = Engine_Api::_()->getApi('settings', 'core');
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sesvideo') && $setting->getSetting('sesvideo.pluginactivated')) {
	    $videoTable = Engine_Api::_()->getDbTable('videos', 'sesvideo');
	    $select = $videoTable->select()->where('importthumbnail =?',0)->order('video_id DESC')->limit(500);
	    $this->view->results = $results = $videoTable->fetchAll($select);
      if ($results && isset($_GET['is_ajax']) && $_GET['is_ajax']) {
        try {
					foreach($results as $result) {
						$type= $result->type;
							$video = Engine_Api::_()->getItem('sesvideo_video', $result->video_id);
							if($type != 3){
								$code = $result->code;
								$thumbnail = $this->handleThumbnail($type, $code);							
								
								$ext = ltrim(strrchr($thumbnail, '.'), '.');
								$thumbnail_parsed = @parse_url($thumbnail);
								if (@getimagesize($thumbnail)) {
		
									$valid_thumb = true;
								} else {
									if($type == 1) {
										$thumbnail = "http://img.youtube.com/vi/$code/hqdefault.jpg";
										if (@getimagesize($thumbnail)) {
											 $valid_thumb = true;
											 $thumbnail_parsed = @parse_url($thumbnail);
										} else {
										 $valid_thumb = false;
										}
									} else {
										$valid_thumb = false;
									}
									
								}
								if ($valid_thumb && $thumbnail && $ext && $thumbnail_parsed && in_array($ext, array('jpg', 'jpeg', 'gif', 'png'))) {
									$tmp_file = APPLICATION_PATH . '/temporary/link_' . md5($thumbnail) . '.' . $ext;
									$thumb_file = APPLICATION_PATH . '/temporary/link_thumb_' . md5($thumbnail) . '.' . $ext;
									$src_fh = fopen($thumbnail, 'r');
									$tmp_fh = fopen($tmp_file, 'w');
									stream_copy_to_stream($src_fh, $tmp_fh, 1024 * 1024 * 2);
									//resize video thumbnails
									$image = Engine_Image::factory();
									$image->open($tmp_file)
													->resize(500, 500)
													->write($thumb_file)
													->destroy();
									try {
										$thumbFileRow = Engine_Api::_()->storage()->create($thumb_file, array(
												'parent_type' => $video->getType(),
												'parent_id' => $video->getIdentity()
										));
										// Remove temp file
										@unlink($thumb_file);
										@unlink($tmp_file);
									} catch (Exception $e) {
										//silence 
									}
									$video->photo_id = $thumbFileRow->file_id;
									
									$video->save();
								}
								$video->importthumbnail = 1;
								$video->save();
						}else{
							$duration = $this->getVideoDuration($video);
							$thumb_splice = $duration / 2;
							//if(!$video->photo_id){
							$this->getVideoThumbnail($video,$thumb_splice);
							$video->importthumbnail = 1;
							$video->save();	
							//}
								
						}
					}
        } catch (Exception $e) {
          //$db->rollBack();
          throw $e;
        }
      }
    }
  }
}