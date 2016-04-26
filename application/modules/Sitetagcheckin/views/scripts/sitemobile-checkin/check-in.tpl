<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-05-07 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
  var statusHtml = '';
</script>
<div class="activity-post-container-input ui-body-b" style="display:none;" >

  <input type="text" placeholder="<?php echo $this->translate('Post Something...'); ?>"/>
</div>
<div class="activitypost-container" style="display:none;" >
  <?php $composerOptions = $this->settingsApi->getSetting('advancedactivity.composer.options', array("withtags", "emotions", "userprivacy")); ?>
  <form method="post" action="<?php echo $this->url(array('module' => 'sitetagcheckin', 'controller' => 'checkin', 'action' => 'check-in',
      'resource_type' => $this->resource_type, 'resource_id' => $this->resource_id, "checkin_use" => $this->checkin_use, 'checkin_verb' => $this->checkin_verb, 'checkedinto_verb' => $this->checkedinto_verb, 'tab' => $this->tab, 'checkin_your' => $this->checkin_your), 'default', true) ?>" class="activity" enctype="application/x-www-form-urlencoded" id="seaocheckinform" >
        <?php //STATUS BOX HEADER ?>

    <div class="ui-header ui-bar-a o_hidden" id="ui-header">
      <a href="#" data-icon="false" data-role="button" href="" data-corners="true" data-shadow="true" data-iconshadow="true" data-wrapperels="span" data-theme="a" class="ui-btn-left" onclick="statusHtml='';$('#activitypost-container-temp').css('display', 'none');" data-rel="back"><?php echo $this->translate('Cancel'); ?></a>
      <?php if ($this->resource_type != 'user'): ?>
        <h2 class="ui-title" role="heading" aria-level="1">
          <?php echo $this->translate('Publish an Update for your Action'); ?>
        </h2>
      <?php else: ?>
        <h2 class="ui-title" role="heading" aria-level="1">
          <?php echo $this->translate('Add Your Location on Map'); ?>
        </h2>
      <?php endif; ?>

      <button id="compose-submit" type="submit" data-theme="b" data-role="button" class="ui-btn-right"><?php echo $this->translate($this->checkin_verb) ?></button>
    </div>
    <?php //CHECKIN HEADER  ?>

    <div class="ui-header ui-bar-a o_hidden" id="ui-header-checkin" style="display:none;">
      <a data-icon="false" data-role="button" href="" data-corners="true" data-shadow="true" data-iconshadow="true" data-wrapperels="span" data-theme="a" class="ui-btn-left" onclick="sm4.activity.toggleFeedArea(this,false, 'checkin');"><?php echo $this->translate('Cancel'); ?></a>
      <h2 class="ui-title" role="heading" aria-level="1"><?php echo $this->translate('Where are you?'); ?></h2>

    </div>
    <?php //ADD PEOPLE HEADER  ?>

    <div class="ui-header ui-bar-a o_hidden" id="ui-header-addpeople" style="display:none;">
      <a data-icon="false" data-role="button" href="" data-corners="true" data-shadow="true" data-iconshadow="true" data-wrapperels="span" data-theme="a" class="ui-btn-left" onclick="sm4.activity.toggleFeedArea(this,false, 'addpeople');"><?php echo $this->translate('Cancel'); ?></a>
      <h2 class="ui-title" role="heading" aria-level="1"><?php echo $this->translate('Who are you with?'); ?></h2>	
      <button id="compose-submit" data-theme="b" data-role="button" class="ui-btn-right" onclick="sm4.activity.composer.addpeople.addFriends();return false;"><?php echo $this->translate("Done") ?></button>
    </div>
    <?php if ($this->resource_type == 'user'): ?>
      <input type="hidden" name="checkinstr_status" value="" id="checkinstr_status" />				  
    <?php endif; ?>
    <div class="sm-post-wrap ui-page-content ui-body-c">
      <textarea id="activity_body" cols="1" rows="1" name="body" ></textarea>

      <?php if ($this->checkin_use): ?>
        <div id="sitetagcheckin_current_date" class="sm_stcheckin_show_date" onclick="showDateFields();">
          <span><a><?php echo date("F j, Y", strtotime(date('Y-m-d'))); ?></a></span>
          <span onclick="showDateFields();" class="stcheckin_post_edit_date"></span>
        </div>
      <?php endif; ?>
      <?php //CHECKIN DATE WORK STRTS HERE..... ?>              
      <?php if ($this->checkin_use): ?>
        <div class="sm_stcheckin_show_date" style="display:none;" id="sitetag_checkin_post_date">
          <select id="year" name="year" data-mini="true" data-role="none">
            <option label="Year" value="Year" disabled="disabled"><?php echo $this->translate('Year'); ?></option>
            <?php $curYear = date('Y'); ?>
            <?php for ($i = 0; $i <= 110; $i++) : ?>
              <option label="<?php echo $curYear; ?>" value="<?php echo $curYear; ?>" <?php if ($i == 0): ?> selected="selected" <?php endif; ?>><?php echo $curYear; ?></option>
              <?php $curYear--; ?>
            <?php endfor; ?>
          </select>

          <a onblur="setTimeMonth();" onclick="showMonth(0);" href="javascript:void(0);" id="addmonth" style="display:none;"><?php echo $this->translate('+ Add Month'); ?></a>
          <select id="month" name="month" onblur="showAddmonth(2)" onclick="showMonth(1)" onchange="showAddday(2)" style="display:block;" data-mini="true" data-role="none">
            <option label="Month" value="0"><?php echo $this->translate('Month'); ?></option>
            <?php $curMonth = (int) date('m'); ?>
            <?php for ($k = 1; $k <= 12; $k++): ?>
              <?php $month = date('F', mktime(0, 0, 0, $k, 1)); ?>
              <option label="<?php echo $month; ?>" value="<?php echo $k; ?>" <?php if ($k == $curMonth): ?> selected="selected" <?php endif; ?>><?php echo $month; ?></option>
            <?php endfor; ?>
          </select>

          <a style="display:none;" id="addday" onblur="setTime();" onclick="showDay(0);" href="javascript:void(0);"><?php echo $this->translate('+ Add Day'); ?></a>
          <select id="day" name="day" style="display:block;" data-mini="true" data-role="none">
            <option label="Day" value="0"><?php echo $this->translate('Day'); ?></option>
            <?php $curDate = (int) date('d'); ?>
            <?php for ($k = 1; $k <= 31; $k++): ?>										
              <option label="<?php echo $k; ?>" value="<?php echo $k; ?>" <?php if ($k == $curDate): ?> selected="selected" <?php endif; ?>><?php echo $k; ?></option>
            <?php endfor; ?>
          </select>
        </div>
      <?php endif; ?>  
      <?php //CHECKIN DATE WORK ENDS HERE..... ?>   


      <div id="toValuesdone-wrapper" class="sm-post-show-tags" style="display:none;"></div> 
      <div class="compose_share_op" style="display:none;">
        <?php echo $this->partial('application/modules/Sitemobile/modules/Advancedactivity/views/scripts/_composerSocialServices.tpl', array()); ?>
      </div>

      <div class="compose_buttons"> 
        <div class="left_options">
          <?php if (in_array("withtags", $composerOptions)): ?>
            <a href="javascript:void(0);" data-role="none" onclick="$('.cm-icon-user').addClass('active');sm4.activity.composer.showPluginForm(this, 'addpeople');">
              <i class="cm-icons cm-icon-user"></i>
            </a>
          <?php endif; ?>

          <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitetagcheckin') && $this->resource_type == 'user'): ?>
            <a href="javascript:void(0);" data-role="none" onclick="$('.cm-icon-map-marker').addClass('active');sm4.activity.composer.showPluginForm(this, 'checkin');">
              <i class="cm-icons cm-icon-map-marker"></i>
            </a>
          <?php endif; ?>

          <?php
          $SEA_EMOTIONS_TAG = unserialize(SEA_EMOTIONS_TAG);
          if (in_array("emotions", $composerOptions) && $SEA_EMOTIONS_TAG && isset($SEA_EMOTIONS_TAG[0])):
            ?>
            <a href="javascript:void(0);" data-role="none" id="emoticons-button"  class="emoticons-button"  onclick="setEmoticonsBoard();sm4.activity.statusbox.toggleEmotions($(this));$('.cm-icon-emoticons').toggleClass('active');" >
              <i class="cm-icons cm-icon-emoticons"></i>
            </a>
          <?php endif; ?>
          <div id="composer-options">
            <?php if (Engine_Api::_()->sitemobile()->enableComposer('photo')) : ?>
              <a href="javascript:void(0);" onclick="$('#attachment-options').css('display', 'block'); $('#smactivityoptions-popup').css('display', 'none'); return sm4.activity.composer.showPluginForm(this, 'photo');" data-role="none">
                <i class="cm-icons cm-icon-photo"></i>
              </a>
            <?php endif; ?>
          </div>

        </div>
        <div class="right_options">	
          <a href="javascript:void(0);" data-role="none" class="right_button dnone" id= "socialshare-button" onclick="$('.compose_share_op').toggle();$('.cm-icon-share').toggleClass('active');">
            <i class="cm-icons cm-icon-share"></i>
          </a>
          <?php if ($this->showPrivacyDropdown): ?>
            <a href="javascript:void(0);" data-role="none" onclick="sm4.activity.statusbox.togglePrivacy($(this));" id="addprivacy">
              <span class="ui-icon ui-icon-caret-down"></span>
            </a>
          <?php endif; ?>
        </div>		
      </div>

      <div id="emoticons-board" class="compose_embox_cont ui-page-content" style="display:none;">
        <div class="sm-seaocore-embox">
          <span class="sm-seaocore-embox-arrow ui-icon ui-icon-caret-up"></span>
          <?php foreach ($SEA_EMOTIONS_TAG[0] as $tag_key => $tag): ?>         
            <span class="sm-seaocore-embox-icon" onmouseover='setEmotionLabelPlate("<?php echo $this->string()->escapeJavascript($this->translate(preg_replace("/__([^_]*)__([^_]*)__([^_]*)__/", "$3", $tag))) ?>","<?php echo $this->string()->escapeJavascript($tag_key) ?>")' onclick='addEmotionIcon("<?php echo $this->string()->escapeJavascript($tag_key) ?>")'  title="<?php echo $this->translate(preg_replace("/__([^_]*)__([^_]*)__([^_]*)__/", "$3", $tag)) . "&nbsp;" . $tag_key; ?>"><?php
          echo preg_replace("/__([^_]*)__([^_]*)__([^_]*)__/", "<img src=\"" . $this->layout()->staticBaseUrl . "application/modules/Seaocore/externals/emoticons/$1\" border=\"0\" alt=\"$2\" />", $tag);
            ?></span>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="composer_status_share_options ui-page-content" style="display:none;">
        <?php if ($this->showPrivacyDropdown): ?> 
          <?php $content = (isset($this->availableLabels[$this->showDefaultInPrivacyDropdown]) || !empty($this->privacylists) ) ? $this->showDefaultInPrivacyDropdown : $this->settingsApi->getSetting('activity.content', 'everyone'); ?> 
          <?php $availableLabels = $this->availableLabels; ?>
          <?php
          if (count($this->privacylists) > 1):
            $content = "friends";
          endif;
          ?>
          <div class="compose_pr_op ui-page-content" id="aaf_tabs_feed">
            <table width="100%" cellpadding="0" cellspacing="0">
              <?php $active_icon_class = ''; ?>
              <?php foreach ($availableLabels as $key => $value): ?>
                <tr onclick="sm4.activity.statusbox.addPrivacy('<?php echo $key; ?>')" id="cm-icon-<?php echo $key; ?>">
                  <td class="compose_pr_op_left">
                    <i class="cm-icons cm-icon-<?php echo $key; ?>"></i>
                  </td>
                  <td class="compose_pr_op_middle"><?php echo $this->translate($value); ?></td>
                  <td class="compose_pr_op_right">
                    <?php if ($content == $key): $active_icon_class = $key; ?>
                      <i class="ui-icon ui-icon-ok"></i>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
              <?php if ($this->enableList): ?>  
                <?php foreach ($this->lists as $list): ?>
                  <tr onclick="sm4.activity.statusbox.addPrivacy('<?php echo 'list_' . $list->list_id; ?>')" id="cm-icon-<?php echo 'list_' . $list->list_id; ?>" >
                    <td class="compose_pr_op_left">
                      <i class="cm-icons cm-icon-list"></i>
                    </td>
                    <td class="compose_pr_op_middle"><?php echo $this->translate($list->getTitle()); ?></td>
                    <td class="compose_pr_op_right">
                      <?php if ($content == $list->list_id): $active_icon_class = 'list'; ?>
                        <i class="ui-icon ui-icon-ok"></i>
                      <?php endif; ?>
                    </td>
                  </tr>                    
                <?php endforeach; ?>
              <?php endif; ?>
              <?php if ($this->enableNetworkList): ?>
                <?php foreach ($this->network_lists as $list): ?>
                  <tr onclick="sm4.activity.statusbox.addPrivacy('<?php echo "network_" . $list->getIdentity(); ?>')" id="cm-icon-<?php echo "network_" . $list->getIdentity(); ?>">
                    <td class="compose_pr_op_left">
                      <i class="cm-icons cm-icon-network-list"></i>
                    </td>
                    <td class="compose_pr_op_middle"><?php echo $this->translate($list->getTitle()); ?></td>
                    <td class="compose_pr_op_right">
                      <?php if ($content == "network_" . $list->getIdentity()): $active_icon_class = 'network-list'; ?>
                        <i class="ui-icon ui-icon-ok"></i>
                      <?php endif; ?>
                    </td>
                  </tr>                         
                <?php endforeach; ?>
              <?php endif; ?>

            </table>
          </div>          
        <?php else: ?>
          <?php
          $content = $this->settingsApi->getSetting('activity.content', 'everyone');
          $active_icon_class = 'everyone';
          ?>						
        <?php endif; ?>
      </div>
      <input type="hidden" id="auth_view" name="auth_view" value="<?php echo $content; ?>" />

      <div id="composer-checkin-tag" class="composer-options" style="display:none;">

        <input type="hidden" name="return_url" value="<?php echo $this->url() ?>" />
        <input type="hidden" name="activity_type" value="1" />
        <?php if ($this->viewer() && $this->subject() && !$this->viewer()->isSelf($this->subject())): ?>
          <input type="hidden" name="subject" value="<?php echo $this->subject()->getGuid() ?>" />
        <?php endif; ?>
        <?php if ($this->formToken): ?>
          <input type="hidden" name="token" value="<?php echo $this->formToken ?>" />
        <?php endif ?>

        <!-- Add Pwople -->






        <div class="adv_post_compose_menu" id="adv_post_container_icons">
          <span class="aaf_activaor_end" style="display:none;"></span>
        </div>
        <div class="compose-menu_before" >                    
        </div>       



        <div id="compose-menu" class="compose-menu ui-page-content">				
        </div>

      </div>     
    </div>

    <?php //ADD PEOPLE WORK STARTS HERE...?>


    <div id="adv_post_container_tagging" class="post_container_tags ui-page-content" style="display:none;" title="<?php echo $this->translate('Who are you with?') ?>" >
      <div id="aff_mobile_aft_search-element">
        <div class="sm-post-search-fields">
          <table width="100%">
            <tr>
              <td class="sm-post-search-fields-left">
                <input class="ui-input-field " type="text" autocomplete="off" value="" id="aff_mobile_aft_search" name="aff_mobile_aft_search" placeholder="Start typing a name..." data-role="none" />
              </td>
            </tr>
          </table>			
          <span role="status" aria-live="polite"></span>
        </div>
        <div class="sm-post-show-tags" id="toValues-temp-wrapper" style="border:none;">
          <div id="toValues-temp-element">
            <input type="hidden" id="toValues-temp" value=""  name="toValues-temp" />
            <input type="hidden" id="toValues" value=""  name="toValues" />
          </div>
        </div> 

      </div>
    </div>


    <?php //CHECKING WORK STARTS HERE...?>	
    <div id="sitetagchecking_mob"></div>		

  </form>

