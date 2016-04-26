<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 6590 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitetagcheckin_LocationController extends Core_Controller_Action_Standard {

  //ACTION FOR BROWSE LOCATION PAGES.
	public function byLocationsAction() {
	  if (Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'group' )) {
	    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('group_main', array(), 'sitetagcheckin_main_grouplocation'); 
		  $this->_helper->content->setEnabled();
		} elseif ( Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'advgroup' )) {
			$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('advgroup_main', array(), 'sitetagcheckin_main_groupbylocation'); 
		  $this->_helper->content->setEnabled();
		} 
		else {
			return $this->_forward('notfound', 'error', 'core');
		}
  }
  
  //ACTION FOR BROWSE LOCATION PAGES.
	public function videobyLocationsAction() {
	
	  if (Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'video' )) {
	    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('video_main', array(), 'sitetagcheckin_main_videolocation'); 
		  $this->_helper->content->setEnabled();
		} /*elseif ( Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'advgroup' )) {
			$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('advgroup_main', array(), 'sitetagcheckin_main_groupbylocation'); 
		  $this->_helper->content->setEnabled();
		}*/
		else {
			return $this->_forward('notfound', 'error', 'core');
		}
  }
  
  //ACTION FOR BROWSE LOCATION PAGES.
	public function mobilevideobyLocationsAction() {
	  if (Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'video' )) {
	    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('video_main', array(), 'sitetagcheckin_main_videolocation'); 
		  $this->_helper->content->setEnabled();
		} /*elseif ( Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'advgroup' )) {
			$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('advgroup_main', array(), 'sitetagcheckin_main_groupbylocation'); 
		  $this->_helper->content->setEnabled();
		}*/ 
		else {
			return $this->_forward('notfound', 'error', 'core');
		}
  }
  
  //ACTION FOR BROWSE LOCATION PAGES.
	public function albumbyLocationsAction() {
	
	  if (Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'album' )) {
	    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('album_main', array(), 'sitetagcheckin_main_albumlocation'); 
		  $this->_helper->content->setEnabled();
		} /*elseif ( Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'advgroup' )) {
			$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('advgroup_main', array(), 'sitetagcheckin_main_groupbylocation'); 
		  $this->_helper->content->setEnabled();
		}*/
		else {
			return $this->_forward('notfound', 'error', 'core');
		}
  }
  
  //ACTION FOR BROWSE LOCATION PAGES.
	public function mobilealbumbyLocationsAction() {
	  if (Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'album' )) {
	    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('album_main', array(), 'sitetagcheckin_main_albumlocation'); 
		  $this->_helper->content->setEnabled();
		} /*elseif ( Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'advgroup' )) {
			$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('advgroup_main', array(), 'sitetagcheckin_main_groupbylocation'); 
		  $this->_helper->content->setEnabled();
		}*/ 
		else {
			return $this->_forward('notfound', 'error', 'core');
		}
  }
  
  //ACTION FOR BROWSE LOCATION PAGES.
	public function mobilebyLocationsAction() {
	  if (Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'group' )) {
	    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('group_main', array(), 'sitetagcheckin_main_grouplocation'); 
		  $this->_helper->content->setEnabled();
		} elseif ( Engine_Api::_()->getDbtable( 'modules' , 'core' )->isModuleEnabled( 'advgroup' )) {
			$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('advgroup_main', array(), 'sitetagcheckin_main_groupbylocation'); 
		  $this->_helper->content->setEnabled();
		} 
		else {
			return $this->_forward('notfound', 'error', 'core');
		}
  }

  //ACTION FOR EDIT LOCATION
  public function editLocationAction() {

    //USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    $this->view->resource_type = $resource_type = $this->_getParam('resource_type');

    switch($resource_type) {
			case 'group':
				$resource_id = 'group_id';
			break;
			
			case 'event':
				$resource_id = 'event_id';
			break;
			case 'user':
				$resource_id = 'user_id';
			break;
    }
    
    $this->view->resource_id = $resource_id = $this->_getParam($resource_id);
    $this->view->seao_locationid = $seao_locationid = $this->_getParam('seao_locationid');
    $this->view->resource = $resource = Engine_Api::_()->getItem($resource_type, $resource_id);
    $viewer = Engine_Api::_()->user()->getViewer();
    
		if ($viewer->getIdentity() != $resource->user_id) {
			return $this->_forward('requireauth', 'error', 'core');
		}
		
    $value['id'] = $seao_locationid;
    $this->view->location = $location = Engine_Api::_()->getDbtable('locationitems', 'seaocore')->getLocations($value);

    //Get form
    if (!empty($location)) {

      $this->view->form = $form = new Seaocore_Form_Location(array(
				'item' => $resource,
				'location' => $location->location
      ));

      if (!$this->getRequest()->isPost()) {
        $form->populate($location->toarray()
      );
        return;
      }

      //FORM VALIDAITON
      if (!$form->isValid($this->getRequest()->getPost())) {
        return;
      }

      //FORM VALIDAITON
      if ($form->isValid($this->getRequest()->getPost())) {

        $values = $form->getValues();
        unset($values['submit']);
        unset($values['location']);

 				$seLocationsTable = Engine_Api::_()->getDbtable('locationitems', 'seaocore');
        $seLocationsTable->update($values, array('locationitem_id =?' => $seao_locationid));
      }
      $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes have been saved.'));
    }
    $this->view->location = Engine_Api::_()->getDbtable('locationitems', 'seaocore')->getLocations($value);
  }

  //ACTION FOR EDIT ADDRESS
  public function editAddressAction() {

    //USER VALIDATION
    if (!$this->_helper->requireUser()->isValid())
      return;

    //GET PAGE ID, PAGE OBJECT AND THEN CHECK PAGE VALIDATION
    $resource_type = $this->_getParam('resource_type');
    switch($resource_type) {
			case 'group':
				$id = 'group_id';
				$itemTable = Engine_Api::_()->getItemTable('group');
				$route = 'sitetagcheckin_groupspecific';
			break;
			
			case 'user':
				$id = 'user_id';
				$itemTable = Engine_Api::_()->getDbtable('users', 'user');
				$route = 'sitetagcheckin_userspecific';
			break;
			
			case 'event':
				$id = 'event_id';
				$itemTable = Engine_Api::_()->getItemTable('event');
				$route = 'sitetagcheckin_specific';
			break;
    }

    $seao_locationid = $this->_getParam('seao_locationid');
    $resource_id = $this->_getParam($id);
    $resource = Engine_Api::_()->getItem($resource_type, $resource_id);

    $this->view->form = $form = new Sitetagcheckin_Form_Address(array('item' => $resource));

    //POPULATE FORM
    if (!$this->getRequest()->isPost()) {
      $form->populate($resource->toArray());
      return;
    }

    //FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {

      $values = $form->getValues();

      //Update field value
			if ($resource_type == 'user') {

        $aliasValues = Engine_Api::_()->fields()->getFieldsValuesByAlias($resource);

				$db = Zend_Db_Table_Abstract::getDefaultAdapter();
				$table_exist = $db->query('SHOW TABLES LIKE \'engine4_user_fields_search\'')->fetch();
				if (!empty($table_exist)) {
					$column_exist = $db->query('SHOW COLUMNS FROM engine4_user_fields_search LIKE \'location\'')->fetch();
				}
				
				$profilemapsTable = Engine_Api::_()->getDbtable('profilemaps', 'sitetagcheckin');
				$profilemapsTablename = $profilemapsTable->info('name');
				
				$select = $profilemapsTable->select()->from($profilemapsTablename, array('profile_type'));
				if (empty($aliasValues['profile_type'])) {
					$select->where($profilemapsTablename . '.option_id = ?', 1);	
				} 
				else {
					$select->where($profilemapsTablename . '.option_id = ?', $aliasValues['profile_type']);
				}
				$option_id =  $select->query()->fetchColumn();
				
				if (!empty($option_id)) {
					$valuesTable = Engine_Api::_()->fields()->getTable('user', 'values');
					$valuesTableName = $valuesTable->info('name');

					$select = $valuesTable->select()
										->from($valuesTableName, array('value'))
										->where($valuesTableName . '.item_id = ?', $resource_id)
										->where($valuesTableName . '.field_id = ?', $option_id);
					$valuesResultsLocation = $select->query()->fetchColumn();
					if (!empty($valuesResultsLocation)) {
						Engine_Api::_()->fields()->getTable('user', 'values')->update(array('value'=> $values['location']), array('item_id =?' => $resource_id, 'field_id =?' => $option_id));
						
						if (!empty($column_exist)) {
							Engine_Api::_()->fields()->getTable('user', 'search')->update(array('location' => $values['location']), array('item_id =?' => $resource_id));
						}
						
					} else {
						$valuesTable = Engine_Api::_()->fields()->getTable('user', 'values');
						$row = $valuesTable->createRow();
						$row->item_id = $resource_id;
						$row->field_id = $option_id;
						$row->index = 0;
						$row->value = $values['location'];
						$row->save();
						if (!empty($column_exist)) {
							Engine_Api::_()->fields()->getTable('user', 'search')->update(array('location' => $values['location']), array('item_id =?' => $resource_id));
						}
					}
				}
			}

      $resource->location = $values['location'];
      if (empty($values['location'])) {
			  //DELETE THE RESULT FORM THE TABLE.
				Engine_Api::_()->getDbtable('locationitems', 'seaocore')->delete(array('resource_id =?' => $resource_id, 'resource_type = ?' => $resource_type));
				$resource->seao_locationid = '0';
			}
      $resource->save();
      unset($values['submit']);

			if (!empty($values['location'])) {
			
				//DELETE THE RESULT FORM THE TABLE.
				Engine_Api::_()->getDbtable('locationitems', 'seaocore')->delete(array('resource_id =?' => $resource_id, 'resource_type = ?' => $resource_type));
			
				$seaoLocation = Engine_Api::_()->getDbtable('locationitems', 'seaocore')->getLocationItemId($values['location'], '', $resource_type, $resource_id);

				//group table entry of location id.
				$itemTable->update(array('seao_locationid'=>  $seaoLocation), array("$id =?" => $resource_id));
			}

      $db->commit();
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 500,
        'parentRedirect' => $this->_helper->url->url(array('action' => 'edit-location', 'seao_locationid' => $seaoLocation, "$id" => $resource_id, "resource_type" => $resource_type), "$route", true),
        'parentRedirectTime' => '2',
        'format' => 'smoothbox',
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your location has been modified successfully.'))
      ));
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

  //ACTION FOR BROWSE LOCATION PAGES.
	public function userbyLocationsAction() {
	  $this->_helper->content->setEnabled();
  }

  //ACTION FOR BROWSE LOCATION PAGES.
	public function usermobilebyLocationsAction() {
	  $this->_helper->content->setEnabled();
  }
  
    //ACTION FOR USER AUTO SUGGEST.
  public function getmemberAction() {

    $data = array();

    $usersTable = Engine_Api::_()->getDbtable('users', 'user');
    $usersTableName = $usersTable->info('name');

    $select = $usersTable->select();

    $select->where('displayname  LIKE ? ', '%' . $this->_getParam('text') . '%')
          ->order('displayname ASC')->limit('40');
    $users = $usersTable->fetchAll($select);

    foreach ($users as $user) {
      $user_photo = $this->view->itemPhoto($user, 'thumb.icon');
      $data[] = array(
				'id' => $user->user_id,
				'label' => $user->displayname,
				'photo' => $user_photo
      );
    }

    return $this->_helper->json($data);
  }
}