<?php
class Spamcontrol_Plugin_Menus
{
  
  public function onMenuInitialize_SpamcontrolAdminMainBlog($row)
  {
      $blog = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('blog');
        
        if(!$blog){
            return false;
        }
        else{
            return $row;
        }
  }
  
  public function onMenuInitialize_SpamcontrolAdminMainPhoto($row)
  {
      $album = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('album');
        
        if(!$album){
            return false;
        }
        else{
            return $row;
        }
  }
}
