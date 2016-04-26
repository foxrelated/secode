<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2><?php echo $this->translate('Suggestions / Recommendations Plugin')?></h2>
<?php if( count($this->navigation) ): ?>
<div class='tabs'>
    <?php
    // Render the menu
    echo $this->navigation()->menu()->setContainer($this->navigation)->render();
    ?>
</div>
<?php endif; ?>

<?php if($this->is_msg == 1): 
	echo '<ul class="form-notices" style="margin:0px;"><li>Data save successfuly.</li></ul>';
endif; ?>

<div class='clear'>
  <div class='settings'>
    <?php echo $this->form->render($this); ?>
  </div>
</div>
<style type="text/css">
#TB_window {
	top:350px !important;
	width:438px !important;
}
#TB_ajaxContent
{
 padding:0 !important;
 width:1px;
 height:auto !important;
 width:438px !important;
 overflow:auto;
 max-height:420px !important;
}
.sugg_newuser
{
	margin:0 !important;
	float:left;
	max-height:400px;
 	overflow:auto;
 	padding:10px 2px 10px 6px !important;
 	width:430px !important;
}
.sugg_newuser p
{
	float:left;
	font-size:12px;
	line-height:1.5em;
	text-align:left !important;
}
.sugg_newuser div
{
	margin:0px !important;
}
.sucess{
	background-color:#E9FAEB;
	background-image:url(<?php echo $this->layout()->staticBaseUrl ?>"application/modules/Suggestion/externals/images/admin/success.png");
	-moz-border-radius:3px 3px 3px 3px;
	background-position:8px 5px;
	background-repeat:no-repeat;
	border:1px solid #CCCCCC;
	clear:left;
	float:left;
	margin:0px 5px 7px;
	overflow:hidden;
	padding:5px 15px 5px 32px;
}
.settings .form-wrapper {
	width:800px;
}   
.form-sub-heading{
	clear:both;
}
.settings .form-description {
	max-width: 800px;
}
.settings .form-element .description {
	max-width: 550px;
}
</style>
<script type="text/javascript">
	$$('input[type=radio]:([name=message])').addEvent('click', function(e){
	    $(this).getParent('.form-wrapper').getAllNext(':([id^=content-wrapper])').setStyle('display', ($(this).get('value')>0?'none':'none'));
	});
	$('sugg_admin_introduction-1').addEvent('click', function(){
	    $('content-wrapper').setStyle('display', ($(this).get('value')>0?'block':'block'));
	    $('sugg_bg_color-wrapper').setStyle('display', ($(this).get('value')>0?'block':'block'));
	    $('sugg_preview').setStyle('display', ($(this).get('value')>0?'block':'block'));
	    $('sugg_or').setStyle('display', ($(this).get('value')>0?'block':'block'));
	});
	$('sugg_admin_introduction-0').addEvent('click', function(){
	    $('content-wrapper').setStyle('display', ($(this).get('value')>0?'none':'none'));
	    $('sugg_bg_color-wrapper').setStyle('display', ($(this).get('value')>0?'none':'none'));
	    $('sugg_preview').setStyle('display', ($(this).get('value')>0?'none':'none'));
	    $('sugg_or').setStyle('display', ($(this).get('value')>0?'none':'none'));
	});
	
	window.addEvent('domready', function() { 
	var e4 = $('sugg_admin_introduction-1');
	$('content-wrapper').setStyle('display', (e4.checked ?'block':'none'));
	$('sugg_bg_color-wrapper').setStyle('display', (e4.checked ?'block':'none'));
	$('sugg_preview').setStyle('display', (e4.checked ?'block':'none'));
	$('sugg_or').setStyle('display', (e4.checked ?'block':'none'));
	var e5 = $('sugg_admin_introduction-0');
	$('content-wrapper').setStyle('display', (e5.checked?'none':'block'));
	$('sugg_bg_color-wrapper').setStyle('display', (e5.checked?'none':'block'));
	$('sugg_preview').setStyle('display', (e5.checked?'none':'block'));
	$('sugg_or').setStyle('display', (e5.checked?'none':'block'));
	});
</script>