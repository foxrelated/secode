<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Overview.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_Form_Overview extends Engine_Form {

  public $_error = array();

  public function init() {

    $this->setTitle('Edit Listing Overview')
        ->setDescription('Edit the overview for your listing using the editor below, and then click "Save Overview" to save changes.')
        ->setAttrib('name', 'lists_overview');

    $listing_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('listing_id', null);

    $upload_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => "upload-photo", 'listing_id' => $listing_id), 'list_general', true);

    $this->addElement('TinyMce', 'overview', array(
            'label' => '',
            'allowEmpty' => false,
            'attribs' => array('rows' => 180, 'cols' => 350, 'style' => 'width:740px; max-width:740px;height:858px;'),
            'editorOptions' => Engine_Api::_()->seaocore()->tinymceEditorOptions($upload_url),
            'filters' => array(new Engine_Filter_Censor()),
    ));

    $this->addElement('Button', 'save', array(
            'label' => 'Save Overview',
            'type' => 'submit',
    ));
  }
}