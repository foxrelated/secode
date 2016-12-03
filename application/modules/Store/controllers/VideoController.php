<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: VideoController.php 19.09.11 16:57 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_VideoController extends Store_Controller_Action_User
{
    public function init()
    {
      error_reporting(E_ALL);
      ini_set('display_errors', 1);
		
    $this->view->navigation = $this->getNavigation();
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

        // //he@todo check admin settings
        // if (
        // !$page->isAllowStore() ||
        // !($page->getStorePrivacy() || $product->isOwner($viewer))
        // !$this->_helper->requireAuth()->setAuthParams($page, null, 'edit')->isValid()
        // ) {
        // $this->_redirectCustom($page->getHref());
        // }

        $this->view->hasVideo = $product->hasVideo();
        // $api = Engine_Api::_()->getApi('page', 'store');
        // $this->view->navigation = $api->getNavigation($page);
    }

  public function editAction()
    {
      $product = $this->view->product;
        $viewer = $this->view->viewer;


        if (!$this->view->hasVideo) {
            $this->redirect('create');
        }

        $this->view->video = $video = $product->getVideo();

        // Make form
        $this->view->form = $form = new Store_Form_Admin_Video_Edit();

        $form->populate($video->toArray());



        $this->view->preview = $video->getRichContent(1);


        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$this->getParam('product_id')) {
            return;
        }

        $table = $video->getTable();

        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
         /**
         * @var $api Store_Api_Core
         * */

          $select = $table->select()->where('product_id = ?',$this->getParam('product_id'))->where('owner_id =?',$viewer->getIdentity());

          $res = $table->fetchRow($select);

          $values = array();
          $values['title'] = $this->getParam('title');
          $values['description'] = $this->getParam('description');

          $res->title=$values['title'];
          $res->description=$values['description'];
          $res->save();

          $db->commit();
          $this->redirect('edit');
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function createAction()
    {

        $viewer = $this->view->viewer;
        $product = $this->view->product;


        if ($this->view->hasVideo) {
            $this->redirect('edit');
        }

        $this->view->video = $product->getVideo();

        // Create form
        $this->view->form = $form = new Store_Form_Admin_Video_Upload();

        $form->getDecorator('description')->setOption('escape', false);




        if ($this->_getParam('type', false)) $form->getElement('type')->setValue($this->_getParam('type'));




        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->_getAllParams())) {
            $values = $form->getValues('url');
            return;
        }



        // Process
        $values = $form->getValues();

        $table = Engine_Api::_()->getDbtable('videos', 'store');
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {



            // Create video
            $video = $table->createRow();

            $video->setFromArray($values);
            $video->product_id = (int)$this->_getParam('product_id');
            $video->owner_id = $viewer->getIdentity();
            $video->status = 1;
            $video->save();

            Engine_Api::_()->getApi('core', 'store')->createThumbnail($video);

            $db->commit();
            $this->redirect('edit');
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function deleteAction()
    {
        $product = $this->view->product;
        $this->view->video = $video = $product->getVideo();

        // In smoothbox
        $this->_helper->layout->setLayout('default-simple');

        $this->view->form = new Store_Form_Admin_Video_Delete();

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
        $this->_forward('success', 'utility', 'core', array(
            'parentRedirect' => Zend_Controller_Front::getInstance()
                    ->getRouter()
                    ->assemble(
                        array(
                            'controller' => 'video',
                            'action' => 'create',
                            'product_id' => $product->getIdentity()
                        ),
                        'store_extended', true
                    ),
            'messages' => Array($this->view->message)
        ));
    }

    public function validationAction()
    {

        $video_type = $this->_getParam('type');
        $code = $this->_getParam('code');
        $ajax = $this->_getParam('ajax', false);
        $valid = false;




		
		
        // check which API should be used
        if ($video_type == "youtube") {
            $valid = $this->checkYouTube($code);
        }
        if ($video_type == "vimeo") {
            $valid = $this->checkVimeo($code);
        }

        $this->view->code = $code;
        $this->view->ajax = $ajax;
        $this->view->valid = $valid;
    }


  public function checkYouTube($code){

   $key = Engine_Api::_()->getApi('settings', 'core')->getSetting('video.youtube.apikey');
  //  $key = 'vGa3FyBW7WFWm-CaVqx6l1JE';

    if (!$data = @file_get_contents('https://www.googleapis.com/youtube/v3/videos?part=id&id=' . $code . '&key=' . $key)) return false;

    $data = Zend_Json::decode($data);
    if (empty($data['items'])) return false;
    return true;
  }
    // Vimeo Functions
    public function checkVimeo($code)
    {

        $data = @simplexml_load_file("https://vimeo.com/api/v2/video/" . $code . ".xml");
        $id = count($data->video->id);
        if ($id == 0) return false;
        return true;
    }

    public function redirect($action)
    {
        $this->_redirectCustom(
            $this->view->url(
                array(
                    'controller' => 'video',
                    'action' => $action,
                    'product_id' => $this->view->product->getIdentity()
                ),
                'store_extended', true
            )
        );
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