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
class Ynidea_Form_EditIdea extends Ynidea_Form_CreateIdea
{
  public $_error = array();
  public function init()
  {
    parent::init();
    $this->setTitle('Edit Idea')
         ->setDescription("Edit your idea below, then click 'Save Changes' to save your idea.");
    $this->submit->setLabel('Save Changes');
  }
}