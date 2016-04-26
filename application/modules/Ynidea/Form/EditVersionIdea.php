<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Auction
 * @copyright  Copyright 2012 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: EditIdea.php
 * @author     
 */
class Ynidea_Form_EditVersionIdea extends Ynidea_Form_CreateIdea
{
  public $_error = array();
  public function init()
  {
    parent::init();
	$translate = Zend_Registry::get('Zend_Translate');
	$idea = Engine_Api::_()->getItem('ynidea_idea', Zend_Controller_Front::getInstance()->getRequest()->getParam('id'));
    $this->setTitle('Edit Idea');
	if($idea->publish_status == 'publish')  
	{       
         $description = $translate->translate("Editing version").": ".$idea->getNewestVersion()->idea_version." .";
		 $description .= $translate->translate("Currently published version").": ".$idea->version.". ".$translate->translate("After saving & reviewing your newest version, you may publish it with the 'Version History' page.");
	}
	else
		$description = $translate->translate("Editing version").": .".$idea->getNewestVersion()->idea_version." ".$translate->translate("After saving & reviewing your newest version, you may publish it with the 'Detail' page.");
    $this->setDescription($description);
    $this->submit->setLabel('Save Changes');
  }
}