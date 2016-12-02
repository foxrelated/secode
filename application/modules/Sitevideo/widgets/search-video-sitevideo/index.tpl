<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2016-3-3 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/core.js');
?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css'); ?>
<script type="text/javascript">
    var pageAction = function (page) {
        $('page').value = page;
        $('filter_form').submit();
    };
<?php if (!empty($this->categoryInSearchForm) && !empty($this->categoryInSearchForm->display)): ?>
        var search_category_id, search_subcategory_id, search_subsubcategory_id;
        en4.core.runonce.add(function () {
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
                document.getElementById('img_' + cat_id).src = '<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitevideo/externals/images/bullet-right.png';
            }
            else if (document.getElementById('subcat_' + cat_id).style.display == '') {
                document.getElementById('subcat_' + cat_id).style.display = 'none';
                document.getElementById('img_' + cat_id).src = '<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitevideo/externals/images/bullet-right.png';
            }
            else {
                document.getElementById('subcat_' + cat_id).style.display = 'block';
                document.getElementById('img_' + cat_id).src = '<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitevideo/externals/images/bullet-bottom.png';
            }
        }
    }

    var searchSitevideos = function () {

        var formElements = $('filter_form').getElements('li');
        formElements.each(function (el) {
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
    };

    en4.core.runonce.add(function () {
        $$('#filter_form input[type=text]').each(function (f) {
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
    window.addEvent('onChangeFields', function () {
        var firstSep = $$('li.browse-separator-wrapper')[0];
        var lastSep;
        var nextEl = firstSep;
        var allHidden = true;
        do {
            if (!nextEl)
                return false;
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
<?php if ($this->viewType == 'horizontal'): ?>
    <div class="seaocore_searchform_criteria <?php
    if ($this->whatWhereWithinmile): echo "seaocore_searchform_criteria_advanced";
    endif;
    if ($this->viewType == 'horizontal'): echo " seaocore_search_horizontal";
    endif;
    ?>">
             <?php echo $this->form->render($this); ?>
    </div>
<?php else: ?>
    <div class="seaocore_searchform_criteria">
        <?php echo $this->form->render($this); ?>
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

            var fieldElements = new Array('video_street', 'video_city', 'video_state', 'video_country', 'orderby', 'category_id', 'view_view');
            var fieldsStatus = 'none';

            if (showFields == 1) {
                var fieldsStatus = 'block';
            }

            for (i = 0; i < fieldElements.length; i++) {
                if ($(fieldElements[i] + '-label')) {
                    if (domeReady == 1) {
                        $(fieldElements[i] + '-label').getParent().style.display = fieldsStatus;
                    }
                    else {
                        $(fieldElements[i] + '-label').getParent().toggle();
                    }
                }

                if ((fieldElements[i] == 'subcategory_id') && ($('subcategory_id-wrapper')) && domeReady != 1 && $('category_id').value != 0) {
                    $(fieldElements[i] + '-wrapper').toggle();
                }

                if ((fieldElements[i] == 'subsubcategory_id') && ($('subsubcategory_id-wrapper')) && domeReady != 1 && $('subcategory_id').value != 0) {
                    $(fieldElements[i] + '-wrapper').toggle();
                }

            }
        }

        if (showFields == 1) {
            $("filter_form").getElements(".field_toggle").each(function (el) {
                if (el.getParent('li')) {
                    el.getParent('li').removeClass('dnone');
                }
            });
        } else {
            $("filter_form").getElements(".field_toggle").each(function (el) {
                if (el.getParent('li')) {
                    el.getParent('li').removeClass('dnone').addClass('dnone');
                }
            });
        }

        advancedSearchLists(advancedSearch, 1);
    }

    var module = '<?php echo Zend_Controller_Front::getInstance()->getRequest()->getModuleName() ?>';
    if (module != 'siteadvsearch') {
        $('global_content').getElement('.browsesitevideos_criteria').addEvent('keypress', function (e) {
            if (e.key != 'enter')
                return;
            searchSitevideos();
        });
    }

    en4.core.runonce.add(function ()
    {
        var item_count = 0;
        var contentAutocomplete = new Autocompleter.Request.JSON('search', '<?php echo $this->url(array('action' => 'get-search-videos'), "sitevideo_video_general", true) ?>', {
            'postVar': 'text',
            'minLength': 1,
            'selectMode': 'pick',
            'autocompleteType': 'tag',
            'className': 'tag-autosuggest seaocore-autosuggest',
            'customChoices': true,
            'filterSubset': true,
            'multiple': false,
            'injectChoice': function (token) {
                if (typeof token.label != 'undefined') {
                    if (token.sitevideo_url != 'seeMoreLink') {
                        var choice = new Element('li', {'class': 'autocompleter-choices', 'html': token.photo, 'id': token.label, 'sitevideo_url': token.sitevideo_url, onclick: 'javascript:getPageResults("' + token.sitevideo_url + '")'});
                        new Element('div', {'html': this.markQueryValue(token.label), 'class': 'autocompleter-choice'}).inject(choice);
                        this.addChoiceEvents(choice).inject(this.choices);
                        choice.store('autocompleteChoice', token);
                    }
                    if (token.sitevideo_url == 'seeMoreLink') {
                        var search = $('search').value;
                        var choice = new Element('li', {'class': 'autocompleter-choices1', 'html': '', 'id': 'stopevent', 'sitevideo_url': ''});
                        new Element('div', {'html': 'See More Results for ' + search, 'class': 'autocompleter-choicess', onclick: 'javascript:Seemore()'}).inject(choice);
                        this.addChoiceEvents(choice).inject(this.choices);
                        choice.store('autocompleteChoice', token);
                    }
                }
            }
        });

        contentAutocomplete.addEvent('onSelection', function (element, selected, value, input) {
            window.addEvent('keyup', function (e) {
                if (e.key == 'enter') {
                    if (selected.retrieve('autocompleteChoice') != 'null') {
                        var url = selected.retrieve('autocompleteChoice').sitevideo_url;
                        if (url == 'seeMoreLink') {
                            Seemore();
                        }
                        else {
                            window.location.href = url;
                        }
                    }
                }
            });
        });
    });

    function Seemore() {
        $('stopevent').removeEvents('click');
        var url = '<?php echo $this->url(array('action' => 'browse'), "sitevideo_video_general", true); ?>';
        window.location.href = url + "?search=" + encodeURIComponent($('search').value);
    }

    function getPageResults(url) {
        if (url != 'null') {
            if (url == 'seeMoreLink') {
                Seemore();
            }
            else {
                window.location.href = url;
            }
        }
    }

    en4.core.runonce.add(function () {
        if ($('location')) {
            var params = {
                'detactLocation': <?php echo $this->locationDetection; ?>,
                'fieldName': 'location',
                'noSendReq': 1,
                'locationmilesFieldName': 'locationmiles',
                'locationmiles': <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefaultmiles', 1000); ?>,
                'reloadPage': 1
            };
            en4.seaocore.locationBased.startReq(params);
        }

        locationAutoSuggest('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.countrycities'); ?>', 'location', 'video_city');
    });

    var profile_type = 0;
    var previous_mapped_level = 0;
    var sitevideo_categories_slug = <?php echo json_encode($this->categories_slug); ?>;

    function showCustomFields(cat_value, cat_level) {

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

    var getProfileType = function (category_id) {
        var mapping = <?php echo Zend_Json_Encoder::encode(Engine_Api::_()->getDbtable('videoCategories', 'sitevideo')->getMapping(array('category_id', 'profile_type'))); ?>;
        for (i = 0; i < mapping.length; i++) {
            if (mapping[i].category_id == category_id)
                return mapping[i].profile_type;
        }
        return 0;
    }

    function clear(element)
    {
        for (var i = (element.options.length - 1); i >= 0; i--) {
            element.options[ i ] = null;
        }
    }

    function addOptions(element_value, element_type, element_updated, domready) {

        var element = $(element_updated);
        if (domready == 0) {
            switch (element_type) {
                case 'cat_dependency':
                    $('subcategory_id' + '-wrapper').style.display = 'none';
                    clear($('subcategory_id'));
                    $('subcategory_id').value = 0;
                    $('categoryname').value = sitevideo_categories_slug[element_value];

                case 'subcat_dependency':
                    $('subsubcategory_id' + '-wrapper').style.display = 'none';
                    clear($('subsubcategory_id'));
                    $('subsubcategory_id').value = 0;
                    $('subsubcategoryname').value = '';
                    if (element_type == 'subcat_dependency')
                        $('subcategoryname').value = sitevideo_categories_slug[element_value];
                    else
                        $('subcategoryname').value = '';
            }
        }

        if (element_value <= 0)
            return;

        var url = '<?php echo $this->url(array('module' => 'sitevideo', 'controller' => 'video', 'action' => 'get-video-categories', 'showAllCategories' => $this->showAllCategories), "default", true); ?>';
        en4.core.request.send(new Request.JSON({
            url: url,
            data: {
                format: 'json',
                element_value: element_value,
                element_type: element_type
            },
            onSuccess: function (responseJSON) {
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
                    sitevideo_categories_slug[categories[i]['category_id']] = categories[i]['category_slug'];
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
</script>