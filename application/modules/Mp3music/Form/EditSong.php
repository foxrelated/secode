<?php
class Mp3music_Form_EditSong extends Engine_Form
{
	public $song;
	public function init()
	{
		// Init form
		$this -> setTitle('Edit song information') -> setAttrib('id', 'form-edit-song') -> setAttrib('name', 'mp3music_edit_song') -> setAttrib('class', '') -> setAction(Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('format' => 'smoothbox'), 'mp3music_edit_song'));
		$song_id = Zend_Controller_Front::getInstance() -> getRequest() -> getParam('song_id');
		$song_info = Engine_Api::_() -> getItem('mp3music_album_song', $song_id);
		//category
		$cats = Mp3music_Model_Cat::getCats(0);
		$catPerms = array();
		$catPerms[0] = "";
		foreach ($cats as $cat)
		{
			$catPerms[$cat -> cat_id] = $cat -> title;
			$subCats = Mp3music_Model_Cat::getCats($cat -> cat_id);
			foreach ($subCats as $subCat)
			{
				$catPerms[$subCat -> cat_id] = "-- " . $subCat -> title;
			}
		}
		$this -> addElement('Select', 'music_categorie_id', array(
			'label' => 'Category',
			'multiOptions' => $catPerms,
			'style' => 'width:100%; margin: 5px 0 10px;',
			'value' => $song_info -> cat_id
		));
		$this -> addElement('Hidden', 'song_id', array('value' => $song_info -> song_id, ));
		$user = Engine_Api::_() -> user() -> getViewer();
		$selling_settings = Mp3music_Api_Cart::getSettingsSelling($user -> level_id);
		if ($selling_settings['can_sell_song'] == 1)
		{
			//Song price
			$this -> addElement('Text', 'price', array(
				'label' => 'Price (USD)',
				'style' => '',
				'value' => $song_info -> price,
				'style' => 'margin: 5px 0 10px;',
				'filters' => array(new Engine_Filter_Censor(), ),
			));
		}
		//Song title
		$this -> addElement('Text', 'title', array(
			'label' => 'Song Title',
			'style' => '',
			'value' => htmlspecialchars_decode($song_info -> title),
			'style' => 'width:100%; margin: 5px 0 10px;',
			'filters' => array(new Engine_Filter_Censor(), 'StripTags'),
		));
		//artist
		$allow_artist = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('mp3music.artist', 1);
		if (!$allow_artist)
		{
			$artists = Mp3music_Api_Core::getArtistRows();
			$artPerms = array();
			$artPerms[0] = "";
			foreach ($artists as $artist)
			{
				$artPerms[$artist -> artist_id] = $artist -> title;
			}
			$this -> addElement('Select', 'music_artist_id', array(
				'label' => 'Artist',
				'multiOptions' => $artPerms,
				'style' => "width:100%",
				'value' => $song_info -> artist_id
			));
		}
		//singger
		$singerTypes = Mp3music_Model_SingerType::getSingerTypes();
		$singerPerms = array();
		$singerPerms[0] = "Other Singer";
		foreach ($singerTypes as $singerType)
		{
			$singers = $singerType -> getSingers();
			foreach ($singers as $singer)
			{
				$singerPerms[$singer -> singer_id] = "" . $singer -> title;
			}
		}
		$this -> addElement('Select', 'music_singer_id', array(
			'label' => 'Singer',
			'multiOptions' => $singerPerms,
			'style' => 'width:100%; margin: 5px 0 10px;',
			'onchange' => "updateTextFieldSingers()",
			'value' => $song_info -> singer_id
		));
		// Init Orther singer field
		$this -> addElement('Text', 'other_singer', array(
			'label' => 'Other Singer Name',
			'style' => 'margin: 5px 0 10px;',
			'value' => $song_info -> other_singer,
			'filters' => array(new Engine_Filter_Censor(), ),
		));

