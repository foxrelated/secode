<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedslideshow
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Edit.php 2011-10-22 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Advancedslideshow_Form_Admin_Slides_Edit extends Engine_Form {

  protected $_field;
  protected $_item;

  public function getItem() {
    return $this->_item;
  }

  public function setItem($item) {
    $this->_item = $item;
    return $this;
  }

  public function init() {
    $this->setTitle('Edit Slide Details')
            ->setDescription('Edit the details of your slide here.');
    
    $advancedslideshow_id = $this->getItem();

    //GET MODEL FROM engine4_advancedslideshows TABLE
    $advancedslideshow = Engine_Api::_()->getItem('advancedslideshow', $advancedslideshow_id);
    
    $imageId = Zend_Controller_Front::getInstance()->getRequest()->getParam('image_id', null);
    $image = Engine_Api::_()->getItem('advancedslideshow_image', $imageId);
    
    if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestore') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreproduct') && $advancedslideshow->resource_type == 'sitestoreproduct_category' && !empty($advancedslideshow->resource_id)) {
      $countSubCategories = Engine_Api::_()->getDbTable('categories', 'sitestoreproduct')->getSubCategories($advancedslideshow->resource_id, 1);
      if($countSubCategories > 0) {
        
        $value = 1;
        if($image->url) {
          $value = 0;
        }
        
        $this->addElement('Radio', 'url_type', array(
            'label' => 'Slide URL',
            'multiOptions' => array(
                1 => 'Product Category URL (On clicking, users will be redirected to the Browse Products page with category chosen in below fields as search criteria.)',
                0 => 'Custom URL (On clicking, users will be redirected to the URL entered in the below field.)',
            ),
            'value' => $value,
            'onClick' => 'showCategoryOption(this.value)',
        ));
        
        $params = array();
        $params['url'] = $image->url;
        if(!empty($image->params)) {
          $params = Zend_Json::decode($image->params);
        }

        $params['category_id'] = $advancedslideshow->resource_id;
        
        if(!isset($params['subcategory_id'])) {
          $params['subcategory_id'] = 0;
        }      
        
        if(!isset($params['subsubcategory_id'])) {
          $params['subsubcategory_id'] = 0;
        }   
        
        $params['subcategory_name'] = '';
        if($params['subsubcategory_id']) {
          $params['subcategory_name'] = Engine_Api::_()->getDbtable('categories', 'sitestoreproduct')->getCategory($params['subsubcategory_id'])->category_name;
        }      

        $categories = Engine_Api::_()->getDbTable('categories', 'sitestoreproduct')->getCategories(null, 0, 0, 1);
        if (count($categories) != 0) {
          $categories_prepared[0] = "";
          foreach ($categories as $category) {
            $categories_prepared[$category->category_id] = $category->category_name;
          }

          $this->addElement('Select', 'category_id', array(
              'label' => 'Category',
              'multiOptions' => $categories_prepared,
              'onchange' => "subcategories(this.value, '', '');",
              'value' => $params['category_id'],
          ));

          $this->addElement('Select', 'subcategory_id', array(
              'RegisterInArrayValidator' => false,
              'allowEmpty' => true,
              'required' => false,
          ));

          $this->addElement('Select', 'subsubcategory_id', array(
              'RegisterInArrayValidator' => false,
              'allowEmpty' => true,
              'required' => false,
          ));

          $this->addDisplayGroup(array(
              'subcategory_id',
              'subsubcategory_id',
                  ), 'Select', array(
              'decorators' => array(array('ViewScript', array(
                          'viewScript' => 'application/modules/Sitestoreproduct/views/scripts/_formSubSubcategory.tpl',
                          'params' => $params,
                          'class' => 'form element')))
          ));
        }   
      }
    }

    $this->addElement('Text', 'url', array(
        'label' => 'Custom URL', 'style' => 'width:200px;',
        'filters' => array(
            array('PregReplace', array('/\s*[a-zA-Z0-9]{2,5}:\/\//', '')),
        )
    ));

    $filter = new Engine_Filter_Html();
    $this->addElement('TinyMce', 'caption', array(
        'label' => 'Slide Caption',
        'required' => false,
        'editorOptions' => array(
            'bbcode' => 1,
            'html' => 1
        ),
        'allowEmpty' => true,
        'filters' => array(
            $filter,
            new Engine_Filter_Censor(),
        )
    ));

    if (!empty($advancedslideshow->level)) {
      //PREPARE LEVELS
      $levels = Engine_Api::_()->getDbtable('levels', 'authorization')->fetchAll();
      foreach ($levels as $level) {
        $levels_prepared[$level->getIdentity()] = $level->getTitle();
      }
      reset($levels_prepared);
      $this->addElement('Multiselect', 'slide_levels', array(
          'label' => 'Member Levels',
          'description' => 'Specify which member levels will be shown this slide. To show this slide to all member levels, leave them all selected. Use CTRL-click to select or deselect multiple levels.',
          'multiOptions' => $levels_prepared,
          'value' => key($levels_prepared),
      ));
    }

    if (!empty($advancedslideshow->network)) {
      //PREPARE NETWORKS
      $networks = Engine_Api::_()->getDbtable('networks', 'network')->fetchAll();

      if (count($networks) > 0) {
        foreach ($networks as $network) {
          $networks_prepared[$network->getIdentity()] = $network->getTitle();
        }
        reset($networks_prepared);

        $this->addElement('Multiselect', 'slide_networks', array(
            'label' => 'Networks',
            'description' => 'Specify which networks will be shown this slide. To show this slide to all networks, leave them all selected. Use CTRL-click to select or deselect multiple networks.',
            'multiOptions' => $networks_prepared,
            'value' => key($networks_prepared)
        ));
      }
    }

    if (empty($advancedslideshow->level)) {
      $this->addElement('Checkbox', 'show_public', array(
          'label' => 'Show this slide to visitors who are not logged in.',
          'description' => 'Slide Visibility for Visitors',
          'value' => 1
      ));
    }

    $id = new Zend_Form_Element_Hidden('id');

    $this->addElements(array(
        $id,
    ));

    $this->addElement('Button', 'submit', array(
        'label' => 'Save',
        'type' => 'submit',
        'ignore' => true,
    ));
  }

  public function setField($stat) {
    $this->_field = $stat;

    if (!empty($stat['url']))
      $this->url->setValue($stat['url']);

    if (!empty($stat['caption']))
      $this->caption->setValue($stat['caption']);

    $this->submit->setLabel('Save');
  }

}
?>
