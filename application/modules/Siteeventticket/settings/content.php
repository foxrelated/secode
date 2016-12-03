<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$statisticsElement = array(
    'MultiCheckbox',
    'statistics',
    array(
        'label' => 'Choose the statistics that you want to be displayed for the Coupons in this block.',
        'multiOptions' => array("startdate" => "Start Date", "enddate" => "End Date", "couponcode" => "Coupon Code", 'discount' => 'Discount', 'expire' => 'Expired'),
    ),
);

return array(
    array(
        'title' => 'Event Profile: Event Tickets',
        'description' => 'Displays the list of all the tickets corresponding to the event being currently viewed.',
        'category' => 'Advanced Events - Tickets',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'siteeventticket.event-tickets',
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'orderby',
                    array(
                        'label' => 'Default ordering in which tickets will be displayed.',
                        'multiOptions' => array(
                            'ticket_id' => 'All tickets in ascending order of creation.',
                            'price' => 'All tickets in descending order of price.',
                        ),
                        'value' => 'ticket_id',
                    )
                ),
                array(
                    'Radio',
                    'showEventFullStatus',
                    array(
                        'label' => 'Do you want to show "Event is Full" message.',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'showTicketStatus',
                    array(
                        'label' => 'Do you want to show Ticket Status.',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),                
            ),
        ),
    ),
    array(
        'title' => "Event’s Ticket List and Information",
        'description' => 'Display the tickets list of the event being currently viewed. It allows user to buy tickets for the normal event and for various occurrences of a repeating event as well. This widget should be placed on the Event Profile Page, in tabbed blocks area of any page of your website.',
        'category' => 'Advanced Events - Tickets',
        'type' => 'widget',
        'name' => 'siteeventticket.tickets-buy',
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'orderby',
                    array(
                        'label' => 'Default ordering in which tickets will be displayed.',
                        'multiOptions' => array(
                            'ticket_id' => 'All tickets in ascending order of creation.',
                            'price' => 'All tickets in descending order of price.',
                        ),
                        'value' => 'ticket_id',
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Ticket Sales Figures (Dashboard)',
        'description' => 'This widget displays the event sales figures of the current day, week and month to the event admins. This widget should be placed on statistics page.',
        'category' => 'Advanced Events - Tickets',
        'type' => 'widget',
        'name' => 'siteeventticket.sales-figures',
        'autoEdit' => true,
    ),
    array(
        'title' => 'Recent Orders (Dashboard)',
        'description' => 'Recent Orders',
        'category' => 'Advanced Events - Tickets',
        'type' => 'widget',
        'name' => 'siteeventticket.latest-orders',
        'autoEdit' => true,
        'defaultParams' => array(
            'title' => 'Recent Orders',
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Orders',
                        'description' => '(number of latest orders to display in widget.Enter 0 for displaying all orders.)',
                        'value' => 5,
                    )
                )
            ))
    ),
    array(
        'title' => 'Ticket Statistics (Dashboard)',
        'description' => 'Displays the current statistics like total commision paid, total tax, total sales based on duration, etc for the event tickets being currently viewed. This widget should be placed on the Event Dashboard page.',
        'category' => 'Advanced Events - Tickets',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'siteeventticket.ticket-statistics',
        'defaultParams' => array(
            'title' => 'Ticket Statistics',
        ),
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
    array(
        'title' => 'Event Coupon View',
        'description' => "Displays event coupon being currently viewed. This widget should be placed on the 'Advanced Events - Event Coupon View Page.",
        'category' => 'Advanced Events - Tickets',
        'type' => 'widget',
        'name' => 'siteeventticket.coupon-content',
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                $statisticsElement,
            ),
        ),
    ),
    array(
        'title' => 'Search Event Coupons Form',
        'description' => 'Displays the form for searching event coupons on the basis of various filters. You can edit this widget to choose the filters to be availble in this form',
        'category' => 'Advanced Events - Tickets',
        'type' => 'widget',
        'name' => 'siteeventticket.search-coupons',
        'autoEdit' => true,
        'defaultParams' => array(
            'title' => '',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'search_column',
                    array(
                        'label' => 'Choose the fields that you want to be available in the Search event coupons form widget.',
                        'multiOptions' => array("1" => "Browse By", "2" => "Event Title", "3" => "Coupon Title", "4" => "Event Category"),
                    ),
                ),
            ),
        )
    ),
    array(
        'title' => 'Browse Event Coupons',
        'description' => 'Displays the list of Coupons from Advanced Events created on your community. This widget should be placed on the "Advanced Events - Browse Event Coupons" page.',
        'category' => 'Advanced Events - Tickets',
        'type' => 'widget',
        'name' => 'siteeventticket.browse-coupons',
        'autoEdit' => true,
        'defaultParams' => array(
            'titleCount' => true,
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'statistics',
                    array(
                        'label' => 'Choose the statistics that you want to be displayed for the Coupons in this block.',
                        'multiOptions' => array("startdate" => "Start Date", "enddate" => "End Date", "couponcode" => "Coupon Code", 'discount' => 'Discount', 'expire' => 'Expired', 'viewCount' => "Views", 'likeCount' => "Likes", 'commentCount' => "Comments"),
                    ),
                ),
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => 'Title Truncation Limit',
                        'value' => 64,
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Number of coupons to show at a time.',
                        'value' => 20,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    ),
                ),
            ),
        ),
    ),
    array(
        'title' => 'Event Profile - Event Coupons',
        'description' => 'This widget forms the Coupons tab on the Event Profile Page and displays the coupons of the Event. It should be placed in the Tabbed Blocks area of the Event Profile.',
        'category' => 'Advanced Events - Tickets',
        'type' => 'widget',
        'name' => 'siteeventticket.event-profile-coupons',
        'defaultParams' => array(
            'title' => 'Coupons',
            'titleCount' => true,
            'loaded_by_ajax' => 1
        ),
        'autoEdit' => true,
        'adminForm' => array(
            'elements' => array(
                $statisticsElement,
                array(
                    'Radio',
                    'loaded_by_ajax',
                    array(
                        'label' => 'Widget Content Loading',
                        'description' => 'Do you want the content of this widget to be loaded via AJAX, after the loading of main webpage content? (Enabling this can improve webpage loading speed. Disabling this would load content of this widget along with the page content.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Number of coupons to show at a time.',
                        'value' => 10,
                    )
                ),
                array(
                    'Text',
                    'truncation',
                    array(
                        'label' => 'Title Truncation Limit',
                        'value' => 64,
                        'validators' => array(
                            array('Int', true),
                            array('GreaterThan', true, array(0)),
                        ),
                    )
                ),
            ),
        ),
    ),
    array(
        'title' => 'Event Profile - Terms & Conditions',
        'description' => 'This widget forms the "Terms & Conditions" tab on the Event Profile Page and displays the text added by event owner from Dashboard of that Event. It should be placed in the Tabbed Blocks area of the Event Profile.',
        'category' => 'Advanced Events - Tickets',
        'type' => 'widget',
        'name' => 'siteeventticket.terms-of-use',
        'defaultParams' => array(
            'title' => 'Terms & Conditions',
        ),
        'autoEdit' => true,
    ),
    array(
        'title' => 'My Tickets',
        'description' => 'Displays all tickets purchased by user on "My Tickets" page.',
        'category' => 'Advanced Events - Tickets',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'siteeventticket.my-tickets',
        'defaultParams' => array(
            'title' => 'My Tickets',
        ),
        'adminForm' => array(
            'elements' => array(
            ),
        ),
    ),
    
    array(
        'title' => 'Event Profile: Event Attendees',
        'description' => 'This widget displays all the members who have bought tickets for the event currently being viewed. This widget should be placed in the right / left column of the Advanced Events - Event Profile page.',
        'category' => 'Advanced Events',
        'type' => 'widget',
        'autoEdit' => true,
        'name' => 'siteeventticket.members-bought-ticket',
        'defaultParams' => array(
            'title' => 'Event Attendees',
            'titleCount' => true,
            'loaded_by_ajax' => 0,
        ),
        'requirements' => array(
            'subject' => 'siteevent_event',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Radio',
                    'loaded_by_ajax',
                    array(
                        'label' => 'Widget Content Loading',
                        'description' => 'Do you want the content of this widget to be loaded via AJAX, after the loading of main webpage content? (Enabling this can improve webpage loading speed. Disabling this would load content of this widget along with the page content.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                ),
                array(
                    'Radio',
                    'show_seeall',
                    array(
                        'label' => 'Show "See All" link.',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'occurrence_filtering',
                    array(
                        'label' => 'Show occurrence filtering.',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 0,
                    )
                ),                
                array(
                    'Text',
                    'itemCount',
                    array(
                        'label' => 'Number of members to show',
                        'value' => 10,
                    )
                ),
            ),
        )
    ),    
    
    
);

