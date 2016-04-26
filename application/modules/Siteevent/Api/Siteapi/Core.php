2<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteapi
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    TopicController.php 2015-09-17 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Api_Siteapi_Core extends Core_Api_Abstract {

    private $_validateSearchProfileFields = false;
    private $_profileFieldsArray = array();
    private $_create = false;

    /**
     * Get event advanced search form.
     * 
     * @return array
     */
    public function getBrowseSearchForm($restapilocation) {

        $searchForm = array();
        $viewer = Engine_Api::_()->user()->getViewer();

        //GET API
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $row = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->getFieldsOptions('siteevent', 'search');
        if (!empty($row) && !empty($row->display)) {
            $searchForm[] = array(
                'type' => 'Text',
                'name' => 'search',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Search Events')
            );
        }

        $row = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->getFieldsOptions('siteevent', 'show');
        if (!empty($row) && !empty($row->display)) {
            $show_multiOptions = array();
            $show_multiOptions["1"] = Engine_Api::_()->getApi('Core', 'siteapi')->translate("Everyone's Events");
            $show_multiOptions["2"] = Engine_Api::_()->getApi('Core', 'siteapi')->translate("Only My Friends' Events");
            $show_multiOptions["4"] = Engine_Api::_()->getApi('Core', 'siteapi')->translate("Events I Like");
//            $show_multiOptions["5"] = "This Month Events";
//            $show_multiOptions["6"] = "This Week Events";
//            $show_multiOptions["7"] = "This Weekend Events";
//            $show_multiOptions["8"] = "Today Events";
            $value_deault = 1;
            $enableNetwork = $settings->getSetting('siteevent.network', 0);
            if (empty($enableNetwork)) {
                $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
                $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
                $viewerNetwork = $networkMembershipTable->fetchRow(array('user_id = ?' => $viewer_id));

                if (!empty($viewerNetwork)) {
                    $show_multiOptions["3"] = Engine_Api::_()->getApi('Core', 'siteapi')->translate('Only My Networks');
                    $browseDefaulNetwork = $settings->getSetting('siteevent.default.show', 0);
                }
            }
            $searchForm[] = array(
                'type' => 'Select',
                'name' => 'show',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Show'),
                'multiOptions' => $show_multiOptions
            );
        }


        $row = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->getFieldsOptions('siteevent', 'view');
        if (!empty($row) && !empty($row->display)) {
            $searchForm[] = array(
                'type' => 'Select',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('View'),
                'name' => 'showEventType',
                'multiOptions' => array('upcoming' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Upcoming & Ongoing'), 'onlyUpcoming' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Upcoming'), 'onlyOngoing' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Ongoing'), 'past' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Past')),
            );
        }

        $row = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->getFieldsOptions('siteevent', 'orderby');
        if (!empty($row) && !empty($row->display)) {

            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) == 3 || Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) == 2) {
                $multiOptionsOrderBy = array(
                    '' => "",
                    'title' => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Alphabetical"),
                    'event_id' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Recently Created'),
                    'starttime' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Start Time'),
                    'view_count' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Viewed'),
                    'like_count' => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Most Liked"),
                    'comment_count' => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Most Commented"),
                    'member_count' => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Most Joined"),
                    'review_count' => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Most Reviewed"),
                    'rating_avg' => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Most Rated"),
                );
            } elseif (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) == 1) {
                $multiOptionsOrderBy = array(
                    '' => "",
                    'title' => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Alphabetical"),
                    'event_id' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Recently Created'),
                    'starttime' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Start Time'),
                    'view_count' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Viewed'),
                    'like_count' => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Most Liked"),
                    'comment_count' => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Most Commented"),
                    'member_count' => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Most Joined"),
                    'rating_avg' => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Most Rated"),
                );
            } else {
                $multiOptionsOrderBy = array(
                    '' => "",
                    'title' => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Alphabetical"),
                    'event_id' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Recently Created'),
                    'starttime' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Start Time'),
                    'view_count' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Viewed'),
                    'like_count' => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Most Liked"),
                    'comment_count' => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Most Commented"),
                    'member_count' => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Most Joined"),
                );
            }

            $searchForm[] = array(
                'type' => 'Select',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Browse By'),
                'name' => 'orderBy',
                'multiOptions' => $multiOptionsOrderBy
            );

            if (Engine_Api::_()->siteevent()->isTicketBasedEvent()) {
                unset($multiOptionsOrderBy['member_count']);
            }
        }
        $row = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->getFieldsOptions('siteevent', 'by_time');
        if (!empty($row) && !empty($row->display)) {
            $searchForm[] = array(
                'type' => 'Select',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Occuring'),
                'name' => 'event_time',
                'multiOptions' => array(
                    '' => "",
                    'today' => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Today"),
                    'tomorrow' => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Tomorrow"),
                    'this_weekend' => Engine_Api::_()->getApi('Core', 'siteapi')->translate("This Weekend"),
                    'this_week' => Engine_Api::_()->getApi('Core', 'siteapi')->translate("This Week"),
                    'this_month' => Engine_Api::_()->getApi('Core', 'siteapi')->translate("This Month"),
                ),
            );
        }


        $row = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->getFieldsOptions('siteevent', 'venue');
        if (!empty($row) && !empty($row->display)) {
            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.veneuname', 1) && !empty($row) && !empty($row->display)) {
                $searchForm[] = array(
                    'type' => 'Text',
                    'name' => 'venue_name',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Venue')
                );
            }
        }

        $row = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->getFieldsOptions('siteevent', 'location');
        if (!empty($row) && !empty($row->display) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.location', 1)) {
            $locationDefault = $settings->getSetting('seaocore.locationdefault', '');

            $seaocore_locationspecific = $settings->getSetting('seaocore.locationspecific', '');
            if ($seaocore_locationspecific && !empty($restapilocation) && isset($restapilocation)) {
                $locationDefault = $restapilocation;
            }
            if (isset($locationDefault) && !empty($locationDefault)) {
                $searchForm[] = array(
                    'type' => 'Text',
                    'name' => 'location',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Location'),
                    'value' => $locationDefault
                );
            } else {
                $searchForm[] = array(
                    'type' => 'Text',
                    'name' => 'location',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Location'),
                    'value' => ''
                );
            }
        }

        $row = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->getFieldsOptions('siteevent', 'proximity');
        if (!empty($row) && !empty($row->display)) {
            $flage = $settings->getSetting('siteevent.proximity.search.kilometer', 0);
            $flage = $settings->getSetting('siteevent.proximity.search.kilometer', 0);
            if ($flage) {
                $locationLable = "Within Kilometers";
                $locationOption = array(
                    '0' => '',
                    '1' => '1 Kilometer',
                    '2' => '2 Kilometers',
                    '5' => '5 Kilometers',
                    '10' => '10 Kilometers',
                    '20' => '20 Kilometers',
                    '50' => '50 Kilometers',
                    '100' => '100 Kilometers',
                    '250' => '250 Kilometers',
                    '500' => '500 Kilometers',
                    '750' => '750 Kilometers',
                    '1000' => '1000 Kilometers',
                );
            } else {
                $locationLable = "Within Miles";
                $locationOption = array(
                    '0' => '',
                    '1' => '1 Mile',
                    '2' => '2 Miles',
                    '5' => '5 Miles',
                    '10' => '10 Miles',
                    '20' => '20 Miles',
                    '50' => '50 Miles',
                    '100' => '100 Miles',
                    '250' => '250 Miles',
                    '500' => '500 Miles',
                    '750' => '750 Miles',
                    '1000' => '1000 Miles',
                );
            }
            $searchForm[] = array(
                'type' => 'Select',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Proximity' . " " . $locationLable),
                'name' => 'proximity',
                'multiOptions' => $locationOption,
                'value' => 0
            );
        }


        $row = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->getFieldsOptions('siteevent', 'street');
        if (!empty($row) && !empty($row->display)) {
            $searchForm[] = array(
                'type' => 'Text',
                'name' => 'Siteevent_street',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Street')
            );
        }
        $row = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->getFieldsOptions('siteevent', 'city');
        if (!empty($row) && !empty($row->display)) {
            $searchForm[] = array(
                'type' => 'Text',
                'name' => 'siteevent_city',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('City')
            );
        }
        $row = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->getFieldsOptions('siteevent', 'state');
        if (!empty($row) && !empty($row->display)) {
            $searchForm[] = array(
                'type' => 'Text',
                'name' => 'siteevent_state',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('State')
            );
        }
        $row = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->getFieldsOptions('siteevent', 'country');
        if (!empty($row) && !empty($row->display)) {
            $searchForm[] = array(
                'type' => 'Text',
                'name' => 'siteevent_country',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Country')
            );
        }

        $row = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->getFieldsOptions('siteevent', 'price');
        if (!empty($row) && !empty($row->display) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.price', 0)) {
            $searchForm[] = array(
                'type' => 'Text',
                'name' => 'price',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Price')
            );
        }
        $categories = Engine_Api::_()->getDbTable('categories', 'siteevent')->getCategories(array('category_id', 'category_name'));
        if (count($categories) != 0) {

            $categories_prepared[0] = "";
        }
        foreach ($categories as $category) {
            $categories_prepared[$category->category_id] = $category->category_name;
        }
        $row = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->getFieldsOptions('siteevent', 'category_id');
        if (!empty($row) && !empty($row->display)) {
            $searchForm[] = array(
                'type' => 'Select',
                'name' => 'category_id',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Category'),
                'multiOptions' => $categories_prepared
            );
        }
        $row = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->getFieldsOptions('siteevent', 'dates');
        if (!empty($row) && !empty($row->display)) {
            $searchForm[] = array(
                'type' => 'Date',
                'name' => 'start_date',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('From')
            );

            $searchForm[] = array(
                'type' => 'Date',
                'name' => 'end_date',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('To')
            );
        }

        $row = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->getFieldsOptions('siteevent', 'has_review');

        if (!empty($row) && !empty($row->display) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2)) {

            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) == 3) {
                $multiOptions = array(
                    '' => '',
                    'rating_avg' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Any Review'),
                    'rating_editor' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Editor Reviews'),
                    'rating_users' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('User Reviews'),
                );
            } elseif (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) == 2) {
                $multiOptions = array(
                    '' => '',
                    'rating_users' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('User Reviews'),
                );
            } elseif (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.reviews', 2) == 1) {
                $multiOptions = array(
                    '' => '',
                    'rating_editor' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Editor Reviews'),
                );
            }
            $searchForm[] = array(
                'type' => 'Select',
                'name' => 'has_review',
                'Label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Event Having'),
                'multioptions' => "$multiOptions"
            );
        }

        $row = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->getFieldsOptions('siteevent', 'has_photo');
        if (!empty($row) && !empty($row->display)) {
            $searchForm[] = array(
                'type' => 'Checkbox',
                'name' => 'has_photo',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Only Events With Photos')
            );
        }
        $row = Engine_Api::_()->getDbTable('searchformsetting', 'seaocore')->getFieldsOptions('siteevent', 'has_free_price');
        if (!empty($row) && !empty($row->display) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.price', 0)) {
            $searchForm[] = array(
                'type' => 'Checkbox',
                'name' => 'has_free_price',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Only Free Events')
            );
        }

        $searchForm[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Search'),
        );

        $responseForm['form'] = $searchForm;
        $profileFields = $this->getProfileTypes();
        if (!empty($profileFields)) {
            $this->_profileFieldsArray = $profileFields;
        }
        $responseForm['fields'] = $this->getSearchProfileFields();

        return $responseForm;
    }

    public function getInformation($siteevent) {
        $profileFields = $this->getProfileTypes();
        if (!empty($profileFields)) {
            $this->_profileFieldsArray = $profileFields;
        }
        $information = $this->getProfileInfo($siteevent);
        return $information;
    }

    public function getMessageOwnerForm() {
        $message = array();

        // init title
        $message[] = array(
            'type' => 'Text',
            'name' => 'title',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Subject'),
            'hasValidators' => 'true'
        );

        // init body - plain text
        $message[] = array(
            'type' => 'Textarea',
            'name' => 'body',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Message'),
        );

        $message[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Send Message'),
        );
        return $message;
    }

    public function getTellAFriendForm() {
        $tell[] = array(
            'type' => 'Text',
            'name' => 'sender_name',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Your Name'),
            'hasValidator' => 'true'
        );

        $tell[] = array(
            'type' => 'Text',
            'name' => 'sender_email',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Your Email'),
            'has Validator' => 'true'
        );

        $tell[] = array(
            'type' => 'Text',
            'name' => 'receiver_emails',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('To'),
            'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Separate multiple addresses with commas'),
            'hasValidators' => 'true'
        );

        $tell[] = array(
            'type' => 'Textarea',
            'name' => 'message',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Message'),
            'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('You can send a personal note in the mail.'),
            'hasValidator' => 'true',
        );

        $tell[] = array(
            'type' => 'Checkbox',
            'name' => 'send_me',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Send a copy to my email address."),
        );


        $tell[] = array(
            'type' => 'Submit',
            'name' => 'send',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Tell a Friend'),
        );
        return $tell;
    }

    public function getInviteForm($isLeader, $subject = null, $viewer = nul) {

        $friends = $viewer->membership()->getMembers();
        $invite = array();
        if ($isLeader) {
            // init to
            $invite[] = array(
                'type' => 'Text',
                'name' => 'user_ids',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Start typing the names of the members that you want to invite.'),
            );
        } else {
            $userFriends = array();
            foreach ($friends as $friend) {
                if ($subject->membership()->isMember($friend, null))
                    continue;
                $userFriends[$friend->getIdentity()] = $friend->getTitle();
            }
            try {
                //TODO TRANSLATE WORK
                if (COUNT($userFriends)) {
                    $invite[] = array(
                        'type' => 'Checkbox',
                        'name' => 'selectall',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Choose All Friends')
                    );

                    $invite[] = array(
                        'type' => 'Multicheckbox',
                        'name' => 'users',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Invite Members'),
                        'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Choose the people you want to invite to this event.'),
                        'multiOptions' => $userFriends,
                    );
                }
            } catch (Exception $ex) {
                
            }
        }

        $invite[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Send Invites'),
        );

        return $invite;
    }

    /**
     * Return the "Photo Edit" form. 
     * 
     * @return array
     */
    public function getPhotoEditForm($form = array()) {
        $form[] = array(
            'type' => 'Text',
            'name' => 'title',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Title'),
            'hasValidator' => true
        );

        $form[] = array(
            'type' => 'Textarea',
            'name' => 'description',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Description'),
            'hasValidator' => true
        );

        $form[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Submit')
        );

        return $form;
    }

    public function getMemberJoinForm() {

        $join = array();

        $join[] = array(
            'type' => 'Radio',
            'name' => 'rsvp',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('RSVP'),
            'multiOptions' => array(
                2 => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Attending'),
                1 => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Maybe Attending'),
                0 => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Not Attending'),
            //3 => 'Awaiting Approval',
            ),
            'hasValidators' => 'true'
        );


        $join[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Join Event'),
        );
        return $join;
    }

    public function getReviewSearchForm() {

        $order = 1;
        $reviewForm = array();
        $reviewForm[] = array(
            'type' => 'Text',
            'name' => 'search',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Search'),
        );
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        if ($viewer_id) {
            $reviewForm[] = array(
                'type' => 'Select',
                'name' => 'show',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Show'),
                'multiOptions' => array('' => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Everyone's Reviews"), 'friends_reviews' => Engine_Api::_()->getApi('Core', 'siteapi')->translate("My Friends' Reviews"), 'self_reviews' => Engine_Api::_()->getApi('Core', 'siteapi')->translate("My Reviews"), 'featured' => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Featured Reviews")),
            );
        }

        $reviewForm[] = array(
            'type' => 'Select',
            'name' => 'type',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Reviews Written By'),
            'multiOptions' => array('' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Everyone'), 'editor' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Editors'), 'user' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Users')),
        );


        $reviewForm[] = array(
            'type' => 'Select',
            'name' => 'order',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Browse By'),
            'multiOptions' => array(
                'recent' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Recent'),
                'rating_highest' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Highest Rating'),
                'rating_lowest' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Lowest Rating'),
                'helpfull_most' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Helpful'),
                'replay_most' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Reply'),
                'view_most' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Viewed')
            ),
        );
        $reviewForm[] = array(
            'type' => 'Select',
            'name' => 'rating',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Ratings'),
            'multiOptions' => array(
                '' => '',
                '5' => sprintf(Zend_Registry::get('Zend_Translate')->_('%1s Star'), 5),
                '4' => sprintf(Zend_Registry::get('Zend_Translate')->_('%1s Star'), 4),
                '3' => sprintf(Zend_Registry::get('Zend_Translate')->_('%1s Star'), 3),
                '2' => sprintf(Zend_Registry::get('Zend_Translate')->_('%1s Star'), 2),
                '1' => sprintf(Zend_Registry::get('Zend_Translate')->_('%1s Star'), 1),
            ),
        );

        $reviewForm[] = array(
            'type' => 'Checkbox',
            'name' => 'recommend',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Only Recommended Reviews'),
        );

        $reviewForm[] = array(
            'type' => 'Submit',
            'name' => 'done',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Search'),
        );

        return $reviewForm;
    }

    public function getEditorCreateForm($params) {

        //GET VIEWER INFO
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $event_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('event_id');
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

        $editorCreate[] = array(
            'type' => 'Textarea',
            'name' => 'pros',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('The Good'),
            'hasValidators' => 'true'
        );

        $editorCreate[] = array(
            'type' => 'Textarea',
            'name' => 'cons',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('The Bad'),
            'hasValidators' => 'true'
        );

        $editorCreate[] = array(
            'type' => 'Textarea',
            'name' => 'title',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('The Bottom Line'),
            'hasValidators' => 'true'
        );

        $editorCreate[] = array(
            'type' => 'Textarea',
            'name' => 'body',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Conclusion'),
            'hasValidators' => 'true'
        );

        if ($params['item'] && $params['item']['status'] == 1) {
            $editorCreate[] = array(
                'type' => 'Textarea',
                'name' => 'update_reason',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Reason Of Updation'),
                'hasValidators' => 'true'
            );
        }
        if ($params['item'] && $params['item']['status'] != 1) {
            $editorCreate[] = array(
                'type' => 'Select',
                'name' => 'status',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Status'),
                'multiOptions' => array("1" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Published"), "0" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Saved As Draft")),
                'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('If this entry is published, it cannot be switched back to draft mode.')
            );
        }
        if ($params['item']) {
            $editorCreate[] = array(
                'type' => 'Submit',
                'name' => 'submit',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Save Changes'),
            );
        } else {
            $editorCreate[] = array(
                'type' => 'Submit',
                'name' => 'submit',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Save Changes'),
            );
        }

        return $editorCreate;
    }

    public function getEditorMailForm($params) {

        $editorMail[] = array(
            'type' => 'Text',
            'name' => 'sender_name',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Your Name'),
        );

        $editorMail[] = array(
            'type' => 'Text',
            'name' => 'sender_email',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Your Email'),
        );

        $editorMail[] = array(
            'type' => 'Textarea',
            'name' => 'message',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Message'),
        );

        $editorMail[] = array(
            'type' => 'Submit',
            'name' => 'send',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Send Email'),
            'ignore' => true,
        );
        return $editorMail;
    }

    public function setPhoto($photo, $subject, $needToUplode = false) {
        try {

            if ($photo instanceof Zend_Form_Element_File) {
                $file = $photo->getFileName();
            } else if (is_array($photo) && !empty($photo['tmp_name'])) {
                $file = $photo['tmp_name'];
            } else if (is_string($photo) && file_exists($photo)) {
                $file = $photo;
            } else {
                throw new Group_Model_Exception('invalid argument passed to setPhoto');
            }
        } catch (Exception $e) {
            
        }

        $imageName = $photo['name'];
        $name = basename($file);
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';

        $params = array(
            'parent_type' => 'siteevent_event',
            'parent_id' => $subject->getIdentity()
        );

        // Save
        $storage = Engine_Api::_()->storage();

        // Resize image (main)
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(720, 720)
                ->write($path . '/m_' . $imageName)
                ->destroy();

        // Resize image (profile)
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(200, 400)
                ->write($path . '/p_' . $imageName)
                ->destroy();

        // Resize image (normal)
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(140, 160)
                ->write($path . '/in_' . $imageName)
                ->destroy();

        // Resize image (icon)
        $image = Engine_Image::factory();
        $image->open($file);

        $size = min($image->height, $image->width);
        $x = ($image->width - $size) / 2;
        $y = ($image->height - $size) / 2;

        $image->resample($x, $y, $size, $size, 48, 48)
                ->write($path . '/is_' . $imageName)
                ->destroy();

        // Store
        $iMain = $storage->create($path . '/m_' . $imageName, $params);
        $iProfile = $storage->create($path . '/p_' . $imageName, $params);
        $iIconNormal = $storage->create($path . '/in_' . $imageName, $params);
        $iSquare = $storage->create($path . '/is_' . $imageName, $params);

        $iMain->bridge($iProfile, 'thumb.profile');
        $iMain->bridge($iIconNormal, 'thumb.normal');
        $iMain->bridge($iSquare, 'thumb.icon');

        // Remove temp files
        @unlink($path . '/p_' . $imageName);
        @unlink($path . '/m_' . $imageName);
        @unlink($path . '/in_' . $imageName);
        @unlink($path . '/is_' . $imageName);

        // Update row
        if (empty($needToUplode)) {
            $subject->modified_date = date('Y-m-d H:i:s');
            $subject->photo_id = $iMain->file_id;
            $subject->save();
        }

        // Add to album
        $viewer = Engine_Api::_()->user()->getViewer();
        $photoTable = Engine_Api::_()->getItemTable('siteevent_photo');
        $eventAlbum = $subject->getSingletonAlbum();
        $photoItem = $photoTable->createRow();
        $photoItem->setFromArray(array(
            'event_id' => $subject->getIdentity(),
            'album_id' => $eventAlbum->getIdentity(),
            'user_id' => $viewer->getIdentity(),
            'file_id' => $iMain->getIdentity(),
            'collection_id' => $eventAlbum->getIdentity()
        ));
        $photoItem->save();

        return $subject;
    }

    public function getDiarySearchForm() {

        $diarySearch[] = array(
            'type' => 'Text',
            'name' => 'search',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Search'),
        );

        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        if ($viewer_id) {
            $diarySearch[] = array(
                'type' => 'Select',
                'name' => 'search_diary',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Diaries'),
                'multiOptions' => array(
                    '' => '',
                    'my_diaries' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('My Event Diaries'),
                    'friends_diaries' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('My Friends Event Diaries'),
                ),
            );
        }

        $diarySearch[] = array(
            'type' => 'Text',
            'name' => 'member',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Member's Name/Email"),
        );

        $diarySearch[] = array(
            'type' => 'Select',
            'name' => 'orderby',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Browse By'),
            'multiOptions' => array(
                'diary_id' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Recent'),
                'total_item' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Maximum Events'),
                'view_count' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Viewed'),
            ),
        );

        $diarySearch[] = array(
            'type' => 'Submit',
            'name' => 'done',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Search'),
        );
        return $diarySearch;
    }

    public function getCreateDiaryForm() {

        $add[] = array(
            'type' => 'Text',
            'name' => 'title',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Diary Name'),
            'hasValidator' => 'true'
        );


        $add[] = array(
            'type' => 'Textarea',
            'name' => 'body',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Diary Note'),
        );

        $availableLabels = array(
            'everyone' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Everyone'),
            'registered' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('All Registered Members'),
            'owner_network' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends and Networks'),
            'owner_member_member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends of Friends'),
            'owner_member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends Only'),
            'owner' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Just Me')
        );

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('siteevent_diary', $viewer, 'auth_view');
        $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));
        $viewOptionsReverse = array_reverse($viewOptions);
        $orderPrivacyHiddenFields = 786590;

        if (count($viewOptions) > 1) {
            $add[] = array(
                'type' => 'Select',
                'name' => 'auth_view',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('View Privacy'),
                'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Who may see this diary?'),
                'multiOptions' => $viewOptions,
            );
        }

        $add[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Save'),
        );
        return $add;
    }

    public function getAddToDiaryForm() {
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        $diaryDatas = Engine_Api::_()->getDbtable('diaries', 'siteevent')->getUserDiaries($viewer_id);
        $diaryDatasCount = Count($diaryDatas);
        $event_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('event_id', null);
        $event = Engine_Api::_()->getItem('siteevent_event', $event_id);

        $diaryIdsDatas = Engine_Api::_()->getDbtable('diarymaps', 'siteevent')->pageDiaries($event_id, $viewer_id);

        if (!empty($diaryIdsDatas)) {
            $diaryIdsDatas = $diaryIdsDatas->toArray();
            $diaryIds = array();
            foreach ($diaryIdsDatas as $diaryIdsData) {
                $diaryIds[] = $diaryIdsData['diary_id'];
            }
        }

        foreach ($diaryDatas as $diaryData) {

            if (in_array($diaryData->diary_id, $diaryIds)) {
                $add[] = array(
                    'type' => 'Checkbox',
                    'name' => 'inDiary_' . $diaryData->diary_id,
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($diaryData->title),
                    'value' => 1,
                );
            } else {
                $add[] = array(
                    'type' => 'Checkbox',
                    'name' => 'diary_' . $diaryData->diary_id,
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($diaryData->title),
                    'value' => 0,
                );
            }
        }


        if ($diaryDatasCount) {
            $add[] = array(
                'type' => 'Text',
                'name' => 'title',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Diary Name'),
            );
        } else {
            $add[] = array(
                'type' => 'Text',
                'name' => 'title',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Diary Name'),
                'hasValidator' => 'true'
            );
        }

        $add[] = array(
            'type' => 'Textarea',
            'name' => 'body',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Description'),
        );

        $availableLabels = array(
            'everyone' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Everyone'),
            'registered' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('All Registered Members'),
            'owner_network' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends and Networks'),
            'owner_member_member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends of Friends'),
            'owner_member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends Only'),
            'owner' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Just Me')
        );

        $viewer = Engine_Api::_()->user()->getViewer();
        $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('siteevent_diary', $viewer, 'auth_view');
        $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));
        $viewOptionsReverse = array_reverse($viewOptions);
        $orderPrivacyHiddenFields = 786590;

        if (count($viewOptions) > 1) {
            $add[] = array(
                'type' => 'Select',
                'name' => 'auth_view',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('View Privacy'),
                'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Who may see this diary?'),
                'multiOptions' => $viewOptions,
            );
        }

        $add[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Save'),
        );
        return $add;
    }

    public function getVideoBrowseSearchForm() {
        $getCategoryArray = $searchForm = array();
        $viewer = Engine_Api::_()->user()->getViewer();
        $searchForm[] = array(
            'type' => 'Text',
            'name' => 'search',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Search')
        );

        $searchForm[] = array(
            'type' => 'Select',
            'name' => 'orderby',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Browse By'),
            'multiOptions' => array(
                'creation_date' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Recent'),
                'view_count' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Most Viewed'),
                'rating' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Highest Rated'),
            )
        );

        $searchForm[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Search')
        );

        return $searchForm;
    }

    public function getOverviewForm() {

        $event_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('event_id', null);
        $siteevent = Engine_Api::_()->getItem('siteevent_event', $event_id);

        //GET VIEWER
        $viewer = Engine_Api::_()->user()->getViewer();

        $overview[] = array(
            'type' => 'Textarea',
            'name' => 'overview',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Overview'),
            'hasValidators' => 'true'
        );

        $overview[] = array(
            'type' => 'Submit',
            'name' => 'save',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Save Overview'),
        );
        return $overview;
    }

    public function getNotificationForm($isLeader) {

        $isTicketBasedEvent = Engine_Api::_()->siteevent()->isTicketBasedEvent();

        $notification[] = array(
            'type' => 'Checkbox',
            'name' => 'email',
            'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Email Notifications'),
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Send email notifications to me when people perform various actions on this event (Below you can individually activate emails for the actions)."),
        );

        //GET EITHER VIEWER IS LEADER OR NOT

        if ($isLeader) {
            if ($isTicketBasedEvent) {
                $optionsArray = array("posted" => "People post updates on this event", "created" => "People create various contents on this event");
            } else {
                $optionsArray = array("posted" => "People post updates on this event", "created" => "People create various contents on this event", "joined" => "People join this event", "rsvp" => "Guests change RSVP for this event");
            }

            $notification[] = array(
                'type' => 'MultiCheckbox',
                'name' => 'action_email',
                'multiOptions' => $optionsArray,
                    //'value' => array("posted", "created", "joined", "rsvp")
            );
        } else {
            $notification[] = array(
                'type' => 'MultiCheckbox',
                'name' => 'action_email',
                'multiOptions' => array("posted" => "People post updates on this event", "created" => "People create various contents on this event"),
                    //'value' => array("posted", "created")
            );
        }
        $notification[] = array(
            'type' => 'Checkbox',
            'name' => 'notification',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Site Notifications'),
            'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Send notification updates to me when people perform various actions on this event (Below you can individually activate notifications for the actions)."),
            'value' => 1,
        );

        if ($isLeader) {

            if ($isTicketBasedEvent) {
                $optionsArray = array("posted" => "People post updates on this event", "created" => "People create various contents on this event", "comment" => "People post comments on this event", "like" => "People like this event", "follow" => "People follow this event", "title" => "Event owner change title of this event", "location" => "Event owner change location of this event", "time" => "Event owner change time of this event", "venue" => "Event owner change venue of this event");
            } else {
                $optionsArray = array("posted" => "People post updates on this event", "created" => "People create various contents on this event", "comment" => "People post comments on this event", "like" => "People like this event", "follow" => "People follow this event", "joined" => "People join this event", "rsvp" => "Guests change RSVP for this event", "title" => "Event owner change title of this event", "location" => "Event owner change location of this event", "time" => "Event owner change time of this event", "venue" => "Event owner change venue of this event");
            }

            $notification[] = array(
                'type' => 'MultiCheckbox',
                'name' => 'action_notification',
                'multiOptions' => $optionsArray,
                    //  'value' => array("posted", "created", "comment", "like", "follow", "joined", "rsvp", "title", "location", "time", "venue")
            );
        } else {
            $notification[] = array(
                'type' => 'MultiCheckbox',
                'name' => 'action_notification',
                'multiOptions' => array("posted" => "People post updates on this event", "created" => "People create various contents on this event", "comment" => "People post comments on this event", "title" => "Event owner change title of this event", "location" => "Event owner change location of this event", "time" => "Event owner change time of this event", "venue" => "Event owner change venue of this event"),
                    //'value' => array("posted", "created", "comment", "title", "location", "time", "venue")
            );
        }

        $notification[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Save Settings'),
        );
        return $notification;
    }

    public function getReviewCreateForm($widgetSettingsReviews) {
        //GET VIEWER INFO
        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        //GET EVENT ID
        $getItemEvent = $widgetSettingsReviews['item'];
        $siteevent_proscons = $widgetSettingsReviews['settingsReview']['siteevent_proscons'];
        $siteevent_limit_proscons = $widgetSettingsReviews['settingsReview']['siteevent_limit_proscons'];
        $siteevent_recommend = $widgetSettingsReviews['settingsReview']['siteevent_recommend'];

        if ($siteevent_proscons) {
            if ($siteevent_limit_proscons) {
                $createReview[] = array(
                    'type' => 'Textarea',
                    'name' => 'pros',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Pros'),
                    'description' => Zend_Registry::get('Zend_Translate')->_("What do you like about this Event?"),
                    'hasValidator' => 'true'
//                   
                );
            } else {
                $createReview[] = array(
                    'type' => 'Textarea',
                    'name' => 'pros',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Pros'),
                    'description' => Zend_Registry::get('Zend_Translate')->_("What do you like about this Event?"),
                    'hasValidator' => 'true',
                );
            }


            if ($siteevent_limit_proscons) {
                $createReview[] = array(
                    'type' => 'Textarea',
                    'name' => 'cons',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Cons'),
                    'description' => Zend_Registry::get('Zend_Translate')->_("What do you dislike about this Event?"),
                    'hasValidator' => 'true',
                );
            } else {
                $createReview[] = array(
                    'type' => 'Textarea',
                    'name' => 'cons',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Cons'),
                    'description' => Zend_Registry::get('Zend_Translate')->_("What do you dislike about this Event?"),
                    'hasValidator' => 'true',
                );
            }
        }

        $createReview[] = array(
            'type' => 'Textarea',
            'name' => 'title',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('One-line summary'),
        );
//
//        $profileTypeReview = $this->getProfileTypeReview();
//        if (!empty($profileTypeReview)) {
//            
//            $customFields = $this->getSiteeventFormCustomStandard(array(
//                'item' => 'siteevent_review',
//                'topLevelId' => 1,
//                'topLevelValue' => $profileTypeReview,
//                'decorators' => array(
//                    'FormElements'
//            )));
//
//            $customFields->removeElement('submit');
//
//            $this->addSubForms(array(
//                'fields' => $customFields
//            ));
//        }

        $createReview[] = array(
            'type' => 'Textarea',
            'name' => 'body',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Summary'),
        );

        if ($siteevent_recommend) {
            $createReview[] = array(
                'type' => 'Radio',
                'name' => 'recommend',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Recommended'),
                'description' => sprintf(Zend_Registry::get('Zend_Translate')->_("Would you recommend %s to a friend?"), $event_title),
                'multiOptions' => array(
                    1 => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Yes'),
                    0 => Engine_Api::_()->getApi('Core', 'siteapi')->translate('No')
                ),
            );
        }

        $createReview[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Submit'),
        );
        return $createReview;
    }

    public function getReviewUpdateForm() {

        $updateReview[] = array(
            'type' => 'Textarea',
            'name' => 'body',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Summary'),
        );

        $updateReview[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Add your Opinion'),
        );
        return $updateReview;
    }

    /**
     * Get the video create form.
     * 
     * @return array
     */
    public function getVideoCreateForm($subject = null) {
        $videoForm = array();
        $viewer = Engine_Api::_()->user()->getViewer();

        $videoForm[] = array(
            'type' => 'Text',
            'name' => 'title',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Video Title'),
            'hasValidator' => true
        );

        $videoForm[] = array(
            'type' => 'Text',
            'name' => 'tags',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Tags (Keywords)'),
            'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Separate tags with commas.')
        );

        $videoForm[] = array(
            'type' => 'Textarea',
            'name' => 'description',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Video Description'),
        );

        $videoForm[] = array(
            'type' => 'Checkbox',
            'name' => 'search',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Show this video entry in search results')
        );

        if (empty($subject)) {

            // Element: Add Type
            $video_options = Array();
            $video_options[2] = Engine_Api::_()->getApi('Core', 'siteapi')->translate('Vimeo');

            //My Computer
            $allowed_upload = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('video', $viewer, 'upload');
            $ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->video_ffmpeg_path;
            if (!empty($ffmpeg_path) && $allowed_upload) {
                $video_options[3] = Engine_Api::_()->getApi('Core', 'siteapi')->translate('My Device');
            }
            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('video.youtube.apikey')) {
                $video_options[1] = Engine_Api::_()->getApi('Core', 'siteapi')->translate('YouTube');
            }
            $videoForm[] = array(
                'type' => 'Select',
                'name' => 'type',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Video Source'),
                'multiOptions' => $video_options,
            );

            $videoForm[] = array(
                'type' => 'Text',
                'name' => 'url',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Video Link (URL)'),
                'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Paste the web address of the video here.'),
            );

            $videoForm[] = array(
                'type' => 'File',
                'name' => 'filedata',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Add Video')
            );

            $videoForm[] = array(
                'type' => 'Submit',
                'name' => 'submit',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Post Video')
            );
        } else {
            $videoForm[] = array(
                'type' => 'Submit',
                'name' => 'submit',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Save Video')
            );
        }

        return $videoForm;
    }

    public function getEditLocationForm($params) {
        $editLocation[] = array(
            'type' => 'Text',
            'name' => 'formatted_address',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Formatted Address'),
        );

        $editLocation[] = array(
            'type' => 'Text',
            'name' => 'latitude',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Latitude'),
        );

        $editLocation[] = array(
            'type' => 'Text',
            'longitude',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Longitude'),
        );

        $editLocation[] = array(
            'type' => 'Text',
            'name' => 'address',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Street Address'),
        );

        $editLocation[] = array(
            'type' => 'Text',
            'name' => 'city',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('City'),
        );

        $editLocation[] = array(
            'type' => 'Text',
            'name' => 'zipcode',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Zipcode'),
        );

        $editLocation[] = array(
            'type' => 'Text',
            'name' => 'state',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('State'),
        );


        $editLocation[] = array(
            'type' => 'Text',
            'name' => 'country',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Country'),
        );


        $editLocation[] = array(
            'type' => 'Submit',
            'name' => 'submit',
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Save Changes'),
        );
        return $editLocation;
    }

    public function getVideoURL($video, $autoplay = true) {
        // YouTube
        if ($video->type == 1) {
            return 'www.youtube.com/embed/' . $video->code . '?wmode=opaque' . ($autoplay ? "&autoplay=1" : "");
        } elseif ($video->type == 2) { // Vimeo
            return 'player.vimeo.com/video/' . $video->code . '?title=0&amp;byline=0&amp;portrait=0&amp;wmode=opaque' . ($autoplay ? "&amp;autoplay=1" : "");
        } elseif ($video->type == 3) { // Uploded Videos
            $staticBaseUrl = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.general.staticBaseUrl', null);
            $video_location = Engine_Api::_()->storage()->get($video->file_id, $video->getType())->getHref();
            $getHost = Engine_Api::_()->getApi('Core', 'siteapi')->getHost();
            return (empty($staticBaseUrl)) ? $getHost . $video_location : $video_location;
        }
    }

    public function getForm($subject = null, $parent_type = null, $parent_id = null, $host = null, $host_icons = null) {

        $user = $viewer = Engine_Api::_()->user()->getViewer();
        $viewer_id = $user->getIdentity();
        $createForm = array();
        //PACKAGE ID
        $package_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('id', null);

        $note = '';
        $seaocoreCalenderDayStart = Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.calendar.daystart', 1);
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $reviewApi = Engine_Api::_()->siteevent();

        //PACKAGE BASED CHECKS
        $hasPackageEnable = Engine_Api::_()->siteevent()->hasPackageEnable();

        $siteeventRepeatEventsTypeInfo = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventrepeatevents.type.info', null);
        $siteeventrepeatLsettings = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventrepeat.lsettings', null);
        $siteeventRepeatGetShowViewType = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventrepeat.getshow.viewtype', null);
        $expertTipsContent = strip_tags(Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.experttips'));
        $expertTipsContent = str_replace('&nbsp;', '', $expertTipsContent);
        $eventRepeatHostType = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
        $tempEventRepeatTypeInfo = @md5($eventRepeatHostType . $siteeventrepeatLsettings);
        $expertTipsContent = trim($expertTipsContent);
        if ($expertTipsContent) {
            $createForm['expert_tip'] = (sprintf(Zend_Registry::get('Zend_Translate')->_('Compose your new event below, then click "Create" to publish the event. %1$sExpert Tips%2$s'), "<span class='siteevent_link_wrap'><i class='siteevent_icon icon_siteevent_tip mright5'></i><a href='javascript:void(0)' onclick='expertTips()'>", "</a></span>"));
        }


        $createFormFields = array(
            'venue',
            'location',
            'tags',
            'photo',
            'description',
            'overview',
            'price',
            'host',
            'viewPrivacy',
            'commentPrivacy',
            'postPrivacy',
            'discussionPrivacy',
            'photoPrivacy',
            'videoPrivacy',
            'rsvp',
            'invite',
            'status',
            'search',
            'guestLists'
        );

//        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventdocument')) {
//            $createFormFields = array_merge($createFormFields, array('document'));
//        }
        try {
            if (empty($event_id) && Engine_Api::_()->getApi('settings', 'core')->hasSetting('siteevent.createFormFields')) {

                $createFormFields = $settings->getSetting('siteevent.createFormFields', $createFormFields);
            }

            if (Engine_Api::_()->siteevent()->isTicketBasedEvent() && in_array('rsvp', $createFormFields)) {
                $indexRSVP = array_search('rsvp', $createFormFields);
                unset($createFormFields[$indexRSVP]);
            }

            if (Engine_Api::_()->siteevent()->isTicketBasedEvent() && in_array('invite', $createFormFields)) {
                $indexInvite = array_search('invite', $createFormFields);
                unset($createFormFields[$indexInvite]);
            }


            $createForm[] = array(
                'type' => 'Text',
                'name' => 'title',
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Event Title"),
                'hasValidator' => 'true'
            );

            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.onlineevent.allow', 1) || ($subject && $subject->is_online == 1)) {
                $createForm[] = array(
                    'type' => 'Checkbox',
                    'name' => 'is_online',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Online Event?')
                );
            }


            if (!empty($createFormFields) && in_array('venue', $createFormFields) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.location', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.veneuname', 1)) {
                $createForm[] = array(
                    'type' => 'Text',
                    'name' => 'venue_name',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Venue Name'),
                );
            }


            if (!empty($createFormFields) && in_array('location', $createFormFields) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.location', 1)) {

                $locationDefault = $settings->getSetting('seaocore.locationdefault', '');
                $seaocore_locationspecific = $settings->getSetting('seaocore.locationspecific', '');
                $seaocore_locationspecificcontent = $settings->getSetting('seaocore.locationspecificcontent', '');

                if ($seaocore_locationspecific && $seaocore_locationspecificcontent) {
                    $locations = Engine_Api::_()->getDbTable('locationcontents', 'seaocore')->getLocations(array('status' => 1));
                    $locationsArray = array();
                    $locationsArray[] = '';
                    foreach ($locations as $location) {
                        $locationsArray[$location->location] = $location->title;
                    }
                    if ($locations) {

                        $createForm[] = array(
                            'type' => 'Select',
                            'name' => 'location',
                            'description' => 'Eg: Fairview Park, Berkeley, CA',
                            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Enter a location'),
                            'multiOptions' => $locationsArray
                        );
                    }
                } else {
                    $createForm[] = array(
                        'type' => 'Text',
                        'name' => 'location',
                        'description' => 'Eg: Fairview Park, Berkeley, CA',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Enter a location'),
                    );
                }
            }

            $user = Engine_Api::_()->user()->getViewer();
            $user_level = Engine_Api::_()->user()->getViewer()->level_id;

            //$defaultProfileId = "0_0_" . $this->getDefaultProfileId();//todo error in deafultProfileId
            //    $translate = Zend_Registry::get('Zend_Translate');
            $categories = Engine_Api::_()->getDbTable('categories', 'siteevent')->getCategories(array('category_id', 'category_name'), null, 0, 0, 1);

            if (count($categories) != 0) {
                $getCategories = array();
                $categories_prepared[0] = "";
                foreach ($categories as $category) {
//                                $subCategories = array();
//                               $subCategoriesObj = Engine_Api::_()->getDbTable('categories', 'siteevent')->getSubCategories($category->category_id);
//                    foreach ($subCategoriesObj as $subcategory) {
//                        $subsubCategories = array();
//                        $subsubCategoriesObj = Engine_Api::_()->getDbTable('categories', 'siteevent')->getSubCategories($subcategory->category_id);
//                        foreach ($subsubCategoriesObj as $subsubcategory) {
//                            $subsubCategories[] = array(
//                                'id' => $subsubcategory->category_id,
//                                'label' => $subsubcategory->category_name,
//                                'type' => 'subSubCategory'
//                            );
//                        }
//                        $subCategories[] = array(
//                            'id' => $subcategory->category_id,
//                            'label' => $subcategory->category_name,
//                            'type' => 'subCategory',
//                            'subsubCategories' => $subsubCategories
//                        );
//                    }
                    $getCategories[$category->category_id] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($category->category_name);
                }
                $createForm[] = array(
                    'type' => 'Select',
                    'name' => 'category_id',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Category'),
                    'multiOptions' => $getCategories,
                    'hasValidator' => 'true'
                );

                $subCategoriesObj = Engine_Api::_()->getDbTable('categories', 'siteevent')->getSubCategories($subject->category_id);
                foreach ($subCategoriesObj as $subcategory) {
                    $getSubCategories[$subcategory->category_id] = $subcategory->category_name;
                }
                if (isset($getSubCategories) && !empty($getSubCategories)) {
                    $createForm[] = array(
                        'type' => 'Select',
                        'name' => 'subcategory_id',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('SubCategory'),
                        'multiOptions' => $getSubCategories,
                    );
                }
                $subsubCategoriesObj = Engine_Api::_()->getDbTable('categories', 'siteevent')->getSubCategories($subject->subcategory_id);
                foreach ($subsubCategoriesObj as $subsubcategory) {
                    $getSubSubCategories[$subsubcategory->category_id] = $subsubcategory->category_name;
                }
                if (isset($getSubSubCategories) && !empty($getSubSubCategories)) {
                    $createForm[] = array(
                        'type' => 'Select',
                        'name' => 'subsubcategory_id',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('3rd Level Category'),
                        'multiOptions' => $getSubSubCategories,
                    );
                }
            }
            $categories = Engine_Api::_()->getDbTable('categories', 'siteevent')->getCategories(array('category_id', 'category_name'), null, 0, 0, 1);

            if (count($categories) != 0) {
                foreach ($categories as $category) {
                    $subCategories = array();
                    $subCategoriesObj = Engine_Api::_()->getDbTable('categories', 'siteevent')->getSubCategories($category->category_id);
                    $getCategories[$category->category_id] = $category->category_name;

                    $getsubCategories = array();

                    foreach ($subCategoriesObj as $subcategory) {

                        $subsubCategories = array();
                        $subsubCategoriesObj = Engine_Api::_()->getDbTable('categories', 'siteevent')->getSubCategories($subcategory->category_id);

                        $subsubCategories = array();
                        foreach ($subsubCategoriesObj as $subsubcategory) {
                            $subsubCategories[$subsubcategory->category_id] = $subsubcategory->category_name;
                        }
                        if (isset($subsubCategories) && !empty($subsubCategories)) {

                            $subsubCategoriesForm[$subcategory->category_id] = array(
                                'type' => 'Select',
                                'name' => 'subsubcategory_id',
                                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('3rd Level Category'),
                                'multiOptions' => $subsubCategories,
                            );
                        }
                        $getsubCategories[$subcategory->category_id] = $subcategory->category_name;
                    }
                    $subcategoriesForm = array(
                        'type' => 'Select',
                        'name' => 'subcategory_id',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Sub-Category'),
                        'multiOptions' => $getsubCategories,
                    );
                    $form[$category->category_id]['form'] = $subcategoriesForm;
                    if (isset($subsubCategoriesForm) && count($subsubCategoriesForm) > 0)
                        $form[$category->category_id]['subsubcategories'] = $subsubCategoriesForm;
                    $subsubCategoriesForm = array();
                }
                $categoriesForm = array(
                    'type' => 'Select',
                    'name' => 'category_id',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Category'),
                    'multiOptions' => $getCategories,
                    'hasValidator' => 'true'
                );
            }
//            $create['form'] = $categoriesForm;
//            $responseForm['categories'] = $create;

            if (!empty($createFormFields) && in_array('tags', $createFormFields) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.tags', 1)) {
                $createForm[] = array(
                    'type' => 'Text',
                    'name' => 'tags',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Tags (Keywords)'),
                    'description' => Zend_Registry::get('Zend_Translate')->_('Separate tags with commas.'),
                );
            }

            $createForm[] = array(
                'type' => 'Date',
                'name' => 'starttime',
                'label' => Zend_Registry::get('Zend_Translate')->_("Start Time"),
                'hasValidator' => true
            );

            $createForm[] = array(
                'type' => 'Date',
                'name' => 'endtime',
                'label' => Zend_Registry::get('Zend_Translate')->_("End Time"),
                'hasValidator' => true
            );

            //CHECK IF SITEEVENT REPEAT MODULE EXIST AND ENABLE ON THE SITE ONLY THEN WE WILL ACTIVATE THE REPEATING EVENT FEATURE.
            if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventrepeat') && (!empty($siteeventRepeatGetShowViewType) || empty($siteeventRepeatEventsTypeInfo) || ($siteeventRepeatEventsTypeInfo == $tempEventRepeatTypeInfo))) {
                $eventrepeat_prepared = array('never' => 'Never', 'daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly', 'custom' => 'Other (be specific)');
                if ($subject && (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.repeat', 1) && empty($subject->repeat_params) || !$editFullEventDate)) {
                    if (!empty($subject->repeat_params)) {
                        $repeatEventInfo = json_decode($subject->repeat_params, true);
                        $eventrepeat_prepared = array($repeatEventInfo['eventrepeat_type'] => $repeatEventInfo['eventrepeat_type']);
                    } else {
                        $eventrepeat_prepared = array('never' => 'Never');
                    }
                }
                $createForm[] = array(
                    'type' => 'Select',
                    'name' => 'eventrepeat_id',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Event Repeats'),
                    'multiOptions' => $eventrepeat_prepared,
                    'value' => 'never',
                );

                $tempForm[] = array(
                    'type' => 'Date',
                    'name' => 'start_date',
                    'label' => Zend_Registry::get('Zend_Translate')->_("Start Date:"),
                    'multioptions' => $daily_dates
                );

                $tempForm[] = array(
                    'type' => 'Date',
                    'name' => 'end_date',
                    'label' => Zend_Registry::get('Zend_Translate')->_("End Date"),
                    'hasValidator' => true
                );

                $tempForm[] = array(
                    'type' => 'Button',
                    'name' => 'add_date',
                    'label' => Zend_Registry::get('Zend_Translate')->_("Add Date"),
                    'hasValidator' => true
                );

                $repeatForm['custom'] = $tempForm;

                $selectedDay = 1;
                $daily_disabled = '';
                $daily_dates = array();
                $selectedDay = ($repeatEventInfo['repeat_interval'] / (24 * 3600));
                if (!$editFullEventDate)
                    $daily_disabled = 'disabled';
                for ($i = 1; $i <= 31; $i++) {
                    if ($selectedDay == $i)
                        $daily_dates_value[$i] = $i;
                    else
                        $daily_dates[$i] = $i;
                }

                $tempForm[] = array(
                    'type' => 'Select',
                    'name' => 'repeat_interval',
                    'label' => Zend_Registry::get('Zend_Translate')->_("Repeat every:"),
                    'multioptions' => $daily_dates
                );

                $tempForm[] = array(
                    'type' => 'Date',
                    'name' => 'date',
                    'label' => Zend_Registry::get('Zend_Translate')->_("End Time"),
                    'hasValidator' => true
                );
                $repeatForm['daily'] = $tempForm;


                $tempForm = array();
                $selectedWeekDay = $repeatEventInfo['repeat_week'];
                $weekdays = array(1 => 'monday', 2 => 'tuesday', 3 => 'wednesday', 4 => 'thursday', 5 => 'friday', 6 => 'saturday', 7 => 'sunday');
                foreach ($repeatEventInfo['repeat_weekday'] as $weekday) {
                    $repeatWeekDays[] = $weekday;
                }
                if (!$this->editFullEventDate)
                    $weekly_disabled = 'disabled';

                for ($i = 1; $i <= 12; $i++) {

                    if ($selectedWeekDay == $i)
                        $weekly_days_value[$i] = $i;
                    else
                        $weekly_days[$i] = $i;
                }
                $tempForm[] = array(
                    'type' => 'Select',
                    'name' => 'repeat_week',
                    'label' => Zend_Registry::get('Zend_Translate')->_("Repeat every:"),
                    'multioptions' => $weekly_days
                );

                $tempForm[] = array(
                    'type' => 'MultiCheckbox',
                    'name' => 'repeat_weekday',
                    'label' => Zend_Registry::get('Zend_Translate')->_(""),
                    'multioptions' => $weekdays
                );
                $tempForm[] = array(
                    'type' => 'Date',
                    'name' => 'date',
                    'label' => Zend_Registry::get('Zend_Translate')->_("End Time"),
                    'hasValidator' => true
                );
                $repeatForm['weekly'] = $tempForm;


                $tempForm = array();
                $monthly_disabled = '';
                $repeat_week = 'first';
                $repeat_weekday = 'monday';
                $monthly_absoluteday = 1;
                $monthly_repeatinterval = 1;
                $relative_day = array('first' => 'First', 'second' => 'Second', 'third' => 'Third', 'fourth' => 'Fourth', 'fifth' => 'Fifth', 'last' => 'Last');
                $i = 0;
                foreach ($relative_day as $key => $day) {
                    $i++;
                    if ($eventtype == 'monthly' && isset($repeatEventInfo['repeat_week']) && $repeatEventInfo['repeat_week'] == $i) {
                        $repeat_week = $key;
                        $week_monthly_value[$key] = $day;
                    } else
                        $week_monthly[$key] = $day;
                }
                $tempForm[] = array(
                    'type' => 'Select',
                    'name' => 'repeat_week',
                    'label' => Zend_Registry::get('Zend_Translate')->_("Repeat every:"),
                    'multioptions' => $weekly_days
                );
                $weekdays = array(1 => 'monday', 2 => 'tuesday', 3 => 'wednesday', 4 => 'thursday', 5 => 'friday', 6 => 'saturday', 7 => 'sunday');


                $tempForm[] = array(
                    'type' => 'Select',
                    'name' => 'repeat_weekday',
                    'label' => Zend_Registry::get('Zend_Translate')->_(""),
                    'multioptions' => $weekdays
                );
                for ($i = 1; $i <= 12; $i++) {

                    if ($selectedWeekDay == $i)
                        $weekly_days_value[$i] = $i;
                    else
                        $weekly_days[$i] = $i;
                }
                $tempForm[] = array(
                    'type' => 'Select',
                    'name' => 'repeat_month',
                    'label' => Zend_Registry::get('Zend_Translate')->_("Repeat every:"),
                    'multioptions' => $weekly_days
                );
                $tempForm[] = array(
                    'type' => 'Date',
                    'name' => 'date',
                    'label' => Zend_Registry::get('Zend_Translate')->_("End Time"),
                    'hasValidator' => true
                );

                $repeatForm['monthly'] = $tempForm;

                if (isset($subject) && isset($subject->repeat_params)) {

                    $repeat_params = json_decode($subject->repeat_params, true);
                    if (isset($repeat_params) && ($repeat_params['eventrepeat_type'] == 'daily' || $repeat_params['eventrepeat_type'] == 'weekly' || $repeat_params['eventrepeat_type'] == 'monthly' )) {
                        $createForm[] = $repeatForm[$repeat_params['eventrepeat_type']];
                    }
                }
            }

            $allowed_upload = Engine_Api::_()->authorization()->getPermission($user_level, 'siteevent_event', "photo");
            if (empty($subject) && !empty($createFormFields) && in_array('photo', $createFormFields) && $allowed_upload) {
                $createForm[] = array(
                    'type' => 'File',
                    'name' => 'photo',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Main Photo')
                );
            }


            //PACKAGE BASED CHECKS
            if ($hasPackageEnable) {
                if (Engine_Api::_()->siteeventpaid()->allowPackageContent($package_id, "overview")) {
                    $allowOverview = 1;
                } else {
                    $allowOverview = 0;
                }
            } else {//AUTHORIZATION CHECKS
                $allowOverview = Engine_Api::_()->authorization()->getPermission($user->level_id, 'siteevent_event', "overview");
            }
            //PACKAGE BASED CHECKS   
            $allowEdit = Engine_Api::_()->authorization()->getPermission($user->level_id, 'siteevent_event', "edit");

            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.overview', 1) && (!empty($createFormFields) && in_array('overview', $createFormFields)) && $allowOverview && $allowEdit) {
                $description = 'Short Description';
            } else {
                $description = 'Description';
            }

            if ((Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.bodyallow', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.bodyrequired', 1)) || (!empty($createFormFields) && in_array('description', $createFormFields) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.bodyallow', 1))) {
                $createForm[] = array(
                    'type' => 'Textarea',
                    'name' => 'body',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($description),
                    'has Validators' => 'true'
                );
            }

            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.overview', 1) && (!empty($createFormFields) && in_array('overview', $createFormFields)) && $allowOverview && $allowEdit && !$subject) {
                $upload_url = "";
                $viewer = Engine_Api::_()->user()->getViewer();
                $albumEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('album');
                if (Engine_Api::_()->authorization()->isAllowed('album', $viewer, 'create') && $albumEnabled) {
                    $upload_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'upload-photo'), 'siteevent_general', true);
                }

                $createForm[] = array(
                    'type' => 'Textarea',
                    'name' => 'overview',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Overview'),
                    'description' => 'Create a rich, attractive overview for your event. Switch the editor to Fullscreen mode by clicking on its icon below to comfortably create the overview.',
                );
            }
            if (!empty($createFormFields) && in_array('price', $createFormFields) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.price', 0)) {
                //   $localeObject = Zend_Registry::get('Locale');
                $currencyCode = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
                $currencyName = Zend_Locale_Data::getContent($localeObject, 'nametocurrency', $currencyCode);
                $createForm[] = array(
                    'type' => 'Text',
                    'name' => 'price',
                    'label' => sprintf(Zend_Registry::get('Zend_Translate')->_('Price (%s)'), $currencyName),
                );
            }

            $availableLabels = array(
                'everyone' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Everyone'),
                'registered' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('All Registered Members'),
                'owner_network' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends and Networks'),
                'owner_member_member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends of Friends'),
                'owner_member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends Only'),
                'member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Event Guests Only'),
                'leader' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Owner and Leaders Only')
            );


            $parentItem = Engine_Api::_()->getItem($parent_type, $parent_id);
            $explodeParentType = explode('_', $parent_type);

            if (!empty($explodeParentType) && isset($explodeParentType[0]) && isset($explodeParentType[1])) {
                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($explodeParentType[0] . 'member') && ($parent_type == 'sitepage_page' || $parent_type == 'sitebusiness_business' || $parent_type == 'sitegroup_group') && (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => $parent_type, 'item_module' => $explodeParentType[0])))) {
                    $shortTypeName = ucfirst($explodeParentType[1]);
                    $availableLabels = array(
                        'everyone' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Everyone'),
                        'registered' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('All Registered Members'),
                        'parent_member' => $shortTypeName . ' Members Only',
                        'member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Event Guests Only'),
                        'leader' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Just Me')
                    );
                } elseif (($parent_type == 'sitepage_page' || $parent_type == 'sitebusiness_business' || $parent_type == 'sitegroup_group' || $parent_type == 'sitestore_store') && (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => $parent_type, 'item_module' => $explodeParentType[0])))) {
                    $availableLabels = array(
                        'everyone' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Everyone'),
                        'registered' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('All Registered Members'),
                        'member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Event Guests Only'),
                        'leader' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Just Me')
                    );
                } elseif (($parent_type == 'sitereview_listing') && (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitereview_listing_' . $parentItem->listingtype_id, 'item_module' => 'sitereview')))) {
                    $availableLabels = array(
                        'everyone' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Everyone'),
                        'registered' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('All Registered Members'),
                        'member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Event Guests Only'),
                        'leader' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Just Me')
                    );
                }
            }
            if (Engine_Api::_()->siteevent()->isTicketBasedEvent() && isset($availableLabels['member'])) {
                unset($availableLabels['member']);
            }

            $view_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('siteevent_event', $user, "auth_view");
            $view_options = array_intersect_key($availableLabels, array_flip($view_options));
            if (!empty($createFormFields) && in_array('viewPrivacy', $createFormFields) && count($view_options) > 1) {
                $createForm[] = array(
                    'type' => 'Select',
                    'name' => 'auth_view',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('View Privacy'),
                    'description' => Zend_Registry::get('Zend_Translate')->_("Who may see this event?"),
                    'multiOptions' => $view_options,
                );
            }
            $comment_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('siteevent_event', $user, "auth_comment");
            $comment_options = array_intersect_key($availableLabels, array_flip($comment_options));
            if (!empty($createFormFields) && in_array('commentPrivacy', $createFormFields) && count($comment_options) > 1) {
                $createForm[] = array(
                    'type' => 'Select',
                    'name' => 'auth_comment',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Comment Privacy'),
                    'description' => Zend_Registry::get('Zend_Translate')->_("Who may comment on this event?"),
                    'multiOptions' => $comment_options,
                    'value' => key($comment_options),
                );
            }
            $availableLabels = array(
                'registered' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('All Registered Members'),
                'owner_network' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends and Networks'),
                'owner_member_member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends of Friends'),
                'owner_member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Friends Only'),
                'member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Event Guests Only'),
                'leader' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Owner and Leaders Only')
            );

            if (!empty($explodeParentType) && isset($explodeParentType[0]) && isset($explodeParentType[1])) {
                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($explodeParentType[0] . 'member') && ($parent_type == 'sitepage_page' || $this->_parent_type == 'sitebusiness_business' || $parent_type == 'sitegroup_group') && (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => $parent_type, 'item_module' => $explodeParentType[0])))) {
                    $shortTypeName = ucfirst($explodeParentType[1]);
                    $availableLabels = array(
                        'registered' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('All Registered Members'),
                        'parent_member' => $shortTypeName . ' Members Only',
                        'like_member' => 'Who liked this ' . $shortTypeName,
                        'member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Event Guests Only'),
                        'leader' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Just Me')
                    );
                } elseif (($parent_type == 'sitepage_page' || $parent_type == 'sitebusiness_business' || $parent_type == 'sitegroup_group' || $parent_type == 'sitestore_store') && (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => $parent_type, 'item_module' => $explodeParentType[0])))) {
                    $shortTypeName = ucfirst($explodeParentType[1]);
                    $availableLabels = array(
                        'registered' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('All Registered Members'),
                        'like_member' => 'Who liked this ' . $shortTypeName,
                        'member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Event Guests Only'),
                        'leader' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Just Me')
                    );
                } elseif (($parent_type == 'sitereview_listing') && (Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitereview_listing_' . $parentItem->listingtype_id, 'item_module' => 'sitereview')))) {
                    $availableLabels = array(
                        'registered' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('All Registered Members'),
                        'member' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Event Guests Only'),
                        'leader' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Just Me')
                    );
                }
            }

            if (Engine_Api::_()->siteevent()->isTicketBasedEvent() && isset($availableLabels['member'])) {
                unset($availableLabels['member']);
            }

            if (Engine_Api::_()->hasModuleBootstrap('advancedactivity')) {
                $post_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('siteevent_event', $user, "auth_post");
                $post_options = array_intersect_key($availableLabels, array_flip($post_options));

                if (!empty($createFormFields) && in_array('postPrivacy', $createFormFields) && count($post_options) > 1) {
                    $createForm[] = array(
                        'type' => 'Select',
                        'name' => 'auth_post',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Posting Updates Privacy'),
                        'description' => Zend_Registry::get('Zend_Translate')->_("Who may post updates on this event?"),
                        'multiOptions' => $post_options,
                    );
                } elseif (count($post_options) == 1) {
                    $createForm[] = array(
                        'type' => 'Select',
                        'name' => 'auth_post',
                        'value' => key($post_options),
                    );
                } else {
                    $createForm[] = array(
                        'type' => 'Select',
                        'name' => 'auth_post',
                        'value' => 'member',
                    );
                }
            }

            $topic_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('siteevent_event', $user, "auth_topic");
            $topic_options = array_intersect_key($availableLabels, array_flip($topic_options));

            if (!empty($createFormFields) && in_array('discussionPrivacy', $createFormFields) && count($topic_options) > 1) {

                $createForm[] = array(
                    'type' => 'Select',
                    'name' => 'auth_topic',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Discussion Topic Privacy'),
                    'description' => Zend_Registry::get('Zend_Translate')->_("Who may post discussion topics for this event?"),
                    'multiOptions' => $topic_options,
                );
            } elseif (count($topic_options) == 1) {

                $createForm[] = array(
                    'type' => 'Select',
                    'name' => 'auth_topic',
                    'value' => $topic_options,
                );
            } else {
                $createForm[] = array(
                    'type' => 'Select',
                    'name' => 'auth_topic',
                    'value' => 'member',
                );
            }

            $photo_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('siteevent_event', $user, "auth_photo");
            $photo_options = array_intersect_key($availableLabels, array_flip($photo_options));

            //PACKAGE BASED CHECKS
            $can_show_photo_list = true;
            if ($hasPackageEnable && !Engine_Api::_()->siteeventpaid()->allowPackageContent($package_id, "photo")) {
                $can_show_photo_list = false;
            }
            if (!empty($createFormFields) && in_array('photoPrivacy', $createFormFields) && count($photo_options) > 1 && $can_show_photo_list) {
                $createForm[] = array(
                    'type' => 'Select',
                    'name' => 'auth_photo',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Photo Privacy'),
                    'description' => Zend_Registry::get('Zend_Translate')->_("Who may upload photos for this event?"),
                    'multiOptions' => $photo_options,
                );
            } elseif (count($photo_options) == 1 && $can_show_photo_list) {
                $createForm[] = array(
                    'type' => 'Select',
                    'name' => 'auth_photo',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Photo Privacy'),
                    'value' => $photo_options
                );
            } else {
                $createForm[] = array(
                    'type' => 'Select',
                    'name' => 'auth_photo',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Photo Privacy'),
                    'value' => 'member',
                );
            }
