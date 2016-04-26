<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitecoupon
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: faq_help.tpl 6590 2012-06-24 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
  function faq_show(id) {
    if($(id).style.display == 'block') {
      $(id).style.display = 'none';
    } else {
      $(id).style.display = 'block';
    }
  }
</script>
<div class="admin_sitepage_files_wrapper">
	<ul class="admin_sitepage_files sitepage_faq">
 
<li>
      <a href="javascript:void(0);" onClick="faq_show('faq_1');"><?php echo $this->translate("The CSS of this plugin is not coming on my site. What should I do ?"); ?></a>
      <div class='faq' style='display: none;' id='faq_1'>
        <?php echo "Ans: Please enable the 'Development Mode' system mode for your site from the Admin homepage and then check the page which was not coming fine. It should now seem fine. Now you can again change the system mode to 'Production Mode'."; ?>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_2');"><?php echo $this->translate("Some videos from my site’s activity feeds are being shown in the Video Lightbox Viewer when I click on their titles, but for some other videos I am being redirected to their view pages. What might be the reason?"); ?></a>
      <div class='faq' style='display: none;' id='faq_2'>
        <?php echo "Ans: Videos from the <a target='_blank' href='http://www.socialengineaddons.com/socialengine-directory-pages-plugi' >\"Directory / Pages Plugin\" </a> and <a target='_blank' href='http://www.socialengineaddons.com/socialengine-directory-businesses-plugin' >\"Directory / Businesses Plugin\" </a> will be shown in the Video Lightbox Viewer when you click on their titles. You can also see all the videos from your site’s activity feeds in the Video Lightbox Viewer by using our <a target='_blank'href='http://www.socialengineaddons.com/socialengine-advanced-activity-feeds-wall-plugin'> \"Advanced Activity Feeds / Wall Plugin\"</a>. (To see details, please <a target='_blank' href='http://www.socialengineaddons.com/socialengine-advanced-activity-feeds-wall-plugin'> visit here</a>.) or by doing minor modifications mentioned below to make Video Lightbox Viewer work with activity feeds:"; ?>
        <p>
          <b><?php echo $this->translate("Step 1:") ?></b>
        </p>
        <div class="code">
	            <?php echo $this->translate(nl2br("a) Open the file: 'application/modules/Activity/Model/Helper/Item.php'.
	<br />b) Go to the line around line number 59 having:
	<b class='bold'>class='feed_item_username' </b> 
	<br />c) Replace the above code with the new block of code below after the dot(.):
	<b class='bold'>class=\"feed_item_username feed_'.".'$item->getType()'.".'_title\" </b>
")) ?>
          </div><br />
          <p>
          <b><?php echo $this->translate("Step 2:") ?></b>
        </p>
        <div class="code">
	            <?php echo $this->translate(nl2br("a) Open the file: 'application/modules/Activity/views/scripts/_activityText.tpl'.
	<br />b) Go to the line around line number 65 having:
	<b class='bold'>$$('.comments_body').enableLinks(); </b> 
	<br />c) Now below this line, add the new block of code mentioned below:
	<b class='bold'>if(en4.sitevideoview){
  en4.sitevideoview.attachClickEvent(Array('feed','feed_video_title','feed_sitepagevideo_title','feed_sitebusinessvideo_title','feed_ynvideo_title','feed_sitegroupvideo_title','feed_sitestorevideo_title'));
  }</b>
")) ?>
       </div>
      </div>
    </li>
    <li>
      <a href="javascript:void(0);" onClick="faq_show('faq_3');"><?php echo $this->translate("I have the Related Videos Widgets from SocialEngineExperts.com installed on my site, but for this widget, the videos are not appearing in the Video Lightbox Viewer. What should I do?"); ?></a>
      <div class='faq' style='display: none;' id='faq_3'>
        <?php echo       $this->translate( "Ans: To make this plugin work with the Related Videos Widgets from SocialEngineExperts.com, please follow the steps below: ")?>
        <div class="code">
	            <?php echo $this->translate(nl2br('a) Open the file: "application/widgets/related-videos/index.tpl".
	<br />b) Go to the line around line number 191 having:
	<b class="bold" >listItem.inject(sees_related_videos_list);
                        }
                    });</b> 
	<br />c) Now, add this below code after the above code:
	<b class="bold" >if(en4.sitevideoview)
                en4.sitevideoview.attachClickEvent(Array("sees_related_video_title","sees_related_video_thumb"));</b>
')) ?>
          </div>
      </div>
    </li>
    <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('ynvideo')): ?>
      <li>
        <a href="javascript:void(0);" onClick="faq_show('faq_4');"><?php echo $this->translate("I have the Advanced Video Plugin from YouNet installed on my site, but for some widgets of this plugin, the videos are not appearing fine in the Video Lightbox Viewer. What should I do? "); ?></a>
        <div class='faq' style='display: none;' id='faq_4'>
          <?php echo $this->translate("Ans: To make this plugin work with the Advanced Video Plugin from YouNet, please follow the steps below:"); ?>
            <p>
              <b><?php echo $this->translate("Step 1:") ?></b>
            </p>
						<div class="code">
	            <?php echo $this->translate(nl2br('a) Open the file: "application/modules/Ynvideo/widgets/list-popular-videos/index.tpl".
	<br />b) Go to the line around line number 11 having:
	<b class="bold" >&ltul class="generic_list_widget ynvideo_widget &lt;?php echo ($this->viewType == \'small\')?\'\':\'videos_browse ynvideo_frame ynvideo_list\'?&gt;"&gt;</b> 
	<br />c) Now, replace the above code with the new block of code below:
	<b class="bold" >&lt;ul class="generic_list_widget ynvideo_widget layout_ynvideo_&lt;?php echo  $this->popularType?>_videos <?php echo ($this->viewType == \'small\')?\'\':\'videos_browse ynvideo_frame ynvideo_list\'?&gt;"&gt;</b>
')) ?>
          </div><br />
         	
            <p>
              <b><?php echo $this->translate("Step 2:") ?></b>
            </p>       
					 <div class="code">
            <?php echo $this->translate(nl2br('a) Open the file: "application/modules/Ynvideo/widgets/list-recent-videos/index.tpl".
<br />b) Go to the line around line number 13 having:
<b class="bold" >en4.core.runonce.add(function(){</b> 
<br />c) Now below this line, add the new block of code mentioned below:
<b class="bold">if(en4.sitevideoview)
      en4.sitevideoview.attachClickEvent(Array(\'item_photo_video\',\'ynvideo_title\'));</br>
')) ?>

          </div>
        </div>
      </li>
    <?php endif; ?>

	</ul>
</div>