<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedslideshow
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Simpleupload.php 2011-10-22 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Advancedslideshow_Form_Admin_Image_Simpleupload extends Engine_Form
{
  public function init()
  {
		$advancedslideshow_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('advancedslideshow_id', null);
		$advancedslideshow = Engine_Api::_()->getItem('advancedslideshow', $advancedslideshow_id);
		$height = $advancedslideshow->height;
		$width = $advancedslideshow->width;
		$type = $advancedslideshow->slideshow_type;
		$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;

		$this
		->setTitle('Upload Slides')
		->setDescription("Here, you can upload slides using basic uploader.")
		->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

		$href = 'http://'.$_SERVER['HTTP_HOST']. Zend_Controller_Front::getInstance()->getBaseUrl() .'/admin/advancedslideshow/slideshows/edit/slideshowtype/'.$type.'/advancedslideshow_id/'.$advancedslideshow_id;

		$changeLink = "<div class='tip' style='width:600px;'><span>You can change the slideshow picture width and height by configuring the slideshow width and height from the <a href=$href target='_parent'>".Zend_Registry::get('Zend_Translate')->_('Edit Slideshow') . '</a> ' . Zend_Registry::get('Zend_Translate')->_('section. The current width and height set over here are ').$width.'px and '.$height."px respectively. It is recommended that you upload pictures of these dimensions.</span></div>";

    $changeLink = sprintf($changeLink);

		$this->addElement('dummy', 'dummy_message', array(
			'description' => $changeLink
		));
		$this->dummy_message->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

		for($i = 1; $i<= 10; $i++) {
			$elementName = "photo_$i";
			$this->addElement('File', $elementName, array(
				'label' => "Slide $i"
			));
			$this->$elementName->addValidator('Extension', false, 'jpg,png,gif,jpeg');
		}

    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));
  }
}
?>