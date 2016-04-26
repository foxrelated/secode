<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Searchchanel.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesvideo_Form_Searchchanel extends Engine_Form {

  protected $_optionTitle;

  public function setOptionTitle($title) {
    $this->_optionTitle = $title;
    return $this;
  }

  public function getOptionTitle() {
    return $this->_optionTitle;
  }

  public function init() {
    $this
            ->setAttribs(array(
                'id' => 'filter_form',
                'class' => 'global_form_box',
            ))
            ->setMethod('GET')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('module' => 'sesvideo', 'controller' => 'chanel', 'action' => 'category')))
    ;

    $this->addElement('Text', 'text', array(
        'label' => 'Search',
    ));

    // prepare categories
    $categories = Engine_Api::_()->sesvideo()->getCategories();
    if (count($categories) > 0) {
      $categories_prepared[0] = "";
      foreach ($categories as $category) {
        $categories_prepared[$category->category_id] = $category->category_name;
      }
      // category field
      $this->addElement('Select', 'category_id', array(
          'label' => 'Category',
          'multiOptions' => $categories_prepared,
          'onchange' => 'showSubCategory(this.value);'
      ));
      //Add Element: Sub Category
      $this->addElement('Select', 'subcat_id', array(
          'label' => "2nd-level Category",
          'allowEmpty' => true,
          'required' => false,
          'multiOptions' => array('0' => 'Please select sub category'),
          'registerInArrayValidator' => false,
          'onchange' => "showSubSubCategory(this.value);"
      ));
      //Add Element: Sub Sub Category
      $this->addElement('Select', 'subsubcat_id', array(
          'label' => "3rd-level Category",
          'allowEmpty' => true,
          'registerInArrayValidator' => false,
          'required' => false,
          'multiOptions' => array('0' => 'Please select 3rd category'),
      ));
    }
    $this->addElement('Hidden', 'tag');

    $this->addElement('Select', 'orderby', array(
        'label' => 'Browse By',
        'multiOptions' => $this->getOptionTitle(),
    ));
    $this->addElement('Button', 'submit', array(
        'label' => 'Search',
        'type' => 'submit'
    ));
  }

}
