<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Twitter.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Form_Facebook extends Engine_Form {

  public function init() { 
       $this->loadDefaultDecorators();
      $request = Zend_Controller_Front::getInstance()->getRequest();
      $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
      $fblikebox_id = $request->getParam('fblikebox_id', null);
      $advfeedmodule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('advancedactivity');
      if (!empty ($advfeedmodule) && !empty($advfeedmodule->enabled) && $advfeedmodule->version > '4.2.5')
      $description = "By linking your Group to your Facebook Group, updates from your Group will also be published on your Facebook Group.";
    
    $this->setTitle("Link your Group to Facebook")
            ->setDescription($description)
            ->setMethod('POST')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
     $this->getDecorator('Description')->setOption('escape', false);        
     
        $Api_facebook = Engine_Api::_()->getApi('facebook_Facebookinvite', 'seaocore');
        $facebook_userfeed = $Api_facebook->getFBInstance();
        
        
        $fb_checkconnection = $Api_facebook->checkConnection(null, $facebook_userfeed);
        $facebookPermittedPages = null;
        $isFbPageAvailable = false;
        if ($facebook_userfeed && $fb_checkconnection) {
            $facebookPermittedPages = $Api_facebook->getFBPermittedGroups($facebook_userfeed);
        }

        if (empty($facebookPermittedPages)) {
            $description = 'Please login to facebook to choose your fb group';
            $this->setDescription($description);
        } elseif (!empty($facebookPermittedPages) && (count($facebookPermittedPages) < 2)) {
            $description = 'You have not created any facebook group or you have denied user_managed_groups permissions for this app';
            $this->setDescription($description);
        } else {

            $isFbPageAvailable = true;
            
            $this->addElement('Select', 'fbgroup_id', array(
                'label' => 'Facebook Group ',
                'description' => "Select the facebook group you want to link to your group.",
                'style' => 'width:330px;',
                'multiOptions' => $facebookPermittedPages,
            ));

            $this->addElement('Button', 'submit', array(
                'label' => 'Save',
                'type' => 'submit',
                'ignore' => true,
                'decorators' => array(
                    'ViewHelper',
                ),
            ));
        }


        if (!empty($isFbPageAvailable)) {

            $this->addElement('Cancel', 'cancel', array(
                'label' => 'cancel',
                'link' => true,
                'prependText' => ' or ',
                'onclick' => 'javascript:parent.Smoothbox.close()',
                'decorators' => array(
                    'ViewHelper',
                ),
            ));
        } else {

            $this->addElement('Button', 'cancel', array(
                'label' => 'cancel',
                'link' => true,
                'onclick' => 'javascript:parent.Smoothbox.close()',
                'decorators' => array(
                    'ViewHelper',
                ),
            ));
        }
        
    $this->addDisplayGroup(array(
        'submit',
        'cancel',
            ), 'buttons', array(
        'decorators' => array(
            'FormElements',
            'DivDivDivWrapper'
        ),
    ));
  }

}
?>