<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Groupdocument
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: IndexController.php 6590 2010-10-19 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Birthday_Controller_Action_Helper_Birthday extends Zend_Controller_Action_Helper_Abstract {

  function postDispatch() {
    //GET NAME OF MODULE, CONTROLLER AND ACTION
    $front = Zend_Controller_Front::getInstance();
    $module = $front->getRequest()->getModuleName();
    $controller = $front->getRequest()->getControllerName();
    $action = $front->getRequest()->getActionName();
    $view = $this->getActionController()->view;

    //ADD DOCUMENT PRIVACY FIELDS AT GROUP CREATION AND EDITION
    if ($module == 'user' && $action == 'privacy' && $controller == 'settings') {

      $form = $view->form;
      if ($form->publishTypes) {
        $multiOptions = $form->publishTypes->getMultiOptions();
        if (isset($multiOptions['birthday_post']))
          unset($multiOptions['birthday_post']);
        $form->publishTypes->setMultiOptions($multiOptions);
        if (!$front->getRequest()->isPost()) {
          return;
        }
        if ($form->isValid($front->getRequest()->getPost())) {
          if ($form->getElement('publishTypes')) {
            $publishTypes = $form->publishTypes->getValue();
            $publishTypes[] = 'signup';
            $publishTypes[] = 'post';
            $publishTypes[] = 'status';
            $publishTypes[] = 'birthday_post';
            $user = Engine_Api::_()->core()->getSubject();
            Engine_Api::_()->getDbtable('actionSettings', 'activity')->setEnabledActions($user, (array) $publishTypes);
          }
        }
      }
    }
  }

}

?>