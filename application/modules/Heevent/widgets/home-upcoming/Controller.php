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
class Heevent_Widget_HomeUpcomingController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        // Don't render this if not logged in
        $viewer = Engine_Api::_()->user()->getViewer();
        $eventTable = Engine_Api::_()->getItemTable('event');
        $settings = Engine_Api::_()->getApi('settings', 'core');

//    // Show nothing
//    if(!$viewer->getIdentity() ) {
//      return $this->setNoRender();
//    }

        date_default_timezone_set('UTC');

        $now = date('Y-m-d H:i:s', strtotime("now"));
        $nextWeekStart = date('Y-m-d H:i:s', strtotime("next Monday"));
        $nextWeekEnd = date('Y-m-d H:i:s', strtotime($nextWeekStart . '1 week'));

        $eventsSelect = $eventTable->select()
            ->where('search = ?', 1)->where('starttime between \'' . $now . '\' and \'' . $nextWeekEnd . '\'')
            ->orWhere('\'' . $now . '\' between starttime and endtime')->order('starttime')->limit(50);
        $events = Zend_Paginator::factory($eventsSelect)->setItemCountPerPage(50);

        $eventsContainer = array();

        $userTimezone = null;
        if ($viewer->getIdentity()) {
            $userTimezone = $viewer->timezone;
        } else {
            $userTimezone = @$_COOKIE['timezone'];
        }

        date_default_timezone_set($userTimezone);
        $today = date('d');

        foreach ($events as $event) {
            $event->starttime = date('Y-m-d H:i:s', strtotime($event->starttime . 'UTC'));
            $day = date('d', strtotime($event->starttime));

            if ($today > $day && date('m') == date('m', strtotime($event->starttime))) {
                $event->view_count = 'Going';
                $eventsContainer[$today][sizeof($eventsContainer[$today])] = $event;
            } else {
                $eventsContainer[$day][sizeof($eventsContainer[$day])] = $event;
            }
        }

        if (Engine_Api::_()->hasModuleBootstrap('pageevent') && $settings->getSetting('page.browse.pageevent', 0)) {
            $pageEventTable = Engine_Api::_()->getItemTable('pageevent');

            $pageEventsSelect = $pageEventTable->select()
                ->where('starttime between \'' . $now . '\' and \'' . $nextWeekEnd . '\'')
                ->orWhere('\'' . $now . '\' between starttime and endtime')->order('starttime')->limit(50);
            $pageEvent = Zend_Paginator::factory($pageEventsSelect)->setItemCountPerPage(50);

            foreach ($pageEvent as $event) {
                $event->starttime = date('Y-m-d H:i:s', strtotime($event->starttime . 'UTC'));
                $day = date('d', strtotime($event->starttime));
                if ($today > $day && date('m') == date('m', strtotime($event->starttime))) {
                    $event->view_count = 'Going';
                    $eventsContainer[$today][sizeof($eventsContainer[$today])] = $event;
                } else {
                    $eventsContainer[$day][sizeof($eventsContainer[$day])] = $event;
                }
            }
        }

        $this->view->allevents = $eventsContainer;

        if (!isset($eventsContainer) && empty($eventsContainer)) {
            return $this->setNoRender();
        }
    }
}