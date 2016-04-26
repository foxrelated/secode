<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedslideshow
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Demo.php 2011-10-22 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Advancedslideshow_Form_Admin_Settings_Demo extends Engine_Form {

  public function init() {
    $this
            ->setTitle('Slideshow Demo')
            ->setDescription('Here, you can see the demo of all slideshow types.');

    $this->addElement('Select', 'advancedslideshow_type', array(
        'label' => 'Slideshow type',
        'onchange' => 'javascript:slidetype(this.value);',
        'multiOptions' => array(
            'fadd' => 'Fading',
            'flom' => 'Curtain / Blind',
            'zndp' => 'Zooming & Panning',
            'push' => 'Push',
            'flas' => 'Flash',
            'fold' => 'Fold',
            'noob' => 'HTML Slides with Bullet Navigation'
        ),
        'value' => 'fadd'
    ));

    $this->addElement('Checkbox', 'advancedslideshow_thumb', array(
        'label' => 'Hide thumbnails',
        'value' => 0,
    ));

    $this->addElement('Button', 'submit', array(
        'label' => 'View',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}
?>