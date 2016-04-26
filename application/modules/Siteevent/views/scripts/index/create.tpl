<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: create.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $hasPackageEnable = Engine_Api::_()->siteevent()->hasPackageEnable();
if ($hasPackageEnable):?>
  <?php $this->PackageCount = Engine_Api::_()->getDbTable('packages', 'siteeventpaid')->getPackageCount();?>
<?php endif;?>
<?php  
	$this->headTranslate(array('edit','Date & Time', 'on the following days', 'Specific dates and times are set for this event.', 'Start time should be greater than the current time.', 'End time should be greater than the Start time.', 'Daily Event:', 'until', 'Days:', 'weeks', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'In Every', 'months', 'of every month', 'of the month', 'from', 'first', 'second', 'third', 'fourth', 'fifth', 'sixth', 'seventh', 'to', 'every', 'Every', 'Day'));
?>

<?php if ($this->parentTypeItem->getType() != 'user'): ?>
    <div class="siteevent_viewevents_head">
        <?php echo $this->htmlLink($this->parentTypeItem->getHref(), $this->itemPhoto($this->parentTypeItem, 'thumb.icon', '', array('align' => 'left'))) ?>
        <h2>	
            <?php echo $this->parentTypeItem->__toString() ?>	
            <?php echo $this->translate('&raquo; '); ?>
            <?php echo $this->translate('Events'); ?>
        </h2>
    </div><br />
<?php endif; ?>

<?php if ($this->quick && $this->seaoSmoothbox): ?>
    <script type="text/javascript">
        if (!Autocompleter) {
            SmoothboxSEAO.addScriptFiles.push("<?php echo $this->layout()->staticBaseUrl ?>externals/autocompleter/Observer.js");
                    SmoothboxSEAO.addScriptFiles.push("<?php echo $this->layout()->staticBaseUrl ?>externals/autocompleter/Autocompleter.js");
                            SmoothboxSEAO.addScriptFiles.push("<?php echo $this->layout()->staticBaseUrl ?>externals/autocompleter/Autocompleter.Local.js");
                                    SmoothboxSEAO.addScriptFiles.push("<?php echo $this->layout()->staticBaseUrl ?>externals/autocompleter/Autocompleter.Request.js");
        }
        if (!en4.siteevent) {
                                        SmoothboxSEAO.addScriptFiles.push("<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteevent/externals/scripts/core.js");
        }
        if (!en4.siteeventcommon) {
                                        SmoothboxSEAO.addScriptFiles.push("<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteevent/externals/scripts/_commonFunctions.js");
        }
        SmoothboxSEAO.addScriptFiles.push("<?php echo $this->layout()->staticBaseUrl . 'externals/calendar/calendar.compat.js' ?>");
        SmoothboxSEAO.addStylesheets.push("<?php echo $this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/calendar/styles.css' ?>");

        /*  if(!google  || !google.maps.places)
             SmoothboxSEAO.addScriptFiles.push("https://maps.googleapis.com/maps/api/js?libraries=places&key=<?php echo Engine_Api::_()->seaocore()->getGoogleMapApiKey() ?>");*/
    </script>
<?php else: ?>
    <?php
    $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/styles/style_siteevent_dashboard.css');
    ?>
    <?php
    $this->headScript()
            ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
            ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
            ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
            ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js')
            ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/scripts/core.js')
            ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Siteevent/externals/scripts/_commonFunctions.js');
    $this->tinyMCESEAO()->addJS();
    ?> 
    <?php
    $apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
    $this->headScript()
            ->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey")
    ?>
