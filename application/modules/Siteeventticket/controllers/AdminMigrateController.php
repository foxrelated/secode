<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: AdminMigrateController.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_AdminMigrateController extends Core_Controller_Action_Admin {

    public function indexAction() {

        //TAB CREATION
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('siteevent_admin_main', array(), 'siteeventticket_admin_main_ticket');

        //GET NAVIGATION
        $this->view->navigationGeneral = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('siteeventticket_admin_main_ticket', array(), 'siteeventticket_admin_main_migrate');

        $this->view->lastEventId = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.lasteventid', 0);

        if ($this->view->lastEventId) {
            $this->view->form = $form = new Siteeventticket_Form_Admin_Migrate_Migrate();
        }
    }

    public function migrateAction() {

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        ini_set('memory_limit', '2048M');
        set_time_limit(0);
        ini_set('max_input_time', 600);
        ini_set('max_execution_time', 600);

        $lastEventId = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.lasteventid', 0);

        if (empty($lastEventId)) {
            return;
        }

        $siteeventTable = Engine_Api::_()->getDbTable('events', 'siteevent');
        $siteeventTableName = $siteeventTable->info('name');

        $siteeventticketTable = Engine_Api::_()->getDbTable('tickets', 'siteeventticket');
        $siteeventticketTableName = $siteeventticketTable->info('name');

        $siteeventoccurenceTable = Engine_Api::_()->getDbTable('occurrences', 'siteevent');
        $siteeventoccurenceTableName = $siteeventoccurenceTable->info('name');

        $fetchColumns = array('event_id', 'title', 'owner_id', 'member_count', 'creation_date');
        if (Engine_Api::_()->hasModuleBootstrap('siteeventrepeat')) {
            $fetchColumns = array_merge($fetchColumns, array('repeat_params'));
        }

        $selectEvents = $siteeventTable->select()
            ->from($siteeventTableName, $fetchColumns)
            ->where("$siteeventTableName.draft = ?", 0)
            ->where("$siteeventTableName.event_id <= ?", $lastEventId)
            ->order("$siteeventTableName.event_id DESC");
        $eventDatas = $siteeventTable->fetchAll($selectEvents);
        $totalEvents = COUNT($eventDatas);
        $next_import_count = 0;
        foreach ($eventDatas as $eventData) {
            --$totalEvents;
            $lasteventid = ($totalEvents > 0) ? $eventData->getIdentity() : 0;
            $totalTickets = $siteeventticketTable->select()
                ->from($siteeventticketTableName, array('COUNT(*) AS total_tickets'))
                ->where($siteeventticketTableName . ".event_id = ?", $eventData->event_id)
                ->query()
                ->fetchColumn();
            if ($totalTickets > 0) {
                Engine_Api::_()->getApi('settings', 'core')->setSetting('siteeventticket.lasteventid', $lasteventid);
                continue;
            }

            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {

                //ADD CREATE TICKET CODE
                $ticket = $this->createTicket($eventData);

                $selectOccurrences = $siteeventoccurenceTable->select()
                    ->from($siteeventoccurenceTableName, array('starttime', 'endtime', 'occurrence_id'))
                    ->where("$siteeventoccurenceTableName.event_id = ?", $eventData->event_id)
                    ->where("$siteeventoccurenceTableName.endtime > NOW()");
                $occurrencesDatas = $siteeventoccurenceTable->fetchAll($selectOccurrences);
                foreach ($occurrencesDatas as $occurrencesData) {
                    $memberIds = $eventData->membership()->getEventMembers($eventData->event_id, $occurrencesData->occurrence_id, true, 1);
                    foreach ($memberIds as $memberId) {

                        $user = Engine_Api::_()->getItem('user', $memberId);
                        $userId = $user->getIdentity();
                        if (empty($userId) || !($user instanceof Core_Model_Item_Abstract)) {
                            continue;
                        }

                        //ADD PLACE ORDER CODE
                        $this->placeOrder(array('userObject' => $user, 'occurrenceObject' => $occurrencesData, 'eventObject' => $eventData, 'ticketObject' => $ticket));
                    }
                }

                //DECREASE THE VALUE OF LAST EVENT ID
                Engine_Api::_()->getApi('settings', 'core')->setSetting('siteeventticket.lasteventid', $lasteventid);
                $next_import_count++;
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
            
//            if ($next_import_count >= 50) {
//                $this->_redirect("admin/siteeventticket/migrate/migrate");
//            }            
        }

        $this->view->lasteventid = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.lasteventid', 0);
    }

    public function createTicket($siteevent) {

        $values = array();
        $values['event_id'] = $siteevent->getIdentity();
        $values['owner_id'] = $siteevent->owner_id;
        $values['quantity'] = $siteevent->member_count + 100;
        $values['buy_limit_max'] = 10;
        $values['title'] = "Entry Ticket";
        $values['is_same_end_date'] = 0;
        $values['sell_starttime'] = $siteevent->creation_date;

        $sell_endtime = $occurrenceDate = Engine_Api::_()->getDbTable('occurrences', 'siteevent')->getOccurenceEndDate($siteevent->getIdentity(), 'DESC');

        if (Engine_Api::_()->hasModuleBootstrap('siteeventrepeat') && !empty($siteevent->repeat_params)) {
            $eventparams = json_decode($siteevent->repeat_params);
            if (!empty($eventparams) && isset($eventparams->endtime) && !empty($eventparams->endtime->date)) {
                $sell_endtime = date('Y-m-d H:i:s', strtotime($eventparams->endtime->date));
                $sell_endtime = (strtotime($occurrenceDate) > strtotime($sell_endtime)) ? $occurrenceDate : $sell_endtime;
            }
        }

        $values['sell_endtime'] = $sell_endtime;

        $siteeventticketTable = Engine_Api::_()->getDbTable('tickets', 'siteeventticket');
        $ticket = $siteeventticketTable->createRow();
        $ticket->setFromArray($values);
        $ticket->save();

        Engine_Api::_()->getDbtable('occurrences', 'siteevent')->setTicketDetails($siteevent->getIdentity(), $ticket->ticket_id);

        return $ticket;
    }

    public function placeOrder($params = array()) {

        //ASSIGN ALL ARRAY VARIABLES
        extract($params);

        //SAVE ORDERS IN ORDER TABLE
        $orderTable = Engine_Api::_()->getDbtable('orders', 'siteeventticket');
        $order = $orderTable->createRow();

        $order->user_id = $userObject->getIdentity();
        $order->event_id = $eventObject->event_id;
        $order->occurrence_id = $occurrenceObject->occurrence_id;
        $order->occurrence_starttime = $occurrenceObject->starttime;
        $order->occurrence_endtime = $occurrenceObject->endtime;
        $order->order_status = 2;
        $order->payment_status = 'active';
        $order->creation_date = date('Y-m-d H:i:s');
        $order->gateway_id = 5;
        $order->direct_payment = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.payment.to.siteadmin', 0) ? 1 : 0;
        $order->is_private_order = 1;
        $order->ticket_qty = 1;
        $order->save();

        //SAVE ORDER IN TICKETORDER TABLE
        $orderticketTable = Engine_Api::_()->getDbtable('orderTickets', 'siteeventticket');
        $orderTicket = $orderticketTable->createRow();

        $orderTicket->order_id = $order->getIdentity();
        $orderTicket->ticket_id = $ticketObject->getIdentity();
        $orderTicket->title = $ticketObject->title;
        $orderTicket->quantity = 1;
        $orderTicket->save();

        //ADD BUYER DETAILS
        $buyerdetailTable = Engine_Api::_()->getDbtable('buyerdetails', 'siteeventticket');

        $buyerDetail = $buyerdetailTable->createRow();
        $buyerDetail->ticket_id = $ticketObject->getIdentity();
        $buyerDetail->order_id = $order->getIdentity();

        $aliasValues = Engine_Api::_()->fields()->getFieldsValuesByAlias($userObject);
        if (!empty($aliasValues)) {
            $buyerDetail->first_name = !empty($aliasValues['first_name']) ? $aliasValues['first_name'] : null;
            $buyerDetail->last_name = !empty($aliasValues['last_name']) ? $aliasValues['last_name'] : null;
        }

        $buyerDetail->email = $userObject->email;

        //SAVE A RANDOM ALPHANUMERIC NO. AS BUYER TICKET ID
        $buyerDetail->buyer_ticket_id = Engine_Api::_()->siteeventticket()->buyerTicketIdGenerate();
        $buyerDetail->save();

        //UPDATE TICKET DETAILS
        Engine_Api::_()->siteeventticket()->updateTicketsSoldQuantity(array('occurrence_id' => $order->occurrence_id));
    }

}
