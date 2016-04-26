<?php
class Ynresponsivemetro_Form_Admin_Metro_Blocks extends Core_Form_Admin_Widget_Standard
{
  public function init()
  {
    parent::init();
    // Set form attributes
    $this
      ->setTitle('Edit Blocks Widget')
      ->setDescription('Shows your site-wide main logo or title. Images are uploaded via the File Media Manager.')
      ;

    // Get available files
    $logoOptions = array('' => 'Default');
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

    $this->addElement('Select', 'background_image', array(
      'label' => 'Background Image',
      'multiOptions' => $logoOptions,
    ));
  }
}