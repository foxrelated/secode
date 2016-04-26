<?php
class Ynresponsivemetro_Form_Admin_Metro_IntroductionEdit extends Engine_Form
{
  public function init()
  {
	$view = Zend_Registry::get('Zend_View');
	$view -> headScript() -> appendFile(Zend_Registry::get('StaticBaseUrl') . 'application/modules/Ynresponsive1/externals/scripts/jscolor.js');
    $this->setTitle('Edit Block')
      ->setAttrib('id', 'ynrespnosive_metro_photo_introduct_form')
	  ->setAttrib('class', 'global_form_popup')
      ->setMethod("POST")
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

	// Title
    $this->addElement('Text', 'title', array(
      'label' => 'Title',
      'description' => 'Max 64 characters',
      'allowEmpty' => false,
      'required' => true,
      'validators' => array(
        array('NotEmpty', true),
        array('StringLength', false, array(1, 64)),
      ),
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
    ));
	$this->title->getDecorator("Description")->setOption("placement", "append");
	$this -> title -> setAttrib('required', true);
	
    // Description
    $this->addElement('Textarea', 'description', array(
      'label' => 'Content',
      'description' => 'Max 128 characters',
      'maxlength' => '128',
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_EnableLinks(),
        new Engine_Filter_StringLength(array('max' => 128)),
      ),
    ));
	$this->description->getDecorator("Description")->setOption("placement", "append");

    // Get available files
    $iconOptions = array('' => '(No logo)');
    $imageExtensions = array('gif', 'jpg', 'jpeg', 'png');

    $it = new DirectoryIterator(APPLICATION_PATH . '/public/admin/');
    foreach( $it as $file ) {
      if( $file->isDot() || !$file->isFile() ) continue;
      $basename = basename($file->getFilename());
      if( !($pos = strrpos($basename, '.')) ) continue;
      $ext = strtolower(ltrim(substr($basename, $pos), '.'));
      if( !in_array($ext, $imageExtensions) ) continue;
      $iconOptions['public/admin/' . $basename] = $basename;
    }

    $this->addElement('Select', 'icon', array(
      'label' => 'Logo',
      'multiOptions' => $iconOptions,
    ));
	
	$this->addElement('Text', 'link', array(
      'label' => 'Color',
      'class' => 'color',   
      'allowEmpty' => false,
      'value' => "transparent",
    ));
	
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => false,
        'onClick'=> 'javascript:parent.Smoothbox.close();',
      'prependText' => ' or ',
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper',
      ),
    ));
  }
}