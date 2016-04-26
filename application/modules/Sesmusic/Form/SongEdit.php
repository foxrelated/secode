<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: SongEdit.php 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
class Sesmusic_Form_SongEdit extends Engine_Form {

  public function init() {

    $albumsong_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('albumsong_id');
    if ($albumsong_id)
      $album_song = Engine_Api::_()->getItem('sesmusic_albumsong', $albumsong_id);

    $this->setTitle('Edit Songs')
            ->setDescription('Here, you can edit the song information.');

    $this->addElement('Text', 'title', array(
        'label' => 'Song Name',
        'placeholder' => 'Enter Song Name',
        'maxlength' => '63',
        'filters' => array(
            new Engine_Filter_Censor(),
            new Engine_Filter_StringLength(array('max' => '63')),
        )
    ));

    //Category Work
    $categories = Engine_Api::_()->getDbtable('categories', 'sesmusic')->getCategory(array('column_name' => '*', 'param' => 'song'));
    $data[] = 'Select Category';
    foreach ($categories as $category) {
      $data[$category['category_id']] = $category['category_name'];
    }
    if (count($data) > 1) {
      //Add Element: Category
      $this->addElement('Select', 'category_id', array(
          'label' => 'Category',
          'multiOptions' => $data,
          'onchange' => "ses_subcategory(this.value)",
      ));

      //Subcategory
      $subcat = array();
      $subcategory = Engine_Api::_()->getDbtable('categories', 'sesmusic')->getModuleSubcategory(array('column_name' => "*", 'category_id' => $album_song->category_id, 'param' => 'song'));
      $count_subcat = count($subcategory->toarray());

      $subcat[] = "Select 2nd-level Category";
      foreach ($subcategory as $subcategory) {
        $subcat[$subcategory['category_id']] = $subcategory['category_name'];
      }

      //Add Element: Sub Category
      $this->addElement('Select', 'subcat_id', array(
          'label' => '2nd-level Category',
          'allowEmpty' => true,
          'required' => false,
          'multiOptions' => $subcat,
          'onchange' => "sessubsubcat_category(this.value)",
          'registerInArrayValidator' => false
      ));
      if (!empty($album_song->subcat_id)) {
        $this->subcat_id->setValue($album_song->subcat_id);
      }

      //SubSubcategory
      $subsubcat = array();
      $subsubcategory = Engine_Api::_()->getDbtable('categories', 'sesmusic')->getModuleSubsubcategory(array('column_name' => "*", 'category_id' => $album_song->subcat_id, 'param' => 'song'));
      $count_subcat = count($subsubcategory->toarray());
      $subsubcat[] = "Select 3rd-level Category";
      foreach ($subsubcategory as $subsubcategory) {
        $subsubcat[$subsubcategory['category_id']] = $subsubcategory['category_name'];
      }
      //Add Element: Sub Sub Category
      $this->addElement('Select', 'subsubcat_id', array(
          'label' => '3rd-level Category',
          'allowEmpty' => true,
          'multiOptions' => $subsubcat,
          'required' => false,
          'registerInArrayValidator' => false
      ));
      if (!empty($group['subsubcat_id'])) {
        $this->subsubcat_id->setValue($album_song->subcat_id);
      }
    }

    $this->addElement('Textarea', 'description', array(
        'label' => 'Song Description',
        'placeholder' => 'Enter Song Description',
        'maxlength' => '300',
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_StringLength(array('max' => '300')),
            new Engine_Filter_EnableLinks(),
        ),
    ));


