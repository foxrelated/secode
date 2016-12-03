<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Mcard
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2010-10-13 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<style type="text/css">
#select_option-element label,
#select_option-element ul li{
	cursor:move;
}
</style>

<h2><?php echo $this->translate("Membership Cards Plugin") ?></h2>

<script type="text/javascript">
	var siteBaseUrl = "<?php echo $this->preview_base_url; ?>";
  var fetchLevelSettings =function(level_id){
    window.location.href = siteBaseUrl + '/admin/mcard/level/' + level_id;
  }
  var fetchProfileTypeSettings =function(mprofiletype_id){
    var level_id = "<?php echo $this->level->level_id; ?>";
    window.location.href = siteBaseUrl + '/admin/mcard/level/' + level_id + '/' + mprofiletype_id;
  }
</script>

<?php if (count($this->navigation)): ?>
  <div class='tabs'>
  <?php
  // Render the menu
  //->setUlClass()
  echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>

<?php endif; ?>

<div class='seaocore_settings_form'>
  <div class='settings' id="myFormId">
	  <iframe id='ajaxframe' name='ajaxframe' src='javascript:void(0);' height="0px" ></iframe>
	  <?php echo $this->form->render($this) ?>
	</div> 
</div>

</div>
<script type="text/javascript">
  // Show preview here set the all form element value with in a string and send all with the help of queary string.
  var showPreview =function()
  {
		var preview_value = "<?php echo $this->preview_value; ?>";
		var pre_level_id = "<?php echo $this->preview_level_id; ?>";
		var pre_mp_id = "<?php echo $this->preview_mp_id; ?>";
    // "Show Empty Fields" field set or not (If "Profile field value" not available then that profile field show or not.).
    if( $('empty_fields-1').checked ) {
      var empty_fields = 1;
    }
    else {
      var empty_fields = 0;
    }
    // Condition for "Show Logo" (Logo will display or not).
    if( $('logo_select-1').checked ) {
      var logo_select = 1;
    }
    else {
      var logo_select = 0;
    }
    // If set value of logo true then tack the uploaded image name.
    if( $('upload_logo_indication').value ) {
      var upload_logo_image = $('upload_logo_indication').value;
    }
    else {
      var upload_logo_image = 0;
    }
    // Condition for "Show Title" field.
    if( $('show_card_label-1').checked ) {
      var show_title = 1;
    }
    else {
      var show_title = 0;
    }
    // If set value of logo true then tack the uploaded image name.
    if( $('card_label').value ) {
      var show_label = $('card_label').value;
    }
    else {
      var show_label = 0;
    }
    // Find out "Profile Type" value which are set by site admin.
    var profile_type = '';
    $array_lenrth = $('profileform').elements['select_option[]'].length;
    for( var i = 0; i < $array_lenrth; i++ )
    {
      if( $('profileform').elements['select_option[]'][i].checked )
      {
	profile_type = profile_type + $('profileform').elements['select_option[]'][i].value + ',';
      }
    }
    // Condition for "Display Profile Photo" field.
    if( $('profile_photo-1').checked ) {
      var profile_photo_display = 1;
    }
    else {
      var profile_photo_display = 0;
    }
    // Find out "Information Content Color".
    var information_color = $('info_color').value.slice(1);
    // Find out the "Information Font Style".
    var information_font = $('info_font').value;
    // Condition for "Card Background" field.
    if( $('shiny_plastic_look-1').checked ) {
      var card_background = 1;
    }
    else {
      var card_background = 0;
    }
    // Find out the "Card Background Color".
    if( $('card_bg_color').value ) {
      var card_bg_color = $('card_bg_color').value.slice(1);
    }
    else {
      var card_bg_color = 0;
    }
    // Check the "Card Background Image".
    if( $('upload_card_bg_image').value ) {
      var card_background_image = $('upload_card_bg_image').value;
    }
    else {
      var card_background_image = 0;
    }
    // Set "Label Color (Title Color) or not."
    if( $('label_color').value ) {
      var title_color = $('label_color').value.slice(1);
    }
    else {
      var title_color = '';
    }
    var application_path = "<?php echo $this->preview_base_url; ?>";
    // This variable contain the all data of form which set by site admin as queary string.
    var query_string = '';    
    var query_string = 'level_id=' + $('level_id').value + '& profile_type=' + $('mptype_id').value + '& show_empty_field=' + empty_fields + '& logo_select=' + logo_select + '& show_title=' + show_title + '& title_color=' + title_color + '& title_font=' + $('label_font').value + '& profile_type=' + profile_type + '& profile_photo_display=' + profile_photo_display + '& information_color=' + information_color + '& information_font=' + information_font + '& card_background=' + card_background + '& upload_logo_image=' + upload_logo_image + '& show_title_value=' + show_label + '& card_bg_color=' + card_bg_color + '& card_background_image=' + card_background_image + '& pre_level_id=' + pre_level_id + '& pre_mp_id=' + pre_mp_id;
		//if( empty(preview_value) )	{
			window.open (application_path+"/admin/mcard/settings/preview?"+query_string, null, "width=450, height=400 resizable=0");
		//}
  }
