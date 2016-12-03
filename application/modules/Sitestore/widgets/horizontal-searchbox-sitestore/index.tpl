<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl .'application/modules/Sitestore/externals/scripts/core.js'); ?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl .'application/modules/Seaocore/externals/scripts/core.js'); ?>
<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>
<?php
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
?>
<?php if ($this->loaded_by_ajax): ?>
    <script type="text/javascript">
        var params = {
            requestParams:<?php echo json_encode($this->params) ?>,
            responseContainer: $$('.layout_sitestore_horizontal_searchbox_sitestore')
        }
        en4.sitestore.ajaxTab.attachEvent('<?php echo $this->identity ?>', params);
    </script>
<?php endif; ?>

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestore/externals/styles/style_sitestore.css'); ?>

<?php if ($this->showContent): ?>
<div class="sitestore_form_quick_search">
    <?php echo $this->form->setAttrib('class', 'sitestore-search-box')->render($this) ?>
</div>	
<?php endif; ?>


<script type="text/javascript">

    var doSearching = function(searchboxcategory_id) {

        var categoryElementExist = <?php echo $this->categoryElementExist; ?>;
        var searchboxcategory_id = 0;
        if (categoryElementExist == 1) {
            searchboxcategory_id = $('ajaxcategory_id').value;
        }

        if (searchboxcategory_id != 0) {

            var categoriesArray = <?php echo Zend_Json_Encoder::encode(Engine_Api::_()->getDbTable('categories', 'sitestore')->getCategoriesDetails($this->categoriesLevel)); ?>;

            $('searchBox').getElementById('category_id').value = categoriesArray[searchboxcategory_id].category_id;
            $('searchBox').getElementById('subcategory_id').value = categoriesArray[searchboxcategory_id].subcategory_id;
            $('searchBox').getElementById('subsubcategory_id').value = categoriesArray[searchboxcategory_id].subsubcategory_id;
            $('searchBox').getElementById('categoryname').value = encodeURIComponent(categoriesArray[searchboxcategory_id].categoryname);
            $('searchBox').getElementById('subcategoryname').value = encodeURIComponent(categoriesArray[searchboxcategory_id].subcategoryname);
            $('searchBox').getElementById('subsubcategoryname').value = encodeURIComponent(categoriesArray[searchboxcategory_id].subsubcategoryname);
        }

        $('searchBox').submit();
    }


    en4.core.runonce.add(function()
    {
        var item_count = 0;
        if($('titleAjax')) {
        var contentAutocomplete = new Autocompleter.Request.JSON('titleAjax', '<?php echo $this->url(array('action' => 'get-search-events'), "sitestore_general", true) ?>', {
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
                    if (token.sitestore_url != 'seeMoreLink') {
                        var choice = new Element('li', {'class': 'autocompleter-choices', 'html': token.photo, 'id': token.label, 'sitestore_url': token.sitestore_url, onclick: 'javascript:getPageResults("' + token.sitestore_url + '")'});
                        new Element('div', {'html': this.markQueryValue(token.label), 'class': 'autocompleter-choice'}).inject(choice);
                        this.addChoiceEvents(choice).inject(this.choices);
                        choice.store('autocompleteChoice', token);
                    }
                    if (token.sitestore_url == 'seeMoreLink') {
                        var titleAjax = $('titleAjax').value;
                        var choice = new Element('li', {'class': 'autocompleter-choices1', 'html': '', 'id': 'stopevent', 'sitestore_url': ''});
                        new Element('div', {'html': 'See More Results for ' + titleAjax, 'class': 'autocompleter-choicess', onclick: 'javascript:Seemore()'}).inject(choice);
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
                        var url = selected.retrieve('autocompleteChoice').sitestore_url;
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
    }
        if ($('locationSearch')) {
        var locationSearchField = <?php echo isset($_GET['locationSearch']) ? 1 : 0; ?>;
//        var autocomplete = new google.maps.places.Autocomplete(document.getElementById('locationSearch'));
//        google.maps.event.addListener(autocomplete, 'place_changed', function() {
//            var place = autocomplete.getPlace();
//            if (!place.geometry) {
//                return;
//            }
//
//            var myLocationDetails = {'latitude': place.geometry.location.lat(), 'longitude': place.geometry.location.lng(), 'location': document.getElementById('locationSearch').value, 'locationmiles': document.getElementById('locationmilesSearch').value};
//
//            en4.seaocore.locationBased.setLocationCookies(myLocationDetails);
//
//        });

        locationAutoSuggest('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.countrycities'); ?>', 'locationSearch', '');

        if (!locationSearchField) {

            var params = {
                'detactLocation': <?php echo $this->locationDetection; ?>,
                'fieldName': 'locationSearch',
                'noSendReq': 1,
                'locationmilesFieldName': 'locationmilesSearch',
                'locationmiles': <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationdefaultmiles', 1000); ?>,
                'reloadPage': 1,
            };

            en4.seaocore.locationBased.startReq(params);
        }
    }
    });

    function Seemore() {
        $('stopevent').removeEvents('click');
        var url = '<?php echo $this->url(array('action' => 'index'), "sitestore_general", true); ?>';
        window.location.href = url + "?titleAjax=" + encodeURIComponent($('titleAjax').value);
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
</script>