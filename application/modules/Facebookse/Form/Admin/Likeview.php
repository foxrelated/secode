<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Likeview.php 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Facebookse_Form_Admin_Likeview extends Engine_Form {

  public function init() { 
    $settings = Engine_Api::_()->getApi('settings', 'core');
    // create an object for view
    $view = Zend_Registry::isRegistered( 'Zend_View' ) ? Zend_Registry::get( 'Zend_View' ) : null ;
    // My stuff
    $this
        ->setTitle( 'Facebook Like Button View' )
        ->setDescription( "Here, you can customize the Like button on your site." ) ;

    $logoOptions = array('application/modules/Facebookse/externals/images/like.png' => 'Default Icon');
    
    // Get available files (Icon for activity Feed).    
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;    
    $files = Engine_Api::_()->facebookse()->getUPloadedFiles();
    $logoOptions = array_merge($logoOptions, $files);    
    
    $URL = $view->baseUrl() . "/admin/files";
    $click = '<a href="' . $URL . '" target="_blank">over here</a>';
    $customBlocks = sprintf("Upload a small icon for your custom like button %s. (The dimensions of the image should be 13x13 px. The currently associated image is shown below this field.). Once you upload a new icon at the link mentioned, then refresh this page to see its preview below after selection.)", $click);

    if (!empty($logoOptions)) {
      $this->addElement('Select', 'facebookse_likeicon', array(
          'label' => 'Like Thumbs-up Image',
          'description' => $customBlocks,
          'multiOptions' => $logoOptions,
          'onchange' => "updateTextFields(this.value)",
          'value' => $settings->getSetting('facebookse.likeicon', '')
      ));
      $this->getElement('facebookse_likeicon')->getDecorator('Description')->setOptions(array('placement' =>
          'PREPEND', 'escape' => false));
    }
    $logo_photo = $settings->getSetting('facebookse_likeicon', 'application/modules/Facebookse/externals/images/like.png');
    if (!empty($logo_photo)) {

      $photoName = $view->baseUrl() . '/' . $logo_photo;
      $description = "<img src='$photoName' width='13' height='13'/>";
    }
    //VALUE FOR LOGO PREVIEW.
    $this->addElement('Dummy', 'fbbutton_likeicon_preview', array(
        'label' => 'Like Icon Preview',
        'description' => $description,
    ));
    $this->fbbutton_likeicon_preview
            ->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
    
    
    // Get available files (Icon for activity Feed).
    
    $logoOptions = array('application/modules/Facebookse/externals/images/liked.png' => 'Default Icon');  
    $logoOptions = array_merge($logoOptions, $files);
    if (!empty($logoOptions)) {
      $this->addElement('Select', 'facebookse_unlikeicon', array(
          'label' => 'Unlike Thumbs-down Image',
          'description' => $customBlocks,
          'multiOptions' => $logoOptions,
          'onchange' => "updateTextFields1(this.value)",
          'value' => $settings->getSetting('facebookse.unlikeicon', '')
      ));
      $this->getElement('facebookse_unlikeicon')->getDecorator('Description')->setOptions(array('placement' =>
          'PREPEND', 'escape' => false));
    }
    
    $logo_photo = $settings->getSetting('facebookse_unlikeicon', 'application/modules/Facebookse/externals/images/liked.png');
    if (!empty($logo_photo)) {

      $photoName = $view->baseUrl() . '/' . $logo_photo;
      $description = "<img src='$photoName' width='13' height='13'/>";
    }
    //VALUE FOR LOGO PREVIEW.
    $this->addElement('Dummy', 'fbbutton_unlikeicon_preview', array(
         'label' => 'Unlike Icon Preview',
        'description' => $description,
    ));
    $this->fbbutton_unlikeicon_preview
            ->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));

    $this->addElement( 'Text' , 'fblike_background_color' , array (
      'label' => 'Background Color' ,
      'decorators' => array ( array ( 'ViewScript' , array (
            'viewScript' => '_formImagerainbow1.tpl' ,
            'class' => 'form element'
        ) ) )
    ) ) ;

    $this->addElement( 'Text' , 'fblike_background_haourcolor' , array (
      'label' => 'Haour Color' ,
      'decorators' => array ( array ( 'ViewScript' , array (
            'viewScript' => '_formImagerainbow4.tpl' ,
            'class' => 'form element'
        ) ) )
    ) ) ;

    $this->addElement( 'Text' , 'fblike_text_color' , array (
      'label' => 'Text Color' ,
      'decorators' => array ( array ( 'ViewScript' , array (
            'viewScript' => '_formImagerainbow3.tpl' ,
            'class' => 'form element'
        ) ) )
    ) ) ;

    $this->addElement( 'Text' , 'fblike_haour_color' , array (
      'label' => 'Haour Color' ,
      'decorators' => array ( array ( 'ViewScript' , array (
            'viewScript' => '_formImagerainbow2.tpl' ,
            'class' => 'form element'
        ) ) )
    ) ) ;

    $this->addElement( 'Button' , 'submit' , array (
      'label' => 'Save Changes' ,
      'type' => 'submit' ,
      'ignore' => true ,
      'decorators' => array ( 'ViewHelper' )
    ) ) ;
    $buttons[] = 'submit' ;

    $this->addElement( 'Button' , 'default_settings' , array (
      'label' => 'Reset to Default' ,
      'type' => 'submit' ,
      'prependText' => ' or ' ,
      'decorators' => array (
        'ViewHelper'
      )
    ) ) ;
    $buttons[] = 'default_settings' ;


    $this->addDisplayGroup( $buttons , 'buttons' ) ;
    $button_group = $this->getDisplayGroup( 'buttons' ) ;
  }

}
?>