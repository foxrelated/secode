<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Core.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_Plugin_Core extends Zend_Controller_Plugin_Abstract {

  public function routeShutdown(Zend_Controller_Request_Abstract $request) {

		//CEHCK MOBI PLUGIN IS ENABLED OR NOT
    if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('mobi'))
      return;

    //CHECK IF ADMIN
    if (substr($request->getPathInfo(), 1, 5) == "admin") {
      return;
    }

    $mobile = $request->getParam("mobile");
    $session = new Zend_Session_Namespace('mobile');

    if ($mobile == "1") {
      $mobile = true;
      $session->mobile = true;
    } elseif ($mobile == "0") {
      $mobile = false;
      $session->mobile = false;
    } else {
      if (isset($session->mobile)) {
        $mobile = $session->mobile;
      } else {
        if (Engine_Api::_()->mobi()->isMobile()) {
          $mobile = true;
          $session->mobile = true;
        } else {
          $mobile = false;
          $session->mobile = false;
        }
      }
    }

    if (!$mobile) {
      return;
    }

    $module = $request->getModuleName();
    $controller = $request->getControllerName();
    $action = $request->getActionName();
    if ($module == "list") {
      if ($controller == "index" && $action == "home") {
        $request->setControllerName('mobi');
        $request->setActionName('home');
      }

      if ($controller == "index" && $action == "index") {
        $request->setControllerName('mobi');
        $request->setActionName('index');
      }

      if ($controller == "index" && $action == "view") {

        $request->setControllerName('mobi');
        $request->setActionName('view');
      }
    }

    //CREATE LAYOUT
    $layout = Zend_Layout::startMvc();

    //SET OPTIONS
    $layout->setViewBasePath(APPLICATION_PATH . "/application/modules/Mobi/layouts", 'Core_Layout_View')
            ->setViewSuffix('tpl')
            ->setLayout(null);
  }

  public function onRenderLayoutDefault() {

    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $front = Zend_Controller_Front::getInstance();
    $module = $front->getRequest()->getModuleName();
    $controller = $front->getRequest()->getControllerName();
    $action = $front->getRequest()->getActionName();
		$request = Zend_Controller_Front::getInstance()->getRequest();

		//GET CATEGORY TABLE
		$tableCategory = Engine_Api::_()->getDbTable('categories', 'list');

    //MAKING SEO OPTIMIZATION FOR LISTING BROWSE PAGE
		if ($module == "list" && ($controller == "index" || $controller == "mobi") && $action == "index") {
			$siteinfo = $view->layout()->siteinfo;

			//ADD TAGS
			if(isset($_GET['tag']) && !empty($_GET['tag'])) {
				$siteinfo['keywords'] .= ',' . $_GET['tag'];
			}

			if ($request->getParam('category', null)) {
				$category_id = $request->getParam('category', null);
			} elseif ($request->getParam('category_id', null)) {
				$category_id = $request->getParam('category_id', null);
			}
			if ($request->getParam('subcategory', null)) {
				$subcategory_id = $request->getParam('subcategory', null);
			} elseif ($request->getParam('subcategory_id', null)) {
				$subcategory_id = $request->getParam('subcategory_id', null);
			}
			if ($request->getParam('subsubcategory', null)) {
				$subsubcategory_id = $request->getParam('subsubcategory', null);
			} elseif ($request->getParam('subsubcategory_id', null)) {
				$subsubcategory_id = $request->getParam('subsubcategory_id', null);
			}
			if (!empty($category_id)) {
				$row = Engine_Api::_()->getDbtable('categories', 'list')->getCategory($category_id);
				if (!empty($row)) {
					$siteinfo['keywords'] .= $tableCategory->getCategorySlug($row->category_name);
				}
			}
			if (!empty($subcategory_id)) {
				$row = Engine_Api::_()->getDbtable('categories', 'list')->getCategory($subcategory_id);
				if (!empty($row)) {
					$siteinfo['keywords'] .= ',' . $tableCategory->getCategorySlug($row->category_name);
				}
			}
			if (!empty($subsubcategory_id)) {
				$row = Engine_Api::_()->getDbtable('categories', 'list')->getCategory($subsubcategory_id);
				if (!empty($row)) {
					$siteinfo['keywords'] .= ',' . $tableCategory->getCategorySlug($row->category_name);
				}
			}
			$view->layout()->siteinfo = $siteinfo;
		}
		//MAKING SEO OPTIMIZATION FOR LISTING VIEW PAGE
		elseif($module == "list" && ($controller == "index"  || $controller == "mobi") && $action == "view") {
			$siteinfo = $view->layout()->siteinfo;
			$list = Engine_Api::_()->getItem('list_listing', $request->getParam('listing_id', null));
			if (!empty($list->category_id) && !empty($list)) {
				$row = Engine_Api::_()->getDbtable('categories', 'list')->getCategory($list->category_id);
				if (!empty($row)) {
					$siteinfo['keywords'] .= $tableCategory->getCategorySlug($row->category_name);
				}
			}
			if (!empty($list->subcategory_id) && !empty($list)) {
				$row = Engine_Api::_()->getDbtable('categories', 'list')->getCategory($list->subcategory_id);
				if (!empty($row)) {
					$siteinfo['keywords'] .= ',' . $tableCategory->getCategorySlug($row->category_name);
				}
			}
			if (!empty($list->subsubcategory_id) && !empty($list)) {
				$row = Engine_Api::_()->getDbtable('categories', 'list')->getCategory($list->subsubcategory_id);
				if (!empty($row)) {
					$siteinfo['keywords'] .= ',' . $tableCategory->getCategorySlug($row->category_name);
				}
			}
			$view->layout()->siteinfo = $siteinfo;
		}
  }

  public function onStatistics($event) {

    $table = Engine_Api::_()->getDbTable('listings', 'list');
    $select = new Zend_Db_Select($table->getAdapter());
    $select->from($table->info('name'), 'COUNT(*) AS count');
    $event->addResponse($select->query()->fetchColumn(0), 'listing');
  }

  public function onItemDeleteBefore($event) {
    $item = $event->getPayload();
    if ($item instanceof Video_Model_Video) {
      Engine_Api::_()->getDbtable('clasfvideos', 'list')->delete(array('video_id = ?' => $item->getIdentity()));
    }
  }

  public function onUserDeleteBefore($event) {

    $payload = $event->getPayload();
    if ($payload instanceof User_Model_User) {

      // Delete lists
      $listTable = Engine_Api::_()->getDbtable('listings', 'list');
      $listSelect = $listTable->select()->where('owner_id = ?', $payload->getIdentity());
      foreach ($listTable->fetchAll($listSelect) as $list) {
        $list->delete();
      }

      //LIKE COUNT DREASE FORM LISTING TABLE.
      $likesTable = Engine_Api::_()->getDbtable( 'likes' , 'core' );
      $likesTableSelect = $likesTable->select()->where( 'poster_id = ?' , $payload->getIdentity() )->Where( 'resource_type = ?' , 'list_listing' ) ;
      $results =  $likesTable->fetchAll( $likesTableSelect );
      foreach ( $results as $user ) {
        $resource = Engine_Api::_()->getItem('list_listing', $user->resource_id);
        $resource->like_count-- ;
        $resource->save() ;
      }
    }
  }
}