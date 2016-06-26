<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: FooterMenu.php 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

class Sitemenu_Form_FooterMenu extends Engine_Form {

  public function init() {
    $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;    
    $temp_show_in_footer_options = array(
        1 => 'None',
        2 => 'Social Links',
        3 => 'Global Search'
    );

    $siteStoreModule = Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitestore');
    if (!empty($siteStoreModule) && !empty($siteStoreModule->enabled)) {
      $temp_show_in_footer_options[4] = 'Product Search';
    }
    $this->addElement('radio', 'sitemenu_is_language', array(
        'label' => 'Do you want to show language dropdown in footer menu?',
        'multiOptions' => array(
            '1' => 'Yes',
            '0' => 'No',
        ),
        'value' => '1'
    ));

    $this->addElement('radio', 'sitemenu_show_in_footer', array(
        'label' => 'Select the option that you want to show in footer menu.',
        'multiOptions' => $temp_show_in_footer_options,
        'value' => 2
    ));
    
    $this->addElement('Text', 'sitemenu_footer_search_width', array(
        'label' => 'Enter width for searchbox.',
        'value' => 150
    ));
     
     $this->addElement('MultiCheckbox', 'sitemenu_social_links', array(
            'label' => 'Select the social links that you want to be available in this block.',
            'description' => 'This setting will only work, if you have chosen Social Links from the above setting.',
            'multiOptions' => array(
                            "facebooklink" => "Facebook Link",
                            "twitterlink" => "Twitter Link",
                            "pininterestlink" => "Pinterest Link",
                            "youtubelink" => "YouTube Link",
                            "linkedinlink" => "LinkedIn Link"
                        ),
        ));
     
    $tempLogoOptions = array();
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
      $tempLogoOptions['public/admin/' . $basename] = $basename;
    }

    //FOR FACEBOOK SOCIAL LINK
    $this->addElement('Dummy', 'facebook', array(
                  'label' => '1. Facebook'
              )
    );

    $this->addElement('Text', 'facebook_url', array(
            'label' => 'Url',
            'value' => 'http://www.facebook.com/'
        )
    );

    $defaultLogoOptions = array('application/modules/Sitemenu/externals/images/facebook.png' => 'Default Facebook Icon');
    $this->addElement('Select', 'facebook_default_icon', array(
          'label' => 'Default Image Icon',
          'multiOptions' => array_merge($defaultLogoOptions, $tempLogoOptions),
          'value' => '',
      ));

    $defaultLogoOptions = array('application/modules/Sitemenu/externals/images/overfacebook.png' => 'Mouse-over Facebook Icon');
    $this->addElement('Select', 'facebook_hover_icon', array(
            'label' => 'Mouse-over Image Icon',
            'multiOptions' => array_merge($defaultLogoOptions, $tempLogoOptions),
            'value' => ''
        )
    );

    $this->addElement('Text', 'facebook_title', array(
            'label' => 'HTML Title',
            'value' => 'Like us on Facebook'
        )
    );

    //WORK FOR TWITTER SOCIAL LINK
    $this->addElement('Dummy', 'twitter', array(
            'label' => '2. Twitter'
        )
    );

    $this->addElement('Text', 'twitter_url', array(
            'label' => 'Url',
            'value' => 'https://www.twitter.com/'
        )
    );

    $defaultLogoOptions = array('application/modules/Sitemenu/externals/images/twitter.png' => 'Default Twitter Icon');
    $this->addElement('Select', 'twitter_default_icon', array(
            'label' => 'Default Image Icon',
            'multiOptions' => array_merge($defaultLogoOptions, $tempLogoOptions),
            'value' => ''
        )
    );

    $defaultLogoOptions = array('application/modules/Sitemenu/externals/images/overtwitter.png' => 'Mouse-over Twitter Icon');
    $this->addElement('Select', 'twitter_hover_icon', array(
          'label' => 'Mouse-over Image Icon',
          'multiOptions' => array_merge($defaultLogoOptions, $tempLogoOptions),
          'value' => ''
      )
    );