</script>

<script type="text/javascript">

  //    On show_card_label option for loading card_label and else hiding it
  $$('input[type=radio]:([name=show_card_label])').addEvent('click', function(e){
    $(this).getParent('.form-wrapper').getAllNext(':([id^=card_label-wrapper])').setStyle('display', ($(this).get('value')>0?'none':'none'));
  });

  $('show_card_label-1').addEvent('click', function(){
    $('card_label-wrapper').setStyle('display', ($(this).get('value')>0?'block':'none'));
    $('label_color-wrapper').setStyle('display', ($(this).get('value')>0?'block':'none'));
    $('label_font-wrapper').setStyle('display', ($(this).get('value')>0?'block':'none'));
  });
  $('show_card_label-0').addEvent('click', function(){
    $('card_label-wrapper').setStyle('display', ($(this).get('value')<=0?'none':'block'));
    $('label_color-wrapper').setStyle('display', ($(this).get('value')<=0?'none':'block'));
    $('label_font-wrapper').setStyle('display', ($(this).get('value')<=0?'none':'block'));
  });
  $$('input[type=radio]:([name=logo_select])').addEvent('click', function(e){
    $(this).getParent('.form-wrapper').getAllNext(':([id^=logo-wrapper])').setStyle('display', ($(this).get('value')>0?'none':'none'));
  });

  //    On logo_select option for loading community logo and else hiding it
  $('logo_select-1').addEvent('click', function(){
    $('logo-wrapper').setStyle('display', ($(this).get('value')>0?'block':'none'));
  });
  $('logo_select-0').addEvent('click', function(){
    $('logo-wrapper').setStyle('display', ($(this).get('value')<=0?'none':'block'));
  });
  //        Used to display the corresponding item selected in radio button
  //        Either to pick card_bg_color or card_bg_image
  $$('input[type=radio]:([name=shiny_plastic_look])').addEvent('click', function(e){
    $(this).getParent('.form-wrapper').getAllNext(':([id^=card_bg_color-element])').setStyle('display', ($(this).get('value')>0?'none':'none'));
  });

  $('shiny_plastic_look-1').addEvent('click', function(){
    $('card_bg_color-wrapper').setStyle('display', ($(this).get('value')>0?'block':'none'));
    $('card_bg_image-wrapper').setStyle('display', ($(this).get('value')>0?'none':'block'));
  });
  $('shiny_plastic_look-0').addEvent('click', function(){
    $('card_bg_color-wrapper').setStyle('display', ($(this).get('value')<=0?'none':'block'));
    $('card_bg_image-wrapper').setStyle('display', ($(this).get('value')<=0?'block':'none'));
  });



  $$('input[type=radio]:([name=card_status])').addEvent('click', function(e){
    $(this).getParent('.form-wrapper').getAllNext(':([id^=card_bg_color-wrapper])').setStyle('display', ($(this).get('value')>0?'none':'none'));
  });
	
  $('card_status-1').addEvent('click', function(){

		$('shiny_plastic_look-wrapper').setStyle('display', ($(this).get('value')>0?'block':'none'));
		if( $('shiny_plastic_look-1').checked )
		{
			$('card_bg_color-wrapper').setStyle('display', ($(this).get('value')>0?'block':'none'));
		}
		else {
			$('card_bg_image-wrapper').setStyle('display', ($(this).get('value')>0?'block':'none'));
		}

		$('logo_select-wrapper').setStyle('display', ($(this).get('value')>0?'block':'none'));
		if( $('logo_select-1').checked ) {
			$('logo-wrapper').setStyle('display', ($(this).get('value')>0?'block':'none'));
		}
		$('show_card_label-wrapper').setStyle('display', ($(this).get('value')>0?'block':'none'));
		if( $('show_card_label-1').checked ) {
			$('card_label-wrapper').setStyle('display', ($(this).get('value')>0?'block':'none'));
			$('label_color-wrapper').setStyle('display', ($(this).get('value')>0?'block':'none'));
			$('label_font-wrapper').setStyle('display', ($(this).get('value')>0?'block':'none'));
		}
		$('profile_photo-wrapper').setStyle('display', ($(this).get('value')>0?'block':'none'));
		$('select_option-wrapper').setStyle('display', ($(this).get('value')>0?'block':'none'));
		$('empty_fields-wrapper').setStyle('display', ($(this).get('value')>0?'block':'none'));
		$('info_color-wrapper').setStyle('display', ($(this).get('value')>0?'block':'none'));
		$('info_font-wrapper').setStyle('display', ($(this).get('value')>0?'block':'none'));
		$('preview').setStyle('display', ($(this).get('value')>0?'block':'none'));	
  });
  $('card_status-0').addEvent('click', function(){
    $('card_bg_color-wrapper').setStyle('display', ($(this).get('value')<=0?'none':'block'));
		$('shiny_plastic_look-wrapper').setStyle('display', ($(this).get('value')<=0?'none':'block'));
		$('card_bg_image-wrapper').setStyle('display', ($(this).get('value')<=0?'none':'block'));
		$('logo_select-wrapper').setStyle('display', ($(this).get('value')<=0?'none':'block'));
		$('logo-wrapper').setStyle('display', ($(this).get('value')<=0?'none':'block'));
		$('show_card_label-wrapper').setStyle('display', ($(this).get('value')<=0?'none':'block'));
		$('card_label-wrapper').setStyle('display', ($(this).get('value')<=0?'none':'block'));
		$('label_color-wrapper').setStyle('display', ($(this).get('value')<=0?'none':'block'));
		$('label_font-wrapper').setStyle('display', ($(this).get('value')<=0?'none':'block'));
		$('profile_photo-wrapper').setStyle('display', ($(this).get('value')<=0?'none':'block'));
		$('select_option-wrapper').setStyle('display', ($(this).get('value')<=0?'none':'block'));
		$('empty_fields-wrapper').setStyle('display', ($(this).get('value')<=0?'none':'block'));
		$('info_color-wrapper').setStyle('display', ($(this).get('value')<=0?'none':'block'));
		$('info_font-wrapper').setStyle('display', ($(this).get('value')<=0?'none':'block'));
		$('preview').setStyle('display', ($(this).get('value')<=0?'none':'block'));
  });

  window.addEvent('domready', function() {
		// If set by site admin show membership card.
		var e6 = $('card_status-1');
			$('shiny_plastic_look-wrapper').setStyle('display', (e6.checked ?'block':'none'));			
			$('logo_select-wrapper').setStyle('display', (e6.checked ?'block':'none'));			
			$('show_card_label-wrapper').setStyle('display', (e6.checked ?'block':'none'));
			$('profile_photo-wrapper').setStyle('display', (e6.checked ?'block':'none'));
			$('select_option-wrapper').setStyle('display', (e6.checked ?'block':'none'));
			$('empty_fields-wrapper').setStyle('display', (e6.checked ?'block':'none'));
			$('info_color-wrapper').setStyle('display', (e6.checked ?'block':'none'));
			$('info_font-wrapper').setStyle('display', (e6.checked ?'block':'none'));
			$('preview').setStyle('display', (e6.checked ?'block':'none'));
		// If set by site admin dont show membership card.
		var e7 = $('card_status-0');
			$('shiny_plastic_look-wrapper').setStyle('display', (e7.checked ?'none':'blok'));
			$('card_bg_color-wrapper').setStyle('display', (e7.checked ?'none':'block'));			
			$('card_bg_image-wrapper').setStyle('display', (e7.checked ?'none':'block'));
			$('logo_select-wrapper').setStyle('display', (e7.checked ?'none':'block'));
			$('logo-wrapper').setStyle('display', (e7.checked ?'none':'block'));
			$('show_card_label-wrapper').setStyle('display', (e7.checked ?'none':'block'));
			$('card_label-wrapper').setStyle('display', (e7.checked ?'none':'block'));
			$('label_color-wrapper').setStyle('display', (e7.checked ?'none':'block'));
			$('label_font-wrapper').setStyle('display', (e7.checked ?'none':'block'));
			$('profile_photo-wrapper').setStyle('display', (e7.checked ?'none':'block'));
			$('select_option-wrapper').setStyle('display', (e7.checked ?'none':'block'));
			$('empty_fields-wrapper').setStyle('display', (e7.checked ?'none':'block'));
			$('info_color-wrapper').setStyle('display', (e7.checked ?'none':'block'));
			$('info_font-wrapper').setStyle('display', (e7.checked ?'none':'block'));
			$('preview').setStyle('display', (e7.checked ?'none':'block'));

			if( $('card_status-1').checked )	
			{
				var e4 = $('shiny_plastic_look-1');
				$('card_bg_color-wrapper').setStyle('display', (e4.checked ?'block':'none'));
				$('card_bg_image-wrapper').setStyle('display', (e4.checked ?'none':'block'));
				var e5 = $('shiny_plastic_look-0');
				$('card_bg_color-wrapper').setStyle('display', (e5.checked?'none':'block'));
				$('card_bg_image-wrapper').setStyle('display', (e5.checked?'block':'none'));
				var elabel1 = $('show_card_label-1');
				$('card_label-wrapper').setStyle('display', (elabel1.checked ?'block':'none'));
				$('label_color-wrapper').setStyle('display', (elabel1.checked ?'block':'none'));
				$('label_font-wrapper').setStyle('display', (elabel1.checked ?'block':'none'));
				var elabel0 = $('show_card_label-0');
				$('card_label-wrapper').setStyle('display', (elabel0.checked?'none':'block'));
				$('label_color-wrapper').setStyle('display', (elabel0.checked?'none':'block'));
				$('label_font-wrapper').setStyle('display', (elabel0.checked?'none':'block'));
				var elogo1 = $('logo_select-1');
				$('logo-wrapper').setStyle('display', (elogo1.checked ?'block':'none'));
				var elogo0 = $('logo_select-0');
				$('logo-wrapper').setStyle('display', (elogo0.checked?'none':'block'));
			}
  });
  
  /*    For drag and drop list to change their position in up and down direction
   *    if settings changed and user leaves the page then confirmation
   */
  var origOrder;
  var changeOptionsFlag = false;
  var saveFlag = false;
  function setFlag(){
    saveFlag = true;
  }
  window.addEvent('domready', function(){
    //         We autogenerate a list on the fly
    var initList = [];
    var li = $('select_option-element').getElementsByTagName('li');
    for (i = 1; i <= li.length; i++)
      initList.push(li[i]);
    origOrder = initList;
    var temp_array = $('select_option-element').getElementsByTagName('ul');
    temp_array.innerHTML = initList;
    new Sortables(temp_array);
  });

  window.onbeforeunload = function(event){
    var finalOrder = [];
    var li = $('select_option-element').getElementsByTagName('li');
    for (i = 1; i <= li.length; i++)
    {  finalOrder.push(li[i]);
    }

    for (i = 0; i <= li.length; i++){
      if(finalOrder[i]!=origOrder[i])
      {
        changeOptionsFlag = true;
        break;
      }
    }
        
    if(changeOptionsFlag && !saveFlag){
      var answer = confirm("Default setting changes detected!!! Press OK to save the new settings or CANCEL to leave without saving new changes.");

      if(answer){
        var myForm = $('myFormId').getElementsByClassName('global_form');
        myForm.submit();
      }
    }
  }
  var uploadProfilePhoto =function(){
    $('profileform').set('target', 'ajaxframe');
    $('profileform').set('name', 'profileform');
    $('upload_logo_indication').value = $('logo').value;
    document.profileform.submit();	      
  }

  var uploadProfilebgImage =function(){
    $('profileform').set('target', 'ajaxframe');
    $('profileform').set('name', 'profileform');
    $('upload_card_bg_image').value = $('card_bg_image').value;
    document.profileform.submit();
  }

  var mainFormSubmit =function() {
  $('upload_logo_indication').erase('value');
  $('upload_card_bg_image').erase('value');
  document.profileform.submit();
  }
</script>