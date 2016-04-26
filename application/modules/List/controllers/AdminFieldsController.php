<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: AdminFieldsController.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_AdminFieldsController extends Fields_Controller_AdminAbstract
{
  protected $_fieldType = 'list_listing';

  protected $_requireProfileType = true;

	//MANAGE PROFILE FIELDS
  public function indexAction()
  {
    //Make navigation
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('list_admin_main', array(), 'list_admin_main_fields');

		parent::indexAction();
  }

	//ACTION FOR CREATING THE NEW FIELD
  public function fieldCreateAction(){

    parent::fieldCreateAction();

		//GET FORM
    $form = $this->view->form;

    if($form){

			$form->removeElement('show');
			$form->addElement('hidden', 'show', array('value' => 0));

      $display = $form->getElement('display');
      $display->setLabel('Show on listing page?');
      $display->setOptions(array('multiOptions' => array(
          1 => 'Show on listing page',
          0 => 'Hide on listing page'
      )));

      $search = $form->getElement('search');
      $search->setLabel('Show on the search options?');
      $search->setOptions(array('multiOptions' => array(
          0 => 'Hide on the search options',
          1 => 'Show on the search options'
      )));
    }
  }

	//ACTION FOR EDIT THE FIELD
  public function fieldEditAction(){

    parent::fieldEditAction();

		//GET FORM
    $form = $this->view->form;

    if($form){
      $form->setTitle('Edit Listing Question');

			$form->removeElement('show');
			$form->addElement('hidden', 'show', array('value' => 0));

      $display = $form->getElement('display');
      $display->setLabel('Show on listing page?');
      $display->setOptions(array('multiOptions' => array(
          1 => 'Show on listing page',
          0 => 'Hide on listing page'
      )));

      $search = $form->getElement('search');
      $search->setLabel('Show on the search options?');
      $search->setOptions(array('multiOptions' => array(
          0 => 'Hide on the search options',
          1 => 'Show on the search options'
      )));
    }
  }

	//ACTION FOR HEADING CREATION
  public function headingCreateAction() {
    parent::headingCreateAction();

    //GENERATE FORM
    $form = $this->view->form;

    if ($form) {
      $form->removeElement('show');
      $form->addElement('hidden', 'show', array('value' => 0));

      $form->removeElement('display');
      $form->addElement('hidden', 'display', array('value' => 1));
    }
  }

	//ACTION FOR HEADING EDITION
  public function headingEditAction() {
    parent::headingEditAction();

    //GENERATE FORM
    $form = $this->view->form;

    if ($form) {
      $form->removeElement('show');
      $form->addElement('hidden', 'show', array('value' => 0));

      $form->removeElement('display');
      $form->addElement('hidden', 'display', array('value' => 1));
    }
  }

	//ACTION FOR PROFILE DELETION
  public function typeDeleteAction() {
    $option_id = $this->_getParam('option_id');

    if (!empty($option_id)) {

      //DELETE FIELD ENTRIES IF EXISTS
      $fieldmapsTable = Engine_Api::_()->fields()->getTable('list_listing', 'maps');
      $select = $fieldmapsTable->select()->where('option_id = ?', $option_id);
      $metaData = $fieldmapsTable->fetchAll($select)->toArray();
      if (!empty($metaData)) {
        foreach ($metaData as $key => $child_ids) {
          $child_id = $child_ids['child_id'];

          //DELETE FIELD ENTRIES IF EXISTS
          $fieldmetaTable = Engine_Api::_()->fields()->getTable('list_listing', 'meta');
          $fieldmetaTable->delete(array(
              'field_id = ?' => $child_id,
          ));
        }
      }

      $fieldmapsTable = Engine_Api::_()->fields()->getTable('list_listing', 'maps');
      $fieldmapsTable->delete(array(
          'option_id = ?' => $option_id,
      ));

      $listingTable = Engine_Api::_()->getDbtable('listings', 'list');
      $select = $listingTable->select()
              ->from($listingTable->info('name'), array('listing_id'))
              ->where('profile_type = ?', $option_id);
      $rows = $listingTable->fetchAll($select)->toArray();
      if (!empty($rows)) {
        foreach ($rows as $key => $listing_ids) {
          $listing_id = $listing_ids['listing_id'];

          $listing = Engine_Api::_()->getItem('list_listing', $listing_id);
          $listing->profile_type = 0;
          $listing->save();

          //DELETE FIELD ENTRIES IF EXISTS
          $fieldvalueTable = Engine_Api::_()->fields()->getTable('list_listing', 'values');
          $fieldvalueTable->delete(array(
              'item_id = ?' => $listing_id,
          ));

          $fieldsearchTable = Engine_Api::_()->fields()->getTable('list_listing', 'search');
          $fieldsearchTable->delete(array(
              'item_id = ?' => $listing_id,
          ));
        }
      }

			//DELETE MAPPING
			Engine_Api::_()->getDbtable('profilemaps', 'list')->delete(array('profile_type = ?' => $option_id));
    }
    parent::typeDeleteAction();
  }
}