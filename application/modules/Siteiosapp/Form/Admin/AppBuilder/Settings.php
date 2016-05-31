<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteiosapp
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Settings.php 2015-10-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteiosapp_Form_Admin_AppBuilder_Settings extends Engine_Form {

    protected $_doWeHaveLatestVersion;
    protected $_enabledTabName;

    public function getDoWeHaveLatestVersion() {
        return $this->_doWeHaveLatestVersion;
    }

    public function setDoWeHaveLatestVersion($default_profile_id) {
        $this->_doWeHaveLatestVersion = $default_profile_id;
        return $this;
    }

    public function getEnabledTabName() {
        return $this->_enabledTabName;
    }

    public function setEnabledTabName($enabledTabName) {
        $this->_enabledTabName = $enabledTabName;
        return $this;
    }

    public function init() {
        // doWeHaveLatestVersion
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $package = Zend_Controller_Front::getInstance()->getRequest()->getParam('package', '');
        $tab = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab', 1);

        if (empty($package)) {
            $this->addElement('Select', 'package', array(
                'label' => 'Select Your Mobile Apps Subscription Plan',
                'required' => true,
                'allowEmpty' => false,
                'multiOptions' => array(
                    '' => '',
                    'starter' => 'Mobile Starter',
                    'pro' => 'Mobile Pro'
                ),
                'value' => $package,
                'onchange' => 'selectPackage()',
            ));
        } else {
            if ($tab == 1) {
                $this->addElement('Dummy', 'required_fields', array(
                    'label' => Zend_Registry::get('Zend_Translate')->_('Fields with asterisk (<span style="color:RED">*</span>) are mandatory.')
                ));
                $this->required_fields->getDecorator('Label')->setOptions(array('escape' => false));

                if (($package === 'pro')) {
//                $this->addElement('Hidden', 'publish_app', array());
//                $this->addElement('Hidden', 'phone_udid', array());
                    $this->addElement('Dummy', 'ios_developer_account_details', array(
                        'label' => Zend_Registry::get('Zend_Translate')->_('<center><h3>Apple iOS Developer Account Details</h3>We will submit your App on iTunes App Store with this account.</center><br /><span style="font-weight: bold;">Important NOTE: </span>If you have not yet enrolled into the iOS Developer Program, then please enroll from here: <a href="https://developer.apple.com/programs/ios" target="_blank">https://developer.apple.com/programs/ios/</a>.')
                    ));
                    $this->ios_developer_account_details->getDecorator('Label')->setOptions(array('escape' => false));

                    $this->addElement('Text', 'apple_ios_developer_login_email', array(
                        'label' => 'Apple iOS Developer Account Login Email',
                        'required' => true,
                        'allowEmpty' => false,
                        'validators' => array(array('EmailAddress', 1)),
                        'filters' => array('StringTrim')
                    ));

                    $this->addElement('Text', 'apple_ios_developer_login_password', array(
                        'label' => 'Apple iOS Developer Account Login Password',
                        'required' => true,
                        'allowEmpty' => false
                    ));

                    $this->addElement('Radio', 'publish_app', array(
                        'label' => 'Publish App on iTunes App Store?',
                        'description' => "Do you want your App to be published on the Apple App Store? Select 'Yes' to get your app published on App Store by our Support Team. If you select 'No’, then your app's “.ipa” file will be sent to you and the app will not be published by our team.",
                        'multiOptions' => array(
                            0 => 'No, do not publish the app on iTunes App Store',
                            1 => 'Yes, publish the app on iTunes App Store'
                        ),
                        'value' => 1,
                        'onchange' => 'showUdid()',
                    ));
                    $this->publish_app->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

                    $this->addElement('Text', 'phone_udid', array(
                        'label' => 'Enter iPhone or iPad UDID',
                        'description' => 'Please enter the UDID of the iPhone or iPad device on which you want to test the iOS App. We will accordingly send the “.ipa” file to you. Please <a href="https://www.youtube.com/watch?v=2SDwxBs6M1w" target="_blank">click here</a> to see how to get the UDID.'
                    ));
                    $this->phone_udid->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
                }



                $this->addElement('Dummy', 'app_details', array(
                    'label' => Zend_Registry::get('Zend_Translate')->_('<center><h3>App Submission Details</h3></center>'),
                ));
                $this->app_details->getDecorator('Label')->setOptions(array('escape' => false));

                $this->addElement('Text', 'package_name', array(
                    'label' => 'iTunes App Store App ID',
                    'description' => "Enter the iTunes App Store App ID (i.e: com.socialengineaddons.seiosnativeapp). You need to suggest your iOS App ID to our team that you want to create on iTunes.",
//                'required' => true,
//                'allowEmpty' => false,
                ));
                $this->package_name->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

                $this->addElement('Text', 'title', array(
                    'label' => 'App Title',
                    'description' => 'The localized name of your app as it appears on the store. The app name must be at least two characters and no more than 75 bytes, assuming single-byte characters.',
                    'required' => true,
                    'allowEmpty' => false,
                    'maxlength' => 100,
                    'validators' => array(
                        array('stringLength', false, array(0, 100))
                    )
                ));

                $this->addElement('Text', 'bundle_display_name', array(
                    'label' => 'Bundle Display Name',
                    'description' => 'This is the text that shows up under your app icon on an iPhone or iPad.',
                    'required' => true,
                    'allowEmpty' => false
                ));

                $this->addElement('Textarea', 'description', array(
                    'label' => 'App Description',
                    'description' => 'Descriptions are limited to 4000 single-byte characters. The description should be in plain text, with line breaks as needed. HTML formatting isn’t recognized. Make sure to check your text for spelling or grammar errors.',
                    'required' => true,
                    'allowEmpty' => false,
                    'validators' => array(
                        array('stringLength', false, array(0, 4000))
                    )
                ));
                $this->description->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

                $this->addElement('Text', 'default_language', array(
                    'label' => 'Language',
                    'description' => 'The language of the metadata.',
                    'required' => true,
                    'allowEmpty' => false
                ));

                $this->addElement('Text', 'keywords', array(
                    'label' => 'Keywords',
                    'description' => 'One or more localized keywords that describe your app. Separate search terms with commas. At least one keyword of greater than two characters is required. You can provide up to 100 bytes of content. Your app is searchable by app name and company name, so you do not need to duplicate these values in the keyword list. Names of other apps or companies are not allowed.',
                    'required' => true,
                    'allowEmpty' => false
                ));

                $this->addElement('Text', 'primary_category', array(
                    'label' => 'Primary Category',
                    'description' => "The categories that best describe the app you're adding.<br />For a list of categories, please visit: <a href='http://www.idev101.com/code/Distribution/categories.html' target='_blank'>http://www.idev101.com/code/Distribution/categories.html</a> and <a href='https://developer.apple.com/library/ios/documentation/LanguagesUtilities/Conceptual/iTunesConnect_Guide/Chapters/FirstSteps.html#//apple_ref/doc/uid/TP40011225-CH19-SW8' target='_blank'>https://developer.apple.com/library/ios/documentation/LanguagesUtilities/Conceptual/iTunesConnect_Guide/Chapters/FirstSteps.html#//apple_ref/doc/uid/TP40011225-CH19-SW8</a>.",
                    'required' => true,
                    'allowEmpty' => false,
                ));
                $this->primary_category->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

                $this->addElement('Text', 'secondary_category', array(
                    'label' => 'Secondary Category',
                    'description' => "The categories that best describe the app you're adding.<br />For a list of categories, please visit: <a href='http://www.idev101.com/code/Distribution/categories.html' target='_blank'>http://www.idev101.com/code/Distribution/categories.html</a> and <a href='https://developer.apple.com/library/ios/documentation/LanguagesUtilities/Conceptual/iTunesConnect_Guide/Chapters/FirstSteps.html#//apple_ref/doc/uid/TP40011225-CH19-SW8' target='_blank'>https://developer.apple.com/library/ios/documentation/LanguagesUtilities/Conceptual/iTunesConnect_Guide/Chapters/FirstSteps.html#//apple_ref/doc/uid/TP40011225-CH19-SW8</a>."
                ));
                $this->secondary_category->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

                $this->addElement('Text', 'rating', array(
                    'label' => 'Rating',
                    'description' => 'The rating for your app for the purpose of parental controls on the store. For information, see: <a href="https://developer.apple.com/library/ios/documentation/LanguagesUtilities/Conceptual/iTunesConnect_Guide/Chapters/FirstSteps.html#//apple_ref/doc/uid/TP40011225-CH19-SW19" target="_blank">https://developer.apple.com/library/ios/documentation/LanguagesUtilities/Conceptual/iTunesConnect_Guide/Chapters/FirstSteps.html#//apple_ref/doc/uid/TP40011225-CH19-SW19</a>.',
                    'required' => true,
                    'allowEmpty' => false
                ));
                $this->rating->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

                $this->addElement('Dummy', 'contact_details', array(
                    'label' => Zend_Registry::get('Zend_Translate')->_('<center><h3>Contact Details</h3></center>'),
                ));
                $this->contact_details->getDecorator('Label')->setOptions(array('escape' => false));

//            if (($package === 'pro')) {
                $this->addElement('Text', 'first_name', array(
                    'label' => 'First Name',
                    'description' => "Contact information of the person in your organization who should be contacted if the App Review team has any questions or needs additional information.",
                    'required' => true,
                    'allowEmpty' => false,
                ));

                $this->addElement('Text', 'last_name', array(
                    'label' => 'Last Name',
                    'description' => "Contact information of the person in your organization who should be contacted if the App Review team has any questions or needs additional information.",
                    'required' => true,
                    'allowEmpty' => false,
                ));

                $this->addElement('Text', 'email_contact_details', array(
                    'label' => 'Email',
                    'description' => "Contact information of the person in your organization who should be contacted if the App Review team has any questions or needs additional information.",
                    'required' => true,
                    'allowEmpty' => false,
                    'validators' => array(array('EmailAddress', 1)),
                    'filters' => array('StringTrim')
                ));

                $this->addElement('Text', 'phone', array(
                    'label' => 'Phone Number',
                    'description' => "Contact information of the person in your organization who should be contacted if the App Review team has any questions or needs additional information.",
                    'required' => true,
                    'allowEmpty' => false,
                ));


                $this->addElement('Textarea', 'address', array(
                    'label' => 'Address',
                    'description' => "Contact information of the person in your organization who should be contacted if the App Review team has any questions or needs additional information.",
                    'required' => true,
                    'allowEmpty' => false,
                ));

                $this->addElement('Text', 'city', array(
                    'label' => 'City',
                    'description' => "Contact information of the person in your organization who should be contacted if the App Review team has any questions or needs additional information.",
                    'required' => true,
                    'allowEmpty' => false,
                ));

                $this->addElement('Text', 'state', array(
                    'label' => 'State',
                    'description' => "Contact information of the person in your organization who should be contacted if the App Review team has any questions or needs additional information.",
                    'required' => true,
                    'allowEmpty' => false,
                ));

                $this->addElement('Text', 'zip_code', array(
                    'label' => 'Zip Code',
                    'description' => "Contact information of the person in your organization who should be contacted if the App Review team has any questions or needs additional information.",
                    'required' => true,
                    'allowEmpty' => false,
                ));

                //TAKING COUNTRIES OBJECT
                $locale = Zend_Registry::get('Zend_Translate')->getLocale();
                $countries = Zend_Locale::getTranslationList('territory', $locale, 2);
                foreach ($countries as $keys => $tempCountry) {
                    $country[$keys] = $tempCountry;
                }
                $this->addElement('Select', 'country', array(
                    'label' => 'Country',
                    'description' => "Contact information of the person in your organization who should be contacted if the App Review team has any questions or needs additional information.",
                    'multiOptions' => $country
                ));
//            }


                $this->addElement('Text', 'support_url', array(
                    'label' => 'Support URL',
                    'description' => "The support website you plan to provide for users who have questions regarding the app (You can also provide the URL of the 'contact' page of your website). The support URL must lead to actual contact information so that your users can contact you regarding app issues, general feedback, and feature enhancement requests. The URL can specify a localized site.<br/>Include the entire URL, including the protocol. For example, <a href='javascript:void(0)'>http://support.example.com</a>.",
                    'required' => true,
                    'allowEmpty' => false,
                ));
                $this->support_url->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));


                $this->addElement('Text', 'marketing_url', array(
                    'label' => 'Marketing URL',
                    'description' => "The website (web page) where users get more information about the app. The URL can specify a localized site.<br />Include the entire URL, including the protocol. For example, <a href='javascript:void(0)'>http://app.example.com</a>."
                ));
                $this->marketing_url->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

                $this->addElement('Text', 'privacy_url', array(
                    'label' => 'Privacy Policy URL',
                    'description' => "A URL that links to your company's privacy policy. Privacy policy URLs are required for all apps that offer auto-renewable or free subscriptions and for apps that are set to Made for Kids. Customers see this URL on their invoice and on the subscription confirmation email they receive."
                ));
                $this->privacy_url->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

                $this->addElement('Text', 'copyright', array(
                    'label' => 'Copyright',
                    'description' => "The name of the person or entity that owns the exclusive rights to the app, preceded by the year the rights were obtained (for example, 2014 Example, Inc.). The copyright symbol is added automatically.",
                    'required' => true,
                    'allowEmpty' => false,
                ));
                $this->copyright->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));



