<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventpaid
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventpaid_Api_Core extends Core_Api_Abstract {

    /**
     * Send emails for particular event
     * @params $type : which mail send
     * $params $eventId : Id of event
     * */
    public function sendMail($type, $eventId) {

        if (empty($type) || empty($eventId)) {
            return;
        }
        $event = Engine_Api::_()->getItem('siteevent_event', $eventId);
        $mail_template = null;
        if (!empty($event)) {

            $owner = Engine_Api::_()->user()->getUser($event->owner_id);
            switch ($type) {
                case "APPROVAL_PENDING":
                    $mail_template = 'siteevent_approval_pending';
                    break;
                case "EXPIRED":
                    $mail_template = $event->getPackage()->isFree() ? 'siteevent_expired' : 'siteevent_renew';
                    break;
                case "OVERDUE":
                    $mail_template = 'siteevent_overdue';
                    break;
                case "CANCELLED":
                    $mail_template = 'siteevent_cancelled';
                    break;
                case "ACTIVE":
                    $mail_template = 'siteevent_active';
                    break;
                case "PENDING":
                    $mail_template = 'siteevent_pending';
                    break;
                case "REFUNDED":
                    $mail_template = 'siteevent_refunded';
                    break;
                case "APPROVED":
                    $mail_template = 'siteevent_approved';
                    break;
                case "DISAPPROVED":
                    $mail_template = 'siteevent_disapproved';
                    break;
                case "DECLINED":
                    $mail_template = 'siteevent_declined';
                    break;
                case "RECURRENCE":
                    $mail_template = 'siteevent_recurrence';
                    break;
            }

            $httpVar = _ENGINE_SSL ? 'https://' : 'http://';
            $event_baseurl = $httpVar . $_SERVER['HTTP_HOST'] .
                    Zend_Controller_Front::getInstance()->getRouter()->assemble(array('event_id' => $event->event_id, 'slug' => $event->getSlug()), "siteevent_entry_view", true);

            //MAKING EVENT TITLE LINK
            $event_title_link = '<a href="' . $event_baseurl . '"  >' . $event->title . ' </a>';

            Engine_Api::_()->getApi('mail', 'core')->sendSystem($owner, $mail_template, array(
                'site_title' => Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.site.title', 1),
                'event_title' => $event->title,
                'event_description' => ucfirst($event->body),
                'event_title_with_link' => $event_title_link,
                'object_link' => $event_baseurl,
                'queue' => false,
            ));
        }
    }

    /**
     * Allow contect for particular package
     * @params $type : which check
     * $params $package_id : Id of event
     * $params $params : array some extra
     * */
    public function allowPackageContent($package_id, $type = null) {

        if (!Engine_Api::_()->siteevent()->hasPackageEnable())
            return;

        $flage = false;
        $package = Engine_Api::_()->getItem('siteeventpaid_package', $package_id);

        if (!empty($package) && isset($package->$type) && !empty($package->$type)) {
            $flage = true;
        }

        return $flage;
    }

    /**
     * Get Event Profile Fileds If package set some fields
     * @return array : profile fields
     * */
    public function getProfileFields() {

        $packageId = Zend_Controller_Front::getInstance()->getRequest()->getParam('id');
        $packageProfileFields = Engine_Api::_()->getItem('siteeventpaid_package', $packageId)->profilefields;
        return unserialize($packageProfileFields);
    }

    /**
     * Check here that show renew link or not
     * $params $event_id : Id of event
     * @return bool $showLink
     * */
    public function canShowRenewLink($event_id) {
        if (!Engine_Api::_()->siteevent()->hasPackageEnable())
            return;
        $showLink = false;
        $event = Engine_Api::_()->getItem('siteevent_event', $event_id);

        if (!empty($event)) {
            $package = $event->getPackage();

            if (!$package->isOneTime() || $package->isFree() || (!empty($package->level_id) && !in_array($event->getOwner()->level_id, explode(",", $package->level_id)))) {
                return (bool) false;
            }
            if ($package->renew) {
                if (!empty($event->expiration_date) && $event->status != "initial" && $event->status != "overdue") {
                    $diff_days = round((strtotime($event->expiration_date) - strtotime(date('Y-m-d H:i:s'))) / 86400);
                    if ($diff_days <= $package->renew_before || $event->expiration_date <= date('Y-m-d H:i:s')) {
                        return (bool) true;
                    }
                }
            }
        }
        return (bool) $showLink;
    }

    public function canAdminShowRenewLink($event_id) {
        if (!Engine_Api::_()->siteevent()->hasPackageEnable())
            return false;

        $showLink = false;
        $event = Engine_Api::_()->getItem('siteevent_event', $event_id);
        if (!empty($event)) {
            if (!empty($event->approved) && $event->expiration_date !== "2250-01-01 00:00:00")
                $showLink = true;
        }
        return (bool) $showLink;
    }

    /**
     * Check here that show payment link or not
     * $params $eventId : Id of event
     * @return bool $showLink
     * */
    public function canShowPaymentLink($event_id) {

        if (!Engine_Api::_()->siteevent()->hasPackageEnable())
            return;

        $showLink = true;
        $event = Engine_Api::_()->getItem('siteevent_event', $event_id);

        if (!empty($event)) {
            $package = $event->getPackage();
            if ($package->isFree()) {
                return (bool) false;
            }

            if (empty($event->expiration_date) || $event->expiration_date === "0000-00-00 00:00:00") {
                return (bool) true;
            }

            if ($event->status != "initial" && $event->status != "overdue") {
                return (bool) false;
            }

            if (($package->isOneTime()) && !$package->hasDuration() && !empty($event->approved)) {
                return false;
            }
        } else {
            $showLink = false;
        }
        return (bool) $showLink;
    }

    /**
     * FUNCTION USED TO UPDATE THE TABLE & SENT MAIL FOR EVENTS WHICH ARE GETTING EXPIRED. 
     * */
    public function updateExpiredEvents() {

        if (!Engine_Api::_()->siteevent()->hasPackageEnable())
            return;
        $this->updateEventTables();
    }

    public function updateEventTables() {

        $eventTable = Engine_Api::_()->getDbtable('events', 'siteevent');
        $eventTableName = $eventTable->info('name');
        //LIST FOR EVENTS WHICH ARE EXPIRIED NOW AND SEND MAIL
        $select = $eventTable->select()
                ->from($eventTableName, array('event_id'))
                ->where('status <>  ?', 'expired')
                ->where('approved = ?', '1')
                ->where('expiration_date <= ?', date('Y-m-d H:i:s'));

        foreach ($eventTable->fetchAll($select) as $event) {
            $this->sendMail("EXPIRED", $event->event_id);
        }

        //UPDATE THE STATUS
        $eventTable->update(array(
            'approved' => 0,
            'status' => 'expired'
                ), array(
            'status <>?' => 'expired',
            'expiration_date <=?' => date('Y-m-d H:i:s'),
        ));
        Engine_Api::_()->getApi('settings', 'core')->setSetting('siteeventpaid.task.updateexpiredevents', time());

        $eventTable->update(array(
            'search' => 0
                ), array(
            'status =?' => 'expired',
        ));
    }

    /**
     * Check here that show cancel link or not
     * $params $eventId : Id of event
     * @return bool $showLink
     * */
    public function canShowCancelLink($event_id) {

        if (!Engine_Api::_()->siteevent()->hasPackageEnable())
            return;

        $showLink = false;
        $event = Engine_Api::_()->getItem('siteevent_event', $event_id);

        if (!empty($event)) {
            $package = $event->getPackage();

            if (!$package->isFree() && $event->status == "active" && !$package->isOneTime() && !empty($event->approved)) {
                return (bool) true;
            }
        }

        return (bool) $showLink;
    }

}
