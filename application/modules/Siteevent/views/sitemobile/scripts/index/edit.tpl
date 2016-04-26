<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$defaultProfileFieldId = Engine_Api::_()->getDbTable('metas', 'siteevent')->defaultProfileId();
$defaultProfileFieldId = "0_0_$defaultProfileFieldId";
?>
<?php
$this->headTranslate(array('edit', 'Date & Time', 'on the following days', 'Specific dates and times are set for this event.'));
?>

<?php
$cateDependencyArray = Engine_Api::_()->getDbTable('categories', 'siteevent')->getCatDependancyArray();
$subCateDependencyArray = Engine_Api::_()->getDbTable('categories', 'siteevent')->getSubCatDependancyArray();
//$this->headScript()
//        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
//        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
//        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
//        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js')
//        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/scripts/core.js')
//        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/scripts/_commonFunctions.js');
?>

<?php
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()
        ->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey")
?>
<!--WE ARE NOT USING STATIC BASE URL BECAUSE SOCIAL ENGINE ALSO NOT USE FOR THIS JS-->
<!--CHECK HERE Engine_View_Helper_TinyMce => protected function _renderScript()-->
<?php $this->tinyMCESEAO()->addJS(); ?>

<script type="text/javascript">
    var editFullEventDate = '<?php echo (bool) $this->editFullEventDate; ?>';
    sm4.core.runonce.add(function()
    {

        checkDraft();
        setDefaultEndDate();
        var defaultProfileId = '<?php echo $defaultProfileFieldId ?>' + '-wrapper';
        if ($.type($.mobile.activePage.find('#' + defaultProfileId)) && typeof $.mobile.activePage.find('#' + defaultProfileId) != 'undefined') {
            $.mobile.activePage.find('#' + defaultProfileId).css('display', 'none');
        }

        $("#dailyrepeat_interval-wrapper").css("display", "none");
        $("#daily_repeat_time-wrapper").css("display", "none");
        $("#id_weeklyrepeat_interval_select-wrapper").css("display", "none");
        $("#weekly_repeat_time-wrapper").css("display", "none");
        $("#id_monthlyabsolute_day_select-wrapper").css("display", "none");
        $("#id_monthlyrepeat_interval_select-wrapper").css("display", "none");
        $("#monthly_repeat_time-wrapper").css("display", "none");

        sm4.core.Module.autoCompleter.attach("tags", '<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'index', 'action' => 'tag-suggest'), 'default', true) ?>', {'singletextbox': true, 'limit': 10, 'minLength': 1, 'showPhoto': false, 'search': 'text'}, 'toValues');

    });

    function checkDraft() {
        if ($.mobile.activePage.find('#draft')) {
            if ($.mobile.activePage.find('#draft').val() == 1) {
                $.mobile.activePage.find("#search-wrapper").css('display', 'none');
                $.mobile.activePage.find("#search").attr("checked", false);

                if ($.mobile.activePage.find("#creation_date-wrapper")) {
                    $.mobile.activePage.find("#creation_date-wrapper").css('display', 'none');
                }

            } else {
                $.mobile.activePage.find("#search-wrapper").css('display', 'block');
                $.mobile.activePage.find("#search").attr("checked", true);

                if ($.mobile.activePage.find("#creation_date-wrapper")) {
                    $.mobile.activePage.find("#creation_date-wrapper").css('display', 'block');
                }

            }
        }
    }

    $(window).bind('domready', function() {
<?php if ($this->profileType): ?>
            $.mobile.activePage.find('#' + '<?php echo '0_0_' . $this->defaultProfileId ?>').value = <?php echo $this->profileType ?>;
            changeFields($.mobile.activePage.find('#' + '<?php echo '0_0_' . $this->defaultProfileId ?>'));
<?php endif; ?>
    });

//  if( '<?php // echo $this->show_editor ; ?>' == 1 ) {
//    //sm4.core.runonce.add(function(){  
//     setTimeout(function() {
//       sm4.core.tinymce.showTinymce($.mobile.activePage.find('#body')[0]);
//       }, 1000);
//    //});
//  } 


</script>

<?php
/* Include the common user-end field switching javascript */
echo $this->partial('_jsSwitch.tpl', 'fields', array(
        //'topLevelId' => (int) @$this->topLevelId,
        //'topLevelValue' => (int) @$this->topLevelValue
))
?>

