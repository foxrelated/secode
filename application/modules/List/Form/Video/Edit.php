<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    List
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Edit.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class List_Form_Video_Edit extends Engine_Form {

  protected $_isArray = true;

  public function init() {

    $this->clearDecorators()
        ->addDecorator('FormElements');

    $this->addElement('Text', 'title', array(
            'label' => 'Title',
            'filters' => array(
                    new Engine_Filter_Censor(),
            ),
            'decorators' => array(
                    'ViewHelper',
                    array('HtmlTag', array('tag' => 'div', 'class' => 'lists_editvideos_title_input')),
                    array('Label', array('tag' => 'div', 'placement' => 'PREPEND', 'class' => 'lists_editvideos_title')),
            ),
    ));

    $this->addElement('Textarea', 'description', array(
            'label' => 'Video Description',
            'rows' => 2,
            'cols' => 120,
            'filters' => array(
                    new Engine_Filter_Censor(),
            ),
            'decorators' => array(
                    'ViewHelper',
                    array('HtmlTag', array('tag' => 'div', 'class' => 'lists_editvideos_caption_input')),
                    array('Label', array('tag' => 'div', 'placement' => 'PREPEND', 'class' => 'lists_editvideos_caption_label')),
            ),
    ));

    $this->addElement('Checkbox', 'delete', array(
            'label' => "Delete Video",
            'decorators' => array(
                    'ViewHelper',
                    array('Label', array('placement' => 'APPEND')),
                    array('HtmlTag', array('tag' => 'div', 'class' => 'video-delete-wrapper')),
            ),
    ));

    $this->addElement('Hidden', 'video_id', array(
            'validators' => array(
                    'Int',
            )
    ));
  }
}