		// Init Lyric
		if ($song_info -> lyric == "")
		{
			$song_info -> lyric = "No Lyric!";
		}
		$this -> addElement('TinyMce', 'lyric', array(
			'disableLoadDefaultDecorators' => true,
			'value' => $song_info -> lyric,
			'decorators' => array('ViewHelper'),
			'filters' => array(
        new Engine_Filter_Censor(),
        new Engine_Filter_Html(array('AllowedTags'=>"strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr"))),
		));
		$this -> addElement('Button', 'submit', array(
			'label' => 'Save Changes',
			'type' => 'submit',
			'ignore' => true,
			'style' => 'margin: 5px 0 10px;',
			'decorators' => array('ViewHelper', ),
		));
		$this -> addElement('Cancel', 'cancel', array(
			'label' => 'cancel',
			'link' => true,
			'prependText' => ' or ',
			'decorators' => array('ViewHelper', ),
		));
	}

	public function saveValues()
	{
		$translate = Zend_Registry::get('Zend_Translate');
		$values = $this -> getValues();
		$song = Engine_Api::_() -> getItem('mp3music_album_song', $values['song_id']);
		$song -> title = htmlspecialchars($values['title']);
		if (empty($values['title']))
		{
			$song -> title = $translate -> _('_MUSIC_UNTITLED_ALBUM');
		}
		$str = $song -> title;
		$str = strtolower($str);
		$str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", "a", $str);
		$str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", "e", $str);
		$str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", "i", $str);
		$str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", "o", $str);
		$str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", "u", $str);
		$str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", "y", $str);
		$str = preg_replace("/(đ)/", "d", $str);
		$str = preg_replace("/(!|@|%|\^|\*|\(|\)|\+|\=|\<|\>|\?|\/|,|\.|\:|\;|\'| |\"|\&|\#|\[|\]|~|$|_)/", "-", $str);
		$str = preg_replace("/(-+-)/", "-", $str);
		//thay thế 2- thành 1-
		$str = preg_replace("/(^\-+|\-+$)/", "", $str);
		$str = preg_replace("/(-)/", " ", $str);
		$song -> title_url = $str;
		$song -> cat_id = $values['music_categorie_id'];
		$allow_artist = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('mp3music.artist', 1);
		if (!$allow_artist)
			$song -> artist_id = $values['music_artist_id'];
		$user = Engine_Api::_() -> user() -> getViewer();
		$selling_settings = Mp3music_Api_Cart::getSettingsSelling($user -> level_id);
		if (isset($values['price']))
		{
			if (!is_numeric($values['price']))
			{
				$this -> getElement('price') -> addError('The price number is invalid! (Ex: 2000.25)');
				return false;
			}
			if (round($values['price'], 2) < $selling_settings['min_price_song'] && round($values['price'], 2) != 0)
			{
				$this -> getElement('price') -> addError('Song price is must larger than or equal to ' . $selling_settings['min_price_song'] . ' USD');
				return false;
			}
			$song -> price = $values['price'];
		}

		$song -> singer_id = $values['music_singer_id'];
		if ($song -> singer_id == 0)
		{
			$song -> other_singer = $values['other_singer'];
			$str = $song -> other_singer;
			$str = strtolower($str);
			$str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", "a", $str);
			$str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", "e", $str);
			$str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", "i", $str);
			$str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", "o", $str);
			$str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", "u", $str);
			$str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", "y", $str);
			$str = preg_replace("/(đ)/", "d", $str);
			$str = preg_replace("/(!|@|%|\^|\*|\(|\)|\+|\=|\<|\>|\?|\/|,|\.|\:|\;|\'| |\"|\&|\#|\[|\]|~|$|_)/", "-", $str);
			$str = preg_replace("/(-+-)/", "-", $str);
			//thay thế 2- thành 1-
			$str = preg_replace("/(^\-+|\-+$)/", "", $str);
			$str = preg_replace("/(-)/", " ", $str);
			$song -> other_singer_title_url = $str;
		}
		$song -> lyric = $values['lyric'];
		$song -> save();
		return $song -> price;
	}

}
