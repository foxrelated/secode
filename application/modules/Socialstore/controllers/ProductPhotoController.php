<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: ProductphotoController.php 7244 2011-07-2- 01:49:53Z john $
 * @author     Long Le
 */
class Socialstore_ProductPhotoController extends Core_Controller_Action_Standard
{
  public function init()
  {
    if( !Engine_Api::_()->core()->hasSubject() )
    {
      if( 0 !== ($photo_id = (int) $this->_getParam('productphoto_id')) &&
          null !== ($photo = Engine_Api::_()->getItem('socialstore_product_photo', $photo_id)) )
      {
        Engine_Api::_()->core()->setSubject($photo);
      }

      else if( 0 !== ($product_id = (int) $this->_getParam('product_id')) &&
          null !== ($product = Engine_Api::_()->getItem('social_product', $product_id)) )
      {
        Engine_Api::_()->core()->setSubject($product);
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
  public function listPhotoAction() {
  	$product = Engine_Api::_()->core()->getSubject();
  	$viewer = Engine_Api::_()->user()->getViewer();
  	$product = Engine_Api::_()->getItem('social_product', (int) $product->getIdentity());
  	if ($viewer->getIdentity() != $product->owner_id) {
  		return;
  	}
  	$this->view->product_id = $product->product_id;
  	$this->view->productphoto_id = $product->photo_id;
  	$this->view->form = $form = new Socialstore_Form_Photo_Manage();
  	if($product->photo_id > 0)
        if(!$product->getPhoto($product->photo_id)){
          $product->addPhoto($product->photo_id);
        }
	$this->view->album = $album = $product->getSingletonAlbum();
    $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
    
    $paginator->setCurrentPageNumber($this->_getParam('page'));
    $paginator->setItemCountPerPage(100);
    
    foreach( $paginator as $photo )
    {
      $subform = new Socialstore_Form_Photo_Edit(array('elementsBelongTo' => $photo->getGuid()));
      $subform->removeElement('label');
      $subform->removeElement('storephoto_id');
      if($photo->file_id == $product->photo_id){
        $subform->removeElement('delete');
        $subform->removeElement('slideshow');
      }
      $subform->populate($photo->toArray());
      $form->addSubForm($subform, $photo->getGuid());
      $form->cover->addMultiOption($photo->getIdentity(), $photo->getIdentity());
    }
    if( !$this->getRequest()->isPost() )
    {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }

    // Process
    $table = Engine_Api::_()->getItemTable('socialstore_product_photo');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $values = $form->getValues();
      $params = array(
        'product_id' => $product->getIdentity(),
        'user_id' => $viewer->getIdentity(),
      );
      $cover = $values['cover'];
      // Process
      
      foreach( $paginator as $photo )
      {
      	
        $subform = $form->getSubForm($photo->getGuid());
        $subValues = $subform->getValues();
        
        $subValues = $subValues[$photo->getGuid()];
      	if ($subValues['slideshow'] == 1) {
      		$count++;
      	}
      	if ($count > 5) {
      		return $form->addError('You can only set 5 images in slideshow!');
      	}
        
        unset($subValues['productphoto_id']);
		
        if( isset($cover) && $cover == $photo->productphoto_id) {
          $product->photo_id = $photo->file_id;
          $product->save();
        }

        if( isset($subValues['delete']) && $subValues['delete'] == '1' )
        {
          if( $product->photo_id == $photo->file_id ){
            $product->photo_id = 0;
            $product->save();
          }
          $photo->delete();
        }
        else
        {
          
        	$photo->setFromArray($subValues);
          $photo->save();
        }
      }

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
    return $this->_helper->redirector->gotoRoute(array("action"=>"list-photo","controller"=>"product-photo",'product_id'=>$product->product_id),'socialstore_extended',true);
  	
  }
  public function uploadAction()
  {
  $product = Engine_Api::_()->core()->getSubject();
    if( isset($_GET['ul']) || isset($_FILES['Filedata']) ) return $this->_forward('upload-photo', null, null, array('format' => 'json', 'product_id'=>(int) $product->getIdentity()));

    $viewer = Engine_Api::_()->user()->getViewer();
    $product = Engine_Api::_()->getItem('social_product', (int) $product->getIdentity());
    if($product->owner_id == $viewer->getIdentity())
    {
        $this->view->canUpload = true;
    }
    $album = $product->getSingletonAlbum();
    $this->view->product_id = $product->product_id;
    $this->view->form = $form = new Socialstore_Form_Photo_Upload();
    $form->file->setAttrib('data', array('product_id' => $product->getIdentity()));

    if( !$this->getRequest()->isPost() )
    {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      return;
    }

    // Process
    $table = Engine_Api::_()->getItemTable('socialstore_product_photo');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $values = $form->getValues();
      $params = array(
        'product_id' => $product->getIdentity(),
        'user_id' => $viewer->getIdentity(),
      );

      // Add action and attachments
      $api = Engine_Api::_()->getDbtable('actions', 'activity');
      $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $product, 'product_photo_upload', null, array('count' => count($values['file'])));

      // Do other stuff
      $count = 0;
      foreach( $values['file'] as $photo_id )
      {
        $photo = Engine_Api::_()->getItem("socialstore_product_photo", $photo_id);

        if( !($photo instanceof Core_Model_Item_Abstract) || !$photo->getIdentity() ) continue;

        $photo->collection_id = $album->album_id;
        $photo->album_id = $album->album_id;
        $photo->save();

        if ($product->photo_id == 0) {
          $product->photo_id = $photo->file_id;
          $product->save();
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
   /* if($deal->published == 0)
        return $this->_helper->redirector->gotoRoute(array('action' => 'publish','deal'=>$deal->deal_id), 'groupbuy_general', true);
    else
        return $this->_helper->redirector->gotoRoute(array('action' => 'manage-selling'), 'groupbuy_general', true); */
    return $this->_helper->redirector->gotoRoute(array('controller'=>'product-photo','action' => 'list-photo','product_id' => $product->product_id), 'socialstore_extended', true);
  }

  public function uploadPhotoAction()
  {
    $product = Engine_Api::_()->getItem('social_product', (int) $this->_getParam('product_id'));
    if( !$this->_helper->requireUser()->checkRequire() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
      return;
    }

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    // @todo check auth
    //$deal

    $values = $this->getRequest()->getPost();
    if( empty($values['Filename']) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No file');
      return;
    }

    if( !isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name']) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
      return;
    }

    $db = Engine_Api::_()->getDbtable('ProductPhotos', 'Socialstore')->getAdapter();
    $db->beginTransaction();
    try
    {
      $viewer = Engine_Api::_()->user()->getViewer();
      $album = $product->getSingletonAlbum();
      $params = array(
        // We can set them now since only one album is allowed
        'collection_id' => $album->getIdentity(),
        'album_id' => $album->getIdentity(),
        'product_id' => $product->getIdentity(),
        'user_id' => $viewer->getIdentity(),
      );
    
      $photo_id = Engine_Api::_()->getApi('product', 'Socialstore')->createPhoto($params, $_FILES['Filedata'])->productphoto_id;
      if(!$product->photo_id){
        $product->photo_id = $photo_id;
        $product->save();
      }

      $this->view->status = true;
      $this->view->name = $_FILES['Filedata']['name'];
      $this->view->photo_id = $photo_id;

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
       throw $e;
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
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Changes saved')),
      'layout' => 'default-simple',
      'parentRefresh' => true,
      'closeSmoothbox' => true,
    ));
  }

  public function removeAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    
    $photo_id= (int) $this->_getParam('productphoto_id');
    $photo = Engine_Api::_()->getItem('socialstore_product_photo', $photo_id);

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


}
