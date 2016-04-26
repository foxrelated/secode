<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: defaultcssfile.tpl 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="global_form_popup">
	<h3><?php echo $this->translate( "CSS code for Like Button" ) ; ?></h3>
	<div>
	  <ul>
	    <li class="mtop10">
	      <?php echo $this->translate("First, search lines: '/* SEAO like button start here */' and '/* SEAO like button end here */', in theme.css avaliable in the Layout >> Theme Editor >> Editing File section. If found, then please remove code written between both the lines. <br /> Now, please copy the below css code and paste this code in your 'theme.css' avaliable in the Layout >> Theme Editor >> Editing File section." ) ; ?>
	    </li>
	    <li>
	      <textarea spellcheck="false" class="text-box" onclick="this.focus(); this.select()"><?php include APPLICATION_PATH . '/application/modules/Sitelike/externals/styles/likesettings.css'; ?></textarea>
	    </li>
	    <li class="mtop10">
	      <button onclick="parent.Smoothbox.close();" ><?php echo $this->translate( 'Okay' ) ?></button>
	    </li>
	  </ul>
	</div>
</div>
<style type="text/css">
.text-box{
	border: 2px solid #ccc; 
	height: 100px;
	margin-top: 10px;
	padding: 5px;
	width: 600px;
}
</style>