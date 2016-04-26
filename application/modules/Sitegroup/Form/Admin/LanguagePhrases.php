<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitereview
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Addicon.php 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitegroup_Form_Admin_LanguagePhrases extends Engine_Form {

  protected $_isArray = true;
  protected $_elementsBelongTo = 'language_phrases';

  public function init() {
    $coreSettings = Engine_Api::_()->getApi('settings', 'core');
    
    $isSitegroupActive = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.isActivate', 0);
    
    if (!empty($isSitegroupActive)) {
			$this->clearDecorators()
							->addDecorator('FormElements');
			$elements = Engine_Api::_()->getApi('language', 'sitegroup')->getDataWithoutKeyPhase();
			foreach($elements as $key => $element) {
				$this->addElement('Text', $key, array(
						'label' => "Text for '$element'",
						'value'=> $coreSettings->getSetting( "language.phrases.".str_replace('_',".",$key) ,$element),
				));
			}
    }
  }

}