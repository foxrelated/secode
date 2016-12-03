
<?php 
class Qrcode_Model_DbTable_Qrcodes extends Engine_Db_Table
{
	 protected $_name = 'qrcode_qrcodes';
  protected $_rowClass = 'Qrcode_Model_Qrcode';
  protected $_primary = 'qrcode_id';
}
?>