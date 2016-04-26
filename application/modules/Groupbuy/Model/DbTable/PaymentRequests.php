<?php
class Groupbuy_Model_DbTable_PaymentRequests extends Engine_Db_Table
{
     protected $_name     = 'groupbuy_payment_requests';
     protected $_primary  = 'paymentrequest_id';   
     protected $_rowClass = "Groupbuy_Model_PaymentRequest";

}