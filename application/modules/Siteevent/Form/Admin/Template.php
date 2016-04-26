<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Global.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Form_Admin_Template extends Engine_Form {

    public function init() {

        $this->setTitle('Layout Template Settings')
                ->setDescription('Below, you can choose layouts for selected important pages of this plugin.')
                ->setAttrib('name', 'template');

        $coreSettings = Engine_Api::_()->getApi('settings', 'core');

        $defaultHome = "Template 1 (Default)
    " . '<a href="http://demo.socialengineaddons.com/events" title="View Template" class="seaocore_icon_demo mleft5" target="_blank"></a> | <a href="https://lh3.googleusercontent.com/-7_MgPiVmP30/UuzEuGN8FvI/AAAAAAAAA0E/mQVWKVjfqH8/w463-h770-no/eventshome_default.jpg" title="View Screenshot" class="seaocore_icon_view" target="_blank"></a>';

        $template1Home = "Template 2
    " . '<a href="http://demo.socialengineaddons.com/pages/events-home-template2" title="View Template" class="seaocore_icon_demo mleft5" target="_blank"></a> | <a href="https://lh5.googleusercontent.com/-LkSJKv5eOz0/UuzEvjaqL9I/AAAAAAAAA00/DuDycq7ClXo/w330-h771-no/eventshome_template2.jpg" title="View Screenshot" class="seaocore_icon_view" target="_blank"></a>';

        $this->addElement('Radio', 'siteevent_hometemplate', array(
            'label' => 'Events Home Page',
            'description' => 'Choose from below the template for Events Home page of your site.',
            'multiOptions' => array(
                'default' => $defaultHome,
                'template2' => $template1Home,
            ),
            'escape' => false,
            'value' => $coreSettings->getSetting('siteevent.hometemplate', 'default'),
        ));

        $defaultProfile = "2 Columns - Template 1 (Default)
    " . '<a href="http://demo.socialengineaddons.com/event/dance-party/26/218" title="View Template" class="seaocore_icon_demo mleft5" target="_blank"></a> | <a href="https://lh6.googleusercontent.com/-iIJMdzdA6UY/UuzEq5wtrrI/AAAAAAAAAzc/yr2vQyTTIOI/w546-h764-no/eventprofile_default.jpg" title="View Screenshot" class="seaocore_icon_view" target="_blank"></a>';

        $template2Profile = "2 Columns - Template 2
    " . '<a href="http://demo.socialengineaddons.com/event/dance-party/26/218?otherView=tmp22" title="View Template" class="seaocore_icon_demo mleft5" target="_blank"></a> | <a href="https://lh4.googleusercontent.com/-cbzv96_GIlo/UuzErNnQEkI/AAAAAAAAAzo/zTDb4Bgto4Q/w573-h771-no/eventprofile_template2.jpg" title="View Screenshot" class="seaocore_icon_view" target="_blank"></a>';

        $template3Profile = "2 Columns - Template 3
    " . '<a href="http://demo.socialengineaddons.com/event/dance-party/26/218?otherView=tmp23" title="View Template" class="seaocore_icon_demo mleft5" target="_blank"></a> | <a href="https://lh3.googleusercontent.com/-zoNcRakM1jI/UuzErGjEoAI/AAAAAAAAAzk/jQSTYvRp0_Y/w446-h771-no/eventprofile_template3.jpg" title="View Screenshot" class="seaocore_icon_view" target="_blank"></a>';

        $template4Profile = "2 Columns - Template 4
    " . '<a href="http://demo.socialengineaddons.com/event/dance-party/26/218?otherView=tmp24" title="View Template" class="seaocore_icon_demo mleft5" target="_blank"></a> | <a href="https://lh4.googleusercontent.com/-GJWD7qcuLpk/UuzErxmXqbI/AAAAAAAAAzw/ipZozXe96NE/w377-h771-no/eventprofile_template4.jpg" title="View Screenshot" class="seaocore_icon_view" target="_blank"></a>';

        $template5Profile = "3 Columns - Template 5
    " . '<a href="http://demo.socialengineaddons.com/event/dance-party/26/218?otherView=tmp31" title="View Template" class="seaocore_icon_demo mleft5" target="_blank"></a> | <a href="https://lh4.googleusercontent.com/-UhYGMSYOgTs/UuzErxX-YvI/AAAAAAAAAz0/cGDT5Vh8ayY/w703-h771-no/eventprofile_template5.jpg" title="View Screenshot" class="seaocore_icon_view" target="_blank"></a>';

        $template6Profile = "3 Columns - Template 6
    " . '<a href="http://demo.socialengineaddons.com/event/dance-party/26/218?otherView=tmp32" title="View Template" class="seaocore_icon_demo mleft5" target="_blank"></a> | <a href="https://lh3.googleusercontent.com/-hBf-60Izgy8/UuzEtZuMpGI/AAAAAAAAAz8/WSRJ_jJDfTQ/w407-h771-no/eventprofile_template6.jpg" title="View Screenshot" class="seaocore_icon_view" target="_blank"></a>';

        $this->addElement('Radio', 'siteevent_profiletemplate', array(
            'label' => 'Event Profile Page',
            'description' => 'Choose from below the template for Event Profile page of your site.',
            'multiOptions' => array(
                'default' => $defaultProfile,
                'template2' => $template2Profile,
                'template3' => $template3Profile,
                'template4' => $template4Profile,
                'template5' => $template5Profile,
                'template6' => $template6Profile,
            ),
            'escape' => false,
            'value' => $coreSettings->getSetting('siteevent.profiletemplate', 'default'),
        ));

        $this->addElement('Button', 'save', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            //'onclick' => 'confirmSubmit()',
            'ignore' => true
        ));
    }

}