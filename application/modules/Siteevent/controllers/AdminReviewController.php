<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminReviewController.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_AdminReviewController extends Core_Controller_Action_Admin {

    public function indexAction() {

        //GET NAVIGATION
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('siteevent_admin_main', array(), 'siteevent_admin_main_review');

        $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('siteevent_admin_reviewmain', array(), 'siteevent_admin_reviewmain_general');

        // Make form
        $this->view->form = $form = new Siteevent_Form_Admin_Review_Global();

        // Check method/data
        if (!$this->getRequest()->isPost()) {
            return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $form->getValues();
        include APPLICATION_PATH . '/application/modules/Siteevent/controllers/license/license2.php';

        $form->addNotice('Your changes have been saved successfully.');
    }

    //ACTION FOR MANAGING REVIEWS
    public function manageAction() {

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('siteevent_admin_main', array(), 'siteevent_admin_main_review');

        $this->view->subNavigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('siteevent_admin_reviewmain', array(), 'siteevent_admin_reviewmain_manage');

        //HIDDEN SEARCH FORM CONTAIN ORDER AND ORDER DIRECTION  
        $this->view->formFilter = $formFilter = new Siteevent_Form_Admin_Manage_Filter();

        $tableUser = Engine_Api::_()->getItemTable('user')->info('name');

        $tableEvent = Engine_Api::_()->getItemTable('siteevent_event')->info('name');

        $tableReviewRating = Engine_Api::_()->getDbtable('ratings', 'siteevent');
        $tableReviewRatingName = $tableReviewRating->info('name');

        $table = Engine_Api::_()->getDbtable('reviews', 'siteevent');
        $rName = $table->info('name');
        $select = $table->select()
                ->setIntegrityCheck(false)
                ->from($rName)
                ->joinLeft($tableUser, "$rName.owner_id = $tableUser.user_id", array('username', 'email'))
                ->joinLeft($tableEvent, "$rName.resource_id = $tableEvent.event_id", array('title AS event_title', 'rating_users', 'rating_editor'))
                ->joinLeft($tableReviewRatingName, "$rName.review_id = $tableReviewRatingName.review_id", array('rating As review_rating', 'ratingparam_id'))
                ->where($tableReviewRatingName . '.ratingparam_id = ?', 0);

        $values = array();
        if ($formFilter->isValid($this->_getAllParams())) {
            $values = $formFilter->getValues();
        }

        foreach ($values as $key => $value) {
            if (null === $value) {
                unset($values[$key]);
            }
        }

        //REVIEW SEARCH WORK
        if (isset($_POST['search'])) {
            if (!empty($_POST['review_title'])) {
                $this->view->review_title = $_POST['review_title'];
                $select->where($rName . '.title  LIKE ?', '%' . $_POST['review_title'] . '%');
            }
            if (!empty($_POST['event_title'])) {
                $this->view->event_title = $_POST['event_title'];
                $select->where($tableEvent . '.title  LIKE ?', '%' . $_POST['event_title'] . '%');
            }

            if (!empty($_POST['name'])) {
                $this->view->name = $_POST['name'];
                $select->where($tableUser . '.username  LIKE ?', '%' . $_POST['name'] . '%');
            }
            if (!empty($_POST['email'])) {
                $this->view->name = $_POST['email'];
                $select->where($tableUser . '.email  LIKE ?', '%' . $_POST['email'] . '%');
            }
            if (!empty($_POST['review_type'])) {
                $this->view->review_type = $_POST['review_type'];
                $select->where($rName . '.type  LIKE ?', '%' . $_POST['review_type'] . '%');
            }
            if (isset($_POST['review_status'])) {
                $this->view->review_status = $review_status = $_POST['review_status'];
                if ($review_status == 3) {
                    $select->where($rName . '.status =?', 0);
                    $this->view->review_status = 3;
                } else if ($review_status == 1) {
                    $select->where($rName . '.status =?', 1);
                } else if ($review_status == 2) {
                    $select->where($rName . '.status =?', 2);
                }
            }
        } else {
            $this->view->review_title = '';
            $this->view->event_title = '';
            $this->view->name = '';
            $this->view->email = '';
            $this->view->review_status = '';
            $this->view->review_type = '';
        }

        $values = array_merge(array(
            'order' => 'review_id',
            'order_direction' => 'DESC',
                ), $values);

        $this->view->assign($values);
        $select->order((!empty($values['order']) ? $values['order'] : 'review_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));
        include APPLICATION_PATH . '/application/modules/Siteevent/controllers/license/license2.php';
    }

    //ACTION FOR DELETING A REVIEW
    public function deleteAction() {

        $review_id = $this->_getParam('review_id');
        $this->view->review = $review = Engine_Api::_()->getItem('siteevent_review', (int) $review_id);

        if ($this->getRequest()->isPost()) {

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {

                $review->delete();

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh' => true,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Review has been deleted successfully.'))
            ));
        } else {
            $this->renderScript('admin-review/delete.tpl');
        }
    }

    //ACTION FOR MULTI DELETE REVIEWS
    public function multiDeleteAction() {

        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();
            foreach ($values as $key => $value) {
                if ($key == 'delete_' . $value) {
                    Engine_Api::_()->getItem('siteevent_review', (int) $value)->delete();
                }
            }
        }
        return $this->_helper->redirector->gotoRoute(array('controller' => 'review', 'action' => 'manage'));
    }

    //ACTION FOR MAKE REVIEW FEATURED
    public function featuredAction() {

        //GET REVIEW ITEM
        $review = Engine_Api::_()->getItem('siteevent_review', $this->_getParam('review_id'));
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {

            if ($review->featured == 0) {
                $review->featured = 1;
            } else {
                $review->featured = 0;
            }
            $review->save();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        $this->_redirect('admin/siteevent/review/manage');
    }

}