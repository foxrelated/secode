<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteandroidapp
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    Settings.php 2015-10-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteandroidapp_Form_Admin_AppBuilder_Settings extends Engine_Form {

    protected $_doWeHaveLatestVersion;
    protected $_enabledTabName;

    public function getDoWeHaveLatestVersion() {
        return $this->_doWeHaveLatestVersion;
    }

    public function setDoWeHaveLatestVersion($doWeHavelatestVersion) {
        $this->_doWeHaveLatestVersion = $doWeHavelatestVersion;
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
            $APIModules = array(
                'album',
                'blog',
                'classified',
                'event',
                'forum',
                'group',
                'music',
                'poll',
                'video'
            );
            $modules = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();
            $enabledModules = array_intersect($APIModules, $modules);

            foreach ($enabledModules as $module) {
                $multiOptions[$module] = Engine_Api::_()->getDbTable('integrated', 'seaocore')->getModuleTitle($module);
            }

            $this->addElement('Dummy', 'required_fields', array(
                'label' => Zend_Registry::get('Zend_Translate')->_('Fields with asterisk (<span style="color:RED">*</span>) are mandatory.')
            ));
            $this->required_fields->getDecorator('Label')->setOptions(array('escape' => false));

            if ($tab == 1) {

                if (($package === 'pro')) {
                    $this->addElement('Dummy', 'google_play_details', array(
                        'label' => Zend_Registry::get('Zend_Translate')->_('<center><h3>Google Play Developer Credentials and Access</h3></center><br /><span style="font-weight: bold;">Important NOTE: </span>For us to be able to submit your App to Google Play, please ensure that you have Turned Off "2-Step Verification" for your Google Developer / Gmail Account. Please follow the steps mentioned in <a href="application/modules/Siteandroidapp/externals/images/2-Step-Verification-Turn-Off.png" target="_blank">this image</a> for this. If you do not turn this off, then we will not be able to access your Google Developer Account to submit your App.<br />If you do not want to turn off 2-Step Verification for your account, then we will provide to you the APK file for the App, using which you will yourself be able to submit your App to the Google Play Store.')
                    ));
                    $this->google_play_details->getDecorator('Label')->setOptions(array('escape' => false));

                    $this->addElement('Radio', 'publish_app', array(
                        'label' => 'Submitting App to Google Play Store',
                        'description' => "How do you want your Android App to be submitted to the Google Play Store?",
                        'multiOptions' => array(
                            1 => 'I want the SocialEngineAddOns Support Team to submit my app.',
                            0 => 'I want my app’s “.apk” file to be sent to me, and I will myself submit the app.'
                        ),
                        'value' => 1,
                        'onchange' => 'gmailAppSubmission()'
                    ));
                    $this->publish_app->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

                    $this->addElement('Radio', 'provide_google_play_account_details', array(
                        'label' => 'Google Play Developer Credentials / Access',
                        'multiOptions' => array(
                            1 => 'I will provide my Google Play Developer Account Details for app submission.',
                            0 => 'I will give permission to socialengineaddons@gmail.com email address, to access my Google Play Developer Console.'
                        ),
                        'value' => 1,
                        'onchange' => 'gmailPlayStorePermission()'
                    ));

                    $this->addElement('Text', 'gmail_permission_done', array(
                        'decorators' => array(array('ViewScript', array(
                                    'viewScript' => '_gmailPermission.tpl',
                                    'class' => 'form element'
                                )))
                    ));
//                $this->addElement('Checkbox', 'gmail_permission_done', array(
//                    'description' => 'Give Permission to Access Google Play Developer Account',
//                    'label' => 'Please follow this <a href="https://youtu.be/U52t96CwisI" target="_blank">Video Tutorial</a> to give permission to our <span style="font-weight: bold;">socialengineaddons@gmail.com</span> email address, to access your Google Play Developer Console.',
////                    'value' => 0
//                ));
//                $this->gmail_permission_done->getDecorator('Label')->setOptions(array('escape' => false));

                    $this->addElement('Text', 'google_play_login_email', array(
                        'label' => 'Google Play Login Email',
                        'description' => '<span style="font-weight:bold;">Turned Off "2-Step Verification" for your Google Developer / Gmail Account</span>',
//                    'required' => true,
//                    'allowEmpty' => false,
                        'validators' => array(array('EmailAddress', 1)),
                        'filters' => array('StringTrim')
                    ));
                    $this->google_play_login_email->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

                    $this->addElement('Text', 'google_play_login_password', array(
                        'label' => 'Google Play Login Password',
//                    'required' => true,
//                    'allowEmpty' => false
                    ));
                }

                $this->addElement('Text', 'ad_placement_id', array(
                    'label' => 'Advertising - Ad Placement ID',
                    'description' => 'You can now monetize your app through Facebook Ads, with Audience network. To enable this advertising, enter the Facebook Ad Placement ID for your app. [To get this ID, please follow this <a href = "https://youtu.be/Y31ZwKIvkNE" target="_blank">video tutorial</a>. You can configure advertising in your app from the “Advertising” section.]'
                ));
                $this->ad_placement_id->getDecorator('description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

//                $this->addElement('Text', 'google_ad_unit_id', array(
//                    'label' => 'Advertising - Google Ad Unit ID',
//                    'description' => 'You can now monetize your app through Google Ads, with Admob integration. To enable this advertising, enter the Google Ad Unit ID for your app. [To get this ID, please follow this <a href = "https://youtu.be/c34xJcNoxik" target="_blank">video tutorial</a>. You can configure advertising in your app from the “Advertising” section.]'                    
//                ));
//                $this->google_ad_unit_id->getDecorator('description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

                $this->addElement('Text', 'google_analytics_tracking_id', array(
                    'label' => ' Google Analytics Tracking ID',
                    'description' => 'Google Analytics is a very useful tool to measure user activity and engagement in your app. To enable this tracking, enter the Google Analytics Tracking ID for your app. [To get this ID, please follow this <a href = "https://youtu.be/_2u9I9HWkns" target="_blank">video tutorial</a>.]'
                ));
                $this->google_analytics_tracking_id->getDecorator('description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));



                $this->addElement('Dummy', 'app_details', array(
                    'label' => Zend_Registry::get('Zend_Translate')->_('<center><h3>App Submission Details</h3></center>'),
                ));
                $this->app_details->getDecorator('Label')->setOptions(array('escape' => false));

                $this->addElement('Text', 'package_name', array(
                    'label' => 'App Package Name',
                    'description' => "Enter the App Package Name (i.e: com.socialengineaddons.app) Click on the following link and if you get any result then it is concluded that the Application Package name is already been used, if you get \"We're sorry, the requested URL was not found on this server.\" then you can use the app package name. [Use only 'Alphabets' to create your package name.]<br/><span id='package_name_url'><a href='https://market.android.com/details?id=com.socialengineaddons.mobileapp' target='_blank'>https://market.android.com/details?id=com.socialengineaddons.mobileapp</a></span>",
//                'required' => true,
//                'allowEmpty' => false,
                    'onkeyup' => 'addPackageUrl();'
                ));
                $this->package_name->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

                $this->addElement('Text', 'project_number', array(
                    'label' => 'Android Project Number',
                    'description' => "Enter project number from Google Account, by this Push Notification will get enable for your App. <a href='https://youtu.be/FgBQuQZUYtQ' target='_blank'>click here</a> to know how you can create Project Number for your App.",
//                'required' => true,
//                'allowEmpty' => false
                ));
                $this->project_number->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

                $this->addElement('Text', 'title', array(
                    'label' => 'App Title',
                    'description' => '0 to 30 characters',
                    'required' => true,
                    'allowEmpty' => false,
                    'maxlength' => 30,
                    'validators' => array(
                        array('stringLength', false, array(0, 30))
                    )
                ));

                $this->addElement('Textarea', 'short_description', array(
                    'label' => 'App Short Description',
                    'description' => '(0 to 80 chars)',
                    'required' => true,
                    'allowEmpty' => false,
                    'validators' => array(
                        array('stringLength', false, array(0, 80))
                    )
                ));

                $this->addElement('Textarea', 'description', array(
                    'label' => 'App Description',
                    'description' => '0 to 4000 chars (See tips for creating policy compliant description: <a href=\'https://support.google.com/googleplay/android-developer/answer/113474\' target=\'_blank\'>https://support.google.com/googleplay/android-developer/answer/113474</a>)',
                    'required' => true,
                    'allowEmpty' => false,
                    'validators' => array(
                        array('stringLength', false, array(0, 4000))
                    )
                ));
                $this->description->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

                $this->addElement('Text', 'default_language', array(
                    'label' => 'Default Language for the App',
                    'description' => 'We will be using this to select the default language for your App while submitting it in the App Store. We will not be doing any translations.',
                    'required' => true,
                    'allowEmpty' => false
                ));

                $this->addElement('Select', 'category', array(
                    'label' => 'App Category',
                    'required' => true,
                    'allowEmpty' => false,
                    'multiOptions' => array(
                        'book_&_reference' => 'Books & Reference',
                        'business' => 'Business',
                        'comics' => 'Comics',
                        'communication' => 'Communication',
                        'education' => 'Education',
                        'entertainment' => 'Entertainment',
                        'finance' => 'Finance',
                        'health_&_fitness' => 'Health & Fitness',
                        'libraries_&_demo' => 'Libraries & Demo',
                        'lifestyle' => 'Lifestyle',
                        'media_&_video' => 'Media & Video',
                        'medical' => 'Medical',
                        'music_&_audio' => 'Music & Audio',
                        'news_&_magazines' => 'News & Magazines',
                        'personalization' => 'Personalization',
                        'photography' => 'Photography',
                        'productivity' => 'Productivity',
                        'shopping' => 'Shopping',
                        'social' => 'Social',
                        'sports' => 'Sports',
                        'tools' => 'Tools',
                        'transportation' => 'Transportation',
                        'traval_&_local' => 'Travel & Local',
                        'weather' => 'Weather'
                    ),
                ));

                $this->addElement('Select', 'content_rating', array(
                    'label' => 'Content Rating',
                    'description' => 'Note: As per <a href=\'https://support.google.com/googleplay/android-developer/answer/188189#ugc\' target=\'_blank\'>Google Play content rating policy</a>, the Content Rating of your app cannot be one of the other 2 options: "Low Maturity" and "Everyone" as it will enable communication between users, and hence requires "Medium Maturity" or higher content rating.',
                    'required' => true,
                    'allowEmpty' => false,
                    'multiOptions' => array(
                        '' => '',
                        'high_maturity' => 'High Maturity',
                        'medium_maturity' => 'Medium Maturity'
                    ),
                ));
                $this->content_rating->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

                $this->addElement('Dummy', 'contact_details', array(
                    'label' => Zend_Registry::get('Zend_Translate')->_('<center><h3>Contact Details</h3></center>'),
                ));
                $this->contact_details->getDecorator('Label')->setOptions(array('escape' => false));

                $this->addElement('Text', 'website_contact_details', array(
                    'label' => 'Website',
                    'description' => "Website to be associated with this App's listing on Google Play.",
                    'required' => true,
                    'allowEmpty' => false,
                ));

                $this->addElement('Text', 'email_contact_details', array(
                    'label' => 'Email',
                    'description' => "Email address to be associated with this App's listing on Google Play.",
                    'required' => true,
                    'allowEmpty' => false,
                    'validators' => array(array('EmailAddress', 1)),
                    'filters' => array('StringTrim')
                ));

                // Deepak add URI validator here.
                $this->addElement('Text', 'policy_url', array(
                    'label' => 'Privacy Policy URL',
                    'description' => "URL of the privacy policy for this app.",
                    'required' => true,
                    'allowEmpty' => false,
                ));

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

                $this->addElement('Multiselect', 'enabledModules', array(
                    'label' => 'Modules in App',
                    'description' => 'Below you can choose the modules from your website that you want to be available in your Android App. [Modules (of SocialEngineAddOns or 3rd-party) which are not getting listed below can be made available in your app <a href="https://www.socialengineaddons.com/page/enabling-socialengineaddons-3rd-party-plugins-ios-android-apps-webview
" target="_blank">via WebView</a>.]',
                    'multiOptions' => $multiOptions,
                    'value' => array_keys($multiOptions)
                ));
                $this->enabledModules->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));

                $mapGuidelines = $view->url(array('module' => 'seaocore', 'controller' => 'settings', 'action' => 'map-guidelines'), 'admin_default', true);
                $this->addElement('Text', 'map_key', array(
                    'label' => 'Google Places API Key',
                    'description' => 'The Google Places API Key for your app. [Please visit the <a href="' . $mapGuidelines . '" target="_blank">Guidelines for configuring Google Places API key</a> to see how to obtain these credentials.]<br />[Note: We recommend you to enable the billing feature for this API calling as google allow free usage only up to a certain limit, after which it does not give the required response resulting in blank pages. Please <a href="https://support.google.com/cloud/answer/6158867" target="_blank">click here</a> to know more .]',
                    'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.google.map.key')
                ));
                $this->map_key->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
                
                
                $this->addElement('Text', 'commit_chat_package', array(
                    'label' => 'CometChat Package Name',
                    'description' => 'Enter the package name of your CometChat. If you are not having it then please contact to the CometChat team.'
                ));

                $this->addElement('Text', 'twitter_app_id', array(
                    'decorators' => array(array('ViewScript', array(
                                'viewScript' => '_twitterLogin.tpl',
                                'class' => 'form element'
                            )))
                ));

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
            } else if ($tab == 2) {
                $this->addElement('Dummy', 'app_color_code', array(
                    'label' => Zend_Registry::get('Zend_Translate')->_('<center><h3>App Branding Colors</h3></center>'),
                ));
                $this->app_color_code->getDecorator('Label')->setOptions(array('escape' => false));

                $this->addElement('Text', 'app_header_color_primary', array(
                    'decorators' => array(array('ViewScript', array(
                                'viewScript' => '_themeHeaderColorPrimary.tpl',
                                'class' => 'form element'
                            )))
                ));

                $this->addElement('Text', 'app_header_color_dark', array(
                    'decorators' => array(array('ViewScript', array(
                                'viewScript' => '_themeHeaderColorDark.tpl',
                                'class' => 'form element'
                            )))
                ));

                $this->addElement('Text', 'app_header_text_color', array(
                    'decorators' => array(array('ViewScript', array(
                                'viewScript' => '_themeHeaderTextColor.tpl',
                                'class' => 'form element'
                            )))
                ));

                $this->addElement('Text', 'app_header_color_accent', array(
                    'decorators' => array(array('ViewScript', array(
                                'viewScript' => '_themeHeaderColorAccent.tpl',
                                'class' => 'form element'
                            )))
                ));

                $this->addElement('Dummy', 'graphic_assets', array(
                    'label' => Zend_Registry::get('Zend_Translate')->_('<center><h3>Graphic Assets</h3><br />[NOTE: You can download sample Graphic Assets for your reference from here: <a href="http://www.socialengineaddons.com/sites/default/files/Sample_Graphics_Assets_Android_App.tar">http://www.socialengineaddons.com/sites/default/files/Sample_Graphics_Assets_Android_App.tar</a>. If you would like us to build the required graphic assets for your iOS and Android apps, then please order our service: "<a href="http://www.socialengineaddons.com/services/building-graphic-assets-ios-android-mobile-apps" target="_blank">Building Graphic Assets for Mobile Apps</a>".]</center>'),
                ));
                $this->graphic_assets->getDecorator('Label')->setOptions(array('escape' => false));


                $this->addElement('Select', 'font', array(
                    'label' => 'App Font Style',
                    'description' => 'Select the font style for your App with below dropdown. <a target="_blank" class="mleft5" title="View Screenshot" href="application/modules/Siteandroidapp/externals/images/fonts-preview.png" target="_blank"><img src="application/modules/Siteandroidapp/externals/images/eye.png" /></a>',
                    'required' => true,
                    'allowEmpty' => false,
                    'multiOptions' => array(
                        'Roboto-Regular' => 'Roboto Regular (Default)',
                        'Abel-Regular' => 'Abel Regular',
                        'Amaranth-Regular' => 'Amaranth Regular',
                        'Audiowide-Regular' => 'Audiowide Regular',
                        'Bellerose' => 'Bellerose Light',
                        'CLOSCP__' => 'Closecall PM',
                        'Comfortaa-Regular' => 'Comfortaa Regular',
                        'Dosis-Regular' => 'Dosis Book',
                        'Exo-Regular' => 'Exo Regular',
                        'FortuneCity' => 'FortuneCity Regular',
                        'HappyMonkey-Regular' => 'Happy Monkey Regular',
                        'Joyful Juliana' => 'Joyful Juliana Regular',
                        'Lato-Regular' => 'Lato Regular',
                        'Oxygen-Regular' => 'Oxygen Regular',
                        'Philosopher-Regular' => 'Philosopher Regular',
                        'Playball-Regular' => 'Playball Regular',
                        'Play-Regular' => 'Play Regular',
                        'PT_Sans-Narrow-Web-Regular' => 'PT Sans Narrow Regular',
                        'Sansation_Regular' => 'Sansation Regular',
                        'Walkway rounded' => 'Walkway Rounded Regular',
                        'Walkway UltraBold' => 'Walkway UltraBond Regular',
                        'KGAlwaysAGoodTime' => 'KG Always A Good Time'
                    ),
                    'value' => 'Roboto-Regular'
                ));
                $this->font->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));


                $this->addElement('File', 'feature_graphics', array(
                    'label' => Zend_Registry::get('Zend_Translate')->_('Feature Graphic'),
                    'description' => '1024 x 500 pixels, 24-bit PNG (no alpha)',
                    'validators' => array(
                        array('Extension', false, 'png')
                    )
                ));

                $this->addElement('Dummy', 'app_icon', array(
                    'label' => Zend_Registry::get('Zend_Translate')->_('<h3>App Icons</h3>'),
                ));
                $this->app_icon->getDecorator('Label')->setOptions(array('escape' => false));

                $this->addElement('File', 'hi_res_icon', array(
                    'label' => Zend_Registry::get('Zend_Translate')->_('Hi-res icon'),
                    'description' => '512 x 512 pixels, 32-bit PNG (with alpha)',
                    'validators' => array(
                        array('Extension', false, 'png')
                    )
                ));

                $this->addElement('File', 'app_icon_2', array(
                    'label' => 'Icon in 48 x 48 pixels, PNG',
                    'validators' => array(
                        array('Extension', false, 'png')
                    )
                ));

                $this->addElement('File', 'app_icon_3', array(
                    'label' => 'Icon in 72 x 72 pixels, PNG',
                    'validators' => array(
                        array('Extension', false, 'png')
                    )
                ));

                $this->addElement('File', 'app_icon_4', array(
                    'label' => 'Icon in 96 x 96 pixels, PNG',
                    'validators' => array(
                        array('Extension', false, 'png')
                    )
                ));

                $this->addElement('File', 'app_icon_5', array(
                    'label' => 'Icon in 180 x 180 pixels, PNG',
                    'validators' => array(
                        array('Extension', false, 'png')
                    )
                ));

                $this->addElement('Dummy', 'splash_screen_portrait', array(
                    'label' => Zend_Registry::get('Zend_Translate')->_('<h3>Splash Screen Images - Portrait</h3>'),
                ));
                $this->splash_screen_portrait->getDecorator('Label')->setOptions(array('escape' => false));

                $this->addElement('File', 'splash_portrait_1', array(
                    'label' => 'Image in 200 x 320 pixels, PNG',
                    'validators' => array(
                        array('Extension', false, 'png')
                    )
                ));


                $this->addElement('File', 'splash_portrait_2', array(
                    'label' => 'Image in 320 x 480 pixels, PNG',
                    'validators' => array(
                        array('Extension', false, 'png')
                    )
                ));

                $this->addElement('File', 'splash_portrait_3', array(
                    'label' => 'Image in 480 x 800 pixels, PNG',
                    'validators' => array(
                        array('Extension', false, 'png')
                    )
                ));

                $this->addElement('File', 'splash_portrait_4', array(
                    'label' => 'Image in 720 x 1280 pixels, PNG',
                    'validators' => array(
                        array('Extension', false, 'png')
                    )
                ));

                $this->addElement('Dummy', 'splash_screen_landscape', array(
                    'label' => Zend_Registry::get('Zend_Translate')->_('<h3>Splash Screen Images - Landscape</h3>'),
                ));
                $this->splash_screen_landscape->getDecorator('Label')->setOptions(array('escape' => false));

                $this->addElement('File', 'splash_landscape_1', array(
                    'label' => 'Image in 320 x 200 pixels, PNG',
                    'validators' => array(
                        array('Extension', false, 'png')
                    )
                ));


                $this->addElement('File', 'splash_landscape_2', array(
                    'label' => 'Image in 480 x 320 pixels, PNG',
                    'validators' => array(
                        array('Extension', false, 'png')
                    )
                ));

                $this->addElement('File', 'splash_landscape_3', array(
                    'label' => 'Image in 800 x 480 pixels, PNG',
                    'validators' => array(
                        array('Extension', false, 'png')
                    )
                ));

                $this->addElement('File', 'splash_landscape_4', array(
                    'label' => 'Image in 1280 x 720 pixels, PNG',
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
            } else if ($tab == 3) {

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

                $this->addElement('File', 'slideshow_slide_image_1_1', array(
                    'label' => 'Image in 480 x 800 pixels, PNG',
                    'validators' => array(
                        array('Extension', false, 'png')
                    )
                ));

                $this->addElement('File', 'slideshow_slide_image_1_2', array(
                    'label' => 'Image in 720 x 1280 pixels, PNG',
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

                $this->addElement('File', 'slideshow_slide_image_2_1', array(
                    'label' => 'Image in 480 x 800 pixels, PNG',
                    'validators' => array(
                        array('Extension', false, 'png')
                    )
                ));

                $this->addElement('File', 'slideshow_slide_image_2_2', array(
                    'label' => 'Image in 720 x 1280 pixels, PNG',
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

                $this->addElement('File', 'slideshow_slide_image_3_1', array(
                    'label' => 'Image in 480 x 800 pixels, PNG',
                    'validators' => array(
                        array('Extension', false, 'png')
                    )
                ));

                $this->addElement('File', 'slideshow_slide_image_3_2', array(
                    'label' => 'Image in 720 x 1280 pixels, PNG',
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
                    'Label' => Zend_Registry::get('Zend_Translate')->_('<h3>Language Assets [Optional]</h3><br />This section is useful for you only if you want your app to be multi-lingual (in multiple languages), or if you want to change any existing English phrases in your app.<br />You can download sample "Language File" for your reference from here: "<a href="http://mobiledemo.socialengineaddons.com/public/admin/Sample_Android_English_Language.csv">http://mobiledemo.socialengineaddons.com/public/admin/Sample_Android_English_Language.csv</a>". Below, you can upload the language files.<br /><br />'
                            . 'You need to add your changes at right side phrases of CSV file. For example if you want to change "Browse as a Guest" of French csv file then do changes at right side of ";" <br /><br /> '
                            . '<span style="font-weight:bold;">Text before your changes</span><br />'
                            . '&nbsp;&nbsp;&nbsp;"browse_as_guest";"Browse as a Guest"<br /><br />'
                            . '<span style="font-weight:bold;">Text after your changes in French</span><br />'
                            . '&nbsp;&nbsp;&nbsp;"browse_as_guest";"Parcourir en tant qu\'invité"'
                            . '<br /><br /> [Note: If you are not uploading any language files from here, then English will be the default language for your App.]'),
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
                // Language work end

                $this->addElement('Button', 'submit', array(
                    'label' => 'Save Changes and Continue',
                    'type' => 'submit',
                    'ignore' => true,
//                    'order' => 500,
                ));
            } else if ($tab == 5) {
                
            } else if ($tab == 6) {

                $this->removeElement('required_fields');

                if (!empty($this->_doWeHaveLatestVersion)) {
                    // Language File Upload Options
                    $this->addElement('Dummy', 'download_text', array(
                        'Label' => Zend_Registry::get('Zend_Translate')->_('<div class="seaocore_tip"><span><p>You do not have the latest version of the above listed plugins, Please upgrade all listed modules to the latest version to enable its integration with Android Mobile Application.</p></span></div><br />'),
                    ));
                    $this->download_text->getDecorator('Label')->setOptions(array('escape' => false));
                } else {
                    // Language File Upload Options
                    $this->addElement('Dummy', 'download_text', array(
                        'Label' => Zend_Registry::get('Zend_Translate')->_('<h3>Download Tar File</h3><p>
                Before downloading this file, please ensure that you have filled all the App Submission Details correctly. This tar file contains all the details required for building your Android App.
            </p><br />'),
                    ));
                    $this->download_text->getDecorator('Label')->setOptions(array('escape' => false));
                }

//                $submitElementOptions = array(
//                    'label' => 'Save Changes',
//                    'type' => 'submit',
//                    'ignore' => true,
//                    'decorators' => array('ViewHelper')
//                );
//
//                $this->addElement('Button', 'submit', $submitElementOptions);

                $getWebsiteName = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
                $websiteStr = str_replace(".", "-", $getWebsiteName);
                if (empty($this->_doWeHaveLatestVersion) && (is_dir(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'public/' . 'android-' . $websiteStr . '-app-builder'))) {
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

//                    // DisplayGroup: buttons
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
