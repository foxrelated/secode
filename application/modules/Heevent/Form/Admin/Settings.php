<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Settings.php 19.10.13 08:20 jungar $
 * @author     Jungar
 */

/**
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Heevent_Form_Admin_Settings extends Engine_Form
{
  public function init()
  {
    $settingsTbl = Engine_Api::_()->getDbtable('settings', 'core');
    $view = new Zend_View();
    $bgPosSetting = $settingsTbl->getSetting('heevent.cover.position', 1);
    $bgPos = array('left', 'center', 'right');
    $bgRepeat = ((boolean) $settingsTbl->getSetting('heevent.cover.repeat', 1)) ? 'repeat' : 'no-repeat';
    $this
      ->setTitle('Settings')
      ->setAttrib('class', 'heevent-admin-form')
      ->setDescription('HEEVENT_ADMIN_SETTINGS_DESCRIPTION');
    $cover = <<<COVER
    <div id="heevent_cover">
      <div class="cover-wrapper">
        <img id="heevent-admin-setting-cover" class="fake-img" src="{$view->layout()->staticBaseUrl}application/modules/Heevent/externals/images/fake-29x8.gif" style="background-position:{$bgPos[$bgPosSetting]};background-repeat:{$bgRepeat};background-image:url({$view->layout()->staticBaseUrl}application/modules/Heevent/externals/images/admin/cover-sample.gif)"/>
      </div>
    </div>
COVER;
        $this->addElement('Dummy', 'cover-photo', array(
          'content' => $cover
        ));

    $this->addElement('Radio', 'heevent_cover_position', array(
      'multiOptions' => array(
        0 => 'HEEVENT_Left',
        1 => 'HEEVENT_Center',
        2 => 'HEEVENT_Right',
      ),
      'label' => 'Cover Settings',
      'value' => $bgPosSetting
    ));
    // Repeat Background
    $this->addElement('Checkbox', 'heevent_cover_repeat', array(
      'label' => 'HEEVENT_Repeat',
      'value' => (boolean) $settingsTbl->getSetting('heevent.cover.repeat', 1)
    ));

    $this->addDisplayGroup(array('heevent_cover_position', 'heevent_cover_repeat'), 'cover_params', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
      'label' => 'Cover Settings'
    ));

    $map = <<<MAP
<div id="heevent-admin-setting-map">
<img id="heevent-admin-setting-map-img" src="{$view->layout()->staticBaseUrl}application/modules/Heevent/externals/images/fake-4x3.gif" /></div>
MAP;
    $this->addElement('Text', 'heevent_map_zoom', array(
      'label' => 'HEEVENT_Zoom of map',
      'value' => $settingsTbl->getSetting('heevent.map.zoom', 10),
      'style' => 'width: 3em',
      'onchange' => 'drawMap();'
    ));

    $this->addElement('Dummy', 'map', array(
      'content' => $map
    ));

    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
  public function getValues($suppressArrayNotation = false){
    $values = parent::getValues($suppressArrayNotation);
    unset($values['cover-photo']);
    unset($values['map']);
    return $values;
  }
}