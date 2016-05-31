<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteiosapp
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminViewMapsListingTypeController.php 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteiosapp_AdminViewMapsListingTypeController extends Core_Controller_Action_Admin {

    //ACTION FOR MANAGING THE PROFILE-CATEGORY MAPPING
    public function manageAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('siteiosapp_admin_main', array(), 'siteiosapp_admin_api_menus');
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        //GET LISTING TYPE TABLE
        $listingTypeTable = Engine_Api::_()->getDbTable('listingtypes', 'sitereview');
        $listingTypes = Engine_Api::_()->getDbTable('listingtypes', 'sitereview')->getListingTypes();
        $this->view->listingTypes = $listingTypes;
        foreach ($listingTypes as $listingType) {
            $select = new Zend_Db_Select($db);
            $select->from('engine4_siteiosapp_listingtypeViewMaps')
                    ->where('listingtype_id = ?', $listingType['listingtype_id']);
            $isViewRowExist = $select->query()->fetchObject();
            $profileMapViewArray[$listingType['listingtype_id']] = $this->_getViewTypeLabel($isViewRowExist->profileView_id, 1);
            $browseMapViewArray[$listingType['listingtype_id']] = $this->_getViewTypeLabel($isViewRowExist->browseView_id, 2);
        }
        $this->view->profileMapViewArray = $profileMapViewArray;
        $this->view->browseMapViewArray = $browseMapViewArray;
    }

    //ACTION FOR MAP THE PROFILE WITH CATEGORY
    public function mapAction() {
        //DEFAULT LAYOUT
        $this->_helper->layout->setLayout('admin-simple');

        //GET CATEGORY ID
        $this->view->listingtype_id = $listingtype_id = $this->_getParam('listingtype_id');

        //GET MAPPING ITEM
        $listingType = Engine_Api::_()->getItem('sitereview_listingtype', $listingtype_id);

        //GENERATE THE FORM
        $this->view->form = $form = new Siteiosapp_Form_Admin_ViewMapsListingType_Map(array('listingTypeId' => $listingtype_id));


        //POST DATA
        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            //BEGIN TRANSCATION
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {

                include_once APPLICATION_PATH . '/application/modules/Sitereview/controllers/license/license2.php';

                //IF YES BUTTON IS CLICKED THEN CHANGE MAPPING OF ALL LISTINGS
                if (isset($_POST['yes_button'])) {
                    $db = Engine_Db_Table::getDefaultAdapter();
                    $browseViewType = $_POST['browse_view_type'];
                    $profileViewType = $_POST['profile_view_type'];
                    $select = new Zend_Db_Select($db);
                    $select->from('engine4_siteiosapp_listingtypeViewMaps')
                            ->where('listingtype_id = ?', $listingType['listingtype_id']);
                    $isViewRowExist = $select->query()->fetchObject();
                    if (isset($isViewRowExist) && !empty($isViewRowExist)) {
                        $db->query("UPDATE `engine4_siteiosapp_listingtypeViewMaps` SET `profileView_id` ='$profileViewType', `browseView_id` =  '$browseViewType' WHERE `engine4_siteiosapp_listingtypeViewMaps`.`listingtype_id` = '$listingtype_id'  LIMIT 1");
                    } else {
                        $db->query("INSERT INTO `engine4_siteiosapp_listingtypeViewMaps` (`listingtype_id`, `profileView_id`, `browseView_id`) VALUES ('$listingtype_id', '$profileViewType', '$browseViewType')");
                    }
                }

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('View Type Mapping has been done successfully.'))
            ));
        }

        $this->renderScript('admin-view-maps-listing-type/map.tpl');
    }

    //return view name for rescpective ID 
    private function _getViewTypeLabel($viewId, $viewType) {
        //for profile Type View
        if (isset($viewType) && $viewType == 1) {

            $viewId = (!isset($viewId)) ? 3 : $viewId;
            switch ($viewId) {
                case 1:
                    $label = Zend_Registry::get('Zend_Translate')->_("Blog View");
                    break;
                case 2:
                    $label = Zend_Registry::get('Zend_Translate')->_("Classified 1 View");
                    break;
                case 3:
                    $label = Zend_Registry::get('Zend_Translate')->_("Classified 2 View");
                    break;
                default :
                    $label = "-";
            }
        } else if (isset($viewType) && $viewType == 2) {
            $viewId = (!isset($viewId)) ? 2 : $viewId;
            switch ($viewId) {
                case 1:
                    $label = Zend_Registry::get('Zend_Translate')->_("List View");
                    break;
                case 2:
                    $label = Zend_Registry::get('Zend_Translate')->_("Grid View");
                    break;
                case 3:
                    $label = Zend_Registry::get('Zend_Translate')->_("Matrix View");
                    break;
                default :
                    $label = "-";
            }
        }
        return $label;
    }

}
