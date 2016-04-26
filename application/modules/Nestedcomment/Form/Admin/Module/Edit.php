<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Nestedcomment
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: Edit.php 2014-11-07 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
class Nestedcomment_Form_Admin_Module_Edit extends Engine_Form {

    public function init() {

        $this
                ->setTitle('Edit Module Settings for Advanced Comments')
                ->setDescription("Use the form below to manage nested comments and advanced commenting features for this module's content. Start by selecting a content module, and then entering its database table related field names. In case of questions regarding any field name, please contact the developer of that content module.");

        $moduleId = Zend_Controller_Front::getInstance()->getRequest()->getParam('module_id', null);
        $modulesTable = Engine_Api::_()->getDbTable('modules', 'nestedcomment');
        $modulesTableResult = $modulesTable->fetchRow(array('module_id = ?' => $moduleId));
        $moduleame = array();
        $moduleame[] = $modulesTableResult->module;

        $resourceType[] = $modulesTableResult->resource_type;
        $this->addElement('Select', 'module', array(
            'label' => 'Content Module',
            'allowEmpty' => false,
            'disable' => true,
            'multiOptions' => $moduleame,
        ));

        $this->addElement('Select', 'resource_type', array(
            'label' => 'Database Table Item',
            'description' => "This is the value of 'items' key in the manifest file of this plugin. To view this value for a desired module, go to the directory of this module, and open the file 'settings/manifest.php'. In this file, search for 'items', and view its value. [Ex in case of blog module: Open file 'application/modules/Blog/settings/manifest.php', and go to around line 62. You will see the 'items' key array with value 'blog'. Thus, the Database Table Item for blog module is: 'blog']",
            'disable' => true,
            'multiOptions' => $resourceType,
        ));

        $multiOptions = array('friends' => 'Friends');
        $multiComposerOptions = array('addLink' => 'Add Link');
        $include = array('sitepage', 'sitebusiness', 'sitegroup', 'sitestore', 'list', 'group', 'event', 'recipe', 'album', 'advalbum', 'siteevent');
        $module_table = Engine_Api::_()->getDbTable('modules', 'core');
        $module_name = $module_table->info('name');
        $select = $module_table->select()
                ->from($module_name, array('name', 'title'))
                ->where($module_name . '.type =?', 'extra')
                ->where($module_name . '.name in(?)', $include)
                ->where($module_name . '.enabled =?', 1);

        $contentModule = $select->query()->fetchAll();
        $include[] = 'friends';
        foreach ($contentModule as $module) {
            if ($module['name'] != 'album' && $module['name'] != 'advalbum')
                $multiOptions[$module['name']] = $module['title'];
            if ($module['name'] == 'album' || $module['name'] == 'advalbum')
                $multiComposerOptions['addPhoto'] = 'Add Photo';
        }

        if (isset($multiOptions['event'])) {
            $multiOptions['event'] = 'Events (SE Core)';
        }

        if (isset($multiOptions['group'])) {
            $multiOptions['group'] = 'Groups (SE Core)';
        }

        $this->addElement('MultiCheckbox', 'taggingContent', array(
            'description' => "In the comments on content of this module, which all types of content should be taggable? (users will be able to tag a content entity in their comments using '@' symbol)",
            'label' => 'Taggable Content Types',
            'multiOptions' => $multiOptions,
            'value' => $include
        ));
        $multiComposerOptions['addSmilies'] = 'Add Emoticons';
        $this->addElement('MultiCheckbox', 'showComposerOptions', array(
            'description' => "Which all types of attachments do you want to allow in comments on this module's content?",
            'label' => 'Attachment types',
            'multiOptions' => $multiComposerOptions,
            'value' => array('addPhoto', 'addLink', 'addSmilies')
        ));

        $this->addElement('Radio', 'showAsNested', array(
            'description' => "Do you want to enable nested comments feature for this module's content? If selected no, then simple, one-level commenting will be available for this module's content, and users will not be able to 'Reply' to comments.",
            'label' => 'Nested Comments',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => 1
        ));

        $bothLikeandDislike = "Both, Like and Dislike " . '<a href="http://www.socialengineaddons.com/sites/default/files/images/likes.png" title="View Screenshot" class="buttonlink seaocore_icon_view mleft5" target="_blank"></a>';
        $this->addElement('Radio', 'showAsLike', array(
            'description' => 'Selection of Like / Dislike (By choosing the below options you will be able to enable the Like / Dislike link for your content, comment and replies.)',
            'label' => 'Like and Dislike',
            'multiOptions' => array(
                1 => 'Only Like',
                0 => $bothLikeandDislike
            ),
            'value' => 1,
            'escape' => false,
            'onclick' => 'hideOptions(this.value);'
        ));

        $this->addElement('Radio', 'showDislikeUsers', array(
            'description' => "Do you want to show the users who have disliked a content, comment and reply? Sometimes Site Admins do not want to show that who all have disliked a content. So, if you do not want to show this to users then please select ‘No’. [Note: This setting will work only if you have selected 'Both, Like and Dislike' option in the above setting.]",
            'label' => 'Dislike users list',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => 1
        ));

        $voteUp = "Vote Up and Vote Down " . '<a href="http://www.socialengineaddons.com/sites/default/files/images/votes.png" title="View Screenshot" class="buttonlink seaocore_icon_view mleft5" target="_blank"></a>';
        $this->addElement('Radio', 'showLikeWithoutIcon', array(
            'description' => "How do you want to display the Like / Dislike / Vote Up / Vote Down options for feed of this module? This setting will work only when you have selected ‘Both, Like and Dislike’ option in the above setting.",
            'label' => 'Like / Dislike / Vote Up / Vote Down display for content',
            'multiOptions' => array(
                1 => 'Only Text',
                0 => 'Text With Icon',
                3 => $voteUp
            ),
            'escape' => false,
            'value' => 1
        ));

        $voteUp = "Vote Up and Vote Down " . '<a href="http://www.socialengineaddons.com/sites/default/files/images/votes.png" title="View Screenshot" class="buttonlink seaocore_icon_view mleft5" target="_blank"></a>';
        $this->addElement('Radio', 'showLikeWithoutIconInReplies', array(
            'description' => "How do you want to display the Like / Dislike / Vote Up / Vote Down options for comments and replies? This setting will work only when you have selected 'Both, Like and Dislike' option in the above setting.",
            'label' => 'Like / Dislike / Vote Up / Vote Down display for comment / reply',
            'multiOptions' => array(
                1 => 'Only Text',
                0 => 'Text With Icon',
                2 => 'Only Icon',
                3 => $voteUp
            ),
            'escape' => false,
            'value' => 1
        ));


        $this->addElement('Radio', 'commentsorder', array(
            'label' => 'Ordering Comments',
            'description' => 'Select the order in which comments should be displayed on your website.',
            'multiOptions' => array(
                1 => 'Newer to older',
                0 => 'Older to newer'
            ),
            'value' => 1
        ));

        $this->addElement('Radio', 'loaded_by_ajax', array(
            'label' => 'Loading of Nested Comments Widget',
            'description' => 'Do you want the content of Nested Comments widget to be loaded via AJAX, after the loading of main webpage content? (Enabling this can improve webpage loading speed. Disabling this would load content of this widget along with the page content.)',
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => 1
        ));

        $this->addElement('Button', 'submit', array(
            'label' => 'Save Settings',
            'type' => 'submit',
            'ignore' => true
        ));
    }

}
