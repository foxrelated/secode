<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Events
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Filter.php 22.09.12 12:57 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Events
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Heevent_Form_Admin_Transaction_Refund extends Engine_Form
{
  public function init()
  {
    $this
      ->clearDecorators()
      ->addDecorator('FormElements')
      ->addDecorator('Form')
      ;

    $this
      ->setAttribs(array(
        'id' => 'refund_form',
        'class' => 'global_form_box',
      ))
      ->setMethod('GET')
      ;


    // Element: order
    $this->addElement('Hidden', 'refund_id', array(
      'refund_id' => 0,
    ));
  }
}