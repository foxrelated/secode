<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestoreproduct_Widget_StoreStartupLinkController extends Engine_Content_Widget_Abstract {

    public function indexAction() {
        $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
        
        // CHECK IF ANY PACKAGE AVAILABLE OR NOT FOR THIS MEMBER
        if(!empty($viewer_id) && Engine_Api::_()->sitestore()->hasPackageEnable()) {
            $packages_select = Engine_Api::_()->getItemtable('sitestore_package')->getPackagesSql(null);    
            $paginator = Zend_Paginator::factory($packages_select);
            $getPaginatorCount = $paginator->getTotalItemCount();   
            if( empty($getPaginatorCount) ) 
                return $this->setNoRender();
        }
        
        // MEMBER LEVEL SETTINGS NOT ALLOWED TO CREATE STORE
        $menusClass = new Sitestore_Plugin_Menus();        
        $canCreateStores = $menusClass->canCreateSitestores();
        if (!$canCreateStores ||
                (!Engine_Api::_()->sitestore()->hasPackageEnable() &&
                !Engine_Api::_()->sitestoreproduct()->getLevelSettings("allow_store_create")
                )
        ) {
            return $this->setNoRender();
        }
    }

}