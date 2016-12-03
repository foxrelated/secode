<?php
/**
 * SocialEngine
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 2011-08-19 17:22:12 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Store_IndexController extends Store_Controller_Action_Standard
{
    public function indexAction()
    {
	
		
		
        // Render
        $this->_helper->content
            ->setNoRender()
            ->setEnabled();
    }

    public function productsAction()
    {
        // Render
        $this->_helper->content
            ->setNoRender()
            ->setEnabled();
    }

    public function storesAction()
    {
        if (!Engine_Api::_()->getDbtable('modules', 'hecore')->isModuleEnabled('page'))
            return;


        // Render
        $this->_helper->content
            ->setNoRender()
            ->setEnabled();
    }
	
    public function editVideoAction()
  {

    $viewer = Engine_Api::_()->user()->getViewer();
    if ($this->next) {
      $this->view->nextHref = $this->view->url(array('module' => 'store', 'controller' => 'videos', 'action' => 'edit-video', 'product_id' => $this->next->getIdentity()));
    }
    if ($this->prev) {
      $this->view->prevHref = $this->view->url(array('module' => 'store', 'controller' => 'videos', 'action' => 'edit-video', 'product_id' => $this->prev->getIdentity()));
    }
	
	
	 
    $this->view->section_title = $this->view->translate('STORE_Admin Section Edit video');
	
	
    $product = $this->view->product;



	 if(isset($product)){
		$this->view->video = $video = $product->getVideo();
	}
	
	
	
	
    // Make form
    $this->view->form = $form = new Store_Form_Admin_Video_Edit($video);







    if ($video) {
      $form->populate($video->toArray());
    }

    if (!$this->getRequest()->isPost()) {
      return;
    }

	
    if (!$video) {
      $table = Engine_Api::_()->getItemTable('store_video');
    } else {
      $table = $video->getTable();
    }

	
	
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      $values = $this->_getAllParams();


      if (!$video) {
        $video = $table->createRow();
        $video->product_id = (int)$this->_getParam('product_id');
        $video->owner_id = $viewer->getIdentity();
        $video->status = 1;
        $video->save();
        $video->type = $values['type'];
      }

	  
      $api = Engine_Api::_()->getApi('core', 'store');

      switch ($values['type']) {
        case 3: //desktop
          if($video->getIdentity() && empty($_FILES['Filedata'])) {
            break;
          }
          if (empty($_FILES['Filedata'])) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('No file');
            return;
          }

          $illegal_extensions = array('php', 'pl', 'cgi', 'html', 'htm', 'txt');
          if ((!isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name'])) ||
            (in_array(pathinfo($_FILES['Filedata']['name'], PATHINFO_EXTENSION), $illegal_extensions))
          ) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload') . print_r($_FILES, true);
            return;
          }

          $video = $api->createVideo(
            array('owner_type' => 'user', 'owner_id' => $viewer->getIdentity()),
            $_FILES['Filedata'],
            $video
          );
          break;
        case 0: // none
          $video->delete();
          break;
        default: // service
          if ($video->type == 3 || ($video->url != '' && $video->url != $values['url'])) {

            $api->deleteVideo($video);
            $video = $table->createRow();
          }

          $video->status = 1;
          $video->product_id = (int)$this->_getParam('product_id');
          $video->owner_id = $viewer->getIdentity();
      }



      if(!$video->photo_id)
        Engine_Api::_()->getApi('core', 'store')->createThumbnail($video);
      $this->view->status = true;

      $video->setFromArray($values);
      $video->save();
      $this->view->preview = $video->getRichContent(1);
      $db->commit();

    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
	
  }

    public function deleteAction()
  {
    $product = $this->view->product;
	if($product){
    $this->view->video = $video = $product->getVideo();
    }else{
		$video->status = 3;
	}
	$this->view->status = false;

    if (!$video) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Video doesn't exists or not authorized to delete");
      return;
    }

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    $db = $video->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      Engine_Api::_()->getApi('core', 'store')->deleteVideo($video);
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Video has been deleted.');
  }

    public function downloadAction()
    {
        if (!($id = $this->_getParam('id'))) {
            return $this->fileNotFound();
        }

        $free = $this->_getParam('free', 0);

        /**
         * Declare Variables
         * @var $viewer  User_Model_User
         * @var $item    Store_Model_Orderitem
         * @var $product Store_Model_Product
         * @var $order   Store_Model_Order
         * @var $settings Core_Model_DbTable_Settings
         */

        $viewer = Engine_Api::_()->user()->getViewer();
        $settings = Engine_Api::_()->getDbTable('settings', 'core');
        $allowFree = $settings->getSetting('store.free.products', 0);
        $allowPublic = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('store_product', $viewer, 'order');

        if (!$viewer->getIdentity() && !$allowPublic) {
            return $this->fileNotFound();
        }

        $isProduct = false;
        if ($free) {
            if ($allowFree) {
                $item = Engine_Api::_()->getItem('store_product', $id);
                $isProduct = true;
            } else {
                return $this->fileNotFound();
            }
        } else {
            $item = Engine_Api::_()->getItem('store_orderitem', $id);
        }

        if (!$item) {
            return $this->fileNotFound();
        }

        if ($isProduct) {
            $storage = $item->getFile();
        } else {

            $product = $item->getItem();
            $order = $item->getParent();
            $storage = $product->getFile();

            if (!$item->isDownloadable() || !$product || !$order || !$order->isOwner($viewer)) {
                return $this->fileNotFound();
            }
        }

        if (!$storage) {
            return $this->fileNotFound();
        }

        if (!($storage instanceof Storage_Model_File)) {
            if ($isProduct) {
                return $this->fileNotFound();
            } else {
                return $this->fileNotFound($order->getIdentity());
            }
        }

        // Process the file
        $file = $storage->map();
        $link = false;
        if ($storage->extension == 'link') {
            $fileReading = fopen($file, 'r');
            $theData = fread($fileReading, $storage->size);
            fclose($fileReading);
            $link = $theData;
        }

        if (!$link) {
            if (!is_file($file)&&!($file)) {
                if ($isProduct) {
                    return $this->fileNotFound();
                } else {
                    return $this->fileNotFound($order->getIdentity());
                }
            } else {



                try {
                    // Disable view and layout rendering
                    $this->_helper->layout()->disableLayout();
                    $this->_helper->viewRenderer->setNoRender();

                    // Execute Downloading
                    // Set Headers
                    header("Pragma: public");
                    header("Expires: 0");
                    header("Cache-Control: no-cache, must-revalidate, post-check=0, pre-check=0");
                    header("Cache-Control: private", false);
                    header('Content-type: ' . $storage->mime_major . '/' . $storage->mime_minor);
                    header('Content-Disposition: attachment; filename="' . $storage->name . '"');
                    header('Content-Description: File Transfer');
                    header("Content-Transfer-Encoding: binary");
                    header('Content-Length: ' . $storage->size);

                    // Set Body
                    //@TODO review
                    if (readfile($file) /*Engine_Api::_()->store()->readfile_chunked($file)*/) {
                        // Increase download count
                        $item->download_count++;
                        if (!$item->save()) {
                            if ($isProduct) {
                                return $this->fileNotFound();
                            } else {
                                return $this->fileNotFound($order->getIdentity());
                            }
                        }
                    }
                } catch (Exception $e) {
                    print_log($e->__toString());
                }
            }
        } else {
            try {
                // Disable view and layout rendering
                $this->_helper->layout()->disableLayout();
                $this->_helper->viewRenderer->setNoRender();

                $item->download_count++;
                if (!$item->save()) {
                    if ($isProduct) {
                        return $this->fileNotFound();
                    } else {
                        return $this->fileNotFound($order->getIdentity());
                    }
                }
                $this->_redirect($link);

            } catch (Exception $e) {
                print_log($e->__toString());
            }
        }
    }

    protected function fileNotFound($order_id = 0)
    {
        $this->view->message = $this->view->translate('STORE_Sorry, we could not find requested download file.');

        if (!$order_id) {
            return;
        }
        $this->_forward('success', 'utility', 'core', array(
            'layout' => 'default',
            'redirect' => $this->view->url(array('action' => 'transactions',
                    'order_id' => $order_id), 'store_panel', true),
            'redirectTime' => '3000',
            'messages' => Array($this->view->message)
        ));
    }

    public function faqAction()
    {
        $this->view->faqs = Engine_Api::_()->getDbTable('faq', 'store')->fetchAll();
    }
}
