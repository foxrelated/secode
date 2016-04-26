<?php
class Spamcontrol_Form_SearchComments extends Engine_Form
{
  public function init()
  {
        $this
      ->clearDecorators()
      ->addDecorator('FormElements')
      ->addDecorator('Form')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
      ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'))
      ;
      
        $this
      ->setAttribs(array(
        'id' => 'filter_form',
        'class' => 'global_form_box',
      ));
        
        $this->addElement('text', 'text', array(
        'label' => 'Search'
        ));
       
        $plugins = Engine_Api::_()->getDbtable('modules', 'core');
        $pluginsName = $plugins->info('name');
        $comment = Engine_Api::_()->getDbtable('comments', 'core');
        $commentName = $comment->info('name');
        $attachment = Engine_Api::_()->getDbtable('attachments', 'activity');
        $attachmentName = $attachment->info('name');
        $pl = $plugins->fetchAll($plugins->select()
                                        ->from($pluginsName)
                                        //->joinRight($commentName, "`{$commentName}`.`resource_type` = `{$pluginsName}`.`name`", null)       
                                        ->where('enabled =?', 1)
                                        ->where('type =?', 'extra'));
        $options = array(0 => ' ');
        foreach($pl as $value){
            $options[$value['name']] = $value['title'];
        }
        
        
        $this->addElement('select', 'search', array(
        'label' => 'in', 
        'multioptions' => array(
           '1'=> 'in body',
           '2'=> 'by author'
       )
    ));
        $this->addElement('checkbox', 'url', array(
            'label' => 'With Url',
            'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => 'div')),
        array('HtmlTag', array('tag' => 'div', 'style'=>'text-align: center'))
      ),
        ));
        
        $this->addElement('select', 'plugins', array(
            'label' => 'Plugins',
            'multioptions' => $options
        ));
        
    $this->addElement('select', 'showen', array(
           'Label' => 'Showen:',
           'multioptions' => array(
               '0' => 'Last month',
               '1' => 'Last year',
               '2' => 'Last week',
               '3' => 'Last day'),    
    ));
    
   
    
    $this->addElement('select', 'sort_by', array(
        'Label' => 'Sort by',
        'multioptions' => array(
            'date' => 'date',
            'user' => 'user',
            'warn' => 'warn'
        ),
    ));
    
    $this->addElement('button', 'submit', array(
          'type' => 'submit'
      ));
  
  }
}
?>
