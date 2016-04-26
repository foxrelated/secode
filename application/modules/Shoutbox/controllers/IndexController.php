<?php
/**
 * @author     George Coca
 * @website    geodeveloper.net <info@geodeveloper.net>   
 */
class Shoutbox_IndexController extends Core_Controller_Action_Standard
{
    public function indexAction()
    {
        // Gett viewer
        $viewer = Engine_Api::_()->user()->getViewer();        

        // check auth
        if( !Engine_Api::_()->authorization()->isAllowed('shoutbox', $viewer, 'view') ) {
          return;
        }
        
        // Get settings
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $identity = $this->_getParam('identity', null);

        // Get shouts
        $shoutboxTable = Engine_Api::_()->getDbtable('shouts', 'shoutbox');
        $this->view->shouts = $shouts = $shoutboxTable->getShouts($identity);
        $this->view->identity = $identity;

        // Set config for shouts
        $shouts->setItemCountPerPage($settings->getSetting('shoutbox.shouts', 10));
        $shouts->setCurrentPageNumber($this->_getParam('page', 1));
    }

    public function createAction()
    {
        // Gett viewer
        $viewer = Engine_Api::_()->user()->getViewer();        

        // check auth
        if( !Engine_Api::_()->authorization()->isAllowed('shoutbox', $viewer, 'create') ) {
          return;
        }
        
        // Get paramas
        $body = $this->_getParam('msg', null);
        $identity = $this->_getParam('identity', null);
        $creation_date = date("Y-m-d H:i:s");
        
        // Submit shout
        $shoutboxTable = Engine_Api::_()->getDbtable('shouts', 'shoutbox');
        $this->view->shout = $shout = $shoutboxTable->addShout($viewer, $body, $creation_date, $identity);
    
    }
    
    public function getshoutsAction()
    {
        // Gett viewer
        $viewer = Engine_Api::_()->user()->getViewer();        

        // check auth
        if( !Engine_Api::_()->authorization()->isAllowed('shoutbox', $viewer, 'view') ) {
          return;
        }
        
        // Get shouts
        $identity = $this->_getParam('identity', null);
        $shoutboxTable = Engine_Api::_()->getDbtable('shouts', 'shoutbox');
        $this->view->shouts = $shouts = $shoutboxTable->getShouts($identity);
        $this->view->totalShouts = $shouts->getTotalItemCount();
    }
}
