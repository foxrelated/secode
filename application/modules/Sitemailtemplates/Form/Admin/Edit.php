<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemailtemplates
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Edit.php 6590 2012-06-20 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitemailtemplates_Form_Admin_Edit extends Sitemailtemplates_Form_Admin_Create {

  public function init() {
    parent::init();
    $this
				->setTitle('Edit Template')
				->setDescription("Edit your template settings below, then click 'Save Changes' to save your new settings. You can also send sample emails to yourself to see the template design.");

    $template_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('template_id', null);
    $sitemailtemplate = Engine_Api::_()->getItem('sitemailtemplates_templates', $template_id);
    if($sitemailtemplate->active_template) {

			// Disable some elements
			$this->getElement('active_template')
							->setIgnore(true)
							->setAttrib('disable', true)
							->clearValidators()
							->setRequired(false)
							->setAllowEmpty(true)
			;
    }
  }

}