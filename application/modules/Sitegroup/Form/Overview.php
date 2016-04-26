<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Overview.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Form_Overview extends Engine_Form {

  public $_error = array();

  public function init() {
    $this->setTitle('Edit Group Overview')
            ->setDescription('Overview enables you to create a rich profile for your Group using the editor below. Compose the overview and click "Save Overview" to save it.')
            ->setAttrib('name', 'sitegroups_overview')->setAttrib('id', 'sitegroups_overview');
    $upload_url = "";
    $group_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('group_id', null);
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'spcreate');
    if (!empty($isManageAdmin)) {
      $upload_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => "upload-photo", 'group_id' => $group_id), 'sitegroup_dashboard', true);
    }
    // Overview
    $this->addElement('TinyMce', 'body', array(
        'label' => '',
//             'required' => true,
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

?>