<?php endif; ?>
<!--WE ARE NOT USING STATIC BASE URL BECAUSE SOCIAL ENGINE ALSO NOT USE FOR THIS JS-->
<!--CHECK HERE Engine_View_Helper_TinyMce => protected function _renderScript()-->
<?php $coreSettings = Engine_Api::_()->getApi('settings', 'core');?>
<script type="text/javascript">

    en4.core.runonce.add(function() {
            checkDraft();
        var eventCreate = en4.siteevent.create;
            <?php if($coreSettings->getSetting('siteevent.onlineevent.allow', 1) == 1) : ?>
            //ADD A LINK WITH VENUE NAME FIELD:
            var newdiv = document.createElement('div');
        var language = '<?php echo $this->string()->escapeJavascript($this->translate('online event')) ?>';
        newdiv.id = 'online_event';
        newdiv.innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate('Running an ')) ?>' + "<a href='javascript:void(0);'  name='online_event' onclick='en4.siteevent.create.is_online(true);return false;' >" + language + "</a>?<br />";

        if ($('venue_name-element')) {
            newdiv.inject($('venue_name-element'), 'bottom');
                en4.siteevent.create.is_online(false);
<?php if (!empty($_POST) && !empty($_POST['is_online'])) : ?>
                    en4.siteevent.create.is_online(true);
<?php endif; ?>
        }
        <?php endif;?>
            var locationEl = document.getElementById('location');
<?php if ($this->quick): ?>
                locationEl = $('siteevents_create_quick').getElementById('location');
<?php endif; ?>
        if (locationEl && (('<?php echo !Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0);?>') || ('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0);?>' && '<?php echo !Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecificcontent', 0); ?>'))) {
            var autocompleteSECreateLocation = new google.maps.places.Autocomplete(locationEl);
            google.maps.event.addListener(autocompleteSECreateLocation, 'place_changed', function() {
                var place = autocompleteSECreateLocation.getPlace();
                if (!place.geometry) {                     return;
                }
                var address = '', country = '', state = '', zip_code = '', city = '';
                if (place.address_components) {
                var len_add = place.address_components.length;

                    for (var i = 0; i < len_add; i++) {
                        var types_location = place.address_components[i]['types'][0];                         if (types_location === 'country') {
                        country = place.address_components[i]['long_name'];
                            } else if (types_location === 'administrative_area_level_1') {
                        state = place.address_components[i]['long_name'];
                            } else if (types_location === 'administrative_area_level_2') {
                        city = place.address_components[i]['long_name'];
                            } else if (types_location === 'zip_code') {
                        zip_code = place.address_components[i]['long_name'];
                            } else if (types_location === 'street_address') {
                                if (address === '')                                 address = place.address_components[i]['long_name'];                             else
                                address = address + ',' + place.address_components[i]['long_name'];
                        } else if (types_location === 'locality') {
                        if (address === '')
                                address = place.address_components[i]['long_name'];                             else
                                address = address + ',' + place.address_components[i]['long_name'];
                        } else if (types_location === 'route') {
                        if (address === '')
                                address = place.address_components[i]['long_name'];                             else
                                address = address + ',' + place.address_components[i]['long_name'];
                        } else if (types_location === 'sublocality') {
                                if (address === '')                                 address = place.address_components[i]['long_name'];                             else
                                address = address + ',' + place.address_components[i]['long_name'];
                        }
                    }
                }
                var locationParams = '{"location" :"' + locationEl.value + '","latitude" :"' + place.geometry.location.lat() + '","longitude":"' + place.geometry.location.lng() + '","formatted_address":"' + place.formatted_address + '","address":"' + address + '","country":"' + country + '","state":"' + state + '","zip_code":"' + zip_code + '","city":"' + city + '"}';
                document.getElementById('locationParams').value = locationParams;             });
        }
        //make the pre-filled date field. 
            initializeCalendar();
        });

        function checkDraft() {
        if ($('draft') && $('search-wrapper')) {
        if ($('draft').value == 1) {
            $("search-wrapper").style.display = "none";
                $("search").checked = false;
            } else {
                $("search-wrapper").style.display = "block";
                $("search").checked = true;
            }
        }
    }

</script>
<?php
/* Include the common user-end field switching javascript */
echo $this->partial('_jsSwitch.tpl', 'fields', array(
        //'topLevelId' => (int) @$this->topLevelId,
        //'topLevelValue' => (int) @$this->topLevelValue
))
?>
<?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>
<div class='siteevent_event_form'>
    <?php if ($this->current_count >= $this->quota && !empty($this->quota)): ?>
        <div class="tip"> 
            <span>
                <?php echo $this->translate("You have already created the maximum number of events allowed."); ?>
            </span>
        </div>
        <br/>
    <?php elseif ($this->category_count > 0): ?>
        <?php if ($this->siteevent_render == 'siteevent_form'):?>
  <?php if ($hasPackageEnable && $this->PackageCount > 0):?>
	<h3><?php echo $this->translate("Create New Event") ?></h3>
