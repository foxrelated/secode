<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Groupbuy
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: PhotoController.php 7244 2011-07-2- 01:49:53Z john $
 * @author     Minh Nguyen
 */
class Groupbuy_PhotoController extends Core_Controller_Action_Standard
{
  public function init()
  {
    if( !Engine_Api::_()->core()->hasSubject() )
    {
      if( 0 !== ($photo_id = (int) $this->_getParam('photo_id')) &&
          null !== ($photo = Engine_Api::_()->getItem('groupbuy_photo', $photo_id)) )
      {
        Engine_Api::_()->core()->setSubject($photo);
      }

      else if( 0 !== ($deal_id = (int) $this->_getParam('deal_id')) &&
          null !== ($deal = Engine_Api::_()->getItem('deal', $deal_id)) )
      {
        Engine_Api::_()->core()->setSubject($deal);
      }
  }
  }
  public function listAction()
  {
    $this->view->deal = $deal = Engine_Api::_()->core()->getSubject();
    $this->view->album = $album = $deal->getSingletonAlbum();

    $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    $this->view->canUpload = $group->authorization()->isAllowed(null, 'photo.upload');
  }

  public function viewAction()
  {
    $this->view->photo = $photo = Engine_Api::_()->core()->getSubject();
    $this->view->album = $album = $photo->getCollection();
    $this->view->group = $group = $photo->getGroup();
    $this->view->canEdit = $photo->authorization()->isAllowed(null, 'photo.edit');
  }

  public function uploadAction()
  {
    $deal = Engine_Api::_()->core()->getSubject();
    if( isset($_GET['ul']) || isset($_FILES['Filedata']) ) return $this->_forward('upload-photo', null, null, array('format' => 'json', 'deal_id'=>(int) $deal->getIdentity()));

    $viewer = Engine_Api::_()->user()->getViewer();
    $deal = Engine_Api::_()->getItem('deal', (int) $deal->getIdentity());
    if($deal->user_id == $viewer->getIdentity())
    {
        $this->view->canUpload = true;
    }
    $album = $deal->getSingletonAlbum();
    $this->view->deal_id = $deal->deal_id;
    $this->view->form = $form = new Groupbuy_Form_Photo_Upload();
	$form -> deal_id -> setValue($deal -> getIdentity());
	
   
    if( !$this->getRequest()->isPost() )
    {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }

    // Process
    $table = Engine_Api::_()->getItemTable('groupbuy_photo');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $values = $form->getValues();
      $params = array(
        'deal_id' => $deal->getIdentity(),
        'user_id' => $viewer->getIdentity(),
      );

      // Add action and attachments
      $api = Engine_Api::_()->getDbtable('actions', 'activity');
      $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $deal, 'groupbuy_photo_upload', null, array('count' => count($values['file'])));

      // Do other stuff
      $count = 0;
      foreach( $values['file'] as $photo_id )
      {
        $photo = Engine_Api::_()->getItem("groupbuy_photo", $photo_id);

        if( !($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity() ) continue;

        $photo->collection_id = $album->album_id;
        $photo->album_id = $album->album_id;
        $photo->save();

        if ($deal->photo_id == 0) {
          $deal->photo_id = $photo->file_id;
          $deal->save();
        }

        if( $action instanceof Activity_Model_Action && $count < 8 )
        {
          $api->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
        }
        $count++;
      }

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    if($deal->published == 0)
        return $this->_helper->redirector->gotoRoute(array('action' => 'publish','deal'=>$deal->deal_id), 'groupbuy_general', true);
    else
        return $this->_helper->redirector->gotoRoute(array('action' => 'manage-selling'), 'groupbuy_general', true);
  }

  public function uploadPhotoAction()
  {
  	$this -> _helper -> layout() -> disableLayout();
	$this -> _helper -> viewRenderer -> setNoRender(true);  		
  		
    if (!$this -> _helper -> requireUser() -> checkRequire()) {
		$status = false;
		$error = Zend_Registry::get('Zend_Translate') -> _('Max file size limit exceeded (probably).');
		return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error' => $error)))));
	}
	if (!$this -> getRequest() -> isPost()) {
		$status = false;
		$error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
		return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error' => $error)))));
	}
	
	$deal = Engine_Api::_()->getItem('deal', (int)$_REQUEST['deal_id']);
	

    // @todo check auth
    //$deal

    if (empty($_FILES['files'])) {
			$status = false;
			$error = Zend_Registry::get('Zend_Translate') -> _('No file');
			return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'name' => $error)))));
		}

    $name = $_FILES['files']['name'][0];
	$type = explode('/', $_FILES['files']['type'][0]);
	if (!$_FILES['files'] || !is_uploaded_file($_FILES['files']['tmp_name'][0]) || $type[0] != 'image') {
		$status = false;
		$error = Zend_Registry::get('Zend_Translate') -> _('Invalid Upload');
		return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'error' => $error, 'name' => $name)))));
	}

    $db = Engine_Api::_()->getDbtable('photos', 'groupbuy')->getAdapter();
    $db->beginTransaction();
    try
    {
      $viewer = Engine_Api::_()->user()->getViewer();
      $album = $deal->getSingletonAlbum();
      $params = array(
        // We can set them now since only one album is allowed
        'collection_id' => $album->getIdentity(),
        'album_id' => $album->getIdentity(),
        'deal_id' => $deal->getIdentity(),
        'user_id' => $viewer->getIdentity(),
      );
      
	  $temp_file = array('type' => $_FILES['files']['type'][0], 'tmp_name' => $_FILES['files']['tmp_name'][0], 'name' => $_FILES['files']['name'][0]);
		
      $photo_id = Engine_Api::_()->groupbuy()->createPhoto($params, $temp_file)->photo_id;
      if(!$deal->photo_id){
        $deal->photo_id = $photo_id;
        $deal->save();
      }

      $db->commit();
	  $status = true;
	  $name = $_FILES['files']['name'][0];

	  return $this -> getResponse() -> setBody(Zend_Json::encode(array('files' => array(0 => array('status' => $status, 'name' => $name, 'photo_id' => $photo_id)))));
    }

    catch( Exception $e )
    {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
      // throw $e;
      return;
    }
  }

  public function editAction()
  {
    $photo = Engine_Api::_()->core()->getSubject();

    $this->view->form = $form = new Groupbuy_Form_Photo_Edit();

    if( !$this->getRequest()->isPost() )
    {
      $form->populate($photo->toArray());
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('photos', 'groupbuy')->getAdapter();
    $db->beginTransaction();

    try
    {
      $photo->setFromArray($form->getValues())->save();

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    return $this->_forward('success', 'utility', 'core', array(
      'messages' => array('Changes saved'),
      'layout' => 'default-simple',
      'parentRefresh' => true,
      'closeSmoothbox' => true,
    ));
  }

  public function removeAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    
    $photo_id= (int) $this->_getParam('photo_id');
    $photo = Engine_Api::_()->getItem('groupbuy_photo', $photo_id);

    $db = $photo->getTable()->getAdapter();
    $db->beginTransaction();

    try
    {
      $photo->delete();

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
  }
  public function deletePhotoAction()
	{
		$photo = Engine_Api::_() -> getItem('groupbuy_photo', $this -> getRequest() -> getParam('photo_id'));
		
		if (!$photo)
		{
			$this -> view -> success = false;
			$this -> view -> error = $translate -> _('Not a valid photo');
			$this -> view -> post = $_POST;
			return;
		}
		// Process
		$db = Engine_Api::_() -> getDbtable('photos', 'groupbuy') -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$photo -> delete();
			
			$db -> commit();
		}

		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
	}


}
