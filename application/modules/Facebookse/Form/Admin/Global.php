<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 6590 2011-01-06 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Facebookse_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Global Settings')
      ->setDescription("These settings affect all members in your community.");


		$this->addElement('Text', 'facebookse_lsettings', array(
		'label' => 'Enter License key',
		'description' => "Please enter your license key that was provided to you when you purchased this plugin. If you do not know your license key, please contact the Support Team of SocialEngineAddOns from the Support section of your Account Area.(Key Format: XXXXXX-XXXXXX-XXXXXX )",
		'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('facebookse.lsettings'),
		));

	  if( APPLICATION_ENV == 'production' ) {
	    $this->addElement('Checkbox', 'environment_mode', array(
		'label' => 'Your community is currently in "Production Mode". We recommend that you momentarily switch your site to "Development Mode" so that the CSS of this plugin renders fine as soon as the plugin is installed. After completely installing this plugin and visiting few pages of your site, you may again change the System Mode back to "Production Mode" from the Admin Panel Home. (In Production Mode, caching prevents CSS of new plugins to be rendered immediately after installation.)',
		'description' => 'System Mode',
		'value' => 1,
	    ));
	  } else {
	    $this->addElement('Hidden', 'environment_mode', array('order' => 990, 'value' => 0));
	  }

    //CHECK IF THE "PROPERTY" TAG IS ALREADY IN LIBRARY HEADMETA.PHP FILE.
    
    $doctypeHelper = new Zend_View_Helper_Doctype();
    $doctypes = $doctypeHelper->getDoctypes();  
    if(!isset($doctypes['XHTML1_RDFA'])) {
      $this->addElement('Checkbox', 'overwrite_headmeta', array(
        'label' => 'Do you want the HeadMeta View Helper file to be overwritten automatically for the minor addition to it, or do you want to make the change to it manually? (This is required to enable your site to support new Open Graph Meta tags.)',
        'description' => 'HeadMeta View Helper modification',
        'multiOptions'=> array(1 => 'Yes, automatically overwrite the HeadMeta View Helper file.', 0 => 'No, I will manually modify the file. (Click on this radio button to see the minor change that needs to be made.)'),
        'value' => 1,
      ));
    }
    else {
      $this->addElement('Hidden', 'overwrite_headmeta', array('order' => 9990, 'value' => 0));
    }
    // Add submit button
    $this->addElement('Button', 'submit_lsetting', array(
      'label' => 'Activate Your Plugin Now',
      'type' => 'submit',
      'ignore' => true
    ));

    $this->addElement('Text', 'fbadmin_userid', array(
			'label' => "Admin's Facebook User ID",
			'description' => "Please specify the Facebook User ID of the Site Admin.<br /><br /><div class='code'>If you do not know your Facebook User ID, then follow the below step :<br /> 1) Go to your profile and see the URL. The last number in the URL of your profile is your Facebook User ID<br/>2) If you have set a user name for your profile, then enter this username. The last name in the URL of your profile is your Facebook Username.</div> <br />[The Facebook User with this ID will be able to publish Stream Updates on Facebook to the users who have liked the pages on site. There are 2 ways to get to the publishing interface: 1) From your Web page, click Admin Page next to the Like button. 2) From Facebook, click Manage Pages under the Account tab, then click Go To Page next to your page name].",
			'value'=> Engine_Api::_()->getApi('settings', 'core')->getSetting('fbadmin.userid'),
			'required' => true,
			'allowEmpty' => false,
         
		));
		$this->fbadmin_userid->getDecorator('Description')->setOptions(array('placement' => 'append','escape' => false));
     
    