    $this->addElement('Textarea', 'lyrics', array(
        'label' => 'Song Lyrics',
        'placeholder' => 'Enter Song Lyrics',
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            new Engine_Filter_EnableLinks(),
        ),
    ));

    $albumsong_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('albumsong_id');
    if ($albumsong_id)
      $albumsong = Engine_Api::_()->getItem('sesmusic_albumsong', $albumsong_id);

    $artistArray = array();
    $artistsTable = Engine_Api::_()->getDbTable('artists', 'sesmusic');
    $select = $artistsTable->select()->order('order ASC');
    $artists = $artistsTable->fetchAll($select);

    foreach ($artists as $artist) {
      $artistArray[$artist->artist_id] = $artist->name;
    }

    if (!empty($artistArray)) {
      $artistsValues = json_decode($albumsong->artists);
      $this->addElement('MultiCheckbox', 'artists', array(
          'label' => 'Song Artist',
          'description' => 'Choose from the below song artist.',
          'multiOptions' => $artistArray,
          'value' => $artistsValues,
      ));
    }

    $this->addElement('File', 'song_cover', array(
        'label' => 'Song Cover Photo',
        'onchange' => 'showReadImage(this,"song_cover_preview")',
    ));

    $this->song_cover->addValidator('Extension', false, 'jpg,png,gif,jpeg');
    if ($albumsong_id && $albumsong && $albumsong->song_cover) {
      $img_path = Engine_Api::_()->storage()->get($albumsong->song_cover, '')->getPhotoUrl();
      $path = $img_path;
      if (isset($path) && !empty($path)) {
	$this->addElement('Image', 'song_cover_preview', array(
	    'label' => 'Song Cover Preview',
	    'src' => $path,
	    'width' => 100,
	    'height' => 100,
	));
      }
    } else {
      $this->addElement('Image', 'song_cover_preview', array(
	'label' => 'Song Cover Preview',
	'src' => $path,
	'width' => 100,
	'height' => 100,
      ));
    }
    if ($albumsong->song_cover) {
      $this->addElement('Checkbox', 'remove_song_cover', array(
          'label' => 'Yes, remove song cover.'
      ));
    }

    //Init album art
    $this->addElement('File', 'file', array(
        'label' => 'Song Main Photo',
        'onchange' => 'showReadImage(this,"song_mainphoto_preview")',
    ));

    $this->file->addValidator('Extension', false, 'jpg,png,gif,jpeg');
    if ($albumsong_id && $albumsong && $albumsong->photo_id) {
      $img_path = Engine_Api::_()->storage()->get($albumsong->photo_id, '')->getPhotoUrl();
      $path = $img_path;
      if (isset($path) && !empty($path)) {
	$this->addElement('Image', 'song_mainphoto_preview', array(
	    'label' => 'Song Main Photo Preview',
	    'src' => $path,
	    'width' => 100,
	    'height' => 100,
	));
      }
    } else {
      $this->addElement('Image', 'song_mainphoto_preview', array(
	'label' => 'Song Main Photo Preview',
	'src' => $path,
	'width' => 100,
	'height' => 100,
      ));
    }
    if ($albumsong->photo_id) {
      $this->addElement('Checkbox', 'remove_photo', array(
          'label' => 'Yes, remove song photo.'
      ));
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $downloadAlbumSong = Engine_Api::_()->authorization()->isAllowed('sesmusic_album', $viewer, 'download_albumsong');
    if ($downloadAlbumSong) {
      $this->addElement('Checkbox', 'download', array(
          'label' => 'Do you want to download this song?',
          'value' => 1,
      ));
    }

    //Element: execute
    $this->addElement('Button', 'execute', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true,
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
        'label' => 'cancel',
        'link' => true,
        'prependText' => ' or ',
        'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'view', 'albumsong_id' => $albumsong_id, 'slug' => $albumsong->getSlug()), 'sesmusic_albumsong_view', true),
        'onclick' => '',
        'decorators' => array(
            'ViewHelper',
        ),
    ));

    // DisplayGroup: buttons
    $this->addDisplayGroup(array(
        'execute',
        'cancel',
            ), 'buttons', array(
        'decorators' => array(
            'FormElements',
            'DivDivDivWrapper'
        ),
    ));
  }

}