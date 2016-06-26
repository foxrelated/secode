<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Menus.php 19.10.13 08:20 jungar $
 * @author     Jungar
 */

/**
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Heevent_Plugin_Menus
{
  public function getLink()
  {
    if (!Engine_Api::_()->user()->getViewer()->getIdentity()){
      return false;
    }

    if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('mobile')) {
      if (Engine_Api::_()->mobile()->isMobileMode()) {
        $subject = Engine_Api::_()->core()->getSubject();
        $suggest_type = 'link_'.$subject->getType();

        if (Engine_Api::_()->suggest()->isAllowed($suggest_type) && Engine_Api::_()->user()->getViewer()->getIdentity()) {
          $router = Zend_Controller_Front::getInstance()->getRouter();
          $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . "application" . DIRECTORY_SEPARATOR ."modules" . DIRECTORY_SEPARATOR . "Mobile" . DIRECTORY_SEPARATOR .
            "modules" . DIRECTORY_SEPARATOR . "Suggest" . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "scripts";

          $paramStr = '?m=suggest&l=getSuggestItems&nli=0&params[object_type]='.$subject->getType().'&params[object_id]='.$subject->getIdentity() .
            '&action_url='.urlencode($router->assemble(array('action' => 'suggest'), 'suggest_general')) .
            '&params[suggest_type]=' . $suggest_type . '&params[scriptpath]=' . $path;

          $url = $router->assemble(array('controller' => 'index', 'action' => 'contacts', 'module' => 'hecore'), 'default', true) . $paramStr;
          return array(
            'label' => 'Suggest To Friends',
            'icon' => 'application/modules/Suggest/externals/images/suggest.png',
            'class' => 'suggest_link',
            'uri' => $url
          );
        } else {
          return false;
        }
      }
    }

    return array(
      'label' => 'Suggest To Friends',
      'icon' => 'application/modules/Suggest/externals/images/suggest.png',
      'route' => 'suggest_general',
      'class' => 'suggest_link'
    );
  }

  public function onMenuInitialize_HeeventAdminMainSettings($row){
    return $row;
  }
  public function onMenuInitialize_HeeventAdminMainTransactions($row){
    return $row;
  }
  public function onMenuInitialize_HeeventAdminMainTickets($row){
    return $row;
  }
  public function onMenuInitialize_HeeventProfileStyle()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    if( $subject->getType() !== 'event' )
    {
      throw new Event_Model_Exception('Whoops, not an event!');
    }

    if( !$viewer->getIdentity() || !$subject->authorization()->isAllowed($viewer, 'edit') )
    {
      return false;
    }

    if( !$subject->authorization()->isAllowed($viewer, 'style') )
    {
      return false;
    }

    return array(
      'label' => 'Edit Event Style',
      'icon' => 'application/modules/Event/externals/images/style.png',
      'class' => 'smoothbox',
      'route' => 'event_specific',
      'params' => array(
        'action' => 'style',
        'event_id' => $subject->getIdentity(),
        'format' => 'smoothbox',
      )
    );
  }

  public function onMenuInitialize_HeeventProfileReport()
  {
    return false;
  }
  public function onMenuInitialize_EventMainTickets($row)
  {
    if (!Engine_Api::_()->user()->getViewer()->getIdentity()){
      return false;
    }else{
      return $row;
    }
  }
  public function onMenuInitialize_HeeventAdminMainAssignticket($row)
  {
    if (!Engine_Api::_()->user()->getViewer()->getIdentity()){
      return false;
    }else{
      return $row;
    }
  }

  public function onMenuInitialize_HeeventProfileMessage()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    if( $subject->getType() !== 'event' )
    {
      throw new Event_Model_Exception('This event does not exist.');
    }

    if( !$viewer->getIdentity() || !$subject->isOwner($viewer))
    {
      return false;
    }

    return array(
      'label' => 'Message Members',
      'icon' => 'application/modules/Messages/externals/images/send.png',
      'route' => 'messages_general',
      'params' => array(
        'action' => 'compose',
        'to' => $subject->getIdentity(),
        'multi' => 'event'
      )
    );
  }

  public function onMenuInitialize_HeeventProfileDelete()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();
    if( $subject->getType() !== 'event' ) {
      throw new Event_Model_Exception('This event does not exist.');
    } else if( !$subject->authorization()->isAllowed($viewer, 'delete') ) {
      return false;
    }

    return array(
      'label' => 'Delete Event',
      'icon' => 'application/modules/Event/externals/images/delete.png',
      'class' => 'smoothbox',
      'route' => 'event_specific',
      'params' => array(
        'action' => 'delete',
        'event_id' => $subject->getIdentity(),
        //'format' => 'smoothbox',
      ),
    );
  }

  public function onMenuInitialize_HeeventProfileSuggest($row)
  {
  	$showLink = Engine_Api::_()->getApi('settings', 'core')->getSetting('suggest.link.event');

    if (!Engine_Api::_()->core()->hasSubject() || !$showLink) {
      return false;
    }

    return $this->getLink();
  }

}