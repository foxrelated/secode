<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: PostController.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_PostController extends Core_Controller_Action_Standard {

	//COMMON ACTION WHICH CALL BEFORE EVERY ACTION OF THIS CONTROLLER
  public function init() {

		//RETURN IF SUBJECT IS SET
    if (Engine_Api::_()->core()->hasSubject())
      return;

		//SET POST OR TOPIC SUBJECT
    if (0 != ($post_id = (int) $this->_getParam('post_id')) &&
        null != ($post = Engine_Api::_()->getItem('list_post', $post_id))) {
      Engine_Api::_()->core()->setSubject($post);
    } else if (0 != ($topic_id = (int) $this->_getParam('topic_id')) &&
        null != ($topic = Engine_Api::_()->getItem('list_topic', $topic_id))) {
      Engine_Api::_()->core()->setSubject($topic);
    }

    $this->_helper->requireUser->addActionRequires(array(
            'edit',
            'delete',
    ));

    $this->_helper->requireSubject->setActionRequireTypes(array(
            'edit' => 'list_post',
            'delete' => 'list_post',
    ));
  }

	//ACTION FOR EDIT THE POST
  public function editAction() {

		//GET POST SUBJECT
    $post = Engine_Api::_()->core()->getSubject('list_post');

		//GET LISTING
    $list = $post->getParent('list_listing');

		//GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$list->isOwner($viewer) && !$post->isOwner($viewer)) {
      return $this->_helper->requireAuth->forward();
    }

		//MAKE FORM
    $this->view->form = $form = new List_Form_Post_Edit();

		//CHECK METHOD
    if (!$this->getRequest()->isPost()) {
      $form->populate($post->toArray());
      return;
    }

		//FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    //PROCESS
    $table = Engine_Api::_()->getDbTable('posts', 'list');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      $post->setFromArray($form->getValues());
      $post->modified_date = date('Y-m-d H:i:s');
      $post->save();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    return $this->_forward('success', 'utility', 'core', array(
            'closeSmoothbox' => true,
            'parentRefresh' => true,
    ));
  }

	//ACTION FOR DELETE THE POST
  public function deleteAction() {

		//GET POST SUBJECT
    $post = Engine_Api::_()->core()->getSubject('list_post');

		//GET LISTING SUBJECT
    $list = $post->getParent('list_listing');

		//GET VIEWER
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$list->isOwner($viewer) && !$post->isOwner($viewer)) {
      return $this->_helper->requireAuth->forward();
    }

		//MAKE FORM
    $this->view->form = $form = new List_Form_Post_Delete();

		//CHECK METHOD
    if (!$this->getRequest()->isPost()) {
      return;
    }

		//FORM VALIDATION
    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    //PROCESS
    $table = Engine_Api::_()->getDbTable('posts', 'list');
    $db = $table->getAdapter();
    $db->beginTransaction();
    $topic_id = $post->topic_id;
    try {
      $post->delete();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    //GET TOPIC
    $topic = Engine_Api::_()->getItem('list_topic', $topic_id);

    $href = ( null == $topic ? $list->getHref() : $topic->getHref() );
    return $this->_forward('success', 'utility', 'core', array(
            'closeSmoothbox' => true,
            'parentRedirect' => $href,
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('Post deleted.')),
    ));
  }

  public function canEdit($user) {
    return $this->getParent()->getParent()->authorization()->isAllowed($user, 'edit') || $this->isOwner($user);
  }

}