//                START SITEEVENTDOCUMENT PLUGIN WORK
//        if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventdocument')) {
//
//            $document_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('siteevent_event', $user, 'auth_document');
//            $document_options = array_intersect_key($availableLabels, array_flip($document_options));
//
//            if (!empty($createFormFields) && in_array('document', $createFormFields) && count($document_options) > 1) {
//                $this->addElement('Select', 'auth_document', array(
//                    'label' => 'Documents Creation Privacy',
//                    'description' => 'Who may create documents in this event?',
//                    'multiOptions' => $document_options,
//                    'value' => 'member',
//                    'attribs' => array('class' => 'se_quick_advanced'),
//                ));
//                $this->auth_document->getDecorator('Description')->setOption('placement', 'append');
//            } elseif (count($document_options) == 1) {
//                $this->addElement('Hidden', 'auth_document', array('value' => key($document_options),
//                    'order' => ++$orderPrivacyHiddenFields));
//            } else {
//                $this->addElement('Hidden', 'auth_document', array(
//                    'value' => 'member',
//                    'order' => ++$orderPrivacyHiddenFields
//                ));
//            }
//        }
//        //END SITEEVENTDOCUMENT PLUGIN WORK    
//
            $videoEnable = $this->enableVideoPlugin();
            if ($videoEnable) {

                $video_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('siteevent_event', $user, "auth_video");
                $video_options = array_intersect_key($availableLabels, array_flip($video_options));

                //PACKAGE BASED CHECKS
                $can_show_video_list = true;
                if ($hasPackageEnable && !Engine_Api::_()->siteeventpaid()->allowPackageContent($package_id, "video")) {
                    $can_show_video_list = false;
                }
                if (!empty($createFormFields) && in_array('videoPrivacy', $createFormFields) && count($video_options) > 1 && $can_show_video_list) {
                    $createForm[] = array(
                        'type' => 'Select',
                        'name' => 'auth_video',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Video Privacy'),
                        'value' => 'member',
                        'description' => Zend_Registry::get('Zend_Translate')->_("Who may add videos for this event?"),
                        'multiOptions' => $video_options,
                    );
                }
            }


            //NETWORK BASE PAGE VIEW PRIVACY
            if (Engine_Api::_()->siteevent()->listBaseNetworkEnable()) {
                // Make Network List
                $table = Engine_Api::_()->getDbtable('networks', 'network');
                $select = $table->select()
                        ->from($table->info('name'), array('network_id', 'title'))
                        ->order('title');
                $result = $table->fetchAll($select);

                $networksOptions = array('0' => 'Everyone');
                foreach ($result as $value) {
                    $networksOptions[$value->network_id] = $value->title;
                }
//
                if (count($networksOptions) > 0) {
                    $createForm[] = array(
                        'type' => 'Multiselect',
                        'name' => 'networks_privacy',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Networks Selection'),
                        'description' => Zend_Registry::get('Zend_Translate')->_("Select the networks, members of which should be able to see your event. (Press Ctrl and click to select multiple networks. You can also choose to make your event viewable to everyone.)"),
                        'multiOptions' => $networksOptions,
                    );
                } else {
                    
                }
            }
