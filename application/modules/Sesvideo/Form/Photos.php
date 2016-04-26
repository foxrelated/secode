<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Photos.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesvideo_Form_Photos extends Engine_Form {

  public function init() {
    $user = Engine_Api::_()->user()->getViewer();
    // Init form
    $this->setTitle('Add New Photos')
            ->setDescription('Choose photos on your computer to add to this chanel.')
            ->setAttrib('id', 'form-upload')
            ->setAttrib('name', 'albums_create')
            ->setAttrib('enctype', 'multipart/form-data')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

    // Init album
    $chanelTable = Engine_Api::_()->getItemTable('sesvideo_chanel');
    $myChanels = $chanelTable->select()
            ->from($chanelTable, array('chanel_id', 'title'))
            ->where('owner_id = ?', Engine_Api::_()->user()->getViewer()->getIdentity())
            ->query()
            ->fetchAll();
    $chanelOptions = array();
    foreach ($myChanels as $myChanel) {
      $chanelOptions[$myChanel['chanel_id']] = $myChanel['title'];
    }
    $this->addElement('Select', 'chanel', array(
        'label' => 'Choose Chanel',
        'multiOptions' => $chanelOptions,
    ));
    $translate = Zend_Registry::get('Zend_Translate');
    $this->addElement('Dummy', 'fancyuploadfileids', array('content' => '<input id="fancyuploadfileids" name="file" type="hidden" value="" >'));
    $this->addElement('Dummy', 'tabs_form_albumcreate', array(
        'content' => '<div class="sesalbum_create_form_tabs sesbasic_clearfix sesbm"><ul id="sesalbum_create_form_tabs" class="sesbasic_clearfix"><li class="active sesbm"><i class="fa fa-arrows sesbasic_text_light"></i><a href="javascript:;" class="drag_drop">' . $translate->translate('Drag & Drop') . '</a></li><li class=" sesbm"><i class="fa fa-upload sesbasic_text_light"></i><a href="javascript:;" class="multi_upload">' . $translate->translate('Multi Upload') . '</a></li><li class=" sesbm"><i class="fa fa-link sesbasic_text_light"></i><a href="javascript:;" class="from_url">' . $translate->translate('From URL') . '</a></li></ul></div>',
    ));
    $this->addElement('Dummy', 'drag-drop', array(
        'content' => '<div id="dragandrophandler" class="sesalbum_upload_dragdrop_content sesbasic_bxs">' . $translate->translate('Drag & Drop Files Here') . '</div>',
    ));
    $this->addElement('Dummy', 'from-url', array(
        'content' => '<div id="from-url" class="sesalbum_upload_url_content sesbm"><input type="text" name="from_url" id="from_url_upload" value="" placeholder="' . $translate->translate('Enter Image URL to upload') . '"><span id="loading_image"></span><span></span><button id="upload_from_url">' . $translate->translate('Upload') . '</button></div>',
    ));
    // Init file
    $this->addElement('Dummy', 'file_multi', array('content' => '<input type="file" accept="image/x-png,image/jpeg" onchange="readImageUrl(this)" multiple="multiple" id="file_multi" name="file_multi">'));
    $this->addElement('Dummy', 'uploadFileContainer', array('content' => '<div id="show_photo_container" class="sesalbum_upload_photos_container sesbasic_bxs sesbasic_custom_scroll clear"><div id="show_photo"></div></div>'));
    // Init submit
    $this->addElement('Button', 'submit', array(
        'label' => 'Save Photos',
        'type' => 'submit',
    ));
  }

  public function clearChanel() {
    $this->getElement('album')->setValue(0);
  }

  public function saveValues() {
    $values = $this->getValues();
    $api = Engine_Api::_()->getDbtable('actions', 'activity');
    $chanel = Engine_Api::_()->getItem('sesvideo_chanel', $values['chanel']);
    $action = $api->addActivity(Engine_Api::_()->user()->getViewer(), $chanel, 'sesvideo_photo_add', null, array('count' => count(explode(' ', rtrim($_POST['file'], ' ')))));
    // Do other stuff
    $count = 0;
    if (isset($_POST['file'])) {
      $explodeFile = explode(' ', rtrim($_POST['file'], ' '));
      foreach ($explodeFile as $photo_id) {
        $photo = Engine_Api::_()->getItem("sesvideo_chanelphoto", $photo_id);
        $photo->chanel_id = $chanel->chanel_id;
        $photo->save();
        if ($action instanceof Activity_Model_Action && $count < 8) {
          $api->attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
        }
        $count++;
      }
    }
    return $chanel;
  }

}
