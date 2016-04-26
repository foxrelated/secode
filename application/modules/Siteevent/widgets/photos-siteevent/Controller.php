<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Widget_PhotosSiteeventController extends Seaocore_Content_Widget_Abstract {

    protected $_childCount;

    public function indexAction() {

        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return $this->setNoRender();
        }

        //GET EVENT SUBJECT
        $this->view->siteevent = $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');

        if (!$this->_getParam('loaded_by_ajax', false) && Engine_Api::_()->siteevent()->hasPackageEnable() && !Engine_Api::_()->siteeventpaid()->allowPackageContent($siteevent->package_id, "photo")) {
           return $this->setNoRender();
        }
        
        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();
        
        //GET PAGINATOR
        $this->view->album = $album = $siteevent->getSingletonAlbum();
        $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
        $this->view->total_images = $total_images = $paginator->getTotalItemCount();

        if (Engine_Api::_()->siteevent()->hasPackageEnable()) {
          $package = Engine_Api::_()->getItem('siteeventpaid_package', $siteevent->package_id);
          if(Engine_Api::_()->siteeventpaid()->allowPackageContent($siteevent->package_id, "photo")) {
            $uploadPhoto = 1;
            if (empty($package->photo_count))
            $this->view->allowed_upload_photo = 1;
            elseif ($package->photo_count > $total_images)
            $this->view->allowed_upload_photo = 1;
          }else{
            $this->view->allowed_upload_photo = $uploadPhoto = 0;
          }
        } else {
          $this->view->allowed_upload_photo = $uploadPhoto = Engine_Api::_()->authorization()->isAllowed($siteevent, $viewer, "photo");
        }         
    
        if (empty($total_images) && !$uploadPhoto) {
            return $this->setNoRender();
        }

        //ADD COUNT TO TITLE
        if ($this->_getParam('titleCount', false) && $total_images > 0) {
            $this->_childCount = $total_images;
        }
        $params = $this->_getAllParams();
        $this->view->params = $params;
        if ($this->_getParam('loaded_by_ajax', false)) {
            $this->view->loaded_by_ajax = true;
            if ($this->_getParam('is_ajax_load', false)) {
                $this->view->is_ajax_load = true;
                $this->view->loaded_by_ajax = false;
                if (!$this->_getParam('onloadAdd', false))
                    $this->getElement()->removeDecorator('Title');
                $this->getElement()->removeDecorator('Container');
            } else {
                return;
            }
        }
        
        $this->view->showContent = true;
        $this->view->itemCount = $this->_getParam('itemCount', 20);

        $paginator->setCurrentPageNumber($this->_getParam('page'));
        $paginator->setItemCountPerPage($this->view->itemCount);
        $this->view->can_edit = $canEdit = $siteevent->authorization()->isAllowed($viewer, "edit");
    }

    public function getChildCount() {
        return $this->_childCount;
    }

}