//
            if (empty($subject) && !empty($createFormFields) && in_array('status', $createFormFields)) {
                $createForm[] = array(
                    'type' => 'Select',
                    'name' => 'draft',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Status'),
                    'multiOptions' => array("1" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Published"), "0" => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Saved As Draft")),
                    'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('If this event is published, it cannot be switched back to draft mode.'),
                );
            }

            if (!empty($createFormFields) && in_array('rsvp', $createFormFields) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.invite.rsvp.option', 1)) {
                $createForm[] = array(
                    'type' => 'Radio',
                    'name' => 'approval',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Approve members?'),
                    'description' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('When people try to join this event, should they be allowed to join immediately, or should they be forced to wait for approval?'),
                    'multiOptions' => array(
                        0 => Engine_Api::_()->getApi('Core', 'siteapi')->translate('New members can join immediately this event.'),
                        1 => Engine_Api::_()->getApi('Core', 'siteapi')->translate('New members must be approved to join this event.')
                    ),
                    'value' => '1',
                );
            }
            if (!empty($createFormFields) && in_array('invite', $createFormFields) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.invite.other.guests', 1)) {
                $memberModuleName = '';
                if ($parent_type == 'sitepage_page') {
                    $moduleName = 'sitepage';
                    $memberModuleName = 'sitepagemember';
                    $core_setting = 'pagemember.pageasgroup';
                    $label = "Invite all Page Members.";
                    $id = 'page_id';
                } elseif ($parent_type == 'sitebusiness_business') {
                    $moduleName = 'sitebusiness';
                    $memberModuleName = 'sitebusinessmember';
                    $core_setting = 'businessmember.businessasgroup';
                    $label = "Invite all Business Members.";
                    $id = 'business_id';
                } elseif ($parent_type == 'sitegroup_group') {
                    $moduleName = 'sitegroup';
                    $memberModuleName = 'sitegroupmember';
                    $core_setting = 'groupmember.groupasgroup';
                    $label = "Invite all Group Members.";
                    $id = 'group_id';
                }
                $pagemember = '';
                $pageasgroup = '';
                if ($memberModuleName) {
                    $pagemember = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($memberModuleName);
                    if (!empty($pagemember)) {
                        $select = Engine_Api::_()->getDbTable('membership', $moduleName)->hasMembers($viewer_id, $parentItem->$id);
                        $pageasgroup = Engine_Api::_()->getApi('settings', 'core')->getSetting($core_setting);
                    }
                }
                if (empty($pageasgroup) && empty($pagemember)) {
                    // Invite
                    $createForm[] = array(
                        'type' => 'Checkbox',
                        'name' => 'auth_invite',
                        'value' => True,
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Invited guests can invite other people as well.')
                    );
                } elseif (!empty($select)) {
                    $createForm[] = array(
                        'type' => 'Checkbox',
                        'name' => 'all_members',
                        'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($label),
                        'value' => True,
                    );
                }
            }

            if (!empty($createFormFields) && in_array('search', $createFormFields) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.show.browse', 1)) {
                $createForm[] = array(
                    'type' => 'Checkbox',
                    'name' => 'search',
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate("Show this event on browse page and in various blocks."),
                    'value' => 1,
                );
            }
            if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.guestconfimation', 0) && !empty($createFormFields) && in_array('guestLists', $createFormFields)) {
                $createForm[] = array(
                    'type' => 'Radio',
//                    'name' => 'guest_lists',
//                    'label' => 'Approve members?',
//                    'description' => 'When people try to join this event, should they be allowed ' .
//                    'to join immediately, or should they be forced to wait for approval?',
                    'multiOptions' => array(
                        '1' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Unconfirmed members can see the names of the ones that have been confirmed.')),
                    '0' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Unconfirmed members cannot see the names of the ones that have been confirmed.'),
                );
            }
            if (isset($subject) && !empty($subject))
                $label = 'Save';
            else
                $label = 'Create';

            $createForm[] = array(
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($label),
                'type' => 'Submit',
                'name' => 'submit'
            );

            $responseForm['form'] = $createForm;

            if (!empty($createFormFields) && in_array('host', $createFormFields) && Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.host', 1)) {
                $hostOptionsAlow = Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.hostOptions', array('sitepage_page', 'sitebusiness_business', 'user', 'sitegroup_group', 'sitestore_store', 'siteevent_organizer'));

                if (isset($subject) && !empty($subject)) {

                    if ($subject->host_type == 'siteevent_organizer') {
                        $organizer['host_type'] = 'siteevent_organizer';
                        $organizer['host_id'] = $host->organizer_id;
                        $organizer['host_title'] = $host->title;
                        $organizer['image_icon'] = $host_icons;
                    } else if ($subject->host_type == 'user') {
                        $organizer['host_type'] = 'user';
                        $organizer['host_id'] = $host->user_id;
                        $organizer['host_title'] = $host->displayname;
                        $organizer['image_icon'] = $host_icons;
                    }
                } else {

                    $organizer['host_type'] = 'user';
                    $organizer['host_id'] = $viewer->user_id;
                    $organizer['host_title'] = $viewer->displayname;
                    $organizer['image_icon'] = $host_icons;
                }
                $responseForm['host'] = $organizer;
            }

            if (isset($form) && !empty($form))
                $responseForm['subcategories'] = $form;

            $profileFields = $this->getProfileTypes();
            if (!empty($profileFields)) {
                $this->_profileFieldsArray = $profileFields;
            }
            $this->_create = 1;
            $responseForm['fields'] = $this->_getProfileFields();
