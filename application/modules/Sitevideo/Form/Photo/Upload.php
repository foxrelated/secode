<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Upload.php 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitevideo_Form_Photo_Upload extends Engine_Form {

    public function init() {

        $this
                ->setTitle('Add New Photos')
                ->setDescription("Choose photos on your computer to add to this channel. (2MB maximum).")
                ->setAttrib('id', 'form-upload')
                ->setAttrib('class', 'global_form sitevideo_form_upload')
                ->setAttrib('name', 'albums_create')
                ->setAttrib('enctype', 'multipart/form-data')
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

        $fancyUpload = new Engine_Form_Element_FancyUpload('file');
        $fancyUpload->clearDecorators()
                ->addDecorator('FormFancyUpload')
                ->addDecorator('viewScript', array(
                    'viewScript' => '_FancyUploadPhoto.tpl',
                    'placement' => '',
        ));
        Engine_Form::addDefaultDecorators($fancyUpload);
        $this->addElement($fancyUpload);
        $this->addElement('Hidden', 'fancyuploadfileids');


        $this->addElement('Button', 'submit', array(
            'label' => 'Save Photos',
            'type' => 'submit',
        ));
    }

}