//            $this->addElement('Select', 'category', array(
//                'label' => 'App Category',
//                'required' => true,
//                'allowEmpty' => false,
//                'multiOptions' => array(
//                    'book_&_reference' => 'Books & Reference',
//                    'business' => 'Business',
//                    'comics' => 'Comics',
//                    'communication' => 'Communication',
//                    'education' => 'Education',
//                    'entertainment' => 'Entertainment',
//                    'finance' => 'Finance',
//                    'health_&_fitness' => 'Health & Fitness',
//                    'libraries_&_demo' => 'Libraries & Demo',
//                    'lifestyle' => 'Lifestyle',
//                    'media_&_video' => 'Media & Video',
//                    'medical' => 'Medical',
//                    'music_&_audio' => 'Music & Audio',
//                    'news_&_magazines' => 'News & Magazines',
//                    'personalization' => 'Personalization',
//                    'photography' => 'Photography',
//                    'productivity' => 'Productivity',
//                    'shopping' => 'Shopping',
//                    'social' => 'Social',
//                    'sports' => 'Sports',
//                    'tools' => 'Tools',
//                    'transportation' => 'Transportation',
//                    'traval_&_local' => 'Travel & Local',
//                    'weather' => 'Weather'
//                ),
//            ));
//
//            $this->addElement('Select', 'content_rating', array(
//                'label' => 'Content Rating',
//                'description' => 'Note: As per <a href=\'https://support.google.com/googleplay/android-developer/answer/188189#ugc\' target=\'_blank\'>Google Play content rating policy</a>, the Content Rating of your app cannot be one of the other 2 options: "Low Maturity" and "Everyone" as it will enable communication between users, and hence requires "Medium Maturity" or higher content rating.',
//                'required' => true,
//                'allowEmpty' => false,
//                'multiOptions' => array(
//                    '' => '',
//                    'high_maturity' => 'High Maturity',
//                    'medium_maturity' => 'Medium Maturity'
//                ),
//            ));
//            $this->content_rating->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
//            $this->addElement('Dummy', 'contact_details', array(
//                'label' => Zend_Registry::get('Zend_Translate')->_('<center><h3>Contact Details</h3></center>'),
//            ));
//            $this->contact_details->getDecorator('Label')->setOptions(array('escape' => false));
//
//            $this->addElement('Text', 'website_contact_details', array(
//                'label' => 'Website',
//                'description' => "Website to be associated with this App's listing on Google Play.",
//                'required' => true,
//                'allowEmpty' => false,
//            ));
//
//            $this->addElement('Text', 'email_contact_details', array(
//                'label' => 'Email',
//                'description' => "Email address to be associated with this App's listing on Google Play.",
//                'required' => true,
//                'allowEmpty' => false,
//                'validators' => array(array('EmailAddress', 1)),
//                'filters' => array('StringTrim')
//            ));



                $this->addElement('Dummy', 'app_default_settings', array(
                    'label' => Zend_Registry::get('Zend_Translate')->_('<center><h3>Default App Settings</h3></center>'),
                ));
                $this->app_default_settings->getDecorator('Label')->setOptions(array('escape' => false));

                $url = $view->url(array('module' => 'siteapi', 'controller' => 'settings'), 'admin_default', true);
                $this->addElement('Radio', 'siteapi_validate_ssl', array(
                    'label' => 'Select for https',
                    'description' => 'To create mobile application for your website, please select appropriate option. [Note: I want to run my website on https, what should I do ? <a href="' . $url . '#siteapi_ssl_verification" target="_blank">click here</a> to read about this. If you are still working to run your website on https then you should wait to send us mobile application request until site run on https]',
                    'multiOptions' => array(
                        0 => 'I do not want to run my website on https',
                        1 => 'I am running my website on https'
                    ),
                    'required' => true,
                    'allowEmpty' => false
                ));
                $this->siteapi_validate_ssl->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

                $consumerCalling = $view->url(array('module' => 'siteapi', 'controller' => 'consumers', 'action' => 'manage'), 'admin_default', true);
                $this->addElement('Text', 'api_consumer_key', array(
                    'label' => 'API Consumer Key',
                    'description' => 'Enter the correct API Consumer Key for this Android App that you have created from the "API Consumers" section in the Admin Panel of "REST API Plugin". This is required for communication between your server and mobile apps. Please <a href="' . $consumerCalling . '" target="_blank">click here</a>, If you have not configured yet.<br /><b>Note:</b> We recommend that the API of your website should be used on SSL. For details, see "API communication on SSL" field of <a href="' . $url . '">administration</a> of "SocialEngine REST API Plugin".',
                    'required' => true,
                    'allowEmpty' => false
                ));
                $this->api_consumer_key->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

                $this->addElement('Text', 'api_consumer_secret', array(
                    'label' => 'API Consumer Secret',
                    'description' => 'Enter the correct API Consumer Secret for this Android App that you have created from the "API Consumers" section in the Admin Panel of "REST API Plugin". This is required for communication between your server and mobile apps. Please <a href="' . $consumerCalling . '" target="_blank">click here</a>, If you have not configured yet.',
                    'required' => true,
                    'allowEmpty' => false
                ));
                $this->api_consumer_secret->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
                
                $mapGuidelines = $view->url(array('module' => 'seaocore', 'controller' => 'settings', 'action' => 'map-guidelines'), 'admin_default', true);
                $this->addElement('Text', 'map_key', array(
                    'label' => 'Google Places API Key',
                    'description' => 'The Google Places API Key for your app. [Please visit the <a href="' . $mapGuidelines . '" target="_blank">Guidelines for configuring Google Places API key</a> to see how to obtain these credentials.]<br />[Note: We recommend you to enable the billing feature for this API calling as google allow free usage only up to a certain limit, after which it does not give the required response resulting in blank pages. Please <a href="https://support.google.com/cloud/answer/6158867" target="_blank">click here</a> to know more .]',
                    'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.google.map.key')
                ));
                $this->map_key->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

                if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('music')) {
                    $this->addElement('Radio', 'enable_background_service', array(
                        'label' => 'Playing Music in Background',
                        'description' => 'For the Music Plugin’s features in the app, when users play a music playlist and browse to other parts of your app, or browse to other apps, then do you want that music to be played in the background? [Note: If you select “Yes”, then your iOS App could be reject at the time of first submission, with Apple demanding explanation for requirement of music playing in background. So, we recommend that you choose “No” for first time submission of the app, and can choose “Yes” for next app upgrade release.]',
                        'multiOptions' => array(
                            1 => "Yes, enable playing of music in background",
                            0 => "No, disable playing of music in background"
                        ),
                        'value' => 0
                    ));
                }

