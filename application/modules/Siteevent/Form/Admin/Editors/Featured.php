<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Featured.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Form_Admin_Editors_Featured extends Engine_Form {

    public function init() {

        $this->setMethod('post');
        $this->setTitle('Featured Editor')
                ->setDescription('Displays a featured editor selected by admin.');

        //VALUE FOR BORDER COLOR.
        $this->addElement('Text', 'editor_title', array(
            'label' => 'Editor',
            'decorators' => array(array('ViewScript', array(
                        'viewScript' => '/application/modules/Siteevent/views/scripts/admin-editors/add-featured-editor.tpl',
                        'class' => 'form element')))
        ));
        $this->addElement('Hidden', 'user_id', array());
    }

}