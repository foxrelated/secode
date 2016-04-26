<?php

class Spamcontrol_Form_Warn extends Engine_Form {

    protected $_resource_type;
    protected $_resource_id;

    public function setResource_type($value) {
        $this->_resource_type = $value;
    }

    public function setResource_id($value) {
        $this->_resource_id = $value;
    }

    public function init() {

        $this->setAttrib('class', 'global_form_popup')
             ->setTitle('Take Action')
             ->setAction($_SERVER['REQUEST_URI']);

        $item = Engine_Api::_()->getItem($this->_resource_type, $this->_resource_id);
        
         
        
        $count = Engine_Api::_()->getdbtable('warn', 'spamcontrol')->getWarnCount($item->getOwner());
        $str= sprintf(Zend_Registry::get('Zend_Translate')->translate('This user have %s warnings. What would you like to do?'), $count) ;
        $this->setDescription($str);
        
        
        
        

        $this->addElement('Radio', 'action', array(
            'label' => 'Poster Action',
            'multioptions' => array(
                'delete' => 'Delete '.$item->getShortType(),
                'deleteall' => 'Delete All '.$item->getShortType().' this User',
                'warn' => 'Warn',
                'warndelete' => 'Warn and Delete all '.$item->getShortType().' this User',
            ),
            'order' => 0
        ));



        $this->addElement('textarea', 'body', array('label' => 'Comment'));

        $this->addElement('Button', 'execute', array(
            'type' => 'submit',
            'label' => 'Submit',
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addElement('Cancel', 'cancel', array(
            'link' => true,
            'prependText' => ' or ',
            'label' => 'cancel',
            'href' => 'javascript:parent.Smoothbox.close();',
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addDisplayGroup(array('execute', 'cancel'), 'buttons');
    }

}
?>

