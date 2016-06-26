<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Heevent.php 19.10.13 08:20 jungar $
 * @author     Jungar
 */

/**
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 04.10.13
 * Time: 14:21
 * To change this template use File | Settings | File Templates.
 */
class Heevent_View_Helper_Heevent extends Zend_View_Helper_Abstract
{
  public function heevent()
  {
    return $this;
  }
  public function getComposerForm(){
    $viewer = Engine_Api::_()->user()->getViewer();

    if($viewer->getIdentity()){
      $form = new Heevent_Form_Create(array(
              'parent_type' => 'user',
              'parent_id' => $viewer->getIdentity()
            ), true);
      $form->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' =>'create', 'format' => 'html'), 'heevent_extended', true));
    }
      return $form->render($this->view);
  }
  public function getCategoryCovers()
  {
    return Zend_Json::encode(Engine_Api::_()->heevent()->getCategoryCovers());
  }

  public function getTicketForm(Heevent_Model_Event $event = null, $price = 0)
  {
      $ticketForm = new Heevent_Form_Ticket($event,$price);
      $ticketForm->setAction($this->view->url(array('event_id' => $event->getIdentity()), 'heevent_payment', true));
      $form = $ticketForm->render($this->view);
      $content = <<<CONTENT
       <div class="ticket_form_wrapper" >{$form}</div>
CONTENT;
    return $content;
  }
}
