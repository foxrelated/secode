<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: ChangePhoto.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Form_ChangePhoto extends Engine_Form {

    public function init() {

        $event_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('event_id', null);
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

        $this->setTitle("Edit Profile Picture")
                ->setAttrib('enctype', 'multipart/form-data')
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
                ->setAttrib('name', 'EditPhoto');

        $this->addElement('Image', 'current', array(
            'label' => 'Current Photo',
            'ignore' => true,
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '_formEditImage.tpl',
                        'class' => 'form element',
                        'testing' => 'testing'
                    )))
        ));
        Engine_Form::addDefaultDecorators($this->current);

        $this->addElement('File', 'Filedata', array(
            'label' => 'Choose New Photo',
            'destination' => APPLICATION_PATH . '/public/temporary/',
            'validators' => array(
                array('Extension', false, 'jpg,jpeg,png,gif'),
            ),
            'onchange' => 'javascript:uploadPhoto();'
        ));
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $this->addElement('Dummy', 'choose', array(
            'label' => Zend_Registry::get('Zend_Translate')->_('or'),
            'description' => sprintf(Zend_Registry::get('Zend_Translate')->_('%1$sChoose From Existing Pictures%2$s'), "<a href='" . $view->url(array('event_id' => $event_id, "change_url" => 1), "siteevent_albumspecific", true) . "'>", "</a>")
        ));

        $this->getElement('choose')->getDecorator('Description')->setOptions(array('placement', 'APPEND', 'escape' => false));

        $this->addElement('Hidden', 'coordinates', array(
            'filters' => array(
                'HtmlEntities',
            )
        ));

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $url = $view->url(array('action' => 'remove-photo', 'event_id' => $event_id), "siteevent_specific", true);
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);
        if ($siteevent->photo_id != 0) {

            $this->addElement('Button', 'remove', array(
                'label' => 'Remove Photo',
                'onclick' => "removePhotoEvent('$url');",
                'decorators' => array(
                    'ViewHelper',
                ),
            ));

            $url = $view->url(array('event_id' => $event_id, 'slug' => $siteevent->getSlug()), "siteevent_entry_view", true);

            $this->addElement('Cancel', 'cancel', array(
                'label' => 'cancel',
                'prependText' => ' '.Zend_Registry::get('Zend_Translate')->_('or').' ',
                'link' => true,
                'onclick' => "removePhotoEvent('$url');",
                'decorators' => array(
                    'ViewHelper',
                ),
            ));

            $this->addDisplayGroup(array('remove', 'cancel'), 'buttons', array());
        }
    }

}