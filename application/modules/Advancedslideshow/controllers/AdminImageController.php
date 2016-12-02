<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedslideshow
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminImageController.php 2011-10-22 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Advancedslideshow_AdminImageController extends Core_Controller_Action_Admin {

  public function init() {
    //GET SLIDESHOW ID
    $advancedslideshow_id = (int) $this->_getParam('advancedslideshow_id');

    if (!Engine_Api::_()->core()->hasSubject()) {
      if (0 !== ($image_id = (int) $this->_getParam('image_id')) && null !== ($image = Engine_Api::_()->getItem('advancedslideshow_image', $image_id))) {
        Engine_Api::_()->core()->setSubject($image);
      } else if (0 !== ($advancedslideshow_id) && null !== ($advancedslideshow = Engine_Api::_()->getItem('advancedslideshow', $advancedslideshow_id))) {
        Engine_Api::_()->core()->setSubject($advancedslideshow);
      }
    }
  }

  public function simpleUploadAction() {

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('advancedslideshow_admin_main', array(), 'advancedslideshow_admin_main_manage_slideshows');

    //GET ADVANCEDSLIDESHOW ID
    $this->view->advancedslideshow_id = $advancedslideshow_id = $this->_getParam('advancedslideshow_id');

    //CREATE FORM
    $this->view->form = $form = new Advancedslideshow_Form_Admin_Image_Simpleupload();

    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    if ($this->getRequest()->isPost()) {

      for ($i = 1; $i <= 10; $i++) {
        $elementName = "photo_$i";

        //UPLOAD PHOTO
        if (isset($_FILES[$elementName]) && is_uploaded_file($_FILES[$elementName]['tmp_name'])) {
          $params = array(
              'advancedslideshow_id' => $advancedslideshow_id,
              'user_id' => 1,
          );

          //CREATE IMAGE
          Engine_Api::_()->advancedslideshow()->createImage($params, $_FILES[$elementName]);
        }
      }
    }

    //REDIRECT
    $this->_redirect("admin/advancedslideshow/slides/manage/advancedslideshow_id/" . $advancedslideshow_id);
  }

  //ACTION FOR UPLOAD SLIDE
  public function uploadAction() {

    //GET NAVIGATION
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('advancedslideshow_admin_main', array(), 'advancedslideshow_admin_main_manage_slideshows');

    //GET SLIDESHOW SUBJECT AND SLIDESHOW ID
    $advancedslideshow = Engine_Api::_()->core()->getSubject();
    $this->view->advancedslideshow_id = $advancedslideshow_id = $advancedslideshow->advancedslideshow_id;
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $getSlideUpload = $settings->getSetting('siteslideshow.slide.upload', null);

    if (isset($_GET['ul']) || isset($_FILES['Filedata']))
      return $this->_forward('upload-image', null, null, array('format' => 'json', 'advancedslideshow_id' => $advancedslideshow_id));

    $this->view->form = $form = new Advancedslideshow_Form_Admin_Image_Upload();
    $form->file->setAttrib('data', array('advancedslideshow_id' => $advancedslideshow_id));
    $status = $this->getUploadedImage();

    if ((!$this->getRequest()->isPost()) || empty($getSlideUpload)) {
      return;
    }

    if ((!$form->isValid($this->getRequest()->getPost())) || empty($getSlideUpload)) {
      return;
    }

    $db = Engine_Api::_()->getItemTable('advancedslideshow_image')->getAdapter();
    $db->beginTransaction();

    try {
      $values = $form->getValues();
      $params = array(
          'advancedslideshow_id' => $advancedslideshow_id,
          'user_id' => Engine_Api::_()->user()->getViewer()->getIdentity(),
      );

      $settings->setSetting('siteslideshow.slide.upload', $status);
      $settings->setSetting('siteslideshow.manage', $status);
      $settings->setSetting('siteslideshow.slide.info', $status);

      foreach ($values['file'] as $image_id) {
        $image = Engine_Api::_()->getItem("advancedslideshow_image", $image_id);
        if (!($image instanceof Core_Model_Item_Abstract) || !$image->getIdentity())
          continue;

        $image->save();

        if ($advancedslideshow->image_id == 0) {
          $advancedslideshow->image_id = $image->file_id;
          $advancedslideshow->save();
        }
      }

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    //REDIRECT
    $this->_redirect("admin/advancedslideshow/slides/manage/advancedslideshow_id/" . $advancedslideshow_id);
  }

  //ACTION FOR UPLOAD SLIDE
  public function uploadImageAction() {
    //GET SLIDESHOW ID
    $advancedslideshow_id = (int) $this->_getParam('advancedslideshow_id');

    //GET VIEWER INFORMATION
    if (!$this->_helper->requireUser()->checkRequire()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Max file size limit exceeded (probably).');
      return;
    }
    
    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    $values = $this->getRequest()->getPost();
    if (empty($values['Filename'])) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('No file');
      return;
    }

    if (!isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid Upload');
      return;
    }

    $db = Engine_Api::_()->getDbtable('images', 'advancedslideshow')->getAdapter();
    $db->beginTransaction();

    try {
      $params = array(
          'advancedslideshow_id' => $advancedslideshow_id,
          'user_id' => 1,
      );
      $this->view->status = true;
      include_once APPLICATION_PATH . '/application/modules/Advancedslideshow/controllers/license/license3.php';

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('An error occurred.');
      return;
    }
  }

  public function getUploadedImage() {
    $status = false;
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $imageNum = $settings->getSetting('siteslideshow.menu.info');
    $imgStr = (string) strlen($imageNum) - 2;
    $checkValue = $imageNum[$imgStr];

    $getSlideStr = $settings->getSetting('advancedslideshow.getslide');
    $getSlideString = (string) strlen($getSlideStr);
    $getNumber = substr($getSlideString, -1);

    if ($getNumber == $checkValue) {
      $status = true;
    }
    return $status;
  }

  //ACTION FOR DELET SLIDE
  public function removeAction() {
    //DELETE SLIDE DURING THE UPLOAD
    $is_ajax = (int) $this->_getParam('is_ajax');
    if (!empty($is_ajax)) {

      //GET IMAGE ID AND IT'S OBJECT
      $image_id = (int) $this->_getParam('image_id');
      $image = Engine_Api::_()->getItem('advancedslideshow_image', $image_id);

      $db = $image->getTable()->getAdapter();
      $db->beginTransaction();

      try {
        $image->delete();
        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }
      //@unlink(APPLICATION_PATH . "/public/advancedslideshow/1000000/1000/5/" . $image_id . 't.' . $image->extension);
    }

    //GET SLIDESHOW ID AND IT'S OBJECT
    $advancedslideshow_id = (int) $this->_getParam('advancedslideshow_id');
    $this->view->advancedslideshow = $advancedslideshow = Engine_Api::_()->getItem('advancedslideshow', $advancedslideshow_id);

    if ($this->getRequest()->isPost() && $this->getRequest()->getPost('confirm') == true) {

      //GET IMAGE ID AND IT'S OBJECT
      $image_id = (int) $this->_getParam('image_id');
      $image = Engine_Api::_()->getItem('advancedslideshow_image', $image_id);

      if (empty($image)) {
        return;
      }

      $db = $image->getTable()->getAdapter();
      $db->beginTransaction();

        try {
          $image->delete();
          $db->commit();
        } catch (Exception $e) {
          $db->rollBack();
          throw $e;
        }

      //@unlink(APPLICATION_PATH . "/public/advancedslideshow/1000000/1000/5/" . $image_id . 't.' . $image->extension);

      //GET TOTAL SLIDES COUNT
      $total_images = Engine_Api::_()->getDbTable('images', 'advancedslideshow')->getTotalSlides($advancedslideshow_id);

      $start_index = $advancedslideshow->start_index;

      if ($start_index > $total_images - 1) {
        if ($total_images != 0) {
          $advancedslideshow->start_index = $total_images - 1;
          $advancedslideshow->save();
        } else {
          $advancedslideshow->start_index = 0;
          $advancedslideshow->save();
        }
      }

      $parentRedirect = 'admin/advancedslideshow/slides/manage/advancedslideshow_id/' . $advancedslideshow_id;
      $this->_forward('success', 'utility', 'core', array(
          'smoothboxClose' => true,
          'parentRefresh' => 10,
          'parentRedirect' => $parentRedirect,
          'messages' => Zend_Registry::get('Zend_Translate')->_('You have successfully deleted the slide.')
      ));
    }
  }
  
  private function createSlide($slideArg) {
    if ($slideArg == '1')
      return '<div style="position: relative; overflow: hidden; background-color: #ffffff; height: 360px; width: 1088px;">
<div style="top: 0px; left: 0px; overflow: hidden; float: left; position: absolute; width: 256px; height: 259px;"><a href="#"><img title="" src="https://lh4.googleusercontent.com/-yaPqb1NJLCw/Up3fkZo7DTI/AAAAAAAAAoE/vka8wQCYPEk/s260-no/p1.jpg" alt=""> </a></div>
<div style="top: 0px; overflow: hidden; float: left; position: absolute; height: 110px; left: 263px; width: 425px;"><a href="#"> <img title="" src="https://lh5.googleusercontent.com/-VgCSS5AiWbc/Up3flAdP89I/AAAAAAAAAoM/8Is9xW759mk/w450-h150-no/p2.jpg" alt=""> </a></div>
<div style="overflow: hidden; float: left; position: absolute; top: 116px; left: 263px; width: 209px; height: 143px;"><a href="#"><img title="" src="https://lh6.googleusercontent.com/-7mMJwySaJ0E/Up3flsxsWtI/AAAAAAAAAoU/truxgRu6TL4/s329-no/p4.jpg" alt=""> </a></div>
<div style="overflow: hidden; float: left; position: absolute; top: 116px; width: 209px; left: 479px; height: 143px;"><a href="#"> <img title="" src="https://lh3.googleusercontent.com/-n_QYmSut3MY/Up3flxEY6BI/AAAAAAAAAoc/ivU1EWTHmzM/s329-no/p5.jpg" alt=""> </a></div>
<div style="top: 0px; left: 695px; overflow: hidden; float: left; position: absolute; height: 259px; width: 256px;"><a href="#"><img title="" src="https://lh4.googleusercontent.com/-DdiAE7mEH0c/Up3flXf3NAI/AAAAAAAAApA/Wu99j_Z8fEo/s260-no/p3.jpg" alt=""> </a></div>
<div style="top: 0px; overflow: hidden; float: left; position: absolute; height: 167px; width: 130px; left: 958px;"><a href="#"> <img title="" src="https://lh4.googleusercontent.com/-5dF3UNSP8cs/Up3fmcpYM2I/AAAAAAAAAok/HAObjb0MyJA/w239-h329-no/p6.jpg" alt=""> </a></div>
<div style="overflow: hidden; float: left; position: absolute; height: 85px; top: 174px; width: 130px; left: 958px;"><a href="#"> <img width="211" height="194" title="" src="https://lh6.googleusercontent.com/-XxhTTl9a3Uo/Up3fptyesaI/AAAAAAAAAqA/bruHxcSkbpI/w150-h100-no/pro61.jpg" alt=""> </a></div>
<div style="left: 0px; overflow: hidden; float: left; position: absolute; top: 265px; height: 95px; width: 290px;"><a href="#"> <img title="" src="https://lh4.googleusercontent.com/-QRfxQiwFPHU/Up3fmjG1QDI/AAAAAAAAAow/iCWV33xnlDg/w300-h120-no/p8.jpg" alt=""> </a></div>
<div style="overflow: hidden; float: left; position: absolute; top: 265px; height: 95px; width: 180px; left: 296px;"><a href="#"> <img title="" src="https://lh5.googleusercontent.com/-lwZs4DjaV_M/Up3fnPpkW2I/AAAAAAAAAo4/cTMy-cAmNY0/w300-h120-no/p9.jpg" alt=""> </a></div>
<div style="overflow: hidden; float: left; position: absolute; top: 265px; height: 95px; margin-bottom: 4px; left: 482px; width: 175px;"><a href="#"> <img width="209" height="152" title="" src="https://lh4.googleusercontent.com/-yyjpS7BJwj8/Up3fkSskTxI/AAAAAAAAAn4/TCfY8qqQohA/w480-h329-no/p10.jpg" alt=""> </a></div>
<div style="overflow: hidden; float: left; position: absolute; right: 135px; top: 265px; height: 95px; width: 290px;"><a href="#"><img title="" src="https://lh5.googleusercontent.com/-Q-nuy40YnI8/Up3fkUaEzDI/AAAAAAAAAnw/UzK5LUAYpq4/w640-h328-no/p11.jpg" alt=""></a></div>
<div style="overflow: hidden; float: left; position: absolute; right: 0px; top: 265px; width: 130px; height: 95px;"><a href="#"> <img title="" src="https://lh5.googleusercontent.com/-fsnKZGNVjrI/Up3fk2lSmaI/AAAAAAAAAoA/eWa1xV7HN4I/w150-h100-no/p12.jpg" alt=""> </a></div>
</div>';

    else if ($slideArg == '2')
      return'<div style="position: relative; overflow: hidden; background-color: #fff; padding: 5px; height: 360px; width: 1088px;">
<div style="width: 250px; top: 0px; left: 0px; overflow: hidden; float: left; position: absolute; height: 360px;"><a href="#"><img width="250" height="350" title="" src="https://lh4.googleusercontent.com/-IdxTVbVsoCk/Up3fnU_dJTI/AAAAAAAAApE/kakjggTqK2g/w228-h328-no/pro1.jpg" alt=""></a></div>
<div style="top: 0px; overflow: hidden; float: left; position: absolute; left: 256px; width: 433px; height: 158px;"><a href="#"><img width="416" height="150" title="" src="https://lh4.googleusercontent.com/-rpsxo-kSC2Y/Up3fnj3CdUI/AAAAAAAAApg/NAkD36fjUac/w500-h150-no/pro2.jpg" alt=""></a></div>
<div style="top: 165px; overflow: hidden; float: left; position: absolute; height: 195px; left: 256px; width: 213px;"><a href="#"><img width="200" height="185" title="" src="https://lh5.googleusercontent.com/-UWen49IvCpg/Up3foaCTgOI/AAAAAAAAApk/zi7s-iFFefA/w200-h185-no/pro5.png" alt=""></a></div>
<div style="top: 165px; overflow: hidden; float: left; position: absolute; height: 195px; width: 214px; left: 475px;"><a href="#"><img width="200" height="185" title="" src="https://lh6.googleusercontent.com/-WdjMjn7-sAI/Up3fpLmSM0I/AAAAAAAAApo/5B-VVE0cFdQ/w215-h195-no/pro6.jpg" alt=""></a></div>
<div style="width: 250px; top: 0px; left: 695px; overflow: hidden; float: left; position: absolute; height: 360px;"><a href="#"><img width="251" height="350" title="" src="https://lh6.googleusercontent.com/-bvRnbmxiEzE/Up3fnlNOCfI/AAAAAAAAApM/-rlEsrzL61c/w236-h329-no/pro3.jpg" alt=""></a></div>
<div style="top: 0px; overflow: hidden; float: left; position: absolute; left: 951px; width: 137px; height: 213px;"><a href="#"><img width="123" height="203" title="" src="https://lh5.googleusercontent.com/-Ge36N_MJr-Q/Up3foAxhUZI/AAAAAAAAApU/AngWwrfdnXs/w140-h220-no/pro4.jpg" alt=""></a></div>
<div style="top: 220px; overflow: hidden; float: left; position: absolute; width: 137px; left: 951px; height: 140px;"><a href="#"><img width="123" height="130" title="" src="https://lh3.googleusercontent.com/-sUieXrfV5mQ/Up3fpugE7jI/AAAAAAAAAp4/ECSZdW4i4-E/w128-h135-no/pro7.jpg" alt=""></a></div>
</div>';

    else if ($slideArg == '3')
      return'<div class="commerce-images" style="position: relative; overflow: hidden; background-color: rgb(255, 255, 255); height: 360px; width: 1088px;">
<div style="width: 272px; top: 0px; left: 0px; overflow: hidden; float: left; position: absolute; height: 360px;"><a href="#"><img alt="" src="https://lh3.googleusercontent.com/-uGWhaaZkceM/Upc5-bNASPI/AAAAAAAAAl8/G9t_RIzTAWQ/w272-h400-no/product1.png" title="" height="200px" width="300px"></a></div>
<div style="width: 272px; top: 0px; left: 272px; overflow: hidden; float: left; position: absolute; height: 360px;"><a href="#"><img alt="" src="https://lh5.googleusercontent.com/-MVoB-awp1qY/Upc5-_SASMI/AAAAAAAAAmA/NB3BJSq0m5M/w272-h400-no/product2.png" title="" height="200px" width="300px"></a></div>
<div style="width: 272px; top: 0px; left: 544px; overflow: hidden; float: left; position: absolute; height: 360px;"><a href="#"><img alt="" src="https://lh4.googleusercontent.com/-fx9pyweoev4/Upc5_-qH-VI/AAAAAAAAAmI/iEN3sBFNErc/w272-h400-no/product3.png" title="" height="200px" width="300px"></a></div>
<div style="width: 272px; top: 0px; right: 0px; overflow: hidden; float: left; position: absolute; height: 360px;"><a href="#"><img alt="" src="https://lh5.googleusercontent.com/-7zX3Y6op1aw/Upc6AYjHQjI/AAAAAAAAAmQ/D_0Qq13369g/w273-h400-no/product4.png" title="" height="200px" width="300px"></a></div>
</div>';

    else if ($slideArg == '4')
      return '<div style="position: relative; overflow: hidden; background-color: #ffffff; height: 360px; width: 1088px;">
<div style="overflow: hidden; float: left; position: absolute; left: 0px; top: 0px; width: 284px; height: 360px;"><a href="#"> <img width="270" height="210" title="" src="https://lh4.googleusercontent.com/4xeN8C8I34COPB5MwqKXB1YeFOzCrIEtePftf79wvZo=w402-h377-no" alt=""> </a></div>
<div style="overflow: hidden; float: left; position: absolute; top: 0px; left: 290px; width: 155px; height: 131px;"><a href="#"> <img width="149" height="130" title="" src="https://lh4.googleusercontent.com/-I78zyvMmdFI/Up3fq4xsXuI/AAAAAAAAAqQ/AwP92jTqi6o/s155-no/prod2.jpg" alt=""> </a></div>
<div style="overflow: hidden; float: left; position: absolute; top: 0px; width: 155px; left: 451px; height: 131px;"><a href="#"> <img width="150" height="130" title="" src="https://lh5.googleusercontent.com/-RSJwN4eoKAA/Up3frDMIJ2I/AAAAAAAAAqU/VnZiRKEEbPM/s155-no/prod3.jpg" alt=""> </a></div>
<div style="overflow: hidden; float: left; position: absolute; top: 0px; width: 155px; right: 321px; height: 131px;"><a href="#"> <img width="150" height="130" title="" src="https://lh4.googleusercontent.com/-bkAEQBY1Y54/Up3fsKVGUoI/AAAAAAAAAq0/s3clU14jvr8/w526-h329-no/prod4.jpg" alt=""> </a></div>
<div style="overflow: hidden; float: left; position: absolute; top: 0px; width: 155px; right: 160px; height: 131px;"><a href="#"> <img width="150" height="129" title="" src="https://lh6.googleusercontent.com/-Z4qUGun3V_k/Up3fsBOlm-I/AAAAAAAAAqk/ZOo68JgcWAE/w256-h197-no/prod5.jpeg" alt=""> </a></div>
<div style="overflow: hidden; float: left; position: absolute; right: 0px; top: 0px; width: 154px; height: 131px;"><a href="#"> <img width="150" height="129" title="" src="https://lh5.googleusercontent.com/-uLFqHdo67Bs/Up3fsIeQseI/AAAAAAAAAqw/ZPboQpLCbgU/s155-no/prod6.png" alt=""> </a></div>
<div style="overflow: hidden; float: left; position: absolute; left: 290px; top: 137px; height: 223px; width: 190px;"><a href="#"> <img width="172" height="145" title="" src="https://lh6.googleusercontent.com/-OvuAHW5RsV0/Up3fs6N2NEI/AAAAAAAAAq4/wv-eAsB6fdI/w295-h329-no/prod7.jpg" alt=""> </a></div>
<div style="overflow: hidden; float: left; position: absolute; top: 137px; height: 223px; right: 0px; width: 200px;"><a href="#"> <img width="186" height="222" title="" src="https://lh3.googleusercontent.com/-v0d6xYHY-B4/Up3fqwNLcuI/AAAAAAAAAqc/xoYMXaO3ogo/w219-h329-no/prod10.jpg" alt=""> </a></div>
<div style="overflow: hidden; float: left; position: absolute; height: 223px; top: 137px; left: 486px; width: 195px;"><a href="#"> <img width="173" height="145" title="" src="https://lh3.googleusercontent.com/-oO4e5cpvD2k/Up3fs1F6O1I/AAAAAAAAArE/AdKelD2-AGo/s329-no/prod8.jpeg" alt=""> </a></div>
<div style="overflow: hidden; float: left; position: absolute; top: 137px; height: 223px; width: 195px; right: 206px;"><a href="#"> <img width="131" height="108" title="" src="https://lh3.googleusercontent.com/-xmi_Ix1II1E/Up3ftR2K2pI/AAAAAAAAArI/uWyf0NlybCI/s329-no/prod9.jpg" alt=""> </a></div>
</div>';
    
    else if ($slideArg == '5')
      return
    '<div style="position: relative; overflow: hidden; background-color: #fff; height: 343px; width:850px"><div style="top: 0px; left: 0px; overflow: hidden; float: left; position: absolute; height: 250px; width: 204px;"><a href="#"> <img title="" src="https://lh3.googleusercontent.com/-xmi_Ix1II1E/Up3ftR2K2pI/AAAAAAAAArI/uWyf0NlybCI/s329-no/prod9.jpg" alt=""> </a></div><div style="top: 0px; overflow: hidden; float: left; position: absolute; left: 210px; width: 323px; height: 106px;"><a href="#"> <img title="" src="https://lh5.googleusercontent.com/-VgCSS5AiWbc/Up3flAdP89I/AAAAAAAAAoM/8Is9xW759mk/w450-h150-no/p2.jpg" alt=""> </a></div><div style="overflow: hidden; float: left; position: absolute; left: 210px; top: 112px; height: 138px; width: 158px;"><a href="#"> <img title="" src="https://lh5.googleusercontent.com/-uLFqHdo67Bs/Up3fsIeQseI/AAAAAAAAAqw/ZPboQpLCbgU/s155-no/prod6.png" alt=""> </a></div><div style="overflow: hidden; float: left; position: absolute; height: 138px; top: 112px; width: 159px; left: 374px;"><a href="#"> <img title="" src="https://lh3.googleusercontent.com/-n_QYmSut3MY/Up3flxEY6BI/AAAAAAAAAoc/ivU1EWTHmzM/s329-no/p5.jpg" alt=""> </a></div><div style="top: 0px; overflow: hidden; float: left; position: absolute; height: 250px; width: 204px; left: 538px;"><a href="#"> <img title="" src="https://lh4.googleusercontent.com/-QRfxQiwFPHU/Up3fmjG1QDI/AAAAAAAAAow/iCWV33xnlDg/w300-h120-no/p8.jpg" alt=""> </a></div><div style="top: 0px; overflow: hidden; float: left; position: absolute; height: 143px; left: 747px; width: 103px;"><a href="#"> <img title="" src="https://lh5.googleusercontent.com/-lwZs4DjaV_M/Up3fnPpkW2I/AAAAAAAAAo4/cTMy-cAmNY0/w300-h120-no/p9.jpg" alt=""> </a></div><div style="overflow: hidden; float: left; position: absolute; left: 747px; width: 103px; height: 77px; top: 149px;"><a href="#"> <img title="" src="https://lh4.googleusercontent.com/-yyjpS7BJwj8/Up3fkSskTxI/AAAAAAAAAn4/TCfY8qqQohA/w480-h329-no/p10.jpg" alt=""> </a></div><div style="left: 0px; overflow: hidden; float: left; position: absolute; width: 230px; top: 256px; height: 87px;"><a href="#"> <img title="" src="https://lh4.googleusercontent.com/-bkAEQBY1Y54/Up3fsKVGUoI/AAAAAAAAAq0/s3clU14jvr8/w526-h329-no/prod4.jpg" alt=""> </a></div><div style="overflow: hidden; float: left; position: absolute; height: 87px; top: 256px; left: 235px; width: 136px;"><a href="#"> <img title="" src="https://lh4.googleusercontent.com/-I78zyvMmdFI/Up3fq4xsXuI/AAAAAAAAAqQ/AwP92jTqi6o/s155-no/prod2.jpg" alt=""> </a></div><div style="overflow: hidden; float: left; position: absolute; left: 376px; height: 87px; top: 256px; width: 136px;"><a href="#"> <img title="" src="https://lh4.googleusercontent.com/-bkAEQBY1Y54/Up3fsKVGUoI/AAAAAAAAAq0/s3clU14jvr8/w526-h329-no/prod4.jpg" alt=""> </a></div><div style="overflow: hidden; float: left; position: absolute; top: 256px; height: 87px; right: 108px; width: 225px;"><a href="#"> <img title="" src="https://lh4.googleusercontent.com/-yaPqb1NJLCw/Up3fkZo7DTI/AAAAAAAAAoE/vka8wQCYPEk/s260-no/p1.jpg" alt=""> </a></div><div style="overflow: hidden; float: left; position: absolute; top: 232px; right: 0px; height: 111px; width: 103px;"><a href="#"> <img title="" src="https://lh6.googleusercontent.com/-7mMJwySaJ0E/Up3flsxsWtI/AAAAAAAAAoU/truxgRu6TL4/s329-no/p4.jpg" alt=""> </a></div>
</div>';
  }
    

  public function noobSlideAction() {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('advancedslideshow_admin_main', array(), 'advancedslideshow_admin_main_manage_slideshows');
    $this->view->advancedslideshow_id = $advancedslideshow_id = (int) $this->_getParam('advancedslideshow_id');
    if (isset($_GET['slide']))
    $slideParam = $_GET['slide'];  
    if (!empty($slideParam)) {
      switch ($slideParam) {
        case '1': $slide = $this->createSlide($slideParam);
          break;

        case '2': $slide = $this->createSlide($slideParam);
          break;

        case '3': $slide = $this->createSlide($slideParam);
          break;

        case '4': $slide = $this->createSlide($slideParam);
          break;
        
        case '5': $slide = $this->createSlide($slideParam);
          break;
      }
    }
    $imageCount = substr_count($slide, "<img");
    $imageString = $slide;
    $src = array();
    $splittedString = explode("<img", $imageString);
    for ($tempCount = 0; $tempCount < COUNT($splittedString); $tempCount++) {
      $temStr = $splittedString[$tempCount];
      if (strstr($temStr, "src=")) {
        $temExplodedSrcArr = explode('src="', $temStr);
        $explodedSrcArr = explode('"', $temExplodedSrcArr[1]);
        $src[] = $explodedSrcArr[0];
      }
    }
    $this->view->srcCount = @count($src);
    $this->view->form = $form = new Advancedslideshow_Form_Admin_Image_Noobslide(array('src' => $src));
    $form->slide_html->setValue($slide);

    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $values = $form->getValues();

    $tempParams = $params = array();
    $tempParams['thumb_id'] = 0;
    $tempParams['slide_html'] = $values['slide_html'];

    $params['advancedslideshow_id'] = $advancedslideshow_id;
    $params['slide_html'] = @serialize($tempParams);
    $row = Engine_Api::_()->getDbtable('images', 'advancedslideshow')->createRow();
    $row->setFromArray($params);

    $levels = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchAll();
    foreach ($levels as $level) {
      $level_id = $level->getIdentity();
      $levels_prepared[] = strval($level_id);
    }

    // prepare networks
    $networks = Engine_Api::_()->getDbtable('networks', 'network')->fetchAll();

    if (count($networks) > 0) {
      foreach ($networks as $network) {
        $network_id = $network->getIdentity();
        $networks_prepared[] = strval($network_id);
      }
    }
    $networks_prepared = Zend_Json_Encoder::encode($networks_prepared);
    $levels_prepared = Zend_Json_Encoder::encode($levels_prepared);
    $row->network = $networks_prepared;
    $row->level = $levels_prepared;
    $row->show_public = 1;
    if (!empty($values['is_thumb'])) {

      if (empty($values['thumbnail'])) {
        $form->adderror("You had not selected thumbnail");
        return;
      } else {
        $thumb_id = $row->setNoobThumb($form->thumbnail, $advancedslideshow_id);
        if (!empty($thumb_id)) {
          $tempParams['thumb_id'] = $thumb_id;
          $row->slide_html = @serialize($tempParams);
          $row->save();
        }
      }
    }

    for ($tempImage = 1; $tempImage <= $imageCount; $tempImage++) {
      $elementName = "manual_$tempImage";
      if (!empty($values[$elementName])) {
        $src[$tempImage - 1] = Engine_Api::_()->advancedslideshow()->manualPhoto($form->$elementName, $advancedslideshow_id);
      }
    }
    $tempFinalStr = '';
    $tempExplodedHtml = explode('src="', $imageString);
    for ($tempCount = 0; $tempCount < count($tempExplodedHtml); $tempCount++) {
      if (empty($tempCount)) {
        $tempFinalStr = $tempExplodedHtml[$tempCount];
      } else {
        $tempExplode = explode('"', $tempExplodedHtml[$tempCount]);
        $tempExplode[0] = $src[$tempCount - 1];
        $tempExplodedStr = implode('"', $tempExplode);
        $tempFinalStr .= 'src="' . $tempExplodedStr;
      }
    }
    $finalString = str_replace('\'', '"', $tempFinalStr);

    if (empty($values['manual']))
      $tempParams['slide_html'] = $values['slide_html'];
     else 
       $tempParams['slide_html'] = $finalString;
    $row->slide_html = @serialize($tempParams);
    $row->save();
    return $this->_helper->redirector->gotoRoute(array('module' => 'advancedslideshow', 'controller' => 'slides', 'action' => 'manage', 'advancedslideshow_id' => $advancedslideshow_id), 'admin_default', true);
  }

  public function editNoobSlideAction() {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('advancedslideshow_admin_main', array(), 'advancedslideshow_admin_main_manage_slideshows');

    $advancedslideshow = Engine_Api::_()->core()->getSubject();
    $tempArray = @unserialize($advancedslideshow->slide_html);
    $imageString = $tempArray['slide_html'];
    $imageCount = substr_count($imageString, "<img");
    $src = array();
    $splittedString = explode("<img", $imageString);
    for ($tempCount = 0; $tempCount < COUNT($splittedString); $tempCount++) {
      $temStr = $splittedString[$tempCount];
      if (strstr($temStr, "src=")) {
        $temExplodedSrcArr = explode('src="', $temStr);
        $explodedSrcArr = explode('"', $temExplodedSrcArr[1]);
        $src[] = $explodedSrcArr[0];
      }
    }
    $this->view->srcCount = @count($src);
    $this->view->form = $form = new Advancedslideshow_Form_Admin_Image_EditNoobslide(array('src' => $src));
    $form->slide_html->setValue($tempArray['slide_html']);
    $this->view->advancedslideshow_id = $advancedslideshow_id = (int) $this->_getParam('advancedslideshow_id');

    if (!$this->getRequest()->isPost()) {
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return;
    }

    $values = $form->getValues();
    for ($tempImage = 1; $tempImage <= $imageCount; $tempImage++) {
      $elementName = "manual_$tempImage";
      if (!empty($values[$elementName])) {
        $src[$tempImage - 1] = Engine_Api::_()->advancedslideshow()->manualPhoto($form->$elementName, $advancedslideshow_id);
      }
    }
    $tempFinalStr = '';
    $tempExplodedHtml = explode('src="', $imageString);
    for ($tempCount = 0; $tempCount < count($tempExplodedHtml); $tempCount++) {
      if (empty($tempCount)) {
        $tempFinalStr = $tempExplodedHtml[$tempCount];
      } else {
        $tempExplode = explode('"', $tempExplodedHtml[$tempCount]);
        $tempExplode[0] = $src[$tempCount - 1];
        $tempExplodedStr = implode('"', $tempExplode);
        $tempFinalStr .= 'src="' . $tempExplodedStr;
      }
    }
    $finalString = str_replace('\'', '"', $tempFinalStr);
    if (!empty($values['is_thumb'])) {
if (empty($values['thumbnail'])) {
        $form->adderror("You had not selected thumbnail");
        return;
      } else {
        $thumb_id = $advancedslideshow->setNoobThumb($form->thumbnail, $advancedslideshow_id);
        if (!empty($thumb_id)) {
          $tempArray['thumb_id'] = $thumb_id;
        }
      }
    }
    if (empty($values['manual'])) {
      $tempArray['slide_html'] = $values['slide_html'];
    } else {
      $tempArray['slide_html'] = $finalString;
    }
    $advancedslideshow->slide_html = @serialize($tempArray);
    $advancedslideshow->save();

    return $this->_helper->redirector->gotoRoute(array('module' => 'advancedslideshow', 'controller' => 'slides', 'action' => 'manage', 'advancedslideshow_id' => $advancedslideshow_id), 'admin_default', true);
  }

}

?>