<?php
/**
 * @author     George Coca
 * @website    geodeveloper.net <info@geodeveloper.net>   
 */
class Shoutbox_Widget_ShoutboxController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
        // Get settings
        $settings = Engine_Api::_()->getApi('settings', 'core');

        $headScript = new Zend_View_Helper_HeadScript();
        $headScript->appendFile(Zend_Registry::get('StaticBaseUrl') 
        . 'application/modules/Shoutbox/externals/scripts/core.js');

        // Get viewer
        $viewer = Engine_Api::_()->user()->getViewer();
        
        // check auth
        if( !Engine_Api::_()->authorization()->isAllowed('shoutbox', $viewer, 'view') ) {
          return $this->setNoRender();
        }
        
        $this->view->allowCreate = $allowCreate = Engine_Api::_()->authorization()->isAllowed('shoutbox', $viewer, 'create');

        // Set identity
        if(Engine_Api::_()->core()->hasSubject())
        {
            $subject = Engine_Api::_()->core()->getSubject();
            $this->view->identity = $identity = $subject->getType() . "_" .$subject->getIdentity();
        } else {
            $request = Zend_Controller_Front::getInstance()->getRequest();
            $this->view->identity = $identity = $request->getModuleName() . '_' . $request->getControllerName() . '_' . $request->getActionName();
        }

        // Get shouts
        $shoutboxTable = Engine_Api::_()->getDbtable('shouts', 'shoutbox');
        $this->view->shouts = $shouts = $shoutboxTable->getShouts($identity);
        $this->view->totalShouts = $shouts->getTotalItemCount();

        // Set config for shouts
        $shouts->setItemCountPerPage(10);
        $shouts->setItemCountPerPage($settings->getSetting('shoutbox.shouts', 10));
        $this->view->timer = $settings->getSetting('shoutbox.timer', 5000);
        $this->view->autorefresh = $settings->getSetting('shoutbox.autorefresh', 1);
  }
}