<?php // include_once APPLICATION_PATH . '/application/modules/Siteevent/views/scripts/_DashboardNavigation.tpl'; ?>
<div class="siteevent_dashboard_content">

    <?php // echo $this->partial('application/modules/Siteevent/views/scripts/dashboard/header.tpl', array('siteevent' => $this->siteevent)); ?>
    <div class="siteevent_event_form">
        <?php
        if (!$this->editFullEventDate && $this->form->starttime) {
            $this->form->removeElement('starttime');
        }
        echo $this->form->render();
        ?>
    </div>	
</div>

<?php
if (!$this->editFullEventDate):

    $startDateTimeInfo = explode(" ", $this->startdate);
    $startdate = $startDateTimeInfo[0];
    $startTimeInfo = explode(":", $startDateTimeInfo[1]);
    $startHours = $startTimeInfo[0];
    $startMinutes = $startTimeInfo[1];
    if (isset($startTimeInfo[2]))
        $startAMPM = $startTimeInfo[2];
    ?>
    <div style="display:none;" id="siteevent_editstarttime">

        <div class="form-wrapper" id="starttime-wrapper"><div class="form-label" id="starttime-label"><label class="optional" for="starttime"><?php echo $this->translate('Start Time'); ?></label></div>
            <div class="form-element" id="starttime-element">
                <div style="display:inline" class="event_calendar_container"><button type="button" class="event_calendar disabled"></button><input type="hidden" id="starttime" class="" value="<?php echo $this->startdate_hidden; ?>" name="starttime" readonly="">
                    <input type="hidden" id="starttime-date" class="" value="<?php echo $startdate; ?>" name="starttime-date" readonly="">
                    <span id="calendar_output_span_starttime-date" class="calendar_output_span"><?php echo $startdate; ?></span></div>

                <select disabled="disabled" id="starttime-hour">
                    <option label="" value="<?php echo $startHours; ?>"><?php echo $startHours; ?></option>

                </select><select disabled="disabled" id="starttime-minute">

                    <option label="00" value="<?php echo $startMinutes; ?>"><?php echo $startMinutes; ?></option>

                </select>
                <?php if (!$this->locale()->useMilitaryTime()): ?>
                    <select disabled="disabled" id="starttime-ampm">  

                        <option label="PM" value="<?php echo $startAMPM; ?>"><?php echo $startAMPM; ?></option>
                    </select>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php $coreSettings = Engine_Api::_()->getApi('settings', 'core'); ?>
<script type="text/javascript">


//    var eventRepeat = '<?php //echo (bool) Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.repeat', 1);    ?>';
//    var eventRepeatParams = '<?php echo $this->siteevent->repeat_params; ?>';
//    if (eventRepeat == 0 && eventRepeatParams == '' && $('eventrepeat_id-wrapper')) {
//        $('eventrepeat_id-wrapper').style.display = 'none';
//    }

<?php if (!$this->editFullEventDate): ?>

        Elements.from($('siteevent_editstarttime').innerHTML).reverse().inject($('endtime-wrapper'), 'before');
