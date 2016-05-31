<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteiosapp
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Map.php 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteiosapp_Form_Admin_ViewMapsListingType_Map extends Engine_Form {

    protected $_listingTypeId;

    public function getListingTypeId() {
        return $this->_listingTypeId;
    }

    public function setListingTypeId($listingTypeId) {
        $this->_listingTypeId = $listingTypeId;
        return $this;
    }

    public function init() {
        $this->setMethod('post')
                ->setTitle("Select View Type")
                ->setAttrib('class', 'global_form_box')
                ->setDescription("After selecting a view type, if you click on 'Save', then the already created listings of this listing type will also be associated with this view type.");
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $select = new Zend_Db_Select($db);
        $select->from('engine4_siteiosapp_listingtypeViewMaps')
                ->where('listingtype_id = ?', $this->_listingTypeId);
        $isViewRowExist = $select->query()->fetchObject();

        $profileViewValue = $this->_getViewTypeLabel($isViewRowExist->profileView_id, 1);
        $browseViewValue = $this->_getViewTypeLabel($isViewRowExist->browseView_id, 2);
        $browseOptions = array('1' => 'List View', '2' => 'Grid View', '3' => 'Matrix View');
        if (count($browseOptions) > 0) {
            $this->addElement('Select', 'browse_view_type', array(
                'label' => 'Browse View Type',
                'allowEmpty' => false,
                'required' => true,
                'multiOptions' => $browseOptions,
                'value' => $browseViewValue
            ));
        } else if (count($browseOptions) == 1) {
            $this->addElement('Hidden', 'browse_view_type', array(
                'value' => $browseOptions[0]->option_id
            ));
        }
        $viewOptions = array('1' => 'Blog View', '2' => 'Classified 1 View', '3' => 'Classified 2 View');
        if (count($viewOptions) > 0) {
            $this->addElement('Select', 'profile_view_type', array(
                'label' => 'Profile View Type',
                'allowEmpty' => false,
                'required' => true,
                'multiOptions' => $viewOptions,
                'value' => $profileViewValue
            ));
        } else if (count($viewOptions) == 1) {
            $this->addElement('Hidden', 'profile_view_type', array(
                'value' => $viewOptions[0]->option_id
            ));
        }
        $this->addElement('Button', 'yes_button', array(
            'label' => 'Save',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper')
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'href' => '',
            'onClick' => 'javascript:parent.Smoothbox.close();',
            'decorators' => array(
                'ViewHelper'
            )
        ));
        $this->addDisplayGroup(array('yes_button', 'cancel'), 'buttons');
        $button_group = $this->getDisplayGroup('buttons');
    }

    //return view name for rescpective ID 
    private function _getViewTypeLabel($viewId, $viewType) {
        //for profile Type View
        if (isset($viewType) && $viewType == 1) {

            $viewId = (!isset($viewId)) ? 3 : $viewId;
        } else if (isset($viewType) && $viewType == 2) {
            $viewId = (!isset($viewId)) ? 2 : $viewId;
        }
        return $viewId;
    }

}
