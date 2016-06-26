<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Covers.php 19.10.13 08:20 jungar $
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
 * Date: 27.09.13
 * Time: 17:05
 * To change this template use File | Settings | File Templates.
 */
class Heevent_Form_Admin_Covers extends Engine_Form
{
  public function init(){
    $this->addElement('File', 'covers', array(
      'isArray' => true,
      'multiple' => 'multiple',
      'accept' => 'image/*'
    ));
    $this->covers->addValidator('Extension', false, 'jpg,png,gif,jpeg');
  }
}
