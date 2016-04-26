<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Upload.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Form_Photo_Upload extends Engine_Form {

    public function init() {

        $siteevent = Engine_Api::_()->getItem('siteevent_event', Zend_Controller_Front::getInstance()->getRequest()->getParam('event_id', null));

        $this
                ->setTitle('Add New Photos')
                ->setDescription("Choose photos on your computer to add to this event. (2MB maximum).")
                ->setAttrib('id', 'form-upload')
                ->setAttrib('class', 'global_form siteevent_form_upload')
                ->setAttrib('name', 'albums_create')
                ->setAttrib('enctype', 'multipart/form-data')
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

        if(Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')) {
        $fancyUpload = new Engine_Form_Element_FancyUpload('file');
        $fancyUpload->clearDecorators()
                ->addDecorator('FormFancyUpload')
                ->addDecorator('viewScript', array(
                    'viewScript' => '_FancyUpload.tpl',
                    'placement' => '',
        ));
        Engine_Form::addDefaultDecorators($fancyUpload);
        $this->addElement($fancyUpload);
        $this->addElement('Hidden', 'fancyuploadfileids');
        }else{
          $this->addElement('FancyUpload', 'file');
        }

        $this->addElement('Button', 'submit', array(
            'label' => 'Save Photos',
            'type' => 'submit',
        ));
    }

}