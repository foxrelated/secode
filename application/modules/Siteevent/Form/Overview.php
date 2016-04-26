<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Overview.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Form_Overview extends Engine_Form {

    public $_error = array();

    public function init() {

        $event_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('event_id', null);
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        //GET TINYMCE SETTINGS
        $albumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('album');
        $upload_url = "";
        if (Engine_Api::_()->authorization()->isAllowed('album', $viewer, 'create') && $albumEnabled) {
            $upload_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'upload-photo'), 'siteevent_general', true);
        }

        $this->setTitle("Edit Event Overview")
                ->setDescription("Edit the overview for your event using the editor below, and then click 'Save Overview' to save changes.")
                ->setAttrib('name', 'siteevents_overview');

        $this->addElement('TinyMce', 'overview', array(
            'label' => '',
            'allowEmpty' => false,
            'attribs' => array('rows' => 180, 'cols' => 350, 'style' => 'width:740px; max-width:740px;height:858px;'),
            'editorOptions' => Engine_Api::_()->seaocore()->tinymceEditorOptions($upload_url),
            'filters' => array(new Engine_Filter_Censor()),
        ));

        $this->addElement('Button', 'save', array(
            'label' => 'Save Overview',
            'type' => 'submit',
        ));
    }

}