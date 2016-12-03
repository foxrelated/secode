<?php
class Money_Form_Issue extends Engine_Form
{
  protected $_balanse;
  
  public function init()
  {
      $viewer = Engine_Api::_()->user()->getViewer();
      $this->_balanse = $balance = Engine_Api::_()->money()->getUserBalance($viewer);
      $this->setTitle('Issue an invoice');


      $localeObject = Zend_Registry::get('Locale');
      $currency = Engine_Api::_()->getApi('settings',
          'core')->getSetting('money.site.currency', 'USD');
      $currencyName = Zend_Locale_Data::getContent($localeObject, 'currencysymbol', $currency);

      $description = $this->getTranslator()->translate('MONEY_FORM_ISSUE');
      $description = vsprintf($description, array($balance.' '.$currencyName));

      $this->setDescription($description);
   
     
    $options = array('' => '');
        $table = Engine_Api::_()->getDbtable('gateways', 'money');
        $gateway = $table->getEnabledGateways();

        foreach ($gateway as $value) {
            if($value['gateway_id'] != 3)
            $options[$value['gateway_id']] = $value['title'];
        }
    
    $this->addElement('Select', 'gateway_id', array(
        'label'=> 'Output method',
        'required' => true,
        'allowEmpty' => false,
        'multioptions' => $options,
        'onchange' => 'updateLabel(this)'
    ));
   
    $this->addElement('Text', 'amount', array(
        'label' => 'Amount',
        'required' => true,
        'allowEmpty' => false,
        'validators' => array(
        new Engine_Validate_Callback(array($this, 'validateAmount')),
      ),
        'onchange' => "commission(this.value)"
    ));  
    
    $this->addElement('Text', 'purse', array(
        'label' => 'Purse/email',
        'required' => true,
        'allowEmpty' => false,
        
    ));
       
    
    
    $this->addElement('Button', 'upload', array(
        'label' => 'Send',
        'type' => 'submit'
    ));
  }
  
  public function validateAmount($value){ 
     if($this->getValue('gateway_id') == 1){
         $this->getElement('purse')->setValidators(array('EmailAddress'));
     }
      if($value > $this->_balanse){
          $this->amount->getValidator('Engine_Validate_Callback')->setMessage('Do not exceed your account balance.');
          return false;
      }
      if(!is_numeric($value)){
          $this->amount->getValidator('Engine_Validate_Callback')->setMessage('Please enter numeric.');
          return false;
      }
      if($value <= 0){
          $this->amount->getValidator('Engine_Validate_Callback')->setMessage('Big 0.');
          return false;
      }
      return true;
  }
}