</div>

<script>
  sm4.core.runonce.add(function() {
<?php if ($this->showPrivacyDropdown): ?>
             var icon = $('<i />', { 
               'class' : 'cm-icons cm-icon-' + '<?php echo $active_icon_class; ?>'
             });
   
             $('.ui-responsive-panel,ui-page-active').find('#addprivacy').prepend(icon);
<?php endif; ?>
     //CHECK IF NO SOCIAL SERVICES IS COMING.
     if (typeof fb_loginURL != 'undefined' || typeof twitter_loginURL != 'undefined' || typeof linkedin_loginURL != 'undefined') {
       $('.ui-responsive-panel,ui-page-active').find('#socialshare-button').addClass('dblock');
       $('.ui-responsive-panel,ui-page-active').find('#socialshare-button').removeClass('dnone');
     }	
   });
   $(document).on('ready',function() { 
     //sm4.activity.setViewMore();

     //CHECK IF NO SOCIAL SERVICES IS COMING.
     if (typeof fb_loginURL != 'undefined' || typeof twitter_loginURL != 'undefined' || typeof linkedin_loginURL != 'undefined') {
       $('#socialshare-button').addClass('dblock');
       $('#socialshare-button').removeClass('dnone');
     }				
   });
   var hidePrivacyIconClickEnable=false;
