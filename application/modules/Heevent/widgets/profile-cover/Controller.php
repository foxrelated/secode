<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 19.10.13 08:20 jungar $
 * @author     Jungar
 */

/**
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Heevent_Widget_ProfileCoverController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }
    $event = Engine_Api::_()->core()->getSubject();
    if(Engine_Api::_()->hasModuleBootstrap('like') && (bool)(Engine_Api::_()->like()->isAllowed($event))){
      $this->view->hasLike = true;
      $this->view->isLiked = $liked = Engine_Api::_()->like()->isLike($event);
      $action = $liked ? 'unlike' : 'like';
      $toggleAction = $liked ? 'like' : 'unlike';
      $this->view->likeHref = $this->view->url(array('action' => $action, 'object' => $event->getType(), 'object_id' => $event->getIdentity(), 'format' => 'json'), 'like_default');
      $this->view->likeToggleHref = $this->view->url(array('action' => $toggleAction, 'object' => $event->getType(), 'object_id' => $event->getIdentity(), 'format' => 'json'), 'like_default');
    }
    // Get subject and check auth
    $this->view->event = Engine_Api::_()->core()->getSubject('event');
//    $this->view->heevent = $heevent = Engine_Api::_()->getItem('heevent_event', Engine_Api::_()->core()->getSubject('event')->getIdentity());
  }
}