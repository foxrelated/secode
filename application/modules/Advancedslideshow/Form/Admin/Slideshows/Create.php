<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedslideshow
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Create.php 2011-10-22 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Advancedslideshow_Form_Admin_Slideshows_Create extends Engine_Form {

  protected $_SlideType;

  public function getSlidetype() {
    return $this->_SlideType;
  }

  public function setSlidetype($slide_type) {
    $this->_SlideType = $slide_type;
    return $this;
  }

  public function init() {
    $this
            ->setTitle('Create New Slideshow')
            ->setDescription("Create a new Slideshow here. Below, you will be able to choose the page for which you want to create the Slideshow, and the corresponding widget position for it. For widgetized pages, you will be able to adjust the vertical position of the Slideshow widget from the Layout Editor. For non-widgetized pages, you will need to place the generated code at the appropriate place in the template file of desired page. Below you will also be able to configure and customize your slideshow based on various parameters. Visit the Demo tab to see the demo of the various slideshow types.");

    $this->addElement('Select', 'slideshow_type', array(
        'label' => 'Slideshow Type',
        'onchange' => 'javascript:slideshowparam(slideshow_type.value, widget_page.value);',
        'multiOptions' => array(
            'fadd' => 'Fading',
            'flom' => 'Curtain / Blind',
            'zndp' => 'Zooming & Panning',
            'push' => 'Push',
            'flas' => 'Flash',
            'fold' => 'Fold',
            'noob' => 'HTML Slides with Bullet Navigation'
        ),
    ));

    if ($this->_SlideType == "noob") {
      $this->addElement('Select', 'noob_effect', array(
          'label' => 'Transition Effects',
          'description' => 'Select the transition effect for this slideshow.',
          'multiOptions' => array(
              'simple' => 'Simple',
              'bounced' => 'Bounce',
              'elastic' => 'Elastic'
          ),
      ));
    }

    $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
    $pageList = $pageTable->fetchAll();
    $widgetName = array();
    $count = 0;
    foreach ($pageList as $page) {
       if (strpos($page->name, 'mobi') !== false || strpos($page->name, 'sitemobile') !== false) {
         continue;
        }
      //if ($page->name != 'header' && $page->name != 'footer') {
      $widgetName[$page->page_id] = $page->displayname;
      if ($count == 0) {
        $current_page = $page->page_id;
      }
      $count++;
      //}
    }

    $widgetName[-1] = 'Non-Widgetized Page';
    $this->addElement('Select', 'widget_page', array(
        'label' => 'Site Page / Location',
        'description' => 'Select the site page / location that you want to be associated to this Slideshow. (Note: You will be able to vertically adjust the position of the widget on this page / location from the Layout Editor. You are advised not to move the widget horizontally from the Layout Editor.)',
        'onchange' => 'javascript:slideshowparam(slideshow_type.value, widget_page.value);',
        'multiOptions' => $widgetName
    ));

    $page_value = Zend_Controller_Front::getInstance()->getRequest()->getParam('widget', null);
    if (!empty($page_value)) {
      $page_id = $page_value;
    } else {
      $page_id = $current_page;
    }

    $contentTable = Engine_Api::_()->getDbtable('content', 'core');
    $select = $contentTable->select()
            ->from($contentTable->info('name'), 'name')
            ->where('page_id = ?', $page_id);
    $contentValues = $contentTable->fetchAll($select)->toArray();
    $contentArray = array();
    foreach ($contentValues as $key => $name) {
      $contentArray[] = $name['name'];
    }

    $widget_position = array();
    if ((in_array("top", $contentArray) || in_array("bottom", $contentArray)) || ((!in_array("left", $contentArray)) && (!in_array("right", $contentArray)) && in_array("middle", $contentArray))) {
      $widget_position['full_width1'] = "Full Width Slideshow - Widget 1";
      $widget_position['full_width2'] = "Full Width Slideshow - Widget 2";

      if (in_array("top", $contentArray)) {
        $position = 'top';
      } elseif (in_array("bottom", $contentArray)) {
        $position = 'bottom';
      }

      $this->addElement('hidden', 'fullwidth_position', array(
          'value' => $position
      ));
    }
    if (in_array("right", $contentArray) && in_array("left", $contentArray)) {
      $widget_position['middle_column1'] = "Middle Column Slideshow - Widget 1";
      $widget_position['middle_column2'] = "Middle Column Slideshow - Widget 2";
    }
    if (in_array("right", $contentArray)) {
      $widget_position['right_column1'] = "Right Column Slideshow - Widget 1";
      $widget_position['right_column2'] = "Right Column Slideshow - Widget 2";
    }
    if (in_array("left", $contentArray)) {
      $widget_position['left_column1'] = "Left Column Slideshow - Widget 1";
      $widget_position['left_column2'] = "Left Column Slideshow - Widget 2";
    }
    if ((in_array("left", $contentArray) && (!in_array("right", $contentArray))) || (in_array("right", $contentArray) && (!in_array("left", $contentArray)))) {
      $widget_position['extreme1'] = "Extended Right/Left Slideshow - Widget 1";
      $widget_position['extreme2'] = "Extended Right/Left Slideshow - Widget 2";
    }

    if ($page_id == -1) {
      $widget_position['full_width3'] = "Non-Widgetized Page: Full Width Slideshow";
      $widget_position['middle_column3'] = "Non-Widgetized Page: Middle Column Slideshow";
      $widget_position['right_column3'] = "Non-Widgetized Page: Right Column Slideshow";
      $widget_position['left_column3'] = "Non-Widgetized Page: Left Column Slideshow";
      $widget_position['extreme3'] = "Non-Widgetized Page: Extended Right/Left Slideshow";
    } elseif ($page_id == 1) {
      $widget_position['full_width4'] = "Site Header - Full Width Slideshow";
    } elseif ($page_id == 2) {
      $widget_position['full_width5'] = "Site Footer - Full Width Slideshow";
    }

    $tableSlideshow = Engine_Api::_()->getDbTable('advancedslideshows', 'advancedslideshow');
    $rName = $tableSlideshow->info('name');
    $select = $tableSlideshow->select()
            ->from($rName, 'widget_position')
            ->where('widget_page = ?', $page_id);
    $positionValues = $tableSlideshow->fetchAll($select)->toArray();
    $positionValuesArray = array();
    foreach ($positionValues as $key => $name) {
      $positionValuesArray[] = $name['widget_position'];
    }
    $flipPositionArray = array_flip($positionValuesArray);

    $widget_position = array_diff_key($widget_position, $flipPositionArray);

    if (!empty($widget_position)) {
      $this->addElement('Select', 'widget_position', array(
          'label' => 'Slideshow Widget Block',
          'description' => 'Select a widget block to be placed on the above selected page / location. (Note: This widget block will be automatically placed on the above selected widgetized page / location. You will be able to vertically adjust the position of this widget on this page / location from the Layout Editor. You are advised not to move the widget horizontally from the Layout Editor.)',
          'onchange' => 'javascript:slideshowWidthHeight(this.value);',
          'multiOptions' => $widget_position));
    } else {
      $this->addElement('Select', 'widget_position', array(
          'label' => 'Slideshow Widget',
          'description' => 'You have already created maximum number of slideshows for this "Widgetized Page". You can not create more !',
          'disabled' => 'disabled',
          'multiOptions' => $widget_position));
    }

    $preview_link = Zend_Registry::get('Zend_Translate')->_('Click ') . '<a href="javascript:void(0);" onclick="showPositions()">' . Zend_Registry::get('Zend_Translate')->_('here') . '</a>' . Zend_Registry::get('Zend_Translate')->_(' to view the Position of this Block.');

    $this->addElement('Dummy', 'slideshow_widget_preview', array(
        'label' => 'Block Position',
        'description' => $preview_link,
    ));
    $this->getElement('slideshow_widget_preview')->getDecorator('Description')->setOptions(array('placement', 'APPEND', 'escape' => false));

    $this->addElement('Hidden', 'slideshow_widget_preview1', array(
            //'value' => $img_src,
    ));

    $this->addElement('Text', 'widget_title', array(
        'label' => 'Slideshow Name',
        'description' => 'Enter the name of this Slideshow. This name is only for your indicative purpose, and will not be displayed to users.',
        'required' => 'true',
        'maxLength' => 128,
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
            )));


    $this->addElement('Select', 'transition', array(
        'label' => 'Transition type',
        'multiOptions' => array(
            'linear' => 'Linear',
            'Quad' => 'Quadratic',
            'Cubic' => 'Cubic',
            'Quart' => 'Quartic',
            'Quint' => 'Quintic',
            'Sine' => 'Sinusoidal',
            'Expo' => 'Exponential',
            'Circ' => 'Circular',
            'Bounce' => 'Bouncing',
            'Back' => 'Back',
            'Elastic' => 'Elastic'
        ),
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formtransition.tpl',
                    'class' => 'form element'
            )))
    ));


    $this->addElement('Text', 'width', array(
        'label' => 'Slideshow width (in pixels)',
        'description' => "Note: Because of a change in this setting, the widths of only the slideshow pictures uploaded after saving these settings will be affected. Please note that this setting will be applied during the slideshow picture upload to resize picture only if 'Image Resizing' has been enabled from the bottom of this form. To see the appropriate dimensions for various widget positions, please visit the FAQ section.",
        'required' => true,
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
        'value' => 523,
    ));
    $this->width->getDecorator('Description')->setOption('placement', 'append');

    $this->addElement('Text', 'height', array(
        'label' => 'Slideshow height (in pixels)',
        'description' => "Note : Because of a change in this setting, the heights of only the slideshow pictures uploaded after saving these settings will be affected. Please note that this setting will be applied during the slideshow picture upload to resize picture only if 'Image Resizing' has been enabled from the bottom of this form. To see the appropriate dimensions for various widget positions, please visit the FAQ section.",
        'required' => true,
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
        'value' => 250,
    ));
    $this->height->getDecorator('Description')->setOption('placement', 'append');

    $this->addElement('Radio', 'network', array(
        'label' => 'Network Specific Slides',
        'description' => "Do you want the slides in this slideshow to be network specific? (If you select ‘Yes’, then for each slide, you will be able to choose the network(s) in which it should be visible in the slideshow. If you select ‘No’, then all slides of this slideshow will be visible in all networks.)",
        'multioptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => 0,
    ));

    $this->addElement('Radio', 'level', array(
        'label' => 'Member Level Specific Slides',
        'description' => "Do you want the slides in this slideshow to be member level specific? (If you select ‘Yes’, then for each slide, you will be able to choose the member level(s) to which it should be visible in the slideshow. If you select ‘No’, then all slides of this slideshow will be visible to all member levels.)",
        'multioptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => 0,
    ));

    if ($this->_SlideType != "noob") {
      $description = 'Open URLs of slides in new browser window / tab.';
    } else {
      $description = 'Open URLs of slides in new browser window / tab.(Note: This setting will not apply on HTML slides created in this slideshow.)';
    }
    $this->addElement('Radio', 'target', array(
        'label' => 'Slide URLs Window',
        'description' => $description,
        'multioptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => 0,
    ));


    $this->addElement('Text', 'blinds', array(
        'label' => 'Number of blinds between transitions',
        'required' => true,
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
        'value' => 24,
    ));

    $this->addElement('Text', 'interval', array(
        'label' => 'Slideshow interval',
        'required' => true,
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
        'value' => 8000,
    ));

    $this->addElement('Radio', 'progressbar', array(
        'label' => 'Show Progress bar',
        'multioptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => 1,
    ));
    $delayValue = 2000;
    if ($this->_SlideType == "noob")
      $delayValue = 4000;
    $this->addElement('Text', 'delay', array(
        'label' => 'Delay (in millisecond)',
        'description' => 'What is the delay you want between slide changes?',
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
        'value' => $delayValue,
    ));

    $this->addElement('Text', 'duration', array(
        'label' => 'Effect duration (in millisecond)',
        'description' => 'What is the duration you want for slide effects?',
        'filters' => array(
            'StripTags',
            new Engine_Filter_Censor(),
        ),
        'value' => 750,
    ));

    $this->addElement('Radio', 'overlap', array(
        'label' => 'Overlapping of slides',
        'description' => 'Do you want slides to overlap between transitions? [Note : Non-overlapping means the first slide should transition out before the second transitions in.]',
        'multioptions' => array(
            1 => 'Yes, the slides should overlap between transitions',
            0 => 'No, the slides should not overlap between transitions'
        ),
        'value' => 1,
    ));

    $this->addElement('Radio', 'random', array(
        'label' => 'Sequential or Random slideshow',
        'description' => 'Do you want the slides in the slideshow to appear in a sequential order, or randomly? [Note : In case of random order, we suggest that you display the thumbnails for the slideshow as well.]',
        'multioptions' => array(
            0 => 'Yes, show the slides in sequential order.',
            1 => 'No, show the slides in random order.'
        ),
        'value' => 0,
    ));

