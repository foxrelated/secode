<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteadvsearch
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2014-08-06 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php
$baseUrl = $this->layout()->staticBaseUrl;

$this->headTranslate(array('You have not entered anything in the search box. Please write something in the search box and start searching again a search query.'));

$this->headScript()
        ->appendFile($baseUrl . 'externals/autocompleter/Observer.js')
        ->appendFile($baseUrl . 'externals/autocompleter/Autocompleter.js')
        ->appendFile($baseUrl . 'externals/autocompleter/Autocompleter.Local.js')
        ->appendFile($baseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>

<?php
//GET API KEY
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()
        ->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
?>

<!--START ADVANCED MEMBER JS WORK-->
<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitemember')): ?>
    <?php
    $this->headScript()
            ->appendFile($baseUrl . 'application/modules/Seaocore/externals/scripts/pinboard/mooMasonry.js')
            ->appendFile($baseUrl . 'application/modules/Seaocore/externals/scripts/pinboard/pinboard.js');
    ?>
<?php endif ?>
<!--END ADVANCED MEMBER JS WORK-->

<?php
$this->headLink()
        ->prependStylesheet($baseUrl . 'application/modules/Seaocore/externals/styles/styles.css')
        ->prependStylesheet($baseUrl . 'application/modules/Seaocore/externals/styles/style_infotooltip.css')
        ->prependStylesheet($baseUrl . 'application/modules/Seaocore/externals/styles/slider.css');
$this->headScript()
        ->appendFile($baseUrl . 'application/modules/Seaocore/externals/scripts/slider.js');

$this->headLink()->prependStylesheet($baseUrl . 'application/modules/Seaocore/externals/styles/style_board.css');
?>

<?php
$this->headLink()
        ->appendStylesheet($baseUrl . 'application/modules/Siteadvsearch/externals/styles/style_siteadvsearch.css')
?>

<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteevent')): ?>
    <?php
    $this->headScript()
            ->appendFile($baseUrl . 'externals/calendar/calendar.compat.js')
            ->appendFile($baseUrl . 'application/modules/Siteevent/externals/scripts/_commonFunctions.js')
            ->appendFile($baseUrl . 'application/modules/Siteevent/externals/scripts/core.js');
    ?>
<?php endif; ?>

<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitereview')): ?>
    <?php $this->headScript()->appendFile($baseUrl . 'application/modules/Sitereview/externals/scripts/core.js'); ?>
<?php endif; ?>

<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('feedback')): ?>
    <?php $this->headScript()->appendFile($baseUrl . 'application/modules/Feedback/externals/scripts/core.js'); ?>
<?php endif; ?>

<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitefaq')): ?>
    <?php include APPLICATION_PATH . '/application/modules/Sitefaq/views/scripts/helpful_content.tpl'; ?>
<?php endif; ?>

<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroup')): ?>
    <?php include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/common_style_css.tpl'; ?>
<?php endif; ?>

<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitebusiness')): ?>
    <?php include_once APPLICATION_PATH . '/application/modules/Sitebusiness/views/scripts/common_style_css.tpl'; ?>
<?php endif; ?>

<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestore')): ?>
    <?php include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl'; ?>
<?php endif; ?>

<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitestoreproduct')): ?>
    <?php $this->headScript()->appendFile($baseUrl . 'application/modules/Sitestoreproduct/externals/scripts/core.js'); ?>
<?php endif; ?>

<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitepage')): ?>
    <?php include APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/Adintegration.tpl'; ?>
    <?php include_once APPLICATION_PATH . '/application/modules/Sitepage/views/scripts/common_style_css.tpl'; ?>
<?php endif; ?>

<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitevideo')): ?>
    
<?php
$this->headLink()
        ->appendStylesheet($baseUrl . 'application/modules/Sitevideo/externals/styles/style_sitevideo.css');
?>
<?php $this->headScript()->appendFile($baseUrl . 'application/modules/Sitevideo/externals/scripts/core_video_lightbox.js'); ?>
<?php $this->headScript()->appendFile($baseUrl . 'application/modules/Sitevideo/externals/scripts/core.js'); ?>
<?php endif; ?>

