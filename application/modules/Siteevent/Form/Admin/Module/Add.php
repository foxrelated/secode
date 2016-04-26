<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Add.php 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Siteevent_Form_Admin_Module_Add extends Engine_Form {

    public function init() {

        $this->setTitle('Add New Module')
                ->setDescription('Use the form below to enable users to create, edit, view and perform various actions on events for their content. Start by selecting a content module, and then entering the various database table related field names. In case of doubts regarding any field name, please contact the developer of that content module.');

        $notInclude = array('activity', 'advancedactivity', 'sitealbum', 'sitecontentcoverphoto', 'sitepageoffer', 'sitepagebadge', 'featuredcontent', 'sitepagediscussion', 'sitepagelikebox', 'mobi', 'advancedslideshow', 'birthday', 'birthdayemail', 'communityad', 'dbbackup', 'facebookse', 'facebooksefeed', 'facebooksepage', 'feedback', 'groupdocument', 'grouppoll', 'mapprofiletypelevel', 'mcard', 'poke', 'sitealbum', 'sitepageinvite', 'siteslideshow', 'socialengineaddon', 'seaocore', 'suggestion', 'userconnection', 'sitepageform', 'sitepageadmincontact', 'sitebusinessbadge', 'sitebusinessoffer', 'sitebusinessdiscussion', 'sitebusinesslikebox', 'sitebusinessinvite', 'sitebusinessform', 'sitebusinessadmincontact', 'sitetagcheckin', 'sitereviewlistingtype', 'sitegroupoffer', 'sitepageintegration', 'sitebusinessintegration', 'sitegroupintegration', 'sitepagemember', 'sitebusinessmember', 'sitegroupmember', 'sitemailtemplates', 'sitepageurl', 'sitestoreadmincontact', 'sitestorealbum', 'sitestoreform', '
sitestoreinvite', 'sitestorelikebox', 'sitestoreoffer', 'sitestoreproduct', 'sitestorereview', 'sitestoreurl', 'sitestorevideo', 'communityad', 'communityadsponsored', 'sitelike', 'sitestorelikebox', 'sitemobile', 'siteusercoverphoto', 'siteevent', 'eventdocument', 'sitecoupon', 'sitefaq', 'sitegroupadmincontact', 'sitegroupbadge', 'sitegroupdiscussion', 'sitegroupform', 'sitegroupinvite', 'sitegrouplikebox', 'sitegroupurl', 'sitevideoview', 'sitebusinessurl', 'sitestoreinvite', 'nestedcomment', 'sitemobileapp', 'album', 'blog', 'document', 'event', 'forum', 'poll', 'video', 'list', 'group', 'music', 'recipe', 'user', 'sitepagenote', 'sitepagevideo', 'sitepagepoll', 'sitepagemusic', 'sitepagealbum', 'sitepageevent', 'sitepagereview', 'sitepagedocument', 'sitebusinessalbum', 'sitebusinessdocument', 'sitebusinessevent', 'sitebusinessnote', 'sitebusinesspoll', 'sitebusinessmusic', 'sitebusinessvideo', 'sitebusinessreview', 'sitegroupalbum', 'sitegroupdocument', 'sitegroupevent', 'sitegroupnote', 'sitegrouppoll'
            , 'sitegroupmusic', 'sitegroupvideo', 'sitegroupreview', 'siteeventinvite', 'siteeventadmincontact', 'siteeventemail', 'siteeventdocument', 'classified', 'younet-core', 'siteeventrepeat', 'bigstep', 'siteestore');
        $module_table = Engine_Api::_()->getDbTable('modules', 'core');
        $module_name = $module_table->info('name');
        $select = $module_table->select()
                ->from($module_name, array('name', 'title'))
                ->where($module_name . '.type =?', 'extra')
                ->where($module_name . '.name not in(?)', $notInclude)
                ->where($module_name . '.enabled =?', 1);

        $contentModuloe = $select->query()->fetchAll();
        $contentModuloeArray = array();

        if (!empty($contentModuloe)) {
            $contentModuloeArray[] = '';
            foreach ($contentModuloe as $modules) {
                $contentModuloeArray[$modules['name']] = $modules['title'];
            }
        }

        $type = Zend_Controller_Front::getInstance()->getRequest()->getParam('type', null);
        if (!empty($contentModuloeArray)) {
            $this->addElement('Select', 'item_module', array(
                'label' => 'Content Module',
                'allowEmpty' => false,
                'onchange' => "setModuleName(this.value, '$type')",
                'multiOptions' => $contentModuloeArray,
            ));
        } else {
            //VALUE FOR LOGO PREVIEW.
            $description = "<div class='tip'><span>" . Zend_Registry::get('Zend_Translate')->_("There are currently no new modules to be added to ‘Manage Module’ section.") . "</span></div>";
            $this->addElement('Dummy', 'item_module', array(
                'description' => $description,
            ));
            $this->item_module->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
        }

        $module = Zend_Controller_Front::getInstance()->getRequest()->getParam('item_module', null);

        $contentItem = array();
        if (!empty($module)) {
            $this->item_module->setValue($module);
            $contentItem = $this->getContentItem($module);
            if (empty($contentItem))
                $this->addElement('Dummy', 'dummy_title', array(
                    'description' => 'For this module, there is  no item defined in the manifest file.',
                ));
        }
        if (!empty($contentItem)) {
            $this->addElement('Select', 'item_type', array(
                'label' => 'Database Table Item',
                'description' => "This is the value of 'items' key in the manifest file of this plugin. To view this value for a desired module, go to the directory of this module, and open the file 'settings/manifest.php'. In this file, search for 'items', and view its value. [Ex in case of blog module: Open file 'application/modules/Blog/settings/manifest.php', and go to around line 62. You will see the 'items' key array with value 'blog'. Thus, the Database Table Item for blog module is: 'blog']",
                //  'required' => true,
                'multiOptions' => $contentItem,
            ));

            //ELEMENT PACKAGE TITLE
            $this->addElement('Text', 'item_title', array(
                'label' => 'Title',
                'description' => 'Please enter the Title for content from this module (Ex: Pages, Businesses, Groups, Stores, etc.).',
                'allowEmpty' => FALSE,
                'validators' => array(
                    array('NotEmpty', true),
                )
            ));


            $id = Zend_Controller_Front::getInstance()->getRequest()->getParam('id', null);
            $integratedTable = Engine_Api::_()->getDbTable('modules', 'siteevent');
            $integratedTableResult = $integratedTable->fetchRow(array('module_id = ?' => $id));
            $integratedRowValues = $integratedTableResult->toArray();
            $itemTypeValue = $integratedRowValues['item_type'];

            $this->addElement('Radio', "siteevent_event_leader_owner_" . $itemTypeValue, array(
                'label' => 'Event Leader / Owner',
                'description' => 'Who do you want to be associated with events as their leader / owner. The Choosen entity will be displayed with the events as their leader / owner at various places like widgets, activity feeds, etc.',
                'multiOptions' => array(
                    0 => 'Creator (User)',
                    1 => 'Parent Content'
                ),
                'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting("siteevent.event.leader.owner.$itemTypeValue", 1)
            ));
            
            $this->addElement('Radio', "siteevent_multiple_leader_" . $itemTypeValue, array(
                'label' => 'Allow Multiple Event Leaders',
                'description' => 'Do you want there to be multiple leaders for events on your site? (If enabled, then every Event will be able to have multiple leaders who will be able to manage that Event. These will have the authority to add other users as leaders of their Events.)',
                'multiOptions' => array(
                   1 => 'Yes',
                    0 => 'No'
                ),
                'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting("siteevent.multiple.leader.$itemTypeValue", 0)
            ));
            
            $itemMemberType = array(            
                    'contentmembers' => 'Members who have Joined event in this content module.',
                    'contentlikemembers' => 'Members who have Liked event in this content module.',
                    'contentfollowmembers' => 'Members who have Followed event in this content module.'
                );
            
       
            if($module == 'sitestore') 
              unset($itemMemberType['contentmembers']);
            elseif($module == 'sitereview') {
              unset($itemMemberType['contentmembers']);
              unset($itemMemberType['contentfollowmembers']);
            }
            elseif($module == 'sitepage' && !Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepagemember'))
               unset($itemMemberType['contentmembers']);     
         
            $this->addElement('MultiCheckbox', 'item_membertype', array(
                'label' => 'Members to be Invited to Events',
                'description' => "Choose members from below whom event owners, leaders and guests would be able to invite to their events.",
                'multiOptions' => $itemMemberType,
                'value' => 1
            )); 

            $this->addElement('Checkbox', 'enabled', array(
                'description' => 'Enable Module for Event in the Content',
                'label' => 'Enable Module for Event in the Content.',
                'value' => 1
            ));

            $this->addElement('Hidden', 'type', array('order' => 3, 'value' => $type));

            // Element: execute
            $this->addElement('Button', 'execute', array(
                'label' => 'Save Settings',
                'type' => 'submit',
                'ignore' => true,
                'decorators' => array('ViewHelper'),
            ));

            // Element: cancel
            $this->addElement('Cancel', 'cancel', array(
                'label' => 'cancel',
                'prependText' => ' or ',
                'ignore' => true,
                'link' => true,
                'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'index')),
                'decorators' => array('ViewHelper'),
            ));
        }
    }

    public function getContentItem($moduleName) {
        $id = Zend_Controller_Front::getInstance()->getRequest()->getParam('id', null);
        $mixSettingsTable = Engine_Api::_()->getDbtable('modules', 'siteevent');
        $mixSettingsTableName = $mixSettingsTable->info('name');
        $moduleArray = $mixSettingsTable->select()
                ->from($mixSettingsTableName, "$mixSettingsTableName.item_type")
                ->where($mixSettingsTableName . '.item_module = ?', $moduleName)
                ->query()
                ->fetchAll(Zend_Db::FETCH_COLUMN);

        $file_path = APPLICATION_PATH . "/application/modules/" . ucfirst($moduleName) . "/settings/manifest.php";
        $contentItem = array();
        if (@file_exists($file_path)) {
            $ret = include $file_path;
            if (isset($ret['items'])) {
                foreach ($ret['items'] as $item) {
                    if ($id) {
                        $contentItem[$item] = $item . " ";
                    } else {
                        if (!in_array($item, $moduleArray))
                            $contentItem[$item] = $item . " ";
                    }
                }
            }
        }
        return $contentItem;
    }

}