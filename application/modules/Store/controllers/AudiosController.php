<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AudiosController.php 19.09.11 13:57 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Store_AudiosController extends Store_Controller_Action_User
{

    public function init()
    {
        $this->view->navigation = $this->getNavigation();
        if ($this->getRequest()->getQuery('ul', false)) {
            $this->_forward('upload', null, null, array('format' => 'json'));
        }
        if ($this->getRequest()->getQuery('rm', false)) {
            $this->_forward('remove', null, null, array('format' => 'json'));
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
    //    $this->view->page = $page = $product->getStore();
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

        //he@todo check admin settings
        // if (
            // !$page->isAllowStore() ||
// //      !$this->_helper->requireAuth()->setAuthParams($page, null, 'edit')->isValid() ||
            // !($page->getStorePrivacy() || $product->isOwner($viewer))
        // ) {
            // $this->_redirectCustom($page->getHref());
        // }

        // $api = Engine_Api::_()->getApi('page', 'store');
        // $this->view->navigation = $api->getNavigation($page);
    }

    public function editAction()
    {
        $this->view->storage = Engine_Api::_()->storage();
        $this->view->audios = $audios = Engine_Api::_()->getDbTable('audios', 'store')->getAudios($this->view->product->getIdentity());

        if (!count($audios)) {
            $this->_redirectCustom(
                $this->view->url(
                    array(
                        'controller' => 'audios',
                        'action' => 'create',
                        'product_id' => $this->view->product->getIdentity()
                    ),
                    'store_extended', true
                )
            );
        }
    }

    public function createAction()
    {
        $product = $this->view->product;
        $this->view->audios = Engine_Api::_()->getDbTable('audios', 'store')->getAudios($this->view->product->getIdentity());
        $this->view->form = $form = new Store_Form_Admin_Audios_Create();

        if (!$this->getRequest()->isPost()) {
            return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $db = Engine_Api::_()->getDbTable('audios', 'store')->getAdapter();
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
                    'controller' => 'audios',
                    'action' => 'edit',
                    'product_id' => $product->getIdentity()
                ),
                'store_extended', true
            )
        );
    }

    public function uploadAction()
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
        $db = Engine_Api::_()->getDbtable('audios', 'store')->getAdapter();
        $db->beginTransaction();

        try {
            $file = Engine_Api::_()->getApi('core', 'store')->createAudio($_FILES['Filedata']);
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

    public function deleteAction()
    {
        $audio_id = (int)$this->_getParam('audio_id');
        $this->_helper->layout->setLayout('default-simple');
        $this->view->form = new Store_Form_Admin_Audios_Delete();
        $audio = Engine_Api::_()->getDbTable('audios', 'store')->findRow($audio_id);

        if (!$audio) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_("Audio doesn't exists or not authorized to delete");
            return;
        }
        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }
        if ($audio_id) {
            $db = $audio->getTable()->getAdapter();
            $db->beginTransaction();
            try {
                Engine_Api::_()->getApi('core', 'store')->deleteAudio($audio);
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->view->message = Zend_Registry::get('Zend_Translate')->_('Audio has been deleted.');
            $this->_forward('success', 'utility', 'core', array(
                'parentRedirect' => Zend_Controller_Front::getInstance()
                        ->getRouter()
                        ->assemble(
                            array(
                                'controller' => 'audios',
                                'action' => 'edit',
                                'product_id' => $this->view->product->getIdentity()
                            ),
                            'store_extended', true
                        ),
                'messages' => Array($this->view->message)
            ));
        }
    }

    public function removeAction()
    {
        $file_id = $this->_getParam('file_id', 0);
        Engine_Api::_()->getApi('core', 'store')->deleteFile($file_id);
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
        'class' => (in_array($menu, array('products', 'edit', 'copy', 'create'))) ? 'active' : '',
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