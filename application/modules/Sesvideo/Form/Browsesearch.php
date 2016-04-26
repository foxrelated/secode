<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Browsesearch.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
 
class Sesvideo_Form_Browsesearch extends Engine_Form {
  protected $_searchTitle;
	protected $_searchFor;
	protected $_browseBy;
	protected $_categoriesSearch;
	protected $_locationSearch;
	protected $_kilometerMiles;
	protected $_friendsSearch;
	public function setFriendsSearch($title) {
    $this->_friendsSearch = $title;
    return $this;
  }
  public function getFriendsSearch() {
    return $this->_friendsSearch;
  }	
  public function setSearchTitle($title) {
    $this->_searchTitle = $title;
    return $this;
  }

  public function getSearchTitle() {
    return $this->_searchTitle;
  }
	public function setSearchFor($title) {
    $this->_searchFor = $title;
    return $this;
  }
  public function getSearchFor() {
    return $this->_searchFor;
  }
 public function setBrowseBy($title) {
    $this->_browseBy = $title;
    return $this;
  }
  public function getBrowseBy() {
    return $this->_browseBy;
  }
	public function setCategoriesSearch($title) {
    $this->_categoriesSearch = $title;
    return $this;
  }
  public function getCategoriesSearch() {
    return $this->_categoriesSearch;
  }	 
 public function setLocationSearch($title) {
    $this->_locationSearch = $title;
    return $this;
  }
  public function getLocationSearch() {
    return $this->_locationSearch;
  }

	public function setKilometerMiles($title) {
    $this->_kilometerMiles = $title;
    return $this;
  }
  public function getKilometerMiles() {
    return $this->_kilometerMiles;
  }
  public function init() {
    parent::init();
		$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
		if($this->getSearchFor() == 'video')
			$searchFor =$view->url(array('controller' => 'index', 'action' => 'browse','module'=>'sesvideo'), 'sesvideo_general', true);
		else if($this->getSearchFor() == 'chanel')
			$searchFor = $view->url(array('controller' => 'chanel', 'action' => 'browse','module'=>'sesvideo'), 'sesvideo_chanel', true);		
    $this
            ->setAttribs(array(
                'id' => 'filter_form',
                'class' => 'global_form_box',
            ))
            ->setAction($searchFor);

    if ($this->_searchTitle == 'yes') {
			 $valueD = Zend_Registry::get('Zend_Translate')->_('Search %s / Keyword:');
			 $valueD = sprintf($valueD,ucwords($this->getSearchFor()).'s');
      $this->addElement('Text', 'search', array(
          'label' => $valueD
      ));
    }
    if ($this->_browseBy == 'yes') {
    $this->addElement('Select', 'sort', array(
        'label' => 'Browse By:',
        'multiOptions' => array(''),
    ));
		}
		if ($this->_friendsSearch == 'yes' && Engine_Api::_()->user()->getViewer()->getIdentity() != 0) {
      $this->addElement('Select', 'show', array(
          'label' => 'View:',
          'multiOptions' => array(
              '1' => 'Everyone\'s '.ucwords($this->getSearchFor()).'s',
              '2' => 'Only My Friend\'s '.ucwords($this->getSearchFor()).'s',
          ),
      ));
    }
		 if ($this->_categoriesSearch == 'yes') {
    // prepare categories
    $categories = Engine_Api::_()->getDbtable('categories', 'sesvideo')->getCategoriesAssoc();
    if (count($categories) > 0) {
			$categories = array('0'=>'')+$categories;
      $this->addElement('Select', 'category_id', array(
          'label' => 'Category:',
          'multiOptions' => $categories,
					'onchange' => "showSubCategory(this.value);",
      ));
			//Add Element: Sub Category
      $this->addElement('Select', 'subcat_id', array(
          'label' => "2nd-level Category:",
          'allowEmpty' => true,
          'required' => false,
					'multiOptions' => array('0'=>''),
          'registerInArrayValidator' => false,
          'onchange' => "showSubSubCategory(this.value);"
      ));			
      //Add Element: Sub Sub Category
      $this->addElement('Select', 'subsubcat_id', array(
          'label' => "3rd-level Category:",
          'allowEmpty' => true,
          'registerInArrayValidator' => false,
          'required' => false,
					'multiOptions' => array('0'=>''),
      ));
    }
		 }
		  if ($this->_locationSearch == 'yes' && $this->getSearchFor() == 'video') {
		/*Location Elements*/
		$this->addElement('Text', 'location', array(
        'label' => 'Location:',
				'id' =>'locationSesList',
        'filters' => array(
            new Engine_Filter_Censor(),
            new Engine_Filter_HtmlSpecialChars(),
        ),
    ));
		$this->addElement('Text', 'lat', array(
        'label' => 'Lat:',
				'id' =>'latSesList',
        'filters' => array(
            new Engine_Filter_Censor(),
            new Engine_Filter_HtmlSpecialChars(),
        ),
    ));
		$this->addElement('Text', 'lng', array(
        'label' => 'Lng:',
				'id' =>'lngSesList',
        'filters' => array(
            new Engine_Filter_Censor(),
            new Engine_Filter_HtmlSpecialChars(),
        ),
    ));
	
	 if ($this->_kilometerMiles == 'yes') {
		if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sesvideo.search.type',1) == 1){
			$searchType = 'Miles:';
		}else
			$searchType = 'Kilometer:';
		//Add Element: Sub Category
      $this->addElement('Select', 'miles', array(
          'label' => $searchType,
          'allowEmpty' => true,
          'required' => false,
					'multiOptions' => array('0'=>'','1'=>'1','5'=>'5','10'=>'10','20'=>'20','50'=>'50','100'=>'100','200'=>'200','500'=>'500','1000'=>'1000'),
					'value'=>'1000',
          'registerInArrayValidator' => false,
      ));
	 }
}
    $this->addElement('Button', 'submit', array(
        'label' => 'Search',
        'type' => 'submit'
    ));
		$this->addElement('Dummy','loading-img-sesvideo', array(
        'content' => '<img src="application/modules/Core/externals/images/loading.gif" id="sesvideo-category-widget-img" alt="Loading" />',
   ));
  }

}