<?php if (in_array("emotions", $composerOptions)) : ?>
    var hideEmotionIconClickEnable=false;
    function setEmoticonsBoard(){
      //   if(composeInstance)
      //    composeInstance.focus();
      $('#emotion_lable').html('');
      $('#emotion_symbol').html();
      hideEmotionIconClickEnable=true;    
      var  a=$('#emoticons-button');
      a.toggleClass('emoticons_active');
      a.toggleClass('');               
                 
    }

    function addEmotionIcon(iconCode){ 
      var  el=$('.compose_embox_cont');
      el.toggle();
      var content; 
      content=sm4.activity.getContent();
      content=content.replace(/(<br>)$/g, "");
      content =  content +' '+ iconCode; 
      sm4.activity.setContent(content);
          
    }
    //hide on body click
    $(document.body).unbind('click').bind('click', function(event){
      hideEmotionIconClickEvent();
      hidePrivacyIconClickEvent()       
    });
      
    function hideEmotionIconClickEvent(){ 
      if(!hideEmotionIconClickEnable && $('.compose_embox_cont')){ 
        $('.compose_embox_cont').css('display', 'none');      
      }
      hideEmotionIconClickEnable=false;
    }
     
    function setEmotionLabelPlate(lable,symbol){
      $('#emotion_lable').html(lable);
      $('#emotion_symbol').html(symbol);
    }
