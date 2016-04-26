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
class List_Widget_ReviewListController extends Engine_Content_Widget_Abstract {

  protected $_childCount;

  public function indexAction() {

    // Don't render this if not authorized
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    if (!Engine_Api::_()->core()->hasSubject()) {
      return $this->setNoRender();
    }

    $viewer_id = $viewer->getIdentity();
    if (!empty($viewer_id)) {
      $this->view->level_id = $viewer->level_id;
    } else {
      $this->view->level_id = 0;
    }

    // Get subject and check auth
    $list = Engine_Api::_()->core()->getSubject('list_listing');
    $this->view->list = $list;
    $delete_id = $this->_getParam('delete_id');
    if (!empty($delete_id)) {
      $this->delete($delete_id);
    }

    $table = Engine_Api::_()->getDbTable('reviews', 'list');
    $select = $table->select()
    ->where('listing_id = ?', $list->getIdentity())
    ->order('modified_date DESC');

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(10);
    $this->view->paginator = $paginator->setCurrentPageNumber($this->_getParam('page'));

    // Do not render if nothing to show and not viewer
    if ($paginator->getTotalItemCount() <= 0 && !$viewer->getIdentity()) {
      return $this->setNoRender();
    }

    // Add count to title if configured
    if ($this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0) {
      $this->_childCount = $paginator->getTotalItemCount();
    }
  }

  public function getChildCount() {
    return $this->_childCount;
  }

  public function delete($id) {
    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();
    try {
      $review = Engine_Api::_()->getItem('list_reviews', $id);
      //DELETE REVIEW FROM DATABASE
      $review->delete();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }
  }

}