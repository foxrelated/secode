<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: Filterchanel.php 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

class Sesvideo_Form_Admin_Manage_Filterchanel extends Engine_Form {

  public function init() {

    parent::init();

    $this->clearDecorators()
            ->addDecorator('FormElements')
            ->addDecorator('Form')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
            ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'));

    $this->setAttribs(array(
                'id' => 'filter_form',
                'class' => 'global_form_box',
            ))
            ->setMethod('GET');

    $titlename = new Zend_Form_Element_Text('title');
    $titlename
            ->setLabel('Channel Title')
            ->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
            ->addDecorator('HtmlTag', array('tag' => 'div'));


    $owner_name = new Zend_Form_Element_Text('owner_name');
    $owner_name
            ->setLabel('Owner Name')
            ->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
            ->addDecorator('HtmlTag', array('tag' => 'div'));

    $is_featured = new Zend_Form_Element_Select('is_featured');
    $is_featured
            ->setLabel('Featured')
            ->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
            ->addDecorator('HtmlTag', array('tag' => 'div'))
            ->setMultiOptions(array(
                '' => '',
                '1' => 'Yes',
                '0' => 'No',
            ))
            ->setValue('');

    $offtheday = new Zend_Form_Element_Select('offtheday');
    $offtheday
            ->setLabel('Of The Day')
            ->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
            ->addDecorator('HtmlTag', array('tag' => 'div'))
            ->setMultiOptions(array(
                '' => '',
                '1' => 'Yes',
                '0' => 'No',
            ))
            ->setValue('');
    $is_sponsored = new Zend_Form_Element_Select('is_sponsored');
    $is_sponsored
            ->setLabel('Sponsored')
            ->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
            ->addDecorator('HtmlTag', array('tag' => 'div'))
            ->setMultiOptions(array(
                '' => '',
                '1' => 'Yes',
                '0' => 'No',
            ))
            ->setValue('');

    $is_hot = new Zend_Form_Element_Select('is_hot');
    $is_hot
            ->setLabel('Hot')
            ->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
            ->addDecorator('HtmlTag', array('tag' => 'div'))
            ->setMultiOptions(array(
                '' => '',
                '1' => 'Yes',
                '0' => 'No',
            ))
            ->setValue('');
    $is_verified = new Zend_Form_Element_Select('is_verified');
    $is_verified
            ->setLabel('Verified')
            ->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
            ->addDecorator('HtmlTag', array('tag' => 'div'))
            ->setMultiOptions(array(
                '' => '',
                '1' => 'Yes',
                '0' => 'No',
            ))
            ->setValue('');

    $rating = new Zend_Form_Element_Select('rating');
    $rating
            ->setLabel('Rated')
            ->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
            ->addDecorator('HtmlTag', array('tag' => 'div'))
            ->setMultiOptions(array(
                '' => '',
                '1' => 'Yes',
                '0' => 'No',
            ))
            ->setValue('');

    $date = new Zend_Form_Element_Text('creation_date');
    $date
            ->setLabel('Creation Date: ex (2000-12-01)')
            ->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
            ->addDecorator('HtmlTag', array('tag' => 'div'));

    // prepare categories
    $categories = Engine_Api::_()->getDbtable('categories', 'sesvideo')->getCategory();

    if (count($categories) != 0) {
      $categories_prepared[''] = "";
      foreach ($categories as $category) {
        $categories_prepared[$category->category_id] = $category->category_name;
      }

      // category field
      $category = new Zend_Form_Element_Select('category_id', array('onchange' => 'showSubCategory(this.value)'));
      $category
              ->setLabel('Category')
              ->clearDecorators()
              ->setMultiOptions($categories_prepared)
              ->addDecorator('ViewHelper')
              ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
              ->addDecorator('HtmlTag', array('tag' => 'div'));

      //Add Element: Sub Category

      $subCategory = new Zend_Form_Element_Select('subcat_id', array('onchange' => 'showSubSubCategory(this.value)'));
      $subCategory
              ->setLabel('2nd-level Category')
              ->clearDecorators()
              ->setMultiOptions(array('0' => ''))
              ->addDecorator('ViewHelper')
              ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
              ->addDecorator('HtmlTag', array('tag' => 'div'));
      //Add Element: Sub Sub Category
      $subsubCategory = new Zend_Form_Element_Select('subsubcat_id');
      $subsubCategory
              ->setLabel('3rd-level Category')
              ->clearDecorators()
              ->setMultiOptions(array('0' => ''))
              ->addDecorator('ViewHelper')
              ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
              ->addDecorator('HtmlTag', array('tag' => 'div'));
    }
    $submit = new Zend_Form_Element_Button('search', array('type' => 'submit'));
    $submit
            ->setLabel('Search')
            ->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons'))
            ->addDecorator('HtmlTag2', array('tag' => 'div'));

    $arrayItem = array();
    $arrayItem = !empty($titlename) ? array_merge($arrayItem, array($titlename)) : '';
    $arrayItem = !empty($owner_name) ? array_merge($arrayItem, array($owner_name)) : $arrayItem;
    $arrayItem = !empty($is_featured) ? array_merge($arrayItem, array($is_featured)) : $arrayItem;
    $arrayItem = !empty($is_sponsored) ? array_merge($arrayItem, array($is_sponsored)) : $arrayItem;
    $arrayItem = !empty($is_hot) ? array_merge($arrayItem, array($is_hot)) : $arrayItem;
    $arrayItem = !empty($is_verified) ? array_merge($arrayItem, array($is_verified)) : $arrayItem;
    $arrayItem = !empty($offtheday) ? array_merge($arrayItem, array($offtheday)) : $arrayItem;
    $arrayItem = !empty($rating) ? array_merge($arrayItem, array($rating)) : $arrayItem;
    $arrayItem = !empty($date) ? array_merge($arrayItem, array($date)) : $arrayItem;
    $arrayItem = !empty($category) ? array_merge($arrayItem, array($category)) : $arrayItem;
    $arrayItem = !empty($subCategory) ? array_merge($arrayItem, array($subCategory)) : $arrayItem;
    $arrayItem = !empty($subsubCategory) ? array_merge($arrayItem, array($subsubCategory)) : $arrayItem;
    $arrayItem = !empty($submit) ? array_merge($arrayItem, array($submit)) : '';
    $this->addElements($arrayItem);
  }

}
