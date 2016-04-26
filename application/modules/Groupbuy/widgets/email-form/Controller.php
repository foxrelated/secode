<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: latest auctions
 * @author     Minh Nguyen
 */
class Groupbuy_Widget_EmailFormController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
	$this->view->form = $form = new Groupbuy_Form_Email();
  }
}
?>