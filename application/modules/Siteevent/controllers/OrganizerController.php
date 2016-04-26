<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: TopicController.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_OrganizerController extends Seaocore_Controller_Action_Standard {

    //COMMON ACTION WHICH CALL BEFORE EVERY ACTION OF THIS CONTROLLER
    public function init() {

        //AUTHORIZATION CHECK
        if (!$this->_helper->requireAuth()->setAuthParams('siteevent_event', null, "view")->isValid())
            return;

        //RETURN IF SUBJECT IS ALREADY SET
        if (Engine_Api::_()->core()->hasSubject())
            return;

        //SET TOPIC OR EVENT SUBJECT
        if (0 != ($organizer_id = (int) $this->_getParam('organizer_id')) &&
                null != ($organizer = Engine_Api::_()->getItem('siteevent_organizer', $organizer_id))) {
            Engine_Api::_()->core()->setSubject($organizer);
        }
    }

    //ACTION TO BROWSE ALL TOPICS
    public function indexAction() {

        //RETURN IF EVENT SUBJECT IS NOT SET
        if (!$this->_helper->requireSubject('siteevent_organizer')->isValid())
            return;

        //GET EVENT SUBJECT
        $this->view->organizer = $siteevent = Engine_Api::_()->core()->getSubject();



        //GET PAGINATOR
        $this->view->paginator = Engine_Api::_()->getDbtable('organizers', 'siteevent');
    }

    public function viewAction() {
        //RETURN IF EVENT SUBJECT IS NOT SET
        if (!$this->_helper->requireSubject('siteevent_organizer')->isValid())
            return;
        //GET EVENT SUBJECT
        $this->view->organizer = $organizer = Engine_Api::_()->core()->getSubject();
        if (empty($organizer)) {
            return $this->_forwardCustom('notfound', 'error', 'core');
        }
        //ADD CSS
        $this->view->headLink()
                ->prependStylesheet($this->view->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteevent.css');
        $this->view->headLink()
                ->prependStylesheet($this->view->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteeventprofile.css');
        $this->_helper->content
                ->setNoRender()
                ->setEnabled();
    }

    public function editAction() {
        //ONLY LOGGED IN USER CAN CREATE TOPIC
        if (!$this->_helper->requireUser()->isValid())
            return;

        if (!$this->_helper->requireSubject('siteevent_organizer')->isValid())
            return;

        $this->view->organizer = $organizer = Engine_Api::_()->core()->getSubject();
        //GET VIEWER
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();


        //MAKE FORM
        $this->view->form = $form = new Siteevent_Form_Organizer_Edit(array('item' => $organizer, 'isEdit' => true));

        //CHECK METHOD/DATA
        if (!$this->getRequest()->isPost()) {
            $organizerValue = $organizer->toArray();
            $organizerValue['host_title'] = $organizer->title;
            $organizerValue['host_description'] = $organizer->description;
            $form->populate($organizerValue);
            return;
        }

        //FORM VALIDATION
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        //PROCESS
        $values = $form->getValues();
        $table = Engine_Api::_()->getItemTable('siteevent_organizer');
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {

            $hostInfo = array(
                'title' => $values['host_title'],
                'description' => isset($values['host_description']) ? $values['host_description'] : '',
                'facebook_url' => isset($_POST['host_facebook']) && $_POST['host_facebook'] ? $_POST['host_facebook'] : null,
                'twitter_url' => isset($_POST['host_twitter']) && $_POST['host_twitter'] ? $_POST['host_twitter'] : null,
                'web_url' => isset($_POST['host_website']) && $_POST['host_website'] ? $_POST['host_website'] : null,
            );
            $organizer->setFromArray($hostInfo);
            if (isset($form->host_photo) && $form->host_photo)
                $organizer->setPhoto($form->host_photo);
            $organizer->save();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
        return $this->_forward('success', 'utility', 'core', array(
                    'smoothboxClose' => true,
                    'parentRefresh' => $this->_getParam('parentRefresh', true),
                    'messages' => array(Zend_Registry::get('Zend_Translate')->_('Changes has been made successfully !')),
        ));
    }

    public function deleteAction() {
        //ONLY LOGGED IN USER CAN CREATE TOPIC
        if (!$this->_helper->requireUser()->isValid())
            return;

        if (!$this->_helper->requireSubject('siteevent_organizer')->isValid())
            return;

        $this->view->organizer = $organizer = Engine_Api::_()->core()->getSubject();

        //GET VIEWER
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $table = Engine_Api::_()->getItemTable('siteevent_organizer');
        $user = $organizer->getOwner();
        //FORM VALIDATION
        if (!$this->getRequest()->getPost()) {

            $eventMoveOptions = array($user->getGuid() => $user->getTitle());
            if ($organizer->countOrganizedEvent()) {
                $organizersListByCreator = $table->getPaginator(array('creator_id' => $organizer->creator_id, 'orderby' => 'title ASC'));

                foreach ($organizersListByCreator as $org) {
                    if ($org->getGuid() != $organizer->getGuid())
                        $eventMoveOptions[$org->getGuid()] = $org->getTitle();
                }
            }
            $this->view->eventMoveOptions = $eventMoveOptions;
            return;
        }

        //DELETE SITEEVENT AFTER CONFIRMATION
        if ($this->getRequest()->isPost() && $this->getRequest()->getPost('confirm') == true) {
            $moveinto = $this->getRequest()->getPost('moveInto');
            $move_host = array();
            if ($moveinto === $user->getGuid()) {
                $move_host['move_host_type'] = $user->getType();
                $move_host['move_host_id'] = $organizer->creator_id;
            } else {
                $move_host['move_host_type'] = $organizer->getType();
                $move_host['move_host_id'] = str_replace($organizer->getType() . "_", "", $moveinto);
            }
            $organizer->delete($move_host);
            return $this->_forward('success', 'utility', 'core', array(
                        'smoothboxClose' => true,
                        'parentRedirect' => $this->view->url(array('action' => 'manage'), 'siteevent_general', true),
                        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Remove successfully !')),
            ));
            // return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'siteevent_general', true);
        }
    }

}