//            $createForm = array_merge($createForm, $getProfileFeilds);

            if (isset($repeatForm) && !empty($repeatForm))
                $responseForm['repeatOccurences'] = $repeatForm;

            return $responseForm;
        } catch (Exception $ex) {
            // Blank Exceptaion
        }
    }

//CHECK VIDEO PLUGIN ENABLE / DISABLE
    public function enableVideoPlugin() {

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.show.video', 1)) {
            return Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('video');
        } else {
            return 1;
        }
    }

    public function getAnnouncementCreateForm() {
        // Add title

        $createForm[] = array(
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Title'),
            'type' => 'Text',
            'name' => 'title',
            'hasValidator' => 'true'
        );

        $createForm[] = array(
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Body'),
            'type' => 'Textarea',
            'name' => 'body',
            'hasValidator' => 'true'
        );

        $createForm[] = array(
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Start Date'),
            'type' => 'date',
            'name' => 'startdate',
            'hasValidator' => 'true'
        );

        $createForm[] = array(
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Expiry Date'),
            'type' => 'date',
            'name' => 'expirydate',
            'hasValidator' => 'true'
        );


        $createForm[] = array(
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Activate Announcement'),
            'type' => 'Checkbox',
            'name' => 'status',
            'hasValidator' => 'true',
            'value' => 1
        );

        $createForm[] = array(
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Post Announcement'),
            'type' => 'Submit',
            'name' => 'submit'
        );

        return $createForm;
    }

    // Search Profile Fields
    public function getSearchProfileFields() {
        $this->_validateSearchProfileFields = true;
        $this->_profileFieldsArray = $this->getProfileTypes();

        $_getProfileFields = $this->_getProfileFields();
        return $_getProfileFields;
    }

    private function _getProfileFields($fieldsForm = array()) {
        foreach ($this->_profileFieldsArray as $option_id => $prfileFieldTitle) {

            if (!empty($option_id)) {
                $mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps('siteevent_event');
                $getRowsMatching = $mapData->getRowsMatching('option_id', $option_id);

                $fieldArray = array();
                $getFieldInfo = Engine_Api::_()->fields()->getFieldInfo();
                $getHeadingName = '';
                foreach ($getRowsMatching as $map) {
                    $meta = $map->getChild();
                    $type = $meta->type;

                    if (!empty($type) && ($type == 'heading')) {
                        $getHeadingName = $meta->label;
                        continue;
                    }

                    if (!empty($this->_validateSearchProfileFields) && (!isset($meta->search) || empty($meta->search)))
                        continue;


                    $fieldForm = $getMultiOptions = array();
                    $key = $map->getKey();


                    // Findout respective form element field array.
                    if (isset($getFieldInfo['fields'][$type]) && !empty($getFieldInfo['fields'][$type])) {
                        $getFormFieldTypeArray = $getFieldInfo['fields'][$type];

                        // In case of Generic profile fields.
                        if (isset($getFormFieldTypeArray['category']) && ($getFormFieldTypeArray['category'] == 'generic')) {
                            // If multiOption enabled then perpare the multiOption array.

                            if (($type == 'select') || ($type == 'radio') || (isset($getFormFieldTypeArray['multi']) && !empty($getFormFieldTypeArray['multi']))) {
                                $getOptions = $meta->getOptions();
                                if (!empty($getOptions)) {
                                    foreach ($getOptions as $option) {
                                        $getMultiOptions[$option->option_id] = $option->label;
                                    }
                                }
                            }

                            // Prepare Generic form.
                            $fieldForm['type'] = ucfirst($type);
                            $fieldForm['name'] = $key . '_field_' . $meta->field_id;
                            $fieldForm['label'] = (isset($meta->label) && !empty($meta->label)) ? Engine_Api::_()->getApi('Core', 'siteapi')->translate($meta->label) : '';
                            $fieldForm['description'] = (isset($meta->description) && !empty($meta->description)) ? Engine_Api::_()->getApi('Core', 'siteapi')->translate($meta->description) : '';

                            // Add multiOption, If available.
                            if (!empty($getMultiOptions)) {
                                $fieldForm['multiOptions'] = $getMultiOptions;
                            }
                            // Add validator, If available.
                            if (isset($meta->required) && !empty($meta->required))
                                $fieldForm['hasValidator'] = true;

                            if (COUNT($this->_profileFieldsArray) > 1) {

                                if (isset($this->_create) && !empty($this->_create) && $this->_create == 1) {
                                    $optionCategoryName = Engine_Api::_()->getDbtable('options', 'siteevent')->getProfileTypeLabel($option_id);
                                    $fieldsForm[$optionCategoryName][] = $fieldForm;
                                } else {
                                    $fieldsForm[$option_id][] = $fieldForm;
                                }
                            } else
                                $fieldsForm[] = $fieldForm;
                        }else if (isset($getFormFieldTypeArray['category']) && ($getFormFieldTypeArray['category'] == 'specific') && !empty($getFormFieldTypeArray['base'])) { // In case of Specific profile fields.
                            // Prepare Specific form.
                            $fieldForm['type'] = ucfirst($getFormFieldTypeArray['base']);
                            $fieldForm['name'] = $key . '_field_' . $meta->field_id;
                            $fieldForm['label'] = (isset($meta->label) && !empty($meta->label)) ? Engine_Api::_()->getApi('Core', 'siteapi')->translate($meta->label) : '';
                            $fieldForm['description'] = (isset($meta->description) && !empty($meta->description)) ? $meta->description : '';

                            // Add multiOption, If available.
                            if ($getFormFieldTypeArray['base'] == 'select') {
                                $getOptions = $meta->getOptions();
                                foreach ($getOptions as $option) {
                                    $getMultiOptions[$option->option_id] = Engine_Api::_()->getApi('Core', 'siteapi')->translate($option->label);
                                }
                                $fieldForm['multiOptions'] = $getMultiOptions;
                            }

                            // Add validator, If available.
                            if (isset($meta->required) && !empty($meta->required))
                                $fieldForm['hasValidator'] = true;

                            if (COUNT($this->_profileFieldsArray) > 1) {
                                if (isset($this->_create) && !empty($this->_create) && $this->_create == 1) {
                                    $optionCategoryName = Engine_Api::_()->getDbtable('options', 'siteevent')->getProfileTypeLabel($option_id);
                                    $fieldsForm[$optionCategoryName][] = $fieldForm;
                                } else {
                                    $fieldsForm[$option_id][] = $fieldForm;
                                }
                            } else
                                $fieldsForm[] = $fieldForm;
//                                $fieldsForm[] = $fieldForm;
                        }
                    }
                }
            }
        }
        return $fieldsForm;
    }

    public function getProfileTypes($profileFields = array()) {

        $topStructure = Engine_Api::_()->fields()->getFieldStructureTop('siteevent_event');

        if (count($topStructure) == 1 && $topStructure[0]->getChild()->type == 'profile_type') {
            $profileTypeField = $topStructure[0]->getChild();
            $options = $profileTypeField->getOptions();

            $options = $profileTypeField->getElementParams('siteevent_event');
            if (isset($options['options']['multiOptions']) && !empty($options['options']['multiOptions']) && is_array($options['options']['multiOptions'])) {
                // Make exist profile fields array.         
                foreach ($options['options']['multiOptions'] as $key => $value) {
                    if (!empty($key)) {
                        $profileFields[$key] = $value;
                    }
                }
            }
        }
        return $profileFields;
    }

