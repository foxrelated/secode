<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if (!empty($this->is_ajax_load) && !empty($this->titleLink) && ($this->titleLinkPosition == 'top')) : ?>
    <span class="sitegroup_top_link">
        <?php echo $this->titleLink; ?>
    </span>
<?php endif; ?>
<?php
//GET API KEY
$apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
?>
<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/scripts/core.js');
?>
<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/styles.css'); ?>
<?php include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/common_style_css.tpl'; ?>
<?php
$this->headLink()
        ->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/styles/sitegroup-tooltip.css');
?>
<?php
$enableBouce = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.map.sponsored', 1);
$recently_randum_isthumb = Zend_Registry::isRegistered('sitegroup_is_random_thumb') ? Zend_Registry::get('sitegroup_is_random_thumb') : null;
$currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');
?>

<?php if ($this->is_ajax_load): ?>

    <div class="layout_core_container_tabs">
        <?php if (($this->list_view && $this->grid_view) || ($this->map_view && $this->grid_view) || ($this->list_view && $this->map_view) || count($this->tabs) > 1): ?>
            <?php if (count($this->tabs) > 1): ?>
                <div class="tabs_alt tabs_parent">
                <?php else: ?>
                    <div class="sitegroup_view_select">
                    <?php endif; ?>
                    <?php if (count($this->sitegroupsitegroup)): ?>
                        <ul id="main_tabs">
                            <?php if (count($this->tabs) > 1): ?>
                                <?php $active = true; ?> 
                                <?php foreach ($this->tabs as $key => $tab): ?>
                                    <?php $class = $active ? 'active' : '' ?>
                                    <?php $active = false; ?> 
                                    <li class = '<?php echo $class ?>'  id = '<?php echo 'sitegroup_home_group_' . $key . '_tab' ?>'>
                                        <a href='javascript:void(0);'  onclick="showListSitegroup('<?php echo $tab['tabShow']; ?>', '<?php echo $key; ?>');"><?php echo $this->translate($tab['title']) ?></a>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>  

                            <?php if (($this->list_view && $this->grid_view) || ($this->map_view && $this->grid_view) || ($this->list_view && $this->map_view)): ?>
                                <?php if ($this->enableLocation && $this->map_view): ?>
                                    <?php $latitude = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.map.latitude', 0); ?>
                                    <?php $longitude = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.map.longitude', 0); ?>
                                    <?php $defaultZoom = Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.map.zoom', 1); ?>
                                    <li class="seaocore_tab_select_wrapper fright">
                                        <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("Map View") ?></div>
                                        <span class="seaocore_tab_icon tab_icon_map_view" onclick="rswitchviewGroup(2)"></span>
                                    </li>
                                <?php endif; ?>
                                <?php if ($this->grid_view): ?> 
                                    <li class="seaocore_tab_select_wrapper fright">
                                        <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("Grid View") ?></div>
                                        <span class="seaocore_tab_icon tab_icon_grid_view" onclick="rswitchviewGroup(1)"></span>
                                    </li> 
                                <?php endif; ?>
                                <?php if ($this->list_view): ?>
                                    <li class="seaocore_tab_select_wrapper fright">
                                        <div class="seaocore_tab_select_view_tooltip"><?php echo $this->translate("List View") ?></div>
                                        <span class="seaocore_tab_icon tab_icon_list_view" onclick="rswitchviewGroup(0)"></span>
                                    </li>
                                <?php endif; ?>
                            <?php endif; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <div id="dynamic_app_info_group">
                <?php
                if (!empty($recently_randum_isthumb)) {
                    include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/_recently_popular_random_group.tpl';
                }
                ?>
            </div>
        </div>

        <?php if (!empty($this->is_ajax_load) && !empty($this->titleLink) && ($this->titleLinkPosition == 'bottom')) : ?>
            <span class="sitegroup_bottom_link">
                <?php echo $this->titleLink; ?>
            </span>
        <?php endif; ?>

        <script type="text/javascript" >
            function rswitchviewGroup(flage) {
                if (flage == 2) {
                    if ($('rmap_canvas_view_group')) {
                        $('rmap_canvas_view_group').style.display = 'block';
                        google.maps.event.trigger(rmap_group, 'resize');
                        rmap_group.setZoom(<?php echo $defaultZoom; ?>);
                        rmap_group.setCenter(new google.maps.LatLng(<?php echo $latitude; ?>,<?php echo $longitude; ?>));
                    }
                    if ($('rgrid_view_group'))
                        $('rgrid_view_group').style.display = 'none';
                    if ($('rimage_view_group'))
                        $('rimage_view_group').style.display = 'none';
                } else if (flage == 1) {
                    if ($('rmap_canvas_view_group'))
                        $('rmap_canvas_view_group').style.display = 'none';
                    if ($('rgrid_view_group'))
                        $('rgrid_view_group').style.display = 'none';
                    if ($('rimage_view_group'))
                        $('rimage_view_group').style.display = 'block';
                } else {
                    if ($('rmap_canvas_view_group'))
                        $('rmap_canvas_view_group').style.display = 'none';
                    if ($('rgrid_view_group'))
                        $('rgrid_view_group').style.display = 'block';
                    if ($('rimage_view_group'))
                        $('rimage_view_group').style.display = 'none';
                }
            }
        </script>
        <script type="text/javascript">

            /* moo style */
            en4.core.runonce.add(function () {
                if ($('rimage_view_group')) {
                    //showtooltipGroup();
                }
    <?php if ($this->enableLocation && $this->map_view): ?>
                    rinitializeGroup();
    <?php endif; ?>
                rswitchviewGroup(<?php echo $this->defaultView ?>);
                $$('.tab_layout_sitegroup_recently_popular_random_sitegroup').addEvent('click', function () {

                    google.maps.event.trigger(rmap_group, 'resize');
                    rmap_group.setZoom(<?php echo $defaultZoom; ?>);
                    rmap_group.setCenter(new google.maps.LatLng(<?php echo $latitude; ?>,<?php echo $longitude; ?>));
                });
            });

            //var showtooltipGroup = function (){
            //  if($('rimage_view_group')){
            //  //opacity / display fix
            //	$$('.sitegroup_tooltip').setStyles({
            //		opacity: 0,
            //		display: 'block'
            //	});
            //	//put the effect in place
            //	$$('.jq-sitegroup_tooltip li').each(function(el,i) {
            //		el.addEvents({
            //			'mouseenter': function() {
            //				el.getElement('div').fade('in');
            //			},
            //			'mouseleave': function() {
            //				el.getElement('div').fade('out');
            //			}
            //		});
            //	});
            //  }
            //}
        </script>

        <script type="text/javascript">

            var showListSitegroup = function (tabshow, tabName) {
    <?php foreach ($this->tabs as $key => $tab): ?>
                    if ($('<?php echo 'sitegroup_home_group_' . $key . '_tab' ?>'))
                        $('<?php echo 'sitegroup_home_group_' . $key . '_tab' ?>').erase('class');
    <?php endforeach; ?>
                if ($('sitegroup_home_group_' + tabName + '_tab'))
                    $('sitegroup_home_group_' + tabName + '_tab').set('class', 'active');

                if ($('dynamic_app_info_group') != null) {

                    $('dynamic_app_info_group').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitegroup/externals/images/loader.gif" class="sitegroup_tabs_loader_img" /></center>';
                }

                var request = new Request.HTML({
                    'url': '<?php echo $this->url(array(), 'sitegroup_ajaxhomelist', true) ?>',
                    'data': {
                        'format': 'html',
                        'task': 'ajax',
                        'tab_show': tabshow,
                        'list_limit':<?php echo $this->active_tab_list; ?>,
                        'grid_limit':<?php echo $this->active_tab_image; ?>,
                        'list_view':<?php echo $this->list_view; ?>,
                        'grid_view':<?php echo $this->grid_view; ?>,
                        'map_view':<?php echo $this->map_view; ?>,
                        'category_id':<?php echo $this->category_id; ?>,
                        'defaultView':<?php echo $this->defaultView; ?>,
                        'columnWidth':<?php echo $this->columnWidth; ?>,
                        'columnHeight':<?php echo $this->columnHeight; ?>,
                        'showlikebutton':<?php echo $this->showlikebutton; ?>,
                        'turncation':<?php echo $this->turncation; ?>,
                        'listview_turncation':<?php echo $this->listview_turncation; ?>,
                        'showfeaturedLable':<?php echo $this->showfeaturedLable; ?>,
                        'showsponsoredLable':<?php echo $this->showsponsoredLable; ?>,
                        'showlocation':<?php echo $this->showlocation; ?>,
                        'showprice':<?php echo $this->showprice; ?>,
                        'showpostedBy':<?php echo $this->showpostedBy; ?>,
                        'showdate':<?php echo $this->showdate; ?>,
                        'statistics': '<?php echo json_encode($this->statistics) ?>',
                        'detactLocation': '<?php echo $this->detactLocation ?>',
                        'defaultLocationDistance': '<?php echo $this->defaultLocationDistance ?>',
                        'latitude': window.locationsParamsSEAO && window.locationsParamsSEAO.latitude ? window.locationsParamsSEAO.latitude : 0,
                        'longitude': window.locationsParamsSEAO && window.locationsParamsSEAO.longitude ? window.locationsParamsSEAO.longitude : 0,
                        'showgetdirection':'<?php echo $this->showgetdirection; ?>'
                    },
                    onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                        $('dynamic_app_info_group').innerHTML = responseHTML;

                        // showtooltipGroup();
    <?php if ($this->enableLocation && $this->map_view): ?>
                            rinitializeGroup();
    <?php endif; ?>
                        rswitchviewGroup(<?php echo $this->defaultView ?>);
                    }
                });

                request.send();
            }
        </script>
        <style type="text/css">
            #rmap_canvas_group {
                width: 100% !important;
                height: 400px;
                float: left;
            }
            #rmap_canvas_group > div{
                height: 300px;
            }
            #infoPanel {
                float: left;
                margin-left: 10px;
            }
            #infoPanel div {
                margin-bottom: 5px;
            }
        </style>
    <?php else: ?>

        <div id="layout_sitegroup_recently_popular_random_sitegroup_<?php echo $this->identity; ?>">
            <!--    <div class="seaocore_content_loader"></div>-->
        </div>
        <?php if ($this->detactLocation): ?>
            <script type="text/javascript">
                var requestParams = $merge(<?php echo json_encode($this->params); ?>, {'content_id': '<?php echo $this->identity; ?>'})
                var params = {
                    'detactLocation': <?php echo $this->detactLocation; ?>,
                    'responseContainer': 'layout_sitegroup_recently_popular_random_sitegroup_<?php echo $this->identity; ?>',
                    requestParams: requestParams
                };

                en4.seaocore.locationBased.startReq(params);
            </script>  
        <?php else: ?>
            <script type="text/javascript">
                window.addEvent('domready', function () {
                    en4.sitegroup.ajaxTab.sendReq({
                        loading: true,
                        requestParams: $merge(<?php echo json_encode($this->paramsLocation); ?>, {'content_id': '<?php echo $this->identity; ?>'}),
                        responseContainer: [$('layout_sitegroup_recently_popular_random_sitegroup_<?php echo $this->identity; ?>')]
                    });
                });
            </script>  
        <?php endif; ?>


    <?php endif; ?>