<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/scripts/core.js');
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/core.js');
?>

<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()
        ->prependStylesheet($baseUrl . 'application/modules/Seaocore/externals/styles/styles.css');
?>
<?php
$dateFormat = $this->locale()->useDateLocaleFormat();
$calendarFormatString = trim(preg_replace('/\w/', '$0/', $dateFormat), '/');
$calendarFormatString = str_replace('y', 'Y', $calendarFormatString);
?>

<script type="text/javascript">
    seao_dateFormat = '<?php echo $this->locale()->useDateLocaleFormat(); ?>';
    var showMarkerInDate = "<?php echo $this->showMarkerInDate ?>";
    en4.core.runonce.add(function()
    {
        en4.core.runonce.add(function init()
        {
            monthList = [];
            myCal = new Calendar({'start_cal[date]': '<?php echo $calendarFormatString; ?>', 'end_cal[date]': '<?php echo $calendarFormatString; ?>'}, {
                classes: ['event_calendar'],
                pad: 0,
                direction: 0
            });
        });
    });

    var cal_starttimesearchsiteevent_onHideStart = function() {
        if (showMarkerInDate == 0)
            return;
        var cal_bound_start = seao_getstarttime(document.getElementById('startdate-date').value);
        // check end date and make it the same date if it's too
        cal_endtimesearchsiteevent.calendars[0].start = new Date(cal_bound_start);
        // redraw calendar
        cal_endtimesearchsiteevent.navigate(cal_endtimesearchsiteevent.calendars[0], 'm', 1);
        cal_endtimesearchsiteevent.navigate(cal_endtimesearchsiteevent.calendars[0], 'm', -1);
    }
    var cal_endtimesearchsiteevent_onHideStart = function() {
        if (showMarkerInDate == 0)
            return;
        var cal_bound_start = seao_getstarttime(document.getElementById('endtimesearchsiteevent-date').value);
        // check start date and make it the same date if it's too
        cal_starttimesearchsiteevent.calendars[0].end = new Date(cal_bound_start);
        // redraw calendar
        cal_starttimesearchsiteevent.navigate(cal_starttimesearchsiteevent.calendars[0], 'm', 1);
        cal_starttimesearchsiteevent.navigate(cal_starttimesearchsiteevent.calendars[0], 'm', -1);
    }

    en4.core.runonce.add(function() {
        cal_starttimesearchsiteevent_onHideStart();
        cal_endtimesearchsiteevent_onHideStart();
    });

    en4.core.runonce.add(function() {
        if ($('starttimesearchsiteevent-minute') && $('endtimesearchsiteevent-minute')) {
            $('starttimesearchsiteevent-minute').destroy();
            $('endtimesearchsiteevent-minute').destroy();
        }
        if ($('starttimesearchsiteevent-ampm') && $('endtimesearchsiteevent-ampm')) {
            $('starttimesearchsiteevent-ampm').destroy();
            $('endtimesearchsiteevent-ampm').destroy();
        }
        if ($('starttimesearchsiteevent-hour') && $('endtimesearchsiteevent-hour')) {
            $('starttimesearchsiteevent-hour').destroy();
            $('endtimesearchsiteevent-hour').destroy();
        }

        if ($('calendar_output_span_starttimesearchsiteevent-date')) {
            $('calendar_output_span_starttimesearchsiteevent-date').style.display = 'none';
        }

        if ($('calendar_output_span_endtimesearchsiteevent-date')) {
            $('calendar_output_span_endtimesearchsiteevent-date').style.display = 'none';
        }

        if ($('starttimesearchsiteevent-date')) {
            $('starttimesearchsiteevent-date').setAttribute('type', 'text');
        }

        if ($('endtimesearchsiteevent-date')) {
            $('endtimesearchsiteevent-date').setAttribute('type', 'text');
        }

    });