//get all occurrence date.
    public function convertDateFormat($date) {

        $date_orig = $date;

        $date = explode("/", $date);
        if (count($date) == 3) {
            $date = $date[1] . '/' . $date[0] . '/' . $date[2];
        } else
            $date = str_replace("/", "-", $date_orig);

        //CHECK IF THE COVERTTED DATE RETURNS TRUE OR FALSE.
        if (!strtotime($date))
            return $date_orig;
        return $date;
    }

    /**
     * Set the profile fields value to newly created user.
     * 
     * @return array
     */
    public function setProfileFields($siteevent, $data) {
        // Iterate over values
        $values = Engine_Api::_()->fields()->getFieldsValues($siteevent);

        $fVals = $data;
        $privacyOptions = Fields_Api_Core::getFieldPrivacyOptions();

        foreach ($fVals as $key => $value) {

            if (strstr($key, 'oauth'))
                continue;

            $parts = explode('_', $key);
            if (count($parts) < 3)
                continue;
            list($parent_id, $option_id, $field_id) = $parts;

            // Array mode
            if (is_array($value)) {

                // Lookup
                $valueRows = $values->getRowsMatching(array(
                    'field_id' => $field_id,
                    'item_id' => $user->getIdentity()
                ));

                // Delete all
                $prevPrivacy = null;
                foreach ($valueRows as $valueRow) {
                    if (!empty($valueRow->privacy)) {
                        $prevPrivacy = $valueRow->privacy;
                    }
                    $valueRow->delete();
                }

                // Insert all
                $indexIndex = 0;
                if (is_array($value) || !empty($value)) {
                    foreach ((array) $value as $singleValue) {

                        $valueRow = $values->createRow();
                        $valueRow->field_id = $field_id;
                        $valueRow->item_id = $siteevent->getIdentity();
                        $valueRow->index = $indexIndex++;
                        $valueRow->value = $singleValue;
                        $valueRow->save();
                    }
                } else {
                    $valueRow = $values->createRow();
                    $valueRow->field_id = $field_id;
                    $valueRow->item_id = $siteevent->getIdentity();
                    $valueRow->index = 0;
                    $valueRow->value = '';
                    $valueRow->save();
                }
            }

            // Scalar mode
            else {
                // Lookup
                $valueRow = $values->getRowMatching(array(
                    'field_id' => $field_id,
                    'item_id' => $siteevent->getIdentity(),
                    'index' => 0
                ));

                // Create if missing
                $isNew = false;
                if (!$valueRow) {
                    $isNew = true;
                    $valueRow = $values->createRow();
                    $valueRow->field_id = $field_id;
                    $valueRow->item_id = $siteevent->getIdentity();
                }
                $valueRow->value = htmlspecialchars($value);
                $valueRow->save();
            }
        }
        return;
    }

    // Get the Profile Fields Information, which will show on profile page.
    public function getProfileInfo($subject, $setKeyAsResponse = false) {
        // Getting the default Profile Type id.
        $getFieldId = $this->getDefaultProfileTypeId($subject);
        // Start work to get form values.
        $values = Engine_Api::_()->fields()->getFieldsValues($subject);
        $fieldValues = array();
        // In case if Profile Type available. like User module.
        if (!empty($getFieldId)) {
            // Set the default profile type.
            $this->_profileFieldsArray[$getFieldId] = $getFieldId;
            $_getProfileFields = $this->_getProfileFields();
            foreach ($_getProfileFields as $heading => $tempValue) {
                foreach ($tempValue as $value) {
                    $key = $value['name'];
                    $label = $value['label'];
                    $type = $value['type'];
                    $parts = @explode('_', $key);

                    if (count($parts) < 3)
                        continue;

                    list($parent_id, $option_id, $field_id) = $parts;

                    $valueRows = $values->getRowsMatching(array(
                        'field_id' => $field_id,
                        'item_id' => $subject->getIdentity()
                    ));

                    if (!empty($valueRows)) {
                        foreach ($valueRows as $fieldRow) {

                            $tempValue = $fieldRow->value;

                            // In case of Select or Multi send the respective label.
                            if (isset($value['multiOptions']) && !empty($value['multiOptions']) && isset($value['multiOptions'][$fieldRow->value]))
                                $tempValue = $value['multiOptions'][$fieldRow->value];
                            $tempKey = !empty($setKeyAsResponse) ? $key : $label;
                            $fieldValues[$tempKey] = $tempValue;
                        }
                    }
                }
            }
        } else { // In case, If there are no Profile Type available and only Profile Fields are available. like Classified.
            $getType = $subject->getType();
            $_getProfileFields = $this->_getProfileFields($getType);

            foreach ($_getProfileFields as $value) {
                $key = $value['name'];
                $label = $value['label'];
                $parts = @explode('_', $key);

                if (count($parts) < 3)
                    continue;

                list($parent_id, $option_id, $field_id) = $parts;

                $valueRows = $values->getRowsMatching(array(
                    'field_id' => $field_id,
                    'item_id' => $subject->getIdentity()
                ));

                if (!empty($valueRows)) {
                    foreach ($valueRows as $fieldRow) {
                        if (!empty($fieldRow->value)) {
                            $tempKey = !empty($setKeyAsResponse) ? $key : $label;
                            $fieldValues[$tempKey] = $fieldRow->value;
                        }
                    }
                }
            }
        }

        return $fieldValues;
    }

    public function getDefaultProfileTypeId($subject) {
        $getFieldId = null;
        $fieldsByAlias = Engine_Api::_()->fields()->getFieldsObjectsByAlias($subject);
        if (!empty($fieldsByAlias['profile_type'])) {
            $optionId = $fieldsByAlias['profile_type']->getValue($subject);
            $getFieldId = $optionId->value;
        }
        if (empty($getFieldId)) {
            return;
        }

        return $getFieldId;
    }

    public function getMessageComposeForm() {

        // Element : restriction
        if (Engine_Api::_()->siteevent()->isTicketBasedEvent()) {

            $composeForm[] = array(
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Select Member'),
                'type' => 'Select',
                'name' => 'guests',
                'hasValidator' => 'true',
            );
        } else {
            $composeForm[] = array(
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Select Member'),
                'type' => 'Select',
                'name' => 'guests',
                'multiOptions' => array(
                    '3' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('All Guests'),
                    '2' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Attending'),
                    '1' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Maybe Attending'),
                    '0' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Not Attending'),
                    '4' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Particular Guests'),
                ),
                'hasValidator' => 'true',
            );
        }

        $composeForm[] = array(
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Subject'),
            'type' => 'Text',
            'name' => 'title',
            'hasValidator' => 'true',
        );
        $composeForm[] = array(
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Members to Message'),
            'type' => 'Text',
            'name' => 'searchGuests',
            'hasValidator' => 'true',
        );

        $composeForm[] = array(
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Message'),
            'type' => 'Textarea',
            'name' => 'body',
            'hasValidator' => 'true',
        );
        $composeForm[] = array(
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Submit'),
            'type' => 'Submit',
            'name' => 'submit',
        );
        return $composeForm;
    }

    //MAKE THE REPEAT EVENT INFO OBJECT.
    public function getRepeatEventCompleteInfo($postedValues, $eventparams, $editFullEventDate = true, $action = 'save') {
        if (!isset($postedValues['eventrepeat_id']) || $postedValues['eventrepeat_id'] == 'never')
            return;
        if (isset($postedValues['eventrepeat_id']) && $postedValues['eventrepeat_id'] == 'daily') {
            if (!isset($postedValues['daily-repeat_interval'])) {
                $postedValues['daily-repeat_interval'] = ($eventparams['repeat_interval'] / (24 * 60 * 60));
            }
            $repeatEventInfo['repeat_interval'] = $postedValues['daily-repeat_interval'] * 24 * 60 * 60;
            $repeatEventInfo['eventrepeat_type'] = $postedValues['eventrepeat_id'];
            $repeatEventInfo['endtime'] = $postedValues[$postedValues['eventrepeat_id'] . '_repeat_time'];
        } elseif (isset($postedValues['eventrepeat_id']) && $postedValues['eventrepeat_id'] == 'weekly') {
            $weekdays = array(1 => 'monday', 2 => 'tuesday', 3 => 'wednesday', 4 => 'thursday', 5 => 'friday', 6 => 'saturday', 7 => 'sunday');
            if (!$editFullEventDate) {
                $postedValues['id_weekly-repeat_interval'] = $eventparams['repeat_week'];
                foreach ($eventparams['repeat_weekday'] as $weekday) {
                    $postedValues['weekly-repeat_on_' . $weekdays[$weekday]] = 1;
                }
            }
            $repeatEventInfo['repeat_interval'] = 0;
            $repeatEventInfo['repeat_week'] = $postedValues['id_weekly-repeat_interval'];
            foreach ($weekdays as $key => $weekday) {
                if (isset($postedValues['weekly-repeat_on_' . $weekday])) {
                    $weekdaysSelected[] = $key;
                }
            }
            $repeatEventInfo['repeat_weekday'] = $weekdaysSelected;
            $repeatEventInfo['eventrepeat_type'] = $postedValues['eventrepeat_id'];
            $repeatEventInfo['endtime'] = $postedValues[$postedValues['eventrepeat_id'] . '_repeat_time'];
        } elseif (isset($postedValues['eventrepeat_id']) && $postedValues['eventrepeat_id'] == 'monthly') {
            //CHECK FOR EITHER ABSOLUTE MONTH DAY OR RELATIVE DAY
            $noOfWeeks = array('first' => 1, 'second' => 2, 'third' => 3, 'fourth' => 4, 'fifth' => 5, 'last' => 6);

            $dayOfWeeks = array('monday' => 1, 'tuesday' => 2, 'wednesday' => 3, 'thursday' => 4, 'friday' => 5, 'saturday' => 6, 'sunday' => 7);

            if (!$editFullEventDate) {
                $postedValues['monthly_day'] = 'absolute_day';
                $postedValues['id_monthly-repeat_interval'] = $eventparams['repeat_month'];
                if (isset($eventparams['repeat_week'])) {
                    $postedValues['id_monthly-relative_day'] = array_search($eventparams['repeat_week'], $noOfWeeks);
                    $postedValues['monthly_day'] = 'relative_weekday';
                }
                if (isset($eventparams['repeat_weekday']))
                    $postedValues['id_monthly-day_of_week'] = array_search($eventparams['repeat_weekday'], $dayOfWeeks);
                if (isset($eventparams['repeat_day']))
                    $postedValues['id_monthly-absolute_day'] = $eventparams['repeat_day'];
            }
            $repeatEventInfo['repeat_interval'] = 0;
            $repeatEventInfo['eventrepeat_type'] = 'monthly';
            $repeatEventInfo['repeat_month'] = $postedValues['id_monthly-repeat_interval'];
            $repeatEventInfo['endtime'] = $postedValues['monthly_repeat_time'];

            if ($postedValues['monthly_day'] == 'relative_weekday') {
                $repeatEventInfo['repeat_week'] = $noOfWeeks[$postedValues['id_monthly-relative_day']];
                $repeatEventInfo['repeat_weekday'] = $dayOfWeeks[$postedValues['id_monthly-day_of_week']];
            } else {
                $repeatEventInfo['repeat_day'] = $postedValues['id_monthly-absolute_day'];
            }
        } elseif (isset($postedValues['eventrepeat_id']) && $postedValues['eventrepeat_id'] === 'custom') {
            $repeatEventInfo['eventrepeat_type'] = 'custom';
            if ($action == 'display') {
                $customEventType = array();
                if ($event_id) {

                    $repeatEventInfo_temp = $eventparams;
                    if ($repeatEventInfo_temp['eventrepeat_type'] == 'custom' && !$editFullEventDate)
                        $customEventType = Engine_Api::_()->getDbtable('occurrences', 'siteevent')->getCustomEventInfo($event_id)->toarray();
                }

                if (!empty($postedValues)) {

                    $j = 0;
                    for ($i = 0; $i <= $postedValues['countcustom_dates']; $i++) {
                        if (isset($postedValues['customdate_' . $i])) {

                            $startenddate = explode("-", $postedValues['customdate_' . $i]);
                            if ($editFullEventDate) {
                                $customEventType[$j]['starttime'] = $startenddate[0];
                                $customEventType[$j]['endtime'] = $startenddate[1];
                                $j++;
                            } else {
                                $customEventType[$i]['starttime'] = $startenddate[0];
                                $customEventType[$i]['endtime'] = $startenddate[1];
                            }
                        }
                    }
                }
                $repeatEventInfo = array_merge($repeatEventInfo, $customEventType);
            }
        } else {
            $repeatEventInfo = '';
        }


        return $postedValues;
    }

}
