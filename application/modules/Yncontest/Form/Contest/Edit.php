<?php
class Yncontest_Form_Contest_Edit extends Yncontest_Form_Contest_Create
{
  public function init()
  {
   
  	 

    $this
      ->setTitle('Basic Information');

    $this->addElement('Text', 'contest_name', array(
      'label' => 'Contest Name*',
      'allowEmpty' => false,
      'required' => true,
      'validators' => array(
        array('NotEmpty', true),
        array('StringLength', false, array(1, 64)),
      ),
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
      ),
    ));
    
    $allowed_html = 'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr, object , param, iframe';
    $this->addElement('TinyMce', 'description', array(
    		'label' => 'Description*',
    		'editorOptions' => array(
    				'bbcode' => 1,
    				'html'   => 1,
    				'theme_advanced_buttons1' => array(
    						'undo', 'redo', 'cleanup', 'removeformat', 'pasteword', '|',
    						'media', 'image','link', 'unlink', 'fullscreen', 'preview', 'emotions'
    				),
    				'theme_advanced_buttons2' => array(
    						'fontselect', 'fontsizeselect', 'bold', 'italic', 'underline',
    						'strikethrough', 'forecolor', 'backcolor', '|', 'justifyleft',
    						'justifycenter', 'justifyright', 'justifyfull', '|', 'outdent', 'indent', 'blockquote',
    				),
    		),
    		'required'   => true,
    		'allowEmpty' => false,
    		'filters' => array(
    				new Engine_Filter_Censor(),
    				new Engine_Filter_Html(array('AllowedTags'=>$allowed_html)))
    ));
    
    
	
    $this->addElement('Text', 'tags',array(
          'label'=>'Tags',
          'autocomplete' => 'off',
          'description' => 'Tips: separate tags with commas.',
          'filters' => array(
            new Engine_Filter_Censor(),
          ),
        ));
    $this->tags->getDecorator("Description")->setOption("placement", "append");
 
    
    $this->addElement('Radio', 'contest_type', array(
    		'label' => 'Contest Type*',
    		'required'   => true,
    		'multiOptions' => $this->_plugin,
    		'value' => key($this->_plugin),
    ));
    

    $this->addElement('File', 'photo_id', array(
    		'label' => 'Contest Photo*',
    		'required' => true,
    ));
    $this->photo_id->addValidator('Extension', false, 'jpg,png,gif,jpeg');
	
	
  
    $start = new Engine_Form_Element_CalendarDateTime('start_date');
    $start->setLabel("Start Date*");
    $start->setAllowEmpty(false);
    $start->setRequired(true);      
    $this->addElement($start);
    
    $end = new Engine_Form_Element_CalendarDateTime('end_date');
    $end->setLabel("End Date*");
    $end->setAllowEmpty(false);
    $end->setRequired(true);
    $this->addElement($end);
    	
    
    $this->addElement('TinyMce', 'award', array(
    		'label' => 'Award*',
    		'editorOptions' => array(
    				'bbcode' => 1,
    				'html'   => 1,
    				'theme_advanced_buttons1' => array(
    						'undo', 'redo', 'cleanup', 'removeformat', 'pasteword', '|',
    						'media', 'image','link', 'unlink', 'fullscreen', 'preview', 'emotions'
    				),
    				'theme_advanced_buttons2' => array(
    						'fontselect', 'fontsizeselect', 'bold', 'italic', 'underline',
    						'strikethrough', 'forecolor', 'backcolor', '|', 'justifyleft',
    						'justifycenter', 'justifyright', 'justifyfull', '|', 'outdent', 'indent', 'blockquote',
    				),
    		),
    		'required'   => true,
    		'allowEmpty' => false,
    		'filters' => array(
    				new Engine_Filter_Censor(),
    				new Engine_Filter_Html(array('AllowedTags'=>$allowed_html)))
    ));
 	$this->addElement('TinyMce', 'condition', array(
    		'label' => 'Terms and Conditions*',
    		'description' => 'Tips: This is Terms and Conditions applied for contest.',
    		'editorOptions' => array(
    				'bbcode' => 1,
    				'html'   => 1,
    				'theme_advanced_buttons1' => array(
    						'undo', 'redo', 'cleanup', 'removeformat', 'pasteword', '|',
    						'media', 'image','link', 'unlink', 'fullscreen', 'preview', 'emotions'
    				),
    				'theme_advanced_buttons2' => array(
    						'fontselect', 'fontsizeselect', 'bold', 'italic', 'underline',
    						'strikethrough', 'forecolor', 'backcolor', '|', 'justifyleft',
    						'justifycenter', 'justifyright', 'justifyfull', '|', 'outdent', 'indent', 'blockquote',
    				),
    		),
    		'required'   => true,
    		'allowEmpty' => false,
    		'filters' => array(
    				new Engine_Filter_Censor(),
    				new Engine_Filter_Html(array('AllowedTags'=>$allowed_html)))
    ));
    $this->condition->getDecorator("Description")->setOption("placement", "append");
  