</script>
<script type="text/javascript">
    var pageAction = function(page) {
        $('page').value = page;
        $('filter_form').submit();
    }

    var searchSiteevents = function() {
        var formElements = $('filter_form').getElements('li');
        formElements.each(function(el) {
            var field_style = el.style.display;
            if (field_style == 'none') {
                el.destroy();
            }
        });

        if (Browser.Engine.trident) {
            document.getElementById('filter_form').submit();
        } else {
            $('filter_form').submit();
        }
    }
    en4.core.runonce.add(function() {
        $$('#filter_form input[type=text]').each(function(f) {
            if (f.value == '' && f.id.match(/\min$/)) {
                new OverText(f, {'textOverride': 'min', 'element': 'span'});
                //f.set('class', 'integer_field_unselected');
            }
            if (f.value == '' && f.id.match(/\max$/)) {
                new OverText(f, {'textOverride': 'max', 'element': 'span'});
                //f.set('class', 'integer_field_unselected');
            }
        });
    });
    window.addEvent('onChangeFields', function() {
        var firstSep = $$('li.browse-separator-wrapper')[0];
        var lastSep;
        var nextEl = firstSep;
        var allHidden = true;
        do {
            nextEl = nextEl.getNext();
            if (nextEl.get('class') == 'browse-separator-wrapper') {
                lastSep = nextEl;
                nextEl = false;
            } else {
                allHidden = allHidden && (nextEl.getStyle('display') == 'none');
            }
        } while (nextEl);
        if (lastSep) {
            lastSep.setStyle('display', (allHidden ? 'none' : ''));
        }
    });
</script>

<?php
//if(empty($this->siteevent_post)){return;}
/* Include the common user-end field switching javascript */
echo $this->partial('_jsSwitch.tpl', 'fields', array(
        //'topLevelId' => (int) @$this->topLevelId,
        //'topLevelValue' => (int) @$this->topLevelValue
))
?>

<?php if ($this->viewType == 'horizontal'): ?>
    <div class="seaocore_searchform_criteria seaocore_searchform_criteria_horizontal <?php
    if ($this->whatWhereWithinmile) {
        echo "seaocore_searchform_criteria_advanced";
    }
    ?>">
             <?php
             if ($this->siteevent_post == 'enabled') {
                 echo $this->form->render($this);
             } else {
                 return;
             }
             ?>
    </div>
    <?php else: ?>
    <div class="seaocore_searchform_criteria">
        <?php
        if ($this->siteevent_post == 'enabled') {
            echo $this->form->render($this);
        } else {
            return;
        }
        ?>
    </div>
<?php endif; ?>

<script type="text/javascript">

    var viewType = '<?php echo $this->viewType; ?>';
    var whatWhereWithinmile = <?php echo $this->whatWhereWithinmile; ?>;

<?php if (isset($_GET['search']) || isset($_GET['location'])): ?>
        var advancedSearch = 1;
<?php else: ?>
        var advancedSearch = <?php echo $this->advancedSearch; ?>;
