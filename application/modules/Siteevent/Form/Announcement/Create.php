<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Create.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Form_Announcement_Create extends Engine_Form {

    public function init() {

        $this->setTitle('Post New Announcement')
                ->setDescription('Please compose a new announcement for your event below.');

        // Add title
        $this->addElement('Text', 'title', array(
            'label' => 'Title',
            'required' => true,
            'allowEmpty' => false,
        ));

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.announcementeditor', 1)) {
            $viewer = Engine_Api::_()->user()->getViewer();
            //GET TINYMCE SETTINGS
            $albumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('album');
            $upload_url = "";
            if (Engine_Api::_()->authorization()->isAllowed('album', $viewer, 'create') && $albumEnabled) {
                $upload_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'upload-photo'), 'siteevent_general', true);
            }
            $this->addElement('TinyMce', 'body', array(
                'label' => 'Body',
                'required' => true,
                'allowEmpty' => false,
                'attribs' => array('rows' => 180, 'cols' => 350, 'style' => 'width:740px; max-width:740px;height:858px;'),
                'editorOptions' => Engine_Api::_()->seaocore()->tinymceEditorOptions($upload_url),
                'filters' => array(
                    new Engine_Filter_Censor(),
                    new Engine_Filter_Html(array('AllowedTags' => "strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr, table, tr, td, iframe"))),
            ));
        } else {
            $this->addElement('Textarea', 'body', array(
                'label' => 'Body',
                'allowEmpty' => false,
                'required' => true,
                'filters' => array(
                    new Engine_Filter_Censor(),
                    new Engine_Filter_HtmlSpecialChars(),
                    new Engine_Filter_EnableLinks(),
                ),
            ));
        }
        $date = (string) date('Y-m-d');
        $this->addElement('CalendarDateTime', 'startdate', array(
            'label' => 'Start Date',
            'description' => "Select a start date for this announcement.",
            'value' => $date . ' 00:00:00',
        ));

        $this->addElement('CalendarDateTime', 'expirydate', array(
            'label' => 'Expiry Date',
            'description' => 'Select an expiry date for this announcement.',
            'value' => $date . ' 00:00:00',
        ));

        $this->addElement('Checkbox', 'status', array(
            'description' => 'Activate Announcement',
            'label' => 'Yes, activate this announcement for my event.',
            'value' => 1,
        ));

        $this->addElement('Button', 'submit', array(
            'label' => 'Post Announcement',
            'ignore' => true,
            'decorators' => array('ViewHelper'),
            'type' => 'submit'
        ));

        $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))->setMethod('POST');
    }

}