<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Takeaction.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Form_Admin_Takeaction extends Engine_Form {

  public function init() {

    $group_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('group_id', null);
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $url = $view->url(array('group_url' => Engine_Api::_()->sitegroup()->getGroupUrl($group_id)), 'sitegroup_entry_view', true);
    $sitegroup = Engine_Api::_()->getItem('sitegroup_group', $group_id);
    $sitegroup_title = "<a href='$url' target='_blank'>$sitegroup->title</a>";

    $this->setMethod('post');
    $this->setTitle("Take an Action")
            ->setDescription("Please take an appropriate action for this group:" . $sitegroup_title);

    $this->addElement('Button', 'submit', array(
        'label' => 'Save',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array('ViewHelper')
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $button_group = $this->getDisplayGroup('buttons');
  }

}

?>