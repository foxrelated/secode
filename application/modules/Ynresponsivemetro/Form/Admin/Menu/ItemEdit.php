<?php
class Ynresponsivemetro_Form_Admin_Menu_ItemEdit extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Edit Menu Item')
      ->setAttrib('class', 'global_form_popup')
      ;
	$view = Zend_Registry::get('Zend_View');
	$view -> headScript() -> appendFile(Zend_Registry::get('StaticBaseUrl') . 'application/modules/Ynresponsive1/externals/scripts/jscolor.js');
    $this->addElement('Text', 'label', array(
      'label' => 'Label',
      'required' => true,
      'allowEmpty' => false,
    ));
	
    $this->addElement('Text', 'uri', array(
      'label' => 'URL',
      'required' => true,
      'allowEmpty' => false,
      'style' => 'width: 300px',
    ));
    
    // Get available files
    $logoOptions = array('' => 'Text-only (No images)');
    $imageExtensions = array('gif', 'jpg', 'jpeg', 'png');

    $it = new DirectoryIterator(APPLICATION_PATH . '/public/admin/');
    foreach( $it as $file ) {
      if( $file->isDot() || !$file->isFile() ) continue;
      $basename = basename($file->getFilename());
      if( !($pos = strrpos($basename, '.')) ) continue;
      $ext = strtolower(ltrim(substr($basename, $pos), '.'));
      if( !in_array($ext, $imageExtensions) ) continue;
      $logoOptions['public/admin/' . $basename] = $basename;
    }

    $this->addElement('Select', 'icon', array(
      'label' => 'Menu Icon',
      'multiOptions' => $logoOptions,
    ));
    
	 $this->addElement('Select', 'hover_active_icon', array(
      'label' => 'Menu Hover/Active Icon',
      'multiOptions' => $logoOptions,
    ));
	
 	$this->addElement('Text', 'background_color', array(
      'label' => 'Background Color',
      'class' => 'color',   
      'allowEmpty' => false,
      'value' => "transparent",
    ));
	$this->addElement('Text', 'text_color', array(
      'label' => 'Text Color',
      'class' => 'color',   
      'allowEmpty' => false,
      'value' => "transparent",
    )); 
	$this->addElement('Text', 'hover_color', array(
      'label' => 'Hover Color',
      'class' => 'color',   
      'allowEmpty' => false,
      'value' => "transparent",
    ));  
    
    $this->addElement('Checkbox', 'target', array(
      'label' => 'Open in a new window?',
      'checkedValue' => '_blank',
      'uncheckedValue' => '',
    ));

    $this->addElement('Checkbox', 'enabled', array(
      'label' => 'Enabled?',
      'checkedValue' => '1',
      'uncheckedValue' => '0',
    ));

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Edit Menu Item',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));
    
    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }
}