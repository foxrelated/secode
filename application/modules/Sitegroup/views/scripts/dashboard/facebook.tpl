<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: foursquare.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Sitegroup/externals/styles/style_sitegroup_dashboard.css');
?>
<?php echo $this->form->setAttrib('class', 'global_form_popup global_form sitegroup_fbconnect_form')->render($this) ?>

<?php if (@$this->closeSmoothbox): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>
<style type="text/css">
  .global_form{width:550px;}
  .global_form > div{
  	float:none;
  }
</style>

  <script type="text/javascript">
     window.addEvent('domready', function() {
       if(typeof($('fbgroup_id')) != 'undefined') {
         $('show_facebookgroupid_createHelp').inject($('fbgroup_id-wrapper'), 'after');
         
       }
       
     });
  </script>  
<div id='show_facebookgroupid_createHelp' style='display:none;'>
 <div class="sitegroup_fbgroup_guidelines">
   <ul id="guideline_1">
     <li>
      
			
            	<?php echo $this->translate("Go to the above group URL which you have filled in 'Facebook Group URL' field and click on settings of this group. Now go to 'Report Group' section.");?>
   
      
     </li>
     <li>

            	<?php echo $this->translate("Click on the 'Report Group' link and open this in new tab of your browser window.");?>   

     </li>
      <li>

            	<?php echo $this->translate("You will be redirected to the URL which will looks like: https://www.facebook.com/ajax/report.php?content_type=1&cid=674772559253250. Now copy From this URL value of 'cid' which is <b> 674772559253250 </b> for this example URL. Now paste it to 'Facebook Group Id' field above.");?>   

     </li>
   </ul>
</div> 