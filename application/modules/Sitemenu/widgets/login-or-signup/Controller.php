<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.tpl 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitemenu_Widget_LoginOrSignupController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Do not show if logged in
    if( Engine_Api::_()->user()->getViewer()->getIdentity() ) {
      $this->setNoRender();
      return;
    }
    
    // DON'T SHOW WIDGET, IF PLUGIN NOT ACTIVATED.
    $isPluginActivate = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitemenu.isActivate', false);
    if(empty($isPluginActivate))
      return $this->setNoRender();
    
    // FETCH THE USER LOGIN/SIGNUP FORM
    $form = $this->view->form = new User_Form_Login();
    
    // REMOVING THE DEFAULT CREATED GROUP BECAUSE "buttons-wrapper" WERE CONFLICT WITH OUT COUPON PLUGIN IN SIGNUP PROCESS.
    $form->removeDisplayGroup("buttons");
    $form->addDisplayGroup(array(
      'submit',
      'remember'
    ), 'loginsignupbuttons');
    
    $form->setTitle(null)->setDescription(null);
    $form->return_url->setValue('64-' . base64_encode($_SERVER['REQUEST_URI']));
    $form->removeElement('forgot');

    // Facebook login
    if( 'none' == Engine_Api::_()->getApi('settings', 'core')->core_facebook_enable ) {
      $form->removeElement('facebook');
    }
    
    $sitemenu_login_and_signup = Zend_Registry::isRegistered('sitemenu_login_and_signup') ? Zend_Registry::get('sitemenu_login_and_signup') : null;
    
    // Check for recaptcha - it's too fat
    $this->view->noForm = false;
    if( ($captcha = $form->getElement('captcha')) instanceof Zend_Form_Element_Captcha && 
        $captcha->getCaptcha() instanceof Zend_Captcha_ReCaptcha ) {
      $this->view->noForm = true;
//      $form->removeElement('email');
//      $form->removeElement('password');
//      $form->removeElement('captcha');
//      $form->removeElement('submit');
//      $form->removeElement('remember');
//      $form->removeDisplayGroup('buttons');
    }
    
    if(empty($sitemenu_login_and_signup))
      return $this->setNoRender();
  }
  
  public function getCacheKey()
  {
    return false;
  }
}
