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
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteevent.css');
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/scripts/core.js');
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/core.js');
?>
<?php
$dateFormat = $this->locale()->useDateLocaleFormat();
$calendarFormatString = trim(preg_replace('/\w/', '$0/', $dateFormat), '/');
$calendarFormatString = str_replace('y', 'Y', $calendarFormatString);
?>
<script type="text/javascript">
    var showMarkerInDate = "<?php echo $this->showMarkerInDate ?>";
    seao_dateFormat = '<?php echo $this->locale()->useDateLocaleFormat(); ?>';
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

    var cal_starttime_onHideStart = function() {
        if (showMarkerInDate == 0)
            return;
        var cal_bound_start = seao_getstarttime(document.getElementById('startdate-date').value);
        // check end date and make it the same date if it's too
        cal_endtime.calendars[0].start = new Date(cal_bound_start);
        // redraw calendar
        cal_endtime.navigate(cal_endtime.calendars[0], 'm', 1);
        cal_endtime.navigate(cal_endtime.calendars[0], 'm', -1);
    }
    var cal_endtime_onHideStart = function() {
        if (showMarkerInDate == 0)
            return;
        var cal_bound_start = seao_getstarttime(document.getElementById('endtime-date').value);
        // check start date and make it the same date if it's too
        cal_starttime.calendars[0].end = new Date(cal_bound_start);
        // redraw calendar
        cal_starttime.navigate(cal_starttime.calendars[0], 'm', 1);
        cal_starttime.navigate(cal_starttime.calendars[0], 'm', -1);
    }

    en4.core.runonce.add(function() {
        cal_starttime_onHideStart();
        cal_endtime_onHideStart();
    });

    window.addEvent('domready', function() {
        if ($('starttime-minute') && $('endtime-minute')) {
            $('starttime-minute').destroy();
            $('endtime-minute').destroy();
        }
        if ($('starttime-ampm') && $('endtime-ampm')) {
            $('starttime-ampm').destroy();
            $('endtime-ampm').destroy();
        }
        if ($('starttime-hour') && $('endtime-hour')) {
            $('starttime-hour').destroy();
            $('endtime-hour').destroy();
        }

        if ($('calendar_output_span_starttime-date')) {
            $('calendar_output_span_starttime-date').style.display = 'none';
        }

        if ($('calendar_output_span_endtime-date')) {
            $('calendar_output_span_endtime-date').style.display = 'none';
        }

        if ($('starttime-date')) {
            $('starttime-date').setAttribute('type', 'text');
        }

        if ($('endtime-date')) {
            $('endtime-date').setAttribute('type', 'text');
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
            }
            if (f.value == '' && f.id.match(/\max$/)) {
                new OverText(f, {'textOverride': 'max', 'element': 'span'});
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
/* Include the common user-end field switching javascript */
echo $this->partial('_jsSwitch.tpl', 'fields', array());
?>

<?php if ($this->form): ?>
    <?php echo $this->form->setAttrib('class', 'global_form_box siteevent_advanced_search_form')->render($this) ?>
    <div class="" id="page_location_pops_loding_image" style="display: none;">
        <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' />
        <?php //echo $this->translate("Loading ...") ?>
    </div>
<?php endif ?>

<script type="text/javascript">
    var flag = '<?php echo $this->advanced_search; ?>';
    var mapGetDirection;
    var myLatlng;

    window.addEvent('domready', function() {

        if (document.getElementById('location').value == '') {
            submiForm();
        }

        if ($$('.browse-separator-wrapper')) {
            $$('.browse-separator-wrapper').setStyle("display", 'none');
        }

        $('page_location_pops_loding_image').injectAfter($('done-element'));

//        var autocomplete = new google.maps.places.Autocomplete(document.getElementById('location'));
//        google.maps.event.addListener(autocomplete, 'place_changed', function() {
//            var place = autocomplete.getPlace();
//            if (!place.geometry) {
//                return;
//            }
//
//            var myLocationDetails = {'latitude': place.geometry.location.lat(), 'longitude': place.geometry.location.lng(), 'location': document.getElementById('location').value, 'locationmiles': document.getElementById('locationmiles').value};
//            if (document.getElementById('Latitude') && document.getElementById('Longitude')) {
//                document.getElementById('Latitude').value = place.geometry.location.lat();
//                document.getElementById('Longitude').value = place.geometry.location.lng()
//            }
//
//            en4.seaocore.locationBased.setLocationCookies(myLocationDetails);
//
//        });
        
        locationAutoSuggest('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.countrycities'); ?>', 'location', 'siteevent_city');

        var params = {
            'detactLocation': <?php echo $this->locationDetection; ?>,
            'fieldName': 'location',
            //'noSendReq':1,
            'locationmiles': <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefaultmiles', 1000); ?>,
        };
        params.callBack = function() {
            submiForm();
            advancedSearchLists(flag);
        };
        en4.seaocore.locationBased.startReq(params);
        //}
    });

    function submiForm() {

        if ($('category_id')) {
            if ($('category_id').options[$('category_id').selectedIndex].value == 0) {
                $('category_id').value = 0;
            }
        }

        var formElements = document.getElementById('filter_form');
        //var url = en4.core.baseUrl + 'widget/index/mod/siteevent/name/browselocation-siteevent'; 
        var url = en4.core.baseUrl + 'widget/index/content_id/<?php echo sprintf('%d', $this->identity) ?>';
        var parms = formElements.toQueryString();

        var param = (parms ? parms + '&' : '') + 'is_ajax=1&format=html';
        document.getElementById('page_location_pops_loding_image').style.display = '';
        en4.core.request.send(new Request.HTML({
            method: 'post',
            'url': url,
            'data': param,
            onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                document.getElementById('page_location_pops_loding_image').style.display = 'none';
                if($('list_map_container_topbar'))
                $('list_map_container_topbar').style.display = 'block';
                document.getElementById('list_location_map_anchor').getParent().innerHTML = responseHTML;
                setMarker();
                en4.core.runonce.trigger();
                if($('list_map_container'))
                $('list_map_container').style.visibility = 'visible';
                if ($('seaocore_browse_list')) {
                    var elementStartY = $('listlocation_map').getPosition().x;
                    var offsetWidth = $('list_map_container').offsetWidth;
                    var actualRightPostion = window.getSize().x - (elementStartY + offsetWidth);
                }
                if(document.getElementById('list_location_map_anchor'))
                Smoothbox.bind(document.getElementById('list_location_map_anchor').getParent());
            }
        }), {
            "force": true
        });
    }

    function locationPage() {
        var list_location = document.getElementById('location');

        if (document.getElementById('Latitude').value) {
            document.getElementById('Latitude').value = 0;
        }

        if (document.getElementById('Longitude').value) {
            document.getElementById('Longitude').value = 0;
        }
    }

    function locationSearch() {

        var formElements = document.getElementById('filter_form');
        formElements.addEvent('submit', function(event) {
            event.stop();
            submiForm();
        });
    }

    function advancedSearchLists() {

        if (flag == 0) {
            if ($('fieldset-grp2'))
                $('fieldset-grp2').style.display = 'none';

            if ($('fieldset-grp1'))
                $('fieldset-grp1').style.display = 'none';

            flag = 1;
            $('advanced_search').value = 0;
            if ($('siteevent_street'))
                $('siteevent_street').value = '';
            if ($('siteevent_country'))
                $('siteevent_country').value = '';
            if ($('siteevent_state'))
                $('siteevent_state').value = '';
            if ($('siteevent_city'))
                $('siteevent_city').value = '';
            if ($('profile_type'))
                $('profile_type').value = '';
            changeFields($('profile_type'));
            if ($('orderby'))
                $('orderby').value = 'starttime';
            if ($('category_id'))
                $('category_id').value = 0;

        }
        else {
            if ($('fieldset-grp2'))
                $('fieldset-grp2').style.display = 'block';
            if ($('fieldset-grp1'))
                $('fieldset-grp1').style.display = 'block';
            flag = 0;
            $('advanced_search').value = 1;

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
    }
</script>

<script type="text/javascript">

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

        var url = '<?php echo $this->url(array('module' => 'siteevent', 'controller' => 'review', 'action' => 'categories'), "default", true); ?>';
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

    var search_category_id, search_subcategory_id, search_subsubcategory_id;
    window.addEvent('domready', function() {

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
</script>
<div id="siteevent_location_map_none" style="display: none;"></div>