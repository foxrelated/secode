<?php
class Ynresponsivemetro_Form_Admin_Metro_Introduction extends Core_Form_Admin_Widget_Standard
{
  public function init()
  {
    parent::init();
    // Set form attributes
    $this
      ->setTitle('Edit Introduction Widget')
      ->setDescription('Images are uploaded via the File Media Manager.')
      ;
	  
	$this->addElement('Text', 'title', array(
      'label' => 'Introduction Title',
      'value' => 'What can we do for you?',
      'description' => 'Max 25 characters',
      'validators' => array(
        array('NotEmpty', true),
        array('StringLength', false, array(0, 25)),
      ),
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
    ));
	$this->title->getDecorator("Description")->setOption("placement", "append");
	$this->addElement('Text', 'content', array(
      'label' => 'Introduction Content',
      'description' => 'Max 256 characters',
      'validators' => array(
        array('NotEmpty', true),
        array('StringLength', false, array(0, 256)),
      ),
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
	));
	$this->content->getDecorator("Description")->setOption("placement", "append");
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
      'label' => 'Middle Image',
      'multiOptions' => $logoOptions,
    ));
  }
}