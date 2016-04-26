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
class Siteevent_Widget_ProfileEventButtonsController extends Seaocore_Content_Widget_Abstract {

    public function indexAction() {
        //DONT RENDER IF SUBJECT IS NOT SET
        if (!Engine_Api::_()->core()->hasSubject('siteevent_event')) {
            return $this->setNoRender();
        }

        $this->view->showButtons = $this->_getParam('showButtons', array('signIn', 'signUp', 'uploadPhotos', 'uploadVideos'));
        
        //GET VIEWER
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $this->view->viewer_id = $viewer_id = $viewer->getIdentity();
        
        if(!in_array('signIn', $this->view->showButtons) && !in_array('signUp', $this->view->showButtons) && !$viewer_id) {
            return $this->setNoRender();
        }        

        //GET EVENT SUBJECT
        $this->view->siteevent = $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');

        //PACKAGE BASED CHECKS
        if (Engine_Api::_()->siteevent()->hasPackageEnable()) {
          $photoCount = Engine_Api::_()->getItem('siteeventpaid_package', $siteevent->package_id)->photo_count;
          $paginator = $siteevent->getSingletonAlbum()->getCollectiblesPaginator();
          if (Engine_Api::_()->siteeventpaid()->allowPackageContent($siteevent->package_id, "photo")) {
            $this->view->allowed_upload_photo = 1;
            if (empty($photoCount))
              $this->view->allowed_upload_photo = 1;
            elseif ($photoCount <= $paginator->getTotalItemCount())
              $this->view->allowed_upload_photo = 0;
          } else {
            $this->view->allowed_upload_photo = 0;
          }
        } else { //GET LEVEL SETTING
          $this->view->allowed_upload_photo = Engine_Api::_()->authorization()->isAllowed($siteevent, $viewer, "photo");
        }
       
        //VIDEO TABLE
        $videoTable = Engine_Api::_()->getDbtable('videos', 'siteevent');

        //TOTAL VIDEO COUNT FOR THIS EVENT
        $this->view->count_video = $counter = $videoTable->getEventVideoCount($siteevent->event_id);
        
        //PACKAGE BASED CHECKS + AUTHORIZATION CHECK
        $this->view->allowed_upload_video = Engine_Api::_()->siteevent()->allowVideo($siteevent, $viewer, $counter);
                 
        $this->view->type_video = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.show.video');
				$this->view->allowPhotoVideo = 1;
        if(!$this->_getParam('show_after_event_finish', 1)){
					$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
					$endDate = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurenceEndDate($siteevent->event_id);

					$currentDate = time();
					$endDate = strtotime($endDate);

					if ($endDate > $currentDate) {
						$this->view->allowPhotoVideo = 0;
					} else {
						$this->view->allowPhotoVideo = 1;
					}
        }
    }

}
