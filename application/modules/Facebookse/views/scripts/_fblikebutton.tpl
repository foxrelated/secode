<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Facebookse
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _fblikebutton.tpl 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<div class="form-wrapper" id="actiontype_custom-wrapper" style="display: block;"><div class="form-label" id="actiontype_custom-label">&nbsp;</div>
<div class="form-element" id="actiontype_custom-element"><p class="description">Specify the custom action type which you have created in your app dashboard.</p>

<input type="text" value="" id="actiontype_custom" name="actiontype_custom"></div></div>
<script type="text/javascript">
  window.addEvent('domready', function() {
    setActionType($('action_type'));
    if($('show_customicon-1') && $('show_customicon-1').checked == 0)  
      showLikeUnlikeIcons(0);
    if($('fbbutton_likeicon_preview')) {
      $('fbbutton_likeicon_preview').src = en4.core.staticBaseUrl + $('fbbutton_likeicon').value;
      $('fbbutton_unlikeicon_preview').src = en4.core.staticBaseUrl + $('fbbutton_unlikeicon').value;
    }
    
  });
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
</script>