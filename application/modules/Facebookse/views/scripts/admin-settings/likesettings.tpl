<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: likesetitngs.tpl 6590 2011-01-06 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<style type="text/css">
.settings #submit-label{
	display:block;
	margin-bottom:25px;
}
#TB_window {
	background:#fff !important;
}
</style>

<h2><?php echo $this->translate('Advanced Facebook Integration / Likes, Social Plugins and Open Graph');?></h2>
<?php if( count($this->navigation) ): ?>
<div class='seaocore_admin_tabs'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<?php endif; ?>
<div class='clear'>
  <div class= '<?php if (Engine_Api::_()->getApi("settings", "core")->getSetting('fblike.type', 'default') == 'default'): echo "settings";else: echo "settings facebookse_likesetting_form";endif;?>'> 
		<?php echo $this->form->render($this) ?>
	</div>
	<?php $navigation_auth = Engine_Api::_()->getApi('settings', 'core')->getSetting('facebookse.navi.auth'); ?>
	<?php if (!empty($navigation_auth) && Engine_Api::_()->getApi("settings", "core")->getSetting('fblike.type', 'default') == 'default') : ?>	
<!--	<div class='settings facebookse_likesetting_formR'>
		<?php //echo $this->subform->render($this) ?>
	</div>-->
 <?php endif;?>
	<div id="show_likepreview" style="display:none">
		
</div>
<?php $curr_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->baseUrl();?>
<script type="text/javascript">
//<![CDATA[
var content_type = '<?php echo sprintf('%s', $this->content_type) ?>';
if (content_type != '') { 
  if ($('action_type'))
    setActionType($('action_type'));
	window.addEvent('domready', function() {
		showlikefields(<?php echo $this->likesetting_showlike; ?>);
		$(content_type + '_1-1').addEvent('click', function () {
			if (this.checked == true) {
				showlikefields (1);	
			}
		})
		$(content_type + '_1-0').addEvent('click', function () {
			if (this.checked == true) {
				showlikefields (0);	
			}
		})
   
	  if($('show_customicon-1') && $('show_customicon-1').checked == 0) {
      showLikeUnlikeIcons(0);
      
    }
	});
  
  var showlikefields = function(action) {
    $('likesetting_form').getElement('div.form-elements').getChildren('div').each(function(el, key) {
     
        if (el.getProperty('id') != 'pagelevel_id-wrapper' && el.getProperty('id') != content_type + '_1-wrapper' && el.getProperty('id') != 'buttons-wrapper' ) { 
           
           
            if (parseInt(action)) { 
              if (el.getProperty('id') == 'actiontype_custom-wrapper')
                setActionType($('action_type'));
              else
                el.style.display = 'block';           
            }
            else
              el.style.display = 'none';
        }
      
    });
    
    
    
  } 
  
 
}

var fetchLikeSettings =function(pagelevel_id) {
	if (pagelevel_id != 0) {
	  window.location.href= en4.core.baseUrl+'admin/facebookse/settings/likes/'+pagelevel_id;
	}
	else {
	  window.location.href= en4.core.baseUrl+'admin/facebookse/settings/likes/';
	}
   
  }
  
  function get_code () {  	
	var url =   en4.core.baseUrl+'admin/facebookse/settings/getlikecode';
	url += '?' + $('likebutton_config').toQueryString() ;
	Smoothbox.open(url);

  }

var show_likepreview = function () {
  var plugin_type = '';
	var verbtodisplay = 'like';
	var fb_height = '250';
	var show_faces = true;
  var send_button = true;
	var likefont = '';
	var like_color = 'light';
  var layout_style = 'standard';
  if ($('pagelevel_id')) {
    plugin_type = $('pagelevel_id').value;
   }
	if ($(plugin_type + '_1-1').checked == true) {

	if ($(plugin_type + '_2')) {
     verbtodisplay = $(plugin_type + '_2').value;
  }

	if ($(plugin_type + '_3-1').checked == false) {
     show_faces = false;
  }
  if ($(plugin_type + '_4-1').checked == false) {
     send_button = false;
  }
	
  if ($('layout_style')) {
    layout_style = $('layout_style').value;
  }
	if ($('likefont')) {
    likefont = $('likefont').value;
  }

	if ($('widget_font')) {
     var widget_font = $('widget_font').value;
  }

	if ($('like_color')) {
    like_color = $('like_color').value;
  }

		
  if (plugin_type != '') {
    var url =   en4.core.baseUrl+'admin/facebookse/settings/showlikepreview';
		url += '?href="<?php echo $curr_url;?>"&layout=' + layout_style + '&show_faces=' + show_faces +'&action=' + verbtodisplay +'&font=' + likefont +'&colorscheme=' + like_color + '&send_button=' + send_button  ;
		Smoothbox.open(url);
  }
 }
}


function setActionType(self) {
  
  if (self.value === 'custom') {
   
    $('actiontype_custom-wrapper').style.display = 'block';
    $('actiontype_custom-wrapper').focus();
    if ($('objecttype_custom').value == 'object')
      $('objecttype_custom').value = '';
  }
  else {
    $('actiontype_custom-wrapper').style.display = 'none';
    $('objecttype_custom').value = 'object';
  }
  
  
}

function showLikeUnlikeIcons(action) { 
 var display = action == true ? 'block' : 'none'; 
 $('fbbutton_likeicon-wrapper').setStyle('display', display);
 $('fbbutton_likeicon_preview-wrapper').setStyle('display', display);
 $('fbbutton_unlikeicon-wrapper').setStyle('display', display);
 $('fbbutton_unlikeicon_preview-wrapper').setStyle('display', display);
}

function updateTextFields(option) {
 
  if($('fbbutton_likeicon_preview-element')) {
    $('fbbutton_likeicon_preview-element').innerHTML = "<img src='" + option + "' width='13' height='13' >" ;
  }
}


function updateTextFields1(option1) {  
  if($('fbbutton_unlikeicon_preview-element')) {
    $('fbbutton_unlikeicon_preview-element').innerHTML = "<img src='" + option1 + "' width='13' height='13' >" ;
  }
}

//]]>
</script>