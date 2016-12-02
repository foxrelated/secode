<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Controller.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Widget_DiscussionContentController extends Seaocore_Content_Widget_Abstract {

  protected $_childCount;
  
  //ACTION FOR FETCHING THE DISCUSSIONS FOR THE GROUP
  public function indexAction() { 	
  	
    //DON'T RENDER IF THERE IS NO SUBJECT
    if (!Engine_Api::_()->core()->hasSubject('sitegroup_topic')) {
      return $this->setNoRender();
    }

    //SEND THE VIEWER TO THE TPL   
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    //SEND THE TOPIC SUBJECT TO THE TPL
    $this->view->topic = $topic = Engine_Api::_()->core()->getSubject();

    //GET THE SITEGROUP ITEM 
    $this->view->sitegroup = $sitegroup = $topic->getParentSitegroup();

    //SEND THE TAB ID TO THE TPL
    $this->view->tab_selected_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab');

    //START MANAGE-ADMIN CHECK
    $isManageAdmin = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'view');
    if (empty($isManageAdmin)) {
      return $this->setNoRender();
    }
    //END MANAGE-ADMIN CHECK
    //GET COMMENT PRIVACY
    $this->view->canPost = $canPost = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'sdicreate');

    //GET EDIT PRIVACY
    $this->view->canEdit = Engine_Api::_()->sitegroup()->isManageAdmin($sitegroup, 'edit');

    //INCREMENT IN VIEWS
    if (!$viewer || !$viewer->getIdentity() || $viewer->getIdentity() != $topic->user_id) {
      $topic->view_count = new Zend_Db_Expr('view_count + 1');
      $topic->save();
    }

    //CHECK WAITING
    $isWatching = null;
    if ($viewer->getIdentity()) {
      $topicWatchesTable = Engine_Api::_()->getDbtable('topicwatches', 'sitegroup');
      $isWatching = $topicWatchesTable->isWatching($sitegroup->getIdentity(), $topic->getIdentity(), $viewer->getIdentity());
      if (false === $isWatching) {
        $isWatching = null;
      } else {
        $isWatching = (bool) $isWatching;
      }
    }
    $this->view->isWatching = $isWatching;

    //@TODO IMPLEMENT SCAN TO POST
    $this->view->post_id = $post_id = (int) $this->_getParam('post');
    $order = $this->_getParam('postorder', 0);
    //MAKE PAGINATOR
    $this->view->paginator = $paginator = Engine_Api::_()->getDbtable('posts', 'sitegroup')->getPost($sitegroup->getIdentity(), $topic->getIdentity(), $order);
    $paginator->setItemCountPerPage(10);
    $page = Zend_Controller_Front::getInstance()->getRequest()->getParam('page');
    $paginator->setCurrentPageNumber($page);    
    
    // set up variables for pages
    $this->view->page_param = $page;
    $this->view->total_page = ceil(($paginator->getTotalItemCount() + 1) / 10);
//     //SKIP TO GROUP OF SPECIFIED POST
//     if (0 !== ($post_id = (int) $this->_getParam('post_id')) &&
//             null !== ($post = Engine_Api::_()->getItem('sitegroup_post', $post_id))) {
//       $icpp = $paginator->getItemCountPerPage();
//       $group = ceil(($post->getPostIndex() + 1) / $icpp);
//       $paginator->setCurrentPageNumber($group);
//     }
// 
//     //USE SPECIFIED GROUP
//     else if (0 !== ($group = (int) $this->_getParam('group'))) {
//       $paginator->setCurrentPageNumber($this->_getParam('group'));
//     }

    if ($canPost && !$topic->closed) {
      $this->view->form = $form = new Sitegroup_Form_Post_Create();
      $form->setAction($topic->getHref(array('action' => 'post', 'page' => $this->view->total_page)));
      $form->populate(array(
          'topic_id' => $topic->getIdentity(),
          'ref' => $topic->getHref(),
          'watch' => ( false === $isWatching ? '0' : '1' ),
      ));
    }
  }

}