<!--	<p><?php echo $this->translate("Create an event using these quick, easy steps and get going.");?></p>	-->
    <h4 class="siteevent_create_step"><?php echo $this->translate("2. Configure your event based on the package you have chosen."); ?></h4>
	  <div class='siteeventpage_layout_right'>      
    	<div class="siteevent_package_page p5">          
        <ul class="siteevent_package_list">
        	<li class="p5">
          	<div class="siteevent_package_list_title">
              <h3><?php echo $this->translate('Package Details'); ?>: <?php echo $this->translate(ucfirst($this->package->title)); ?></h3>
            </div>           
            <div class="siteevent_package_stat"> 
              <?php if (in_array('price', $this->packageInfoArray)): ?>
              <span>
								<b><?php echo $this->translate("Price"). ": "; ?> </b>
        <?php if(isset ($this->package->price)):?>
          <?php if($this->package->price > 0):echo $this->locale()->toCurrency($this->package->price, $currency); else: echo $this->translate('FREE'); endif; ?>
        <?php endif;?>
             	</span>
              <?php endif;?>
              <?php if (in_array('ticket_type', $this->packageInfoArray)): ?>
                <span>
                    <b><?php echo $this->translate("Ticket Types"). ": "; ?> </b>
                    <?php
                    if ($this->package->ticket_type):echo $this->translate("PAID & FREE");
                    else: echo $this->translate('FREE');
                    endif;
                    ?>
                </span>
              <?php endif;?>                
              <?php if (in_array('billing_cycle', $this->packageInfoArray)): ?>
             	<span>
                <b><?php echo $this->translate("Billing Cycle"). ": "; ?> </b>
                <?php echo $this->package->getBillingCycle() ?>
              </span>
              <?php endif;?>
              <?php if (in_array('duration', $this->packageInfoArray)): ?>
              <span style="width: auto;">
              	<b><?php echo ($this->package->price > 0 && $this->package->recurrence > 0 && $this->package->recurrence_type != 'forever' ) ? $this->translate("Billing Duration"). ": ": $this->translate("Duration"). ": "; ?> </b>
               	<?php echo $this->package->getPackageQuantity() ; ?>
             	</span>
              <?php endif;?>
              <br />
              <?php if (in_array('featured', $this->packageInfoArray)): ?>
              <span>
              	<b><?php echo $this->translate("Featured"). ": "; ?> </b>
               	<?php
                	if ($this->package->featured == 1)
                		echo $this->translate("Yes");
                	else
                  	echo $this->translate("No");
                ?>
             	</span>
              <?php endif;?>
              <?php if (in_array('Sponsored', $this->packageInfoArray)): ?>
              <span>
              	<b><?php echo $this->translate("Sponsored"). ": "; ?> </b>
               	<?php
                	if ($this->package->sponsored == 1)
                  	echo $this->translate("Yes");
                	else
                  	echo $this->translate("No");
             	 	?>
             	</span>
              <?php endif;?>
              <?php if(in_array('rich_overview', $this->packageInfoArray) && ($this->overview && Engine_Api::_()->authorization()->getPermission($this->viewer->level_id, 'siteevent_event', "overview"))):?>
                <span>
                 <b><?php echo $this->translate("Rich Overview"). ": "; ?> </b>
                 <?php
                  if ($this->package->overview == 1)
                    echo $this->translate("Yes");
                  else
                    echo $this->translate("No");
                 ?>
                </span>
              <?php endif;?>
              <?php if(in_array('videos', $this->packageInfoArray) && Engine_Api::_()->authorization()->getPermission($this->viewer->level_id, 'siteevent_event', "video")):?>
                <span>
                 <b><?php echo $this->translate("Videos"). ": "; ?> </b>
                  <?php
                  if ($this->package->video == 1)
                    if ($this->package->video_count)
                      echo $this->package->video_count;
                    else
                      echo $this->translate("Unlimited");
                  else
                    echo $this->translate("No");
                 ?>
                </span>
              <?php endif;?>
              <?php if(in_array('photos', $this->packageInfoArray) && Engine_Api::_()->authorization()->getPermission($this->viewer->level_id, 'siteevent_event', "photo")):?>
                <span>
                 <b><?php echo $this->translate("Photos"). ": "; ?> </b>
                  <?php
                  if ($this->package->photo == 1)
                    if ($this->package->photo_count)
                      echo $this->package->photo_count;
                    else
                      echo $this->translate("Unlimited");
                  else
                    echo $this->translate("No");
                 ?>
                </span>
              <?php endif;?>
						</div>
             <?php if(in_array('description', $this->packageInfoArray)):?>
						<div class="siteevent_list_details">
							<?php echo $this->translate($this->package->description); ?>
		        </div>
            <?php endif;?>
           <?php if($this->PackageCount > 1):?>
          	<div class="siteevent_create_link mtop10 clr">
           		<a href="<?php echo $this->url(array('action'=>'index'), "siteevent_package", true) ?>">&laquo; <?php echo $this->translate("Choose a different package"); ?></a>
          	</div>
           <?php endif;?>
          </li>
        </ul>
      </div>
    </div>
    <div class="siteevent_layout_left">
  <?php endif; ?>
      <?php echo $this->form->setAttrib('class', 'global_form siteevent_create_list_form')->render($this);?>
        <?php if ($hasPackageEnable && $this->PackageCount > 0):?>
					</div>
  	   <?php endif;?>
    <?php  else:?>
      <?php echo $this->translate($this->siteevent_formrender);?>
    <?php endif;?>
    <?php endif; ?>
