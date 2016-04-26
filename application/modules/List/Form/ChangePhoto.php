<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: ChangePhoto.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
class List_Form_ChangePhoto extends Engine_Form {

  public function init() {

    $listing_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('listing_id', null);
    $list = Engine_Api::_()->getItem('list_listing', $listing_id);

    $this->setTitle("Edit Listing's Profile Picture")
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

    $this->addElement('Hidden', 'coordinates', array(
        'filters' => array(
            'HtmlEntities',
        )
    ));

    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
    $url = $view->url(array('action' => 'remove-photo', 'listing_id' => $listing_id), 'list_specific', true);
    $list = Engine_Api::_()->getItem('list_listing', $listing_id);
    if ($list->photo_id != 0) {

      $this->addElement('Button', 'remove', array(
				'label' => 'Remove Photo',
				'onclick' => "removePhotoListing('$url');",
				'decorators' => array(
					'ViewHelper',
				),
      ));

      $url = $view->url(array('listing_id' => $listing_id,'user_id' => $list->owner_id, 'slug' => $list->getSlug()), 'list_entry_view', true);
      
      $this->addElement('Cancel', 'cancel', array(
				'label' => 'cancel',
				'prependText' => ' or ',
				'link' => true,
				'onclick' => "removePhotoListing('$url');",
				'decorators' => array(
					'ViewHelper',
				),
      ));

      $this->addDisplayGroup(array('remove', 'cancel'), 'buttons', array());
    }
  }

}