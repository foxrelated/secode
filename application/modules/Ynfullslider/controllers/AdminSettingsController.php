<?php

class Ynfullslider_AdminSettingsController extends Core_Controller_Action_Admin {

    public function indexAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('ynfullslider_admin_main', array(), 'ynfullslider_admin_main_settings');

        // Check ffmpeg path for correctness
        if (function_exists('exec')) {
            $ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->ynfullslider_ffmpeg_path;

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
                        exec($ffmpeg_path . ' -version', $output, $return);
                        if (0 != $return) {
                            $ffmpeg_path = null;
                        }
                    }
                }
            }
            if ($ffmpeg_path != $ffmpeg_path_original) {
                Engine_Api::_()->getApi('settings', 'core')->ynfullslider_ffmpeg_path = $ffmpeg_path;
            }
        }

        // Make form
        $this->view->form = $form = new Ynfullslider_Form_Admin_Global();

        // Check method/data
        if (!$this->getRequest()->isPost()) {
            return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $form->getValues();

        // Check ffmpeg path
        if (!empty($values['ynfullslider_ffmpeg_path'])) {
            if (function_exists('exec')) {
                $ffmpeg_path = $values['ynfullslider_ffmpeg_path'];
                $output = null;
                $return = null;
                exec($ffmpeg_path . ' -version', $output, $return);
                if ($return > 0) {
                    $form->ynfullslider_ffmpeg_path->addError('FFMPEG path is not valid or does not exist');
                    $values['ynfullslider_ffmpeg_path'] = '';
                }
            } else {
                $form->ynfullslider_ffmpeg_path->addError('The exec() function is not available. The ffmpeg path has not been saved.');
                $values['ynfullslider_ffmpeg_path'] = '';
            }
        }

        // Okay, save
        foreach ($values as $key => $value) {
            Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
        }
        $form->addNotice('Your changes have been saved.');
    }


    public function utilityAction() {
        if (defined('_ENGINE_ADMIN_NEUTER') && _ENGINE_ADMIN_NEUTER) {
            return $this->_helper->redirector->gotoRoute(array(), 'admin_default', true);
        }

        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('ynfullslider_admin_main', array(), 'ynfullslider_admin_main_utility');

        $ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->ynfullslider_ffmpeg_path;
        if (function_exists('shell_exec')) {
            // Get version
            $this->view->version = $version
                    = shell_exec(escapeshellcmd($ffmpeg_path) . ' -version 2>&1');
            $command = "$ffmpeg_path -formats 2>&1";
            $this->view->format = $format
                    = shell_exec(escapeshellcmd($ffmpeg_path) . ' -formats 2>&1')
                    . shell_exec(escapeshellcmd($ffmpeg_path) . ' -codecs 2>&1');
        }
    }
}