</div>

<script type="text/javascript">
    if ($('subcategory_id'))
        $('subcategory_id').style.display = 'none';

    en4.core.runonce.add(function()
    {
    new Autocompleter.Request.JSON('tags', '<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'index', 'action' => 'tag-suggest', 'resourceType' => 'siteevent_event'), 'default', true) ?>', {
            'postVar': 'text',
            'minLength': 1,
                'selectMode': 'pick',
                'autocompleteType': 'tag',
                'className': 'tag-autosuggest',
                'customChoices': true,
                'filterSubset': true,             'multiple': true,
                'injectChoice': function(token) {
                    var choice = new Element('li', {'class': 'autocompleter-choices', 'value': token.label, 'id': token.id});
                    new Element('div', {'html': this.markQueryValue(token.label), 'class': 'autocompleter-choice'}).inject(choice);
                choice.inputValue = token;
                    this.addChoiceEvents(choice).inject(this.choices);
                    choice.store('autocompleteChoice', token);
            }
        });
    });

    //   var eventRepeat = '<?php //echo (bool) Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.repeat', 1); ?>'
//   
//		if(eventRepeat == 0) {
//			$('eventrepeat_id-wrapper').style.display = 'none';
//		}
    var getProfileType = function(category_id) {
        var mapping = <?php echo Zend_Json_Encoder::encode(Engine_Api::_()->getDbTable('categories', 'siteevent')->getMapping('profile_type')); ?>;
        for (i = 0; i < mapping.length; i++) {
            if (mapping[i].category_id == category_id)
        return mapping[i].profile_type;
        }
            return 0;
    }
    en4.core.runonce.add(function() {
    var defaultProfileId = '<?php echo '0_0_' . $this->defaultProfileId ?>' + '-wrapper';
        if ($type($(defaultProfileId)) && typeof $(defaultProfileId) != 'undefined') {
        $(defaultProfileId).setStyle('display', 'none');
        }
    }); 
</script>

<?php if (0): ?>
    <div style="display:none;" id="expertTips">
        <div class="global_form_popup" style="width:450px;">
            <div class="show_content_body">
    <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.experttips'); ?>
            </div>
            <div class="clr mtop10">
                <button onclick="SmoothboxSEAO.close();"><?php echo $this->translate('Close'); ?></button>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function expertTips() {
            SmoothboxSEAO.open('<div>' + $('expertTips').innerHTML + '</div>');
        }
    </script>  
