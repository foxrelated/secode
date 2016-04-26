<?php

class Advancedslideshow_Form_Admin_Image_Noobslide extends Engine_Form {

  protected $_src;

  public function getSrc() {
    return $this->_src;
  }

  public function setSrc($value) {
    $this->_src = $value;
    return;
  }

  public function init() {
    $src = $this->_src;
    $slideCount = count($src);
    $this
            ->setTitle('Create a Custom Slide')
            ->setDescription('Here, create a custom slide that can contain more than 1 image. You can also select a layout for your slide from the “Available Slide Layouts” section of this page.');

    $advancedslideshow_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('advancedslideshow_id', null);

    $upload_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'upload-photo', 'module' => 'advancedslideshow', 'controller' => 'slideshows', 'advancedslideshow_id' => $advancedslideshow_id), 'admin_default', true);
    
    if (!empty($slideCount)) {
      $this->addElement('radio', 'manual', array(
          'label' => 'Upload Manually',
          'description' => 'Do you want to upload images manually for this slide?',
          'multiOptions' => array(
              1 => 'Yes',
              0 => 'No',
          ),
          'onclick' => "manualBrowse();",
          'value' => 0
      ));

      for ($tempSlideCount = 1; $tempSlideCount <= $slideCount; $tempSlideCount++) {
        $elementName = "manual_$tempSlideCount";
        $x = $tempSlideCount - 1;
        $this->addElement('File', $elementName, array(
            'label' => "<img src = '$src[$x]' height=48px width =48px>"
        ));
        $this->$elementName->getDecorator('label')->setOption('escape', false);
        $this->$elementName->addValidator('Extension', false, 'jpg,png,gif,jpeg');
      }
    }
    
    $this->addElement('TinyMce', 'slide_html', array(
        'label' => 'Slide Content',
        'allowEmpty' => false,
        'attribs' => array('rows' => 180, 'cols' => 350, 'style' => 'width:740px; max-width:740px;height:858px;'),
        'editorOptions' => Engine_Api::_()->seaocore()->tinymceEditorOptions($upload_url),
//        'editorOptions' => array(
//            'plugins' => 'preview,table,layer,style,xhtmlxtras,media,paste',
//            'theme_advanced_buttons1' => "preview,code,|,cut,copy,paste,pastetext,pasteword,|,undo,redo,|,link,unlink,anchor,charmap,image,media,|,hr,removeformat,cleanup",
//            'theme_advanced_buttons2' => "bold,italic,underline,strikethrough,|,bullist,numlist,|,outdent,indent,blockquote,|,justifyleft,justifycenter,justifyright,justifyfull,|,sub,sup,|,tablecontrols",
//            'upload_url' => $upload_url,
//            'theme_advanced_buttons3' => "formatselect,fontselect,fontsizeselect,|,forecolor,backcolor,|,insertlayer,moveforward,movebackward,absolute,|,styleprops,attribs,|,cite,del,ins,"),
        'filters' => array(new Engine_Filter_Censor()),
    ));

    $advancedslideshow = Engine_Api::_()->getItem('advancedslideshow', $advancedslideshow_id);
    $noobWalk = (unserialize($advancedslideshow->noob_elements) );
    if (empty($noobWalk['noob_walk']))
      $noobThumb = 1;
    else
      $noobThumb = 0;
    $this->addElement('radio', 'is_thumb', array(
        'label' => 'Upload Thumbnail',
        'description' => 'Do you want to upload a thumbnail for this slide?',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No',
        ),
        'onclick' => 'showBrowse();',
        'value' => $noobThumb
    ));
    $this->addElement('File', 'thumbnail', array(
    ));

    $this->addElement('Button', 'submit', array(
        'label' => 'Create',
        'type' => 'submit',
        'ignore' => true
    ));
  }

}
?>