//    $this->addElement('Text', 'fbapp_namespace', array(
//			'label' => "Facebook App Namespace",
//			'description' => "Please specify the Facebook App Namespace you have created by going into the settings section of the Facebook App. From here copy the value of 'Namespace' field and fill here.",
//			'value'=> Engine_Api::_()->getApi('settings', 'core')->getSetting('fbapp.namespace'),
//			'required' => true,
//			'allowEmpty' => false,
//         
//		));

      
    if(!isset($doctypes['XHTML1_RDFA'])) {
        $this->addElement('Radio', 'overwrite_headmeta_active', array(
           'description' => 'Do you want the HeadMeta View Helper file to be overwritten automatically for the minor addition to it, or do you want to make the change to it manually? (This is required to enable your site to support new Open Graph Meta tags.)',
           'label' => 'HeadMeta View Helper modification',
           'multiOptions'=> array(1 => 'Yes, automatically overwrite the HeadMeta View Helper file.', 0 => 'No, I will manually modify the file. (Click on this radio button to see the minor change that needs to be made.)'),
           'value' => 1,
         ));
     
    $this->addElement('Dummy', 'show_manual', array(
      'content' => 'You need to apply a minor change to the HeadMeta View Helper file as described below:<br />OPEN the file: application/libraries/Zend/View/Helper/HeadMeta.php<br /><br />FIND (around line 42):<br /><div class="code">protected $_typeKeys = array("name", "http-equiv");</div><br /><br />REPLACE this with:<br /><div class="code">protected $_typeKeys     = array("name", "http-equiv", "property");</div>',
			
    ));
    } else {
      $this->addElement('Hidden', 'overwrite_headmeta_active', array('order' => 9991, 'value' => 0));
    }

    $language_array = array ('ca_ES' => 'Catalan' , 'cs_CZ' => 'Czech' , 'cy_GB' => 'Welsh' , 'da_DK' => 'Danish' , 'de_DE' => 'German' , 'eu_ES' => 'Basque' , 'en_PI' => 'English (Pirate)' , 'en_UD' => 'English (Upside Down)' , 'ck_US' => 'Cherokee' , 'en_US' => 'English (US)' , 'es_LA' => 'Spanish' , 'es_CL' => 'Spanish (Chile)' , 'es_CO' => 'Spanish (Colombia)' , 'es_ES' => 'Spanish (Spain)' , 'es_MX' => 'Spanish (Mexico)' , 'es_VE' => 'Spanish (Venezuela)' , 'fb_FI' => 'Finnish (test)' , 'fi_FI' => 'Finnish' , 'fr_FR' => 'French (France)' , 'gl_ES' => 'Galician' , 'hu_HU' => 'Hungarian' , 'it_IT' => 'Italian' , 'ja_JP' => 'Japanese' , 'ko_KR' => 'Korean' , 'nb_NO' => 'Norwegian (bokmal)' , 'nn_NO' => 'Norwegian (nynorsk)' , 'nl_NL' => 'Dutch' , 'pl_PL' => 'Polish' , 'pt_BR' => 'Portuguese (Brazil)' , 'pt_PT' => 'Portuguese (Portugal)' , 'ro_RO' => 'Romanian' , 'ru_RU' => 'Russian' , 'sk_SK' => 'Slovak' , 'sl_SI' => 'Slovenian' , 'sv_SE' => 'Swedish' , 'th_TH' => 'Thai' , 'tr_TR' => 'Turkish' , 'ku_TR' => 'Kurdish' , 'zh_CN' => 'Simplified Chinese (China)' , 'zh_HK' => 'Traditional Chinese (Hong Kong)' , 'zh_TW' => 'Traditional Chinese (Taiwan)' , 'fb_LT' => 'Leet Speak' ,  'af_ZA' => 'Afrikaans' , 'sq_AL' => 'Albanian' , 'hy_AM' => 'Armenian' , 'az_AZ' => 'Azeri' , 'be_BY' => 'Belarusian' , 'bn_IN' => 'Bengali' , 'bs_BA' => 'Bosnian' , 'bg_BG' => 'Bulgarian' , 'hr_HR' => 'Croatian' , 'nl_BE' => 'Dutch (België)' , 'en_GB' => 'English (UK)' , 'eo_EO' => 'Esperanto' , 'et_EE' => 'Estonian' , 'fo_FO' => 'Faroese' , 'fr_CA' => 'French (Canada)' , 'ka_GE' => 'Georgian' , 'el_GR' => 'Greek' , 'gu_IN' => 'Gujarati' , 'hi_IN' => 'Hindi' , 'is_IS' => 'Icelandic' , 'id_ID' => 'Indonesian' , 'ga_IE' => 'Irish' , 'jv_ID' => 'Javanese' , 'kn_IN' => 'Kannada' , 'kk_KZ' => 'Kazakh' , 'la_VA' => 'Latin' , 'lv_LV' => 'Latvian' , 'li_NL' => 'Limburgish' , 'lt_LT' => 'Lithuanian' , 'mk_MK' => 'Macedonian' , 'mg_MG' => 'Malagasy' , 'ms_MY' => 'Malay' , 'mt_MT' => 'Maltese' , 'mr_IN' => 'Marathi' , 'mn_MN' => 'Mongolian' , 'ne_NP' => 'Nepali' , 'pa_IN' => 'Punjabi' , 'rm_CH' => 'Romansh' , 'sa_IN' => 'Sanskrit' , 'sr_RS' => 'Serbian' , 'so_SO' => 'Somali' , 'sw_KE' => 'Swahili' , 'tl_PH' => 'Filipino' , 'ta_IN' => 'Tamil' , 'tt_RU' => 'Tatar' , 'te_IN' => 'Telugu' , 'ml_IN' => 'Malayalam' , 'uk_UA' => 'Ukrainian' , 'uz_UZ' => 'Uzbek' , 'vi_VN' => 'Vietnamese' , 'xh_ZA' => 'Xhosa' , 'zu_ZA' => 'Zulu' , 'km_KH' => 'Khmer' , 'tg_TJ' => 'Tajik' , 'ar_AR' => 'Arabic' , 'he_IL' => 'Hebrew' , 'ur_PK' => 'Urdu' , 'fa_IR' => 'Persian' , 'sy_SY' => 'Syriac' , 'yi_DE' => 'Yiddish' , 'gn_PY' => 'Guaraní' , 'qu_PE' => 'Quechua' , 'ay_BO' => 'Aymara' , 'se_NO' => 'Northern Sámi' , 'ps_AF' => 'Pashto' , 'tl_ST' => 'Klingon');
		 asort($language_array);
     $this->addElement('Select', 'fblanguage_id', array(
      'label' => 'Facebook Localization',
       'description' => 'You can choose your language for Facebook integration by selecting one of the below. (This language will apply for various components that come from Facebook such as Like Buttons, Social Plugins, Feed Prompts, etc.)',
      'multiOptions' => $language_array,
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('fblanguage.id', 'en_US'),
       
    ));
     
     
