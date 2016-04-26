<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Dashboardmenus.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Plugin_Dashboardmenus {

    public function onMenuInitialize_SiteeventDashboardEditinfo($row) {

        //GET EVENT ID AND SITEEVENT OBJECT
        $event_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('event_id', null);
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        if ($siteevent->getType() !== 'siteevent_event') {
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        ;
        $editPrivacy = $siteevent->authorization()->isAllowed($viewer, "edit");
        if (empty($editPrivacy)) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'siteevent_specific',
            'action' => 'edit',
            'params' => array(
                'event_id' => $siteevent->getIdentity()
            ),
        );
    }
    
    public function onMenuInitialize_SiteeventDashboardWaitlist($row) {

        //GET EVENT ID AND SITEEVENT OBJECT
        $event_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('event_id', null);
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        if ($siteevent->getType() !== 'siteevent_event') {
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();

        $editPrivacy = $siteevent->authorization()->isAllowed($viewer, "edit");
        if (empty($editPrivacy)) {
            return false;
        }
        
        if(!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.waitlist', 1)) {
            return false;
        }        

        return array(
            'label' => $row->label,
            'route' => 'siteevent_extended',
            'controller' => 'waitlist',
            //'class' => 'ajax_dashboard_enabled',
            'action' => 'manage',
            'params' => array(
                'event_id' => $siteevent->getIdentity()
            ),
        );
    }    

    public function onMenuInitialize_SiteeventDashboardOverview($row) {

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.overview', 1))
            return false;
        //GET EVENT ID AND SITEEVENT OBJECT
        $event_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('event_id', null);
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        if ($siteevent->getType() !== 'siteevent_event') {
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $editPrivacy = $siteevent->authorization()->isAllowed($viewer, "edit");
        if (empty($editPrivacy)) {
            return false;
        }

        //PACKAGE BASED CHECKS
        if (Engine_Api::_()->siteevent()->hasPackageEnable() && !Engine_Api::_()->siteeventpaid()->allowPackageContent($siteevent->package_id, "overview")) {
              return false;
        } else {       
          $overviewPrivacy = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'siteevent_event', "overview");
          if (empty($overviewPrivacy)) {
               return false;
          }   
        }
        return array(
            'label' => $row->label,
            'route' => 'siteevent_specific',
            'action' => 'overview',
            'params' => array(
                'event_id' => $siteevent->getIdentity()
            ),
        );
    }

    public function onMenuInitialize_SiteeventDashboardProfilepicture($row) {

        //GET EVENT ID AND SITEEVENT OBJECT
        $event_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('event_id', null);
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        if ($siteevent->getType() !== 'siteevent_event') {
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $editPrivacy = $siteevent->authorization()->isAllowed($viewer, "edit");
        if (empty($editPrivacy)) {
            return false;
        }

        //AUTHORIZATION CHECK
        $allowed_upload_photo = Engine_Api::_()->authorization()->isAllowed($siteevent, $viewer, "photo");
        if (empty($allowed_upload_photo)) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'siteevent_dashboard',
            'action' => 'change-photo',
            'class' => 'ajax_dashboard_enabled',
            'params' => array(
                'event_id' => $siteevent->getIdentity()
            ),
        );
    }

    public function onMenuInitialize_SiteeventDashboardContact($row) {
        
        //GET EVENT ID AND SITEEVENT OBJECT
        $event_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('event_id', null);
        $siteevent = Engine_Api::_()->core()->getSubject('siteevent_event');
        if ($siteevent->getType() !== 'siteevent_event') {
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $editPrivacy = $siteevent->authorization()->isAllowed($viewer, "edit");
        if (empty($editPrivacy)) {
            return false;
        }

        $contactPrivacy = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'siteevent_event', "contact");

        if (empty($contactPrivacy)) {
            return false;
        }

        $contactDetailsSettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.contactdetail', array('phone', 'website', 'email'));

        if (empty($contactDetailsSettings)) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'siteevent_dashboard',
            'action' => 'contact',
            'class' => 'ajax_dashboard_enabled',
            'params' => array(
                'event_id' => $siteevent->getIdentity()
            ),
        );
    }

    public function onMenuInitialize_SiteeventDashboardEditlocation($row) {
        
        //GET EVENT ID AND SITEEVENT OBJECT
        $event_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('event_id', null);
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        if ($siteevent->getType() !== 'siteevent_event') {
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $editPrivacy = $siteevent->authorization()->isAllowed($viewer, "edit");
        if (empty($editPrivacy)) {
            return false;
        }

        if (Engine_Api::_()->siteevent()->enableLocation() && empty($siteevent->is_online)) {
            return array(
                'label' => $row->label,
                'route' => 'siteevent_specific',
                'action' => 'editlocation',
                'params' => array(
                    'event_id' => $siteevent->getIdentity()
                ),
            );
        }

        return false;
    }

    public function onMenuInitialize_SiteeventDashboardEditphoto($row) {

        //GET EVENT ID AND SITEEVENT OBJECT
        $event_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('event_id', null);
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        if ($siteevent->getType() !== 'siteevent_event') {
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $editPrivacy = $siteevent->authorization()->isAllowed($viewer, "edit");
        if (empty($editPrivacy)) {
            return false;
        }

        //PACKAGE BASED CHECKS
        if (Engine_Api::_()->siteevent()->hasPackageEnable()) {
            $allowPhotoUpload = Engine_Api::_()->siteeventpaid()->allowPackageContent($siteevent->package_id, "photo") ? 1 : 0;
        } else { //AUTHORIZATION CHECKS
            $allowPhotoUpload = Engine_Api::_()->authorization()->isAllowed($siteevent, $viewer, "photo");
        }
        if (empty($allowPhotoUpload)) {
            return false;
        }
				$request = Zend_Controller_Front::getInstance()->getRequest();
				$module = $request->getModuleName();
				$controller = $request->getControllerName();
				$action = $request->getActionName();

				if($module == 'siteevent'  && $controller == 'photo' && $action == 'upload') {
						return array(
								'label' => $row->label,
								'route' => 'siteevent_photoalbumupload',
								'action' => 'upload',
								'params' => array(
										'event_id' => $siteevent->getIdentity(),
										'content_id' => Zend_Controller_Front::getInstance()->getRequest()->getParam('content_id', null)
								),
						);
				} else {
						return array(
								'label' => $row->label,
								'route' => 'siteevent_albumspecific',
								'action' => 'editphotos',
								'class' => 'ajax_dashboard_enabled',
								'params' => array(
										'event_id' => $siteevent->getIdentity()
								),
						);
				}

    }

    public function onMenuInitialize_SiteeventDashboardEditvideo($row) {

        
        //GET EVENT ID AND SITEEVENT OBJECT
        $event_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('event_id', null);
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        if ($siteevent->getType() !== 'siteevent_event') {
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $editPrivacy = $siteevent->authorization()->isAllowed($viewer, "edit");
        if (empty($editPrivacy)) {
            return false;
        }

        //PACKAGE BASED CHECKS
        if (Engine_Api::_()->siteevent()->hasPackageEnable()) {
            $allowVideoUpload = Engine_Api::_()->siteeventpaid()->allowPackageContent($siteevent->package_id, "video") ? 1 : 0;
        } else {
            $allowVideoUpload = Engine_Api::_()->siteevent()->allowVideo($siteevent, $viewer);
        }
        if (empty($allowVideoUpload)) {
            return false;
        }

				$request = Zend_Controller_Front::getInstance()->getRequest();
				$module = $request->getModuleName();
				$controller = $request->getControllerName();
				$action = $request->getActionName();
				$type_video = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.show.video');

				if($module == 'siteevent'  && $controller == 'video' && ($action == 'index' || $action == 'create')) {
						if($type_video) {
							return array(
									'label' => $row->label,
									'route' => 'siteevent_video_upload',
									'action' => 'index',
									'params' => array(
											'event_id' => $siteevent->getIdentity(),
											'content_id' => Zend_Controller_Front::getInstance()->getRequest()->getParam('content_id', null)
									),
							);
					} else {
							return array(
									'label' => $row->label,
									'route' => 'siteevent_video_create',
									'action' => 'create',
									'params' => array(
											'event_id' => $siteevent->getIdentity(),
											'content_id' => Zend_Controller_Front::getInstance()->getRequest()->getParam('content_id', null)
									),
							);
					}
				} else {

					return array(
							'label' => $row->label,
							'route' => 'siteevent_videospecific',
							'class' => 'ajax_dashboard_enabled',
							'action' => 'edit',
							'params' => array(
									'event_id' => $siteevent->getIdentity()
							),
					);
			}
    }

    public function onMenuInitialize_SiteeventDashboardEditmetakeyword($row) {
        
        //GET EVENT ID AND SITEEVENT OBJECT
        $event_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('event_id', null);
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        if ($siteevent->getType() !== 'siteevent_event') {
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $editPrivacy = $siteevent->authorization()->isAllowed($viewer, "edit");
        if (empty($editPrivacy)) {
            return false;
        }

        $allowMetaKeywords = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'siteevent_event', "metakeyword");

        if (empty($allowMetaKeywords) || !Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.metakeyword', 1)) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'siteevent_dashboard',
            'action' => 'meta-detail',
            'class' => 'ajax_dashboard_enabled',
            'href' => '',
            'params' => array(
                'event_id' => $siteevent->getIdentity()
            ),
        );
    }

    public function onMenuInitialize_SiteeventDashboardNotificationsettings($row) {

        //GET EVENT ID AND SITEEVENT OBJECT
        $event_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('event_id', null);
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        if ($siteevent->getType() !== 'siteevent_event') {
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
//        $editPrivacy = $siteevent->authorization()->isAllowed($viewer, "edit");
//        if (empty($editPrivacy)) {
//            return false;
//        }
        $row = $siteevent->membership()
                    ->getRow($viewer);
        if(!$row)
         return false;
        return array(
            'label' => $row->label,
            'route' => 'siteevent_dashboard',
            'action' => 'notification-settings',
            'class' => 'ajax_dashboard_enabled',
            'params' => array(
                'event_id' => $siteevent->getIdentity()
            ),
        );
    }

    public function onMenuInitialize_SiteeventDashboardManageleaders($row) {

        //GET EVENT ID AND SITEEVENT OBJECT
        $event_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('event_id', null);
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        if ($siteevent->getType() !== 'siteevent_event') {
            return false;
        }

        $itemTypeValue = $siteevent->getParent()->getType();
        $multipleLeader = 0;
        if ($itemTypeValue == 'sitereview_listing') {
            $item = Engine_Api::_()->getItem('sitereview_listing', $siteevent->getIdentity());
            $itemTypeValue = $itemTypeValue . $item->listingtype_id;
            $multipleLeader = Engine_Api::_()->getApi('settings', 'core')->getSetting("siteevent.multiple.leader.$itemTypeValue", 0);
        } elseif ($itemTypeValue != 'user') {
            $multipleLeader = Engine_Api::_()->getApi('settings', 'core')->getSetting("siteevent.multiple.leader.$itemTypeValue", 0);
        }
        if($itemTypeValue != 'user' && !$multipleLeader) {
            return false;
        }

        if ($itemTypeValue == 'user' && !Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.leader', 1)) {
            return false;
        }
        
        $viewer = Engine_Api::_()->user()->getViewer();
        $editPrivacy = $siteevent->authorization()->isAllowed($viewer, "edit");
        if (empty($editPrivacy)) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'siteevent_extended',
            'controller' => 'member',
            'action' => 'manage-leaders',
            'class' => 'ajax_dashboard_enabled',
            'params' => array(
                'event_id' => $siteevent->getIdentity()
            ),
        );
    }

    public function onMenuInitialize_SiteeventDashboardAnnouncements($row) {

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.announcement', 1)) {
            return false;
        }

        //GET EVENT ID AND SITEEVENT OBJECT
        $event_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('event_id', null);
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        if ($siteevent->getType() !== 'siteevent_event') {
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $editPrivacy = $siteevent->authorization()->isAllowed($viewer, "edit");
        if (empty($editPrivacy)) {
            return false;
        }

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $url = $view->url(array('controller' => 'announcement', 'action' => 'manage', 'event_id' => $siteevent->event_id), "siteevent_extended", true);

        return array(
            'label' => $row->label,
            'route' => 'siteevent_extended',
            'controller' => 'announcement',
            'action' => 'manage',
            'class' => 'ajax_dashboard_enabled',
            'params' => array(
                'event_id' => $siteevent->getIdentity()
            ),
        );
    }

    public function onMenuInitialize_SiteeventDashboardEditstyle($row) {

        //GET EVENT ID AND SITEEVENT OBJECT
        $event_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('event_id', null);
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        if ($siteevent->getType() !== 'siteevent_event') {
            return false;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $editPrivacy = $siteevent->authorization()->isAllowed($viewer, "edit");
        if (empty($editPrivacy)) {
            return false;
        }

        $stylePrivacy = $siteevent->authorization()->isAllowed($viewer, "style");
        if (empty($stylePrivacy)) {
            return false;
        }

        return array(
            'label' => $row->label,
            'route' => 'siteevent_specific',
            'action' => 'editstyle',
            'class' => 'ajax_dashboard_enabled',
            'params' => array(
                'event_id' => $siteevent->getIdentity()
            ),
        );
    }
    
    public function onMenuInitialize_SiteeventDashboardPackages($row) {
        
        //GET EVENT ID AND SITEEVENT OBJECT
        $event_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('event_id', null);
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        if ($siteevent->getType() !== 'siteevent_event') {
            return false;
        }        
        
        if(!Engine_Api::_()->siteevent()->hasPackageEnable()) {
            return false;
        }        

        return array(
            'label' => $row->label,
            'route' => 'siteevent_package',
            'action' => 'update-package',
            'class' => 'ajax_dashboard_enabled',
            'params' => array(
                'event_id' => $siteevent->getIdentity()
            ),
        );
    }      

}