<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroupmember
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Core.php 2013-03-18 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroupmember_Api_Siteapi_Core extends Core_Api_Abstract {

    public function getMemberSearchForm($sitegroup) {
        $rolesTable = Engine_Api::_()->getDbtable('roles', 'sitegroupmember');
        $rolesParams = array();
        $roleParamsArray[0] = '';
        $roleParamsArray = $rolesTable->rolesParams(array($sitegroup->category_id), 0, $rolesParams, 1, $sitegroup->group_id);

        $searchForm[] = array(
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Member Keywords'),
            'type' => 'Text',
            'name' => 'search_member',
        );

        $searchForm[] = array(
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Browse By'),
            'type' => 'Select',
            'name' => 'orderby',
            'multiOptions' => array(
                '' => '',
                'join_date' => 'Most Recent',
                'member_count' => "Top Group Joiners",
                'featured_member' => 'Featured Members',
            ),
        );

        $searchForm[] = array(
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Roles'),
            'type' => 'Select',
            'name' => 'role_id',
            'multiOptions' => $roleParamsArray
        );

        return $searchForm;
    }

    public function getMessageComposeForm($sitegroup) {
        $composeForm[] = array(
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Message Who?'),
            'type' => 'Select',
            'name' => 'coupon_mail',
            'multiOptions' => array(
                '1' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Particular Members (You can enter the members using the auto-suggest below.)'),
                '0' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('All Group Members'),
            ),
            'value' => 0
        );

        $composeForm[] = array(
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Members to Message'),
            'type' => 'Text',
            'name' => 'toValues',
            'hasValidator' => 'true',
        );

        $composeForm[] = array(
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Subject'),
            'type' => 'Text',
            'name' => 'title',
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

    public function getMemberJoinForm($sitegroup) {
        $group_id = $sitegroup->group_id;

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('groupmember.title', 1)) {

            $roles = Engine_Api::_()->getDbtable('roles', 'sitegroupmember')->getRolesAssoc($group_id);
            if (!empty($roles)) {
                $roleKey = array();
                foreach ($roles as $k => $role) {
                    $role_name[$k] = $role;
                    $roleKey[] = $k;
                }
                reset($role_name);
                $joinForm[] = array(
                    'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('ROLE'),
                    'type' => 'Multiselect',
                    'name' => 'role_id',
                    'multiOptions' => $role_name,
                    'value' => $roleKey,
                );
            }
        }

        if (Engine_Api::_()->getApi('settings', 'core')->getSetting('groupmember.date', 1)) {
            $curYear = date('Y');
            $year = array('Year');

            for ($i = 0; $i <= 110; $i++) {
                $year[$curYear] = $curYear;
                $curYear--;
            }

            $joinForm[] = array(
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Year'),
                'type' => 'Select',
                'name' => 'year',
                'multiOptions' => $year,
                'value' => date('Y'),
                'hasValidator' => 'true',
            );


            $months = array('Month');
            for ($x = 1; $x <= 12; $x++) {
                $months[$x] = date('F', mktime(0, 0, 0, $x));
            }

            $joinForm[] = array(
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Month'),
                'type' => 'Select',
                'name' => 'month',
                'multiOptions' => $months,
                'hasValidator' => 'true',
            );

            $day = array('Day');
            for ($x = 1; $x <= 31; $x++) {
                $day[] = $x;
            }

            $joinForm[] = array(
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Day'),
                'type' => 'Select',
                'name' => 'day',
                'multiOptions' => $day,
                'hasValidator' => 'true',
            );
        }

        $joinForm[] = array(
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Submit'),
            'type' => 'Submit',
            'name' => 'submit',
        );

        return $joinForm;
    }

    public function getMemberInviteForm() {
        $coreSettings = Engine_Api::_()->getApi('settings', 'core');
        $memberSettings = $coreSettings->getSetting('groupmember.automatically.addmember', 1);
        if (!empty($memberSettings)) {
            $inviteForm[] = array(
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Add People to this Group'),
                'type' => 'Dummy',
                'name' => 'title'
            );
            $inviteForm[] = array(
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Select the people you want to add to this group.'),
                'type' => 'Dummy',
                'name' => 'title'
            );
            $Button = 'Add People';
        } else {
            $inviteForm[] = array(
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Invite People to this Group'),
                'type' => 'Dummy',
                'name' => 'title'
            );
            $inviteForm[] = array(
                'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Select the members you want to invite to this group.'),
                'type' => 'Dummy',
                'name' => 'title'
            );
            $Button = 'Invite People';
        }

        $inviteForm[] = array(
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate('Start typing the name of the member...'),
            'type' => 'Multiselect',
            'name' => 'user_ids',
        );

        $inviteForm[] = array(
            'label' => Engine_Api::_()->getApi('Core', 'siteapi')->translate($Button),
            'type' => 'Submit',
            'name' => 'submit',
        );
        return $inviteForm;
    }

}
