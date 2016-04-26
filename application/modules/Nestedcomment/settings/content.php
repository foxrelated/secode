<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Nestedcomment
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: content.php 2014-11-07 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
$multiOptions = array('friends' => 'Friends');
$multiComposerOptions = array('addLink' => 'Add Link');
$include = array('sitepage', 'sitebusiness', 'sitegroup','sitestore','list', 'group', 'event', 'recipe', 'album', 'advalbum', 'siteevent');
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

$multiComposerOptions['addSmilies'] = 'Add Emoticons';
$view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
$content_array = array(
    array(
        'title' => 'Comments & Replies',
        'description' => 'Enable users to comment and reply on the content being viewed. Displays all the comments and replies on the Content View page. This widget should be placed on Content View page.',
        'category' => 'Advanced Comments Plugin - Nested Comments, Replies, Voting & Attachments',
        'type' => 'widget',
        'name' => 'nestedcomment.comments',
        'autoEdit' => 'true',
        'defaultParams' => array(
            'title' => '',
            'taggingContent' => array("friends", "group", "event", "sitepage", "sitebusiness", "list", "recipe", "siteevent")
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'MultiCheckbox',
                    'taggingContent',
                    array(
                        'label' => "In the comments on content of this module, which all types of content should be taggable? (users will be able to tag a content entity in their comments using '@' symbol)",
                        'multiOptions' => $multiOptions,
                    //'value' => array(0)
                    )
                ),
                array(
                    'MultiCheckbox',
                    'showComposerOptions',
                    array(
                        'label' =>  "Which all types of attachments do you want to allow in comments on this module's content?",
                        'multiOptions' => $multiComposerOptions,
                    //'value' => ''
                    )
                ),
                array(
                    'Radio',
                    'showAsNested',
                    array(
                        'label' => "Do you want to enable nested comments feature for this module's content? If selected no, then simple, one-level commenting will be available for this module's content, and users will not be able to 'Reply' to comments.",
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1
                    ),
                ),
                array(
                    'Radio',
                    'showAsLike',
                    array(
                        'label' => "Selection of Like / Dislike (By choosing the below options you will be able to enable the Like / Dislike link for your content, comment and replies.)",
                        'multiOptions' => array(
                            1 => 'Only Like',
                            0 => 'Both, Like and Dislike'
                        ),
                        'value' => 1
                    ),
                ),
                array(
                    'Radio',
                    'showDislikeUsers',
                    array(
                        'label' => "Do you want to show the users who have disliked a content, comment and reply? Sometimes Site Admins do not want to show that who all have disliked a content. So, if you do not want to show this to users then please select ‘No’. [Note: This setting will work only if you have selected 'Both, Like and Dislike' option in the above setting.]",
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1
                    ),
                ),
                array(
                    'Radio',
                    'showLikeWithoutIcon',
                    array(
                        'label' => 'How do you want to display the Like / Dislike options for content of this module? This setting will work only when you have selected ‘Both, Like and Dislike’ option in the above setting.',
                        'multiOptions' => array(
                            1 => 'Only Text',
                            0 => 'Text With Icon'
                        ),
                        'value' => 1
                    ),
                ),
                array(
                    'Radio',
                    'showLikeWithoutIconInReplies',
                    array(
                        'label' => "How do you want to display the Like / Dislike options for comments and replies? This setting will work only when you have selected 'Both, Like and Dislike' option in the above setting.",
                        'multiOptions' => array(
                            1 => 'Only Text',
                            0 => 'Text With Icon',
                            2 => 'Only Icon',
                            3 => 'Vote Up and Vote Down'
                        ),
                        'value' => 1
                    ),
                ),
                array(
                    'Radio',
                    'commentsorder',
                    array(
                                    'label' => 'Select the order in which comments should be displayed on your website.',
                                    'multiOptions' => array(
                                                    1 => 'Newer to older',
                                                    0 => 'Older to newer'
                                    ),
                                    'value' => 1,
                    )
                ),
                array(
                    'Radio',
                    'loaded_by_ajax',
                    array(
                        'label' => 'Widget Content Loading',
                        'description' => 'Do you want the content of this widget to be loaded via AJAX, after the loading of main webpage content? (Enabling this can improve webpage loading speed. Disabling this would load content of this widget along with the page content.)',
                        'multiOptions' => array(
                            1 => 'Yes',
                            0 => 'No'
                        ),
                        'value' => 1,
                    )
                ),
            ),
        ),
        'requirements' => array(
            'subject',
        ),
    ),
);

return $content_array;