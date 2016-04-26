<?php

class Semtomfriend_Model_DbTable_Semtomfriend extends Engine_Db_Table
{

  public function getSetting($key, $default = '') {
    
    $key = $this->_normalize($key);
    
    $row = $this->fetchRow($this->select()->where('name = ?', $key));
    
    return !is_null($row) ? $row['value'] : $default;
    
  }
  
  public function setSetting($key, $value) {
    
    $key = $this->_normalize($key);
    $success = true;

    try {
    
      $this->insert(array( 'name'   => $key,
                           'value'  => $value
                           )
                     );
      
    } catch(Exception $ex) {
      
      $success = false;
      
    }
    
    if($success) {
      return;
    }

    try {
    
      $x = $this->update(array('value'    => $value),
                         array('name = ?' => $key)
                     );

      
    } catch(Exception $ex) {
      
    }
    
  }


  protected function _normalize($key)
  {
    return str_replace('_', '.', $key);
  }
  
  
}