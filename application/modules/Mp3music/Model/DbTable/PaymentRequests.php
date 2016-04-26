<?php
class Mp3music_Model_DbTable_PaymentRequests extends Engine_Db_Table
{
     protected $_name     = 'mp3music_payment_requests';
     protected $_primary  = 'paymentrequest_id';   
     protected $_rowClass = "Mp3music_Model_PaymentRequest";

}