<?php endif; ?>

<?php
//INCLUDE THE REPETE EVENT HTML FILE WHICH WILL BE HIDE ON THIS PAGE
//include APPLICATION_PATH . '/application/modules/Siteevent/views/scripts/_repeatEvent.tpl';
?>

<style type="text/css">

.se_create_more{
  display: block !important;
  margin-bottom: 10px;
}
</style>
<script type="text/javascript">

    if($('guest_lists-wrapper')) {
      $('guest_lists-wrapper').style.display = 'none'; 
    }
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

    en4.core.runonce.add(function()
    {
    var viewFullPage = '<?php echo $this->viewFullPage;?>';

    if ($('overview-wrapper') || ($('host-host_description') && SmoothboxSEAO.active && $('siteevents_create_quick')) && (viewFullPage == 0)) {
                    var tinymacEditor = function(element_id) {<?php
                    echo $this->tinyMCESEAO()->render(array('element_id' => 'element_id',
                        'language' => $this->language,
                        'upload_url' => $this->upload_url,
                        'directionality' => $this->directionality));
                    ?>
                    };
                    if ($('overview-wrapper'))
                        tinymacEditor('overview');            
            } 


        if (SmoothboxSEAO.active && $('siteevents_create_quick') && (viewFullPage == 0)) {            
             if ($('host-host_description'))
                    tinymacEditor('host-host_description');
                  
            if ($('siteevents_create_quick').getElementById('body'))
                $('siteevents_create_quick').getElementById('body').autogrow();
            if($('siteevents_create_quick').getElements('.se_quick_advanced').length > 0){    
            var toogleAdvancedView = function(el) {
                if (el.retrieve('activeHideAdvanced', false)) {
                $('siteevents_create_quick').getElements('.se_quick_advanced').getParent('.form-wrapper').removeClass('dnone');
                    el.store('activeHideAdvanced', false);
                    el.innerHTML='<?php echo $this->string()->escapeJavascript($this->translate('Hide Advanced Options'));?>';
                    el.addClass('seaocore_icon_minus');
                } else {
                    $('siteevents_create_quick').getElements('.se_quick_advanced').getParent('.form-wrapper').addClass('dnone');
                    el.store('activeHideAdvanced', true);
                    el.innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate('Show Advanced Options')); ?>';
                    el.removeClass('seaocore_icon_minus');
                }
            };
            var el= new Element('a', {
      'class': 'buttonlink seaocore_icon_add se_create_more'
    }).inject($('siteevents_create_quick').getElementById('buttons-element'),'top');
                el.addEvent('click', function() {
            toogleAdvancedView(el);
            });
            toogleAdvancedView(el);
            }
        } else if($('siteevents_create_quick') && (viewFullPage == 1)) {

            if($('siteevents_create_quick').getElements('.se_quick_advanced').length > 0){    
            var toogleAdvancedView = function(el) {
                if (el.retrieve('activeHideAdvanced', false)) {
                $('siteevents_create_quick').getElements('.se_quick_advanced').getParent('.form-wrapper').removeClass('dnone');
                    el.store('activeHideAdvanced', false);
                    el.innerHTML='<?php echo $this->string()->escapeJavascript($this->translate('Hide Advanced Options'));?>';
                    el.addClass('seaocore_icon_minus');
                } else {
                    $('siteevents_create_quick').getElements('.se_quick_advanced').getParent('.form-wrapper').addClass('dnone');
                    el.store('activeHideAdvanced', true);
                    el.innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate('Show Advanced Options')); ?>';
                    el.removeClass('seaocore_icon_minus');
                }
            };
            var el= new Element('a', {
      'class': 'buttonlink seaocore_icon_add se_create_more'
    }).inject($('siteevents_create_quick').getElementById('buttons-element'),'top');
                el.addEvent('click', function() {
            toogleAdvancedView(el);
            });
            toogleAdvancedView(el);
            }
        }
    });


</script>