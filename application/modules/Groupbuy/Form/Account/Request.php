<?php


class Groupbuy_Form_Account_Request extends Engine_Form
{
public function init()
  {
  	$currency = Engine_Api::_() -> groupbuy() -> getDefaultCurrency();
	$viewer = Engine_Api::_()->user()->getViewer();
	$commission= Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('groupbuy_deal', $viewer, 'commission');
  	 if($commission == "")
         {
             $mtable  = Engine_Api::_()->getDbtable('permissions', 'authorization');
             $maselect = $mtable->select()
                ->where("type = 'groupbuy_deal'")
                ->where("level_id = ?",$viewer->level_id)
                ->where("name = 'commission'");
              $mallow_a = $mtable->fetchRow($maselect);          
              if (!empty($mallow_a))
                $commission = $mallow_a['value'];
              else
                 $commission = 0;
         }
    $this->setTitle('Request');
  	$min_payout = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupbuy.minWithdrawSeller', 5.00);
    $max_payout = Engine_Api::_()->getApi('settings', 'core')->getSetting('groupbuy.maxWithdrawSeller', 100.00);
  	$user_id = $viewer->getIdentity();
    $requested_amount = Groupbuy_Api_Account::getTotalRequest($user_id,1);
	$info_account = Groupbuy_Api_Account::getCurrentAccount($user_id);
	$rest = $info_account['total_amount'] - $requested_amount;
	if ($rest <= $max_payout) {
		$maxvalue = $rest;
	}
	else {
		$maxvalue = $max_payout;
	}
    if($rest < $min_payout)
    {
         $this->addNotice("You can not request, because available amount is smaller than minimum amount to request.");
    }
    else
    {
  	    $this->addElement('Text', 'txtrequest_money',array(
          'label'=>'Amount',
          'allowEmpty' => false,
          'required'=>true,
	      'validators' => array(
            array('NotEmpty', true),
       	    array('Float', true),
            array('Between', true, array($min_payout, $maxvalue, true)),
          ),
          'filters' => array(
            new Engine_Filter_Censor(),
          ),
         'value'=>'',
        ));
  	    $this->addElement('Textarea', 'textarea_request', array(
          'label' => 'Reason',
          'description' => '',
          'required' => false,
          'allowEmpty' => true,
          'validators' => array(
            array('NotEmpty', true),
          ),
        ));
    $this->addElement('Button', 'submit', array(
      'label' => 'Request',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));
     $this->addElement('Hidden', 'deal', array(
      'order' => 100
    ));

    $this->addElement('Hidden', 'number_buy', array(
      'order' => 102
    ));

    $this->addElement('Hidden', 'total_amount', array(
      'order' => 103
    ));
    // Element: cancel
    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'onClick'=> 'javascript:parent.Smoothbox.close();',
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
}