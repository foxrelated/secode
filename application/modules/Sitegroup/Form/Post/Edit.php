<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Edit.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Form_Post_Edit extends Engine_Form {

  public function init() {

    $this
            ->setTitle('Edit Post');


    if (Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.tinymceditor', 1)) {
      $upload_url = "";
      $group_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('group_id', null);
      $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
      $sitegroupalbumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupalbum');
      $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'spcreate');
      if ($sitegroupalbumEnabled && (!empty($isManageAdmin) || Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit'))) {
        $upload_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => "upload-photo", 'group_id' => $group_id, 'special' => 'discussions'), 'sitegroup_dashboard', true);
      }
      /* Some time overview is not work so use body
       */
      // Overview
      $this->addElement('TinyMce', 'body', array(
          'label' => '',
          //             'required' => true,
          'allowEmpty' => false,
          'attribs' => array('rows' => 180, 'cols' => 350, 'style' => 'width:740px; max-width:740px;height:858px;'),

          'editorOptions' => Engine_Api::_()->seaocore()->tinymceEditorOptions($upload_url),
          'filters' => array(
              new Engine_Filter_Censor(),
              new Engine_Filter_Html(array('AllowedTags' => "strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr, iframe"))),
      ));
    } else {
      $this->addElement('textarea', 'body', array(
          'filters' => array(
              new Engine_Filter_Censor(),
          )
      ));
    }

    $this->addElement('Button', 'submit', array(
        'label' => 'Edit Post',
        'ignore' => true,
        'type' => 'submit',
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'prependText' => ' or ',
        'type' => 'link',
        'link' => true,
        'onclick' => 'parent.Smoothbox.close();',
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }

}

?>