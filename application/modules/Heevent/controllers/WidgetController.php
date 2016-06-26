<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: WidgetController.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Event
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Heevent_WidgetController extends Core_Controller_Action_Standard
{
  /* protected $_gateway;*/

  protected $_order;

  public function init()
  {
    /*$this->_gateway = Engine_Api::_()->getItem('heevent_gateway', 2);*/

  }
  public function profileInfoAction() 
  {
    // Don't render this if not authorized
    if( !$this->_helper->requireAuth()->setAuthParams(null, null, 'view')->isValid() )
      return $this->_helper->viewRenderer->setNoRender(true);
  }

  public function profileRsvpAction()
  {

    $this->view->form = new Event_Form_Rsvp();
    $event = Engine_Api::_()->core()->getSubject();

    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$event->membership()->isMember($viewer, true))
    {
      return;
    }

    $row = $event->membership()->getRow($viewer);
    $this->view->viewer_id = $viewer->getIdentity();
    if ($row) {
      $this->view->rsvp = $row->rsvp;
    }
    else
    {
      return $this->_helper->viewRenderer->setNoRender(true);
    }
    if ($this->getRequest()->isPost())
    {
      $option_id = $this->getRequest()->getParam('option_id');

      $row->rsvp = $option_id;
      $row->save();
    }
  }
  public function heprofileRsvpAction()
  {

    $this->view->form = new Event_Form_Rsvp();
    $event = Engine_Api::_()->core()->getSubject();

    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$event->membership()->isMember($viewer, true))
    {
      return;
    }

    $row = $event->membership()->getRow($viewer);
    $this->view->viewer_id = $viewer->getIdentity();
    if ($row) {
      $this->view->rsvp = $row->rsvp;
    }
    else
    {
      return $this->_helper->viewRenderer->setNoRender(true);
    }


      $this->view->isPostTrue = false;
   if ($this->getRequest()->isPost())
    {
        $this->view->isPostTrue = true;
        $this->view->price = $tempParams['ticket_price'] = $this->getRequest()->getParam('price_heevent');
        $this->view->count = $tempParams['ticket_count'] = $this->getRequest()->getParam('quantity')+1;
        $tempParams['event_id'] = $event->getIdentity();
        $tempParams['user_id'] = $viewer->getIdentity();
        $tempParams['status'] = 0;
        $event->setTemp($tempParams);
     /*   $this->view->name = $event->getTitle();
        $schema = 'http://';
        if (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) {
          $schema = 'https://';
        }
      $host = $_SERVER['HTTP_HOST'];
      $params             = array();
      $params['language'] = $this->_user->language;
      $localeParts        = explode('_', $this->_user->language);
      if (count($localeParts) > 1) {
        $params['region'] = $localeParts[1];
      }
      $params['vendor_order_id'] = $event->getIdentity();
      $params['return_url']      = $schema . $host
        . $this->view->url(array('action' => 'return'))
        . '?event_id=' . $event->getIdentity()
        . '&state=' . 'return';
      $params['cancel_url']      = $schema . $host
        . $this->view->url(array('action' => 'return'))
        . '?event_id=' . $event->getIdentity()
        . '&state=' . 'cancel';
      $params['ipn_url']         = $schema . $host
        . $this->view->url(array('action'     => 'index',
          'controller' => 'ipn',
          'module' => 'store',
        ), 'default', true)
        . '?event_id=' . $event->getIdentity()
        . '&state=' . 'ipn';
     $this->view->gatewayPlugin = $gatewayPlugin = $this->_gateway->getGateway();
      $this->view->gatewayPlugin = $gatewayPlugin = $this->_gateway->getGateway();
      $plugin                    = $this->_gateway->getPlugin();*/

     /* try {
        $transaction = $plugin->createCartTransaction($event->getIdentity(), $params);
      } catch (Exception $e) {
        if ('development' == APPLICATION_ENV) {
          throw $e;
        } elseif (in_array($e->getCode(), array(10736, 10731))) {
          $this->_session->__set('errorMessage', array(
            'STORE_PAYMENT_PROCESS_GATEWAY_RETURNED_AN_ERROR',
            $this->view->translate(
              'STORE_TRANSACTION_REPORT_FORM %1$scontact%2$s',
              '<a href="javascript:void(0);" onclick="goToContactPageAfterError();return false;">',
              '</a>'
            ),
            $e->getMessage()
          ));
          $this->_session->__set('errorName', $e->getCode());
        } else {
          $this->_session->__set('errorMessage', 'STORE_PAYMENT_PROCESS_GATEWAY_RETURNED_AN_ERROR');
          print_log($e->__toString());
        }

        return $this->_finishPayment('failed');
      }
      $plugin                    = $this->_gateway->getPlugin();
      $this->view->transactionUrl    = $transactionUrl = $gatewayPlugin->getGatewayUrl();
      $this->view->transactionMethod = $transactionMethod = $gatewayPlugin->getGatewayMethod();
      $this->view->transactionData   = $transactionData = $transaction->getData();
      /*$countParams['count_ticket'] = $this->getRequest()->getParam('quantity')+1;*/
        /* $countParams['user'] = $viewer->getIdentity();
         $countParams['event_id'] = $event->getIdentity();
        $event->setEventCount($countParams);
       $option_id = $this->getRequest()->getParam('option_id');

       $row->rsvp = $option_id;
       $row->save();*/
    }

  }

  public function requestEventAction()
  {
    $this->view->notification = $notification = $this->_getParam('notification');
  }
}