<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Format.php 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteeventticket_Form_Admin_Settings_Format extends Engine_Form {

    public function init() {

        $this->setTitle('Ticket Formats')
                ->setDescription('Here, you can configure the Ticket Format for your event tickets. Please click on the ‘Save Settings’ button available at the bottom of the form to save your changes else your changes will not get reflected in the tickets.')
                ->setName('siteeventticket_format_settings');
  
        $dompdfEnabled = Engine_Api::_()->hasModuleBootstrap('sitemailtemplates') && file_exists('application/libraries/dompdf/dompdf_config.inc.php');

        // Get available files
        $logoOptions = array('' => 'No logo');
        $imageExtensions = array('gif', 'jpg', 'jpeg', 'png');

        $it = new DirectoryIterator(APPLICATION_PATH . '/public/admin/');
        foreach ($it as $file) {
            if ($file->isDot() || !$file->isFile())
                continue;
            $basename = basename($file->getFilename());
            if (!($pos = strrpos($basename, '.')))
                continue;
            $ext = strtolower(ltrim(substr($basename, $pos), '.'));
            if (!in_array($ext, $imageExtensions))
                continue;
            $logoOptions['public/admin/' . $basename] = $basename;
        }

        $this->addElement('Select', 'siteeventticket_adsimage', array(
            'label' => 'Advertisement Image',
            'description' => 'Select the advertisement image from the dropdown below, which you want with pdf file of event tickets. This image will get printed on your event tickets and sent with the ticket PDF via email to buyers. It can prove to be a source of monetization for you by promoting featured/sponsored events or by publishing 3rd party ads.
<br/>[Note 1: Images shown below are coming from files uploaded on ‘Layout >> Files & Media Manager’ (available in the admin panel of your site). So you need to upload your advertisement image here to make it available in the dropdown.]<br/>[Note 2: Recommended size of the image: Width X Height : 150 px X 100 px] ',
            'multiOptions' => $logoOptions,
            'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.adsimage')
        ));
        $this->siteeventticket_adsimage->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
        
        if($dompdfEnabled) {
            
            $file = APPLICATION_PATH.'/application/libraries/dompdf/lib/fonts/dompdf_font_family_cache.dist.php';
            $fontOptions = array();
            if (file_exists($file) && ($enabledModuleDirectories = include $file)) {

              foreach($enabledModuleDirectories as $key => $enabledModuleDirectorie) {
                  $fontOptions[$key] = $key;
              }
            }

            if(COUNT($fontOptions) > 1) {
                $this->addElement('Select', 'siteeventticket_dompdffont', array(
                    'label' => 'Font Family for Ticket PDF',
                    'multiOptions' => $fontOptions,
                    'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('siteeventticket.dompdffont', 'serif'),
                    'disableTranslator' => 'true'
                ));
            }
        }

        // Element: language
        $this->addElement('Select', 'language', array(
            'label' => 'Language Pack',
            'description' => 'Your community has more than one language pack installed. Please select the language pack you want to edit right now.',
            'onchange' => 'javascript:setLanguage(this.value);',
        ));

        // Languages
        $localeObject = Zend_Registry::get('Locale');
        $translate = Zend_Registry::get('Zend_Translate');
        $languageList = $translate->getList();

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
            }
        }

        $defaultLanguage = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.locale', 'en');
        if (isset($localeMultiOptions[$defaultLanguage])) {
            $localeMultiOptions = array_merge(array(
                $defaultLanguage => $localeMultiOptions[$defaultLanguage],
                    ), $localeMultiOptions);
        }

        $this->language->setMultiOptions($localeMultiOptions);

        //FORMAT EDITOR
        $this->addElement('dummy', 'dummy_text', array('description' => 'Available Placeholders:
[Free], [event_name], [event_date_time],[event_location], [event_venue], [buyer_ticket_id], [ticket_time], [ticket_title], [ticket_price], [user_name], [QR_code_image]'));

        $editorOptions = array(
            'html' => (bool) true,
            'mode' => "exact",
            'forced_root_block' => false,
            'force_p_newlines' => false,
            'elements' => 'bodyhtml',
            'plugins' => array(
                'table', 'fullscreen', 'preview', 'paste',
                'code', 'image', 'textcolor', 'link'
            ),
            'toolbar1' => array(
                'undo', 'redo', 'removeformat', 'pastetext', '|', 'code',
                'image', 'link', 'fullscreen',
                'preview'
        ));


        $this->addElement('TinyMce', 'bodyhtml', array(
            'label' => 'Format Body',
            'allowEmpty' => false,
            'required' => true,
            'description' => 'Please customize your Ticket Format by using below editor.',
            'editorOptions' => $editorOptions,
        ));

        $this->addElement('Button', 'submit', array(
            'type' => 'submit',
            'label' => 'Save Settings',
            'ignore' => true,
            'decorators' => array('ViewHelper')
        ));

        $this->addElement('Button', 'default', array(
            'type' => 'submit',
            'label' => 'Reset to Default',
            'ignore' => true,
            'decorators' => array('ViewHelper')
        ));

        $buttonsArray = array('submit', 'default');

        if ($dompdfEnabled) {
            $this->addElement('Button', 'attachementPreview', array(
                'label' => 'Ticket PDF Preview',
                'onclick' => 'seeAttachementPreview()',
                'ignore' => true,
                'decorators' => array('ViewHelper')
            ));

            $this->addElement('dummy', 'attachementPreviewLoadingImage', array(
                'ignore' => true,
                'decorators' => array(
                    'ViewHelper',
                    array('Label', array('tag' => 'span'))
                ),
            ));

            $buttonsArray = array_merge($buttonsArray, array('attachementPreview', 'attachementPreviewLoadingImage'));
        }

        $this->addDisplayGroup($buttonsArray, 'buttons');
        $this->getDisplayGroup('buttons');
    }

}
