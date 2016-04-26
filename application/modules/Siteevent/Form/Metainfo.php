<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Metainfo.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Form_Metainfo extends Engine_Form {

    public function init() {

        $siteevent = Engine_Api::_()->getItem('siteevent_event', Zend_Controller_Front::getInstance()->getRequest()->getParam('event_id', null));

        $this->setTitle('Meta Keywords')
                ->setDescription("Meta keywords are a great way to provide search engines with information about your event so that search engines populate your event in search results. Below, you can add meta keywords for this event. (The tags entered by you for this event will also be added to the meta keywords.)")
                ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
                ->setAttrib('name', 'metainfo');

        $this->addElement('Textarea', 'keywords', array(
            'label' => 'Meta Keywords',
            'description' => 'Separate meta tags with commas.',
        ));

        $this->keywords->getDecorator('Description')->setOption('placement', 'append');
        $this->addElement('Button', 'submit', array(
            'label' => 'Save Details',
            'type' => 'submit',
            'ignore' => true,
        ));
    }

}