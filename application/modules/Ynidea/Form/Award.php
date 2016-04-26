<?php

class Ynidea_Form_Award extends Engine_Form
{
  public function init()
  {
  	   $this->setTitle("Give your award");     
       $this->setAttrib('class', 'global_form_popup')
	   		->setAttrib('id','give_award_from');
			
       $this->addElement('Radio', 'award', array(
            'label' => 'Award',
            'description' => '',
            'multiOptions' => array(
              0 => 'Gold',
              1 => 'Silver',              
            ),
            'onclick' => 'showcomment(this)',
            'value' => 0,
       ));
        
      
       $this->addElement('textarea','comment',array(
        'label'=>'Comment',
        'description'=>'',
        'value' =>'',
      )); 
       //$this->comment->getDecorator("Description")->setOption("placement", "append");
        
        /*$this->addElement('hidden','tropy_id', array(
                'value' => 0,
                'order' =>100
            ));  
       $this->addElement('hidden','idea_id', array(
                'value' => 0,
                'order' =>100
            ));  
        */         
        $this->addElement('Button', 'submit', array(
        'label' => 'Give Award',
        'type' => 'submit',
        'onclick' => 'return award_idea()',
        'decorators' => array(
        'ViewHelper',
        ),
        ));
        // Element: cancel
        $this->addElement('Cancel', 'cancel', array(
          'label' => 'cancel',
          'link' => true,
          'prependText' => ' or ',
          'href' => 'javascript:;',
          'onclick' => 'parent.Smoothbox.close()',
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