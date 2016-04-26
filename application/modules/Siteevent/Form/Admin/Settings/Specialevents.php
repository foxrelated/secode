<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Dayitem.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Form_Admin_Settings_Specialevents extends Engine_Form {

    public function init() {

        $this->setMethod('post');
        $this->setTitle('Special Events')
                ->setDescription('Displays special Events as selected by you from the auto-suggest box below.');

        //SHOW PREFIELD START AND END DATETIME
        $httpReferer = $_SERVER['HTTP_REFERER'];
        $params = $toValues = $toValuesArray = array();
        $toValuesString = '';
        if (!empty($httpReferer) && strstr($httpReferer, '?page=')) {
            $httpRefererArray = explode('?page=', $httpReferer);
            $page_id = (int) $httpRefererArray['1'];
        } elseif (!empty($httpReferer) && strstr($httpReferer, 'admin/content') && !strstr($httpReferer, 'admin/content?')) {
            $page_id = 3; //FOR HOME PAGE
        }

        if (!empty($page_id) && is_numeric($page_id)) {

            //GET CONTENT TABLE
            $tableContent = Engine_Api::_()->getDbtable('content', 'core');
            $tableContentName = $tableContent->info('name');

            //GET CONTENT
            $params = $tableContent->select()
                    ->from($tableContentName, array('params'))
                    ->where('page_id = ?', $page_id)
                    ->where('name = ?', 'siteevent.special-events')
                    ->query()
                    ->fetchColumn();

            if (!empty($params)) {
                $params = Zend_Json_Decoder::decode($params);
            }
        }

        $this->addElement('Text', 'event_ids', array(
            'autocomplete' => 'off',
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '/application/modules/Siteevent/views/scripts/admin-settings/add-special-events.tpl',
                        'thisObject' => $this,
                        'class' => 'form element')))
        ));
        Engine_Form::addDefaultDecorators($this->event_ids);

        $this->addElement('Hidden', 'toValues', array(
            'label' => '',
            'order' => 1,
            'filters' => array(
                'HtmlEntities'
            ),
        ));
        Engine_Form::addDefaultDecorators($this->toValues);

        // Start time
        $start = new Engine_Form_Element_CalendarDateTime('starttime');
        $start->setLabel("Start Time");
        $start->setAllowEmpty(false);
        $this->addElement($start);

        // End time
        $end = new Engine_Form_Element_CalendarDateTime('endtime');
        $end->setLabel("End Time");
        $end->setAllowEmpty(false);
        $this->addElement($end);

        if (isset($params['starttime']) && !empty($params['starttime'])) {
            $start->setValue($params['starttime']);
        }

        if (isset($params['endtime']) && !empty($params['endtime'])) {
            $end->setValue($params['endtime']);
        }

        $this->addElement(
                'Radio', 'titlePosition', array(
            'label' => 'Do you want "Event Title" to be displayed inside the Grid View?',
            'multiOptions' => array(1 => 'Yes', 0 => 'No'),
            'value' => 1,
                )
        );
        $this->addElement(
                'Radio', 'viewType', array(
            'label' => 'Choose the View Type for events.',
            'multiOptions' => array(
                'listview' => 'List View',
                'gridview' => 'Grid View',
            ),
            'value' => 'listview',
                )
        );
        $this->addElement(
                'Text', 'columnWidth', array(
            'label' => 'Column Width For Grid View.',
            'value' => '180',
                )
        );
        $this->addElement(
                'Text', 'columnHeight', array(
            'label' => 'Column Height For Grid View.',
            'value' => '328',
                )
        );
        $this->addElement(
                'MultiCheckbox', 'eventInfo', array(
            'label' => 'Choose the options that you want to be displayed for the Events in this block.',
            'multiOptions' => array_merge(array(
                "hostName" => "Hosted By",
                "categoryLink" => "Category",
                "featuredLabel" => "Featured Label (for Grid View only)",
                "sponsoredLabel" => "Sponsored Label (for Grid View only)",
                "newLabel" => "New Label (for Grid View only)",
                "startDate" => "Start Date and Time",
                "endDate" => "End Date and Time",
                "ledBy" => "Led By",
                "price" => "Price",
                "venueName" => "Venue Name",
                "location" => "Location",
                "directionLink" => "Open Get Direction popup on clicking location. (Dependent on Location)"
                    ), array("viewCount" => "Views", "likeCount" => "Likes", "commentCount" => "Comments", "memberCount" => "Guests", 'reviewCount' => 'Reviews', 'ratingStar' => 'Ratings')),
                )
        );
        $this->addElement(
                'Text', 'itemCount', array(
            'label' => 'Number of Events to show',
            'value' => 3,
                )
        );
        $this->addElement(
                'Text', 'truncationLocation', array(
            'label' => 'Truncation Limit of Location (Depend on Location)',
            'value' => 35,
                )
        );
        $this->addElement(
                'Text', 'truncation', array(
            'label' => 'Title Truncation Limit',
            'value' => 16,
            'validators' => array(
                array('Int', true),
                array('GreaterThan', true, array(0)),
            ),
                )
        );
        $this->addElement(
                'Select', 'ratingType', array(
            'label' => 'Rating Type',
            'multiOptions' => array('rating_avg' => 'Average Ratings', 'rating_editor' => 'Only Editor Ratings', 'rating_users' => 'Only User Ratings', 'rating_both' => 'Both User and Editor Ratings'),
                )
        );
    }

}
