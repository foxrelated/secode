<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitetagcheckin
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Edit.php 2012-08-20 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Sitetagcheckin_Form_Admin_Content_Edit extends Sitetagcheckin_Form_Admin_Content_Add {

  public function init() {

    parent::init();

    //EDIT HEADING
    $this
            ->setTitle('Edit Module for Check-in')
            ->setDescription('Use the form below to configure content from a module of your site to enable users to check-in into its content from its view page. For the chosen content module, enter the various database table related field names. In case of doubts regarding any field name, please contact the developer of that content module.');

    //DISABLE SOME ELEMENTS
    $this->getElement('module')
            ->setAttrib('disable', true)
            ->clearValidators()
            ->setRequired(false)
            ->setAllowEmpty(true);

    $this->getElement('resource_type')
            ->setAttrib('disable', true)
            ->clearValidators()
            ->setRequired(false)
            ->setAllowEmpty(true);
  }

}