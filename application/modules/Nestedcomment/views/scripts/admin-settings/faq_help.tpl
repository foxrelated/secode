<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Nestedcomment
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: faq_help.tpl 2014-11-07 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<script type="text/javascript">
    function faq_show(id) {
        if ($(id)) {
            if ($(id).style.display == 'block') {
                $(id).style.display = 'none';
            } else {
                $(id).style.display = 'block';
            }
        }
    }
    <?php if ($this->faq_id): ?>
            window.addEvent('domready', function() {
        faq_show('<?php echo $this->faq_id; ?>');
    });
    <?php endif; ?>
</script>

<?php $i = 1; ?>
<div class="admin_seaocore_files_wrapper">
    <ul class="admin_seaocore_files seaocore_faq">

<li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo $this->translate('I have installed this plugin now, I am unable to do nested comments. What might be the reason?'); ?></a>
            <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
                <div class="code">
                    <?php echo $this->translate("Please do the following steps:<br />
1. Go to the widgetized page where you are unable to do the nested comments. <br />
2. Now, check whether nested comments have been replaced with the SocialEngine’s core comment box. If no then, please go to the ‘Manage Modules’ section available in the admin panel of this plugin and enable the module for which you want to do the nested comments."); ?>
                </div>
            </div>
        </li>

        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo $this->translate('I have placed ‘Comments and Replies’ widget on the content page, but I am unable to do nested comment. What might be the reason?'); ?></a>
            <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
                <div class="code">
                    <?php echo $this->translate("Please do the following steps:<br />
1. Go to the widgetized page where you have placed ‘Comments and Replies’ widget and use the available ‘edit’ link. <br />
2. Now choose ‘yes’ for the ‘Do you want to enable nested comments feature for this widget? If selected no, then you will be able to do simple comments instead of nested comments.’ setting."); ?>
                </div>
            </div>
        </li> 

 
        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo $this->translate('I am having Directory / Pages Plugin installed on my website. Now I have created a page but, when I comment on that Page, comment is shown with my name not with the page name. How can I resolve this problem?'); ?></a>
            <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
                <div class="code">
                    <?php echo $this->translate("It is happening because you might have now enabled the setting for this module from the admin panel, to do so:<br />
1. Please go to the admin panel of ‘Directory / Pages Plugin’ >> ‘Activity Feeds’ section. <br />
2. Now, choose ‘Page's Photo and Title’ option for ‘Directory Items / Pages Activity Feed Type’ setting.<br />

you will now be able to see the page title and photo in the activity feed.<br />
[Note: It is dependent on our <a href='http://www.socialengineaddons.com/socialengine-directory-pages-plugin' target='_blank'>Directory / Pages Plugin</a>.]"); ?>

                </div>
            </div>
        </li> 

        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo $this->translate('What all places can I use the ‘Comments and Replies’ widget for nested comments?
'); ?></a>
            <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
                <div class="code">
                    <?php echo $this->translate('Comments and Replies widget enables you to comment and reply on the content being viewed. Displays all the comments & replies on the content view page. So it should be placed at the Content View Page.<br />
[<strong>Note:</strong> if you want to place this widget on any widgetized page then you can place ‘Comments and Replies widget else if it is a non widgetized page and the code is coming directly in the content view page ‘tpl file’ then please follow the below steps:<br />
1. Find the below code:<br />
echo $this->action("list", "comment", "core", <br />

2. Now, replace this whole line with the below code:<br />
include_once APPLICATION_PATH . "/application/modules/Seaocore/views/scripts/_listComment.tpl";
    
 ’]'); ?>

                </div>
            </div>
        </li>

 <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo $this->translate('I want to enable nested comments for 3rd party plugin, How can I do so ?'); ?></a>
            <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
                <div class="code">
                    <?php echo $this->translate('You can do so by two methods:<br />
Method 1: If it is a widgetize page then please place ‘Comments & Replies’ widget on the content view page of that module and configure the available settings. <br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Else<br />
Method 2: If it is non widgetized page and code is coming directly in the content view page ‘tpl file’ then please follow the below steps:<br />
1. Go to the ‘Manage Modules‘ section available in the admin panel of this plugin. <br />
2. Now click on ‘Add New Module’ link. Please choose the module from the dropdown, for which you want to enable nested comments.<br />
3. Find the below code:<br />
echo $this->action("list", "comment", "core", <br />
4. Now, replace this whole line with the below code:<br />
include_once APPLICATION_PATH . "/application/modules/Seaocore/views/scripts/_listComment.tpl";<br />
    
 ’]'); ?>

                </div>
            </div>
        </li>

        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo $this->translate('Is it possible to tag Directory / Pages, Advanced Events in comments? If yes, how can I do so?'); ?></a>
            <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
                <div class="code">
                    <?php echo $this->translate("Yes you can do so, <br />
1. Please go to the ‘Layout Manager’ from the admin panel, now go to the widgetized page where you have placed ‘Comments and Replies’ widget. <br />
2. Use the ‘edit’ link available on the widget to configure the given settings. <br />
3. Choose the contents which you want your users can tag in their comments from the checkboxes, available in the ‘Which type of content should be tagged? setting.<br />

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; OR<br />
If the code is not coming from the widget then you need to configure the setting for this module from the admin panel of this plugin, <br />
1. Please go to the 'Manage Modules' section available in the admin panel of this plugin. <br />
2. Now, click on the edit link available in the right side for your module (for eg in this case: Directory / Pages and Advanced Events Plugin). <br />
3. Now enable the modules from the checkboxes available for 'Which type of content should be tagged? setting.'<br />
[Note: It is dependent on our <a href='http://www.socialengineaddons.com/socialengine-directory-pages-plugin' target='_blank'>Directory / Pages Plugin</a> and <a href='http://www.socialengineaddons.com/socialengine-advanced-events-plugin' target='_blank'>Advanced Events Plugin</a>.]
"); ?>
                </div>
            </div>
        </li>

        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo $this->translate('I want my users to vote for the comments posted by the site users instead of Like / Dislike. Is it possible to do so?'); ?></a>
            <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
                <div class="code">
                    <?php echo $this->translate("Yes ‘Vote’ is a great way to display which comments have been appreciated highly and which are right according to the viewers. You can easily do so by: <br />
1. Please go to the ‘Layout Manager’ from the admin panel, now go to the widgetized page where you have placed ‘Comments and Replies’ widget.<br /> 
2. Use the ‘edit’ link available on the widget to configure the given settings. <br />
3. Choose ‘vote’ option for the setting: ‘How do you want to display Like / Dislike for comments and reply? This setting will only work if you have selected the 'Both, Like and Dislike' option in the above setting'. <br />

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; OR<br />

If the code is not coming from the widget then you need to configure the setting for this module from the admin panel of this plugin, <br />
1. Please go to the 'Manage Modules' section available in the admin panel of this plugin. <br />
2. Now, click on the edit link available in the right side for your module. <br />
3. Now enable the modules from the checkboxes available for 'How do you want to display Like / Dislike for comments and reply? This setting will only work if you have selected the 'Both, Like and Dislike' option in the above setting'<br />
Your users can now vote for the comments they think are right instead of Like / Dislike.
"); ?>
                </div>
            </div>
        </li> 

     <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo $this->translate('I want my users to dislike the content they want. Is it possible with this plugin?'); ?></a>
            <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
                <div class="code">
                    <?php echo $this->translate("Yes you can do so by:<br />
1. Please go to the ‘Layout Manager’, now go to the widgetized page where you have placed ‘Comments and Replies’ widget. <br />
2. Use the ‘edit’ link available on the widget to configure the given settings. <br />
3. Now enable the ‘Like / Dislike Both’ option available in the setting: Selection of Like / Dislike (By choosing the below options you will be able to enable the Like / Dislike link for your content, comment and replies.) <br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; OR<br />

If the code is not coming from the widget then you need to configure the setting for this module from the admin panel of this plugin, <br />
1. Please go to the 'Manage Modules' section available in the admin panel of this plugin. <br />
2. Now, click on the edit link available in the right side for your module. <br />
3. Now enable the modules from the checkboxes available for 'Selection of Like / Dislike (By choosing the below options you will be able to enable the Like / Dislike link for your content, comment and replies.)'<br />
Your users can now vote for the comments they think are right instead of Like / Dislike.<br />
Your users can now use Dislike button for the content.
"); ?>
                </div>
            </div>
        </li> 

<li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo $this->translate('Will my previous comments be lost after enabling Nested Comments?'); ?></a>
            <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
                <div class="code">
                    <?php echo $this->translate("No, none of your comments will get lost they all will be visible to you even after enabling Nested Comments.

"); ?>
                </div>
            </div>
        </li>
        
<li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo $this->translate('I am unable to attach photos in my comments. What might be the reason?'); ?></a>
            <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
                <div class="code">
                    <?php echo $this->translate("It is happening because you have not selected ‘Photo’ option in the ‘Comments and Replies’ widget for ‘Which type of attachment can be allowed in the Comments?’ setting. So please go to the widgetized page where you have placed the above widget and enable the ‘Photo’ option.
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; OR<br />
If the code is not coming from the widget then please go to the 'Manage Modules' section of this plugin to configure the 'Which type of attachment can be allowed in the Comments?' setting for your content module. "); ?>
                </div>
            </div>
        </li>

<li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo $this->translate('I want only 10 comments to get visible on the single page. How can I do so?
'); ?></a>
            <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
                <div class="code">
                    <?php echo $this->translate("You can do so by:<br />
1. Please go to the Global Settings available in the admin panel of this plugin.<br />
2. Now enter 10 for the given setting: ‘How many comments will be shown per page on content view page?'.
"); ?>
                </div>
            </div>
        </li>

<li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo $this->translate(' I want pagination of 5 for the replies made for the comment. How can I do so?

'); ?></a>
            <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
                <div class="code">
                    <?php echo $this->translate("You can do so by:<br />
1. Please go to the Global Settings available in the admin panel of this plugin.<br />
2. Now enter 5 for the given setting: ‘How many replies will be shown per comment on content view page?'. 
"); ?>
                </div>
            </div>
        </li>
        

        <li>
            <a href="javascript:void(0);" onClick="faq_show('faq_<?php echo $i; ?>');"><?php echo $this->translate('The CSS of this plugin is not coming on my site. What should I do?'); ?></a>
            <div class='faq' style='display: none;' id='faq_<?php echo $i++; ?>'>
                <div class="code">
                    <?php echo $this->translate("Please enable the 'Development Mode' system mode for your site from the Admin homepage and then check the page which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'."); ?>
                </div>
            </div>
        </li>
    </ul>
</div>
