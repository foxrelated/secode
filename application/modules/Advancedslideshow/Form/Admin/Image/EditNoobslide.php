<?php

class Advancedslideshow_Form_Admin_Image_EditNoobslide extends Advancedslideshow_Form_Admin_Image_Noobslide {

  public function init() {
    parent::init();
    $this->setTitle('Edit Custom Slide');
    $this->setDescription('');
    $this->is_thumb->setValue(0);
    $this->submit->setLabel("Save Changes");
  }

}

?>