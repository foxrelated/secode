<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesvideo
 * @package    Sesvideo
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: create-slide.tpl 2015-10-11 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */

?>
<?php
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/jscolor/jscolor.js');
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sesbasic/externals/scripts/jquery.min.js');
?>
<?php include APPLICATION_PATH .  '/application/modules/Sesvideo/views/scripts/dismiss_message.tpl';?>
<div class="sesbasic_search_reasult">
	<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'sesvideo', 'controller' => 'manage-slide', 'action' => 'manage','id'=>$this->gallery_id), $this->translate("Back to Manage Videos and Photos") , array('class'=>'sesbasic_icon_back buttonlink')); ?>
</div>
<div class='clear'>
  <div class='settings sesbasic_admin_form'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>

<script type="application/javascript">
function sign_button(value){
	if(value == 1)
		jqueryObjectOfSes('div[id^="signup_button_"]').show();
	else
		jqueryObjectOfSes('div[id^="signup_button_"]').hide();
	jqueryObjectOfSes('#signup_button-wrapper').show();
}
function log_button(value){
	if(value == 1)
		jqueryObjectOfSes('div[id^="login_button_"]').show();
	else
		jqueryObjectOfSes('div[id^="login_button_"]').hide();
	
	jqueryObjectOfSes('#login_button-wrapper').show();
}
function register_form(value){
	if(value == 1)
		jqueryObjectOfSes('#register_position-wrapper').show();
	 else
	 	jqueryObjectOfSes('#register_position-wrapper').hide();
}
function extra_buton(value){
	if(value == 1)
		jqueryObjectOfSes('div[id^="extra_button_"]').show();
	else
		jqueryObjectOfSes('div[id^="extra_button_"]').hide();
	
	jqueryObjectOfSes('#extra_button_-wrapper').show();
}
sign_button(jqueryObjectOfSes('#login_button').val());
log_button(jqueryObjectOfSes('#signup_button').val());
register_form(jqueryObjectOfSes('#show_register_form').val());
extra_buton(jqueryObjectOfSes('#extra_button').val());
</script>