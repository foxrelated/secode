<?php

/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: RequestController.php 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
//Only for Sitemobile.
class Suggestion_IndexSitemobileController extends Seaocore_Controller_Action_Standard {
    
    //Action for displaying friend request page on suggestions main navigation.
    public function requestAction() {
        //Navigation Display
        Zend_Registry::set('setFixedCreationForm', true);
				Zend_Registry::set('setFixedCreationFormBack', 'back');
				Zend_Registry::set('setFixedCreationHeaderTitle', Zend_Registry::get('Zend_Translate')->_('Friend Requests'));
        Zend_Registry::set('sitemobileNavigationName', 'suggestion_main_app');
        // Render
        $this->_helper->content
                ->setContentName('suggestion_index-sitemobile_request')
                ->setNoRender()
                ->setEnabled();
    }

    //Action for displaying all suggestion widgets on suggestion page.
    public function suggestionsAction() {
    
    //NAVIGATION WORK FOR FOOTER.(DO NOT DISPLAY NAVIGATION IN FOOTER ON VIEW PAGE.)
    if(!Zend_Registry::isRegistered('sitemobileNavigationName')){
    Zend_Registry::set('sitemobileNavigationName','setNoRender');
    }
        // Render
        $this->_helper->content
                ->setNoRender()
                ->setEnabled()
        ;
    }

}