<?php endif; ?>

    var prefieldForm = function() {
<?php
$defaultProfileId = "0_0_" . $this->defaultProfileId;
foreach ($this->form->getSubForms() as $subForm) {
    foreach ($subForm->getElements() as $element) {

        $elementGetName = $element->getName();
        $elementGetValue = $element->getValue();
        $elementGetType = $element->getType();

        if ($elementGetName != $defaultProfileId && $elementGetName != '' && $elementGetName != null && $elementGetValue != '' && $elementGetValue != null) {

            if (!is_array($elementGetValue) && $elementGetType == 'Engine_Form_Element_Radio') {
                ?>
                        $('<?php echo $elementGetName . "-" . $elementGetValue ?>').checked = 1;
            <?php } elseif (!is_array($elementGetValue) && $elementGetType == 'Engine_Form_Element_Checkbox') { ?>
                        $('<?php echo $elementGetName ?>').checked = <?php echo $elementGetValue ?>;
                <?php
            } elseif (is_array($elementGetValue) && ($elementGetType == 'Engine_Form_Element_MultiCheckbox' || $elementGetType == 'Fields_Form_Element_Ethnicity' || $elementGetType == 'Fields_Form_Element_LookingFor' || $elementGetType == Fields_Form_Element_PartnerGender)) {
                foreach ($elementGetValue as $key => $value) {
                    ?>
                            $('<?php echo $elementGetName . "-" . $value ?>').checked = 1;
                    <?php
                }
            } elseif (is_array($elementGetValue) && $elementGetType == 'Engine_Form_Element_Multiselect') {
                foreach ($elementGetValue as $key => $value) {
                    $key_temp = array_search($value, array_keys($element->options));
                    if ($key !== FALSE) {
                        ?>
                                $('<?php echo $elementGetName ?>').options['<?php echo $key_temp ?>'].selected = 1;
                        <?php
                    }
                }
            } elseif (!is_array($elementGetValue) && ($elementGetType == 'Engine_Form_Element_Text' || $elementGetType == 'Engine_Form_Element_Textarea' || $elementGetType == 'Fields_Form_Element_AboutMe' || $elementGetType == 'Fields_Form_Element_Aim' || $elementGetType == 'Fields_Form_Element_City' || $elementGetType == 'Fields_Form_Element_Facebook' || $elementGetType == 'Fields_Form_Element_FirstName' || $elementGetType == 'Fields_Form_Element_Interests' || $elementGetType == 'Fields_Form_Element_LastName' || $elementGetType == 'Fields_Form_Element_Location' || $elementGetType == 'Fields_Form_Element_Twitter' || $elementGetType == 'Fields_Form_Element_Website' || $elementGetType == 'Fields_Form_Element_ZipCode')) {
                ?>
                        $('<?php echo $elementGetName ?>').value = "<?php echo $this->string()->escapeJavascript($elementGetValue, false) ?>";
            <?php } elseif (!is_array($elementGetValue) && $elementGetType != 'Engine_Form_Element_Date' && $elementGetType != 'Fields_Form_Element_Birthdate' && $elementGetType != 'Engine_Form_Element_Heading') { ?>
                        $('<?php echo $elementGetName ?>').value = "<?php echo $this->string()->escapeJavascript($elementGetValue, false) ?>";
                <?php
            }
        }
    }
}
?>
    }
    var subcatid = '<?php echo $this->subcategory_id; ?>';

    var cateDependencyArray = '<?php echo json_encode($cateDependencyArray); ?>';

    var submitformajax = 0;
    var show_subcat = 1;
    var cateDependencyArray = new Array();
    var subCateDependencyArray = new Array();
<?php foreach ($cateDependencyArray as $cat) : ?>
        cateDependencyArray.push(<?php echo $cat ?>);
<?php endforeach; ?>
<?php foreach ($subCateDependencyArray as $cat) : ?>
        subCateDependencyArray.push(<?php echo $cat ?>);
<?php endforeach; ?>

<?php if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.categoryedit', 1) && !empty($this->siteevent->category_id)) : ?>
        show_subcat = 0;
<?php endif; ?>

    sm4.core.runonce.add(function() {
<?php if ($coreSettings->getSetting('siteevent.onlineevent.allow', 1) == 1 || $this->siteevent->is_online == 1) : ?>
            //ADD A LINK WITH VENUE NAME FIELD:
            var language = sm4.core.language.translate("online event");
            if ($('#venue_name-element').length > 0) {
                $("#venue_name-element").append("<div id='myDiv'>Running an <a href='javascript:void(0);'  name='online_event' onclick='siteeventCreateIsOnline(true);return false;'>" + language + "</a> </div>");
                siteeventCreateIsOnline(false);
    <?php if (!empty($_POST) && !empty($_POST['is_online'])) : ?>
                    siteeventCreateIsOnline(true);
    <?php endif; ?>
            }
<?php endif; ?>

        //IF EVENT IS ONLINE THEN HIDE THE LOCATION
<?php if ($this->siteevent->is_online == 1): ?>
            if ($('#venue_name-wrapper'))
                $('#venue_name-wrapper').css('display', 'none');
            if ($('#location-wrapper'))
                $('#location-wrapper').css('display', 'none');
            //$('location_map-wrapper').setStyle('display', 'none');
            if ($('#online_events-wrapper'))
                $('#online_events-wrapper').css('display', 'block');
<?php endif; ?>

//        initializeCalendarOnEdit();
    });

    function siteeventCreateIsOnline(isonline) {
        if (isonline == true) {
            $('#venue_name-wrapper').css('display', 'none');
            $('#online_events-wrapper').css('display', 'block');
            if ($('#location-wrapper'))
                $('#location-wrapper').css('display', 'none');
            if ($('#location_map-wrapper'))
                $('#location_map-wrapper').css('display', 'none');
            $('#is_online').val(1);
        } else {
            $('#venue_name-wrapper').css('display', 'block');
            $('#online_events-wrapper').css('display', 'none');
            if ($('#location-wrapper'))
                $('#location-wrapper').css('display', 'block');
            if ($('#location_map-wrapper'))
                $('#location_map-wrapper').css('display', 'block');
            $('#is_online').val(0);
        }
    }

    var getProfileType = function(category_id) {

        var mapping = <?php echo Zend_Json_Encoder::encode(Engine_Api::_()->getDbTable('categories', 'siteevent')->getMapping('profile_type')); ?>;
        for (i = 0; i < mapping.length; i++) {
            if (mapping[i].category_id == category_id)
                return mapping[i].profile_type;
        }
        return 0;
    }

