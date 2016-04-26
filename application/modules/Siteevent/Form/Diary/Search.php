<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Search.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Form_Diary_Search extends Engine_Form {

    public function init() {

        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $this->setAttribs(array(
                    'id' => 'filter_form',
                    'class' => 'global_form_box diary_search_form',
                ))
                ->setMethod('GET')
                ->setAction($view->url(array(), "siteevent_diary_general", true));

        $this->addElement('Text', 'search', array(
            'label' => "Search",
        ));

        $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
        if ($viewer_id) {
            $this->addElement('Select', 'search_diary', array(
                'label' => 'Diaries',
                'multiOptions' => array(
                    '' => '',
                    'my_diaries' => 'My Event Diaries',
                    'friends_diaries' => 'My Friends Event Diaries',
                ),
                'onchange' => 'showMemberNameSearch();',
            ));
        }

        $this->addElement('Text', 'member', array(
            'label' => "Member's Name / Email",
        ));

        $this->addElement('Select', 'orderby', array(
            'label' => 'Browse By',
            'multiOptions' => array(
                'diary_id' => 'Most Recent',
                'total_item' => 'Maximum Events',
                'view_count' => 'Most Viewed',
            ),
                //     'onchange' => 'this.form.submit();',
        ));

        $this->addElement('hidden', 'viewType', array(
            'value' => 'grid'
        ));

        $this->addElement('Button', 'done', array(
            'label' => 'Search',
            'type' => 'Submit',
                //    'onclick' => 'this.form.submit();',
        ));
    }

}