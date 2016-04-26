<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminDiaryController.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_AdminDiaryController extends Core_Controller_Action_Admin {

    //ACTION FOR MANAGE PLAYLISTS
    public function manageAction() {

        //GET NAVIGATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
                ->getNavigation('siteevent_admin_main', array(), 'siteevent_admin_main_diary');

        //FORM GENERATION
        $this->view->formFilter = $formFilter = new Siteevent_Form_Admin_Filter();

        //GET CURRENT PAGE NUMBER
        $page = $this->_getParam('page', 1);

        //GET USER TABLE NAME
        $tableUserName = Engine_Api::_()->getItemTable('user')->info('name');

        //GET DIARY PAGE TABLE
        $diaryEventTable = Engine_Api::_()->getDbtable('diarymaps', 'siteevent');
        $diaryEventTableName = $diaryEventTable->info('name');

        //MAKE QUERY
        $tableDiary = Engine_Api::_()->getDbtable('diaries', 'siteevent');
        $tableDiaryName = $tableDiary->info('name');
        $select = $tableDiary->select()
                ->setIntegrityCheck(false)
                ->from($tableDiaryName)
                ->joinLeft($diaryEventTableName, "$diaryEventTableName.diary_id = $tableDiaryName.diary_id", array("COUNT($diaryEventTableName.diary_id) AS total_item"))
                ->joinLeft($tableUserName, "$tableDiaryName.owner_id = $tableUserName.user_id", 'username')
                ->group($tableDiaryName . '.diary_id');

        //GET VALUES
        if ($formFilter->isValid($this->_getAllParams())) {
            $values = $formFilter->getValues();
        }

        foreach ($values as $key => $value) {
            if (null === $value) {
                unset($values[$key]);
            }
        }

        $values = array_merge(array('order' => 'diary_id', 'order_direction' => 'DESC'), $values);

        if (!empty($_POST['user_name'])) {
            $user_name = $_POST['user_name'];
        } elseif (!empty($_GET['user_name']) && !isset($_POST['post_search'])) {
            $user_name = $_GET['user_name'];
        } else {
            $user_name = '';
        }

        if (!empty($_POST['diary_name'])) {
            $diary_name = $_POST['diary_name'];
        } elseif (!empty($_GET['diary_name']) && !isset($_POST['post_search'])) {
            $diary_name = $_GET['diary_name'];
        } else {
            $diary_name = '';
        }

        if (!empty($_POST['event_name'])) {
            $event_name = $_POST['event_name'];
        } elseif (!empty($_GET['event_name']) && !isset($_POST['post_search'])) {
            $event_name = $_GET['event_name'];
        } elseif ($this->_getParam('event_name', '') && !isset($_POST['post_search'])) {
            $event_name = $this->_getParam('event_name', '');
        } else {
            $event_name = '';
        }

        //SEARCHING
        $this->view->user_name = $values['user_name'] = $user_name;
        $this->view->diary_name = $values['diary_name'] = $diary_name;
        $this->view->event_name = $values['event_name'] = $event_name;

        if (!empty($user_name)) {
            $select->where($tableUserName . '.username  LIKE ?', '%' . $user_name . '%');
        }
        if (!empty($diary_name)) {
            $select->where($tableDiaryName . '.title  LIKE ?', '%' . $diary_name . '%');
        }
        if (!empty($event_name)) {
            $tablePageName = Engine_Api::_()->getDbTable('events', 'siteevent')->info('name');
            $select->joinLeft($tablePageName, "$diaryEventTableName.event_id = $tablePageName.event_id", array('title AS page_title'))
                    ->where($tablePageName . '.title  LIKE ?', '%' . $event_name . '%');
        }

        //ASSIGN VALUES TO THE TPL
        $this->view->formValues = array_filter($values);
        $this->view->assign($values);

        $select->order((!empty($values['order']) ? $values['order'] : 'diary_id' ) . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC' ));

        include APPLICATION_PATH . '/application/modules/Siteevent/controllers/license/license2.php';
    }

    //ACTION FOR DELETE THE DIARY
    public function deleteAction() {

        //SET LAYOUT
        $this->_helper->layout->setLayout('admin-simple');

        //GET DIARY ID
        $this->view->diary_id = $diary_id = $this->_getParam('diary_id');

        if ($this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();
            try {

                //DELETE DIARY CONTENT
                Engine_Api::_()->getItem('siteevent_diary', $diary_id)->delete();

                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('')
            ));
        }
        $this->renderScript('admin-diary/delete.tpl');
    }

    //ACTION FOR MULTI DELETE DIARY
    public function multiDeleteAction() {

        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();
            foreach ($values as $key => $value) {
                if ($key == 'delete_' . $value) {

                    //GET DIARY ID
                    $diary_id = (int) $value;

                    //DELETE DIARY CONTENT
                    Engine_Api::_()->getItem('siteevent_diary', $diary_id)->delete();
                }
            }
        }
        return $this->_helper->redirector->gotoRoute(array('action' => 'manage'));
    }

}