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
class Ynidea_Form_EditTrophy extends Ynidea_Form_CreateTrophy
{
  public $_error = array();
  public function init()
  {
    parent::init();
    $this->setTitle('Edit Trophy')
         ->setDescription("Edit your Trophy below, then click 'Save Changes' to save your trohpy.");
    $this->submit->setLabel('Save Changes');
  }
}