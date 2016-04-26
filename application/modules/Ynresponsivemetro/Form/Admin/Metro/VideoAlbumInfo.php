<?php
class Ynresponsivemetro_Form_Admin_Metro_VideoAlbumInfo extends Core_Form_Admin_Widget_Standard
{
  public function init()
  {
    parent::init();
    // Set form attributes
    $this
      ->setDescription('Images are uploaded via the File Media Manager.')
      ;
	$view = Zend_Registry::get('Zend_View');
	$view -> headScript() -> appendFile(Zend_Registry::get('StaticBaseUrl') . 'application/modules/Ynresponsive1/externals/scripts/jscolor.js');
    // Get available files
    $logoOptions = array('' => 'Text-only (No logo)');
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
	
    $this->addElement('Select', 'video_icon', array(
      'label' => 'Videos - Icon',
      'multiOptions' => $logoOptions,
    ));
	
	$this->addElement('Text', 'video_background_color', array(
      'label' => 'Videos - Background Color',
      'class' => 'color',   
      'allowEmpty' => false,
      'value' => "transparent",
    ));
	
	$this->addElement('Text', 'video_text_color', array(
      'label' => 'Videos - Text Color',
      'class' => 'color',   
      'allowEmpty' => false,
      'value' => "transparent",
    ));
	
	$this->addElement('Select', 'album_icon', array(
      'label' => 'Albums - Icon',
      'multiOptions' => $logoOptions,
    ));
	
	$this->addElement('Text', 'album_background_color', array(
      'label' => 'Albums - Background Color',
      'class' => 'color',   
      'allowEmpty' => false,
      'value' => "transparent",
    ));
	
	$this->addElement('Text', 'album_text_color', array(
      'label' => 'Albums - Text Color',
      'class' => 'color',   
      'allowEmpty' => false,
      'value' => "transparent",
    ));
	
  }
}