    $this->addElement('Text', 'twitter_title', array(
            'label' => 'HTML Title',
            'value' => 'Follow us on Twitter'
        )
    );
    
    //WORK FOR PININTEREST SOCIAL LINK
    $this->addElement('Dummy', 'pinterest', array(
            'label' => '3. Pinterest'
        )
    );

    $this->addElement('Text', 'pinterest_url', array(
            'label' => 'Url',
            'value' => 'https://www.pinterest.com/'
        )
    );

    $defaultLogoOptions = array('application/modules/Sitemenu/externals/images/pinterest.png' => 'Default Pinterest Icon');
    $this->addElement('Select', 'pinterest_default_icon',array(
            'label' => 'Default Image Icon',
            'multiOptions' => array_merge($defaultLogoOptions, $tempLogoOptions),
            'value' => ''
        )
    );

    $defaultLogoOptions = array('application/modules/Sitemenu/externals/images/overpinterest.png' => 'Mouse-over Pinterest Icon');
    $this->addElement('Select', 'pinterest_hover_icon', array(
            'label' => 'Mouse-over Image Icon',
            'multiOptions' => array_merge($defaultLogoOptions, $tempLogoOptions),
            'value' => ''
        )
    );

    $this->addElement('Text', 'pinterest_title', array(
            'label' => 'HTML Title',
            'value' => 'Pinterest'
        )
    );

    //WORK FOR YOUTUBE SOCIAL LINK
    $this->addElement('Dummy', 'youtube', array(
            'label' => '4. YouTube'
        )
    );

    $this->addElement('Text', 'youtube_url', array(
            'label' => 'Url',
            'value' => 'http://www.youtube.com/'
        )
    );

    $defaultLogoOptions = array('application/modules/Sitemenu/externals/images/youtube.png' => 'Default YouTube Icon');
    $this->addElement('Select', 'youtube_default_icon', array(
            'label' => 'Default Image Icon',
            'multiOptions' => array_merge($defaultLogoOptions, $tempLogoOptions),
            'value' => ''
        )
    );

    $defaultLogoOptions = array('application/modules/Sitemenu/externals/images/overyoutube.png' => 'Mouse-over YouTube Icon');
    $this->addElement('Select', 'youtube_hover_icon', array(
            'label' => 'Mouse-over Image Icon',
            'multiOptions' => array_merge($defaultLogoOptions, $tempLogoOptions),
            'value' => ''
        )
    );

    $this->addElement('Text', 'youtube_title', array(
            'label' => 'HTML Title',
            'value' => 'YouTube'
        )
    );
    
    //WORK FOR LinkedIn SOCIAL LINK
    $this->addElement('Dummy', 'linkedin', array(
            'label' => '4. LinkedIn'
        )
    );

    $this->addElement('Text', 'linkedin_url', array(
            'label' => 'Url',
            'value' => 'https://www.linkedin.com/'
        )
    );

    $defaultLogoOptions = array('application/modules/Sitemenu/externals/images/linkedin.png' => 'Default LinkedIn Icon');
    $this->addElement('Select', 'linkedin_default_icon', array(
            'label' => 'Default Image Icon',
            'multiOptions' => array_merge($defaultLogoOptions, $tempLogoOptions),
            'value' => ''
        )
    );

    $defaultLogoOptions = array('application/modules/Sitemenu/externals/images/overlinkedin.png' => 'Mouse-over LinkedIn Icon');
    $this->addElement('Select', 'linkedin_hover_icon', array(
            'label' => 'Mouse-over Image Icon',
            'multiOptions' => array_merge($defaultLogoOptions, $tempLogoOptions),
            'value' => ''
        )
    );

    $this->addElement('Text', 'linkedin_title', array(
            'label' => 'HTML Title',
            'value' => 'LinkedIn'
        )
    );
  }
}