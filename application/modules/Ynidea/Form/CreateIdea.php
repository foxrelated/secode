<?php

class Ynidea_Form_CreateIdea extends Engine_Form
{
  public $_error = array();

  public function init()
  {   
        $this->setDescription("Compose your new page below, then click 'Save' to create page.")
          ->setAttrib('name', 'ynidea_create_idea');
        $user = Engine_Api::_()->user()->getViewer();
        $user_level = Engine_Api::_()->user()->getViewer()->level_id;
        
        $translate = Zend_Registry::get('Zend_Translate');
                    
        //Category
		 $this->addElement('Select', 'category_id', array(
	      'label' => 'Category',
	    ));
		
        $this->addElement('Text', 'title', array(
          'label' => 'Title',
          'required' => true,
          'title' => $translate->translate('Title of page'),             
          'description' => 'Please give a unique name to this idea',
          'autofocus' => 'autofocus',  
          'filters' => array(
            new Engine_Filter_Censor(),
            'StripTags',
            new Engine_Filter_StringLength(array('max' => '255'))
        )));
        $this->title->getDecorator("Description")->setOption("placement", "append");   
        
        // Tags
        $this->addElement('Text', 'tags',array(
          'label'=>'Tags (Keywords)',
          'autocomplete' => 'off',
          'filters' => array(
            new Engine_Filter_Censor(),
          ),
        ));
       $this->tags->getDecorator("Description")->setOption("placement", "append");
       
        $this->addElement('File', 'thumbnail', array(
        'label' => 'Thumbnail',
        'title' => $translate->translate('Main image of page'),    
        'description' => 'You may upload an image (jpg, png, gif, jpeg) to illustrate this idea. It will help people remember it. ',
      ));
      $this->thumbnail->getDecorator("Description")->setOption("placement", "append");
      $this->thumbnail->addValidator('Extension', false, 'jpg,png,gif,jpeg');
        
     //Cost
      $this->addElement('Text', 'cost',array(
          'label'=>'Cost',
          'autocomplete' => 'off',
          'description' => 'Please estimate the cost for the realization of this idea.',
          'filters' => array(
            new Engine_Filter_Censor(),
          ),
        ));
      $this->cost->getDecorator("Description")->setOption("placement", "append");
      
      //Feasibility   
      $this->addElement('Radio', 'feasibility', array(
        'label' => 'Feasibility',
        'description' => 'Specify how hard this idea will be to important.',
        'multiOptions' => array(
          0 => 'Easy',
          1 => 'Slightly Complex',
          2 => 'Complex',
          3 => 'Very Complex'
        ),
        'value' => 0,
      ));
       $this->feasibility->getDecorator("Description")->setOption("placement", "append");
       
      //Reproducible 
      $this->addElement('Radio', 'reproducible', array(
        'label' => 'Reproducible',
        'description' => 'Is your idea reproducible in other situations.',
        'multiOptions' => array(
          1 => 'Yes',
          0 => 'No',          
        ),
        'value' => 0,
      ));
       $this->reproducible->getDecorator("Description")->setOption("placement", "append");
        
	
       //Short Summary 
       $this->addElement('textarea','description',array(
        'label'=>'Short Summary',
        'description'=>'Write a short summary of the idea with its key points. Try to be concise and engaging!(maximum 1000 characters)',
        'value' =>'',
      )); 
       $this->description->getDecorator("Description")->setOption("placement", "append"); 
       
	  
       // Full Description  
        $allowed_html = Engine_Api::_()->authorization()->getPermission($user_level, 'ynidea_idea', 'auth_html');
        $upload_url = "";        
        if(Engine_Api::_()->authorization()->isAllowed('album', $user, 'create')){
          $upload_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'upload-photo'), 'ynidea_general', true);
        }
        $theme_advanced_buttons1 = "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,fontselect,fontsizeselect,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor,|,charmap,emotions,iespell,media";
        $theme_advanced_buttons2 = "pastetext,pasteword,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,fullscreen";
        $theme_advanced_buttons3 = "";
        $this->addElement('TinyMce', 'body', array(
          'label' => 'Full Description',
          'required' => true,
          'allowEmpty' => false,
          'decorators' => array(
            'ViewHelper'
          ),
          'editorOptions' => array(
            'bbcode' => 1,
            'html'   => 1,
            'mode' => 'exact',
            'elements' => 'body',
            'upload_url' => $upload_url,
            'theme_advanced_buttons1'=> $theme_advanced_buttons1,
            'theme_advanced_buttons2'=> $theme_advanced_buttons2,
            'theme_advanced_buttons3'=> $theme_advanced_buttons3,
            'theme_advanced_resizing'=> true,
            
          ),
          'filters' => array(
            new Engine_Filter_Censor(),
            new Engine_Filter_Html(array('AllowedTags'=>$allowed_html))),
        ));
		
		if(Ynidea_Api_Core::checkFundraisingPlugin())
		  {
		  	  $this->addElement('Checkbox','allow_campaign',array(
		        'label'=>'Allow other members to create fundraising campaign on my idea',
		        'value' => 0,
		      )); 
		  }
        $this->addElement('Button', 'submit', array(
        'label' => 'Save',
        'type' => 'submit',
        'ignore' => true,
        'style' => 'margin-top:10px;',
        'decorators' => array(
        'ViewHelper',
        ),
        ));
        // Element: cancel
        $this->addElement('Cancel', 'cancel', array(
          'label' => 'cancel',
          'link' => true,
          'prependText' => ' or ',
          'onclick' => '',
          'style' => 'margin-top:20px;',
          'decorators' => array(
            'ViewHelper',
          ),
        ));
         // DisplayGroup: buttons
        $this->addDisplayGroup(array(
          'submit',
          'cancel',
        ), 'buttons', array(
          'decorators' => array(
            'FormElements',
            'DivDivDivWrapper'
          ),
        ));
  }
}
