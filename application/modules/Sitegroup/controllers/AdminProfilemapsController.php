<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminProfilemapsController.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_AdminProfilemapsController extends Core_Controller_Action_Admin {

	//ACTION FOR MANAGING THE PROFILE-CATEGORY MAPPING
  public function manageAction() {

		//GET NAVIGATION
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('sitegroup_admin_main', array(), 'sitegroup_admin_main_profilemaps');

		//FETCH MAPPING DATA
    $tableCategory = Engine_Api::_()->getDbtable('profilemaps', 'sitegroup')->info('name');
    $table = Engine_Api::_()->getDbtable('categories', 'sitegroup');

    $rName = $table->info('name');
    $select = $table->select()
            ->setIntegrityCheck(false)
            ->from($rName)
            ->joinLeft($tableCategory, "$rName.category_id = $tableCategory.category_id", array('profile_type', 'profilemap_id'))
            ->joinLeft('engine4_sitegroup_group_fields_options', "engine4_sitegroup_group_fields_options.option_id = $tableCategory.profile_type", array('label'))
            ->where($rName . ".cat_dependency = ?", 0);
    include APPLICATION_PATH . '/application/modules/Sitegroup/controllers/license/license2.php';
		$this->view->paginator->setItemCountPerPage(500);
  }

	//ACTION FOR MAP THE PROFILE WITH CATEGORY
  public function mapAction() {

    $this->_helper->layout->setLayout('admin-simple');

    //GENERATE THE FORM
    $form = $this->view->form = new Sitegroup_Form_Admin_Map();

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $values = $form->getValues();

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try {

				//SAVE THE NEW MAPPING
        $row = Engine_Api::_()->getDbtable('profilemaps', 'sitegroup')->createRow();
        $row->profile_type = $values['profile_type'];
        $row->category_id = $this->_getParam('category_id');
        $row->save();
        $db->commit();

				//IF YES BUTTON IS CLICKED THEN CHANGE MAPPING OF ALL GROUP
        if (isset($_POST['yes_button'])) {
	
					//SELECT GROUP WHICH HAVE THIS CATEGORY AND THIS PROFILE TYPE
          $rows = Engine_Api::_()->getDbTable('groups', 'sitegroup')->getCategoryGroup($this->_getParam('category_id'), $row->profile_type);

          if (!empty($rows)) {
            foreach ($rows as $key => $group_ids) {
              $group_id = $group_ids['group_id'];

              $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
              if ($row->profile_type != $sitegroup->profile_type) {

                $fieldvalueTable = Engine_Api::_()->fields()->getTable('sitegroup_group', 'values');
                $fieldvalueTable->delete(array(
                    'item_id = ?' => $group_id,
                ));

                $fieldsearchTable = Engine_Api::_()->fields()->getTable('sitegroup_group', 'search');
                $fieldsearchTable->delete(array(
                    'item_id = ?' => $group_id,
                ));

                //PUT NEW PROFILE TYPE
                $fieldvalueTable->insert(array(
                    'item_id' => $sitegroup->group_id,
                    'field_id' => 1,
                    'index' => 0,
                    'value' => $row->profile_type,
                ));

                $sitegroup->profile_type = $row->profile_type;
                $sitegroup->save();
              }
            }
          }
        }
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_(''))
      ));
    }

    $this->renderScript('admin-profilemaps/map.tpl');
  }

	//ACTION FOR DELETE MAPPING 
  public function deleteAction() {
    $this->_helper->layout->setLayout('admin-simple');

		//GET MAPPING ID
    $this->view->profilemap_id = $profilemap_id = $this->_getParam('profilemap_id');

    if ($this->getRequest()->isPost()) {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      try {

				//GET MAPPING ITEM
        $sitegroup_profilemap = Engine_Api::_()->getItem('sitegroup_profilemap', $profilemap_id);

				//DELETE MAPPING
        $sitegroup_profilemap->delete();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => 10,
          'parentRefresh' => 10,
          'messages' => array(Zend_Registry::get('Zend_Translate')->_('Mapping deleted successfully !'))
      ));
    }
    $this->renderScript('admin-profilemaps/delete.tpl');
  }
}

?>