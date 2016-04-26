<?php
class Ynchat_Plugin_Task_Core extends Core_Plugin_Task_Abstract {
	public function execute() {
		$now = date("Y-m-d H:i:s");
		$expiredTime = date_sub(date_create($now), date_interval_create_from_date_string('1800 seconds'));
        $table = Engine_Api::_()->getDbTable('status', 'ynchat');
		$where = $table->getAdapter()->quoteInto("creation_date < ?", date_format($expiredTime, "Y-m-d H:i:s"));
		$table->update(array('status' => 'offline'), $where);
    }
}