<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepageurl
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitepageurl_Form_Admin_Global extends Engine_Form {

  public function init() {
   
    $is_element = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepageurl.is.enable', 0);
    $this->setTitle('Global Settings');
		$this->setDescription('These settings affect all members in your community.');
     if(!empty($is_element)) {
			$this->addElement('Radio', 'sitepage_showurl_column', array(
					'label' => 'Custom Page URL',
					'description' => 'Do you want to enable Page Admins to create their custom Page URL during Page creation? (If enabled, a URL field will be available to users at the time of creating a Page.)',
					'multiOptions' => array(
							1 => 'Yes',
							0 => 'No'
					),
          'onclick' => 'showediturl(this.value)',
					'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.showurl.column', 1),
			));

			$this->addElement('Radio', 'sitepage_edit_url', array(
					'label' => 'Edit Custom Page URL',
					'description' => 'Do you want to enable Page Admins to edit their custom Page URL?',
					'multiOptions' => array(
							1 => 'Yes',
							0 => 'No'
					),
					'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.edit.url', 0),
			));

      $this->addElement('Radio', 'sitepage_change_url', array(
					'label' => 'Automatically Shorten Page URLs',
					'description' => 'Do you want the Page URLs to be shortened upon the number of Likes on them exceeding the Likes limit? (You can choose the Likes limit below. Selecting “Yes” will change the URLs of those Pages from the form: “/pageitem/page_url” to: “/page_url”.)',
					'multiOptions' => array(
							1 => 'Yes',
							0 => 'No'
					),
					'onclick' => 'showurl(this.value)',
					'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.change.url', 1),
			));
			
			$this->addElement('Text', 'sitepage_likelimit_forurlblock', array(
					'label' => 'Likes Limit for Active Short URL',
					'allowEmpty' => false,
					'maxlength' => '3',
					'required' => true,
					'description' => 'Please enter the minimum number of Likes after which Page URLs should be shortened.',
					'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.likelimit.forurlblock', 5),
			));   

      $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
      $this->addElement('Dummy', 'sitepage_manifestUrlP', array(
        'label' => 'Pages URL alternate text for "pageitems"',
        'description' => sprintf(Zend_Registry::get('Zend_Translate')->_('Please %1$sedit%2$s the text which want to display in place of "pageitems" in the URLs of Directory/Pages plugin.'),"<a href='" . $view->baseUrl() . "/admin/sitepage/settings#sitepage_manifestUrlP-label' target='_blank'>","</a>"),
      ));
      $this->getElement('sitepage_manifestUrlP')->getDecorator('Description')->setOptions(array('placement', 'APPEND', 'escape' => false));
      $this->addElement('Dummy', 'sitepage_manifestUrlS', array(
        'label' => 'Pages URL alternate text for "pageitem"',
        'description' => sprintf(Zend_Registry::get('Zend_Translate')->_('Please %1$sedit%2$s the text which want to display in place of "pageitem" in the URLs of Directory/Pages plugin.'),"<a href='" . $view->baseUrl() . "/admin/sitepage/settings#sitepage_manifestUrlS-label' target='_blank'>","</a>"),
      ));
				$this->getElement('sitepage_manifestUrlS')->getDecorator('Description')->setOptions(array('placement',
	'APPEND', 'escape' => false));
      $this->addElement('Button', 'submit', array(
        'label' => 'Save Changes',
        'type' => 'submit',
        'ignore' => true
));
    }
    else {
			$this->addElement('Radio', 'sitepage_change_url', array(
					'label' => 'Automatically Shorten Page URLs',
					'description' => 'Do you want the Page URLs to be shortened upon the number of Likes on them exceeding the Likes limit? (You can choose the Likes limit below. Selecting “Yes” will change the URLs of those Pages from the form: “/pageitem/page_url” to: “/page_url”.)',
					'multiOptions' => array(
							1 => 'Yes',
							0 => 'No'
					),
					'onclick' => 'showurl(this.value)',
					'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.change.url', 1),
			));
			
			$this->addElement('Text', 'sitepage_likelimit_forurlblock', array(
					'label' => 'Likes Limit for Active Short URL',
					'allowEmpty' => false,
					'maxlength' => '3',
					'required' => true,
					'description' => 'Please enter the minimum number of Likes after which Page URLs should be shortened.',
					'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('sitepage.likelimit.forurlblock', 5),
			));
			
			$this->addElement('Button', 'submit', array(
        'label' => 'Proceed to activate Plugin',
        'type' => 'submit',
        'ignore' => true
    ));
    }
  }

}
?>