<?php endif; ?> 
 
    function hidePrivacyIconClickEvent(){
      if(!hidePrivacyIconClickEnable && $('.composer_status_share_options')){
        $('.composer_status_share_options').css('display', 'none');      
      }
      hidePrivacyIconClickEnable=false;
    }   
</script>

<script type="text/javascript">
   
  sm4.core.runonce.add(function() { 
    sm4.activity.toggleFeedArea_Dialoge('.activity-post-container-input',true, '');
    //sm4.activity.initialize($('#activity_body'), true);
    var url = sm4.core.baseUrl + 'advancedactivity/friends/suggest';
    sm4.core.Module.autoCompleter.attach("aff_mobile_aft_search", url, {
      'singletextbox': false, 
      'limit':10, 
      'minLength': 1, 
      'showPhoto' : true, 
      'search' : 'search'
    }, 'toValues-temp');
  }); 
      
      
</script>

<script type="text/javascript">

  var addDay=0;
  var addMonth=0;

  function showMonth(month) {
    addMonth=month;
    document.getElementById('addmonth').style.display = 'none';
    document.getElementById('month').style.display = 'block';
    $('#month').css('display', 'block');
    var sel = document.getElementById("month");
    var year = document.getElementById("year");
    var selectedTextYear = year.options[year.selectedIndex].text;
    var selectedValueYear = year.options[year.selectedIndex].value;
    var currentYear = '<?php echo (int) date("Y"); ?>'
    
    //get the selected option
    var selectedTextMonth = sel.options[sel.selectedIndex].text;
    var selectedValueMonth = sel.options[sel.selectedIndex].value;

    var selday = document.getElementById("day");
    //get the selected option
    selday.options[sel.selectedIndex].text = 0;
    selday.options[sel.selectedIndex].value = 0;   

    if(selectedTextMonth != 'Month') { 
      if(parseInt(selectedValueMonth) > parseInt('<?php echo (int) date("m"); ?>') && (currentYear == parseInt(selectedTextYear))) {
        sel.selectedIndex="Month";
        document.getElementById('addday').style.display = 'none';
        $('#day').css('display', 'none');

      } 
      else {
        document.getElementById('addday').style.display = 'block';
        //document.getElementById('day').style.display = 'none';
        $('#day').css('display', 'none');
      }
    } else {
      document.getElementById('addday').style.display = 'none';
      //document.getElementById('day').style.display = 'none';
      $('#day').css('display', 'none');
    }
  }

  function setTimeMonth() {
    setTimeout("showAddmonth(1)", 100);
  }

  function showAddmonth(month) {
    if(addMonth == 0 || month == 2) { 
      addMonth = 0;
      var sel = document.getElementById("month");
      //get the selected option
      var selectedText = sel.options[sel.selectedIndex].text;
      if(selectedText == 'Month') {
        document.getElementById('addmonth').style.display = 'block';
        $('#month').css('display', 'none');
        document.getElementById('addday').style.display = 'none';
        $('#day').css('display', 'none');
      }
    }
  }

  function showDay(day) {
    addDay=day;
    clear('day');
    
    document.getElementById('addday').style.display = 'none';
    document.getElementById('day').style.display = 'block';
    $('#day').css('display', 'block');
    addOption(document.getElementById('day'), '<?php echo $this->translate("Day"); ?>', 0);
    var month_day = document.getElementById('month').value;
    var year_day = document.getElementById('year').value;
    var num = new Date(year_day, month_day, 0).getDate();
<?php $curMonth = (int) date('m'); ?>
       var currentDate = '<?php echo (int) date('d'); ?>';
       if(month_day == '<?php echo (int) date("m"); ?>') {
         for(i=1; i<= currentDate; i++) {
           addOption(document.getElementById('day'), i, i);
         }
       } else {
         for(i=1; i<= num; i++) {
           addOption(document.getElementById('day'), i, i);
         }
       }
    
       if (day == 0) { 
         var currentDate = '<?php echo (int) date("d"); ?>';     
         $('#day').val(currentDate);
     
       }
     }
  

     if($('#day')) {

       $('#day').off('blur').on('blur', function(event){
         showAddday(2)
       });

       $('#day').off('click').on('click', function(event){
         showDay(1);
       });

       $('#day').off('change').on('change', function(event){
         showAddday(2)
       });
     }

     function addOption(selectbox,text,value )
     {  
       var optn = document.createElement("OPTION");
       optn.text = text;
       optn.value = value;
       selectbox.options.add(optn);
     }

     function clear(ddName)
     {
       for (var i = (document.getElementById(ddName).options.length-1); i >= 0; i--) 
       { 
         document.getElementById(ddName).options[ i ]=null; 
       } 
     }	

     function setTime() {
       setTimeout("showAddday(1)", 500);
     }

     function showAddday(day) {
       if(addDay == 0 || day == 2) { 
         addDay = 0;
         var sel = document.getElementById("day");
         //get the selected option
         var selectedText = sel.options[sel.selectedIndex].text;
         var selectedValue = sel.options[sel.selectedIndex].value;
         var selYear = document.getElementById("year");
         var currentYear = '<?php echo (int) date("Y"); ?>'
         var selectedTextYear = selYear.options[selYear.selectedIndex].text;
         var selectedYearValue = selYear.options[selYear.selectedIndex].value; 
         var selMonth = document.getElementById("month");
         var currentMonth = selMonth.options[selMonth.selectedIndex].text;
         var selectedMonthValue = selMonth.options[selMonth.selectedIndex].value; 
         if(selectedText == 'Day') {
           document.getElementById('addday').style.display = 'block';
           $('#day').parents('.ui-select').css('display', 'none');
         } 
         else {
           if(parseInt(selectedValue) > '<?php echo (int) date("d"); ?>' && (currentYear == parseInt(selectedTextYear)) && parseInt(selectedMonthValue) == '<?php echo (int) date("m"); ?>') {
             sel.selectedIndex="Day";
           } 
           else {
             document.getElementById('addday').style.display = 'none';
             $('#day').parents('.ui-select').css('display', 'block');
           }
         }
       }
     }

     function showDateFields() {
       document.getElementById('sitetag_checkin_post_date').style.display ="block";
       document.getElementById('sitetagcheckin_current_date').style.display ="none";
     }
     sm4.core.runonce.add(function() {   
       var requestOptions = {
         'photourl'  : sm4.core.baseUrl + '<?php echo $this->photoUploadUrl; ?>'           

       }
       sm4.activity.composer.init(requestOptions);
       $('#seaocheckinform').off('submit').on('submit', function (e) { 
         if (photoUpload == true) {e.preventDefault(); photoUpload = false;return false;}
         else { 
           statusHtml = '';
           $('#activitypost-container-temp').css('display', 'none');
      
         }
       });
  
<?php if ($this->resource_type != 'user'): ?>
        $('#activity_body').val("<?php echo $this->translate(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitetagcheckin.default.textarea.text', 'I am here!')); ?>")
<?php endif; ?>
    });
  
  
  
</script>