//WE COMMENTED THIS CODE BECAUSE AT THE TIME OF CREATION THERE IS NO SLIDES 
// 			$this->addElement('Text', 'start_index', array(
// 				'label' => 'Slideshow starting index',
// 				'description' => 'From which slide do you want the slideshow to begin ? [Note : Enter 0 for first, 1 for second, and so on.]',
// 				'value' => 0,
// 				'disabled' => 'disabled'
// 			));
    if (!$this->_SlideType == "noob") {
      $this->addElement('Radio', 'slide_title', array(
          'label' => 'Slide titles',
          'description' => 'Do you want to use the slide captions as slide title attributes? [Note : Title attributes are the text that come upon mouseover on a slide.]',
          'multioptions' => array(
              1 => 'Yes, use the caption text for title attribute',
              0 => 'No, do not use the caption text for title attribute'
          ),
          'value' => 1,
      ));
    }

    $this->addElement('Radio', 'slide_caption', array(
        'label' => 'Captions',
        'description' => 'Do you want to show captions on the slides?',
        'multioptions' => array(
            1 => 'Yes, show captions on the slides',
            0 => 'No, do not show captions on the slides'
        ),
        'value' => 1,
    ));
    
      $captionPositionArray = array(
            0 => 'Top of the slides',
            1 => 'Bottom of the slides'
        );
    if ($this->_SlideType == "noob") {
      $captionPositionArray = array(
            0 => 'Top of the slides',
            1 => 'Bottom of the slides',
            2 => 'Left of the slides',
            3 => 'Right of the slides',
        );
    }
    $this->addElement('Radio', 'caption_position', array(
        'label' => 'Caption Positions',
        'description' => 'Choose the position for the captions on the slides.',
        'multioptions' => $captionPositionArray,
        'value' => 1,
    ));

    $this->addElement('Text', 'caption_backcolor', array(
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formImagerainbow6.tpl',
                    'class' => 'form element'
            )))
    ));

    $this->addElement('Radio', 'controller', array(
        'label' => 'Slideshow Controller',
        'description' => 'Do you want to show the slideshow controller on the slides? [Note : The slideshow controller has options like pause/play, forward, next, etc. The controller is only visible upon mouseover on the slideshow.]',
        'multioptions' => array(
            1 => 'Yes, show the controller',
            0 => 'No, do not show the controller'
        ),
        'value' => 1,
    ));

    if ($this->_SlideType == "noob") {
      $this->addElement('Radio', 'noob_autoplay', array(
          'label' => 'Auto Play Slideshow',
          'description' => 'Do you want the Slideshow to automatically start playing when page containing this slideshow is opened?',
          'multioptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'value' => 1,
      ));

      $this->addElement('Radio', 'noob_walk', array(
          'label' => 'Show Bullet Navigation',
          'description' => 'Do you want to show bullet navigation to navigate between the slides? (If you select No, then you will be able to choose to show thumbnails to navigate between slides.)',
          'multioptions' => array(
              1 => 'Yes',
              0 => 'No'
          ),
          'onclick' => 'showWalk();',
          'value' => 1,
      ));

      $this->addElement('Radio', 'noob_walkIcon', array(
          'label' => 'Bullet Type',
          'description' => 'Select the type of the bullets to be shown for this slideshow.',
          'multioptions' => array(
              1 => 'Circle',
              0 => 'Square'
          ),
          'value' => 1,
      ));

      $this->addElement('Text', 'noob_walkSize', array(
          'label' => 'Bullet Size',
          'description' => 'Enter the size of the bullets.',
          'value' => 10,
      ));
      
       $this->addElement('Text', 'noob_bulletcolor', array(
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formImagerainbowbullet.tpl',
                    'class' => 'form element'
            )))
    ));
         $this->addElement('Text', 'noob_bulletactivecolor', array(
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formImagerainbowbulletactive.tpl',
                    'class' => 'form element'
            )))
    ));

      $this->addElement('Radio', 'noob_walkDiv', array(
          'label' => 'Bullet Navigation Position',
          'description' => 'Where do you want to show bullets?',
          'multioptions' => array(
              1 => 'Inside this Slideshow',
              0 => 'Outside this Slideshow'
          ),
          'value' => 1,
      ));

      $this->addElement('Radio', 'noob_walk_position', array(
          'label' => 'Bullets\' Alignment',
          'description' => 'Select the alignment of the bullets in this slideshow?',
          'multioptions' => array(
              'left' => 'In the left of Slideshow',
              'right' => 'In the right of Slideshow',
              'middle' => ' In the middle of Slideshow'
          ),
          'value' => 'middle',
      ));
    }

    $this->addElement('Radio', 'thumbnail', array(
        'label' => 'Slide thumbnails',
        'description' => 'Do you want to show thumbnails for the slides?',
        'multioptions' => array(
            1 => 'Yes, show thumbnails for the slides',
            0 => 'No, do not show thumbnails for the slides'
        ),
        'value' => 1,
    ));


    $this->addElement('Text', 'thumb_backcolor', array(
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formImagerainbow3.tpl',
                    'class' => 'form element'
            )))
    ));

    $this->addElement('Text', 'thumb_bordactivecolor', array(
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formImagerainbow5.tpl',
                    'class' => 'form element'
            )))
    ));

    $this->addElement('Text', 'thumb_bordcolor', array(
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formImagerainbow4.tpl',
                    'class' => 'form element'
            )))
    ));

    if ($this->_SlideType == "noob") {
      $this->addElement('Text', 'opacity', array(
          'label' => 'Opacity of Thumbnails Background Area',
          'description' => 'Enter the opacity for the background of thumbnails area.',
          'validators' => array(
              array('NotEmpty', true),
              array('Float', true),
              array('Between', false, array('min' => '0', 'max' => '1', 'inclusive' => false)),
          ),
          'value' => 0.75,
      ));
    }
    $this->addElement('Text', 'flash_color1', array(
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formImagerainbow1.tpl',
                    'class' => 'form element'
            )))
    ));

    $this->addElement('Text', 'flash_color2', array(
        'decorators' => array(array('ViewScript', array(
                    'viewScript' => '_formImagerainbow2.tpl',
                    'class' => 'form element'
            )))
    ));


    $this->addElement('Radio', 'slide_resize', array(
        'label' => 'Image Resizing',
        'description' => "Do you want the slideshow images uploaded by you to be resized to the slideshow dimensions? (Resizing an image might decrease its quality. If you select 'No' over here, then you should ensure that you upload images matching slideshow dimensions. Note that a change in this setting will only apply to images uploaded henceforth in this slideshow.)",
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => 1,
    ));


    $resource_type = Zend_Controller_Front::getInstance()->getRequest()->getParam('resource_type', null);
    if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestore') && Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreproduct') && $resource_type == 'sitestoreproduct_category') {
      $pageIdArray = Engine_Api::_()->sitestoreproduct()->getCategoryPageIds();
      if (in_array($page_value, $pageIdArray)) {
        if (array_key_exists('extreme1', $widget_position)) {
          $this->widget_position->setValue('extreme1');
        } elseif (array_key_exists('extreme2', $widget_position)) {
          $this->widget_position->setValue('extreme2');
        }
        $this->thumbnail->setValue(0);
        $this->controller->setValue(0);
        $this->slide_resize->setValue(0);
      }
    }

    $this->addElement('Button', 'submit', array(
        'type' => 'submit',
        'ignore' => true,
        'label' => 'Save Settings',
    ));
  }

}

?>
