<?php
/**
 * Created by PhpStorm.
 * User: bolot
 * Date: 05.02.14
 * Time: 11:39
 */

class Heevent_Form_Ticket extends Engine_Form {
    private $event = null;
    private $ticketPrice = 0;
    private $event_id = 0;
    public function __construct(Heevent_Model_Event $event = null) {
        $idAttr = 'ticket_form';
        if($event) {
            $this->event = $event;
            $this->event_id = $event->getIdentity();
            $this->ticketPrice = Engine_Api::_()->getDbTable('tickets', 'heevent')->getEventTickets($event)->ticket_price;
            $idAttr .= $event->getIdentity();
        }
        $this->setAttribs(array(
            'id' => $idAttr
        ));

        parent::__construct();
    }
    public function init()
    {
      $this->addElement('Dummy', 'Title', array(
        'content' => '<span style="font-size:18px" >'.$this->event->getTitle().'</span>'
      ));
        $this->addElement('Dummy', 'price_label', array(
            'label' => 'HEEVENT_Price',
            'content' => '<span class="heticket_price_'.$this->event_id.'">' .   $this->event->getCurentPrice($this->event->getPrice($this->event_id)) . '</span>'
        ));
      $count  = Engine_Api::_()->getDbtable('cards', 'heevent')->getUserCountBuy($this->event_id)->count;
      $list = 5-$count;
      if($list<=0){
        $this->addElement('Dummy', 'Error', array(
          'content' => '<span style="font-size:18px; colot:red;" >Exhausted  limit of 5 Tickets</span>'
        ));
        return;
      }else{
        $q = array();
        $y=0;
        for($i=0; $i<$list; $i++){
          $q[$i]= ++$y;
        }

      }
        $this->addElement('Select', 'ticket_quantity', array(
            'label' => 'HEEVENT_Quantity',
            'id' => 'ticket_quantity_',
            'onchange' => 'price_changer(this,'.$this->event_id.' )',
            'multiOptions' =>  $q,
        ));
        $this->addElement('Hidden', 'event_id', array(
            'value' => $this->event_id
        ));
        $this->addElement('Hidden', 'price_heevent', array(
            'value' => $this->ticketPrice,
            'id' => 'price_heevent'.$this->event_id,
            'class' => 'price_heevent'.$this->event_id
        ));
        $this->addElement('Button', 'ticket_buy', array(
            'label' => 'HEEVENT_Buy',
            'class' => 'ticket_buy'.$this->event_id,
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'onclick' => 'hideBuy_form('.$this->event_id.')',
            'prependText' => ' or ',
            'decorators' => array(
                'ViewHelper',
            ),
        ));
        $this->addDisplayGroup(array('ticket_buy', 'cancel'), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper',
            ),
        ));

    }
} 