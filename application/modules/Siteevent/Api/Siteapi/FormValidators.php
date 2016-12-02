<?php

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
class Siteevent_Api_Siteapi_FormValidators extends Siteapi_Api_Validators {

    /**
     * Validations of Create OR Edit Form.
     * 
     * @param object $subject get object
     * @param array $formValidators array variable
     * @return array
     */
    public function getFormValidators($subject = array(), $formValidators = array()) {
        $formValidators['title'] = array(
            'required' => true,
            'allowEmpty' => false,
            'validators' => array(
                array('NotEmpty', true),
                array('StringLength', false, array(3, 63))
            )
        );

        $formValidators['body'] = array(
            'required' => Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.bodyrequired', 1) ? true : false,
            'allowEmpty' => Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.bodyrequired', 1) ? false : true,
        );

        $formValidators['category_id'] = array(
            'required' => true,
            'allowEmpty' => false,
            'validators' => array(
                array('Int', true)
            )
        );

        $formValidators['starttime'] = array(
            'required' => true,
            'allowEmpty' => false,
        );

        $formValidators['endtime'] = array(
            'required' => true,
            'allowEmpty' => false,
        );

        $formValidators['price'] = array(
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )
        );
        return $formValidators;
    }

    /**
     * Validation: user signup field form
     * 
     * @return array
     */
    public function getFieldsFormValidations($values) {
        $option_id = $values['profile_type'];

        $mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps('siteevent_event');
        $getRowsMatching = $mapData->getRowsMatching('option_id', $option_id);
        $fieldArray = array();
        $getFieldInfo = Engine_Api::_()->fields()->getFieldInfo();
        foreach ($getRowsMatching as $map) {
            $meta = $map->getChild();
            $type = $meta->type;

            if (!empty($type) && ($type == 'heading'))
                continue;

            if (!isset($meta->show) || empty($meta->show))
                continue;

            $fieldForm = $getMultiOptions = array();
            $key = $map->getKey();

            if (!empty($meta->alias))
                $key = $key . '_' . 'alias_' . $meta->alias;
            else {
                $key = $key . '_' . 'field_' . $meta->alias->field_id;
            }

            if (isset($meta->required) && !empty($meta->required))
                $fieldArray[$key] = array(
                    'required' => true,
                    'allowEmpty' => false
                );

            if (isset($mets->validators) && !empty($mets->validators)) {
                $fieldArray[$key]['validators'] = $mets->validators;
            }
        }

        return $fieldArray;
    }

    public function getPhotoEditValidators($subject = array(), $formValidators = array()) {
        $formValidators['title'] = array(
            'validators' => array(
                array('NotEmpty', true),
                array('StringLength', false, array(3, 63))
            )
        );

        return $formValidators;
    }

    public function getVideoCreateFormValidators($subject = array(), $formValidators = array()) {
        $formValidators['title'] = array(
            'required' => true,
            'allowEmpty' => false,
            'validators' => array(array('NotEmpty', true), array('StringLength', false, array(3, 63)))
        );
        if (empty($subject)) {
            $formValidators['type'] = array(
                'required' => true,
                'allowEmpty' => false
            );
        }


        return $formValidators;
    }

    public function getAnnouncementFormValidators() {

        $formValidators['title'] = array(
            'required' => true,
            'allowEmpty' => false,
        );
        $formValidators['body'] = array(
            'required' => true,
            'allowEmpty' => false,
        );
        $formValidators['startdate'] = array(
            'required' => true,
            'allowEmpty' => false,
        );
        $formValidators['expirydate'] = array(
            'required' => true,
            'allowEmpty' => false,
        );
        return $formValidators;
    }

    public function getReviewCreateFormValidators($widgetSettingsReviews) {

        $getItemEvent = $widgetSettingsReviews['item'];
        $siteevent_proscons = $widgetSettingsReviews['settingsReview']['siteevent_proscons'];
        $siteevent_limit_proscons = $widgetSettingsReviews['settingsReview']['siteevent_limit_proscons'];
        $siteevent_recommend = $widgetSettingsReviews['settingsReview']['siteevent_recommend'];
        if ($siteevent_proscons) {
            if ($siteevent_limit_proscons) {
                $formValidators['pros'] = array(
                    'allowEmpty' => false,
                    'maxlength' => $widgetSettingsReviews['siteevent_limit_proscons'],
                    'required' => true,
                    'filters' => array(
                        'StripTags',
                        new Engine_Filter_Censor(),
                        new Engine_Filter_HtmlSpecialChars(),
                        new Engine_Filter_EnableLinks(),
                    ),
                );
            } else {
                $formValidators['pros'] = array(
                    'allowEmpty' => false,
                    'required' => true,
                    'filters' => array(
                        'StripTags',
                        new Engine_Filter_Censor(),
                        new Engine_Filter_HtmlSpecialChars(),
                        new Engine_Filter_EnableLinks(),
                    ),
                );
            }
            if ($siteevent_limit_proscons) {
                $formValidators['cons'] = array(
                    'allowEmpty' => false,
                    'maxlength' => $widgetSettingsReviews['siteevent_limit_proscons'],
                    'required' => true,
                    'filters' => array(
                        'StripTags',
                        new Engine_Filter_Censor(),
                        new Engine_Filter_HtmlSpecialChars(),
                        new Engine_Filter_EnableLinks(),
                    ),
                );
            } else {
                $formValidators['cons'] = array(
                    'allowEmpty' => false,
                    'required' => true,
                    'filters' => array(
                        'StripTags',
                        new Engine_Filter_Censor(),
                        new Engine_Filter_HtmlSpecialChars(),
                        new Engine_Filter_EnableLinks(),
                    ),
                );
            }
        }
        $formValidators['title'] = array(
            'required' => true,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_HtmlSpecialChars(),
                new Engine_Filter_EnableLinks(),
            ),
        );
        return $formValidators;
    }

    public function getReviewUpdateFormValidators() {
        $formValidators['body'] = array(
            'allowEmpty' => true,
            'required' => false,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_HtmlSpecialChars(),
                new Engine_Filter_EnableLinks(),
            ),
        );
        return $formValidators;
    }

    public function getEditorCreateValidators($item) {


        $formValidators['pros'] = array(
            'allowEmpty' => false,
            'maxlength' => 500,
            'required' => true,
            'filters' => array(
            ),
        );

        $formValidators['cons'] = array(
            'allowEmpty' => false,
            'maxlength' => 500,
            'required' => true,
            'filters' => array(
            ),
        );

        $formValidators['title'] = array(
            'allowEmpty' => false,
            'maxlength' => 500,
            'required' => true,
            'filters' => array(
            ),
        );

        $formValidators['body'] = array(
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
            ),
        );
        if ($item && $item['status'] == 1) {
            $formValidators['pros'] = array(
                'maxlength' => 500,
                'filters' => array(
                ),
            );

            $formValidators['cons'] = array(
                'maxlength' => 500,
                'filters' => array(
                ),
            );
            $formValidators['body'] = array(
                'filters' => array(
                ),
            );

            $formValidators['title'] = array(
                'maxlength' => 500,
            );
            if (empty($title['update_reason'])) {
                $formValidators['update_reason'] = array(
                    'allowEmpty' => false,
                    'required' => true,
                    'filters' => array(
                    ),
                );
            }
        }

        return $formValidators;
    }

    public function getEditLocationValidators() {

        $formValidators['formatted_address'] = array(
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )
        );
        $formValidators['latitude'] = array(
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )
        );
        $formValidators['longitude'] = array(
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            )
        );
        $formValidators['address'] = array(
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor()
        ));
        $formValidators['city'] = array(
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength(array('max' => '63')),
        ));

        $formValidators['zipcode'] = array(
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength(array('max' => '63')),
        ));

        $formValidators['state'] = array(
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength(array('max' => '63')),
        ));


        $formValidators['country'] = array(
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength(array('max' => '63')),
        ));
        return $formValidators;
    }

    public function getEditorMailValidators() {

        $formValidators['sender_name'] = array(
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength(array('max' => '63')),
        ));

        $formValidators['sender_email'] = array(
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength(array('max' => '63')),
        ));

        $formValidators['message'] = array(
            'required' => true,
            'allowEmpty' => false,
            'description' => 'You can send a personal note in the mail.',
            'filters' => array(
                'StripTags',
                new Engine_Filter_HtmlSpecialChars(),
                new Engine_Filter_EnableLinks(),
                new Engine_Filter_Censor(),
        ));
        return $formValidators;
    }

    public function getTopicCreateFormValidators() {
        $formValidators['title'] = array(
            'allowEmpty' => false,
            'required' => true,
            'filters' => array(
                new Engine_Filter_Censor(),
                new Engine_Filter_HtmlSpecialChars(),
            ),
            'validators' => array(
                array('StringLength', true, array(1, 64)),
            )
        );

        if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.tinymceditor', 1)) {
            $formValidators['body'] = array(
                'allowEmpty' => false,
                'required' => true,
                'filters' => array(
                    new Engine_Filter_Censor(),
                    new Engine_Filter_HtmlSpecialChars(),
                    new Engine_Filter_EnableLinks(),
                ),
            );
        } else {
            $formValidators['body'] = array(
                'allowEmpty' => false,
                'required' => true,
                'filters' => array(new Engine_Filter_Censor()),
            );
        }
        return $formValidators;
    }

//    public function getVideoCreateFormValidators($subject = array(), $formValidators = array()) {
//        $formValidators['title'] = array(
//            'required' => true,
//            'allowEmpty' => false,
//            'validators' => array(array('NotEmpty', true), array('StringLength', false, array(3, 63)))
//        );
//        if (empty($subject)) {
//            $formValidators['type'] = array(
//                'required' => true,
//                'allowEmpty' => false
//            );
//        }
//
//
//        return $formValidators;
//    }
}