//      $this->addElement('Radio', 'fblike_type', array(
//      'description' => 'How do you want Facebook Likes to function on your website?',
//			'label' => 'FB Like Button',
//      'multiOptions'=> array('default' => 'Normal Facebook Like Buttons (This is generated from the Facebook Like Social Plugin. In this case, the standard Facebook Like Buttons will appear on your site and the feeds published on Facebook from your site for Like actions will be simple feeds without custom actions.)', 'custom' => 'Custom Facebook Like Buttons (In this case, you can customize Facebook Like Buttons on your website with custom appearance / icon and custom action specific to your site’s idea. These custom Facebook Like Buttons will be deeply integrated with Open Graph to publish your site’s custom actions on Facebook. You should choose this for your website if it integrates with Facebook Authentication for seamless Facebook integration experience. These buttons also enable you to publish custom stories on Facebook for user actions.)'),
//      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('fblike.type', 'default'),
//    ));
     
     $this->addElement('Radio', 'scrape_sitepageurl', array(
      'description' => 'Do you want to clear the cache at Facebook side before 24 hours? (Facebook maintains a cache of your data and clears it in every 24 hours, so if any Like is made within this time period, then the URL of the page of Liked content is cached and feed generated at Facebook side will be with the old Open Graph Settings only.)',
			'label' => 'Clear Cache (Scrap Page URL)',
      'multiOptions'=> array(1 => 'Yes', 0 => 'No'),
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('scrape.sitepageurl', 0),
    )); 

    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Settings',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}