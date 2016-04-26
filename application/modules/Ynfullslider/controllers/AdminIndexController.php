<?php

class Ynfullslider_AdminIndexController extends Core_Controller_Action_Admin
{

    public function uploadVideoAction()
    {
        $this -> _helper -> layout() -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);

        if( !$this->getRequest()->isPost() )
        {
            $status = false;
            $error  = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return $this -> getResponse() -> setBody(Zend_Json::encode(array('status' => $status, 'error'=> $error)));
        }

        if( empty($_FILES['fileToUpload']) )
        {
            $status = false;
            $error = Zend_Registry::get('Zend_Translate') ->_('No file selected');
            return $this -> getResponse() -> setBody(Zend_Json::encode(array('status' => $status, 'error'=> $error)));
        }

        if( !isset($_FILES['fileToUpload']) || !is_uploaded_file($_FILES['fileToUpload']['tmp_name']) )
        {
            $status = false;
            $error = Zend_Registry::get('Zend_Translate') -> _('Invalid Upload');
            return $this -> getResponse() -> setBody(Zend_Json::encode(array('status' => $status, 'error'=> $error)));
        }

        $accepted_extensions = array('mp4', 'ogg', 'webm');
        if( !in_array(pathinfo($_FILES['fileToUpload']['name'], PATHINFO_EXTENSION), $accepted_extensions) )
        {
            $status = false;
            $error = Zend_Registry::get('Zend_Translate') -> _('Invalid File Type');
            return $this -> getResponse() -> setBody(Zend_Json::encode(array('status' => $status, 'error'=> $error)));
        }

        $file = $_FILES['fileToUpload'];
        $fileName = $file['name'];
        $status = true;

        $storage = Engine_Api::_()->getItemTable('storage_file');
        $storageObject = $storage->createFile($file, array(
            'parent_type' => 'ynfullslider_slide',
        ));

        $photoPath = '';
        $photoId = Engine_Api::_()->getApi('image', 'ynfullslider')->_getVideoImage($storageObject->file_id);
        if ($photo = Engine_Api::_() -> getItem('storage_file', $photoId)) {
            $photoPath = $photo->map();
        }

        // Remove temporary file
        @unlink($file['tmp_name']);

        return $this -> getResponse() -> setBody(Zend_Json::encode(array(
            'status' => $status,
            'name'=> $fileName,
            'file_id' => $storageObject->file_id,
            'file_path' =>$storageObject->map(),
            'photo_id' => $photoId,
            'photo_path' => $photoPath,
        )));
    }

    public function uploadImageAction()
    {
        $this -> _helper -> layout() -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);
        if (!$this -> _helper -> requireUser() -> checkRequire())
        {
            $status = false;
            $error = Zend_Registry::get('Zend_Translate') -> _('Max file size limit exceeded (probably).');
            return $this -> getResponse() -> setBody(Zend_Json::encode(array('status' => $status, 'error'=> $error)));
        }

        if (!$this -> getRequest() -> isPost())
        {
            $status = false;
            $error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
            return $this -> getResponse() -> setBody(Zend_Json::encode(array('status' => $status, 'error'=> $error)));
        }

        if (!$_FILES['fileToUpload'])
        {
            $status = false;
            $error = Zend_Registry::get('Zend_Translate') -> _('No file uploaded or file size too big.');
            return $this -> getResponse() -> setBody(Zend_Json::encode(array('status' => $status, 'error'=> $error)));
        }

        if (!isset($_FILES['fileToUpload']) || !is_uploaded_file($_FILES['fileToUpload']['tmp_name']))
        {
            $status = false;
            $error = Zend_Registry::get('Zend_Translate') -> _('Invalid Upload') . print_r($_FILES, true);
            return $this -> getResponse() -> setBody(Zend_Json::encode(array('status' => $status, 'error'=> $error)));
        }

        // TEST FOR REAL IMAGE
        try {
            $file = $_FILES['fileToUpload'];
            $image = Engine_Image::factory();
            $image -> open($file['tmp_name']);
        }
        catch( Engine_Image_Adapter_Exception $e ) {
            $status = false;
            $error = Zend_Registry::get('Zend_Translate') -> _('The uploaded file is not supported or is corrupt.');
            return $this -> getResponse() -> setBody(Zend_Json::encode(array('status' => $status, 'error'=> $error)));
        }

        $file = $_FILES['fileToUpload'];
        $fileName = $file['name'];
        $status = true;
        $parentType = $this->_getParam('parent_type');
        $parentId = $this->_getParam('parent_id');

        $storage = Engine_Api::_()->getItemTable('storage_file');
        $storageObject = $storage->createFile($file, array(
            'parent_type' => $parentType,
            'parent_id' => $parentId,
        ));

        // Remove temporary file
        @unlink($file['tmp_name']);

        return $this -> getResponse() -> setBody(Zend_Json::encode(array(
            'status' => $status,
            'name'=> $fileName,
            'file_id' => $storageObject->file_id,
            'file_path' =>$storageObject->map()
        )));
    }
}