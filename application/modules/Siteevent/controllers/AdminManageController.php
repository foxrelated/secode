<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminManageController.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_AdminManageController extends Core_Controller_Action_Admin {

    //ACTION FOR MANAGE EVENTS
    public function indexAction() {

        $this->view->contentType = $contentType = $this->_getParam('contentType', 'All');
        $this->view->contentModule = $contentModule = $this->_getParam('contentModule', 'siteevent');

        //GET NAVIGATION
        if ($contentModule == 'sitereview') {
            $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                    ->getNavigation($contentModule . '_admin_main', array(), $contentModule . '_admin_main_manageevent');
        } else {
            $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                    ->getNavigation($contentModule . '_admin_main', array(), $contentModule . '_admin_main_manage');
        }

        //MAKE FORM
        $this->view->formFilter = $formFilter = new Siteevent_Form_Admin_Manage_Filter();

        //GET PAGE NUMBER
        $page = $this->_getParam('page', 1);
        $tempManageEventFlag = false;
        //GET USER TABLE NAME
        $tableUserName = Engine_Api::_()->getItemTable('user')->info('name');

        //GET CATEGORY TABLE
        $this->view->tableCategory = $tableCategory = Engine_Api::_()->getDbtable('categories', 'siteevent');
        $tempManageEventFlag = true;
        //GET EVENT TABLE
        $tableEvent = Engine_Api::_()->getDbtable('events', 'siteevent');
        $eventTableName = $tableEvent->info('name');

        //MAKE QUERY
        $select = $tableEvent->select()
                ->setIntegrityCheck(false)
                ->from($eventTableName)
                ->joinLeft($tableUserName, "$eventTableName.owner_id = $tableUserName.user_id", 'username')
                ->group("$eventTableName.event_id");

        if ($contentType && $contentType != 'All' && $contentModule == 'sitereview' && !isset($_POST['search'])) {
            if (strpos($contentType, "sitereview_listing") !== false) {
                $explodedArray = explode("_", $contentType);
                $listingTable = Engine_Api::_()->getDbtable('listings', 'sitereview');
                $listingTableName = $listingTable->info('name');
                $select->join($listingTableName, $listingTableName . '.listing_id = ' . $eventTableName . '.parent_id', array(""));
                $select->where($eventTableName . ".parent_type =?", 'sitereview_listing')
                        ->where($listingTableName . ".listingtype_id =?", $explodedArray[2]);
            }
        } elseif ($contentType && $contentType != 'All' && !isset($_POST['search'])) {
            $select->where($eventTableName . '.parent_type = ? ', $contentType);
        }
        $values = array();

        if ($formFilter->isValid($this->_getAllParams())) {
            $values = $formFilter->getValues();
        }
        foreach ($values as $key => $value) {

            if (null == $value) {
                unset($values[$key]);
            }
        }

      //PACKAGE LIST 
      if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventpaid')) {
          $packageTable = Engine_Api::_()->getDbtable('packages', 'siteeventpaid');
          $packageselect = $packageTable->select()->from($packageTable->info("name"), array("package_id", "title"))->order("package_id DESC");
          $this->view->packageList = $packageTable->fetchAll($packageselect);
      }
    
        // searching
        $this->view->owner = '';
        $this->view->title = '';
        $this->view->sponsored = '';
        $this->view->newlabel = '';
        $this->view->approved = '';
        $this->view->featured = '';
        $this->view->status = '';
        $this->view->eventbrowse = '';
        $this->view->category_id = '';
        $this->view->subcategory_id = '';
        $this->view->subsubcategory_id = '';
        $this->view->starttime = '';
        $this->view->endtime = '';
        $this->view->host_type = '';
        $this->view->host_title = '';
        $this->view->package_id = '';
        $this->view->package = 1;

        if (isset($_POST['search'])) {

            if (!empty($_POST['starttime']) && !empty($_POST['starttime']['date'])) {
                $values['from'] = $_POST['starttime']['date'];
            } else {
                $values['from'] = '';
            }

            if (!empty($_POST['endtime']) && !empty($_POST['endtime']['date'])) {
                $values['to'] = $_POST['endtime']['date'];
            } else {
                $values['to'] = '';
            }

            $this->view->starttime = $values['from'];
            $this->view->endtime = $values['to'];

            if (!empty($this->view->starttime) || !empty($this->view->endtime)) {

                $occurrencesTable = Engine_Api::_()->getDbtable('occurrences', 'siteevent');
                $occurrencesTableName = $occurrencesTable->info('name');

                $select->join($occurrencesTableName, "$occurrencesTableName.event_id = $eventTableName.event_id", array(''))
                        ->group("$occurrencesTableName.event_id");
            }

            if (!empty($this->view->starttime)) {
                $values['from'] = $this->view->starttime . ' 00:00:00';
                $select->where("$occurrencesTableName.starttime >= ?", $values['from']);
            }

            if (!empty($this->view->endtime)) {
                $select->where("$occurrencesTableName.endtime <= ?", $values['to']);
            }

            if (isset($_POST['host_type']) && !empty($_POST['host_type'])) {
                $host_type = $this->view->host_type = $_POST['host_type'];
                if (isset($_POST['host_title']) && !empty($_POST['host_title'])) {
                    $host_title = $this->view->host_title = $_POST['host_title'];
                }
                $select->where('host_type = ?', $host_type);
                if ($host_title) {
                    switch ($host_type) {
                        case 'user':

                            $select->where("$tableUserName.username  LIKE '%$host_title%' OR $tableUserName.displayname  LIKE '%$host_title%'");
                            break;

                        case 'siteevent_organizer':

                            $organizersTable = Engine_Api::_()->getDbtable('organizers', 'siteevent');
                            $organizersTableName = $organizersTable->info('name');
                            $select->joinLeft($organizersTableName, "$organizersTableName.organizer_id = $eventTableName.host_id", array(''))
                                    ->where("$organizersTableName.title  LIKE '%$host_title%'");
                            break;
                        case 'sitepage_page':

                            $pageTable = Engine_Api::_()->getDbtable('pages', 'sitepage');
                            $pageTableName = $pageTable->info('name');
                            $select->joinLeft($pageTableName, "$pageTableName.page_id = $eventTableName.host_id", array(''))
                                    ->where("$pageTableName.title  LIKE '%$host_title%'");
                            break;
                        case 'sitebusiness_business':

                            $businessTable = Engine_Api::_()->getDbtable('business', 'sitebusiness');
                            $businessTableName = $businessTable->info('name');
                            $select->joinLeft($businessTableName, "$businessTableName.business_id = $eventTableName.host_id", array(''))
                                    ->where("$businessTableName.title  LIKE '%$host_title%'");
                            break;
                        case 'sitegroup_group':

                            $groupTable = Engine_Api::_()->getDbtable('groups', 'sitegroup');
                            $groupTableName = $groupTable->info('name');
                            $select->joinLeft($groupTableName, "$groupTableName.group_id = $eventTableName.host_id", array(''))
                                    ->where("$groupTableName.title  LIKE '%$host_title%'");
                            break;

                        case 'sitestore_store':

                            $storeTable = Engine_Api::_()->getDbtable('stores', 'sitestore');
                            $storeTableName = $storeTable->info('name');
                            $select->joinLeft($storeTableName, "$storeTableName.store_id = $eventTableName.host_id", array(''))
                                    ->where("$storeTableName.title  LIKE '%$host_title%'");
                            break;
                    }
                }
            }

            if (!empty($_POST['owner'])) {
                $owner = $this->view->owner = $_POST['owner'];
                $select->where("$tableUserName.username  LIKE '%$owner%' OR $tableUserName.displayname  LIKE '%$owner%'");
            }

            if (!empty($_POST['review_status'])) {
                $this->view->review_status = $review_status = $_POST['review_status'];
                $_POST['review_status']--;

                if ($review_status == 'rating_editor') {
                    $select->where($eventTableName . '.rating_editor > ? ', 0);
                } elseif ($review_status == 'rating_users') {
                    $select->where($eventTableName . '.rating_users > ? ', 0);
                } elseif ($review_status == 'rating_avg') {
                    $select->where($eventTableName . '.rating_avg > ? ', 0);
                } elseif ($review_status == 'both') {
                    $select->where($eventTableName . '.rating_editor > ? ', 0);
                    $select->where($eventTableName . '.rating_users > ? ', 0);
                }
            }

            if (!empty($_POST['title'])) {
                $this->view->title = $_POST['title'];
                $select->where($eventTableName . '.title  LIKE ?', '%' . $_POST['title'] . '%');
            }

            if (!empty($_POST['sponsored'])) {
                $this->view->sponsored = $_POST['sponsored'];
                $_POST['sponsored']--;

                $select->where($eventTableName . '.sponsored = ? ', $_POST['sponsored']);
            }

            if (!empty($_POST['approved'])) {
                $this->view->approved = $_POST['approved'];
                $_POST['approved']--;
                $select->where($eventTableName . '.approved = ? ', $_POST['approved']);
            }

            if (!empty($_POST['featured'])) {
                $this->view->featured = $_POST['featured'];
                $_POST['featured']--;
                $select->where($eventTableName . '.featured = ? ', $_POST['featured']);
            }

            if (!empty($_POST['newlabel'])) {
                $this->view->newlabel = $_POST['newlabel'];
                $_POST['newlabel']--;
                $select->where($eventTableName . '.newlabel = ? ', $_POST['newlabel']);
            }

            if (!empty($_POST['status'])) {
                $this->view->status = $_POST['status'];
                $_POST['status']--;
                $select->where($eventTableName . '.closed = ? ', $_POST['status']);
            }

            if (!empty($_POST['package_id'])) {
                $this->view->package_id = $_POST['package_id'];
                $select->where($eventTableName . '.package_id = ? ', $_POST['package_id']);
            }
            if (!empty($_POST['eventbrowse'])) {
                $this->view->eventbrowse = $_POST['eventbrowse'];
                $_POST['eventbrowse']--;
                if ($_POST['eventbrowse'] == 0) {
                    $select->order($eventTableName . '.view_count DESC');
                } else {
                    $select->order($eventTableName . '.event_id DESC');
                }
            }

            if (isset($_POST['contentType']) && !empty($_POST['contentType']) && $_POST['contentType'] != 'All') {
                $this->view->contentType = $_POST['contentType'];
                if (strpos($_POST['contentType'], "sitereview_listing") !== false) {
                    $explodedArray = explode("_", $_POST['contentType']);
                    $listingTable = Engine_Api::_()->getDbtable('listings', 'sitereview');
                    $listingTableName = $listingTable->info('name');
                    $select->join($listingTableName, $listingTableName . '.listing_id = ' . $eventTableName . '.parent_id', array(""));
                    $select->where($eventTableName . ".parent_type =?", 'sitereview_listing')
                            ->where($listingTableName . ".listingtype_id =?", $explodedArray[2]);
                } else {
                    $select->where($eventTableName . ".parent_type =?", $_POST['contentType']);
                }
            }
            if (!empty($_POST['category_id']) && empty($_POST['subcategory_id']) && empty($_POST['subsubcategory_id'])) {
                $this->view->category_id = $_POST['category_id'];
                $select->where($eventTableName . '.category_id = ? ', $_POST['category_id']);
            } elseif (!empty($_POST['category_id']) && !empty($_POST['subcategory_id']) && empty($_POST['subsubcategory_id'])) {
                $this->view->category_id = $_POST['category_id'];
                $this->view->subcategory_id = $_POST['subcategory_id'];
                $this->view->subcategory_name = $tableCategory->getCategory($this->view->subcategory_id)->category_name;

                $select->where($eventTableName . '.category_id = ? ', $_POST['category_id'])
                        ->where($eventTableName . '.subcategory_id = ? ', $_POST['subcategory_id']);
            } elseif (!empty($_POST['category_id']) && !empty($_POST['subcategory_id']) && !empty($_POST['subsubcategory_id'])) {
                $this->view->category_id = $_POST['category_id'];
                $this->view->subcategory_id = $_POST['subcategory_id'];
                $this->view->subsubcategory_id = $_POST['subsubcategory_id'];
                $this->view->subcategory_name = $tableCategory->getCategory($this->view->subcategory_id)->category_name;
                $this->view->subsubcategory_name = $tableCategory->getCategory($this->view->subsubcategory_id)->category_name;

                $select->where($eventTableName . '.category_id = ? ', $_POST['category_id'])
                        ->where($eventTableName . '.subcategory_id = ? ', $_POST['subcategory_id'])
                        ->where($eventTableName . '.subsubcategory_id = ? ', $_POST['subsubcategory_id']);
            }
        }

        if (empty($tempManageEventFlag)) {
            return;
        }

        $values = array_merge(array(
            'order' => 'event_id',
            'order_direction' => 'DESC',
                ), $values);

        $this->view->assign($values);
        //$this->view->formValues = array_merge($value, $_GET);

        $select->order((!empty($values['order']) ? $values['order'] : 'event_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));
        include APPLICATION_PATH . '/application/modules/Siteevent/controllers/license/license2.php';

        //MAKE PAGINATOR
        $this->view->paginator = Zend_Paginator::factory($select);
        $this->view->paginator->setItemCountPerPage(50);
        $this->view->paginator = $this->view->paginator->setCurrentPageNumber($page);
    }

    //ACTION FOR VIEWING SITEEVENT DETAILS
    public function detailAction() {

        //GET THE SITEEVENT ITEM
        $this->view->siteeventDetail = Engine_Api::_()->getItem('siteevent_event', (int) $this->_getParam('id'));
    }

    //ACTION FOR MULTI-DELETE EVENTS
    public function multiDeleteAction() {

        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();

            foreach ($values as $key => $value) {
                if ($key == 'delete_' . $value) {
                    Engine_Api::_()->getItem('siteevent_event', (int) $value)->delete();
                }
            }
        }
        return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }

}