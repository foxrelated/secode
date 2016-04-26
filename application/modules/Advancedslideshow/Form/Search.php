<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedslideshow
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Search.php 2011-10-22 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Advancedslideshow_Form_Search extends Engine_Form {

  public function init() {
    $this->setAttribs(array(
                'id' => 'filter_form',
                'class' => 'global_form_box',))
            ->setAction($_SERVER['REQUEST_URI']);

    $this->addElement('Select', 'slideshow_type', array(
        'label' => 'Slideshow type',
        'onchange' => 'javascript:slidetype(this.value);',
        'multiOptions' => array(
            'fadd' => 'Fading',
            'flom' => 'Curtain / Blind',
            'zndp' => 'Zooming & Panning',
            'push' => 'Push',
            'flas' => 'Flash',
            'fold' => 'Fold'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('advancedslideshow.type', 'fadd'),
    ));

    $this->addElement('Checkbox', 'slideshow_thumb', array(
        'label' => 'Show thumbnails',
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('advancedslideshow.thumb', '0'),
    ));

    $this->addElement('Button', 'submit', array(
        'label' => 'View',
        'type' => 'submit',
        'value' => '1',
    ));
  }

}
?>