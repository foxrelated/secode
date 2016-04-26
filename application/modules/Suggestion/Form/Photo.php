<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Photo.php 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Suggestion_Form_Photo extends Engine_Form
{
  public function init()
  {
	    $this
	      ->setAttrib('enctype', 'multipart/form-data')
	      ->setAttrib('id', 'profileform');
	          
	    $this->addElement('Image', 'current', array(
	      'ignore' => true,
	    ));
	    Engine_Form::addDefaultDecorators($this->current);
	
	    $this->addElement('File', 'Filedata', array(
	      'label' => 'Choose New Photo',      
	      'destination' => APPLICATION_PATH.'/public/temporary/',
	      'multiFile' => 1,
	      'validators' => array(
	        array('Count', false, 2),
	        array('Size', false, 612000),
	        array('Extension', false, 'jpg,png,gif'),
	      ),
	      'onchange'=>'javascript:uploadProfilePhoto();'
	    ));
	    
	    // Use for uploading image.
	    $this->addElement('Hidden', 'uploadPhoto', array(
	      'order' => 2
	    ));
  }
}