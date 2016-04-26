<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Template.php 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Api_Template extends Core_Api_Abstract {

    public function homePageCreate() {

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        //EVENTS HOME PAGE CREATION
        $page_id = $db->select()
                ->from('engine4_core_pages', 'page_id')
                ->where('name = ?', "siteevent_index_home")
                ->limit(1)
                ->query()
                ->fetchColumn();

        if (empty($page_id)) {

            //CREATE PAGE
            $db->insert('engine4_core_pages', array(
                'name' => "siteevent_index_home",
                'displayname' => 'Advanced Events - Events Home',
                'title' => 'Events Home',
                'description' => 'This is the events home page.',
                'custom' => 0,
            ));
            $page_id = $db->lastInsertId();
        }

        return $page_id;
    }

    public function profilePageCreate() {

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        //EVENT PROFILE PAGE
        $page_id = $db->select()
                ->from('engine4_core_pages', 'page_id')
                ->where('name = ?', "siteevent_index_view")
                ->query()
                ->fetchColumn();

        if (empty($page_id)) {

            $db->insert('engine4_core_pages', array(
                'name' => "siteevent_index_view",
                'displayname' => 'Advanced Events - Event Profile',
                'title' => '',
                'description' => 'This is Event profile page.',
                'custom' => 0
            ));
            $page_id = $db->lastInsertId('engine4_core_pages');
        }

        return $page_id;
    }

    public function deleteCoreContent($page_id) {

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        if (!empty($page_id)) {
            $db->delete('engine4_core_content', array('page_id = ?' => $page_id));
        }
    }

    public function defaultHome() {

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        //EVENTS HOME PAGE CREATION
        $page_id = $this->homePageCreate();

        if (!empty($page_id)) {

            $this->deleteCoreContent($page_id);

            $containerCount = 0;
            $widgetCount = 0;

            //TOP CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'order' => $containerCount++,
            ));
            $top_container_id = $db->lastInsertId();

            //MAIN CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => $containerCount++,
            ));
            $main_container_id = $db->lastInsertId();

            //INSERT TOP-MIDDLE
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_container_id,
                'order' => $containerCount++,
            ));
            $top_middle_id = $db->lastInsertId();

            //LEFT CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'left',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => $containerCount++,
            ));
            $left_container_id = $db->lastInsertId();

            //RIGHT CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'right',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => $containerCount++,
            ));
            $right_container_id = $db->lastInsertId();

            //MAIN-MIDDLE CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => $containerCount++,
            ));
            $main_middle_id = $db->lastInsertId();

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.navigation-siteevent',
                'parent_content_id' => $top_middle_id,
                'order' => $widgetCount++,
                'params' => '',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.searchbox-siteevent',
                'parent_content_id' => $top_middle_id,
                'order' => $widgetCount++,
                'params' => '{"title":"","titleCount":"","locationDetection":"1","formElements":["textElement","categoryElement","locationElement","locationmilesSearch"],"categoriesLevel":["category"],"showAllCategories":"0","textWidth":"338","locationWidth":"250","locationmilesWidth":"135","categoryWidth":"150","nomobile":"0","name":"siteevent.searchbox-siteevent"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.zeroevent-siteevent',
                'parent_content_id' => $main_middle_id,
                'order' => $widgetCount++,
                'params' => '',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.slideshow-siteevent',
                'parent_content_id' => $main_middle_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Featured Events","titleCount":true,"statistics":["viewCount","likeCount","commentCount","memberCount","reviewCount"],"eventType":"0","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","fea_spo":"featured","eventInfo":["hostName","categoryLink","featuredLabel","sponsoredLabel","startDate","endDate","location","directionLink","memberCount"],"showEventType":"all","popularity":"view_count","interval":"overall","blockHeight":"173","truncationLocation":"35","truncation":"100","truncationDescription":"0","count":"3","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"1","name":"siteevent.slideshow-siteevent"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.sponsored-siteevent',
                'parent_content_id' => $main_middle_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Sponsored Events","titleCount":true,"showOptions":["category","rating","review"],"eventType":"0","fea_spo":"sponsored","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","showPagination":"1","viewType":"0","blockHeight":"195","blockWidth":"187","itemCount":"3","popularity":"event_id","eventInfo":["startDate"],"showEventType":"upcoming","interval":"300","truncationLocation":"35","truncation":"100","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"siteevent.sponsored-siteevent"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.recently-popular-random-siteevent',
                'parent_content_id' => $main_middle_id,
                'order' => $widgetCount++,
                'params' => '{"title":"","titleCount":"","statistics":["viewCount","likeCount","commentCount","memberCount","reviewCount"],"layouts_views":["listZZZview","gridZZZview","mapZZZview"],"ajaxTabs":["upcoming","thisZZZmonth","thisZZZweek","thisZZZweekend","today"],"showContent":["price","location"],"upcoming_order":"1","reviews_order":"10","popular_order":"9","featured_order":"7","sponosred_order":"8","joined_order":"6","columnWidth":"194","titleLink":"","eventType":"0","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","eventInfo":["hostName","startDate","location","directionLink"],"showEventType":"all","defaultOrder":"gridZZZview","columnHeight":"260","month_order":"5","week_order":"3","weekend_order":"4","today_order":"2","titlePosition":"1","showViewMore":"1","limit":"15","truncationLocation":"35","truncationList":"600","truncationGrid":"100","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"siteevent.recently-popular-random-siteevent"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.calendarview-siteevent',
                'parent_content_id' => $right_container_id,
                'order' => $widgetCount++,
                'params' => '{"title":"","titleCount":true,"siteevent_calendar_event_count":"0","siteevent_calendar_event_count_type":"0","nomobile":"0","name":"siteevent.calendarview-siteevent"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.newevent-siteevent',
                'parent_content_id' => $right_container_id,
                'order' => $widgetCount++,
                'params' => '{"nomobile":"1"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.events-siteevent',
                'parent_content_id' => $right_container_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Most Joined Events","titleCount":true,"statistics":["likeCount","memberCount"],"viewType":"gridview","columnWidth":"215","eventType":"0","fea_spo":"","showEventType":"upcoming","titlePosition":"1","columnHeight":"330","popularity":"member_count","interval":"overall","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","eventInfo":["hostName","startDate"],"itemCount":"2","truncationLocation":"35","truncation":"100","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"siteevent.events-siteevent"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.events-siteevent',
                'parent_content_id' => $right_container_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Most Recent Events","titleCount":true,"statistics":["likeCount","memberCount"],"viewType":"gridview","columnWidth":"215","eventType":"0","fea_spo":"","showEventType":"upcoming","titlePosition":"1","columnHeight":"328","popularity":"event_id","interval":"overall","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","eventInfo":["startDate"],"itemCount":"2","truncationLocation":"35","truncation":"100","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"siteevent.events-siteevent"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.popularlocation-siteevent',
                'parent_content_id' => $right_container_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Popular Locations","titleCount":true,"eventType":"upcoming","itemCount":"10","nomobile":"0","name":"siteevent.popularlocation-siteevent"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.tagcloud-siteevent',
                'parent_content_id' => $right_container_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Popular Tags (%s)","titleCount":true,"eventType":"upcoming","orderingType":"1","itemCount":"25","nomobile":"1","name":"siteevent.tagcloud-siteevent"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'seaocore.change-my-location',
                'parent_content_id' => $left_container_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Select your Location","showSeperateLink":"1","nomobile":"0","name":"seaocore.change-my-location"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.special-events',
                'parent_content_id' => $left_container_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Event of the Day","titlePosition":"1","viewType":"gridview","columnWidth":"215","columnHeight":"328","eventInfo":["hostName","featuredLabel","sponsoredLabel","startDate","location","directionLink"],"itemCount":"1","truncationLocation":"35","truncation":"100","ratingType":"rating_avg","nomobile":"0","name":"siteevent.special-events"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.events-siteevent',
                'parent_content_id' => $left_container_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Events Starting Soon","titleCount":true,"statistics":["likeCount","memberCount"],"viewType":"gridview","columnWidth":"215","eventType":"0","fea_spo":"","showEventType":"onlyUpcoming","titlePosition":"1","columnHeight":"328","popularity":"starttime","interval":"overall","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","eventInfo":["startDate"],"itemCount":"2","truncationLocation":"35","truncation":"100","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"1","name":"siteevent.events-siteevent"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.recently-viewed-siteevent',
                'parent_content_id' => $left_container_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Recently Viewed By Friends","titleCount":true,"statistics":["likeCount","memberCount"],"eventType":"0","fea_spo":"","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","show":"1","viewType":"gridview","columnWidth":"215","columnHeight":"328","eventInfo":["startDate","location","directionLink"],"titlePosition":"1","truncationLocation":"35","truncation":"100","count":"2","ratingType":"rating_avg","nomobile":"1","name":"siteevent.recently-viewed-siteevent"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.events-siteevent',
                'parent_content_id' => $left_container_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Most Liked Events","titleCount":true,"statistics":["likeCount","memberCount"],"viewType":"gridview","columnWidth":"215","eventType":"0","fea_spo":"","showEventType":"upcoming","titlePosition":"1","columnHeight":"328","popularity":"view_count","interval":"overall","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","eventInfo":["startDate"],"itemCount":"2","truncationLocation":"35","truncation":"100","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"siteevent.events-siteevent"}',
            ));
        }
    }

    public function template2Home() {

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        //EVENTS HOME PAGE CREATION
        $page_id = $this->homePageCreate();

        if (!empty($page_id)) {

            $this->deleteCoreContent($page_id);

            $containerCount = 0;
            $widgetCount = 0;

            //TOP CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'order' => $containerCount++,
            ));
            $top_container_id = $db->lastInsertId();

            //MAIN CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'main',
                'page_id' => $page_id,
                'order' => $containerCount++,
            ));
            $main_container_id = $db->lastInsertId();

            //INSERT TOP-MIDDLE
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_container_id,
                'order' => $containerCount++,
            ));
            $top_middle_id = $db->lastInsertId();

            //LEFT CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'left',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => $containerCount++,
            ));
            $left_container_id = $db->lastInsertId();

            //RIGHT CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'right',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => $containerCount++,
            ));
            $right_container_id = $db->lastInsertId();

            //MAIN-MIDDLE CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $main_container_id,
                'order' => $containerCount++,
            ));
            $main_middle_id = $db->lastInsertId();

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.navigation-siteevent',
                'parent_content_id' => $top_middle_id,
                'order' => $widgetCount++,
                'params' => '',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.categories-sponsored',
                'parent_content_id' => $top_middle_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Sponsored Categories","titleCount":true,"itemCount":"9","showIcon":"1","nomobile":"0","name":"siteevent.categories-sponsored"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.ads-plugin-siteevent',
                'parent_content_id' => $top_middle_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Slideshow","titleCount":"true","pluginName":"advancedslideshow","nomobile":"1"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.recently-popular-random-siteevent',
                'parent_content_id' => $top_middle_id,
                'order' => $widgetCount++,
                'params' => '{"title":"","titleCount":"","statistics":["viewCount","likeCount","commentCount","memberCount","reviewCount"],"layouts_views":["listZZZview","gridZZZview","mapZZZview"],"ajaxTabs":["upcoming","mostZZZreviewed","featured","sponsored","mostZZZjoined","thisZZZmonth","thisZZZweek","thisZZZweekend","today"],"showContent":["price","location"],"upcoming_order":"1","reviews_order":"2","popular_order":"3","featured_order":"4","sponosred_order":"5","joined_order":"6","columnWidth":"193","eventType":"0","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","eventInfo":["startDate","location","directionLink"],"showEventType":"all","defaultOrder":"gridZZZview","columnHeight":"225","month_order":"7","week_order":"8","weekend_order":"9","today_order":"10","titlePosition":"1","limit":"10","truncationLocation":"35","truncationList":"600","truncationGrid":"100","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"siteevent.recently-popular-random-siteevent"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.calendarview-siteevent',
                'parent_content_id' => $main_middle_id,
                'order' => $widgetCount++,
                'params' => '{"title":"","titleCount":true}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.pinboard-events-siteevent',
                'parent_content_id' => $main_middle_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Most Popular Events","statistics":["likeCount","memberCount"],"show_buttons":"","eventType":"0","fea_spo":"","popularity":"view_count","interval":"overall","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","eventInfo":["startDate","location","directionLink"],"showEventType":"upcoming","userComment":"0","autoload":"0","defaultLoadingImage":"1","itemWidth":"210","withoutStretch":"0","itemCount":"12","noOfTimes":"0","truncationLocation":"35","truncationDescription":"100","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"siteevent.pinboard-events-siteevent"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'seaocore.change-my-location',
                'parent_content_id' => $right_container_id,
                'order' => $widgetCount++,
                'params' => '',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.categories-sidebar-siteevent',
                'parent_content_id' => $right_container_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Categories","titleCount":true}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.sponsored-siteevent',
                'parent_content_id' => $right_container_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Popular Events","titleCount":true,"showOptions":["category","rating","review"],"eventType":"0","fea_spo":"featured","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","showPagination":"1","viewType":"1","blockHeight":"208","blockWidth":"213","itemCount":"3","popularity":"view_count","eventInfo":["startDate","viewCount"],"showEventType":"upcoming","interval":"300","truncationLocation":"35","truncation":"100","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"siteevent.sponsored-siteevent"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.sponsored-siteevent',
                'parent_content_id' => $left_container_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Sponsored Events","titleCount":true,"showOptions":["category","rating","review"],"eventType":"0","fea_spo":"sponsored","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","showPagination":"1","viewType":"1","blockHeight":"180","blockWidth":"328","itemCount":"2","popularity":"event_id","eventInfo":["startDate","location","directionLink"],"showEventType":"all","interval":"300","truncationLocation":"35","truncation":"100","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"siteevent.sponsored-siteevent"}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.recently-viewed-siteevent',
                'parent_content_id' => $left_container_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Viewed by Users","titleCount":true,"statistics":["likeCount","memberCount"],"eventType":"0","fea_spo":"","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","show":"0","viewType":"gridview","columnWidth":"215","columnHeight":"287","eventInfo":["hostName","featuredLabel","sponsoredLabel","newLabel","startDate","location","directionLink"],"titlePosition":"1","truncationLocation":"35","truncation":"100","count":"2","ratingType":"rating_avg","nomobile":"0","name":"siteevent.recently-viewed-siteevent"}',
            ));
        }
    }

    public function defaultProfile() {

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        //EVENTS HOME PAGE CREATION
        $page_id = $this->profilePageCreate();

        if (!empty($page_id)) {

            $this->deleteCoreContent($page_id);

            $containerCount = 0;
            $widgetCount = 0;

            //TOP CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'order' => $containerCount++,
            ));
            $top_container_id = $db->lastInsertId();

            //INSERT TOP-MIDDLE
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_container_id,
                'order' => $containerCount++,
            ));
            $top_middle_id = $db->lastInsertId();

            //MAIN CONTAINER
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'main',
                'order' => $containerCount++,
                'params' => '',
            ));
            $main_container_id = $db->lastInsertId('engine4_core_content');

            //RIGHT CONTAINER
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'right',
                'parent_content_id' => $main_container_id,
                'order' => $containerCount++,
                'params' => '',
            ));
            $right_container_id = $db->lastInsertId('engine4_core_content');

            //MIDDLE CONTAINER  
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $main_container_id,
                'order' => $containerCount++,
                'params' => '',
            ));
            $main_middle_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.list-profile-breadcrumb',
                'parent_content_id' => $top_middle_id,
                'order' => $widgetCount++,
                'params' => '',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.led-by-siteevent',
                'parent_content_id' => $right_container_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Led By","titleCount":true}'
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.event-status',
                'parent_content_id' => $right_container_id,
                'order' => $widgetCount++,
                'params' => '{"title":"","titleCount":true}'
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.diary-add-link',
                'parent_content_id' => $right_container_id,
                'order' => $widgetCount++,
                'params' => '{"title":""}'
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.profile-event-buttons',
                'parent_content_id' => $right_container_id,
                'order' => $widgetCount++,
                'params' => '{"title":"","showButtons":["signIn","signUp","uploadPhotos","uploadVideos"],"nomobile":"0","name":"siteevent.profile-event-buttons"}'
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.location-sidebar-siteevent',
                'parent_content_id' => $right_container_id,
                'order' => $widgetCount++,
                'params' => '{"title":"When and Where","titleCount":true,"showContent":["startDate","endDate"],"height":"200","nomobile":"0","name":"siteevent.location-sidebar-siteevent"}'
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.overall-ratings',
                'parent_content_id' => $right_container_id,
                'order' => $widgetCount++,
                'params' => '{"title":"","titleCount":true,"show_rating":"both","ratingParameter":"1","nomobile":"0","name":"siteevent.overall-ratings"}'
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.write-siteevent',
                'parent_content_id' => $right_container_id,
                'order' => $widgetCount++,
                'params' => '{"title":"","titleCount":true,"nomobile":"1"}'
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.profile-host-info',
                'parent_content_id' => $right_container_id,
                'order' => $widgetCount++,
                'params' => '{"placeWidget":"smallColumn","showInfo":["totalevent","totalrating","hostDescription","socialLinks","messageHost"],"title":"Host","nomobile":"1","name":"siteevent.profile-host-info"}'
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.review-button',
                'parent_content_id' => $right_container_id,
                'order' => $widgetCount++,
                'params' => '{"title":"","nomobile":"1"}'
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.information-siteevent',
                'parent_content_id' => $right_container_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Information","titleCount":true,"showContent":["ledBy","price","viewCount","likeCount","commentCount","memberCount","tags","rsvp","socialShare"],"allowSocialSharing":"0","nomobile":"1","name":"siteevent.information-siteevent"}'
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.about-editor-siteevent',
                'parent_content_id' => $right_container_id,
                'order' => $widgetCount++,
                'params' => '{"title":"About Editor","titleCount":"true","nomobile":"1"}'
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.quick-specification-siteevent',
                'parent_content_id' => $right_container_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Quick Informations","titleCount":"true","nomobile":"1"}'
            ));

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitetagcheckin')) {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitetagcheckin.checkinbutton-sitetagcheckin',
                    'parent_content_id' => $right_container_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","titleCount":true,"checkin_use":"1","checkin_button_sidebar":"1","checkin_button":"1","checkin_button_link":"Check-in here","checkin_icon":"1","checkin_verb":"Check-in","checkedinto_verb":"checked-into","checkin_your":"You\'ve checked-in here","checkin_total":"Total check-ins here","nomobile":"0","name":"sitetagcheckin.checkinbutton-sitetagcheckin"}'
                ));

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitetagcheckin.checkinuser-sitetagcheckin',
                    'parent_content_id' => $right_container_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","titleCount":true,"checkedin_heading":"People Here","checkedin_see_all_heading":"People who have been here","checkedin_users":"0","checkedin_user_photo":"1","checkedin_user_name":"0","checkedin_user_checkedtime":"0","checkedin_item_count":"5","nomobile":"0","name":"sitetagcheckin.checkinuser-sitetagcheckin"}'
                ));
            }

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.related-events-view-siteevent',
                'parent_content_id' => $right_container_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Related Events","titleCount":true,"statistics":["likeCount","reviewCount","memberCount"],"eventType":"All","related":"tags","viewType":"listview","columnWidth":"180","columnHeight":"328","showEventType":"upcoming","titlePosition":"1","eventInfo":["startDate","endDate"],"itemCount":"3","truncation":"40","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"1","name":"siteevent.related-events-view-siteevent"}'
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.userevent-siteevent',
                'parent_content_id' => $right_container_id,
                'order' => $widgetCount++,
                'params' => '{"title":"%s\'s Events","titleCount":true,"statistics":["likeCount","reviewCount"],"show":"owner","eventType":"All","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","eventInfo":["startDate","endDate"],"showEventType":"upcoming","titlePosition":"1","viewType":"listview","columnWidth":"180","columnHeight":"328","count":"3","truncation":"40","ratingType":"rating_avg","nomobile":"1","name":"siteevent.userevent-siteevent"}'
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'seaocore.scroll-top',
                'parent_content_id' => $right_container_id,
                'order' => $widgetCount++,
                'params' => '',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'seaocore.social-share-buttons',
                'parent_content_id' => $right_container_id,
                'order' => $widgetCount++,
                'params' => '{"show_buttons":["facebook","twitter","linkedin","plusgoogle","share"],"title":"","nomobile":"0","name":"seaocore.social-share-buttons"}'
            ));

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitecontentcoverphoto')) {

                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('spectacular')) {
                    $db->query('DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`name` = "seaocore.social-share-buttons" AND `engine4_core_content`.`page_id` = "' . $page_id . '" LIMIT 1');
                    $db->query('DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`name` = "siteevent.add-to-my-calendar-siteevent" AND `engine4_core_content`.`page_id` = "' . $page_id . '" LIMIT 1');
                    $db->query('DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`name` = "siteevent.list-profile-breadcrumb" AND `engine4_core_content`.`page_id` = "' . $page_id . '" LIMIT 1');

                    $db->insert('engine4_core_content', array(
                        'page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'sitecontentcoverphoto.content-cover-photo',
                        'parent_content_id' => $top_middle_id,
                        'order' => $widgetCount++,
                        'params' => '{"modulename":"siteevent_event","showContent_0":"","showContent_siteevent_event":["title","joinButton","inviteGuest","updateInfoButton","inviteRsvpButton","optionsButton","venue","startDate","endDate","location","hostName", "addToMyCalendar","shareOptions"],"profile_like_button":"0","columnHeight":"400","sitecontentcoverphotoChangeTabPosition":"1","contacts":"","showMemberLevelBasedPhoto":"1","emailme":"1","editFontColor":"0","contentFullWidth":"1","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-photo"}',
                    ));
                } else {

                    $db->insert('engine4_core_content', array(
                        'page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'sitecontentcoverphoto.content-cover-photo',
                        'parent_content_id' => $main_middle_id,
                        'order' => $widgetCount++,
                        'params' => '{"modulename":"siteevent_event","showContent_0":"","showContent_siteevent_event":["title","joinButton","inviteRsvpButton","optionsButton","venue","startDate","endDate","showrepeatinfo"],"showContent_sitegroup_group":"","showContent_sitepage_page":"","showContent_sitereview_listing_12":"","showContent_sitestore_store":"","profile_like_button":"1","columnHeight":"300","showMember":"1","memberCount":"8","onlyMemberWithPhoto":"1","sitecontentcoverphotoChangeTabPosition":"1","contacts":["1","2","3"],"emailme":"1","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-photo"}',
                    ));
                }
            } else {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteevent.list-information-profile',
                    'parent_content_id' => $main_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","showContent":["title","postedDate","ledBy","price","photo","photosCarousel","featuredLabel","sponsoredLabel","newLabel","description","reviewCreate","venueName","likeButton","showrepeatinfo"],"like_button":"1","actionLinks":"1","truncationDescription":"300","nomobile":"0","name":"siteevent.list-information-profile"}',
                ));
            }

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'core.container-tabs',
                'parent_content_id' => $main_middle_id,
                'order' => $widgetCount++,
                'params' => '{"max":"7"}',
            ));
            $tab_id = $db->lastInsertId('engine4_core_content');

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'advancedactivity.home-feeds',
                    'parent_content_id' => $tab_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"Updates","advancedactivity_tabs":["aaffeed"],"nomobile":"0"}'
                ));
            } else {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'activity.feed',
                    'parent_content_id' => $tab_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"Updates"}'
                ));
            }

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.editor-reviews-siteevent',
                'parent_content_id' => $tab_id,
                'order' => $widgetCount++,
                'params' => '{"titleEditor":"Review","titleOverview":"Overview","titleDescription":"Description","titleCount":"true","loaded_by_ajax":"1"}'
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.profile-members',
                'parent_content_id' => $tab_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Guests","titleCount":true,"loaded_by_ajax":"1"}'
            ));

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventrepeat')) {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteeventrepeat.occurrences',
                    'parent_content_id' => $tab_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"Occurrences","titleCount":true,"loaded_by_ajax":"1","guest_pictures":"1","guestCountActive":"13","guestCountDeactive":"18","guest_count_link":"1","rsvp_dropdown":"1","date_filter":"1","profile_links":["join","leave","accept-ignore","invite","review"],"autoloading":"1","itemCount":"10","nomobile":"0","name":"siteeventrepeat.occurrences"}'
                ));
            }

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.overview-siteevent',
                'parent_content_id' => $tab_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Overview","titleCount":"true","loaded_by_ajax":"1"}'
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.profile-announcements-siteevent',
                'parent_content_id' => $tab_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Announcements","titleCount":true}',
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.specification-siteevent',
                'parent_content_id' => $tab_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Information","titleCount":"true","loaded_by_ajax":"1"}'
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.photos-siteevent',
                'parent_content_id' => $tab_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Photos","titleCount":"true","loaded_by_ajax":"1"}'
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.video-siteevent',
                'parent_content_id' => $tab_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Videos","titleCount":"true","loaded_by_ajax":"1"}'
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.discussion-siteevent',
                'parent_content_id' => $tab_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Discussions","titleCount":"true","loaded_by_ajax":"1"}'
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.location-siteevent',
                'parent_content_id' => $tab_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Map","titleCount":"true"}'
            ));

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'siteevent.user-siteevent',
                'parent_content_id' => $tab_id,
                'order' => $widgetCount++,
                'params' => '{"title":"User Reviews","titleCount":"true","loaded_by_ajax":"1"}'
            ));

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventdocument')) {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteeventdocument.profile-siteeventdocuments',
                    'parent_content_id' => $tab_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"Documents","loaded_by_ajax":true}',
                ));
            }

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'core.profile-links',
                'parent_content_id' => $tab_id,
                'order' => $widgetCount++,
                'params' => '{"title":"Links","titleCount":"true"}'
            ));
        }
    }

    public function template2Profile() {

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        //EVENTS HOME PAGE CREATION
        $page_id = $this->profilePageCreate();

        if (!empty($page_id)) {

            $this->deleteCoreContent($page_id);

            $containerCount = 0;
            $widgetCount = 0;

            //TOP CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'order' => $containerCount++,
            ));
            $top_container_id = $db->lastInsertId();

            //INSERT TOP-MIDDLE
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_container_id,
                'order' => $containerCount++,
            ));
            $top_middle_id = $db->lastInsertId();

            //MAIN CONTAINER
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'main',
                'order' => $containerCount++,
                'params' => '',
            ));
            $main_container_id = $db->lastInsertId('engine4_core_content');

            //LEFT CONTAINER
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'left',
                'parent_content_id' => $main_container_id,
                'order' => $containerCount++,
                'params' => '',
            ));
            $left_container_id = $db->lastInsertId('engine4_core_content');

            //MIDDLE CONTAINER  
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $main_container_id,
                'order' => $containerCount++,
                'params' => '',
            ));
            $main_middle_id = $db->lastInsertId('engine4_core_content');


            $db->query('
                
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES

(' . $page_id . ', "widget", "siteevent.list-profile-breadcrumb", ' . $top_middle_id . ', 4, \'["[]"]\', NULL),
(' . $page_id . ', "widget", "siteevent.location-sidebar-siteevent", ' . $left_container_id . ', 7, \'{"title":"","titleCount":true,"showContent":["startEndDates","addToCalendar"]}\', NULL),
(' . $page_id . ', "widget", "siteevent.event-status", ' . $left_container_id . ', 8, \'{"title":"","titleCount":true}\', NULL),
(' . $page_id . ', "widget", "siteevent.profile-host-info", ' . $left_container_id . ', 9, \'{"placeWidget":"smallColumn","showInfo":["totalevent","hostDescription","socialLinks","messageHost","viewHostProfile"],"title":"Event Hosted By","nomobile":"0","name":"siteevent.profile-host-info"}\', NULL),
(' . $page_id . ', "widget", "siteevent.review-button", ' . $left_container_id . ', 10, \'{"title":""}\', NULL),
(' . $page_id . ', "widget", "siteevent.profile-event-buttons", ' . $left_container_id . ', 11, \'{"title":""}\', NULL),
(' . $page_id . ', "widget", "siteevent.write-siteevent", ' . $left_container_id . ', 12, \'{"title":"","titleCount":true}\', NULL),
(' . $page_id . ', "widget", "siteevent.information-siteevent", ' . $left_container_id . ', 13, \'{"title":"Information","titleCount":true,"showContent":["hostName","categoryLink","startDate","price","venueName","location","viewCount","likeCount","commentCount","memberCount","reviewCount","tags","rsvp","joinLink","likeButton","addtodiary"],"allowSocialSharing":"0","nomobile":"0","name":"siteevent.information-siteevent"}\', NULL),
(' . $page_id . ', "widget", "siteevent.quick-specification-siteevent", ' . $left_container_id . ', 14, \'{"title":"Quick Informations","titleCount":true}\', NULL),
(' . $page_id . ', "widget", "siteevent.about-editor-siteevent", ' . $left_container_id . ', 15, \'{"title":"About Me","titleCount":""}\', NULL),
(' . $page_id . ', "widget", "siteevent.overall-ratings", ' . $left_container_id . ', 16, \'{"title":"","titleCount":true,"show_rating":"both","ratingParameter":"1","nomobile":"0","name":"siteevent.overall-ratings"}\', NULL),
(' . $page_id . ', "widget", "siteevent.related-events-view-siteevent", ' . $left_container_id . ', 17, \'{"title":"More Events in %s","truncation":"40","titleCount":true,"statistics":["likeCount","reviewCount","memberCount"]}\', NULL),
(' . $page_id . ', "widget", "siteevent.userevent-siteevent", ' . $left_container_id . ', 18, \'{"title":"%s Events","truncation":"40","titleCount":true,"statistics":["likeCount","reviewCount"]}\', NULL),
(' . $page_id . ', "widget", "seaocore.social-share-buttons", ' . $left_container_id . ', 19, \'{"show_buttons":["facebook","twitter","plusgoogle","share"],"title":"","nomobile":"0","name":"seaocore.social-share-buttons"}\', NULL)');

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitetagcheckin')) {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitetagcheckin.checkinbutton-sitetagcheckin',
                    'parent_content_id' => $left_container_id,
                    'order' => 999,
                    'params' => '{"title":"","titleCount":true,"checkin_use":"1","checkin_button_sidebar":"1","checkin_button":"1","checkin_button_link":"Check-in here","checkin_icon":"1","checkin_verb":"Check-in","checkedinto_verb":"checked-into","checkin_your":"You\'ve checked-in here","checkin_total":"Total check-ins here","nomobile":"0","name":"sitetagcheckin.checkinbutton-sitetagcheckin"}'
                ));

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitetagcheckin.checkinuser-sitetagcheckin',
                    'parent_content_id' => $left_container_id,
                    'order' => 999,
                    'params' => '{"title":"","titleCount":true,"checkedin_heading":"People Here","checkedin_see_all_heading":"People who have been here","checkedin_users":"0","checkedin_user_photo":"1","checkedin_user_name":"0","checkedin_user_checkedtime":"0","checkedin_item_count":"5","nomobile":"0","name":"sitetagcheckin.checkinuser-sitetagcheckin"}'
                ));
            } else {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteevent.ads-plugin-siteevent',
                    'parent_content_id' => $left_container_id,
                    'order' => 999,
                    'params' => '{"title":"Check-in here","titleCount":"true","pluginName":"sitetagcheckin","nomobile":"1"}',
                ));
            }

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitecontentcoverphoto')) {

                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('spectacular')) {
                    $db->query('DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`name` = "seaocore.social-share-buttons" AND `engine4_core_content`.`page_id` = "' . $page_id . '" LIMIT 1');
                    $db->query('DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`name` = "siteevent.add-to-my-calendar-siteevent" AND `engine4_core_content`.`page_id` = "' . $page_id . '" LIMIT 1');
                    $db->query('DELETE FROM `engine4_core_content` WHERE `engine4_core_content`.`name` = "siteevent.list-profile-breadcrumb" AND `engine4_core_content`.`page_id` = "' . $page_id . '" LIMIT 1');

                    $db->insert('engine4_core_content', array(
                        'page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'sitecontentcoverphoto.content-cover-photo',
                        'parent_content_id' => $top_middle_id,
                        'order' => $widgetCount++,
                        'params' => '{"modulename":"siteevent_event","showContent_0":"","showContent_siteevent_event":["title","joinButton","inviteGuest","updateInfoButton","inviteRsvpButton","optionsButton","venue","startDate","endDate","location","hostName", "addToMyCalendar","shareOptions"],"profile_like_button":"0","columnHeight":"400","sitecontentcoverphotoChangeTabPosition":"1","contacts":"","showMemberLevelBasedPhoto":"1","emailme":"1","editFontColor":"0","contentFullWidth":"1","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-photo"}',
                    ));
                } else {

                    $db->insert('engine4_core_content', array(
                        'page_id' => $page_id,
                        'type' => 'widget',
                        'name' => 'sitecontentcoverphoto.content-cover-photo',
                        'parent_content_id' => $main_middle_id,
                        'order' => $widgetCount++,
                        'params' => '{"modulename":"siteevent_event","showContent_0":"","showContent_siteevent_event":["title","joinButton","inviteRsvpButton","optionsButton","venue","startDate","endDate","showrepeatinfo"],"showContent_sitegroup_group":"","showContent_sitepage_page":"","showContent_sitereview_listing_12":"","showContent_sitestore_store":"","profile_like_button":"1","columnHeight":"300","showMember":"1","memberCount":"8","onlyMemberWithPhoto":"1","sitecontentcoverphotoChangeTabPosition":"1","contacts":["1","2","3"],"emailme":"1","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-photo"}',
                    ));
                }
            } else {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteevent.list-information-profile',
                    'parent_content_id' => $main_middle_id,
                    'order' => $widgetCount++,
                    'params' => '{"title":"","showContent":["title","postedDate","ledBy","price","photo","photosCarousel","featuredLabel","sponsoredLabel","newLabel","description","reviewCreate","venueName","likeButton","showrepeatinfo"],"like_button":"1","actionLinks":"1","truncationDescription":"300","nomobile":"0","name":"siteevent.list-information-profile"}',
                ));
            }

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'core.container-tabs',
                'parent_content_id' => $main_middle_id,
                'order' => 22,
                'params' => '{"max":"7","title":"","nomobile":"0","name":"core.container-tabs"}',
            ));
            $tab_id = $db->lastInsertId('engine4_core_content');

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'advancedactivity.home-feeds',
                    'parent_content_id' => $tab_id,
                    'order' => 23,
                    'params' => '{"title":"What\'s New","advancedactivity_tabs":["aaffeed"],"showScrollTopButton":"1","nomobile":"0","name":"advancedactivity.home-feeds"}'
                ));
            } else {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteevent.ads-plugin-siteevent',
                    'parent_content_id' => $tab_id,
                    'order' => 23,
                    'params' => '{"title":"What\'s New","titleCount":"true","pluginName":"advancedactivity","nomobile":"1"}'
                ));
            }

            $db->query('
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES                
(' . $page_id . ', "widget", "siteevent.editor-reviews-siteevent", ' . $tab_id . ', 24, \'{"titleEditor":"Review","titleOverview":"Overview","titleDescription":"Description","titleCount":"","loaded_by_ajax":1}\', NULL),
(' . $page_id . ', "widget", "siteevent.profile-members", ' . $tab_id . ', 25, \'{"title":"Guests", "titleCount":true, "loaded_by_ajax":1}\', NULL)');

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventrepeat')) {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteeventrepeat.occurrences',
                    'parent_content_id' => $tab_id,
                    'order' => 26,
                    'params' => '{"title":"Event Occurrences","titleCount":true,"loaded_by_ajax":"1","occurrence_date":"1","guest_pictures":"1","guestCount":"5","guest_count_link":"1","rsvp_dropdown":"1","date_filter":"1","profile_links":["join","leave","request","accept-ignore","invite","review"],"viewmore":"1","itemCount":"10","nomobile":"0","name":"siteeventrepeat.occurrences"}'
                ));
            } else {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteevent.ads-plugin-siteevent',
                    'parent_content_id' => $tab_id,
                    'order' => 26,
                    'params' => '{"title":"Occurrences","titleCount":"true","pluginName":"siteeventrepeat","nomobile":"1"}',
                ));
            }

            $db->query('
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES   
(' . $page_id . ', "widget", "siteevent.photos-siteevent", ' . $tab_id . ', 27, \'{"title":"Photos","titleCount":true,"loaded_by_ajax":1}\', NULL),
(' . $page_id . ', "widget", "siteevent.video-siteevent", ' . $tab_id . ', 28, \'{"title":"Videos","titleCount":true,"loaded_by_ajax":1}\', NULL),
(' . $page_id . ', "widget", "siteevent.overview-siteevent", ' . $tab_id . ', 29, \'{"title":"Overview  ","titleCount":true,"loaded_by_ajax":"1","showAfterEditorReview":"1","showComments":"0","nomobile":"0","name":"siteevent.overview-siteevent"}\', NULL),
(' . $page_id . ', "widget", "siteevent.user-siteevent", ' . $tab_id . ', 30, \'{"title":"User Reviews","titleCount":"true","loaded_by_ajax":"1","itemProsConsCount":"3","itemReviewsCount":"3","nomobile":"0","name":"siteevent.user-siteevent"}\', NULL),
(' . $page_id . ', "widget", "siteevent.specification-siteevent", ' . $tab_id . ', 31, \'{"title":"Information","titleCount":true,"loaded_by_ajax":"1","nomobile":"0","name":"siteevent.specification-siteevent"}\', NULL),    
(' . $page_id . ', "widget", "siteevent.location-siteevent", ' . $tab_id . ', 32, \'{"title":"Map","titleCount":true}\', NULL)');

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventdocument')) {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteeventdocument.profile-siteeventdocuments',
                    'parent_content_id' => $tab_id,
                    'order' => 33,
                    'params' => '{"title":"Documents","loaded_by_ajax":true}',
                ));
            } else {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteevent.ads-plugin-siteevent',
                    'parent_content_id' => $tab_id,
                    'order' => 33,
                    'params' => '{"title":"Documents","titleCount":"true","pluginName":"siteeventdocument","nomobile":"1"}',
                ));
            }

            $db->query('
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES 
(' . $page_id . ', "widget", "siteevent.discussion-siteevent", ' . $tab_id . ', 34, \'{"title":"Discussions","titleCount":true,"loaded_by_ajax":1}\', NULL),
(' . $page_id . ', "widget", "siteevent.profile-announcements-siteevent", ' . $tab_id . ', 35, \'{"title":"Announcements","titleCount":true}\', NULL),
(' . $page_id . ', "widget", "core.profile-links", ' . $tab_id . ', 36, \'{"title":"Links","titleCount":true}\', NULL),
(' . $page_id . ', "widget", "siteevent.profile-members-sidebar", ' . $tab_id . ', 37, \'{"title":"Guests","titleCount":true,"join_filters":["2","1"],"show_seeall":"1","itemCount":"","nomobile":"0","name":"siteevent.profile-members-sidebar"}\', NULL);

');
        }
    }

    public function template3Profile() {

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        //EVENTS HOME PAGE CREATION
        $page_id = $this->profilePageCreate();

        if (!empty($page_id)) {

            $this->deleteCoreContent($page_id);

            $containerCount = 0;
            $widgetCount = 0;

            //TOP CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'order' => $containerCount++,
            ));
            $top_container_id = $db->lastInsertId();

            //INSERT TOP-MIDDLE
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_container_id,
                'order' => $containerCount++,
            ));
            $top_middle_id = $db->lastInsertId();

            //MAIN CONTAINER
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'main',
                'order' => $containerCount++,
                'params' => '',
            ));
            $main_container_id = $db->lastInsertId('engine4_core_content');

            //RIGHT CONTAINER
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'right',
                'parent_content_id' => $main_container_id,
                'order' => $containerCount++,
                'params' => '',
            ));
            $right_container_id = $db->lastInsertId('engine4_core_content');

            //MIDDLE CONTAINER  
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $main_container_id,
                'order' => $containerCount++,
                'params' => '',
            ));
            $main_middle_id = $db->lastInsertId('engine4_core_content');

            $db->query('
                
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES
(' . $page_id . ', "widget", "siteevent.list-profile-breadcrumb", ' . $top_middle_id . ', 4, \'["[]"]\', NULL),
(' . $page_id . ', "widget", "siteevent.list-information-profile", ' . $main_middle_id . ', 7, \'{"title":"","showContent":["title","postedDate","ledBy","price","photo","photosCarousel","featuredLabel","sponsoredLabel","newLabel","description","reviewCreate","venueName","likeButton","showrepeatinfo"],"like_button":"1","actionLinks":"1","truncationDescription":"300","nomobile":"0","name":"siteevent.list-information-profile"}\', NULL)');

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'core.container-tabs',
                'parent_content_id' => $main_middle_id,
                'order' => 22,
                'params' => '{"max":"7","title":"","nomobile":"0","name":"core.container-tabs"}',
            ));
            $tab_id = $db->lastInsertId('engine4_core_content');

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'advancedactivity.home-feeds',
                    'parent_content_id' => $tab_id,
                    'order' => 9,
                    'params' => '{"title":"Updates","advancedactivity_tabs":["aaffeed"],"showScrollTopButton":"1","nomobile":"0","name":"advancedactivity.home-feeds"}'
                ));
            } else {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteevent.ads-plugin-siteevent',
                    'parent_content_id' => $tab_id,
                    'order' => 9,
                    'params' => '{"title":"Updates","titleCount":"true","pluginName":"advancedactivity","nomobile":"1"}'
                ));
            }

            $db->query('
                
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES    
(' . $page_id . ', "widget", "siteevent.editor-reviews-siteevent", ' . $tab_id . ', 10, \'{"titleEditor":"Review","titleOverview":"Overview","titleDescription":"Description","titleCount":"","loaded_by_ajax":1}\', NULL),
(' . $page_id . ', "widget", "siteevent.profile-members", ' . $tab_id . ', 11, \'{"title":"Guests","titleCount":true,"loaded_by_ajax":1}\', NULL)');

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventrepeat')) {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteeventrepeat.occurrences',
                    'parent_content_id' => $tab_id,
                    'order' => 12,
                    'params' => '{"title":"Occurrences","titleCount":true,"loaded_by_ajax":1}'
                ));
            } else {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteevent.ads-plugin-siteevent',
                    'parent_content_id' => $tab_id,
                    'order' => 12,
                    'params' => '{"title":"Occurrences","titleCount":"true","pluginName":"siteeventrepeat","nomobile":"1"}',
                ));
            }

            $db->query('
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES   
(' . $page_id . ', "widget", "siteevent.photos-siteevent", ' . $tab_id . ', 13, \'{"title":"Photos","titleCount":true,"loaded_by_ajax":1}\', NULL),
(' . $page_id . ', "widget", "siteevent.video-siteevent", ' . $tab_id . ', 14, \'{"title":"Videos","titleCount":true,"loaded_by_ajax":1}\', NULL),
(' . $page_id . ', "widget", "siteevent.user-siteevent", ' . $tab_id . ', 15, \'{"title":"User Reviews","titleCount":"true","loaded_by_ajax":1}\', NULL),
(' . $page_id . ', "widget", "siteevent.overview-siteevent", ' . $tab_id . ', 16, \'{"title":"Overview  ","titleCount":true,"loaded_by_ajax":"1","showAfterEditorReview":"1","showComments":"0","nomobile":"0","name":"siteevent.overview-siteevent"}\', NULL),
(' . $page_id . ', "widget", "siteevent.specification-siteevent", ' . $tab_id . ', 17, \'{"title":"Info","titleCount":true,"loaded_by_ajax":"1","nomobile":"0","name":"siteevent.specification-siteevent"}\', NULL),
(' . $page_id . ', "widget", "siteevent.location-siteevent", ' . $tab_id . ', 18, \'{"title":"Map","titleCount":true}\', NULL),
(' . $page_id . ', "widget", "siteevent.discussion-siteevent", ' . $tab_id . ', 19, \'{"title":"Discussions","titleCount":true,"loaded_by_ajax":1}\', NULL)');

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventdocument')) {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteeventdocument.profile-siteeventdocuments',
                    'parent_content_id' => $tab_id,
                    'order' => 20,
                    'params' => '{"title":"Documents","loaded_by_ajax":true}',
                ));
            } else {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteevent.ads-plugin-siteevent',
                    'parent_content_id' => $tab_id,
                    'order' => 20,
                    'params' => '{"title":"Documents","titleCount":"true","pluginName":"siteeventdocument","nomobile":"1"}',
                ));
            }

            $db->query('
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES 
(' . $page_id . ', "widget", "siteevent.event-status", ' . $right_container_id . ', 22, \'{"title":"","titleCount":true}\', NULL),
(' . $page_id . ', "widget", "siteevent.location-sidebar-siteevent", ' . $right_container_id . ', 23, \'{"title":"","titleCount":true,"showContent":["startDate","endDate"],"height":"200","nomobile":"0","name":"siteevent.location-sidebar-siteevent"}\', NULL),
(' . $page_id . ', "widget", "siteevent.profile-announcements-siteevent", ' . $right_container_id . ', 24, \'{"title":"Announcements","titleCount":true}\', NULL),
(' . $page_id . ', "widget", "siteevent.profile-members-sidebar", ' . $right_container_id . ', 25, \'{"title":"Guests","titleCount":true,"join_filters":["2","1","0"],"show_seeall":"1","itemCount":"3","nomobile":"0","name":"siteevent.profile-members-sidebar"}\', NULL),
(' . $page_id . ', "widget", "siteevent.diary-add-link", ' . $right_container_id . ', 26, \'{"title":""}\', NULL),
(' . $page_id . ', "widget", "siteevent.profile-event-buttons", ' . $right_container_id . ', 27, \'{"title":""}\', NULL),
(' . $page_id . ', "widget", "siteevent.review-button", ' . $right_container_id . ', 28, \'{"title":""}\', NULL),
(' . $page_id . ', "widget", "siteevent.overall-ratings", ' . $right_container_id . ', 29, \'{"title":"","titleCount":true,"show_rating":"both","ratingParameter":"1","nomobile":"0","name":"siteevent.overall-ratings"}\', NULL),
(' . $page_id . ', "widget", "siteevent.about-editor-siteevent", ' . $right_container_id . ', 30, \'{"title":"About Me","titleCount":""}\', NULL),
(' . $page_id . ', "widget", "siteevent.write-siteevent", ' . $right_container_id . ', 31, \'{"title":"","titleCount":true}\', NULL),
(' . $page_id . ', "widget", "siteevent.quick-specification-siteevent", ' . $right_container_id . ', 32, \'{"title":"Quick Informations","titleCount":true}\', NULL),
(' . $page_id . ', "widget", "siteevent.profile-host-info", ' . $right_container_id . ', 33, \'{"placeWidget":"smallColumn","showInfo":["totalevent","hostDescription","socialLinks","messageHost","viewHostProfile"],"title":"Event Hosted By","nomobile":"0","name":"siteevent.profile-host-info"}\', NULL),
(' . $page_id . ', "widget", "siteevent.information-siteevent", ' . $right_container_id . ', 34, \'{"title":"Information","titleCount":true,"showContent":["hostName","categoryLink","startDate","price","location","directionLink","viewCount","likeCount","commentCount","memberCount","reviewCount","tags","joinLink","likeButton","addtodiary"],"allowSocialSharing":"0","nomobile":"0","name":"siteevent.information-siteevent"}\', NULL),
(' . $page_id . ', "widget", "siteevent.share", ' . $right_container_id . ', 35, \'{"title":"Share and Report ","titleCount":true,"options":["siteShare","friend","report","print","socialShare"],"allowSocialSharing":"0","nomobile":"0","name":"siteevent.share"}\', NULL),
(' . $page_id . ', "widget", "seaocore.people-like", ' . $right_container_id . ', 36, \'{"itemCount":"3","title":"","nomobile":"0","name":"seaocore.people-like"}\', NULL),
(' . $page_id . ', "widget", "siteevent.userevent-siteevent", ' . $right_container_id . ', 37, \'{"title":"%s Events","truncation":"40","titleCount":true,"statistics":["likeCount","reviewCount"]}\', NULL),
(' . $page_id . ', "widget", "siteevent.related-events-view-siteevent", ' . $right_container_id . ', 38, \'{"title":"More Events in %s","truncation":"40","titleCount":true,"statistics":["likeCount","reviewCount","memberCount"]}\', NULL)');

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitetagcheckin')) {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitetagcheckin.checkinbutton-sitetagcheckin',
                    'parent_content_id' => $right_container_id,
                    'order' => 999,
                    'params' => '{"title":"","titleCount":true,"checkin_use":"1","checkin_button_sidebar":"1","checkin_button":"1","checkin_button_link":"Check-in here","checkin_icon":"1","checkin_verb":"Check-in","checkedinto_verb":"checked-into","checkin_your":"You\'ve checked-in here","checkin_total":"Total check-ins here","nomobile":"0","name":"sitetagcheckin.checkinbutton-sitetagcheckin"}'
                ));

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitetagcheckin.checkinuser-sitetagcheckin',
                    'parent_content_id' => $right_container_id,
                    'order' => 999,
                    'params' => '{"title":"","titleCount":true,"checkedin_heading":"People Here","checkedin_see_all_heading":"People who have been here","checkedin_users":"0","checkedin_user_photo":"1","checkedin_user_name":"0","checkedin_user_checkedtime":"0","checkedin_item_count":"5","nomobile":"0","name":"sitetagcheckin.checkinuser-sitetagcheckin"}'
                ));
            } else {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteevent.ads-plugin-siteevent',
                    'parent_content_id' => $right_container_id,
                    'order' => 999,
                    'params' => '{"title":"Check-in here","titleCount":"true","pluginName":"sitetagcheckin","nomobile":"1"}',
                ));
            }
        }
    }

    public function template4Profile() {

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        //EVENTS HOME PAGE CREATION
        $page_id = $this->profilePageCreate();

        if (!empty($page_id)) {

            $this->deleteCoreContent($page_id);

            $containerCount = 0;
            $widgetCount = 0;

            //TOP CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'order' => $containerCount++,
            ));
            $top_container_id = $db->lastInsertId();

            //INSERT TOP-MIDDLE
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_container_id,
                'order' => $containerCount++,
            ));
            $top_middle_id = $db->lastInsertId();

            //MAIN CONTAINER
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'main',
                'order' => $containerCount++,
                'params' => '',
            ));
            $main_container_id = $db->lastInsertId('engine4_core_content');

            //RIGHT CONTAINER
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'right',
                'parent_content_id' => $main_container_id,
                'order' => $containerCount++,
                'params' => '',
            ));
            $right_container_id = $db->lastInsertId('engine4_core_content');

            //MIDDLE CONTAINER  
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $main_container_id,
                'order' => $containerCount++,
                'params' => '',
            ));
            $main_middle_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'core.html-block',
                'parent_content_id' => $top_middle_id,
                'order' => 4,
                'params' => '{"title":"","data":"<style type=\"text\/css\">\r\n.generic_layout_container > h3 {\r\n    background-color: #3A3A3A;\r\n    border-radius: 0;\r\n    color: #FFFFFF;\r\n    margin-bottom: 6px;\r\n}\r\n<\/style>","nomobile":"0","name":"core.html-block"}',
            ));

            $db->query('
 
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES
(' . $page_id . ', "widget", "siteevent.list-profile-breadcrumb", ' . $top_middle_id . ', 5, \'["[]"]\', NULL)');

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitecontentcoverphoto')) {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitecontentcoverphoto.content-cover-photo',
                    'parent_content_id' => $main_middle_id,
                    'order' => 6,
                    'params' => '{"modulename":"siteevent_event","showContent_0":"","showContent_siteevent_event":["title","joinButton","inviteGuest","updateInfoButton","inviteRsvpButton","optionsButton","venue","startDate","endDate","location","price"],"showContent_sitegroup_group":"","showContent_sitepage_page":"","showContent_sitereview_listing_12":"","showContent_sitestore_store":"","profile_like_button":"1","columnHeight":"350","showMember":"1","memberCount":"8","onlyMemberWithPhoto":"1","sitecontentcoverphotoChangeTabPosition":"1","contacts":["1","2","3"],"emailme":"1","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-photo"}',
                ));
            } else {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteevent.ads-plugin-siteevent',
                    'parent_content_id' => $main_middle_id,
                    'order' => 6,
                    'params' => '{"title":"Cover Photo","titleCount":"true","pluginName":"sitecontentcoverphoto","nomobile":"1"}',
                ));
            }

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventrepeat')) {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteeventrepeat.occurrences',
                    'parent_content_id' => $main_middle_id,
                    'order' => 9,
                    'params' => '{"title":"Event Occurrences","titleCount":true,"loaded_by_ajax":"0","guest_pictures":"1","guestCountActive":"13","guestCountDeactive":"18","guest_count_link":"1","rsvp_dropdown":"1","date_filter":"1","profile_links":["join","leave","accept-ignore","invite","review"],"autoloading":"0","itemCount":"5","nomobile":"0","name":"siteeventrepeat.occurrences"}'
                ));
            } else {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteevent.ads-plugin-siteevent',
                    'parent_content_id' => $main_middle_id,
                    'order' => 9,
                    'params' => '{"title":"Occurrences","titleCount":"true","pluginName":"siteeventrepeat","nomobile":"1"}',
                ));
            }

            $db->query('
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES       
    
(' . $page_id . ', "widget", "siteevent.specification-siteevent", ' . $main_middle_id . ', 10, \'{"title":"Information","titleCount":true,"loaded_by_ajax":"0","nomobile":"0","name":"siteevent.specification-siteevent"}\', NULL),
(' . $page_id . ', "widget", "siteevent.editor-reviews-siteevent", ' . $main_middle_id . ', 11, \'{"titleEditor":"Event Details","titleOverview":"Event Details","titleDescription":"Event Details","titleCount":"","loaded_by_ajax":"0","title":"","show_slideshow":"1","slideshow_height":"400","slideshow_width":"550","showCaption":"1","showButtonSlide":"1","mouseEnterEvent":"0","thumbPosition":"bottom","autoPlay":"0","slidesLimit":"20","captionTruncation":"200","showComments":"0","nomobile":"0","name":"siteevent.editor-reviews-siteevent"}\', NULL),
(' . $page_id . ', "widget", "siteevent.overview-siteevent", ' . $main_middle_id . ', 11, \'{"title":"Overview","titleCount":true,"loaded_by_ajax":"0","showAfterEditorReview":"1","showComments":"0","nomobile":"0","name":"siteevent.overview-siteevent"}\', NULL),
(' . $page_id . ', "widget", "siteevent.photos-siteevent", ' . $main_middle_id . ', 12, \'{"title":"Event Photos","titleCount":true,"loaded_by_ajax":"0","itemCount":"8","nomobile":"0","name":"siteevent.photos-siteevent"}\', NULL),
(' . $page_id . ', "widget", "siteevent.video-siteevent", ' . $main_middle_id . ', 13, \'{"title":"Event Videos","titleCount":true,"loaded_by_ajax":"0","count":"8","truncation":"35","nomobile":"0","name":"siteevent.video-siteevent"}\', NULL),
(' . $page_id . ', "widget", "siteevent.discussion-siteevent", ' . $main_middle_id . ', 14, \'{"title":"Event Discussions","titleCount":true,"loaded_by_ajax":"0","nomobile":"0","name":"siteevent.discussion-siteevent"}\', NULL)');

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventdocument')) {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteeventdocument.profile-siteeventdocuments',
                    'parent_content_id' => $main_middle_id,
                    'order' => 15,
                    'params' => '{"title":"Event Documents","loaded_by_ajax":"0","statistics":["viewCount","likeCount","commentCount"],"truncationTitle":"55","nomobile":"0","name":"siteeventdocument.profile-siteeventdocuments"}',
                ));
            } else {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteevent.ads-plugin-siteevent',
                    'parent_content_id' => $main_middle_id,
                    'order' => 15,
                    'params' => '{"title":"Documents","titleCount":"true","pluginName":"siteeventdocument","nomobile":"1"}',
                ));
            }

            $db->query('
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES 
(' . $page_id . ', "widget", "siteevent.user-siteevent", ' . $main_middle_id . ', 16, \'{"title":"User Reviews","titleCount":"true","loaded_by_ajax":"0","itemProsConsCount":"3","itemReviewsCount":"3","nomobile":"0","name":"siteevent.user-siteevent"}\', NULL),
(' . $page_id . ', "widget", "siteevent.location-sidebar-siteevent", ' . $right_container_id . ', 18, \'{"title":"When and Where","titleCount":true,"showContent":["startDate","endDate"],"height":"150","nomobile":"0","name":"siteevent.location-sidebar-siteevent"}\', NULL),
(' . $page_id . ', "widget", "siteevent.information-siteevent", ' . $right_container_id . ', 19, \'{"title":"","titleCount":true,"showContent":["ledBy","price","viewCount","likeCount","commentCount","reviewCount","tags","rsvp"],"allowSocialSharing":"0","nomobile":"0","name":"siteevent.information-siteevent"}\', NULL),
(' . $page_id . ', "widget", "siteevent.ads-plugin-siteevent", ' . $right_container_id . ', 20, \'{"title":"Community Ads","titleCount":"true","pluginName":"communityad","nomobile":"1"}\', NULL),
(' . $page_id . ', "widget", "siteevent.review-button", ' . $right_container_id . ', 21, \'{"title":"","seeAllReviews":"1","nomobile":"0","name":"siteevent.review-button"}\', NULL),
(' . $page_id . ', "widget", "siteevent.editor-profile-info", ' . $right_container_id . ', 22, \'{"title":"About Me","titleCount":""}\', NULL),
(' . $page_id . ', "widget", "siteevent.event-status", ' . $right_container_id . ', 23, \'{"title":"","titleCount":true}\', NULL),
(' . $page_id . ', "widget", "siteevent.quick-specification-siteevent", ' . $right_container_id . ', 24, \'{"title":"Quick Information","titleCount":true,"show_specificationlink":"0","show_specificationtext":"More Informations","itemCount":"5","nomobile":"0","name":"siteevent.quick-specification-siteevent"}\', NULL),
(' . $page_id . ', "widget", "siteevent.profile-host-info", ' . $right_container_id . ', 25, \'{"placeWidget":"smallColumn","showInfo":["totalevent","totalguest","totalrating","hostDescription","socialLinks","messageHost","viewHostProfile"],"title":"Host","nomobile":"0","name":"siteevent.profile-host-info"}\', NULL),
(' . $page_id . ', "widget", "siteevent.userevent-siteevent", ' . $right_container_id . ', 26, \'{"title":"More Events Hosted By %s","titleCount":true,"statistics":["likeCount","reviewCount"],"show":"host","eventType":"0","category_id":"0","subcategory_id":null,"hidden_category_id":"","hidden_subcategory_id":"","hidden_subsubcategory_id":"","eventInfo":["startDate","location","directionLink"],"showEventType":"upcoming","titlePosition":"1","viewType":"listview","columnWidth":"227","columnHeight":"227","count":"2","truncationLocation":"40","truncation":"40","ratingType":"rating_avg","nomobile":"0","name":"siteevent.userevent-siteevent"}\', NULL),
(' . $page_id . ', "widget", "siteevent.related-events-view-siteevent", ' . $right_container_id . ', 27, \'{"title":"More Events in %s","titleCount":true,"statistics":["likeCount","reviewCount","memberCount"],"eventType":"0","related":"categories","viewType":"listview","columnWidth":"227","columnHeight":"227","showEventType":"upcoming","titlePosition":"1","eventInfo":["startDate","location","directionLink"],"itemCount":"2","truncationLocation":"20","truncation":"40","ratingType":"rating_avg","detactLocation":"0","defaultLocationDistance":"1000","nomobile":"0","name":"siteevent.related-events-view-siteevent"}\', NULL),
(' . $page_id . ', "widget", "seaocore.social-share-buttons", ' . $right_container_id . ', 28, \'{"show_buttons":["facebook","twitter","linkedin","plusgoogle","share"],"title":"","nomobile":"0","name":"seaocore.social-share-buttons"}\', NULL),
(' . $page_id . ', "widget", "seaocore.people-like", ' . $right_container_id . ', 29, \'{"itemCount":"3","title":"","nomobile":"0","name":"seaocore.people-like"}\', NULL)');

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitetagcheckin')) {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitetagcheckin.checkinbutton-sitetagcheckin',
                    'parent_content_id' => $right_container_id,
                    'order' => 999,
                    'params' => '{"title":"","titleCount":true,"checkin_use":"1","checkin_button_sidebar":"1","checkin_button":"1","checkin_button_link":"Check-in here","checkin_icon":"1","checkin_verb":"Check-in","checkedinto_verb":"checked-into","checkin_your":"You\'ve checked-in here","checkin_total":"Total check-ins here","nomobile":"0","name":"sitetagcheckin.checkinbutton-sitetagcheckin"}'
                ));

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitetagcheckin.checkinuser-sitetagcheckin',
                    'parent_content_id' => $right_container_id,
                    'order' => 999,
                    'params' => '{"title":"","titleCount":true,"checkedin_heading":"People Here","checkedin_see_all_heading":"People who have been here","checkedin_users":"0","checkedin_user_photo":"1","checkedin_user_name":"0","checkedin_user_checkedtime":"0","checkedin_item_count":"5","nomobile":"0","name":"sitetagcheckin.checkinuser-sitetagcheckin"}'
                ));
            } else {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteevent.ads-plugin-siteevent',
                    'parent_content_id' => $right_container_id,
                    'order' => 999,
                    'params' => '{"title":"Check-in here","titleCount":"true","pluginName":"sitetagcheckin","nomobile":"1"}',
                ));
            }
        }
    }

    public function template5Profile() {

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        //EVENTS HOME PAGE CREATION
        $page_id = $this->profilePageCreate();

        if (!empty($page_id)) {

            $this->deleteCoreContent($page_id);

            $containerCount = 0;
            $widgetCount = 0;

            //TOP CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'order' => $containerCount++,
            ));
            $top_container_id = $db->lastInsertId();

            //INSERT TOP-MIDDLE
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_container_id,
                'order' => $containerCount++,
            ));
            $top_middle_id = $db->lastInsertId();

            //MAIN CONTAINER
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'main',
                'order' => $containerCount++,
                'params' => '',
            ));
            $main_container_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'left',
                'parent_content_id' => $main_container_id,
                'order' => $containerCount++,
                'params' => '',
            ));
            $left_container_id = $db->lastInsertId('engine4_core_content');

            //RIGHT CONTAINER
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'right',
                'parent_content_id' => $main_container_id,
                'order' => $containerCount++,
                'params' => '',
            ));
            $right_container_id = $db->lastInsertId('engine4_core_content');

            //MIDDLE CONTAINER  
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $main_container_id,
                'order' => $containerCount++,
                'params' => '',
            ));
            $main_middle_id = $db->lastInsertId('engine4_core_content');

            $db->query('
                
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES
(' . $page_id . ', "widget", "siteevent.list-profile-breadcrumb", ' . $top_middle_id . ', 4, \'["[]"]\', NULL),
(' . $page_id . ', "widget", "siteevent.slideshow-list-photo", ' . $left_container_id . ', 7, \'{"title":"","titleCount":true,"slideshow_height":"200","slideshow_width":"200","showCaption":"1","showButtonSlide":"0","mouseEnterEvent":"0","thumbPosition":"bottom","autoPlay":"0","slidesLimit":"20","captionTruncation":"200","nomobile":"0","name":"siteevent.slideshow-list-photo"}\', NULL),
(' . $page_id . ', "widget", "siteevent.event-status", ' . $left_container_id . ', 8, \'{"title":"","titleCount":true}\', NULL),    
(' . $page_id . ', "widget", "siteevent.write-siteevent", ' . $left_container_id . ', 8, \'{"title":"","titleCount":true}\', NULL),
    
(' . $page_id . ', "widget", "siteevent.profile-event-buttons", ' . $left_container_id . ', 9, \'{"title":""}\', NULL),
(' . $page_id . ', "widget", "siteevent.profile-announcements-siteevent", ' . $left_container_id . ', 10, \'{"title":"","titleCount":true,"showTitle":"1","itemCount":"3","nomobile":"0","name":"siteevent.profile-announcements-siteevent"}\', NULL),
(' . $page_id . ', "widget", "siteevent.review-button", ' . $left_container_id . ', 11, \'{"title":"","seeAllReviews":"1","nomobile":"0","name":"siteevent.review-button"}\', NULL),
(' . $page_id . ', "widget", "siteevent.overall-ratings", ' . $left_container_id . ', 12, \'{"title":"Reviews","titleCount":true}\', NULL),
(' . $page_id . ', "widget", "siteevent.profile-members-sidebar", ' . $left_container_id . ', 13, \'{"title":"Event Guests","titleCount":true,"join_filters":["2","1"],"show_seeall":"1","itemCount":"","nomobile":"0","name":"siteevent.profile-members-sidebar"}\', NULL),
(' . $page_id . ', "widget", "siteevent.information-siteevent", ' . $left_container_id . ', 14, \'{"title":"Event Information ","titleCount":true,"showContent":["startDate","price","venueName","location","directionLink","viewCount","likeCount","commentCount","memberCount","reviewCount","joinLink","likeButton"],"allowSocialSharing":"0","nomobile":"0","name":"siteevent.information-siteevent"}\', NULL),
(' . $page_id . ', "widget", "siteevent.options-siteevent", ' . $left_container_id . ', 15, \'{"title":"","titleCount":true,"nomobile":"0","name":"siteevent.options-siteevent"}\', NULL),
(' . $page_id . ', "widget", "seaocore.social-share-buttons", ' . $left_container_id . ', 16, \'{"show_buttons":["facebook","twitter","linkedin","plusgoogle","share"],"title":"","nomobile":"0","name":"seaocore.social-share-buttons"}\', NULL)');

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitecontentcoverphoto')) {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitecontentcoverphoto.content-cover-photo',
                    'parent_content_id' => $main_middle_id,
                    'order' => 6,
                    'params' => '{"modulename":"siteevent_event","showContent_0":"","showContent_siteevent_event":["title","joinButton","inviteRsvpButton","optionsButton","venue","startDate","location"],"showContent_sitegroup_group":"","showContent_sitepage_page":"","showContent_sitereview_listing_12":"","showContent_sitestore_store":"","profile_like_button":"1","columnHeight":"300","showMember":"1","memberCount":"8","onlyMemberWithPhoto":"1","sitecontentcoverphotoChangeTabPosition":"1","contacts":["1","2","3"],"emailme":"1","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-photo"}',
                ));
            } else {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteevent.ads-plugin-siteevent',
                    'parent_content_id' => $main_middle_id,
                    'order' => 19,
                    'params' => '{"title":"Cover Photo","titleCount":"true","pluginName":"sitecontentcoverphoto","nomobile":"1"}',
                ));
            }

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'core.container-tabs',
                'parent_content_id' => $main_middle_id,
                'order' => 20,
                'params' => '{"max":"4","title":"","nomobile":"0","name":"core.container-tabs"}',
            ));
            $tab_id = $db->lastInsertId('engine4_core_content');

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'advancedactivity.home-feeds',
                    'parent_content_id' => $tab_id,
                    'order' => 21,
                    'params' => '{"title":"What\'s New","advancedactivity_tabs":["aaffeed"],"showScrollTopButton":"1","nomobile":"0","name":"advancedactivity.home-feeds"}'
                ));
            } else {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteevent.ads-plugin-siteevent',
                    'parent_content_id' => $tab_id,
                    'order' => 21,
                    'params' => '{"title":"What\'s New","titleCount":"true","pluginName":"advancedactivity","nomobile":"1"}'
                ));
            }

            $db->query('
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES         
(' . $page_id . ', "widget", "siteevent.editor-reviews-siteevent", ' . $tab_id . ', 22, \'{"titleEditor":"Review","titleOverview":"Overview","titleDescription":"Description","titleCount":"","loaded_by_ajax":"1","title":"","show_slideshow":"0","slideshow_height":"400","slideshow_width":"600","showCaption":"1","showButtonSlide":"1","mouseEnterEvent":"0","thumbPosition":"bottom","autoPlay":"0","slidesLimit":"20","captionTruncation":"200","showComments":"1","nomobile":"0","name":"siteevent.editor-reviews-siteevent"}\', NULL),
(' . $page_id . ', "widget", "siteevent.profile-members", ' . $tab_id . ', 23, \'{"title":"Guests","titleCount":true,"loaded_by_ajax":1}\', NULL)');

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventrepeat')) {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteeventrepeat.occurrences',
                    'parent_content_id' => $tab_id,
                    'order' => 24,
                    'params' => '{"title":"Event Occurrences","titleCount":true,"loaded_by_ajax":"1","occurrence_date":"1","guest_pictures":"1","guestCount":"5","guest_count_link":"1","rsvp_dropdown":"1","date_filter":"1","profile_links":["join","leave","request","accept-ignore","invite","review"],"viewmore":"1","itemCount":"10","nomobile":"0","name":"siteeventrepeat.occurrences"}'
                ));
            } else {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteevent.ads-plugin-siteevent',
                    'parent_content_id' => $tab_id,
                    'order' => 24,
                    'params' => '{"title":"Occurrences","titleCount":"true","pluginName":"siteeventrepeat","nomobile":"1"}',
                ));
            }

            $db->query('
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES 
(' . $page_id . ', "widget", "siteevent.photos-siteevent", ' . $tab_id . ', 25, \'{"title":"Photos","titleCount":true,"loaded_by_ajax":1}\', NULL),
(' . $page_id . ', "widget", "siteevent.specification-siteevent", ' . $tab_id . ', 26, \'{"title":"Information","titleCount":true,"loaded_by_ajax":"1","nomobile":"0","name":"siteevent.specification-siteevent"}\', NULL),
(' . $page_id . ', "widget", "siteevent.video-siteevent", ' . $tab_id . ', 27, \'{"title":"Videos","titleCount":true,"loaded_by_ajax":1}\', NULL),
(' . $page_id . ', "widget", "siteevent.discussion-siteevent", ' . $tab_id . ', 28, \'{"title":"Discussions","titleCount":true,"loaded_by_ajax":1}\', NULL),
(' . $page_id . ', "widget", "siteevent.user-siteevent", ' . $tab_id . ', 29, \'{"title":"User Reviews","titleCount":"true","loaded_by_ajax":1}\', NULL),
(' . $page_id . ', "widget", "siteevent.location-siteevent", ' . $tab_id . ', 30, \'{"title":"Map","titleCount":true}\', NULL)');

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventdocument')) {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteeventdocument.profile-siteeventdocuments',
                    'parent_content_id' => $tab_id,
                    'order' => 31,
                    'params' => '{"title":"Documents","loaded_by_ajax":true}',
                ));
            } else {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteevent.ads-plugin-siteevent',
                    'parent_content_id' => $tab_id,
                    'order' => 31,
                    'params' => '{"title":"Documents","titleCount":"true","pluginName":"siteeventdocument","nomobile":"1"}',
                ));
            }

            $db->query('
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES 
(' . $page_id . ', "widget", "siteevent.overview-siteevent", ' . $tab_id . ', 32, \'{"title":"Overview","titleCount":true,"loaded_by_ajax":1}\', NULL),
(' . $page_id . ', "widget", "siteevent.location-sidebar-siteevent", ' . $right_container_id . ', 34, \'{"title":"","titleCount":true,"showContent":["startEndDates","addToCalendar"]}\', NULL),
(' . $page_id . ', "widget", "siteevent.profile-host-info", ' . $right_container_id . ', 35, \'{"placeWidget":"smallColumn","showInfo":["totalevent","totalrating"],"title":"Event Hosted By","nomobile":"0","name":"siteevent.profile-host-info"}\', NULL),
(' . $page_id . ', "widget", "siteevent.about-editor-siteevent", ' . $right_container_id . ', 36, \'{"title":"About Me","titleCount":""}\', NULL),
(' . $page_id . ', "widget", "siteevent.quick-specification-siteevent", ' . $right_container_id . ', 37, \'{"title":"Quick Informations","titleCount":true}\', NULL),
(' . $page_id . ', "widget", "siteevent.related-events-view-siteevent", ' . $right_container_id . ', 38, \'{"title":"Related Events","truncation":"40","titleCount":true,"statistics":["likeCount","reviewCount","memberCount"]}\', NULL),
(' . $page_id . ', "widget", "siteevent.userevent-siteevent", ' . $right_container_id . ', 39, \'{"title":"%s Events","truncation":"40","titleCount":true,"statistics":["likeCount","reviewCount"]}\', NULL)
');

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitetagcheckin')) {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitetagcheckin.checkinbutton-sitetagcheckin',
                    'parent_content_id' => $right_container_id,
                    'order' => 999,
                    'params' => '{"title":"","titleCount":true,"checkin_use":"1","checkin_button_sidebar":"1","checkin_button":"1","checkin_button_link":"Check-in here","checkin_icon":"1","checkin_verb":"Check-in","checkedinto_verb":"checked-into","checkin_your":"You\'ve checked-in here","checkin_total":"Total check-ins here","nomobile":"0","name":"sitetagcheckin.checkinbutton-sitetagcheckin"}'
                ));

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitetagcheckin.checkinuser-sitetagcheckin',
                    'parent_content_id' => $right_container_id,
                    'order' => 999,
                    'params' => '{"title":"","titleCount":true,"checkedin_heading":"People Here","checkedin_see_all_heading":"People who have been here","checkedin_users":"0","checkedin_user_photo":"1","checkedin_user_name":"0","checkedin_user_checkedtime":"0","checkedin_item_count":"5","nomobile":"0","name":"sitetagcheckin.checkinuser-sitetagcheckin"}'
                ));
            } else {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteevent.ads-plugin-siteevent',
                    'parent_content_id' => $right_container_id,
                    'order' => 999,
                    'params' => '{"title":"Check-in here","titleCount":"true","pluginName":"sitetagcheckin","nomobile":"1"}',
                ));
            }
        }
    }

    public function template6Profile() {

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        //EVENTS HOME PAGE CREATION
        $page_id = $this->profilePageCreate();

        if (!empty($page_id)) {

            $this->deleteCoreContent($page_id);

            $containerCount = 0;
            $widgetCount = 0;

            //TOP CONTAINER
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'top',
                'page_id' => $page_id,
                'order' => $containerCount++,
            ));
            $top_container_id = $db->lastInsertId();

            //INSERT TOP-MIDDLE
            $db->insert('engine4_core_content', array(
                'type' => 'container',
                'name' => 'middle',
                'page_id' => $page_id,
                'parent_content_id' => $top_container_id,
                'order' => $containerCount++,
            ));
            $top_middle_id = $db->lastInsertId();

            //MAIN CONTAINER
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'main',
                'order' => $containerCount++,
                'params' => '',
            ));
            $main_container_id = $db->lastInsertId('engine4_core_content');

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'left',
                'parent_content_id' => $main_container_id,
                'order' => $containerCount++,
                'params' => '',
            ));
            $left_container_id = $db->lastInsertId('engine4_core_content');

            //RIGHT CONTAINER
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'right',
                'parent_content_id' => $main_container_id,
                'order' => $containerCount++,
                'params' => '',
            ));
            $right_container_id = $db->lastInsertId('engine4_core_content');

            //MIDDLE CONTAINER  
            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'container',
                'name' => 'middle',
                'parent_content_id' => $main_container_id,
                'order' => $containerCount++,
                'params' => '',
            ));
            $main_middle_id = $db->lastInsertId('engine4_core_content');


            $db->query('
                
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES
(' . $page_id . ', "widget", "siteevent.list-profile-breadcrumb", ' . $top_middle_id . ', 3, \'["[]"]\', NULL)');

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitecontentcoverphoto')) {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitecontentcoverphoto.content-cover-photo',
                    'parent_content_id' => $top_middle_id,
                    'order' => 5,
                    'params' => '{"modulename":"siteevent_event","showContent_0":"","showContent_siteevent_event":["title","joinButton","inviteGuest","updateInfoButton","inviteRsvpButton","optionsButton","venue","startDate","featured","sponsored","newlabel","showeventtype","showeventtime"],"showContent_sitegroup_group":"","showContent_sitepage_page":"","showContent_sitereview_listing_12":"","showContent_sitestore_store":"","profile_like_button":"1","columnHeight":"300","showMember":"1","memberCount":"8","onlyMemberWithPhoto":"1","sitecontentcoverphotoChangeTabPosition":"1","contacts":["1","2","3"],"emailme":"1","title":"","nomobile":"0","name":"sitecontentcoverphoto.content-cover-photo"}',
                ));
            } else {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteevent.ads-plugin-siteevent',
                    'parent_content_id' => $top_middle_id,
                    'order' => 5,
                    'params' => '{"title":"Cover Photo","titleCount":"true","pluginName":"sitecontentcoverphoto","nomobile":"1"}',
                ));
            }

            $db->query('
                
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES    
(' . $page_id . ', "widget", "siteevent.mainphoto-siteevent", ' . $left_container_id . ', 8, \'{"titleCount":true,"ownerName":"0","featuredLabel":"0","sponsoredLabel":"0","title":"","nomobile":"0","name":"siteevent.mainphoto-siteevent"}\', NULL),
(' . $page_id . ', "widget", "siteevent.event-status", ' . $left_container_id . ', 8, \'{"title":"","titleCount":true}\', NULL),     
(' . $page_id . ', "widget", "siteevent.profile-members-sidebar", ' . $left_container_id . ', 9, \'{"title":"Events Guests","titleCount":true,"join_filters":["2","1"],"show_seeall":"1","itemCount":"","nomobile":"0","name":"siteevent.profile-members-sidebar"}\', NULL),
(' . $page_id . ', "widget", "siteevent.profile-announcements-siteevent", ' . $left_container_id . ', 10, \'{"title":"Announcements","titleCount":true}\', NULL),
(' . $page_id . ', "widget", "siteevent.diary-add-link", ' . $left_container_id . ', 11, \'{"title":""}\', NULL),
(' . $page_id . ', "widget", "siteevent.profile-event-buttons", ' . $left_container_id . ', 12, \'{"title":"","showButtons":["signIn","signUp","uploadPhotos","uploadVideos"],"nomobile":"0","name":"siteevent.profile-event-buttons"}\', NULL),
(' . $page_id . ', "widget", "siteevent.write-siteevent", ' . $left_container_id . ', 13, \'{"title":"","titleCount":true}\', NULL),
(' . $page_id . ', "widget", "siteevent.profile-host-info", ' . $left_container_id . ', 14, \'{"placeWidget":"smallColumn","showInfo":["totalevent","totalrating","hostDescription","socialLinks","messageHost","viewHostProfile"],"title":"Host","nomobile":"0","name":"siteevent.profile-host-info"}\', NULL),
(' . $page_id . ', "widget", "siteevent.overall-ratings", ' . $left_container_id . ', 15, \'{"title":"Reviews","titleCount":true}\', NULL),
(' . $page_id . ', "widget", "siteevent.information-siteevent", ' . $left_container_id . ', 16, \'{"title":"Event Information ","titleCount":true,"showContent":["categoryLink","startDate","ledBy","price","location","directionLink","viewCount","likeCount","commentCount","memberCount","joinLink","likeButton"],"allowSocialSharing":"0","nomobile":"0","name":"siteevent.information-siteevent"}\', NULL),
(' . $page_id . ', "widget", "siteevent.quick-specification-siteevent", ' . $left_container_id . ', 17, \'{"title":"Quick Informations","titleCount":true}\', NULL),
(' . $page_id . ', "widget", "siteevent.userevent-siteevent", ' . $left_container_id . ', 18, \'{"title":"%s Events","truncation":"40","titleCount":true,"statistics":["likeCount","reviewCount"]}\', NULL),
(' . $page_id . ', "widget", "seaocore.people-like", ' . $left_container_id . ', 19, \'["[]"]\', NULL),
(' . $page_id . ', "widget", "siteevent.location-sidebar-siteevent", ' . $main_middle_id . ', 21, \'{"title":"","titleCount":true,"showContent":["startEndDates","addToCalendar"]}\', NULL)');

            $db->insert('engine4_core_content', array(
                'page_id' => $page_id,
                'type' => 'widget',
                'name' => 'core.container-tabs',
                'parent_content_id' => $main_middle_id,
                'order' => 22,
                'params' => '{"max":"4","title":"","nomobile":"0","name":"core.container-tabs"}',
            ));
            $tab_id = $db->lastInsertId('engine4_core_content');

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('advancedactivity')) {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'advancedactivity.home-feeds',
                    'parent_content_id' => $tab_id,
                    'order' => 23,
                    'params' => '{"title":"What\'s New","advancedactivity_tabs":["aaffeed"],"showScrollTopButton":"1","nomobile":"0","name":"advancedactivity.home-feeds"}'
                ));
            } else {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteevent.ads-plugin-siteevent',
                    'parent_content_id' => $tab_id,
                    'order' => 23,
                    'params' => '{"title":"What\'s New","titleCount":"true","pluginName":"advancedactivity","nomobile":"1"}'
                ));
            }

            $db->query('     
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES  
(' . $page_id . ', "widget", "siteevent.editor-reviews-siteevent", ' . $tab_id . ', 24, \'{"titleEditor":"Review","titleOverview":"Overview","titleDescription":"Description","titleCount":"","loaded_by_ajax":1}\', NULL),
(' . $page_id . ', "widget", "siteevent.photos-siteevent", ' . $tab_id . ', 25, \'{"title":"Photos","titleCount":true,"loaded_by_ajax":1}\', NULL),
(' . $page_id . ', "widget", "siteevent.video-siteevent", ' . $tab_id . ', 26, \'{"title":"Videos","titleCount":true,"loaded_by_ajax":1}\', NULL),
(' . $page_id . ', "widget", "siteevent.profile-members", ' . $tab_id . ', 27, \'{"title":"Guests","titleCount":true,"loaded_by_ajax":1}\', NULL)');

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventrepeat')) {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteeventrepeat.occurrences',
                    'parent_content_id' => $tab_id,
                    'order' => 24,
                    'params' => '{"title":"Event Occurrences","titleCount":true,"loaded_by_ajax":1}'
                ));
            } else {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteevent.ads-plugin-siteevent',
                    'parent_content_id' => $tab_id,
                    'order' => 24,
                    'params' => '{"title":"Occurrences","titleCount":"true","pluginName":"siteeventrepeat","nomobile":"1"}',
                ));
            }

            $db->query('
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES
(' . $page_id . ', "widget", "siteevent.discussion-siteevent", ' . $tab_id . ', 29, \'{"title":"Discussions","titleCount":true,"loaded_by_ajax":1}\', NULL),
(' . $page_id . ', "widget", "siteevent.location-siteevent", ' . $tab_id . ', 30, \'{"title":"Map","titleCount":true}\', NULL)');

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventdocument')) {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteeventdocument.profile-siteeventdocuments',
                    'parent_content_id' => $tab_id,
                    'order' => 31,
                    'params' => '{"title":"Documents","loaded_by_ajax":true}',
                ));
            } else {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteevent.ads-plugin-siteevent',
                    'parent_content_id' => $tab_id,
                    'order' => 31,
                    'params' => '{"title":"Documents","titleCount":"true","pluginName":"siteeventdocument","nomobile":"1"}',
                ));
            }

            $db->query('
INSERT IGNORE INTO `engine4_core_content` (`page_id`, `type`, `name`, `parent_content_id`, `order`, `params`, `attribs`) VALUES 
(' . $page_id . ', "widget", "siteevent.user-siteevent", ' . $tab_id . ', 32, \'{"title":"User Reviews","titleCount":"true","loaded_by_ajax":1}\', NULL),
(' . $page_id . ', "widget", "siteevent.specification-siteevent", ' . $tab_id . ', 33, \'{"title":"Information","titleCount":true,"loaded_by_ajax":1}\', NULL),
(' . $page_id . ', "widget", "siteevent.ads-plugin-siteevent", ' . $right_container_id . ', 35, \'{"title":"Community Ads","titleCount":"true","pluginName":"communityad","nomobile":"1"}\', NULL),
(' . $page_id . ', "widget", "siteevent.related-events-view-siteevent", ' . $right_container_id . ', 36, \'{"title":"More Events in %s","truncation":"40","titleCount":true,"statistics":["likeCount","reviewCount","memberCount"]}\', NULL),
(".$page_id.", "widget", "siteevent.share", ' . $right_container_id . ', 37, \'{"title":"Share and Report ","titleCount":true,"options":["siteShare","friend","report","print","socialShare"],"allowSocialSharing":"1","nomobile":"0","name":"siteevent.share"}\', NULL);
');

            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitetagcheckin')) {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitetagcheckin.checkinbutton-sitetagcheckin',
                    'parent_content_id' => $right_container_id,
                    'order' => 999,
                    'params' => '{"title":"","titleCount":true,"checkin_use":"1","checkin_button_sidebar":"1","checkin_button":"1","checkin_button_link":"Check-in here","checkin_icon":"1","checkin_verb":"Check-in","checkedinto_verb":"checked-into","checkin_your":"You\'ve checked-in here","checkin_total":"Total check-ins here","nomobile":"0","name":"sitetagcheckin.checkinbutton-sitetagcheckin"}'
                ));

                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'sitetagcheckin.checkinuser-sitetagcheckin',
                    'parent_content_id' => $right_container_id,
                    'order' => 999,
                    'params' => '{"title":"","titleCount":true,"checkedin_heading":"People Here","checkedin_see_all_heading":"People who have been here","checkedin_users":"0","checkedin_user_photo":"1","checkedin_user_name":"0","checkedin_user_checkedtime":"0","checkedin_item_count":"5","nomobile":"0","name":"sitetagcheckin.checkinuser-sitetagcheckin"}'
                ));
            } else {
                $db->insert('engine4_core_content', array(
                    'page_id' => $page_id,
                    'type' => 'widget',
                    'name' => 'siteevent.ads-plugin-siteevent',
                    'parent_content_id' => $right_container_id,
                    'order' => 999,
                    'params' => '{"title":"Check-in here","titleCount":"true","pluginName":"sitetagcheckin","nomobile":"1"}',
                ));
            }
        }
    }

}