<div class="asearch_searchbox">
    <h2><?php echo $this->translate('Search'); ?></h2>
    <div <?php if (!empty($this->show_resourcetype_option)): ?> class="asearch_searchbox_withresource"<?php endif; ?> >
        <span>
            <input type="text" autocomplete="off" value="" class='text suggested' id="search-content" onclick="showClass();" name="content" >

            <?php if ($this->showLocationSearch && $this->locationspecific): ?>

                <select name="searchLocation" id="searchLocation">
                    <?php foreach ($this->locationArray as $key => $locationElement): ?>
                        <option <?php
                        if (!empty($key) && $key == $this->locationValue) {
                            echo 'selected = selected';
                        }
                        ?> value="<?php echo $key ?>"><?php echo $locationElement; ?></option>
                        <?php endforeach; ?>
                </select>

            <?php elseif ($this->showLocationSearch && !$this->locationspecific): ?>

                <input style="width:<?php echo "30"; ?>px;" type='text' id="searchLocation" class='text suggested' name='searchLocation' size='20' maxlength='130' placeholder="<?php echo $this->translate('Select Location'); ?>"  autocomplete="off" <?php
                $searchLocationValue = $this->locationValue;
                if (!empty($searchLocationValue)) {
                    echo "value='$searchLocationValue'";
                }
                ?> />

            <?php endif; ?>      


            <span style="display: none;" id="all-search-loading"> 
                <img alt="Loading" src="<?php echo $baseUrl ?>application/modules/Siteadvsearch/externals/images/loading.gif" align="middle" />
            </span>
            <?php $allResourceType = array(); ?>
            <?php if (!empty($this->show_resourcetype_option)): ?>
                <?php if (isset($_GET['type'])): ?>
                    <?php $Currenttype = $_GET['type']; ?>
                <?php else: ?>
                    <?php $Currenttype = 'null'; ?>
                <?php endif; ?>
                <select  onchange="showContent($(this).value)" id="resource_type" name="resource_type">  
                    <?php $availableTypes = Engine_Api::_()->getApi('search', 'core')->getAvailableTypes(); ?>
                    <?php $arrayOption = array('' => 'Everything'); ?>
                    <?php $availableTypes = array_merge($arrayOption, $availableTypes); ?>
                    <?php foreach ($availableTypes as $key => $type): ?>
                        <option value="<?php if ($type != 'Everything'): ?><?php echo $type; ?><?php else: ?><?php echo 'all'; ?><?php endif; ?>" <?php if ($Currenttype == $type): ?>selected<?php endif; ?>><?php if ($type != 'Everything'): ?><?php echo $this->translate(strtoupper('ITEM_TYPE_' . $type)); ?><?php else: ?><?php echo $this->translate(strtoupper('Everything')); ?><?php endif; ?>
                        </option>
                        <?php $allResourceType[] = $type; ?>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
        </span>
        <?php if (!empty($this->show_resourcetype_option) || ($this->showLocationSearch && $this->locationspecific)): ?>
            <button onclick="showContent('<?php echo "search"; ?>');" type="button" id="search_button" name="searchButton">Search</button>
        <?php elseif (empty($this->show_resourcetype_option)): ?>
            <button onclick="showContent('<?php echo "searchbox"; ?>');" type="button" id="submitButton" name="submitButton">Search</button>
        <?php endif; ?>
    </div>
</div>
<?php if (count($this->items) > 1): ?>
    <div class="" id="">
        <div class="" style="display: none;" id="">
            <img alt="Loading" src="<?php echo $baseUrl ?>application/modules/Core/externals/images/loading.gif" align="left" />
        </div>
        <ul class='advsearch searchbox-stoprequest'>
            <?php $key = 0; ?>
            <?php $resourceType = array(); ?>
            <?php foreach ($this->items as $item): ?>
                <?php $blankUrl = $this->url(array('action' => 'index'), 'siteadvsearch_general', true) . '?query=&type=' . $item['resource_type']; ?>
                <?php if ($key < $this->max): ?>
                    <?php if ($item['resource_type'] != 'all'): ?>
                        <?php $privacyCheck = Engine_Api::_()->siteadvsearch()->canViewItemType($item['resource_type'], $item['listingtype_id']); ?>
                        <?php if (empty($privacyCheck)) continue; ?><?php endif; ?>
                    <li><a id="<?php echo $item['resource_type']; ?>" href="<?php echo $blankUrl; ?>" onclick="showContent('<?php echo $item['resource_type']; ?>');"><?php echo $this->translate($item['resource_title']); ?></a></li>
                <?php else: ?>
                    <?php break; ?>
                <?php endif; ?>
                <?php $key++ ?>
                <?php $resourceType[] = $item['resource_type']; ?>
            <?php endforeach; ?>
            <?php if (count($this->items) > $this->max): ?>
                <li class="tab_closed more_tab fleft" onclick="moreTabSwitchSiteadvsearch($(this));">
                    <div class="tab_pulldown_contents_wrapper">
                        <div class="tab_pulldown_contents">          
                            <ul>
                                <?php $key = 0; ?>
                                <?php foreach ($this->items as $item): ?>
                                    <?php $blankUrl = $this->url(array('action' => 'index'), 'siteadvsearch_general', true) . '?query=&type=' . $item['resource_type']; ?>
                                    <?php if ($key >= $this->max): ?>
                                        <?php if ($item['resource_type'] != 'all'): ?>
                                            <?php $privacyCheck = Engine_Api::_()->siteadvsearch()->canViewItemType($item['resource_type'], $item['listingtype_id']); ?>
                                            <?php if (empty($privacyCheck)) continue; ?><?php endif; ?>
                                        <li class="fleft"><a id="<?php echo $item['resource_type']; ?>" href="<?php echo $blankUrl; ?>" onclick="showContent('<?php echo $item['resource_type']; ?>');
                                                                addActiveClass($(this));"><?php echo $this->translate($item['resource_title']); ?></a></li>
                                        <?php endif; ?>
                                        <?php $key++ ?>
                                        <?php $resourceType[] = $item['resource_type']; ?>
                                    <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <a href="javascript:void(0);"><?php echo $this->translate('More +') ?><span></span></a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
