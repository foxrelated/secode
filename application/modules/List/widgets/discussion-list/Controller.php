<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: Controller.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_Widget_DiscussionListController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;
  
  public function indexAction()
  {
    //DONT RENDER IF SUBJECT IS NOT SET
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

		//GET VIEWER
		$viewer = Engine_Api::_()->user()->getViewer();

    //GET SUBJECT
    $subject = Engine_Api::_()->core()->getSubject('list_listing');

		//AUTHORIZATION CHECK
    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }

		//WHO CAN POST THE DISCUSSION
    $this->view->canPost = Engine_Api::_()->authorization()->isAllowed($subject, $viewer, 'comment');

    //GET PAGINATOR
    $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('list_topic')->getListingTopices($subject->getIdentity());

    //DONT RENDER IF NOTHING TO SHOW
    if( $paginator->getTotalItemCount() <= 0 && (!$viewer->getIdentity() || empty($this->view->canPost)) ) {
      return $this->setNoRender();
    }

    //ADD COUNT TO TITLE
    if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
      $this->_childCount = $paginator->getTotalItemCount();
    }
  }

  public function getChildCount()
  {
    return $this->_childCount;
  }
}