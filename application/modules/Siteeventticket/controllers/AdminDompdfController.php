<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminDompdfController.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_AdminDompdfController extends Core_Controller_Action_Admin {

    protected $_basePath;

    public function init() {

        //CHECK IF FOLDER EXIST AND WRITEABLE
        $dirPath = APPLICATION_PATH . '/application/libraries';
        @chmod($dirPath, 0777);
        if (!is_writable($dirPath)) {
            return $this->_forward('error', null, null, array(
                    'message' => 'The /application/libraries folder is not writable. Please set full permissions on it (chmod 0777) to continue the upload procedure.',
            ));
        }

        //SET BASE PATH
        $this->_basePath = realpath($dirPath);
    }

    public function indexAction() {
        
        //TAB CREATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('siteevent_admin_main', array(), 'siteeventticket_admin_main_ticket');
        
        //GET NAVIGATION
        $this->view->navigationGeneral = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('siteeventticket_admin_main_ticket', array(), 'siteeventticket_admin_main_settings');        
        
        $this->view->path = $path = $this->_getPath();
        $this->view->relPath = $relPath = $this->_getRelPath($path);
    }

    public function uploadAction() {

        $this->view->path = $path = $this->_getPath();
        $this->view->relPath = $relPath = $this->_getRelPath($path);

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (null === $this->_getParam('ul')) {
            return;
        }

        if (empty($_FILES['Filedata'])) {
            $this->view->error = 'File failed to upload. Check your server settings (such as php.ini max_upload_filesize).';
            return;
        }

        if (strtolower(substr($_FILES['Filedata']['name'], -7, 7)) != '.tar.gz') {
            $this->view->error = 'File type or extension forbidden.';
            return;
        }

        $info = $_FILES['Filedata'];
        $targetFile = $path . '/' . $info['name'];

        if (file_exists($targetFile)) {
            @unlink($targetFile);
        }

        if (!is_writable($path)) {
            $this->view->error = 'Path is not writeable. Please CHMOD 0777 the application/libraries directory.';
            return;
        }

        // Try to move uploaded file
        if (!move_uploaded_file($info['tmp_name'], $targetFile)) {
            $this->view->error = "Unable to move file to upload directory.";
            return;
        }

        @chmod($targetFile, 0777);
        $archive = new Archive_Tar($targetFile);
        $archive->extract(APPLICATION_PATH . '/application/libraries');

        $this->setPermission(APPLICATION_PATH . '/application/libraries/dompdf', 0777);

        $file = APPLICATION_PATH . '/application/libraries/dompdf/dompdf_config.inc.php';
        $fileContent = file_get_contents($file);
        $fileContent = str_replace('def("DOMPDF_ENABLE_REMOTE", false);', 'def("DOMPDF_ENABLE_REMOTE", true);', $fileContent);
        @file_put_contents($file, $fileContent);

        if (file_exists($targetFile)) {
            @unlink($targetFile);
        }

        $this->view->status = 1;

        if (null === $this->_helper->contextSwitch->getCurrentContext()) {
            return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
        } else if ('smoothbox' === $this->_helper->contextSwitch->getCurrentContext()) {
            return $this->_forward('success', 'utility', 'core', array(
                    'smoothboxClose' => true,
                    'parentRefresh' => true,
            ));
        }
    }

    public function errorAction() {
        $this->getResponse()->setBody($this->_getParam('message', 'error'));
        $this->_helper->viewRenderer->setNoRender(true);
    }

    protected function _getPath($key = 'path') {
        return $this->_checkPath(urldecode($this->_getParam($key, '')), $this->_basePath);
    }

    protected function _getRelPath($path, $basePath = null) {
        if (null === $basePath)
            $basePath = $this->_basePath;
        $path = realpath($path);
        $basePath = realpath($basePath);
        $relPath = trim(str_replace($basePath, '', $path), '/\\');
        return $relPath;
    }

    protected function _checkPath($path, $basePath) {

        $path = preg_replace('/\.{2,}/', '.', $path);
        $path = preg_replace('/[\/\\\\]+/', '/', $path);
        $path = trim($path, './\\');
        $path = $basePath . '/' . $path;

        $basePath = realpath($basePath);
        $path = realpath($path);

        if ($basePath != $path && strpos($basePath, $path) !== false) {
            return $this->_helper->redirector->gotoRoute(array());
        }

        return $path;
    }

    public function setPermission($path, $value = 0777) {

        $dir = new DirectoryIterator($path);
        foreach ($dir as $item) {
            @chmod($item->getPathname(), $value);
            if ($item->isDir() && !$item->isDot()) {
                $this->setPermission($item->getPathname(), $value);
            }
        }
    }

}