//            $this->addElement('Text', 'twitter_app_id', array(
//                'decorators' => array(array('ViewScript', array(
//                            'viewScript' => '_twitterLogin.tpl',
//                            'class' => 'form element'
//                        )))
//            ));

                $this->addElement('Text', 'facebook_app_id', array(
                    'decorators' => array(array('ViewScript', array(
                                'viewScript' => '_facebookLogin.tpl',
                                'class' => 'form element'
                            )))
                ));

                $this->addElement('Text', 'facebook_access_permission', array(
                    'decorators' => array(array('ViewScript', array(
                                'viewScript' => '_facebookAccessPermission.tpl',
                                'class' => 'form element'
                            )))
                ));

                $this->addElement('Text', 'facebook_app_display_name', array(
                    'label' => 'Enter Facebook App Display Name',
                    'description' => 'You can find it from <a href="https://developers.facebook.com" target="_blank">https://developers.facebook.com</a> >> My Apps (Select Your App) >> Settings >> <a target="_blank" class="mleft5" title="View Screenshot" href="application/modules/Siteiosapp/externals/images/facebook_dispay_name.png" target="_blank"><img src="application/modules/Siteandroidapp/externals/images/eye.png" /></a>'
                ));
                $this->facebook_app_display_name->getDecorator('description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));



//            $facebookIntegrationPage = $view->url(array('module' => 'user', 'controller' => 'settings', 'action' => 'facebook'), 'admin_default', true);
//            $this->addElement('Checkbox', 'facebook_app_id', array(
//              'description' => 'Facebook Login',
//              'label' => 'Enable login using Facebook [You should have enabled Facebook Integration <a href="' . $facebookIntegrationPage . '">from here</a>. You can use the SocialEngineAddOns "Facebook Application Configuration and Submission Service" if required.]'
//            ));
//
//            
//            $this->facebook_app_id->getDecorator('label')->setOptions(array('escape' => false));
//            $this->addElement('Text', 'facebook_app_id', array(
//                'label' => 'Facebook Login',
//                'description' => 'Enter the valid facebook id. Please integrate SocialEngine to Facebook. To do so, create an Application through the <a href="http://www.facebook.com/developers/apps.php" target="_blank">Facebook Developers</a> page.',
//                'required' => true,
//                'allowEmpty' => false,
//            ));
//            $this->facebook_app_id->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
//            $this->addElement('Text', 'twitter_key', array(
//                'label' => 'Twitter Key',
//                'description' => 'Enter the valid twitter key. Please integrate SocialEngine to Twitter. To do so, create an Application through the <a href="https://dev.twitter.com/apps/new" target="_blank">Twitter Developers</a> page. You will need to select "Read & Write" in order to allow posting to Twitter.',
//                'required' => true,
//                'allowEmpty' => false,
//            ));
//            $this->twitter_key->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
//
//            $this->addElement('Text', 'twitter_secret', array(
//                'label' => 'Twitter Secret',
//                'description' => 'Enter the valid twitter secret. Please integrate SocialEngine to Twitter. To do so, create an Application through the <a href="https://dev.twitter.com/apps/new" target="_blank">Twitter Developers</a> page. You will need to select "Read & Write" in order to allow posting to Twitter.',
//                'required' => true,
//                'allowEmpty' => false,
//            ));
//            $this->twitter_secret->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

                $this->addElement('Button', 'submit', array(
                    'label' => 'Save Changes and Continue',
                    'type' => 'submit',
                    'ignore' => true,
//                    'order' => 500,
                ));
            } elseif ($tab == 2) {
                $this->addElement('Dummy', 'app_color_code', array(
                    'label' => Zend_Registry::get('Zend_Translate')->_('<center><h3>App Branding Colors</h3></center>'),
                ));
                $this->app_color_code->getDecorator('Label')->setOptions(array('escape' => false));

                $this->addElement('Text', 'app_header_color', array(
                    'decorators' => array(array('ViewScript', array(
                                'viewScript' => '_themeHeaderColor.tpl',
                                'class' => 'form element'
                            )))
                ));

//            $this->addElement('Text', 'app_header_color_dark', array(
//                'decorators' => array(array('ViewScript', array(
//                            'viewScript' => '_themeHeaderColorDark.tpl',
//                            'class' => 'form element'
//                        )))
//            ));
//
//            $this->addElement('Text', 'app_header_text_color', array(
//                'decorators' => array(array('ViewScript', array(
//                            'viewScript' => '_themeHeaderTextColor.tpl',
//                            'class' => 'form element'
//                        )))
//            ));
//
//            $this->addElement('Text', 'app_header_color_accent', array(
//                'decorators' => array(array('ViewScript', array(
//                            'viewScript' => '_themeHeaderColorAccent.tpl',
//                            'class' => 'form element'
//                        )))
//            ));

                $this->addElement('Dummy', 'graphic_assets', array(
                    'label' => Zend_Registry::get('Zend_Translate')->_('<center><h3>Graphic Assets</h3><br />[NOTE: You can download sample Graphic Assets for your reference from here: <a href="https://www.socialengineaddons.com/sites/default/files/Sample_Graphics_Assets_iOS_App.rar">https://www.socialengineaddons.com/sites/default/files/Sample_Graphics_Assets_iOS_App.rar</a>. If you would like us to build the required graphic assets for your iOS and Android apps, then please order our service: "<a href="http://www.socialengineaddons.com/services/building-graphic-assets-ios-android-mobile-apps" target="_blank">Building Graphic Assets for Mobile Apps</a>".]</center>'),
                ));
                $this->graphic_assets->getDecorator('Label')->setOptions(array('escape' => false));

                $this->addElement('Text', 'normal_font', array(
                    'label' => Zend_Registry::get('Zend_Translate')->_("App Normal Font Style"),
                    'description' => 'Enter the \'Normal Font Name\' for your App, <a href="http://iosfonts.com/" target="_blank">click here</a> to get font name.',
                    'required' => true,
                    'allowEmpty' => false,
                    'value' => 'Helvetica Neue'
                ));
                $this->normal_font->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

                $this->addElement('Text', 'bold_font', array(
                    'label' => Zend_Registry::get('Zend_Translate')->_("Apps Bold Font Style"),
                    'description' => 'Enter the \'Bold Font Name\' for your App, <a href="http://iosfonts.com/" target="_blank">click here</a> to get font name.',
                    'required' => true,
                    'allowEmpty' => false,
                    'value' => 'Arial-BoldMT'
                ));
                $this->bold_font->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

                $this->addElement('File', 'large_app_icon', array(
                    'label' => Zend_Registry::get('Zend_Translate')->_('Large App Icon (1024 x 1024), PNG'),
                    'description' => '1024 x 1024 pixels, 72 dpi, RGB, flattened, no transparency.',
                    'validators' => array(
                        array('Extension', false, 'png')
                    )
                ));

                $this->addElement('Dummy', 'app_icon', array(
                    'label' => Zend_Registry::get('Zend_Translate')->_('<h3>App Icons</h3>'),
                ));
                $this->app_icon->getDecorator('Label')->setOptions(array('escape' => false));

//            $this->addElement('File', 'hi_res_icon', array(
//                'label' => Zend_Registry::get('Zend_Translate')->_('Hi-res icon'),
//                'description' => '512 x 512 pixels, 32-bit PNG (with alpha)',
//                'validators' => array(
//                    array('Extension', false, 'png')
//                )
//            ));           

                $this->addElement('File', 'app_icon_1', array(
                    'label' => 'Icon in 29 x 29 pixels, PNG',
                    'validators' => array(
                        array('Extension', false, 'png')
                    )
                ));

                $this->addElement('File', 'app_icon_2', array(
                    'label' => 'Icon in 40 x 40 pixels, PNG',
                    'validators' => array(
                        array('Extension', false, 'png')
                    )
                ));

                $this->addElement('File', 'app_icon_3', array(
                    'label' => 'Icon in 50 x 50 pixels, PNG',
                    'validators' => array(
                        array('Extension', false, 'png')
                    )
                ));

                $this->addElement('File', 'app_icon_4', array(
                    'label' => 'Icon in 57 x 57 pixels, PNG',
                    'validators' => array(
                        array('Extension', false, 'png')
                    )
                ));

                $this->addElement('File', 'app_icon_5', array(
                    'label' => 'Icon in 58 x 58 pixels, PNG',
                    'validators' => array(
                        array('Extension', false, 'png')
                    )
                ));

                $this->addElement('File', 'app_icon_6', array(
                    'label' => 'Icon in 60 x 60 pixels, PNG',
                    'validators' => array(
                        array('Extension', false, 'png')
                    )
                ));

                $this->addElement('File', 'app_icon_7', array(
                    'label' => 'Icon in 72 x 72 pixels, PNG',
                    'validators' => array(
                        array('Extension', false, 'png')
                    )
                ));

                $this->addElement('File', 'app_icon_8', array(
                    'label' => 'Icon in 76 x 76 pixels, PNG',
                    'validators' => array(
                        array('Extension', false, 'png')
                    )
                ));

                $this->addElement('File', 'app_icon_9', array(
                    'label' => 'Icon in 80 x 80 pixels, PNG',
                    'validators' => array(
                        array('Extension', false, 'png')
                    )
                ));

                $this->addElement('File', 'app_icon_16', array(
                    'label' => 'Icon in 87 x 87 pixels, PNG',
                    'validators' => array(
                        array('Extension', false, 'png')
                    )
                ));

                $this->addElement('File', 'app_icon_10', array(
                    'label' => 'Icon in 100 x 100 pixels, PNG',
                    'validators' => array(
                        array('Extension', false, 'png')
                    )
                ));

                $this->addElement('File', 'app_icon_11', array(
                    'label' => 'Icon in 114 x 114 pixels, PNG',
                    'validators' => array(
                        array('Extension', false, 'png')
                    )
                ));

                $this->addElement('File', 'app_icon_12', array(
                    'label' => 'Icon in 120 x 120 pixels, PNG',
                    'validators' => array(
                        array('Extension', false, 'png')
                    )
                ));

                $this->addElement('File', 'app_icon_13', array(
                    'label' => 'Icon in 144 x 144 pixels, PNG',
                    'validators' => array(
                        array('Extension', false, 'png')
                    )
                ));

                $this->addElement('File', 'app_icon_14', array(
                    'label' => 'Icon in 152 x 152 pixels, PNG',
                    'validators' => array(
                        array('Extension', false, 'png')
                    )
                ));

                $this->addElement('File', 'app_icon_15', array(
                    'label' => 'Icon in 180 x 180 pixels, PNG',
                    'validators' => array(
                        array('Extension', false, 'png')
                    )
                ));

                $this->addElement('Button', 'submit', array(
                    'label' => 'Save Changes and Continue',
                    'type' => 'submit',
                    'ignore' => true,
//                    'order' => 500,
                ));
            } elseif ($tab == 3) {
                $this->addElement('Dummy', 'splash_screen_portrait', array(
                    'label' => Zend_Registry::get('Zend_Translate')->_('<h3>Splash Screen Images - Portrait</h3>'),
                ));
                $this->splash_screen_portrait->getDecorator('Label')->setOptions(array('escape' => false));

//            $this->addElement('File', 'splash_portrait_1', array(
//                'label' => 'Image in 200 x 320 pixels, PNG',
//                'validators' => array(
//                    array('Extension', false, 'png')
//                )
//            ));
//            $this->addElement('File', 'splash_portrait_1', array(
//                'label' => 'Image in 320 x 480 pixels, PNG (For iPhone)',
//                'validators' => array(
//                    array('Extension', false, 'png')
//                )
//            ));

                $this->addElement('File', 'splash_portrait_2', array(
                    'label' => 'Image in 640 x 960 pixels, PNG (For iPhone)',
                    'validators' => array(
                        array('Extension', false, 'png')
                    )
                ));

                $this->addElement('File', 'splash_portrait_3', array(
                    'label' => 'Image in 640 x 1136 pixels, PNG (For iPhone)',
                    'validators' => array(
                        array('Extension', false, 'png')
                    )
                ));

                $this->addElement('File', 'splash_portrait_4', array(
                    'label' => 'Image in 768 x 1024 pixels, PNG (For iPad)',
                    'validators' => array(
                        array('Extension', false, 'png')
                    )
                ));

                $this->addElement('File', 'splash_portrait_5', array(
                    'label' => 'Image in 1536 x 2048 pixels, PNG (For iPad)',
                    'validators' => array(
                        array('Extension', false, 'png')
                    )
                ));

//            $this->addElement('File', 'splash_portrait_3', array(
//                'label' => 'Image in 480 x 800 pixels, PNG',
//                'validators' => array(
//                    array('Extension', false, 'png')
//                )
//            ));
//
//            $this->addElement('File', 'splash_portrait_4', array(
//                'label' => 'Image in 720 x 1280 pixels, PNG',
//                'validators' => array(
//                    array('Extension', false, 'png')
//                )
//            ));

                $this->addElement('Dummy', 'splash_screen_landscape', array(
                    'label' => Zend_Registry::get('Zend_Translate')->_('<h3>Splash Screen Images - Landscape</h3>'),
                ));
                $this->splash_screen_landscape->getDecorator('Label')->setOptions(array('escape' => false));


                $this->addElement('File', 'splash_landscape_1', array(
                    'label' => 'Image in 480 x 320 pixels, PNG (For iPhone)',
                    'validators' => array(
                        array('Extension', false, 'png')
                    )
                ));

                $this->addElement('File', 'splash_landscape_2', array(
                    'label' => 'Image in 960 x 640 pixels, PNG (For iPhone)',
                    'validators' => array(
                        array('Extension', false, 'png')
                    )
                ));

                $this->addElement('File', 'splash_landscape_3', array(
                    'label' => 'Image in 1024 x 768 pixels, PNG (For iPad)',
                    'validators' => array(
                        array('Extension', false, 'png')
                    )
                ));

                $this->addElement('File', 'splash_landscape_4', array(
                    'label' => 'Image in 2048 x 1536 pixels, PNG (For iPad)',
                    'validators' => array(
                        array('Extension', false, 'png')
                    )
                ));

                $this->addElement('Dummy', 'slideshow_first_slide', array(
                    'label' => Zend_Registry::get('Zend_Translate')->_('<h3>Introductory Slideshow Details - First Slide</h3>'),
                ));
                $this->slideshow_first_slide->getDecorator('Label')->setOptions(array('escape' => false));

                $this->addElement('Text', 'slideshow_slide_1_title', array(
                    'label' => 'Title',
//                'required' => true,
//                'allowEmpty' => false,
                ));

                $this->addElement('Textarea', 'slideshow_slide_1_description', array(
                    'label' => 'Description',
//                'required' => true,
//                'allowEmpty' => false,
                ));

//            $this->addElement('File', 'slideshow_slide_image_1_1', array(
//                'label' => 'Image in 640 x 1136 pixels, PNG (For iPhone)',
//                'validators' => array(
//                    array('Extension', false, 'png')
//                )
//            ));

                $this->addElement('File', 'slideshow_slide_image_1_2', array(
                    'label' => 'Image in 1536 x 2048 pixels, PNG (For iPad)',
                    'validators' => array(
                        array('Extension', false, 'png')
                    )
                ));


                $this->addElement('Dummy', 'slideshow_second_slide', array(
                    'label' => Zend_Registry::get('Zend_Translate')->_('<h3>Introductory Slideshow Details - Second Slide</h3>'),
                ));
                $this->slideshow_second_slide->getDecorator('Label')->setOptions(array('escape' => false));

                $this->addElement('Text', 'slideshow_slide_2_title', array(
                    'label' => 'Title',
//                'required' => true,
//                'allowEmpty' => false,
                ));

                $this->addElement('Textarea', 'slideshow_slide_2_description', array(
                    'label' => 'Description',
//                'required' => true,
//                'allowEmpty' => false,
                ));

//            $this->addElement('File', 'slideshow_slide_image_2_1', array(
//                'label' => 'Image in 640 x 1136 pixels, PNG (For iPhone)',
//                'validators' => array(
//                    array('Extension', false, 'png')
//                )
//            ));

                $this->addElement('File', 'slideshow_slide_image_2_2', array(
                    'label' => 'Image in 1536 x 2048 pixels, PNG (For iPad)',
                    'validators' => array(
                        array('Extension', false, 'png')
                    )
                ));

                $this->addElement('Dummy', 'slideshow_third_slide', array(
                    'label' => Zend_Registry::get('Zend_Translate')->_('<h3>Introductory Slideshow Details - Third Slide</h3>'),
                ));
                $this->slideshow_third_slide->getDecorator('Label')->setOptions(array('escape' => false));

                $this->addElement('Text', 'slideshow_slide_3_title', array(
                    'label' => 'Title',
//                'required' => true,
//                'allowEmpty' => false,
                ));

                $this->addElement('Textarea', 'slideshow_slide_3_description', array(
                    'label' => 'Description',
//                'required' => true,
//                'allowEmpty' => false,
                ));

//            $this->addElement('File', 'slideshow_slide_image_3_1', array(
//                'label' => 'Image in 640 x 1136 pixels, PNG (For iPhone)',
//                'validators' => array(
//                    array('Extension', false, 'png')
//                )
//            ));

                $this->addElement('File', 'slideshow_slide_image_3_2', array(
                    'label' => 'Image in 1536 x 2048 pixels, PNG (For iPad)',
                    'validators' => array(
                        array('Extension', false, 'png')
                    )
                ));

                $this->addElement('Button', 'submit', array(
                    'label' => 'Save Changes and Continue',
                    'type' => 'submit',
                    'ignore' => true,
//                    'order' => 500,
                ));
            } else if ($tab == 4) {

                // Language File Upload Options
                $this->addElement('Dummy', 'language_assets', array(
                    'Label' => Zend_Registry::get('Zend_Translate')->_('<center><h3>Language Assets [Optional]</h3><br />You can download sample "Language File" for your reference from here: "<a href="http://mobiledemo.socialengineaddons.com/public/admin/Sample_iOS_English_Language.csv">http://mobiledemo.socialengineaddons.com/public/admin/Sample_iOS_English_Language.csv</a>". Below, you can uplaod the language files.<br /><br />'
                            . 'You need to add your changes at right side phrases of CSV file. For example if you want to change "Browse as a Guest" of French csv file then do changes at right side of ";" <br /><br /> '
                            . '<span style="font-weight:bold;">Text before your changes</span><br />'
                            . '&nbsp;&nbsp;&nbsp;"browse_as_guest";"Browse as a Guest"<br /><br />'
                            . '<span style="font-weight:bold;">Text after your changes in French</span><br />'
                            . '&nbsp;&nbsp;&nbsp;"browse_as_guest";"Parcourir en tant qu\'invité"'
                            . '<br /><br /> [Note: If you are not uploading any language file here then, English will be default language for your App.]</center>'),
                ));
                $this->language_assets->getDecorator('Label')->setOptions(array('escape' => false));


                $getLanguages = Engine_Api::_()->getApi('Core', 'siteapi')->getLanguages(true);
                if (isset($getLanguages)) {

                    foreach ($getLanguages as $key => $label) {

                        $this->addElement('File', $key, array(
                            'label' => 'Upload Language File For: [' . $label . ']',
                            'validators' => array(
                                array('Extension', false, 'csv')
                            )
                        ));
                    }
                }
                // Langauge work ends

                $this->addElement('Button', 'submit', array(
                    'label' => 'Save Changes and Continue',
                    'type' => 'submit',
                    'ignore' => true,
//                    'order' => 500,
                ));
            } else if ($tab == 5) {
                $this->removeElement('required_fields');
//                $this->addElement('Dummy', 'ios_app_rejection', array(
//                    'label' => Zend_Registry::get('Zend_Translate')->_('<center><h3>App Rejection Confirmation</h3></center>'),
//                ));
//                $this->ios_app_rejection->getDecorator('Label')->setOptions(array('escape' => false));
//


//            $this->addElement('Checkbox', 'ios_app_rejection_confirmation', array(
//              'description' => '', //'iOS App Rejection',
//              'label' => 'I have read all reasons due of that my iOS App could be reject. I have resolved all of them on my site.'
//            ));
//            $this->ios_app_rejection_confirmation->getDecorator('description')->setOptions(array('escape' => false));
//                $submitElementOptions = array(
//                    'label' => 'Save Changes',
//                    'type' => 'submit',
//                    'ignore' => true,
//                    'decorators' => array('ViewHelper')
//                );

                if (!empty($this->_doWeHaveLatestVersion)) {
                    // Language File Upload Options
                    $this->addElement('Dummy', 'download_text', array(
                        'Label' => Zend_Registry::get('Zend_Translate')->_('<div class="seaocore_tip"><span><p>You do not have the latest version of the above listed plugins, Please upgrade all listed modules to the latest version to enable its integration with iOS Mobile Application.</p></span></div><br />'),
                    ));
                    $this->download_text->getDecorator('Label')->setOptions(array('escape' => false));
                } else {
                    // Language File Upload Options
                    $this->addElement('Dummy', 'download_text', array(
                        'Label' => Zend_Registry::get('Zend_Translate')->_('<h3>Download Tar File</h3><p>
                Before downloading this file, please ensure that you have filled all the App Submission Details correctly. This tar file contains all the details required for building your iOS App.
            </p><br />'),
                    ));
                    $this->download_text->getDecorator('Label')->setOptions(array('escape' => false));
                }

//                $this->addElement('Button', 'submit', $submitElementOptions);

                $getWebsiteName = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
                $websiteStr = str_replace(".", "-", $getWebsiteName);
                if (empty($this->_doWeHaveLatestVersion) && is_dir(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public/' . 'ios-' . $websiteStr . '-app-builder')) {
                    
                    $this->addElement('Text', 'ios_app_rejection_confirmation', array(
                        'decorators' => array(array('ViewScript', array(
                                    'viewScript' => '_rejectionConfirmation.tpl',
                                    'class' => 'form element'
                                )))
                    ));
                
                    // Element: Download TAR
                    $this->addElement('Cancel', 'download_tar', array(
                        'label' => 'Download tar',
//                        'prependText' => ' or ',
                        'type' => 'cancel',
                        'ignore' => true,
//                        'link' => true,
//                        'class' => 'Smoothbox',
                        'onclick' => 'Smoothbox.open("' . Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'download')) . '")',
//                        'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'download')),
                        'decorators' => array('ViewHelper'),
                    ));
                    
                    // Element: Download TAR
//                    $this->addElement('Cancel', 'download_tar', array(
//                        'label' => 'Download tar',
//                        'prependText' => ' or ',
//                        'type' => 'cancel',
//                        'ignore' => true,
//                        'link' => true,
//                        'class' => 'smoothbox',
//                        'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'download')),
//                        'decorators' => array('ViewHelper'),
//                    ));
                    // DisplayGroup: buttons
//                    $this->addDisplayGroup(array('submit', 'download_tar'), 'buttons', array(
//                        'decorators' => array(
//                            'FormElements',
//                            'DivDivDivWrapper',
//                        )
//                    ));
                }
            }
        }
    }

}
