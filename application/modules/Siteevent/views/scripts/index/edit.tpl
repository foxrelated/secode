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
	$this->headTranslate(array('edit','Date & Time', 'on the following days', 'Specific dates and times are set for this event.', 'Start time should be greater than the current time.', 'End time should be greater than the Start time.'));
?>

<?php
$cateDependencyArray = Engine_Api::_()->getDbTable('categories', 'siteevent')->getCatDependancyArray();
$subCateDependencyArray = Engine_Api::_()->getDbTable('categories', 'siteevent')->getSubCatDependancyArray();
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/scripts/core.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/scripts/_commonFunctions.js');
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

    en4.core.runonce.add(function()
    {
        checkDraft();
        
        var usersAutocomplete = new Autocompleter.Request.JSON('host', '<?php echo $this->url(array('module' => 'siteevent', 'controller' => 'index', 'action' => 'get-member'), 'default', true) ?>', {
            'postVar': 'text',
            'minLength': 1,
            'selectMode': 'pick',
            'autocompleteType': 'tag',
            'className': 'tag-autosuggest seaocore-autosuggest',
            'customChoices': true,
            'filterSubset': true,
            'multiple': false,
            'injectChoice': function(token) {
                var choice = new Element('li', {'class': 'autocompleter-choices1', 'html': token.photo, 'id': token.label});
                new Element('div', {'html': this.markQueryValue(token.label), 'class': 'autocompleter-choice1'}).inject(choice);
                this.addChoiceEvents(choice).inject(this.choices);
                choice.store('autocompleteChoice', token);

            }
        });

        usersAutocomplete.addEvent('onSelection', function(element, selected, value, input) {
            document.getElementById('user_id').value = selected.retrieve('autocompleteChoice').id;
        });
        usersAutocomplete.addEvent('onCommand', function(e) {
            if (typeof e.key != 'undefined')
                document.getElementById('user_id').value = 0;
        });

    });

    function checkDraft() {
        if ($('draft')) {
            if ($('draft').value == 1) {
                $("search-wrapper").style.display = "none";
                $("search").checked = false;
            } else {
                $("search-wrapper").style.display = "block";
                $("search").checked = true;
            }
        }
    }

    var makeRichTextarea = function(element_id) {
        <?php
        echo $this->tinyMCESEAO()->render(array('element_id' => 'element_id',
            'language' => $this->language,
            'directionality' => $this->directionality,
            'upload_url' => $this->upload_url));
        ?>
    }

    window.addEvent('domready', function() {
        if ($('host_body')) {
            makeRichTextarea('host_body');
        }

        if ($('overview-wrapper')) {
            makeRichTextarea('overview');
        }

    });
</script>

<?php
/* Include the common user-end field switching javascript */
echo $this->partial('_jsSwitch.tpl', 'fields', array(
        //'topLevelId' => (int) @$this->topLevelId,
        //'topLevelValue' => (int) @$this->topLevelValue
))
?>

<?php include_once APPLICATION_PATH . '/application/modules/Siteevent/views/scripts/_DashboardNavigation.tpl'; ?>
<div class="siteevent_dashboard_content">

        <?php echo $this->partial('application/modules/Siteevent/views/scripts/dashboard/header.tpl', array('siteevent' => $this->siteevent)); ?>
    <div class="siteevent_event_form">
        <?php
        if (!$this->editFullEventDate && $this->form->starttime) {
            $this->form->removeElement('starttime');
        }
        echo $this->form->render();
        ?>
    </div>	
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
        <div class="form-wrapper" id="starttime-wrapper" title="<?php echo $this->translate("Sorry, you cannot edit the event start time as, either some of the site members has joined your event or some tickets has been purchased by your event guests."); ?>">
          <div class="form-label" id="starttime-label"><label class="optional" for="starttime"><?php echo $this->translate('Start Time'); ?></label></div>
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
<?php $coreSettings = Engine_Api::_()->getApi('settings', 'core');?>
<script type="text/javascript">
    
    en4.core.runonce.add(function()
    {
        new Autocompleter.Request.JSON('tags', '<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'index', 'action' => 'tag-suggest', 'resourceType' => 'siteevent_event'), 'default', true) ?>', {
            'postVar': 'text',
            'minLength': 1,
            'selectMode': 'pick',
            'autocompleteType': 'tag',
            'className': 'tag-autosuggest',
            'filterSubset': true,
            'multiple': true,
            'injectChoice': function(token) {
                var choice = new Element('li', {'class': 'autocompleter-choices', 'value': token.label, 'id': token.id});
                new Element('div', {'html': this.markQueryValue(token.label), 'class': 'autocompleter-choice'}).inject(choice);
                choice.inputValue = token;
                this.addChoiceEvents(choice).inject(this.choices);
                choice.store('autocompleteChoice', token);
            }
        });
     });
    