    $this->addElement('TinyMce', 'condition', array(
    		'label' => 'Terms and Conditions*',
    		'description' => 'Tips: This is Terms and Conditions applied for contest.',
    		'editorOptions' => array(
    				'bbcode' => 1,
    				'html'   => 1,
    				'theme_advanced_buttons1' => array(
    						'undo', 'redo', 'cleanup', 'removeformat', 'pasteword', '|',
    						'media', 'image','link', 'unlink', 'fullscreen', 'preview', 'emotions'
    				),
    				'theme_advanced_buttons2' => array(
    						'fontselect', 'fontsizeselect', 'bold', 'italic', 'underline',
    						'strikethrough', 'forecolor', 'backcolor', '|', 'justifyleft',
    						'justifycenter', 'justifyright', 'justifyfull', '|', 'outdent', 'indent', 'blockquote',
    				),
    		),
    		'required'   => true,
    		'allowEmpty' => false,
    		'filters' => array(
    				new Engine_Filter_Censor(),
    				new Engine_Filter_Html(array('AllowedTags'=>$allowed_html)))
    ));
    $this->addElement('TinyMce', 'winner_description', array(
    		'label' => 'Winners Congratulation*',    		
    		'editorOptions' => array(
    				'bbcode' => 1,
    				'html'   => 1,
    				'theme_advanced_buttons1' => array(
    						'undo', 'redo', 'cleanup', 'removeformat', 'pasteword', '|',
    						'media', 'image','link', 'unlink', 'fullscreen', 'preview', 'emotions'
    				),
    				'theme_advanced_buttons2' => array(
    						'fontselect', 'fontsizeselect', 'bold', 'italic', 'underline',
    						'strikethrough', 'forecolor', 'backcolor', '|', 'justifyleft',
    						'justifycenter', 'justifyright', 'justifyfull', '|', 'outdent', 'indent', 'blockquote',
    				),
    		),
    		'required'   => true,
    		'allowEmpty' => false,
    		'filters' => array(
    				new Engine_Filter_Censor(),
    				new Engine_Filter_Html(array('AllowedTags'=>$allowed_html)))
    ));

   
 
//     $this->addElement('ContestMultiLevel', 'category_id', array(
//     		'label' => 'Category*',
//     		'required'=>true,
//     		'allowEmpty'=>false,
//     		'model'=>'Yncontest_Model_DbTable_Categories',
//     		'onchange'=>"en4.yncontest.changeCategory($(this),'category_id','Yncontest_Model_DbTable_Categories','contest/my-contest')",
//     		'title' => '',
//     		'value' => ''
//     ));
    
//     $this->addElement('ContestMultiLevel', 'location_id', array(
//         'label' => 'Location',
//         'required'=>false,
// 		'allowEmpty' => true,
//         'model'=>'Yncontest_Model_DbTable_Locations',
//         'onchange'=>"en4.yncontest.changeCategory($(this),'location_id','Yncontest_Model_DbTable_Locations','contest/my-contest')",
// 		'title' => '',
// 		'value' => ''
//      ));
    
    
    
    
   
    
//     $this->addElement('Text', 'contest_email', array(
//     		'label' => 'Contest Email*',    		
//     		'description' => 'We will send email alert to you if there is a submitted entry.',
//     		'required'   => true,
//     		'allowEmpty' => false,
//     		'validators' => array(
// 		        array('NotEmpty', true),
// 		        array('EmailAddress', true),
// 		      ),    		
//     ));
//     $this->contest_email->getDecorator("Description")->setOption("placement", "append");
    
   
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Save & Continue',     
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));

     
    
    $this->addElement('Cancel', 'cancel', array(
    		'label' => 'cancel',
    		'link' => true,
    		'prependText' => ' or ',
    		'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'index'), 'yncontest_general', true),
    		'onclick' => '',
    		'decorators' => array(
    				'ViewHelper'
    		)
    ));
  }
}

