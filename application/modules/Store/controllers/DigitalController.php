<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: DigitalController.php 21.09.11 15:48 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Store_DigitalController extends Store_Controller_Action_User
{
    public function init()
    {
        $this->view->navigation = $this->getNavigation();
        if (isset($_GET['ul']) || isset($_FILES['Filedata'])) {
            $this->_forward('upload-file', null, null, array('format' => 'json'));
        }

        if (isset($_GET['rm'])) {
            $this->_forward('remove-file', null, null, array('format' => 'json'));
        }

        /**
         * @var $product Store_Model_Product
         */
        if (null != ($product = Engine_Api::_()->getItem('store_product', $this->_getParam('product_id', 0)))) {
            Engine_Api::_()->core()->setSubject($product);
        }

        //Set Requires
        $this->_helper->requireSubject('store_product')->isValid();

        $this->view->product = $product = Engine_Api::_()->core()->getSubject('store_product');
        // $this->view->page = $page = $product->getStore();
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

        //he@todo check admin settings
        if (!$product->isOwner($viewer)) {
            $this->_redirectCustom($product->getHref());
        }

   //     $api = Engine_Api::_()->getApi('page', 'store');
   //     $this->view->navigation = $api->getNavigation($page);

        if (!$product->hasFile() && $this->_getParam('action') == 'edit-file') {
            $this->_redirectCustom(
                $this->view->url(
                    array(
                        'controller' => 'digital',
                        'action' => 'create-file',
                        'product_id' => $product->getIdentity()
                    ), 'store_extended', true
                )
            );
        } elseif ($product->hasFile() && $this->_getParam('action') == 'create-file') {
            $this->_redirectCustom(
                $this->view->url(
                    array(
                        'controller' => 'digital',
                        'action' => 'edit-file',
                        'product_id' => $product->getIdentity()
                    ), 'store_extended', true
                )
            );
        }
    }

    public function createFileAction()
    {
        $product = $this->view->product;

        $this->view->form = $form = new Store_Form_Admin_Digital_Create();

        if (!$this->getRequest()->isPost()) {
            return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $this->getRequest()->getPost();
        $file_id = trim($values['fancyuploadfileids']);



        if (empty($file_id)) {
            $link = $values['link'];

            $new_file = APPLICATION_PATH . '/public/store_product/product_link_file_' . time() . '.link';
            $fp = fopen($new_file, "w");
            fwrite($fp, $link);
            fclose($fp);

            $storage = Engine_Api::_()->getItemTable('storage_file');

            $row = $storage->createRow();
            $row->setFromArray(array(
                'parent_type' => 'store_product',
                'parent_id' => 1, // Hack
                'user_id' => null,
            ));

            $row->store($new_file);
            $form->fancyuploadfileids->setValue($row->getIdentity());
            unlink($new_file);
        }

        $db = Engine_Api::_()->getDbTable('products', 'store')->getAdapter();
        $db->beginTransaction();
        try {
            $form->saveValues($product);
            $db->commit();
        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
        $this->_redirectCustom(
            $this->view->url(
                array(
                    'controller' => 'digital',
                    'action' => 'edit-file',
                    'product_id' => $product->getIdentity()
                ), 'store_extended', true
            )
        );
    }

    public function editFileAction()
    {
        $product = $this->view->product;
        $this->view->file = $product->getFile();
    }

    public function deleteFileAction()
    {
        $this->_helper->layout->setLayout('default-simple');
        $this->view->form = new Store_Form_Admin_Digital_Delete();
        $file = $this->view->product->getFile();

        if (!$file) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_("File doesn't exists or not authorized to delete");
            return;
        }

        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }

        if ($file) {
            $table = Engine_Api::_()->getDbTable('products', 'store');
            $db = $table->getAdapter();
            $db->beginTransaction();
            try {
                Engine_Api::_()->getApi('core', 'store')->deleteFile($file->file_id);
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->view->message = Zend_Registry::get('Zend_Translate')->_('File has been deleted.');
            $this->_forward('success', 'utility', 'core', array(
                'parentRedirect' => Zend_Controller_Front::getInstance()
                        ->getRouter()
                        ->assemble(
                            array(
                                'controller' => 'digital',
                                'action' => 'create-file',
                                'product_id' => $this->view->product->getIdentity()
                            ),
                            'store_extended', true
                        ),
                'messages' => Array($this->view->message)
            ));
        }
    }

    public function removeFileAction()
    {
        $file_id = $this->_getParam('file_id', 0);
        Engine_Api::_()->getApi('core', 'store')->deleteFile($file_id);
    }

    public function uploadFileAction()
    {
        // Check method
        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = $this->view->translate('Invalid request method');
            return;
        }

        // Check file
        $values = $this->getRequest()->getPost();
        if (empty($values['Filename']) || empty($_FILES['Filedata'])) {
            $this->view->status = false;
            $this->view->error = $this->view->translate('No file');
            return;
        }

        // Process
        $db = Engine_Api::_()->getDbtable('products', 'store')->getAdapter();
        $db->beginTransaction();

        try {
            $file = Engine_Api::_()->getApi('core', 'store')->createFile($_FILES['Filedata']);
            $this->view->status = true;
            $this->view->file = $file;
            $this->view->file_id = $file->getIdentity();
            $this->view->file_url = $file->getHref();
            $db->commit();

        } catch (Storage_Model_Exception $e) {
            $db->rollback();

            $this->view->status = false;
            $this->view->message = $this->view->translate($e->getMessage());

        } catch (Exception $e) {
            $db->rollback();

            $this->view->status = false;
            $this->view->message = $this->view->translate('Upload failed by database query');

            throw $e;
        }
    }

  public function getNavigation()
  {
    $menu = $this->_getParam('action', 'index');

    $navigation = new Zend_Navigation();
    $isPageEnabled = (
    Engine_Api::_()->getDbtable('modules', 'hecore')->isModuleEnabled('page')
      //$this->_helper->requireAuth()->setAuthParams('page', null, 'create')->isValid()
    );

    $navigation->addPages(array(
      array(
        'label' => "My Products",
        'route' => 'store_panel',
        'action' => 'products',
        'icon' => 'application/modules/Store/externals/images/items.png',
        'class' => (in_array($menu, array('products', 'edit', 'copy'))) ? 'active' : '',
      ),
      array(
        'label' => "My Purchases",
        'route' => 'store_panel',
        'action' => 'purchases',
        'icon' => 'application/modules/Store/externals/images/business-request.png',
        'class' => (in_array($menu, array('purchase', 'purchases'))) ? 'active' : '',
      ),
      array(
        'label' => "My Transactions",
        'route' => 'store_panel',
        'action' => 'transactions',
        'icon' => 'application/modules/Store/externals/images/history.png',
        'class' => (in_array($menu, array('transactions', 'detail'))) ? 'active' : '',
      ),
      array(
        'label' => "My Wishlist",
        'route' => 'store_panel',
        'action' => 'wish-list',
        'icon' => 'application/modules/Store/externals/images/heart.png',
        'class' => ($menu == 'wish-list') ? 'active' : '',
      ),
      array(
        'label' => "My Shipping Details",
        'route' => 'store_panel',
        'action' => 'address',
        'icon' => 'application/modules/Store/externals/images/ship.png',
        'class' => ($menu == 'address') ? 'active' : '',
      ),
      array(
            'label' => "Create New Product",
            'route' => 'store_products',
            'action' => 'create',
            'icon' => 'application/modules/Store/externals/images/new_product.png',
            'class' => ($menu == 'create') ? 'active' : '',
          )
    ));

    if ($isPageEnabled) {
      $navigation->addPages(array(
          array(
            'label' => "Manage Stores",
            'route' => 'store_panel',
            'icon' => 'application/modules/Store/externals/images/edit_store.png',
            'class' => ($menu == 'index') ? 'active' : ''
          ),
          array(
            'label' => "Create New Store",
            'route' => 'page_create',
            'icon' => 'application/modules/Store/externals/images/new_product.png'
          ))
      );
    }

    return $navigation;
  }
}