//    var eventRepeat = '<?php //echo (bool) Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.repeat', 1); ?>';
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

    window.addEvent('domready', function() {
        <?php if ($this->profileType): ?>
            $('<?php echo '0_0_' . $this->defaultProfileId ?>').value = <?php echo $this->profileType ?>;
            changeFields($('<?php echo '0_0_' . $this->defaultProfileId ?>'));
        <?php endif; ?>

        var eventCreate = en4.siteevent.create;
        <?php if($coreSettings->getSetting('siteevent.onlineevent.allow', 1) == 1 || $this->siteevent->is_online == 1) : ?>
        //ADD A LINK WITH VENUE NAME FIELD:
        var newdiv = document.createElement('div');
        var language = '<?php echo $this->string()->escapeJavascript($this->translate('online event')) ?>';
        newdiv.id = 'online_event';
        newdiv.innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate('Running an ')) ?>' + "<a href='javascript:void(0);' name='online_event' onclick='en4.siteevent.create.is_online(true);return false;' >" + language + "</a>?<br />";

        if ($('venue_name-element')) {
            newdiv.inject($('venue_name-element'), 'bottom');
            if ($('online_events-wrapper'))
                $('online_events-wrapper').setStyle('display', 'none');
        }
        <?php endif;?>
//    if(document.getElementById('location'))
//    var autocomplete = new google.maps.places.Autocomplete(document.getElementById('location'));

        //IF EVENT IS ONLINE THEN HIDE THE LOCATION
        <?php if ($this->siteevent->is_online == 1): ?>
            if ($('venue_name-wrapper'))
                $('venue_name-wrapper').setStyle('display', 'none');
            if ($('location-wrapper'))
                $('location-wrapper').setStyle('display', 'none');
            //$('location_map-wrapper').setStyle('display', 'none');
            if ($('online_events-wrapper'))
                $('online_events-wrapper').setStyle('display', 'block');
        <?php endif; ?>

        initializeCalendarOnEdit();
    });

    var getProfileType = function(category_id) {

        var mapping = <?php echo Zend_Json_Encoder::encode(Engine_Api::_()->getDbTable('categories', 'siteevent')->getMapping('profile_type')); ?>;
        for (i = 0; i < mapping.length; i++) {
            if (mapping[i].category_id == category_id)
                return mapping[i].profile_type;
        }
        return 0;
    }

    var defaultProfileId = '<?php echo '0_0_' . $this->defaultProfileId ?>' + '-wrapper';
    if ($type($(defaultProfileId)) && typeof $(defaultProfileId) != 'undefined') {
        $(defaultProfileId).setStyle('display', 'none');
    }

    var subcategories = function(category_id, subcatid, subcatname, subsubcatid)
    {
        if (subcatid > 0) {
            changesubcategory(subcatid, subsubcatid);
        }
        if (!in_array(cateDependencyArray, category_id)) {
            if ($('subcategory_id-wrapper'))
                $('subcategory_id-wrapper').style.display = 'none';
            if ($('subcategory_id-label'))
                $('subcategory_id-label').style.display = 'none';
            if ($('buttons-wrapper')) {
                $('buttons-wrapper').style.display = 'block';
            }
            return;
        }
        if ($('subsubcategory_backgroundimage'))
            $('subcategory_backgroundimage').style.display = 'block';
        if ($('subcategory_id'))
            $('subcategory_id').style.display = 'none';
        if ($('subsubcategory_id'))
            $('subsubcategory_id').style.display = 'none';
        if ($('subcategory_id-label'))
            $('subcategory_id-label').style.display = 'none';
        if ($('subcategory_backgroundimage'))
            $('subcategory_backgroundimage').innerHTML = '<div class="form-label"></div><div  class="form-element"><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteevent/externals/images/loading.gif" /></center></div>';


        if ($('buttons-wrapper')) {
            $('buttons-wrapper').style.display = 'none';
        }
        if ($('subsubcategory_id-wrapper'))
            $('subsubcategory_id-wrapper').style.display = 'none';
        if ($('subsubcategory_id-label'))
            $('subsubcategory_id-label').style.display = 'none';
        var url = '<?php echo $this->url(array('action' => 'sub-category'), 'siteevent_general', true); ?>';
        en4.core.request.send(new Request.JSON({
            url: url,
            data: {
                format: 'json',
                category_id_temp: category_id
            },
            onSuccess: function(responseJSON) {
                if ($('buttons-wrapper')) {
                    $('buttons-wrapper').style.display = 'block';
                }
                if ($('subcategory_backgroundimage'))
                    $('subcategory_backgroundimage').style.display = 'none';

                clear('subcategory_id');
                var subcatss = responseJSON.subcats;
                addOption($('subcategory_id'), " ", '0');
                for (i = 0; i < subcatss.length; i++) {
                    addOption($('subcategory_id'), subcatss[i]['category_name'], subcatss[i]['category_id']);
                    if (show_subcat == 0) {
                        if ($('subcategory_id'))
                            $('subcategory_id').disabled = 'disabled';
                        if ($('subsubcategory_id'))
                            $('subsubcategory_id').disabled = 'disabled';
                    }
                    if ($('subcategory_id')) {
                        $('subcategory_id').value = '<?php echo $this->siteevent->subcategory_id; ?>';
                    }
                }

                if (category_id == 0) {
                    clear('subcategory_id');
                    if ($('subcategory_id'))
                        $('subcategory_id').style.display = 'none';
                    if ($('subcategory_id-label'))
                        $('subcategory_id-label').style.display = 'none';
                }
            }
        }), {
            "force": true
        });
    };
    function in_array(ArrayofCategories, value) {
        for (var i = 0; i < ArrayofCategories.length; i++) {
            if (ArrayofCategories[i] == value) {
                return true;
            }
        }
        return false;
    }
    
    var changesubcategory = function(subcatid, subsubcatid)
    {
        if ($('buttons-wrapper')) {
            $('buttons-wrapper').style.display = 'none';
        }

        if (!in_array(subCateDependencyArray, subcatid)) {
            if ($('subsubcategory_id-wrapper'))
                $('subsubcategory_id-wrapper').style.display = 'none';
            if ($('subsubcategory_id-label'))
                $('subsubcategory_id-label').style.display = 'none';
            if ($('buttons-wrapper')) {
                $('buttons-wrapper').style.display = 'block';
            }
            return;
        }
        if ($('subsubcategory_backgroundimage'))
            $('subsubcategory_backgroundimage').style.display = 'block';
        if ($('subsubcategory_id'))
            $('subsubcategory_id').style.display = 'none';
        if ($('subsubcategory_id-label'))
            $('subsubcategory_id-label').style.display = 'none';
        if ($('subsubcategory_backgroundimage'))
            $('subsubcategory_backgroundimage').innerHTML = '<div class="form-label"></div><div  class="form-element"><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteevent/externals/images/loading.gif" /></center></div>';


        if ($('buttons-wrapper')) {
            $('buttons-wrapper').style.display = 'none';
        }
        var url = '<?php echo $this->url(array('action' => 'subsub-category'), 'siteevent_general', true); ?>';
        var request = new Request.JSON({
            url: url,
            data: {
                format: 'json',
                subcategory_id_temp: subcatid
            },
            onSuccess: function(responseJSON) {
                if ($('buttons-wrapper')) {
                    $('buttons-wrapper').style.display = 'block';
                }
                if ($('subsubcategory_backgroundimage'))
                    $('subsubcategory_backgroundimage').style.display = 'none';

                clear('subsubcategory_id');
                var subsubcatss = responseJSON.subsubcats;
                if ($('subsubcategory_id')) {
                    addSubOption($('subsubcategory_id'), " ", '0');
                    for (i = 0; i < subsubcatss.length; i++) {
                        addSubOption($('subsubcategory_id'), subsubcatss[i]['category_name'], subsubcatss[i]['category_id']);
                        if ($('subsubcategory_id')) {
                            $('subsubcategory_id').value = '<?php echo $this->siteevent->subsubcategory_id; ?>';
                        }
                    }
                }
            }
        });
        request.send();
    };

    function clear(ddName)
    {
        if (document.getElementById(ddName)) {
            for (var i = (document.getElementById(ddName).options.length - 1); i >= 0; i--)
            {
                document.getElementById(ddName).options[ i ] = null;
            }
        }
    }
    function addOption(selectbox, text, value)
    {
        if ($('subcategory_id')) {
            var optn = document.createElement("OPTION");
            optn.text = text;
            optn.value = value;

            if (optn.text != '' && optn.value != '') {
                if ($('subcategory_id'))
                    $('subcategory_id').style.display = 'block';
                if ($('subcategory_id-wrapper'))
                    $('subcategory_id-wrapper').style.display = 'block';
                if ($('subcategory_id-label'))
                    $('subcategory_id-label').style.display = 'block';
                selectbox.options.add(optn);
            } else {
                if ($('subcategory_id'))
                    $('subcategory_id').style.display = 'none';
                if ($('subcategory_id-wrapper'))
                    $('subcategory_id-wrapper').style.display = 'none';
                if ($('subcategory_id-label'))
                    $('subcategory_id-label').style.display = 'none';
                selectbox.options.add(optn);
            }
        }
    }

    var cat = '<?php echo $this->category_id ?>';
    if (cat != '') {
        subcatid = '<?php echo $this->subcategory_id; ?>';
        subsubcatid = '<?php echo $this->subsubcategory_id; ?>';
        var subcatname = '<?php echo $this->subcategory_name; ?>';
        subcategories(cat, subcatid, subcatname, subsubcatid);
    }
    function addSubOption(selectbox, text, value)
    {
        if ($('subsubcategory_id')) {
            var optn = document.createElement("OPTION");
            optn.text = text;
            optn.value = value;
            if (optn.text != '' && optn.value != '') {
                if ($('subsubcategory_id'))
                    $('subsubcategory_id').style.display = 'block';
                if ($('subsubcategory_id-wrapper'))
                    $('subsubcategory_id-wrapper').style.display = 'block';
                if ($('subsubcategory_id-label'))
                    $('subsubcategory_id-label').style.display = 'block';
                selectbox.options.add(optn);
            } else {
                if ($('subsubcategory_id'))
                    $('subsubcategory_id').style.display = 'none';
                if ($('subsubcategory_id-wrapper'))
                    $('subsubcategory_id-wrapper').style.display = 'none';
                if ($('subsubcategory_id-label'))
                    $('subsubcategory_id-label').style.display = 'none';
                selectbox.options.add(optn);
            }
        }
    }
</script>

<script type="text/javascript">
    seao_dateFormat = '<?php echo $this->locale()->useDateLocaleFormat(); ?>';
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
   
   window.addEvent('domready', function() {
   showGuestLists('<?php echo $this->guest_lists;?>');
   });
    
        function showGuestLists(option) {
      
      if($('guest_lists-wrapper')) {
        if(option == 0) {
           $('guest_lists-wrapper').style.display = 'block';
        }
        else {
          $('guest_lists-wrapper').style.display = 'none';
        }
      }
    }
</script>