</script>

<script type="text/javascript">
    seao_dateFormat = '<?php echo $this->locale()->useDateLocaleFormat(); ?>';

    function smSiteeventRepeatEvent(repeatEvent) {
        if (repeatEvent.val() == "daily") {
            $("#dailyrepeat_interval-wrapper").css("display", "block");
            $("#daily_repeat_time-wrapper").css("display", "block");
            $("#id_weeklyrepeat_interval_select-wrapper").css("display", "none");
            $("#weekly_repeat_time-wrapper").css("display", "none");
            $("#id_monthlyabsolute_day_select-wrapper").css("display", "none");
            $("#id_monthlyrepeat_interval_select-wrapper").css("display", "none");
            $("#monthly_repeat_time-wrapper").css("display", "none");

        } else if (repeatEvent.val() == "weekly") {
            $("#dailyrepeat_interval-wrapper").css("display", "none");
            $("#daily_repeat_time-wrapper").css("display", "none");
            $("#id_weeklyrepeat_interval_select-wrapper").css("display", "block");
            $("#weekly_repeat_time-wrapper").css("display", "block");
            $("#id_monthlyabsolute_day_select-wrapper").css("display", "none");
            $("#id_monthlyrepeat_interval_select-wrapper").css("display", "none");
            $("#monthly_repeat_time-wrapper").css("display", "none");
        } else if (repeatEvent.val() == "monthly") {
            $("#dailyrepeat_interval-wrapper").css("display", "none");
            $("#daily_repeat_time-wrapper").css("display", "none");
            $("#id_weeklyrepeat_interval_select-wrapper").css("display", "none");
            $("#weekly_repeat_time-wrapper").css("display", "none");
            $("#id_monthlyabsolute_day_select-wrapper").css("display", "block");
            $("#id_monthlyrepeat_interval_select-wrapper").css("display", "block");
            $("#monthly_repeat_time-wrapper").css("display", "block");
        } else if (repeatEvent.val() == "never") {
            $("#dailyrepeat_interval_select-wrapper").css("display", "none");
            $("#daily_repeat_time-wrapper").css("display", "none");
            $("#id_weeklyrepeat_interval_select-wrapper").css("display", "none");
            $("#weekly_repeat_time-wrapper").css("display", "none");
            $("#id_monthlyabsolute_day_select-wrapper").css("display", "none");
            $("#id_monthlyrepeat_interval_select-wrapper").css("display", "none");
            $("#monthly_repeat_time-wrapper").css("display", "none");
        }
    }

<?php if (!$this->editFullEventDate): ?>
        if ($('eventrepeat_id-wrapper'))
            $('eventrepeat_id-wrapper').style.display = 'none';
<?php endif; ?>

    var initializeCalendarOnEdit = function() {
        var cal_bound_start = seao_getstarttime($('starttime-date').value);
        // check end date and make it the same date if it's too
        cal_endtime.calendars[0].start = new Date(cal_bound_start);
        // redraw calendar
        cal_endtime.navigate(cal_endtime.calendars[0], 'm', 1);
        cal_endtime.navigate(cal_endtime.calendars[0], 'm', -1);
<?php if ($this->editFullEventDate): ?>
            editFullEventDate = false;
            // check start date and make it the same date if it's too
    <?php if (isset($this->starttimestemp) && $this->starttimestemp < time()) : ?>
                editFullEventDate = false;
                cal_starttime.calendars[0].start = new Date(cal_bound_start);
    <?php else: ?>
                cal_starttime.calendars[0].start = new Date("<?php echo date('m/d/Y', time()); ?>");
    <?php endif; ?>
            // redraw calendar
            cal_starttime.navigate(cal_starttime.calendars[0], 'm', 1);
            cal_starttime.navigate(cal_starttime.calendars[0], 'm', -1);
            cal_starttime.changed(cal_starttime.calendars[0]);
<?php endif; ?>
    }
</script>