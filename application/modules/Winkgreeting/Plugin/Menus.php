<?php

class Winkgreeting_Plugin_Menus
{
  //Wink	
  public function onMenuInitialize_WinkgreetingWink($row)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
	$settings = Engine_Api::_()->getApi('settings', 'core');
	$enable = isset($settings->winkgreeting_wink) ? $settings->winkgreeting_wink : false;
	$confirm = isset($settings->winkgreeting_confirm) ? $settings->winkgreeting_confirm : false;

    $label = $row->label;

    if( !$viewer->getIdentity() || $viewer->getIdentity() == $subject->getIdentity() || !$enable )
    {
      return false;
    }
	
	if( !Engine_Api::_()->authorization()->isAllowed('winkgreeting', $viewer, 'wink') ) {
      return false;
    }	
	if (!$confirm) {
      return array(
        'label' => $label,
		'class' => 'smoothbox',
        'icon' => 'application/modules/Winkgreeting/externals/images/wink.gif',
        'route' => 'winkgreeting_extended',
        'params' => array(
          'controller' => 'compose',
          'action' => 'wink',
		  'id' => $subject->getIdentity(),
        )
      );
	}
	else {
      return array(
        'label' => $label,
		'class' => 'smoothbox',
        'icon' => 'application/modules/Winkgreeting/externals/images/wink.gif',
        'route' => 'winkgreeting_extended',
        'params' => array(
          'controller' => 'compose',
          'action' => 'confirmwink',
		  'id' => $subject->getIdentity(),
        )
      );	
	}	  
    return false;
  }
  //Greeting	
  public function onMenuInitialize_WinkgreetingGreeting($row)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
	$settings = Engine_Api::_()->getApi('settings', 'core');
	$enable = isset($settings->winkgreeting_greeting) ? $settings->winkgreeting_greeting : false;
	$confirm = isset($settings->winkgreeting_confirm) ? $settings->winkgreeting_confirm : false;
	
    $label = $row->label;

    if( !$viewer->getIdentity() || $viewer->getIdentity() == $subject->getIdentity() || !$enable )
    {
      return false;
    }
	
	if( !Engine_Api::_()->authorization()->isAllowed('winkgreeting', $viewer, 'greeting') ) {
      return false;
    }	
	if (!$confirm) {
      return array(
        'label' => $label,
		'class' => 'smoothbox',
        'icon' => 'application/modules/Winkgreeting/externals/images/greeting.png',
        'route' => 'winkgreeting_extended',
        'params' => array(
          'controller' => 'compose',
          'action' => 'greeting',
		  'id' => $subject->getIdentity(),
        )
      );
	}
	else {
      return array(
        'label' => $label,
		'class' => 'smoothbox',
        'icon' => 'application/modules/Winkgreeting/externals/images/greeting.png',
        'route' => 'winkgreeting_extended',
        'params' => array(
          'controller' => 'compose',
          'action' => 'confirmgreeting',
		  'id' => $subject->getIdentity(),
        )
      );	
	}	  
    return false;
  }  
}
