<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Seshtmlbackground
 * @package    Seshtmlbackground
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: AdminSettingsController.php 2015-10-22 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Seshtmlbackground_AdminSettingsController extends Core_Controller_Action_Admin {
  public function indexAction() {	
    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('seshtmlbackground_admin_main', array(), 'seshtmlbackground_admin_main_settings');
    // Check ffmpeg path for correctness
    if (function_exists('exec')) {
      $ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->seshtmlbackground_ffmpeg_path;
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
        Engine_Api::_()->getApi('settings', 'core')->seshtmlbackground_ffmpeg_path = $ffmpeg_path;
      }
    }

    // Make form
    $this->view->form = $form = new Seshtmlbackground_Form_Admin_Global();
    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();
    
		// Check ffmpeg path
      if (!empty($values['seshtmlbackground_ffmpeg_path'])) {
        if (function_exists('exec')) {
          $ffmpeg_path = $values['seshtmlbackground_ffmpeg_path'];
          $output = null;
          $return = null;
          exec($ffmpeg_path . ' -version', $output, $return);

          if ($return > 0 && $output != NULL) {
            $form->seshtmkbackground_ffmpeg_path->addError('FFMPEG path is not valid or does not exist');
            $values['seshtmlbackground_ffmpeg_path'] = '';
          }
        } else {
          $form->seshtmkbackground_ffmpeg_path->addError('The exec() function is not available. The ffmpeg path has not been saved.');
          $values['seshtmlbackground_ffmpeg_path'] = '';
        }
      }
     //if(Engine_Api::_()->getApi('settings', 'core')->getSetting('seshtmlbackground.pluginactivated')) {
      include_once APPLICATION_PATH . "/application/modules/Seshtmlbackground/controllers/License.php";
      if (Engine_Api::_()->getApi('settings', 'core')->getSetting('seshtmlbackground.pluginactivated')) {
        foreach ($values as $key => $value) {
          if (is_null($value) || $value == '')
            continue;
          Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
        }
        $form->addNotice('Your changes have been saved.');
        $this->_helper->redirector->gotoRoute(array());
      //}
     }
		}
  }	
	 public function utilityAction() {
    if (defined('_ENGINE_ADMIN_NEUTER') && _ENGINE_ADMIN_NEUTER) {
      return $this->_helper->redirector->gotoRoute(array(), 'admin_default', true);
    }
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('seshtmlbackground_admin_main', array(), 'seshtmlbackground_admin_main_utility');
    $ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->seshtmlbackground_ffmpeg_path;
    if (function_exists('shell_exec')) {
      // Get version
      $this->view->version = $version = shell_exec(escapeshellcmd($ffmpeg_path) . ' -version 2>&1');
      $command = "$ffmpeg_path -formats 2>&1";
      $this->view->format = $format = shell_exec(escapeshellcmd($ffmpeg_path) . ' -formats 2>&1')
              . shell_exec(escapeshellcmd($ffmpeg_path) . ' -codecs 2>&1');
    }
  }
}