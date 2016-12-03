<?php
class Money_Model_DbTable_Packages extends Engine_Db_Table
{
  protected $_rowClass = 'Money_Model_Package';

  public function getEnabledPackageCount()
  {
    return $this->select()
      ->from($this, new Zend_Db_Expr('COUNT(*)'))
      ->where('enabled = ?', 1)
      ->query()
      ->fetchColumn()
      ;
  }

  public function getEnabledPackage()
  {
    return $this->fetchAll($this->select()
      ->where('enabled = ?', 1)
      ->where('price > ?', 0))
      ;
  }
}