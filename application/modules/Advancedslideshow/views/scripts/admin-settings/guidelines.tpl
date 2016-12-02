<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedslideshow
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: guidelines.tpl 2011-10-22 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2><?php echo $this->translate("Advanced Slideshow Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
	<div class='tabs'>
		<?php	echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
	</div>
<?php endif; ?>

<script type="text/javascript">
  function guideline_show(id) {
    if($(id).style.display == 'block') {
      $(id).style.display = 'none';
    } else {
      $(id).style.display = 'block';
    }
  }
</script>

<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedslideshow/externals/images/back.png" class="icon" />
<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'advancedslideshow', 'controller' => 'slideshows', 'action' => 'manage'), $this->translate('Back to Manage Slideshows'), array('class'=> 'buttonlink', 'style'=> 'padding-left:0px;')) ?>
<br />
<br class="clr" />

<div class='clear'>
	<h3><?php echo $this->translate("Guidelines for placing Slideshow on some Popular Non-widgetized Pages") ?></h3>
	<p><?php echo $this->translate("With this plugin, you can also place Slideshow on the Non-Widgetized pages of your site. These are the pages which are not available for layout customization in the Layout Editor of SocialEngine Admin Panel. To place Slideshows on such pages, firstly create a new Slideshow for Non-Widgetized Page. Then go to Manage Slideshows page to view the code for this Slideshow. Copy the code of the Slideshow and paste it at the desired location in the template file of the non-widgetized page to display slideshow over there. Please be sure that you paste the code at the right place. After pasting the code in your template page, if the page layout becomes disorganized, then contact your theme developer to assist you.<br />Below are the guidelines for placing Slideshow on some popular Non-Widgetized pages. Use the code generated for Slideshow on these pages from the 'Manage Slideshows' section.") ?></p><br />
	<div class="admin_files_wrapper" style="width:930px;">
		<ul class="admin_files advslideshow_guidelines_faq" style="max-height:none;">
			<li>
				<a href="javascript:void(0);" onClick="guideline_show('guideline_0');"><?php echo $this->translate("Invite Page");?></a>
				<div class='faq' style='display: none;' id='guideline_0'>
					<?php echo $this->translate('<b>For this page, place the Slideshow code in the template file: '). $this->translate('"/application/modules/Invite/views/scripts/index/index.tpl"</b><p><b>Example:</b> In this example, we show how to place an ordinary Slideshow (non-featured, non-sponsored) in the right side of Invite page. First, create a Slideshow from the Manage Slideshows page for a "Non-Widgetized Page", and "Non-Widgetized Page: Right Column Slideshow" block position. Then, copy the code for this Slideshow from the Manage Slideshows page. Now:</p><br /><p>1. Open the template file and search for the line given below:</p><div class="code">&lt;div id="invite_form"&gt;</div><br /><p>2. Now insert your Slideshow code, that was generated from the "Manage Slideshows" page using \'code\' option against this slideshow, just before the above mentioned line.</p><b>Example Code</b></p><div class="code">&lt;div class="layout_middle"&gt;<br />&lt;?php echo $this->content()->renderWidget("advancedslideshow.fullwidth3-advancedslideshows");?&gt;<br />&lt;/div&gt;</div>');?>
				</div>
			</li>
			
			<li>
				<a href="javascript:void(0);" onClick="guideline_show('guideline_1');"><?php echo $this->translate("Video manage Page");?></a>
				<div class='faq' style='display: none;' id='guideline_1'>
					<?php echo $this->translate('<b>For this page, place the Slideshow code in the template file: '). $this->translate('"/application/modules/Video/views/scripts/index/manage.tpl"</b><p>Example: In this example, we show how to place an ordinary Slideshow (non-featured, non-sponsored) in the left side of Video manage page. First, create a Slideshow from the Manage Slideshows page for a "Non-Widgetized Page", and "Non-Widgetized Page: Extended Right / Left Slideshow" block position. Then, copy the code for this Slideshow from the Manage Slideshows page. Now:</p><br /><p>1. Open the template file and search for the line given below:</p><div class="code"> &lt;div class=\'layout_middle\'&gt;</div><p><br />2. Now insert your Slideshow code, that was generated from the "Manage Slideshows" page using \'code\' option against this slideshow, just after the above mentioned line.</p><p><b>Example Code</b></p><div class="code">&lt;?php echo $this->content()->renderWidget("advancedslideshow.extended3-advancedslideshows");?&gt;</div>');?>
				</div>
			</li>
			
			<li>
				<a href="javascript:void(0);" onClick="guideline_show('guideline_2');"><?php echo $this->translate("Group manage Page");?></a>
				<div class='faq' style='display: none;' id='guideline_2'>
					<?php echo $this->translate('<b>For this page, place the Slideshow code in the template file: '). $this->translate('"/application/modules/Group/views/scripts/index/manage.tpl"</b><p>Example: In this example, we show how to place an ordinary Slideshow (non-featured, non-sponsored) in the left side of Group manage page. First, create a Slideshow from the Manage Slideshows page for a "Non-Widgetized Page", and "Non-Widgetized Page: Extended Right / Left Slideshow" block position. Then, copy the code for this Slideshow from the Manage Slideshows page. Now:</p><br /><p>1. Open the template file and search for the line given below:</p><div class="code"> &lt;div class=\'layout_middle\'&gt;</div><p><br />2. Now insert your Slideshow code, that was generated from the "Manage Slideshows" page using \'code\' option against this slideshow, just after the above mentioned line.</p><p><b>Example Code</b></p><div class="code">&lt;?php echo $this->content()->renderWidget("advancedslideshow.extended3-advancedslideshows");?&gt;</div>');?>
				</div>
			</li>
			
			<li>
				<a href="javascript:void(0);" onClick="guideline_show('guideline_3');"><?php echo $this->translate("Music manage Page");?></a>
				<div class='faq' style='display: none;' id='guideline_3'>
					<?php echo $this->translate('<b>For this page, place the Slideshow code in the template file: '). $this->translate('"/application/modules/Music/views/scripts/index/manage.tpl"</b><p>Example: In this example, we show how to place an ordinary Slideshow (non-featured, non-sponsored) in the left side of Music manage page. First, create a Slideshow from the Manage Slideshows page for a "Non-Widgetized Page", and "Non-Widgetized Page: Extended Right / Left Slideshow" block position. Then, copy the code for this Slideshow from the Manage Slideshows page. Now:</p><br /><p>1. Open the template file and search for the line given below:</p><div class="code"> &lt;div class=\'layout_middle\'&gt;</div><p><br />2. Now insert your Slideshow code, that was generated from the "Manage Slideshows" page using \'code\' option against this slideshow, just after the above mentioned line.</p><p><b>Example Code</b></p><div class="code">&lt;?php echo $this->content()->renderWidget("advancedslideshow.extended3-advancedslideshows");?&gt;</div>');?>
				</div>
			</li>
			
			<li>
				<a href="javascript:void(0);" onClick="guideline_show('guideline_4');"><?php echo $this->translate("Classified manage Page");?></a>
				<div class='faq' style='display: none;' id='guideline_4'>
					<?php echo $this->translate('<b>For this page, place the Slideshow code in the template file: '). $this->translate('"/application/modules/Classified/views/scripts/index/manage.tpl"</b><p>Example: In this example, we show how to place an ordinary Slideshow (non-featured, non-sponsored) in the left side of Classified manage page. First, create a Slideshow from the Manage Slideshows page for a "Non-Widgetized Page", and "Non-Widgetized Page: Extended Right / Left Slideshow" block position. Then, copy the code for this Slideshow from the Manage Slideshows page. Now:</p><br /><p>1. Open the template file and search for the line given below:</p><div class="code"> &lt;div class=\'layout_middle\'&gt;</div><p><br />2. Now insert your Slideshow code, that was generated from the "Manage Slideshows" page using \'code\' option against this slideshow, just after the above mentioned line.</p><p><b>Example Code</b></p><div class="code">&lt;?php echo $this->content()->renderWidget("advancedslideshow.extended3-advancedslideshows");?&gt;</div>');?>
				</div>
			</li>
			
			<li>
				<a href="javascript:void(0);" onClick="guideline_show('guideline_5');"><?php echo $this->translate("Poll manage Page");?></a>
				<div class='faq' style='display: none;' id='guideline_5'>
					<?php echo $this->translate('<b>For this page, place the Slideshow code in the template file: '). $this->translate('"/application/modules/Poll/views/scripts/index/manage.tpl"</b><p>Example: In this example, we show how to place an ordinary Slideshow (non-featured, non-sponsored) in the left side of Poll manage page. First, create a Slideshow from the Manage Slideshows page for a "Non-Widgetized Page", and "Non-Widgetized Page: Extended Right / Left Slideshow" block position. Then, copy the code for this Slideshow from the Manage Slideshows page. Now:</p><br /><p>1. Open the template file and search for the line given below:</p><div class="code"> &lt;div class=\'layout_middle\'&gt;</div><p><br />2. Now insert your Slideshow code, that was generated from the "Manage Slideshows" page using \'code\' option against this slideshow, just after the above mentioned line.</p><p><b>Example Code</b></p><div class="code">&lt;?php echo $this->content()->renderWidget("advancedslideshow.extended3-advancedslideshows");?&gt;</div>');?>
				</div>
			</li>
			
			<li>
				<a href="javascript:void(0);" onClick="guideline_show('guideline_6');"><?php echo $this->translate("Album manage Page");?></a>
				<div class='faq' style='display: none;' id='guideline_6'>
					<?php echo $this->translate('<b>For this page, place the Slideshow code in the template file: '). $this->translate('"/application/modules/Album/views/scripts/index/manage.tpl"</b><p>Example: In this example, we show how to place an ordinary Slideshow (non-featured, non-sponsored) in the left side of Album manage page. First, create a Slideshow from the Manage Slideshows page for a "Non-Widgetized Page", and "Non-Widgetized Page: Extended Right / Left Slideshow" block position. Then, copy the code for this Slideshow from the Manage Slideshows page. Now:</p><br /><p>1. Open the template file and search for the line given below:</p><div class="code"> &lt;div class="layout_middle"&gt;</div><p><br />2. Now insert your Slideshow code, that was generated from the "Manage Slideshows" page using \'code\' option against this slideshow, just after the above mentioned line.</p><p><b>Example Code</b></p><div class="code">&lt;?php echo $this->content()->renderWidget("advancedslideshow.extended3-advancedslideshows");?&gt;</div>');?>
				</div>
			</li>
			
			<li>
				<a href="javascript:void(0);" onClick="guideline_show('guideline_7');"><?php echo $this->translate("Blog manage Page");?></a>
				<div class='faq' style='display: none;' id='guideline_7'>
					<?php echo $this->translate('<b>For this page, place the Slideshow code in the template file: '). $this->translate('"/application/modules/Blog/views/scripts/index/manage.tpl"</b><p>Example: In this example, we show how to place an ordinary Slideshow (non-featured, non-sponsored) in the left side of Blog manage page. First, create a Slideshow from the Manage Slideshows page for a "Non-Widgetized Page", and "Non-Widgetized Page: Extended Right / Left Slideshow" block position. Then, copy the code for this Slideshow from the Manage Slideshows page. Now:</p><br /><p>1. Open the template file and search for the line given below:</p><div class="code"> &lt;div class=\'layout_middle\'&gt;</div><p><br />2. Now insert your Slideshow code, that was generated from the "Manage Slideshows" page using \'code\' option against this slideshow, just after the above mentioned line.</p><p><b>Example Code</b></p><div class="code">&lt;?php echo $this->content()->renderWidget("advancedslideshow.extended3-advancedslideshows");?&gt;</div>');?>
				</div>
			</li>
			
			<li>
				<a href="javascript:void(0);" onClick="guideline_show('guideline_8');"><?php echo $this->translate("Event manage Page");?></a>
				<div class='faq' style='display: none;' id='guideline_8'>
					<?php echo $this->translate('<b>For this page, place the Slideshow code in the template file: '). $this->translate('"/application/modules/Event/views/scripts/index/manage.tpl"</b><p>Example: In this example, we show how to place an ordinary Slideshow (non-featured, non-sponsored) in the left side of Event manage page. First, create a Slideshow from the Manage Slideshows page for a "Non-Widgetized Page", and "Non-Widgetized Page: Extended Right / Left Slideshow" block position. Then, copy the code for this Slideshow from the Manage Slideshows page. Now:</p><br /><p>1. Open the template file and search for the line given below:</p><div class="code"> &lt;div class=\'layout_middle\'&gt;</div><p><br />2. Now insert your Slideshow code, that was generated from the "Manage Slideshows" page using \'code\' option against this slideshow, just after the above mentioned line.</p><p><b>Example Code</b></p><div class="code">&lt;?php echo $this->content()->renderWidget("advancedslideshow.extended3-advancedslideshows");?&gt;</div>');?>
				</div>
			</li>
			
			<li>
				<a href="javascript:void(0);" onClick="guideline_show('guideline_9');"><?php echo $this->translate("Document manage Page");?></a>
				<div class='faq' style='display: none;' id='guideline_9'>
					<?php echo $this->translate('<b>For this page, place the Slideshow code in the template file: '). $this->translate('"/application/modules/Document/views/scripts/index/manage.tpl"</b><p>Example: In this example, we show how to place an ordinary Slideshow (non-featured, non-sponsored) in the left side of Document manage page. First, create a Slideshow from the Manage Slideshows page for a "Non-Widgetized Page", and "Non-Widgetized Page: Extended Right / Left Slideshow" block position. Then, copy the code for this Slideshow from the Manage Slideshows page. Now:</p><br /><p>1. Open the template file and search for the line given below:</p><div class="code"> &lt;div class=\'layout_middle\'&gt;</div><p><br />2. Now insert your Slideshow code, that was generated from the "Manage Slideshows" page using \'code\' option against this slideshow, just after the above mentioned line.</p><p><b>Example Code</b></p><div class="code">&lt;?php echo $this->content()->renderWidget("advancedslideshow.extended3-advancedslideshows");?&gt;</div>');?>
				</div>
			</li>
			
		</ul>
	</div>
</div>