<?php endif; ?>

<div id="search-content-type" style="display:none;"></div>
<div id="search-content-message"></div>

<div id="show-error-tip" style="display:none;">
    <div class="tip" >
        <span><?php echo $this->translate('Please Enter Search Key.'); ?></span>
    </div>
</div>

<div class="" id="siteadvsearch_page_loding_image" style="display:none;">
    <div class="seaocore_loading_image"></div>
</div>

<script type="text/javascript">

    $('search-content').addEvent('keyup', function (e) {
        if (e.key == 'enter')
            showContent('<?php echo "searchbox"; ?>');
        showAutosuggestContent(e.key);
    });

    function showAutosuggestContent(key) {

        if ($('search-content').value == '' || key == 'enter')
            $('all-search-loading').style.display = 'none';
        else if (key != 'up' && key != 'down')
            $('all-search-loading').style.display = 'inline-block';
    }

    en4.core.runonce.add(function () {
        var moreTabSwitchSiteadvsearch = window.moreTabSwitchSiteadvsearch = function (el) {
            el.toggleClass('seaocore_tab_open active');
            el.toggleClass('tab_closed');
        }
    });
    var type = '<?php echo $this->type; ?>';
    var searchValue = '<?php echo $this->search_value; ?>';
    document.getElementById('search-content').value = searchValue;

    if (type != '') {
        if (type == 'forum_topic')
            type = 'forum'
        showContent(type);
    }
    else {
        showContent('<?php echo "all" ?>');
    }
    var ShowTab;
    var oldPage;
    var listingTypeId;
    var resource_type_review;
    var sendRequest;
    function showContent(resource_type) {

        window.onscroll = '';

        if (typeof (sendRequest) != 'undefined') {
            sendRequest.cancel();
        }

        if (resource_type == 'searchbox')
            resource_type = ShowTab;

        if (document.getElementById(resource_type))
            document.getElementById(resource_type).href = "javascript:void(0)";

        if (resource_type == 'search') {
            var resourceTypeId = document.getElementById("resource_type");
            if (resourceTypeId) {
                var currentResourceType = resourceTypeId.options[resourceTypeId.selectedIndex].value;
                resource_type = currentResourceType;
            }
        }

        searchValue = encodeURIComponent(document.getElementById('search-content').value);
        var browseUrl = '<?php echo $this->url(array('action' => 'index'), 'siteadvsearch_general', true); ?>' + '?query=' + searchValue + '&type=' + resource_type;

        var searchLocationSet = 0;
        if (document.getElementById('searchLocation') && document.getElementById('searchLocation').value != 0 && document.getElementById('searchLocation').value != null) {
            browseUrl = browseUrl + '&searchLocation=' + document.getElementById('searchLocation').value;
            searchLocationSet = 1;
        }

        if (searchLocationSet == 0) {
<?php if (isset($_GET['searchLocation']) && $_GET['searchLocation']): ?>
                browseUrl = browseUrl + '&searchLocation=' + '<?php echo $_GET['searchLocation']; ?>';
<?php endif; ?>
        }

        if (history.pushState) {
            history.pushState({}, document.title, browseUrl);
        } else {
            window.location.hash = browseUrl;
        }

        var explodedResourceType = resource_type.split('_');
        listingTypeId = explodedResourceType['2'];

        if ('sitereview_listingtype_' + listingTypeId == resource_type) {
            listingTypeId = explodedResourceType['2'];
            resource_type_review = 'sitereview_listingtype_' + listingTypeId;
        }
        else {
            listingTypeId = 0;
        }

        if (typeof ShowTab != 'undefined') {
            if (document.getElementById(ShowTab))
                document.getElementById(ShowTab).className = "";
            else if (document.getElementById('all'))
                document.getElementById('all').className = "";
        }

        if (document.getElementById('search-content-type'))
            document.getElementById('search-content-type').style.display = 'none';
        document.getElementById('siteadvsearch_page_loding_image').style.display = 'block';

        var checkresourceType = '<?php echo Zend_Json_Encoder::encode($resourceType) ?>'.indexOf(resource_type);

        if (listingTypeId == 0) {
            var coreResourceType = '<?php echo Zend_Json_Encoder::encode($allResourceType) ?>'.indexOf(resource_type);
        }
        else {
            var coreResourceType = '<?php echo Zend_Json_Encoder::encode($allResourceType) ?>'.indexOf('sitereview_listing');
        }

        if (checkresourceType != '-1' && resource_type != 'sitereview_listing') {
            if (document.getElementById(resource_type))
                document.getElementById(resource_type).className = "bold";
            if (document.getElementById("resource_type")) {
                if (coreResourceType != '-1' && listingTypeId == 0)
                    document.getElementById("resource_type").value = resource_type;
                else if (coreResourceType != '-1') {
                    document.getElementById("resource_type").value = 'sitereview_listing';
                }
                else
                    document.getElementById("resource_type").value = 'all';
            }
        }
        else {
            if (document.getElementById('all'))
                document.getElementById('all').className = "bold";
            if (resource_type == 'sitereview_listing')
                resource_type = 'sitereview_listing';
            else if (coreResourceType != '-1' && document.getElementById("resource_type"))
                document.getElementById("resource_type").value = resource_type;
            else
                resource_type = 'all';
        }

        if (searchValue == '' && (resource_type == 'all' || resource_type == 'sitereview_listing')) {
            $('search-content-message').innerHTML = '<div class="tip" ><br />' + '<span>' + '<?php echo $this->string()->escapeJavascript($this->translate("You have not entered anything in the search box. Please write something in the search box and start searching again a search query.")); ?>' + '</span>' + '</div>';
            $('search-content-message').style.display = 'block';
            document.getElementById('siteadvsearch_page_loding_image').style.display = 'none';
            ShowTab = resource_type;
            return;
        }
        else {
            $('search-content-message').style.display = 'none';
            
        }
        
        if(resource_type == 'blog' && <?php echo Engine_Api::_()->hasModuleBootstrap('ynblog');?> )
          var sendUrl = '<?php echo $this->url(array('module' => 'siteadvsearch', 'controller' => 'index', 'action' => 'show-content', 'showLocationSearch' => $this->showLocationSearch, 'showLocationBasedContent' => $this->showLocationBasedContent), "default", true); ?>' + '/resource_type/' + resource_type + '?search=' + searchValue;
        else 
          var sendUrl = requestUrl(resource_type);

        sendRequest = new Request.HTML({
            method: 'post',
            url: sendUrl,
            data: {
                default_view: 2,
                format: 'html'
            },
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {

                if (document.getElementById(resource_type))
                    document.getElementById(resource_type).href = browseUrl;
                var str = responseHTML;
                var res = str.replace(new RegExp('javascript:pageAction', 'g'), 'AdvSavevalues');
                if (document.getElementById('search-content-type')) {
                    document.getElementById('search-content-type').innerHTML = res;
                    document.getElementById('search-content-type').style.display = 'block';
                }

                en4.core.runonce.trigger();
                if ($('location')) {
                    $('location').addEvent('keyup', function (e) {
                        if (e.key == 'enter')
                            AdvSavevalues();
                    });
                }
                if ($('search')) {
                    $('search').addEvent('keyup', function (e) {
                        if (e.key == 'enter')
                            AdvSavevalues();
                    });
                }
                if ($('search_text')) {
                    $('search_text').addEvent('keyup', function (e) {
                        if (e.key == 'enter')
                            AdvSavevalues();
                    });
                }
                if ($('displayname')) {
                    $('displayname').addEvent('keyup', function (e) {
                        if (e.key == 'enter')
                            AdvSavevalues();
                    });
                }
                if ($('text')) {
                    $('text').addEvent('keyup', function (e) {
                        if (e.key == 'enter')
                            AdvSavevalues();
                    });
                }
                $('siteadvsearch_page_loding_image').style.display = 'none';
                
                var checkresourceType = '<?php echo Zend_Json_Encoder::encode($resourceType) ?>'.indexOf(resource_type);
                if (checkresourceType != '-1')
                    changeAttributes(resource_type);
                $('siteadvsearch_page_loding_image').style.display = 'none';
                if (document.getElementById('search-content-type')) {
                 if (SmoothboxSEAO) {
                            SmoothboxSEAO.bind($('search-content-type'));
                        }
                    }
            }
        });
        en4.core.request.send(sendRequest, {
            'force': true
        });

        ShowTab = resource_type;
    }

    var AdvSavevalues = function (page) {
        window.onscroll = '';

        if (ShowTab == 'music_playlist' || ShowTab == 'classified' || ShowTab == 'blog' || ShowTab == 'video' || ShowTab == 'album' || ShowTab == 'poll') {
            if (ShowTab == 'album' && '<?php echo Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitealbum') ?>')
                formElement = $$('.browsesitealbums_criteria')[0];
            else
                formElement = $$('.global_form_box')[0];
        }
        else if (ShowTab == 'group' || ShowTab == 'event') {
            formElement = $$('.filters')[0];
        }
        else if (ShowTab == 'user' || ShowTab == 'feedback' || ShowTab == 'sitepage_page' || ShowTab == 'sitebusiness_business' || ShowTab == 'sitegroup_group' || ShowTab == 'sitestore_store' || ShowTab == 'document' || ShowTab == 'recipe' || ShowTab == 'list_listing' || ShowTab == 'sitestoreproduct_product' || ShowTab == 'siteevent_event' || ShowTab == 'sitefaq_faq' || ShowTab == 'sitetutorial_tutorial' || ShowTab == resource_type_review || ShowTab ==  'sitevideo_video') {
            formElement = $$('.field_search_criteria')[0];
        }

        if (document.getElementById('search')) {
            document.getElementById('search-content').value = document.getElementById('search').value;
        }
        else if (document.getElementById('search_text')) {
            document.getElementById('search-content').value = document.getElementById('search_text').value;
        }
        else if ($('displayname')) {
            document.getElementById('search-content').value = document.getElementById('displayname').value;
        }
        else if (document.getElementById('text')) {
            document.getElementById('search-content').value = document.getElementById('text').value;
        }

        searchValue = document.getElementById('search-content').value;

        var pageurl = requestUrl(ShowTab);

        if (typeof formElement != 'undefined') {
            currentValues = formElement.toQueryString();
        }

        $('search-content-message').innerHTML = '<center><img src="<?php echo $baseUrl ?>application/modules/Seaocore/externals/images/loading.gif" /></center>';
        if (document.getElementById('search-content-type'))
            $('search-content-type').style.display = 'none';
        $('search-content-message').style.display = 'block';

        if (typeof currentValues != 'undefined') {
            if (typeof page_url != 'undefined') {
                var param = (currentValues ? currentValues + '&' : '') + 'is_ajax=0&format=html&page_url=' + page_url;
            }
            else {
                var param = (currentValues ? currentValues + '&' : '') + 'is_ajax=0&format=html';
            }
        }
        else {
            var param = 'is_ajax=0&format=html';
        }

        //START PAGINATION WORK
        if (ShowTab == 'sitestore_store')
            var checkString = '&store=';
        else
            var checkString = '&page=';

        var regExpCheck = new RegExp(checkString, "gi");
        var lengthofCheck = param.match(regExpCheck) ? param.match(regExpCheck).length : 0;
        if (lengthofCheck == 0) {
            var replaceString = param + checkString + page;
            var param = param.replace(new RegExp(param, 'g'), replaceString);
        }
        else {
            var matchString = checkString + oldPage;
            var regExp = new RegExp(matchString, "gi");
            var length = param.match(regExp) ? param.match(regExp).length : 0;
            if (length == 0 || typeof oldPage == 'undefined')
                var param = param.replace(new RegExp(checkString, 'g'), checkString + page);
            else if (length > 0)
                var param = param.replace(new RegExp(matchString, 'g'), checkString + page);
        }
        //END PAGINATION WORK

        var request = new Request.HTML({
            url: pageurl,
            method: 'get',
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                oldPage = page;
                if (document.getElementById(ShowTab))
                    document.getElementById(ShowTab).href = '#';
                var paginateStr = responseHTML;
                $('search-content-type').style.display = 'block';
                var paginateRes = paginateStr.replace(new RegExp('javascript:pageAction', 'g'), 'AdvSavevalues');
                if ($('search-content-type')) {
                    $('search-content-type').innerHTML = paginateRes;
                }

                var checkresourceType = '<?php echo Zend_Json_Encoder::encode($resourceType) ?>'.indexOf(ShowTab);
                if (checkresourceType != '-1')
                    changeAttributes(ShowTab);
                en4.core.runonce.trigger();
                if ($('location')) {
                    $('location').addEvent('keyup', function (e) {
                        if (e.key == 'enter')
                            AdvSavevalues();
                    });
                }
                if ($('search')) {
                    $('search').addEvent('keyup', function (e) {
                        if (e.key == 'enter')
                            AdvSavevalues();
                    });
                }
                if ($('search_text')) {
                    $('search_text').addEvent('keyup', function (e) {
                        if (e.key == 'enter')
                            AdvSavevalues();
                    });
                }
                if ($('displayname')) {
                    $('displayname').addEvent('keyup', function (e) {
                        if (e.key == 'enter')
                            AdvSavevalues();
                    });
                }
                if ($('text')) {
                    $('text').addEvent('keyup', function (e) {
                        if (e.key == 'enter')
                            AdvSavevalues();
                    });
                }
                $('search-content-message').style.display = 'none';
                                $('siteadvsearch_page_loding_image').style.display = 'none';
                if (document.getElementById('search-content-type')) {
                 if (SmoothboxSEAO) {
                            SmoothboxSEAO.bind($('search-content-type'));
                        }
                    }
            }
        });
        request.send(param);
    }

    function requestUrl(item_type) {

        var checkresourceType = '<?php echo Zend_Json_Encoder::encode($resourceType) ?>'.indexOf(item_type);

        if (checkresourceType != '-1') {
            if (item_type == 'group') {
                var url = '<?php echo $this->url(array('module' => 'siteadvsearch', 'controller' => 'index', 'action' => 'browse-group'), "default", true); ?>' + '?search_text=' + searchValue;
            }
            else if (item_type == 'event') {
                var url = '<?php echo $this->url(array('module' => 'siteadvsearch', 'controller' => 'index', 'action' => 'browse-event'), "default", true); ?>' + '?search_text=' + searchValue;
            }
            else if (item_type == 'blog') {
                var url = '<?php echo $this->url(array('module' => 'siteadvsearch', 'controller' => 'index', 'action' => 'browse-blog'), "default", true); ?>' + '?search=' + searchValue;
            }
            else if (item_type == 'classified') {
                var url = '<?php echo $this->url(array('module' => 'siteadvsearch', 'controller' => 'index', 'action' => 'browse-classified'), "default", true); ?>' + '?search=' + searchValue;
            }
            else if (item_type == 'poll') {
                var url = '<?php echo $this->url(array('module' => 'siteadvsearch', 'controller' => 'index', 'action' => 'browse-poll'), "default", true); ?>' + '?search=' + searchValue;
            }
            else if (item_type == 'video') {
                var url = '<?php echo $this->url(array('module' => 'siteadvsearch', 'controller' => 'index', 'action' => 'browse-video'), "default", true); ?>' + '?text=' + searchValue;
            }
            else if (item_type == 'music_playlist') {
                var url = '<?php echo $this->url(array('module' => 'siteadvsearch', 'controller' => 'index', 'action' => 'browse-music'), "default", true); ?>' + '?search=' + searchValue;
            }
            else if (item_type == 'album') {
                var url = '<?php echo $this->url(array('module' => 'siteadvsearch', 'controller' => 'index', 'action' => 'browse-album'), "default", true); ?>' + '?search=' + searchValue;
            }
            else if (item_type == 'forum') {
                var url = '<?php echo $this->url(array('module' => 'siteadvsearch', 'controller' => 'index', 'action' => 'browse-forum'), "default", true); ?>' + '?search=' + searchValue;
            }
            else if (item_type == 'user') {
                if ('<?php echo Engine_Api::_()->siteadvsearch()->getWidgetInfo(); ?>' == 1) {
                    var url = '<?php echo $this->url(array('module' => 'siteadvsearch', 'controller' => 'index', 'action' => 'browse-member'), "default", true); ?>' + '?search=' + searchValue;
                }
                else {
                    var url = '<?php echo $this->url(array('module' => 'siteadvsearch', 'controller' => 'index', 'action' => 'browse-member'), "default", true); ?>' + '?displayname=' + searchValue;
                }
            }
            else if (item_type == 'feedback' || item_type == 'document' || item_type == 'recipe' || item_type == 'list_listing' || item_type == 'sitepage_page' || item_type == 'sitebusiness_business' || item_type == 'sitegroup_group' || item_type == 'sitestore_store' || item_type == 'sitestoreproduct_product' || item_type == 'siteevent_event' || item_type == 'sitefaq_faq' || item_type == 'sitetutorial_tutorial' || item_type == resource_type_review || item_type == 'sitevideo_video' || item_type == 'sitevideo_channel') {
                var url = '<?php echo $this->url(array('module' => 'siteadvsearch', 'controller' => 'index', 'action' => 'browse-page'), "default", true); ?>' + '/listingtype_id/' + listingTypeId + '/resource_type/' + item_type + '?search=' + searchValue;
            }
            else {
                var url = '<?php echo $this->url(array('module' => 'siteadvsearch', 'controller' => 'index', 'action' => 'show-content', 'showLocationSearch' => $this->showLocationSearch, 'showLocationBasedContent' => $this->showLocationBasedContent), "default", true); ?>' + '/resource_type/' + item_type + '?search=' + searchValue;
            }
        }
        else {
            var url = '<?php echo $this->url(array('module' => 'siteadvsearch', 'controller' => 'index', 'action' => 'show-content', 'showLocationSearch' => $this->showLocationSearch, 'showLocationBasedContent' => $this->showLocationBasedContent), "default", true); ?>' + '/resource_type/' + item_type + '?search=' + searchValue;
        }

        var searchLocationSet = 0;
        if (document.getElementById('searchLocation') && document.getElementById('searchLocation').value) {
            searchLocationSet = 1;
        }

        var searchLocationValue = '<?php echo (isset($_GET['searchLocation']) ? $_GET['searchLocation'] : ''); ?>';
        if (searchLocationSet) {
            searchLocationValue = document.getElementById('searchLocation').value;
        }
        if (searchLocationValue) {
            if (searchLocationValue == 0) {
                searchLocationValue = '';
            }
            url = url + '&searchLocation=' + searchLocationValue;

            if (item_type == 'sitepage_page' || item_type == 'sitebusiness_business' || item_type == 'sitegroup_group' || item_type == 'sitestore_store' || item_type == 'list_listing') {
                var locationColumn = item_type.split('_');
                url = url + '&' + locationColumn[0] + '_location=' + searchLocationValue;
            }
            else if (item_type == 'siteevent_event' || item_type == 'sitestoreproduct_product' || item_type == 'album' || item_type == 'user'  || item_type == 'sitevideo_video' || item_type == 'sitevideo_channel') {
                url = url + '&location=' + searchLocationValue;
            }
        }
        return url;
    }

    function changeAttributes(item_type) {

        if (document.getElementById('displayname')) {
            $$('.field_search_criteria').removeEvents('submit').addEvent('submit', function (e) {
                e.stop();
            });
        }
        if (document.getElementById('filter_form')) {
            document.getElementById('filter_form').removeEvents('submit').addEvent('submit', function (e) {
                e.stop();
            });
        }
        if (document.getElementById('search_text')) {
            $$('.filters').removeEvents('submit').addEvent('submit', function (e) {
                e.stop();
            });
        }
        if (item_type == 'user' || item_type == 'siteevent_event' || item_type == 'classified' || item_type == 'sitefaq_faq' || item_type == 'sitetutorial_tutorial' || item_type == 'document' || item_type == 'list_listing' || item_type == 'recipe' || item_type == resource_type_review || item_type == 'sitestoreproduct_product' || item_type == 'sitevideo_video' || item_type == 'sitevideo_channel') {
            if (document.getElementById('network_id'))
                document.getElementById('network_id').setAttribute('onchange', "AdvSavevalues('network')");
            if (document.getElementById('done'))
                document.getElementById('done').setAttribute('onclick', 'AdvSavevalues()');
            if (document.getElementById('eventType'))
                document.getElementById('eventType').setAttribute('onchange', 'AdvSavevalues()');
        }
        else if (item_type == 'feedback') {
            document.getElementById('done').setAttribute('onclick', 'AdvSavevalues()');
            document.getElementById('search').setAttribute('onchange', 'AdvSavevalues()');
        }
        else if (item_type == 'blog') {
            document.getElementById('search').setAttribute('onchange', 'AdvSavevalues()');
        }
        else if (item_type == 'album' && '<?php echo Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitealbum') ?>' && document.getElementById('done')) {
            document.getElementById('done').setAttribute('onclick', 'AdvSavevalues()');
        }
        else {
            if (document.getElementById('done'))
                document.getElementById('done').setAttribute('onchange', 'AdvSavevalues()');
        }
        if (document.getElementById('has_review'))
            document.getElementById('has_review').setAttribute('onchange', 'AdvSavevalues()');
        if (document.getElementById('orderby-label'))
            document.getElementById('orderby-label').setAttribute('onchange', 'AdvSavevalues()');
        if (document.getElementById('closed-label'))
            document.getElementById('closed-label').setAttribute('onchange', 'AdvSavevalues()');
        if (document.getElementById('show-label'))
            document.getElementById('show-label').setAttribute('onchange', 'AdvSavevalues()');
        if (document.getElementById('extra-done'))
            document.getElementById('extra-done').setAttribute('onclick', 'AdvSavevalues()');
        if (document.getElementById('orderby'))
            document.getElementById('orderby').setAttribute('onchange', 'AdvSavevalues()');
        if (document.getElementById('show'))
            document.getElementById('show').setAttribute('onchange', 'AdvSavevalues()');
        if (document.getElementById('view_view'))
            document.getElementById('view_view').setAttribute('onchange', 'AdvSavevalues()');
        if (document.getElementById('closed'))
            document.getElementById('closed').setAttribute('onchange', 'AdvSavevalues()');
        if (document.getElementById('offer_type'))
            document.getElementById('offer_type').setAttribute('onchange', 'AdvSavevalues()');
        if (document.getElementById('badge_id'))
            document.getElementById('badge_id').setAttribute('onchange', 'AdvSavevalues()');

        //START EVENT WORK
        if (item_type == 'event' || item_type == 'album' || item_type == 'group') {
            if (document.getElementById('category_id'))
                document.getElementById('category_id').setAttribute('onchange', 'AdvSavevalues()');
        }
        if (document.getElementById('view'))
            document.getElementById('view').setAttribute('onchange', 'AdvSavevalues()');
        if (document.getElementById('order'))
            document.getElementById('order').setAttribute('onchange', 'AdvSavevalues()');
        //END EVENT WORK

        //START ALBUM WORK
        if (document.getElementById('sort'))
            document.getElementById('sort').setAttribute('onchange', 'AdvSavevalues()');
        //END ALBUM WORK

        //START CLASSIFIED WORK
        if (document.getElementById('category'))
            document.getElementById('category').setAttribute('onchange', 'AdvSavevalues()');
        //END CLASSIFIED WORK

        //START FEEDBACK WORK
        if (document.getElementById('stat'))
            document.getElementById('stat').setAttribute('onchange', 'AdvSavevalues()');
        //END FEEDBACK WORK
    }

    function showClass(event) {
        if ($$('.searchbox-stoprequest').hasClass('searchbox-remove')) {
            $$('.searchbox-stoprequest').removeClass('searchbox-remove')
        }

        if (event == 'enter' && $('search-content').value != '') {

            $('all-search-loading').style.display = 'none';
            var elements = new Array();
            var z = 1;
            elements = hideClass('searchbox-stoprequest');
            for (i in elements) {
                if (z == 1)
                    elements[i].addClass('searchbox-remove');
                z++;
            }
        }
    }

    function hideLoadingImgae() {
        $('all-search-loading').style.display = 'none';
    }


    function hideClass(classname) {
        var node = document.getElementsByTagName("body")[0];
        var a = [];
        var re = new RegExp('\\b' + classname + '\\b');
        var els = node.getElementsByTagName("*");
        for (var i = 0, j = els.length; i < j; i++)
            if (re.test(els[i].className))
                a.push(els[i]);
        return a;
    }

    $('search-content').addEvent('keyup', function (e) {
        if (e.key == 'enter')
            showClass('enter');
        else {
            showClass('keyup');
        }
    });

    var contentAutocomplete;
    en4.core.runonce.add(function ()
    {

        $(document.body).addEvent('click', function (event) {
            var elements = new Array();
            var z = 1;
            elements = hideClass('searchbox-stoprequest');
            for (i in elements) {
                if (z == 1)
                    elements[i].addClass('searchbox-remove');
                z++;
                hideLoadingImgae();
            }
        });

        contentAutocomplete = new Autocompleter.Request.JSON('search-content', '<?php echo $this->url(array('module' => 'siteadvsearch', 'controller' => 'index', 'action' => 'get-search-result'), "default", true) ?>', {
            'postVar': 'text',
            'cache': false,
            'minLength': 1,
            'selectFirst': false,
            'selectMode': 'pick',
            'autocompleteType': 'tag',
            'className': 'tag-autosuggest searchbox-stoprequest',
            'maxChoices': 10,
            'customChoices': true,
            'filterSubset': true,
            'multiple': false,
            'injectChoice': function (token) {
                if (typeof token.label != 'undefined') {
                    if (token.item_url != 'seeMoreLink') {
                        var choice = new Element('li', {'class': 'autocompleter-choices', 'html': token.photo, 'item_url': token.item_url, onclick: 'javascript:getPageResults("' + token.item_url + '")'});

                        var divEl = new Element('div', {
                            'html': token.type ? '<span class="autocompleter-queried">' + this.options.markQueryValueCustom.call(this, (token.label)) + '</span>' : token.label,
                            'class': 'autocompleter-choice'
                        });

                        new Element('div', {
                            'html': token.type, //this.markQueryValue(token.type)  
                            'class': 'seaocore_txt_light f_small'
                        }).inject(divEl);

                        divEl.inject(choice);
                        new Element('input', {
                            'type': 'hidden',
                            'value': JSON.encode(token)
                        }).inject(choice);

                        this.addChoiceEvents(choice).inject(this.choices);
                        choice.store('autocompleteChoice', token);
                    }
                    if (token.item_url == 'seeMoreLink') {
                        var titleSearch = '"' + $('search-content').value + '"';
                        var choice = new Element('li', {'class': 'autocompleter-choices', 'html': '', 'id': 'stopevent', 'item_url': ''});
                        var seeMoreText = '<?php echo $this->string()->escapeJavascript($this->translate('See more results for') . ' '); ?>';
                        new Element('div', {'html': seeMoreText + titleSearch, 'class': 'autocompleter-choicess', onclick: 'javascript:showContent("searchbox")'}).inject(choice);
                        this.addChoiceEvents(choice).inject(this.choices);
                        choice.store('autocompleteChoice', token);
                    }
                }
            },
            onShow: function () {
                $('all-search-loading').style.display = 'none';
            },
            markQueryValueCustom: function (str) {
                return (!this.options.markQuery || !this.queryValue) ? str
                        : str.replace(new RegExp('(' + ((this.options.filterSubset) ? '' : '^') + this.queryValue.escapeRegExp() + ')', (this.options.filterCase) ? '' : 'i'), '<span class="seaocore_txt_light">$1</span>');
            },
        });
        contentAutocomplete.addEvent('onSelection', function (element, selected, value, input) {
            if ($('search-content').value != '') {
                window.addEvent('keyup', function (e) {
                    if (e.key == 'enter') {
                        if (selected.retrieve('autocompleteChoice') != 'null') {
                            $('siteadvsearch_page_loding_image').style.display = 'none';
                            var url = selected.retrieve('autocompleteChoice').item_url;
                            window.location.href = url;
                        }
                    }
                });
            }
        });

        contentAutocomplete.addEvent('onComplete', function () {
            $('all-search-loading').style.display = 'none';
        });

    });

    function getPageResults(url) {
        if (url != 'null') {
            window.location.href = url;
        }
    }

    var addActiveClass = function (element) {
        if (element.tagName.toLowerCase() == 'a') {
            element = element.getParent('li');
        }

        var myContainer = element.getParent('ul');
        myContainer.getElements('li').removeClass('active');
        element.addClass('active');
    }
</script>

<?php if ($this->showLocationSearch): ?>
    <style type="text/css">
        /*when Location search enabled*/
        .layout_siteadvsearch_search_contents .asearch_searchbox span input[type="text"] {
            width:75%;
        }
    </style>
<?php endif; ?>