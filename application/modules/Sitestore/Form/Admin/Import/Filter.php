<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Filter.php 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitestore_Form_Admin_Import_Filter extends Engine_Form {

  public function init() {

    $this
            ->setAttribs(array(
                'id' => 'filter_form',
                'class' => 'global_form_box',
            ));

    $this->addElement('Hidden', 'order', array(
        'order' => 10001,
    ));

    $this->addElement('Hidden', 'order_direction', array(
        'order' => 10002,
    ));

    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
  }

}
?>