<?php endif; ?>

    if (viewType == 'horizontal' && whatWhereWithinmile == 1) {

        function advancedSearchLists(showFields, domeReady) {

            var fieldElements = new Array('siteevent_street', 'siteevent_city', 'siteevent_state', 'siteevent_country', 'orderby', 'show', 'showEventType', 'category_id', 'subcategory_id', 'subsubcategory_id', 'has_photo', 'has_review', 'starttimesearchsiteevent', 'endtimesearchsiteevent', 'minmax_slider', 'venue_name', 'event_time', 'has_free_price', 'eventType');

            var fieldsStatus = 'none';
            if (showFields == 1) {
                var fieldsStatus = 'block';
            }

            if ($('integer-wrapper')) {
                if (domeReady == 1) {
                    $('integer-wrapper').style.display = fieldsStatus;
                }
                else {
                    $('integer-wrapper').toggle();
                }
            }

            for (i = 0; i < fieldElements.length; i++) {
                if ($(fieldElements[i] + '-label')) {
                    if (domeReady == 1) {
                        $(fieldElements[i] + '-label').getParent().style.display = fieldsStatus;
                    }
                    else {                        
                        
                        $(fieldElements[i] + '-label').getParent().toggle();
                        showFields = $(fieldElements[i] + '-label').getParent().style.display == 'block';
                    }
                }
                
                if((fieldElements[i] == 'subcategory_id') &&  ($('subcategory_id-wrapper')) && domeReady != 1 && $('category_id').value != 0) {
                    $(fieldElements[i] + '-wrapper').toggle();
                }

                if((fieldElements[i] == 'subsubcategory_id') &&  ($('subsubcategory_id-wrapper')) && domeReady != 1 && $('subcategory_id').value != 0) {
                    $(fieldElements[i] + '-wrapper').toggle();
                }
                
            }

            if (showFields == 1) {
                $("filter_form").getElements(".field_toggle").each(function(el){
                    if(el.getParent('li')) {
                         el.getParent('li').removeClass('dnone');
                    }
                 });
            }else{
                $("filter_form").getElements(".field_toggle").each(function(el){
                    if(el.getParent('li')) {
                        el.getParent('li').removeClass('dnone').addClass('dnone');
                    }
                 });
            } 
            
            var showSlider = '<?php echo $this->widgetSettings['priceFieldType']; ?>';
            var priceAllow = <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.price', 0); ?>;
            if (showSlider == 'slider' && priceAllow) {
                <?php
                $currency_symbol = Engine_Api::_()->siteevent()->getCurrencySymbol();
                $minPrice = ($this->widgetSettings['minPrice']) ? $this->widgetSettings['minPrice'] : 0;
                $maxPrice = ($this->widgetSettings['maxPrice']) ? $this->widgetSettings['maxPrice'] : 999;
                $searchMinPrice = Zend_Controller_Front::getInstance()->getRequest()->getParam('minPrice');
                $searchMaxPrice = Zend_Controller_Front::getInstance()->getRequest()->getParam('maxPrice');
                ?>
                var mySlideA = new Slider($('slider_minmax_gutter_m'), $('slider_minmax_minKnobA'), $('slider_bkg_img'), {
                    start: <?php echo $minPrice ?>,
                    end: <?php echo $maxPrice ?>,
                    offset: 8,
                    snap: false,
                    onChange: function(pos) {

                        $('minPrice').value = pos.minpos;
                        $('maxPrice').value = pos.maxpos;
                        $('slider_minmax_min').innerHTML = '<?php echo $currency_symbol; ?> ' + pos.minpos;
                        $('slider_minmax_max').innerHTML = '<?php echo $currency_symbol; ?> ' + pos.maxpos;
                    }
                },
                $('slider_minmax_maxKnobA')).setMin(<?php echo ($searchMinPrice) ? $searchMinPrice : $minPrice ?>).setMax(<?php echo ($searchMaxPrice) ? $searchMaxPrice : $maxPrice ?>);
            }

        }

        advancedSearchLists(advancedSearch, 1);
    }
  
    var module = '<?php echo Zend_Controller_Front::getInstance()->getRequest()->getModuleName()?>';
  
    if (module != 'siteadvsearch') {
			$('global_content').getElement('.browsesiteevents_criteria').addEvent('keypress', function(e) {
					if (e.key != 'enter')
							return;
					searchSiteevents();
			});	
    }
    var profile_type = 0;
    var previous_mapped_level = 0;
    var siteevent_categories_slug = <?php echo json_encode($this->categories_slug); ?>;
    function showFields(cat_value, cat_level) {

        if (cat_level == 1 || (previous_mapped_level >= cat_level && previous_mapped_level != 1) || (profile_type == null || profile_type == '' || profile_type == 0)) {
            profile_type = getProfileType(cat_value);
            if (profile_type == 0) {
                profile_type = '';
            } else {
                previous_mapped_level = cat_level;
            }
            $('profile_type').value = profile_type;
            changeFields($('profile_type'));
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

    function addOptions(element_value, element_type, element_updated, domready) {

        var element = $(element_updated);
        if (domready == 0) {
            switch (element_type) {
                case 'cat_dependency':
                    $('subcategory_id' + '-wrapper').style.display = 'none';
                    clear($('subcategory_id'));
                    $('subcategory_id').value = 0;
                    $('categoryname').value = siteevent_categories_slug[element_value];

                case 'subcat_dependency':
                    $('subsubcategory_id' + '-wrapper').style.display = 'none';
                    clear($('subsubcategory_id'));
                    $('subsubcategory_id').value = 0;
                    $('subsubcategoryname').value = '';
                    if (element_type == 'subcat_dependency')
                        $('subcategoryname').value = siteevent_categories_slug[element_value];
                    else
                        $('subcategoryname').value = '';
            }
        }

        if (element_value <= 0)
            return;

        var url = '<?php echo $this->url(array('module' => 'siteevent', 'controller' => 'review', 'action' => 'categories', 'showAllCategories' => $this->showAllCategories), "default", true); ?>';
        en4.core.request.send(new Request.JSON({
            url: url,
            data: {
                format: 'json',
                element_value: element_value,
                element_type: element_type
            },
            onSuccess: function(responseJSON) {
                var categories = responseJSON.categories;
                var option = document.createElement("OPTION");
                option.text = "";
                option.value = 0;
                element.options.add(option);
                for (i = 0; i < categories.length; i++) {
                    var option = document.createElement("OPTION");
                    option.text = categories[i]['category_name'];
                    option.value = categories[i]['category_id'];
                    element.options.add(option);
                    siteevent_categories_slug[categories[i]['category_id']] = categories[i]['category_slug'];
                }

                if (categories.length > 0)
                    $(element_updated + '-wrapper').style.display = 'inline-block';
                else
                    $(element_updated + '-wrapper').style.display = 'none';

                if (domready == 1) {
                    var value = 0;
                    if (element_updated == 'category_id') {
                        value = search_category_id;
                    } else if (element_updated == 'subcategory_id') {
                        value = search_subcategory_id;
                    } else {
                        value = search_subsubcategory_id;
                    }
                    $(element_updated).value = value;
                }
            }

        }), {'force': true});
    }

    function clear(element)
    {
        for (var i = (element.options.length - 1); i >= 0; i--) {
            element.options[ i ] = null;
        }
    }

<?php if (!empty($this->categoryInSearchForm) && !empty($this->categoryInSearchForm->display)): ?>
        var search_category_id, search_subcategory_id, search_subsubcategory_id;
       en4.core.runonce.add(function() {

            search_category_id = '<?php echo $this->category_id ?>';

            if (search_category_id != 0) {

                addOptions(search_category_id, 'cat_dependency', 'subcategory_id', 1);

                search_subcategory_id = '<?php echo $this->subcategory_id ?>';

                if (search_subcategory_id != 0) {
                    search_subsubcategory_id = '<?php echo $this->subsubcategory_id ?>';
                    addOptions(search_subcategory_id, 'subcat_dependency', 'subsubcategory_id', 1);
                }
            }
        });
<?php endif; ?>

    function show_subcat(cat_id)
    {
        if (document.getElementById('subcat_' + cat_id)) {
            if (document.getElementById('subcat_' + cat_id).style.display == 'block') {
                document.getElementById('subcat_' + cat_id).style.display = 'none';
                document.getElementById('img_' + cat_id).src = '<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteevent/externals/images/bullet-right.png';
            }
            else if (document.getElementById('subcat_' + cat_id).style.display == '') {
                document.getElementById('subcat_' + cat_id).style.display = 'none';
                document.getElementById('img_' + cat_id).src = '<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteevent/externals/images/bullet-right.png';
            }
            else {
                document.getElementById('subcat_' + cat_id).style.display = 'block';
                document.getElementById('img_' + cat_id).src = '<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteevent/externals/images/bullet-bottom.png';
            }
        }
    }
    en4.core.runonce.add(function()
    {
        var item_count = 0;
        var contentAutocomplete = new Autocompleter.Request.JSON('search', '<?php echo $this->url(array('action' => 'get-search-events'), "siteevent_general", true) ?>', {
            'postVar': 'text',
            'minLength': 1,
            'selectMode': 'pick',
            'autocompleteType': 'tag',
            'className': 'tag-autosuggest seaocore-autosuggest',
            'customChoices': true,
            'filterSubset': true,
            'multiple': false,
            'injectChoice': function(token) {
                if (typeof token.label != 'undefined') {
                    if (token.siteevent_url != 'seeMoreLink') {
                        var choice = new Element('li', {'class': 'autocompleter-choices', 'html': token.photo, 'id': token.label, 'siteevent_url': token.siteevent_url, onclick: 'javascript:getPageResultsSearch("' + token.siteevent_url + '")'});
                        new Element('div', {'html': this.markQueryValue(token.label), 'class': 'autocompleter-choice'}).inject(choice);
                        this.addChoiceEvents(choice).inject(this.choices);
                        choice.store('autocompleteChoice', token);
                    }
                    if (token.siteevent_url == 'seeMoreLink') {
                        var titleAjax = $('search').value;
                        var choice = new Element('li', {'class': 'autocompleter-choices1', 'html': '', 'id': 'stopevent', 'siteevent_url': ''});
                        new Element('div', {'html': 'See More Results for ' + titleAjax, 'class': 'autocompleter-choicess', onclick: 'javascript:SeemoreSearch()'}).inject(choice);
                        this.addChoiceEvents(choice).inject(this.choices);
                        choice.store('autocompleteChoice', token);
                    }
                }
            }
        });

        contentAutocomplete.addEvent('onSelection', function(element, selected, value, input) {
            window.addEvent('keyup', function(e) {
                if (e.key == 'enter') {
                    if (selected.retrieve('autocompleteChoice') != 'null') {
                        var url = selected.retrieve('autocompleteChoice').siteevent_url;
                        if (url == 'seeMoreLink') {
                            SeemoreSearch();
                        }
                        else {
                            window.location.href = url;
                        }
                    }
                }
            });
        });
    });

    function SeemoreSearch() {
        $('stopevent').removeEvents('click');
        var url = '<?php echo $this->url(array('action' => 'index'), "siteevent_general", true); ?>';
        window.location.href = url + "?titleAjax=" + encodeURIComponent($('search').value);
    }

    function getPageResultsSearch(url) {
        if (url != 'null') {
            if (url == 'seeMoreLink') {
                SeemoreSearch();
            }
            else {
                window.location.href = url;
            }
        }
    }

    en4.core.runonce.add(function() {
			if ($('location')) {
//					var autocomplete = new google.maps.places.Autocomplete(document.getElementById('location'));
//					google.maps.event.addListener(autocomplete, 'place_changed', function() {
//							var place = autocomplete.getPlace();
//							if (!place.geometry) {
//									return;
//							}
//
//							var myLocationDetails = {'latitude': place.geometry.location.lat(), 'longitude': place.geometry.location.lng(), 'location': document.getElementById('location').value, 'locationmiles': document.getElementById('locationmiles').value};
//							if (document.getElementById('Latitude') && document.getElementById('Longitude')) {
//									document.getElementById('Latitude').value = place.geometry.location.lat();
//									document.getElementById('Longitude').value = place.geometry.location.lng()
//							}
//							
//							en4.seaocore.locationBased.setLocationCookies(myLocationDetails);
//
//					});

					var params = {
							'detactLocation': <?php echo $this->locationDetection; ?>,
							'fieldName': 'location',
							'noSendReq': 1,
							'locationmilesFieldName': 'locationmiles',
							'locationmiles': <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefaultmiles', 1000); ?>,
							'reloadPage': 1,
					};
					en4.seaocore.locationBased.startReq(params);
			}
			    locationAutoSuggest('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.countrycities'); ?>', 'location', 'siteevent_city');
    });
    
    function showRadiusTip() {
    
      var url = '<?php echo $this->url(array('action' => 'show-radius-tip'), "siteevent_general", true); ?>';
      Smoothbox.open(url);
    }
        

    
</script>