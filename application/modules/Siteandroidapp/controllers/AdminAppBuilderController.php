<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteandroidapp
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    AdminAppBuilderController.php 2015-10-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteandroidapp_AdminAppBuilderController extends Core_Controller_Action_Admin {

    protected $_languagePath;
    protected $_getAPPBuilderBaseURL = 'public/app-builder';
    protected $_getAPPBuilderSettingsFileName = 'settings.php';
    protected $_getAPPBuilderTarFileName = 'app-builder.tar';
    protected $_directoryName = 'app-builder';
    protected $_tarFileSize = 0;
    protected $_requiredFormImages;
    protected $_enabledTabsArray = array(
        1 => 'siteandroidapp_admin_general_settings',
        2 => 'siteandroidapp_admin_graphic_assets',
        3 => 'siteandroidapp_admin_splash_and_slideshows',
        4 => 'siteandroidapp_admin_language_assets',
        5 => 'siteandroidapp_admin_advertising',
        6 => 'siteandroidapp_admin_download',
    );
    protected $_requiredFormFields = array(
        'title',
        'description',
        'short_description',
        'default_language',
        'category',
        'content_rating',
        'website_contact_details',
        'email_contact_details',
        'policy_url',
        'api_consumer_key',
        'api_consumer_secret',
        'font',
        'siteapi_validate_ssl'
    );    
    protected $_EnabledModules = array(
        'advancedactivity',
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
    protected $_getAPPImageDetailsForUpload = array(
        'feature_graphics' => array(
            'title' => 'Feature Graphics (1024 x 500)',
            'width' => 1024,
            'height' => 500,
            'directoryName' => 'Hi-res_Icon',
            'fileName' => 'feature graphics 1024 x 500'
        ),
        'hi_res_icon' => array(
            'title' => 'Hi-res icon (512 x 512)',
            'width' => 512,
            'height' => 512,
            'directoryName' => 'Hi-res_Icon',
            'fileName' => 'Hi-res icon 512 x 512'
        ),
        'app_icon_2' => array(
            'title' => 'APP Icon (48 x 48)',
            'width' => 48,
            'height' => 48,
            'directoryName' => 'App_Icons',
            'fileName' => 'icon 48 x 48'
        ),
        'app_icon_3' => array(
            'title' => 'APP Icon (72 x 72)',
            'width' => 72,
            'height' => 72,
            'directoryName' => 'App_Icons',
            'fileName' => 'icon 72 x 72'
        ),
        'app_icon_4' => array(
            'title' => 'APP Icon (96 x 96)',
            'width' => 96,
            'height' => 96,
            'directoryName' => 'App_Icons',
            'fileName' => 'icon 96 x 96'
        ),
        'app_icon_5' => array(
            'title' => 'APP Icon (180 x 180)',
            'width' => 180,
            'height' => 180,
            'directoryName' => 'App_Icons',
            'fileName' => 'icon 180 x 180'
        ),
        'splash_portrait_1' => array(
            'title' => 'Splash Portrait Image (200 x 320)',
            'width' => 200,
            'height' => 320,
            'directoryName' => 'Splash_Screen_Images/Portrait',
            'fileName' => 'screen 200 x 320'
        ),
        'splash_portrait_2' => array(
            'title' => 'Splash Portrait Image (320 x 480)',
            'width' => 320,
            'height' => 480,
            'directoryName' => 'Splash_Screen_Images/Portrait',
            'fileName' => 'screen 320 x 480'
        ),
        'splash_portrait_3' => array(
            'title' => 'Splash Portrait Image (480 x 800)',
            'width' => 480,
            'height' => 800,
            'directoryName' => 'Splash_Screen_Images/Portrait',
            'fileName' => 'screen 480 x 800'
        ),
        'splash_portrait_4' => array(
            'title' => 'Splash Portrait Image (720 x 1280)',
            'width' => 720,
            'height' => 1280,
            'directoryName' => 'Splash_Screen_Images/Portrait',
            'fileName' => 'screen 720 x 1280'
        ),
        'splash_landscape_1' => array(
            'title' => 'Splash Landscape Image (320 x 200)',
            'width' => 320,
            'height' => 200,
            'directoryName' => 'Splash_Screen_Images/Landscape',
            'fileName' => 'screen 320 x 200'
        ),
        'splash_landscape_2' => array(
            'title' => 'Splash Landscape Image (480 x 320)',
            'width' => 480,
            'height' => 320,
            'directoryName' => 'Splash_Screen_Images/Landscape',
            'fileName' => 'screen 480 x 320'
        ),
        'splash_landscape_3' => array(
            'title' => 'Splash Landscape Image (800 x 480)',
            'width' => 800,
            'height' => 480,
            'directoryName' => 'Splash_Screen_Images/Landscape',
            'fileName' => 'screen 800 x 480'
        ),
        'splash_landscape_4' => array(
            'title' => 'Splash Landscape Image (1280 x 720)',
            'width' => 1280,
            'height' => 720,
            'directoryName' => 'Splash_Screen_Images/Landscape',
            'fileName' => 'screen 1280 x 720'
        ),
        'slideshow_slide_image_1_1' => array(
            'title' => 'Slideshow - First Slide (480 x 800)',
            'width' => 480,
            'height' => 800,
            'directoryName' => 'Slideshow_Images/Small_Images_Mobile 480 x 800',
            'fileName' => 'first'
        ),
        'slideshow_slide_image_1_2' => array(
            'title' => 'Slideshow - First Slide (720 x 1280)',
            'width' => 720,
            'height' => 1280,
            'directoryName' => 'Slideshow_Images/Big_Images_Tablet 720 x 1280',
            'fileName' => 'first'
        ),
        'slideshow_slide_image_2_1' => array(
            'title' => 'Slideshow - Second Slide (480 x 800)',
            'width' => 480,
            'height' => 800,
            'directoryName' => 'Slideshow_Images/Small_Images_Mobile 480 x 800',
            'fileName' => 'second'
        ),
        'slideshow_slide_image_2_2' => array(
            'title' => 'Slideshow - Second Slide (720 x 1280)',
            'width' => 720,
            'height' => 1280,
            'directoryName' => 'Slideshow_Images/Big_Images_Tablet 720 x 1280',
            'fileName' => 'second'
        ),
        'slideshow_slide_image_3_1' => array(
            'title' => 'Slideshow - Third Slide (480 x 800)',
            'width' => 480,
            'height' => 800,
            'directoryName' => 'Slideshow_Images/Small_Images_Mobile 480 x 800',
            'fileName' => 'third'
        ),
        'slideshow_slide_image_3_2' => array(
            'title' => 'Slideshow - Third Slide (720 x 1280)',
            'width' => 720,
            'height' => 1280,
            'directoryName' => 'Slideshow_Images/Big_Images_Tablet 720 x 1280',
            'fileName' => 'third'
        ),
    );

    public function init() {
        $getWebsiteName = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
        $websiteStr = str_replace(".", "-", $getWebsiteName);
        $this->_directoryName = 'android-' . $websiteStr . '-app-builder';
        $this->_getAPPBuilderTarFileName = $this->_directoryName . '.tar';
        $this->_getAPPBuilderBaseURL = 'public/' . $this->_directoryName;
        $tempImageArray = $this->_getAPPImageDetailsForUpload;
        unset($tempImageArray['slideshow_slide_image_2_1']);
        unset($tempImageArray['slideshow_slide_image_2_2']);
        unset($tempImageArray['slideshow_slide_image_3_1']);
        unset($tempImageArray['slideshow_slide_image_3_2']);
        $enabledTab = $this->_getParam('tab', 1);
        if (($enabledTab != 3) && ($enabledTab != 6)) {
            unset($tempImageArray['slideshow_slide_image_1_1']);
            unset($tempImageArray['slideshow_slide_image_1_2']);
        }
        $tempImageArray = array_keys($tempImageArray);
        $this->_requiredFormImages = $tempImageArray;

        // Set default language path
        $this->_languagePath = APPLICATION_PATH . '/application/languages';
    }

    public function indexAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('siteandroidapp_admin_main', array(), 'siteandroidapp_admin_app_create');
    }

    public function editGoogleAdModulesAction() {
        $key = $this->_getParam('key');
        $getAPPBuilderParentPath = APPLICATION_PATH . DIRECTORY_SEPARATOR
                . $this->_getAPPBuilderBaseURL;

        $getAPPBuilderSettingsFilePath = $getAPPBuilderParentPath . DIRECTORY_SEPARATOR
                . $this->_getAPPBuilderSettingsFileName;

        if (@file_exists($getAPPBuilderSettingsFilePath))
            include $getAPPBuilderSettingsFilePath;

        $this->view->form = $form = new Siteandroidapp_Form_Admin_AppBuilder_GoogleAds_Edit();

        if (isset($appBuilderParams) && !empty($appBuilderParams) && isset($appBuilderParams['Ads'][$key]) && !empty($appBuilderParams['Ads'][$key]))
            $form->populate($appBuilderParams['Ads'][$key]);

        if ($this->getRequest()->isPost()) {
            $formError = array();
            $values = $this->getRequest()->getPost();

            if (!empty($values)) {
                unset($values['submit']);
                $values['enabled_google_ad'] = (boolean) $values['enabled_google_ad'];
                $values['google_ad_show_after'] = (int) $values['google_ad_show_after'];
                $appBuilderParams['Ads'][$key] = $values;
            }

            if (!empty($appBuilderParams)) {
                $file_contents = "<?php ";
                $file_contents .= '$appBuilderParams = ';
                $file_contents .= var_export($appBuilderParams, true);
                $file_contents .= " ?>";

                @file_put_contents($getAPPBuilderSettingsFilePath, $file_contents);
                $successMessage = 'Your changes have been saved successfully. Now please download the "tar file" and follow the given instructions, to get started with your APP creation.';
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 300,
                'parentRefresh' => 300,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('successfully saved'))
            ));
        }
    }

    public function statusAction() {
        $key = $this->_getParam('key');
        $getAPPBuilderParentPath = APPLICATION_PATH . DIRECTORY_SEPARATOR
                . $this->_getAPPBuilderBaseURL;

        $getAPPBuilderSettingsFilePath = $getAPPBuilderParentPath . DIRECTORY_SEPARATOR
                . $this->_getAPPBuilderSettingsFileName;

        if (@file_exists($getAPPBuilderSettingsFilePath))
            include $getAPPBuilderSettingsFilePath;

        if (isset($appBuilderParams) && !empty($appBuilderParams) && isset($appBuilderParams['Ads'][$key]) && !empty($appBuilderParams['Ads'][$key])) {
            $appBuilderParams['Ads'][$key]['enabled_google_ad'] = !$appBuilderParams['Ads'][$key]['enabled_google_ad'];
        } else {
            $appBuilderParams['Ads'][$key]['google_ad_show_after'] = 8;
            $appBuilderParams['Ads'][$key]['enabled_google_ad'] = true;
        }

        if (!empty($appBuilderParams)) {
            $file_contents = "<?php ";
            $file_contents .= '$appBuilderParams = ';
            $file_contents .= var_export($appBuilderParams, true);
            $file_contents .= " ?>";

            @file_put_contents($getAPPBuilderSettingsFilePath, $file_contents);
            $successMessage = 'Your changes have been saved successfully. Now please download the "tar file" and follow the given instructions, to get started with your APP creation.';
        }

        $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => true,
            'parentRefresh' => true,
        ));
    }

    public function languageEditAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('siteandroidapp_admin_main', array(), 'siteandroidapp_admin_app_create');
        $this->view->subnavigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('siteandroidapp_admin_appsetup_main', array(), $this->_enabledTabsArray[4]);

        $this->view->isFileExist = false;
        $this->view->locale = $locale = $this->_getParam('locale');
        $this->view->page = $page = $this->_getParam('page');
        $show = $this->_getParam('show', null);
        $search = $this->_getParam('search', null);
        $translate = Zend_Registry::get('Zend_Translate');
        
        // Get existing language array.
        $getAPPLanguageDetailsForUpload = $this->_getAPPLanguageDetailsForUpload();
        
        $defaultTargetStringURL = APPLICATION_PATH . DIRECTORY_SEPARATOR . $this->_getAPPBuilderBaseURL . DIRECTORY_SEPARATOR . $getAPPLanguageDetailsForUpload[$locale]['directoryName'] . '/' . $getAPPLanguageDetailsForUpload[$locale]['fileName'] . '.xml';        

        // Convert CSV to XML
        if (isset($getAPPLanguageDetailsForUpload[$locale]) &&
                isset($getAPPLanguageDetailsForUpload[$locale]['fileName']) &&
                file_exists(APPLICATION_PATH . DIRECTORY_SEPARATOR . $this->_getAPPBuilderBaseURL . DIRECTORY_SEPARATOR . $getAPPLanguageDetailsForUpload[$locale]['directoryName'] . '/' . $getAPPLanguageDetailsForUpload[$locale]['fileName'] . '.csv') &&
                !file_exists($defaultTargetStringURL)) {
            $this->_modifyExistingLanguageXML(APPLICATION_PATH . DIRECTORY_SEPARATOR . $this->_getAPPBuilderBaseURL . DIRECTORY_SEPARATOR . $getAPPLanguageDetailsForUpload[$locale]['directoryName'] . '/' . $getAPPLanguageDetailsForUpload[$locale]['fileName'] . '.csv', $defaultTargetStringURL);
        }

        if (isset($getAPPLanguageDetailsForUpload[$locale]) &&
                isset($getAPPLanguageDetailsForUpload[$locale]['fileName']) &&
                @file_exists($defaultTargetStringURL)) {
            $this->view->isFileExist = true;
            @chmod($defaultTargetStringURL, 0777);
            
            // If exist xml file is corrupted then show error message.
            $getSimpleXMLLoadFile = simplexml_load_file($defaultTargetStringURL);
            if(empty($getSimpleXMLLoadFile)) {
                $this->view->taregtFileCorrupted = $defaultTargetStringURL;
                return;
            }
            
            $getExistingLanguageArray = $this->_ExplodeXMLToArray($defaultTargetStringURL);
        }

        // Get default language array.
        if (@file_exists(APPLICATION_PATH . DIRECTORY_SEPARATOR . '/application/modules/Siteandroidapp/settings/defaultstrings.xml')) {

            $getDefaultLanguageArray = $this->_ExplodeXMLToArray(APPLICATION_PATH . DIRECTORY_SEPARATOR . '/application/modules/Siteandroidapp/settings/defaultstrings.xml');
        }

        // Process filter form
        $this->view->filterForm = $filterForm = new Core_Form_Admin_Language_Filter();
        $filterForm->isValid($this->_getAllParams());
        $filterValues = $filterForm->getValues();
        extract($filterValues); // search, show
        // Make query
        $filterValues = array_filter($filterValues);
        $this->view->values = $filterValues;
        $this->view->query = ( empty($filterValues) ? '' : '?' . http_build_query($filterValues) );

        // Query plural system for max and sample space
        $sample = array();
        $max = 0;
        for ($i = 0; $i <= 1000; $i++) {
            $form = Zend_Translate_Plural::getPlural($i, $locale);
            $max = max($max, $form);
            if (@count($sample[$form]) < 3) {
                $sample[$form][] = $i;
            }
        }
        $pluralFormCount = ( $max + 1 );
        $this->view->pluralFormSample = $sample;

        // Build the fancy array
        $baseMessages = $getDefaultLanguageArray;
        $resultantMessages = array();
        $missing = 0;
        $index = 0;
        $missingLanguageCount = 0;
        foreach ($baseMessages as $key => $value) {
            // Build
            $composite = array(
                'uid' => ++$index,
                'key' => $key,
                'original' => $value
            );

            if (!empty($getExistingLanguageArray) &&
                    !isset($getExistingLanguageArray[$key]))
                $missingLanguageCount++;


            if (($show === 'missing') &&
                    !empty($getExistingLanguageArray) &&
                    isset($getExistingLanguageArray[$key]))
                continue;

            if ($show === 'translated' &&
                    !isset($getExistingLanguageArray[$key]))
                continue;

            $composite['plural'] = $isPlural = (bool) is_array($value);

            $composite['current'] = (!empty($getExistingLanguageArray) && isset($getExistingLanguageArray[$key]) && !empty($getExistingLanguageArray[$key])) ? $getExistingLanguageArray[$key] : $value;

            $composite['pluralFormCount'] = !empty($isPlural) ? COUNT($value) : 2;

//            // filters, plurals, and missing, oh my.
//            if (isset($currentMessages[$key])) {
//                if ('missing' == $show) {
//                    continue;
//                }
//                if (is_array($value) && !is_array($currentMessages[$key])) {
//                    $composite['current'] = array($currentMessages[$key]);
//                } else if (!is_array($value) && is_array($currentMessages[$key])) {
//                    $composite['current'] = current($currentMessages[$key]);
//                } else {
//                    $composite['current'] = $currentMessages[$key];
//                }
//            } else {
//                if ('translated' == $show) {
//                    continue;
//                }
//                if (is_array($value)) {
//                    $composite['current'] = array();
//                } else {
//                    $composite['current'] = '';
//                }
//                $missing++;
//            }
            // Do search
            if ($search && !$this->_searchArrayRecursive($search, $composite)) {
                continue;
            }
            // Add
            $resultantMessages[] = $composite;
        }

        $this->view->missingLanguageCount = $missingLanguageCount;
        
        // Build the paginator
        $this->view->paginator = $paginator = Zend_Paginator::factory($resultantMessages);
        $paginator->setItemCountPerPage(50);
        $paginator->setCurrentPageNumber($page);


        // Process form POST
        if ($this->getRequest()->isPost()) {
            $keys = $this->_getParam('keys');
            $values = $this->_getParam('values');

            $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . $this->_getAPPBuilderBaseURL . DIRECTORY_SEPARATOR . $getAPPLanguageDetailsForUpload[$locale]['directoryName'] . '/' . $getAPPLanguageDetailsForUpload[$locale]['fileName'] . '.xml';
            if (isset($getAPPLanguageDetailsForUpload[$locale]) &&
                    isset($getAPPLanguageDetailsForUpload[$locale]['fileName']) &&
                    !file_exists($path)) {
                if (!copy(APPLICATION_PATH . DIRECTORY_SEPARATOR . '/application/modules/Siteandroidapp/settings/defaultstrings.xml', $path)) {
                    // @Todo: Nood to set error on frontend.
//                    echo "Not able to take backup of created Android APP.";
//                    die;
                }
            }

//            if (!file_exists($path)) {
//                // @Todo: Nood to set error on frontend.    
//                return;
//            }

            /*
             * 1) Check if language available then overright the value. [Singular and Plural]
             * 
             * 2) If language not available then need to add that language in the bottom of the file. [Singular and Plural]
             */


            $explodeXMLToArray = $this->_ExplodeXMLToArray($path, 1);
            $valuesArray = $explodeXMLToArray['values'];
            $pluralsKeyArray = $this->_ExplodeXMLToArray(APPLICATION_PATH . DIRECTORY_SEPARATOR . '/application/modules/Siteandroidapp/settings/defaultstrings.xml', 1);
            $pluralsKeyArray = $pluralsKeyArray['pluralsKey'];
            foreach ($keys as $key => $value) {
                // For Singular
                if (!is_array($value))
                    $valuesArray[$value] = $values[$key];
                else
                    $valuesArray[$value[0]] = $values[$key];
            }

            // Set the plurals key.
            foreach ($valuesArray as $key => $value) {
                if (is_array($value)) {
                    $tempValuesArray = array();
                    foreach ($value as $tempKey => $tempValue) {
                        if (isset($pluralsKeyArray[$key][$tempKey]) && !empty($pluralsKeyArray[$key][$tempKey]))
                            $tempValuesArray[$pluralsKeyArray[$key][$tempKey]] = $tempValue;
                    }

                    if (!empty($tempValuesArray))
                        $valuesArray[$key] = $tempValuesArray;
                }
            }


            // Set the languages
            $this->_setArrayToXML($path, $valuesArray);
            return $this->_redirect($this->view->url(array('module' => 'siteandroidapp', 'controller' => 'app-builder', 'action' => 'create', 'tab' => 4), 'admin_default', false), array('prependBase' => false));


//            @chmod($path, 0777);
//            $myfile = @fopen($path, "r") or die("Unable to open " . $path . " file!");
//            // Output one line until end-of-file
//            while (!feof($myfile)) {
//                $rowContent = @fgets($myfile);
//                if (strstr($rowContent, $search)) {
//                    $tempArray[] = $value;
//                } else {
//                    if (!empty($rowContent))
//                        $tempArray[] = $rowContent;
//                }
//            }
//            @fclose($myfile);
//            // Try to write to a file
//            $targetFile = APPLICATION_PATH . '/application/languages/' . $locale . '/custom.csv';
//            if (!file_exists($targetFile)) {
//                touch($targetFile);
//                chmod($targetFile, 0777);
//            }
//
//            $writer = new Engine_Translate_Writer_Csv($targetFile);
//            $writer->setTranslations($combined);
//            $writer->write();
//
//            // flush cached language vars
//            @Zend_Registry::get('Zend_Cache')->clean();
//
//            // redirect to this same page to get the new values
//            return $this->_redirect($_SERVER['REQUEST_URI'], array('prependBase' => false));
        }
    }

    public function languageIndexAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('siteandroidapp_admin_main', array(), 'siteandroidapp_admin_app_create');

        $enabledTab = $this->_getParam('tab', 1);
        $this->view->doWeHaveLatestVersion = $this->_getParam('doWeHaveLatestVersion', '');
        $this->view->subnavigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('siteandroidapp_admin_appsetup_main', array(), $this->_enabledTabsArray[$enabledTab]);

        $translate = Zend_Registry::get('Zend_Translate');

        // Prepare language list
        $this->view->languageList = $languageList = $translate->getList();



        //Get available language file.
        foreach ($this->_getAPPLanguageDetailsForUpload() as $key => $values) {
            @chmod(APPLICATION_PATH . DIRECTORY_SEPARATOR . $this->_getAPPBuilderBaseURL . DIRECTORY_SEPARATOR . $values['directoryName'] . '/' . $values['fileName'] . '.xml', 0777);
            if (@file_exists(APPLICATION_PATH . DIRECTORY_SEPARATOR . $this->_getAPPBuilderBaseURL . DIRECTORY_SEPARATOR . $values['directoryName'] . '/' . $values['fileName'] . '.xml'))
                $getDefaultAvailableLanguages[$key] = $values;
            elseif (@file_exists(APPLICATION_PATH . DIRECTORY_SEPARATOR . $this->_getAPPBuilderBaseURL . DIRECTORY_SEPARATOR . $values['directoryName'] . '/' . $values['fileName'] . '.csv'))
                    $getDefaultAvailableCSVLanguages[$key] = $values;                            
        }
        $this->view->getAPPLanguageDetailsForUpload = !empty($getDefaultAvailableLanguages) ? $getDefaultAvailableLanguages : array();
        $this->view->getAPPCSVLanguageDetailsForUpload = !empty($getDefaultAvailableCSVLanguages) ? $getDefaultAvailableCSVLanguages : array();

        // Prepare default langauge
        $defaultLanguage = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.locale', 'en');
        if (!in_array($defaultLanguage, $languageList)) {
            if ($defaultLanguage == 'auto' && isset($languageList['en'])) {
                $defaultLanguage = 'en';
            } else {
                $defaultLanguage = null;
            }
        }
        $this->view->defaultLanguage = $defaultLanguage;

        // Init default locale
        $localeObject = Zend_Registry::get('Locale');

        $languages = Zend_Locale::getTranslationList('language', $localeObject);
        $territories = Zend_Locale::getTranslationList('territory', $localeObject);

        $localeMultiOptions = array();
        foreach (/* array_keys(Zend_Locale::getLocaleList()) */ $languageList as $key) {
            $languageName = null;
            if (!empty($languages[$key])) {
                $languageName = $languages[$key];
            } else {
                $tmpLocale = new Zend_Locale($key);
                $region = $tmpLocale->getRegion();
                $language = $tmpLocale->getLanguage();
                if (!empty($languages[$language]) && !empty($territories[$region])) {
                    $languageName = $languages[$language] . ' (' . $territories[$region] . ')';
                }
            }

            if ($languageName) {
                $localeMultiOptions[$key] = $languageName . ' [' . $key . ']';
            } else {
                $localeMultiOptions[$key] = $this->view->translate('Unknown') . ' [' . $key . ']';
            }
        }
        $localeMultiOptions = array_merge(array(
            $defaultLanguage => $defaultLanguage
                ), $localeMultiOptions);
        $this->view->languageNameList = $localeMultiOptions;
    }

    public function languageDeleteAction() {
        $this->_helper->layout->setLayout('admin-simple');
        $this->view->locale = $defaultLocale = $this->_getParam('locale');
        $languageList = Zend_Locale_Data::getList('en', 'language');
        $territoryList = Zend_Locale_Data::getList('en', 'territory');
        $localeCode = $this->_getParam('locale', null);
        if (empty($localeCode))
            return;
        if (FALSE !== strpos($localeCode, '_')) {
            list($locale, $territory) = explode('_', $localeCode);
        } else {
            $locale = $localeCode;
            $territory = null;
        }

        $languagePack = $languageList[$locale];
        if ($territory && !empty($territoryList[$territory]))
            $languagePack .= " ({$territoryList[$territory]})";
        $languagePack .= "  [$localeCode]";
        $this->view->languagePack = $languagePack;
        
        if ($this->getRequest()->isPost()) {
            // Delete existing string file.
            if (!empty($localeCode) && file_exists(APPLICATION_PATH . DIRECTORY_SEPARATOR . $this->_getAPPBuilderBaseURL . DIRECTORY_SEPARATOR . 'Languages_App/' . $defaultLocale . '_language_android_mobileapp.xml'))
                    @unlink(APPLICATION_PATH . DIRECTORY_SEPARATOR . $this->_getAPPBuilderBaseURL . DIRECTORY_SEPARATOR . 'Languages_App/' . $defaultLocale . '_language_android_mobileapp.xml');
            
            // Delete existing csv file.
            if (!empty($localeCode) && file_exists(APPLICATION_PATH . DIRECTORY_SEPARATOR . $this->_getAPPBuilderBaseURL . DIRECTORY_SEPARATOR . 'Languages_App/' . $defaultLocale . '_language_android_mobileapp.csv'))
                    @unlink(APPLICATION_PATH . DIRECTORY_SEPARATOR . $this->_getAPPBuilderBaseURL . DIRECTORY_SEPARATOR . 'Languages_App/' . $defaultLocale . '_language_android_mobileapp.csv');                

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('Deleted Successfully!'))
            ));
        }
    }

    public function createAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('siteandroidapp_admin_main', array(), 'siteandroidapp_admin_app_create');

        $enabledTab = $this->_getParam('tab', 1);
        $this->view->subnavigation = $navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('siteandroidapp_admin_appsetup_main', array(), $this->_enabledTabsArray[$enabledTab]);

        $this->view->doWeHaveLatestVersion = $doWeHaveLatestVersion = $this->_doWeHaveLatestVersion();

        // Forward for language work.
        if ($enabledTab == 4) {
            return $this->_forward('language-index', 'admin-app-builder', 'siteandroidapp', array(
                        'tab' => $enabledTab,
                        'doWeHaveLatestVersion' => $doWeHaveLatestVersion
            ));
        }

        if ($enabledTab == 5) {            
            $multiOptions = array();
            $modules = Engine_Api::_()->getDbtable('modules', 'core')->getEnabledModuleNames();
            $enabledModules = array_intersect($this->_EnabledModules, $modules);
            foreach ($enabledModules as $module) {
                $multiOptions[$module] = Engine_Api::_()->getDbTable('integrated', 'seaocore')->getModuleTitle($module);
            }

            $this->view->getEnabledModules = $multiOptions;
        }

        $dontShowDownloadLink = false;
        $downloadTar = $this->_getParam('downloadTar', false);
        if (isset($downloadTar) && !empty($downloadTar))
            $this->view->downloadTar = true;

        // To check maximum file upload size.
        $upload_max_filesize = (int) @str_replace('M', '', @ini_get("upload_max_filesize"));
        if (isset($upload_max_filesize) &&
                !empty($upload_max_filesize) &&
                isset($this->_tarFileSize) &&
                !empty($this->_tarFileSize) &&
                $this->_tarFileSize > $upload_max_filesize
        ) {
            $this->view->showDownloadTip = true;
            $this->view->tarFileSize = $this->_tarFileSize;
            $this->view->upload_max_filesize = $upload_max_filesize;
        }

        $this->view->package = $package = $this->_getParam('package', null);
        $clientId = $this->_getParam('clientId', null);
        $clientEmail = $this->_getParam('email', null);

        // Validate subscription plane.
        if (empty($package)) {
            $getAvailableSubscriptionPlane = $this->validateSubscriptionPlane();
            if (is_array($getAvailableSubscriptionPlane)) {
                $this->view->getUserInfo = $getUserInfo = $getAvailableSubscriptionPlane[0];
                if (COUNT($getUserInfo['subscriptionPlane']) == 1) {
                    $this->_helper->redirector->gotoRoute(array(
                        'package' => $getUserInfo['subscriptionPlane'][0],
                        'clientId' => $getUserInfo['clientId'],
                        'email' => $getUserInfo['email']
                    ));
                    return;
                }
            } else if (strstr($getAvailableSubscriptionPlane, 'error')) {
                $this->view->errorMessage = @str_replace("error::", "", $getAvailableSubscriptionPlane);
            }
        }
        // Set the app builder settings file url.
        $getAPPBuilderParentPath = APPLICATION_PATH . DIRECTORY_SEPARATOR
                . $this->_getAPPBuilderBaseURL;

        $getAPPBuilderSettingsFilePath = $getAPPBuilderParentPath . DIRECTORY_SEPARATOR
                . $this->_getAPPBuilderSettingsFileName;

        if (@file_exists($getAPPBuilderSettingsFilePath))
            include $getAPPBuilderSettingsFilePath;

        $this->view->form = $form = new Siteandroidapp_Form_Admin_AppBuilder_Settings(array('doWeHaveLatestVersion' => $doWeHaveLatestVersion, 'enabledTabName' => $this->_enabledTabsArray[$enabledTab]));

        // Populate form
        if (!empty($_POST)) {
            $form->populate($_POST);
        } elseif (isset($appBuilderParams) && !empty($appBuilderParams)) {
            $this->view->appBuilderParams = $appBuilderParams;
            $form->populate($appBuilderParams);
        }

        // Set description for uploaded images.
        foreach ($this->_getAPPImageDetailsForUpload as $key => $values) {
            if (@file_exists(APPLICATION_PATH . DIRECTORY_SEPARATOR . $this->_getAPPBuilderBaseURL . DIRECTORY_SEPARATOR . $values['directoryName'] . '/' . $values['fileName'] . '.png')) {
//                if (empty($package) || (($package == 'starter') && ($key === 'hi_res_icon')))
//                    continue;

                $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
                $baseUrl = @trim($baseUrl, "/");
                $getHost = _ENGINE_SSL ? 'https://' . $_SERVER['HTTP_HOST'] . DIRECTORY_SEPARATOR . $baseUrl : 'http://' . $_SERVER['HTTP_HOST'] . DIRECTORY_SEPARATOR . $baseUrl;
                $getHost = @trim($getHost, "/");
                $getHost = str_replace("index.php/", "", $getHost);

                $filePath = $getHost . DIRECTORY_SEPARATOR
                        . $this->_getAPPBuilderBaseURL . DIRECTORY_SEPARATOR
                        . $values['directoryName'] . DIRECTORY_SEPARATOR
                        . $values['fileName']
                        . '.png';

                $tempWidth = $values['width'];
                $tempHeight = $values['height'];
                if (($values['width'] > 500)) {
                    $tempWidth = $values['width'] - 300;
                    $tempHeight = $values['height'] - 300;
                }
                $description = "To check uploaded image. Please <a href='" . $filePath . "' target='_blank'>click here</a>.";
                if (isset($form->$key)) {
//                    if(($key != 'feature_graphics') && ($key != 'hi_res_icon'))
                    $form->$key->setDescription($description);

                    $form->$key->getDecorator('Description')->setOptions(array('placement' => 'PREPEND', 'escape' => false));
                }
            } else if (in_array($key, $this->_requiredFormImages)) {
                $dontShowDownloadLink = true;
            }
        }

        if (($package == 'pro') && !isset($_POST['provide_google_play_account_details']) && !empty($_POST['provide_google_play_account_details'])) {
            $requireFieldsPro = array(
                'google_play_login_email',
                'google_play_login_password',
            );
            $this->_requiredFormFields = array_merge($requireFieldsPro, $this->_requiredFormFields);
        }

        if (empty($dontShowDownloadLink)) {
            foreach ($this->_requiredFormFields as $element) {
                if (!isset($appBuilderParams[$element])) {
                    $dontShowDownloadLink = true;
                    break;
                }
            }
        }

        $this->view->requiredFormFields = array_merge($this->_requiredFormFields, $this->_requiredFormImages);

        if (!empty($dontShowDownloadLink)) {
            if (isset($form->download_text))
                $form->download_text->setLabel('<div class="seaocore_tip"><span>If you are unable to see "Download .tar" link, this means you have not filled all the form sections completely. Please check all the form sections and complete them as required.</span></div>');

            if (isset($form->download_tar))
                $form->removeElement('download_tar');
        }


        $errors = array();

        if ($this->getRequest()->isPost()) {

            $formError = array();
            $values = $this->getRequest()->getPost();
            if (!empty($appBuilderParams))
                $values = array_merge($appBuilderParams, $values);

            foreach ($this->_requiredFormFields as $element) {
                if (!isset($form->$element))
                    continue;
//                if (!array_key_exists($element, $values))
//                    $formError[] = $form->$element->getLabel() . " - Field  is required";
//            }
                if (in_array($element, array('siteapi_validate_ssl'))) {
                    if (!isset($values[$element]))
                        $formError[] = $form->$element->getLabel() . " - Field  is required";
                }else if (empty($values[$element]))
                    $formError[] = $form->$element->getLabel() . " - Field  is required";

                if ($element == 'email_contact_details' && !empty($values[$element])) {

                    // Validate emails
                    $validate = new Zend_Validate_EmailAddress();

                    if (!$validate->isValid(trim($values[$element]))) {
                        $formError[] = $form->$element->getLabel() . " - Enter a valid Email Id";
                    }
                }

                if ($element == 'title' && !empty($values[$element])) {
                    $strLen = Engine_String::strlen($values[$element]);
                    $maxLength = 30;
                    if ($strLen >= $maxLength) {
                        $formError[] = $form->$element->getLabel() . " - Title should be less than 30 single-byte characters";
                    }
                }

                if ($element == 'short_description' && !empty($values[$element])) {
                    $strLen = Engine_String::strlen($values[$element]);
                    $maxLength = 80;
                    if ($strLen >= $maxLength) {
                        $formError[] = $form->$element->getLabel() . " - Title should be less than 80 single-byte characters";
                    }
                }

                if ($element == 'description' && !empty($values[$element])) {
                    $strLen = Engine_String::strlen($values[$element]);
                    $maxLength = 4000;
                    if ($strLen >= $maxLength) {
                        $formError[] = $form->$element->getLabel() . " - Description should be less than 4000 single-byte characters";
                    }
                }
            }
            if (($package == 'pro') && empty($values['provide_google_play_account_details']) && empty($values['gmail_permission_done'])) {
                $formError[] = 'Give permission to access Google Play Developer account.';
            } else if (($package == 'pro') && !empty($values['provide_google_play_account_details']) && empty($values['google_play_login_email']) && empty($values['google_play_login_password'])) {
//                $formError[] = 'Enter `Google Play Login Email` and `Google Play Login Password` to access Google Play Developer account.';
            }

            if (($package == 'pro') &&
                    !empty($values['provide_google_play_account_details']) &&
                    !empty($values['google_play_login_email'])
            ) {
                // Validate emails
                $validate = new Zend_Validate_EmailAddress();

                if (!$validate->isValid(trim($values['google_play_login_email']))) {
                    $formError[] = "Enter a valid `Google Play Login Email`";
                }
            }


            if (isset($values['package_name']) && !empty($values['package_name'])) {
                $explodeArray = explode(".", $values['package_name']);
                if (COUNT($explodeArray) != 3) {
                    $formError[] = 'Please enter the correct App Package Name.';
                }
            }

            if (isset($values['siteapi_validate_ssl']) && !empty($values['siteapi_validate_ssl']) && !_ENGINE_SSL)
                $formError[] = 'You have chosed `I am running my website on https` but there are no SSL available, please change your option OR enable the SSL.';

            if (isset($values['facebook_app_id']) && !empty($values['facebook_app_id'])) {
                $isSocialDNAEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('socialdna');
                if (!empty($isSocialDNAEnabled)) {
                    $values['facebook_app_id'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('socialdna.facebook_api_key', null);
                    $values['facebook_app_secret'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('socialdna.facebook_secret', null);
                } else {
                    $values['facebook_app_id'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.facebook.appid', null);
                    $values['facebook_app_secret'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.facebook.secret', null);
                }

                if (empty($values['facebook_app_id']) || empty($values['facebook_app_secret']))
                    $formError[] = 'Facebook Api or Secret not found. Please configure it from "Facebook Settings" page.';
            }else {
                $values['facebook_app_secret'] = $values['facebook_app_id'] = "";
            }

            if (isset($values['twitter_app_id']) && !empty($values['twitter_app_id'])) {
                $values['twitter_app_id'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.twitter.key', null);
                $values['twitter_app_secret'] = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.twitter.secret', null);

                if (empty($values['twitter_app_id']) || empty($values['twitter_app_secret']))
                    $formError[] = 'Twitter Api or Secret not found. Please configure it from "Twitter Settings" page.';
            }else {
                $values['twitter_app_id'] = $values['twitter_app_secret'] = "";
            }

            $this->createAPPBuilderDefaultDirectory();

            if (!empty($package))
                $values['package'] = $package;

            if (!empty($clientId))
                $values['clientId'] = $clientId;

            if (!empty($clientEmail))
                $values['email'] = $clientEmail;

            $getHost = _ENGINE_SSL ? 'https://' . $_SERVER['HTTP_HOST'] : 'http://' . $_SERVER['HTTP_HOST'];
            $siteHost = $this->view->url(array(), 'home', false);
            $values['api_calling_url'] = $getHost . $siteHost . 'api/rest/';

            // Validate consumer key
            $consumerTable = Engine_Api::_()->getDbtable('consumers', 'siteapi');
            $select = $consumerTable->getSelect(array('key' => $values['api_consumer_key'], 'secret' => $values['api_consumer_secret']));
            $getRow = $consumerTable->fetchRow($select);
            if (empty($getRow))
                $formError[] = 'Please enter correct `API Consumer Key` and `API Consumer Secret`.';

            if (!empty($formError))
                return $form->addErrors($formError);

            try {
                // Set the values in settings file.
                if ((is_file($getAPPBuilderSettingsFilePath) && is_writable($getAPPBuilderSettingsFilePath)) ||
                        (is_dir(dirname($getAPPBuilderSettingsFilePath)) && is_writable(dirname($getAPPBuilderSettingsFilePath)))) {
                    if (!empty($values)) {
                        $file_contents = "<?php ";
                        $file_contents .= '$appBuilderParams = ';
                        $file_contents .= var_export($values, true);
                        $file_contents .= " ?>";
                        @file_put_contents($getAPPBuilderSettingsFilePath, $file_contents);
                        $successMessage = 'Your changes have been saved successfully. Now please download the "tar file" and follow the given instructions, to get started with your APP creation.';
                    }
                } else {
                    return $form->addError('Unable to configure this setting due to the file ' . $getAPPBuilderSettingsFilePath . ' not having the correct permissions. Please CHMOD (change the permissions of) that file to 666, then try again.');
                }

                // Upload the selected image
                foreach ($this->_getAPPImageDetailsForUpload as $key => $values) {
                    // If image not uploaded by siteadministrator then check, is that image already uploaded? IF not then through error message.
                    if (empty($_FILES[$key]["tmp_name"])) {
                        if (@file_exists(APPLICATION_PATH . DIRECTORY_SEPARATOR . $this->_getAPPBuilderBaseURL . DIRECTORY_SEPARATOR . $values['directoryName'] . '/' . $values['fileName'] . '.png'))
                            continue;

                        if (($enabledTab != 2) && ($enabledTab != 3))
                            continue;

                        if (($enabledTab != 3) && (in_array($key, array('slideshow_slide_image_1_1', 'slideshow_slide_image_1_2'))))
                            continue;

                        if (in_array($key, array('slideshow_slide_image_2_1', 'slideshow_slide_image_2_2', 'slideshow_slide_image_3_1', 'slideshow_slide_image_3_2')))
                            continue;

                        if (empty($package) || (($package == 'starter') && ($key === 'hi_res_icon')))
                            continue;


                        $errors[] = $values['title'] . ': Image not found.';
                        continue;
                    }

                    // In case: IF image uploaded by siteadministrator.
                    if (isset($_FILES[$key]) && !empty($_FILES[$key])) {
                        // Validate: file type
                        $check = @getimagesize($_FILES[$key]["tmp_name"]);
                        if ($check === false) {
                            $errors[] = $values['title'] . ': File is not an image.';
                            continue;
                        }

                        // Validate: image extension
                        $target_file = @basename($_FILES[$key]["name"]);
                        $imageFileType = @pathinfo($target_file, PATHINFO_EXTENSION);
                        if ($imageFileType != "png") {
                            $errors[] = $values['title'] . ': Sorry, only PNG files are allowed.';
                            continue;
                        }

                        // Validate: image width and height
                        if (($check[0] !== $values['width']) && ($check[1] !== $values['height'])) {
                            $errors[] = $values['title'] . ': Please upload ' . $values['width'] . ' x ' . $values['height'] . ' pixels image.';
                            continue;
                        }

                        // Validate: authrization permission
                        $isImageUpload = @move_uploaded_file($_FILES[$key]["tmp_name"], APPLICATION_PATH . DIRECTORY_SEPARATOR . $this->_getAPPBuilderBaseURL . DIRECTORY_SEPARATOR . $values['directoryName'] . '/' . $values['fileName'] . '.' . $imageFileType);
                        if (empty($isImageUpload)) {
                            $errors[] = $values['title'] . ': Not able to upload image. Please give 777 permission to /public/' . $this->_directoryName . ' directory.';
                            continue;
                        }
                    }
                }

                // Details for language File to be uploaded
                $getLanguagesFiles = $this->_getAPPLanguageDetailsForUpload();

                // Upload the selected language file
                foreach ($getLanguagesFiles as $key => $values) {

                    // In case: IF language file is uploaded by siteadministrator.
                    if (isset($_FILES[$key]["tmp_name"]) && !empty($_FILES[$key]["tmp_name"])) {

                        // Validate: language file extension
                        $target_file = @basename($_FILES[$key]["name"]);
                        $languageFileType = @pathinfo($target_file, PATHINFO_EXTENSION);

//                        if ($languageFileType != 'csv') {
//                            $errors[] = $values['title'] . ': Sorry, only CSV files are allowed.';
//                            continue;
//                        }

                        $isLanguageUpload = @move_uploaded_file($_FILES[$key]["tmp_name"], APPLICATION_PATH . DIRECTORY_SEPARATOR . $this->_getAPPBuilderBaseURL . DIRECTORY_SEPARATOR . $values['directoryName'] . '/' . $values['fileName'] . '.' . $languageFileType);

                        if (empty($isLanguageUpload)) {
                            $errors[] = $values['title'] . ': Not able to upload language file. Please give 777 permission to /public/' . $this->_directoryName . ' directory.';
                            continue;
                        }
                    }
                }
            } catch (Exception $ex) {
                throw $ex;
            }

            // Display errors
            if (!empty($errors)) {
                foreach ($errors as $errors) {
                    $form->addError($errors);
                }

                return;
            } else {
                if ($enabledTab < 6) {
                    $this->_helper->redirector->gotoRoute(array('tab' => ++$enabledTab));
                    return;
                }
            }
        }

        if (!empty($successMessage))
            $form->addNotice($successMessage);
    }

    public function downloadAction() {
        $this->view->package = $this->_getParam('package', false);
        $this->view->clientId = $this->_getParam('clientId', false);
        $this->view->email = $this->_getParam('email', false);

        if (!$_POST)
            return;

        $this->view->redirectToCreateForm = true;
    }

    public function downloadTarAction() {
        if (!class_exists('DOMDocument', false))
            return $form->addError('Bad PHP version.');

        $createZipSuccessfully = $this->createZip(APPLICATION_PATH . '/public/' . $this->_directoryName . '/');

        if (!empty($createZipSuccessfully)) {
            $path = $tarFilePath = APPLICATION_PATH . DIRECTORY_SEPARATOR
                    . 'public' . DIRECTORY_SEPARATOR
                    . $this->_getAPPBuilderTarFileName;

            if (file_exists($path) && is_file($path)) {
                // Kill zend's ob
                while (ob_get_level() > 0) {
                    ob_end_clean();
                }

                $this->_tarFileSize = filesize($path);
                $this->_tarFileSize = $this->_formatBytes($this->_tarFileSize);
                $this->_tarFileSize = (int) @ceil($this->_tarFileSize);

                header("Content-Disposition: attachment; filename=" . urlencode(basename($path)), true);
                header("Content-Transfer-Encoding: Binary", true);
                header("Content-Type: application/force-download", true);
                header("Content-Type: application/octet-stream", true);
                header("Content-Type: application/download", true);
                header("Content-Description: File Transfer", true);
                header("Content-Length: " . filesize($path), true);
                flush();

                $fp = fopen($path, "r");
                while (!feof($fp)) {
                    echo fread($fp, 65536);
                    flush();
                }
                fclose($fp);
            }
            exit();
        }
    }

    /*
     * creates a compressed tar file  
     */

    protected function createZip() {
        $uploadToTar = APPLICATION_PATH . DIRECTORY_SEPARATOR
                . $this->_getAPPBuilderBaseURL . DIRECTORY_SEPARATOR;

        $tarFileParentPath = APPLICATION_PATH . DIRECTORY_SEPARATOR
                . 'public' . DIRECTORY_SEPARATOR;

        $tarFilePath = $tarFileParentPath . $this->_getAPPBuilderTarFileName;

        // Create the root directory, If not exist.        
        $is_dir = @is_dir($tarFileParentPath);
        if (empty($is_dir))
            @mkdir($tarFileParentPath);

        @chmod($tarFileParentPath, 0777);
        @chmod($uploadToTar, 0777);
        @chmod($tarFilePath, 0777);

        // Create the archive
        $zip = new Archive_Tar($tarFilePath);

        // Remove uploaded tar file first.
        @unlink($tarFilePath);

        $getArray = APPLICATION_PATH . '/public/' . $this->_directoryName . '/';

        // Add content into tar file.
        $zip->addModify(array($uploadToTar), '', APPLICATION_PATH . '/public/');
        @chmod($tarFilePath, 0777);

        //check to make sure the file exists
        return file_exists($tarFilePath);
    }

    /*
     * End point to validate website authorization.
     */

    protected function validateSubscriptionPlane() {
        $apiURL = 'https://www.socialengineaddons.com/check-subscriptions-endpoint';
        $website = str_replace('www.', '', strtolower($_SERVER['HTTP_HOST']));
        $tempAdminMenuPost = array(
            'websites' => serialize(
                    array(
                        $website
                    )
            )
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiURL);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $tempAdminMenuPost);
        ob_start();
        curl_exec($ch);
        curl_close($ch);
        $getCurlResponse = ob_get_contents();
        ob_end_clean();

        if (strstr($getCurlResponse, 'error::'))
            return $getCurlResponse;

        $getCurlResponse = unserialize($getCurlResponse);

        $getResult = $enabledPlan = array();
        if (isset($getCurlResponse[$website])) {
            $getWebsitePlans = $getCurlResponse[$website];
            foreach ($getWebsitePlans as $planObj) {

                if ($planObj->title == 'Mobile Pro Plan')
                    $enabledPlan[] = 'pro';

                if ($planObj->title == 'Mobile Starter Plan')
                    $enabledPlan[] = 'starter';


                if (empty($getResult) && (($planObj->title == 'Mobile Pro Plan') || ($planObj->title == 'Mobile Starter Plan'))) {
                    $getResult['clientId'] = $planObj->uid;
                    $getResult['email'] = $planObj->mail;
                }
            }

            if (!empty($getResult) && !empty($enabledPlan))
                $getResult['subscriptionPlane'] = $enabledPlan;
        }

        if (!empty($getResult))
            return array($getResult);
        else
            return 'error::There are problem to get your subscription plan record. Please configure your Mobile Apps Subscription Plan from the <a href="https://www.socialengineaddons.com/page/steps-follow-before-filing-support-ticket-mobile-apps-setup" target="_blank">"Subscriptions / Recurring Fees"</a> section of your SocialEngineAddOns Client Area. You can login from here to configure your subscription. If you still have any problem then contact to SocialEngineAddOns support team.';
    }

    /*
     * Create blank directory in case, if not exist on server.
     */

    protected function createAPPBuilderDefaultDirectory() {
        $getAPPBuilderParentPath = APPLICATION_PATH . DIRECTORY_SEPARATOR
                . $this->_getAPPBuilderBaseURL;

        if (!@is_dir($getAPPBuilderParentPath))
            @mkdir($getAPPBuilderParentPath);

        @chmod($getAPPBuilderParentPath, 0777);


        if (!@is_dir($getAPPBuilderParentPath . DIRECTORY_SEPARATOR . 'App_Icons')) {
            @mkdir($getAPPBuilderParentPath . DIRECTORY_SEPARATOR . 'App_Icons');
            @chmod($getAPPBuilderParentPath . DIRECTORY_SEPARATOR . 'App_Icons', 0777);
        }

        if (!@is_dir($getAPPBuilderParentPath . DIRECTORY_SEPARATOR . 'Hi-res_Icon')) {
            @mkdir($getAPPBuilderParentPath . DIRECTORY_SEPARATOR . 'Hi-res_Icon');
            @chmod($getAPPBuilderParentPath . DIRECTORY_SEPARATOR . 'Hi-res_Icon', 0777);
        }

        if (!@is_dir($getAPPBuilderParentPath . DIRECTORY_SEPARATOR . 'Slideshow_Images')) {
            @mkdir($getAPPBuilderParentPath . DIRECTORY_SEPARATOR . 'Slideshow_Images');
            @chmod($getAPPBuilderParentPath . DIRECTORY_SEPARATOR . 'Slideshow_Images', 0777);
        }

        if (!@is_dir($getAPPBuilderParentPath . DIRECTORY_SEPARATOR . 'Slideshow_Images' . DIRECTORY_SEPARATOR . 'Big_Images_Tablet 720 x 1280')) {
            @mkdir($getAPPBuilderParentPath . DIRECTORY_SEPARATOR . 'Slideshow_Images' . DIRECTORY_SEPARATOR . 'Big_Images_Tablet 720 x 1280');
            @chmod($getAPPBuilderParentPath . DIRECTORY_SEPARATOR . 'Slideshow_Images' . DIRECTORY_SEPARATOR . 'Big_Images_Tablet 720 x 1280', 0777);
        }

        if (!@is_dir($getAPPBuilderParentPath . DIRECTORY_SEPARATOR . 'Slideshow_Images' . DIRECTORY_SEPARATOR . 'Small_Images_Mobile 480 x 800')) {
            @mkdir($getAPPBuilderParentPath . DIRECTORY_SEPARATOR . 'Slideshow_Images' . DIRECTORY_SEPARATOR . 'Small_Images_Mobile 480 x 800');
            @chmod($getAPPBuilderParentPath . DIRECTORY_SEPARATOR . 'Slideshow_Images' . DIRECTORY_SEPARATOR . 'Small_Images_Mobile 480 x 800', 0777);
        }

        if (!@is_dir($getAPPBuilderParentPath . DIRECTORY_SEPARATOR . 'Splash_Screen_Images')) {
            @mkdir($getAPPBuilderParentPath . DIRECTORY_SEPARATOR . 'Splash_Screen_Images');
            @chmod($getAPPBuilderParentPath . DIRECTORY_SEPARATOR . 'Splash_Screen_Images', 0777);
        }

        if (!@is_dir($getAPPBuilderParentPath . DIRECTORY_SEPARATOR . 'Splash_Screen_Images' . DIRECTORY_SEPARATOR . 'Landscape')) {
            @mkdir($getAPPBuilderParentPath . DIRECTORY_SEPARATOR . 'Splash_Screen_Images' . DIRECTORY_SEPARATOR . 'Landscape');
            @chmod($getAPPBuilderParentPath . DIRECTORY_SEPARATOR . 'Splash_Screen_Images' . DIRECTORY_SEPARATOR . 'Landscape', 0777);
        }

        if (!@is_dir($getAPPBuilderParentPath . DIRECTORY_SEPARATOR . 'Splash_Screen_Images' . DIRECTORY_SEPARATOR . 'Portrait')) {
            @mkdir($getAPPBuilderParentPath . DIRECTORY_SEPARATOR . 'Splash_Screen_Images' . DIRECTORY_SEPARATOR . 'Portrait');
            @chmod($getAPPBuilderParentPath . DIRECTORY_SEPARATOR . 'Splash_Screen_Images' . DIRECTORY_SEPARATOR . 'Portrait', 0777);
        }

        if (!@is_dir($getAPPBuilderParentPath . DIRECTORY_SEPARATOR . 'Languages_App')) {
            @mkdir($getAPPBuilderParentPath . DIRECTORY_SEPARATOR . 'Languages_App');
            @chmod($getAPPBuilderParentPath . DIRECTORY_SEPARATOR . 'Languages_App', 0777);
        }

        // Create settings file.
        $ourFileName = $getAPPBuilderParentPath . DIRECTORY_SEPARATOR . "settings.php";
        if (!is_file($ourFileName)) {
            $ourFileHandle = @fopen($ourFileName, 'w');
            @fclose($ourFileHandle);
            @chmod($ourFileName, 0777);
        }


        return;
    }
    
    /*
     * Set array to xml
     */
    
    protected function _setArrayToXML($path, $values) {        
        $explodeDefaultXMLToArray = $this->_ExplodeXMLToArray(APPLICATION_PATH . DIRECTORY_SEPARATOR . '/application/modules/Siteandroidapp/settings/defaultstrings.xml');
        
        $content = '<resources>';
        $flagStringArray = array();
        foreach ($values as $key => $value) {
            // Key's if not exist in newly added "defaultString.xml" file then not include it now.
            if(!array_key_exists($key, $explodeDefaultXMLToArray))
                    continue;
            
            if (!is_array($value)) {
                $flagStringArray[$key] = '<string name="' . $key . '">' . $this->_validateString($value) . '</string>';
            } else {
                $tempPluralStr = '';
                foreach ($value as $tempKey => $tempValue) {
                    $tempPluralStr .= '<item quantity="' . $tempKey . '">' . $this->_validateString($tempValue) . '</item>';
                }
                $tempPluralStr = @trim($tempPluralStr);
                $flagStringArray[$key] = '<plurals name="' . $key . '">' . $tempPluralStr . '</plurals>';
            }
        }

        $content .= @implode("\n", $flagStringArray);
        $content .= '</resources>';

        // Remove unwanted space and blank lines from the string.
        $content = @preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $content);
        $content = str_replace("><", ">\n<", $content);

        if (@file_exists($path)) {
            @chmod($path, 0777);
            if (is_writable($path)) {
                if (!empty($content)) {
                    $fh = fopen($path, 'w') or die("can't open file");
                    @fwrite($fh, $content);
                    @fclose($fh);
                }
            }
        }

        return;
    }

    /*
     * Get default available languages.
     */

    private function _getAPPLanguageDetailsForUpload() {
        $getLanguages = Engine_Api::_()->getApi('Core', 'siteapi')->getLanguages(true);
        if (isset($getLanguages)) {
            $languageArray = array();
            foreach ($getLanguages as $key => $label) {
                $languageArray[$key] = array(
                    'title' => 'Upload Language File For: [' . $label . ']',
                    'directoryName' => 'Languages_App',
                    'fileName' => $key . '_language_android_mobileapp'
                );
            }
        }

        return $languageArray;
    }

    private function _formatBytes($size, $precision = 2) {
        $base = log($size, 1024);
        $suffixes = array('');

        return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
    }

    /*
     * Listed modules should be in latest version
     */
    
    private function _doWeHaveLatestVersion() {
        $errorInPlugins = array();
        $pluginsArray = array(
            'seaocore' => 'seaddons-core',
            'siteapi' => 'siteapi',
            'siteiosapp' => 'sitemobileiosapp',
            'siteandroidapp' => 'sitemobileandroidapp',
            'advancedactivity' => 'advancedactivity',
            'sitetagcheckin' => 'sitetagcheckin'
        );

        if ($_COOKIE['getMobileDependentModulesList']) {
            $tempValues = unserialize($_COOKIE['getMobileDependentModulesList']);
        } else {
            $rss = Zend_Feed::import('http://www.socialengineaddons.com/plugins/feed');
            $channel = array(
                'title' => $rss->title(),
                'link' => $rss->link(),
                'description' => $rss->description(),
                'items' => array()
            );
            $tempValues = $flagArray = array();
            foreach ($rss as $item) {
                if (in_array($item->ptype(), $pluginsArray)) {
                    $flagArray['productType'] = $item->ptype();
                    $flagArray['availableVersion'] = $item->version();
                    $flagArray['title'] = $item->title();
                    $tempValues[] = $flagArray;
                }
            }

            $cookieData = @serialize($tempValues);
            setcookie("getMobileDependentModulesList", $cookieData, time() + 86400);
        }
        foreach ($tempValues as $values) {
            if (in_array($values['productType'], $pluginsArray)) {
                $flipedArray = array_flip($pluginsArray);
                if (!Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled($flipedArray[$values['productType']]))
                    continue;

                $getModVersion = Engine_Api::_()->getDbtable('modules', 'core')->getModule($flipedArray[$values['productType']]);
                $isModSupport = $this->_checkVersion($getModVersion->version, $values['availableVersion']);
                if (!empty($isModSupport)) {
                    $errorInPlugins[] = $values['title'];
                }
            }
        }

        return $errorInPlugins;
    }

    /*
     * Calling from _doWeHaveLatestVersion()
     */
    
    private function _checkVersion($databaseVersion, $checkDependancyVersion) {
        $running_version = $databaseVersion;
        $product_version = $checkDependancyVersion;
        $shouldUpgrade = false;
        if (!empty($running_version) && !empty($product_version)) {
            $temp_running_verion_2 = $temp_product_verion_2 = 0;
            if (strstr($product_version, "p")) {
                $temp_starting_product_version_array = @explode("p", $product_version);
                $temp_product_verion_1 = $temp_starting_product_version_array[0];
                $temp_product_verion_2 = $temp_starting_product_version_array[1];
            } else {
                $temp_product_verion_1 = $product_version;
            }
            $temp_product_verion_1 = @str_replace(".", "", $temp_product_verion_1);


            if (strstr($running_version, "p")) {
                $temp_starting_running_version_array = @explode("p", $running_version);
                $temp_running_verion_1 = $temp_starting_running_version_array[0];
                $temp_running_verion_2 = $temp_starting_running_version_array[1];
            } else {
                $temp_running_verion_1 = $running_version;
            }
            $temp_running_verion_1 = @str_replace(".", "", $temp_running_verion_1);


            if (($temp_running_verion_1 < $temp_product_verion_1) || (($temp_running_verion_1 == $temp_product_verion_1) && ($temp_running_verion_2 < $temp_product_verion_2))) {
                $shouldUpgrade = true;
            }
        }
        return $shouldUpgrade;
    }
    
    /*
     * Validate strings, while make the .xml file for Android App languages.
     */
    
    private function _validateString($string) {
        $stringArray = array();
        $tempStr = '';
        $breakString = @str_split($string);
        $restrictedWordArray = array(34, 35, 42, 47, 58, 59, 60, 61, 62, 91, 93, 94, 95, 96, 123, 124, 125, 126, 127);
        foreach ($breakString as $word) {
            // Convert to ASCII
            $getASCIINumber = @ord($word);
            if (!in_array($getASCIINumber, $restrictedWordArray))
                $stringArray[] = $getASCIINumber;
        }

        // Convert ASCII to String
        foreach ($stringArray as $ASCII) {
            $tempStr .= @chr($ASCII);
        }

        $tempStr = str_replace("\'", "'", $tempStr);
        $tempStr = str_replace("'", "\'", $tempStr);
        
        $tempStr = @htmlspecialchars($tempStr, ENT_QUOTES);
        return trim($tempStr);
    }

    /*
     * Explode existing xml file to array
     */
    
    private function _ExplodeXMLToArray($path, $getPluralKeys = false) {
        @chmod($path, 0777);
        $xml = @simplexml_load_file($path);

        // Make an array of all singular values.
        foreach ($xml->string as $item)
            $setResponseArray[(string) $item['name']] = (string) $item;

        // Make an array of all plurals values.
        foreach ($xml->plurals as $item) {
            $pluralKeysArray = $pluralItemArray = array();
            foreach ($item->item as $pluralItem) {
                $pluralItemArray[] = (string) $pluralItem;
                if (!empty($getPluralKeys))
                    $pluralKeysArray[] = (string) $pluralItem['quantity'];
            }

            $setResponseArray[(string) $item['name']] = $pluralItemArray;
            if (!empty($getPluralKeys))
                $setResponsePluralKeysArray[(string) $item['name']] = $pluralKeysArray;
        }

        if (empty($getPluralKeys))
            return $setResponseArray;

        return array(
            'values' => $setResponseArray,
            'pluralsKey' => $setResponsePluralKeysArray
        );
    }

    /*
     * Function to convert csv file to Xml File
     */

    private function _modifyExistingLanguageXML($fromFilePath, $destinationPath = null) {
        @chmod($fromFilePath, 0777);
        @chmod($destinationPath, 0777);

        if (@file_exists($fromFilePath)) {            
            if (($handle = @fopen($fromFilePath, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 100000, ";")) !== FALSE) {
                    $count = COUNT($data);
                                       
                    // Check for comments, blank line and empty string
                    if (!isset($data[0]) || empty($data[0]) || $count < 2 || $data[0] == '#')
                        continue;
                    
                    if(array_key_exists($data[0], $dataArray) && !in_array($data[0], array(
                        'view_count',
                        'play_count'
                        )))
                            continue;

                    if ($count == 2)
                        $dataArray[$data[0]] = (string) $data[1]; //htmlspecialchars($data[1], ENT_QUOTES);
                    
                    if($count == 3) {
                        $dataArray[$data[0]] = array(
                          "one" => (string) $data[1], //htmlspecialchars($data[1], ENT_QUOTES),
                          "other" => (string) $data[2] //htmlspecialchars($data[2], ENT_QUOTES)
                        );
                    }
                    
                    if($count == 4) {
                        $dataArray[$data[0]] = array(
                          "zero" => (string) $data[1], //htmlspecialchars($data[1], ENT_QUOTES),
                          "one" => (string) $data[2], //htmlspecialchars($data[2], ENT_QUOTES),
                          "other" => (string) $data[3] // htmlspecialchars($data[3], ENT_QUOTES)
                        );
                    }
                }
            }
            
            // Ceate destination file, If not exist.
            if(!file_exists($destinationPath)) {
                @fopen($destinationPath, "w");
                @fclose($myfile);
                @chmod($destinationPath, 0777);
            }
            
            // Write in newly created xml file.
            if(!empty($dataArray))
                $this->_setArrayToXML($destinationPath, $dataArray);
            
            @chmod($destinationPath, 0777);
        }
        
        return;
    }

}
