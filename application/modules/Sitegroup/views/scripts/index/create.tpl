<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: create.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php $apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey")
?>
<?php if(!empty($this->sitegroupUrlEnabled) && !empty($this->show_url)):?>
	<script type="text/javascript">

		window.addEvent('domready', function() { 
		var e4 = $('group_url_msg-wrapper');
		$('group_url_msg-wrapper').setStyle('display', 'none');
		
				var groupurlcontainer = $('group_url-element');
				var language = '<?php echo $this->string()->escapeJavascript($this->translate('Check Availability')) ?>';
				var newdiv = document.createElement('div');
				newdiv.id = 'url_varify';
				newdiv.innerHTML = "<a href='javascript:void(0);'  name='check_availability' id='check_availability' onclick='GroupUrlBlur();return false;' class='check_availability_button'>"+language+"</a> <br />";

				groupurlcontainer.insertBefore(newdiv, groupurlcontainer.childNodes[2]);
				checkDraft();

				if(document.getElementById('location') && (('<?php echo !Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0);?>') || ('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0);?>' && '<?php echo !Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecificcontent', 0); ?>'))) {
					var autocompleteSECreateLocation = new google.maps.places.Autocomplete(document.getElementById('location'));
					<?php include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/location.tpl'; ?>
				}
		});

		function checkDraft(){
			if($('draft') && $("search-wrapper")){
				if($('draft').value==0) {
					$("search-wrapper").style.display="none";
					$("search").checked= false;
				} else{
					$("search-wrapper").style.display="block";
					$("search").checked= true;
				}
			}
		}


		function GroupUrlBlur() {
			if ($('group_url_alert') == null) {
				var groupurlcontainer = $('group_url-element');
				var newdiv = document.createElement('span');
				newdiv.id = 'group_url_alert';
				newdiv.innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitegroup/externals/images/loading.gif" />';
				groupurlcontainer.insertBefore(newdiv, groupurlcontainer.childNodes[3]);
			}
			else {
				$('group_url_alert').innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitegroup/externals/images/loading.gif" />';
			}
			var url = '<?php echo $this->url(array('action' => 'groupurlvalidation' ), 'sitegroup_general', true);?>';
			en4.core.request.send(new Request.JSON({
				url : url,
				method : 'get',
				data : {
					group_url : $('group_url').value,
          check_url : 0,
          group_id : 0,
					format : 'html'
				},

				onSuccess : function(responseJSON) {
					//$('group_url_msg-wrapper').setStyle('display', 'block');
					if (responseJSON.success == 0) {
						$('group_url_alert').innerHTML = responseJSON.error_msg;
						if ($('group_url_alert')) {
							$('group_url_alert').innerHTML = responseJSON.error_msg;
						}
					}
					else {
						$('group_url_alert').innerHTML = responseJSON.success_msg;
						if ($('group_url_alert')) {
							$('group_url_alert').innerHTML = responseJSON.success_msg;
						}
					}
				}
		}));
	}

	//<![CDATA[
		window.addEvent('load', function()
		{
		  var url = '<?php echo $this->translate('GROUP-NAME');?>';
		  if($('group_url_address')) {
				$('group_url_address').innerHTML = $('group_url_address').innerHTML.replace(url, '<span id="group_url_address_text"><?php echo $this->translate('GROUP-NAME');?></span>');
			}
      
      $('short_group_url_address').innerHTML = $('short_group_url_address').innerHTML.replace(url, '<span id="short_group_url_address_text"><?php echo $this->translate('GROUP-NAME');?></span>');

			$('group_url').addEvent('keyup', function()
			{
				var text = url;
				if( this.value != '' )
				{
					text = this.value;
				}
				if($('group_url_address_text')) {
					$('group_url_address_text').innerHTML = text;
				}
        $('short_group_url_address_text').innerHTML = text;
			});
			// trigger on group-load
			if ($('group_url').value.length)
					$('group_url').fireEvent('keyup');
		});
	//]]>
	</script>
<?php elseif(empty($this->sitegroupUrlEnabled)):?>
  <script type="text/javascript">

		window.addEvent('domready', function() { 
		var e4 = $('group_url_msg-wrapper');
		$('group_url_msg-wrapper').setStyle('display', 'none');
		
				var groupurlcontainer = $('group_url-element');
				var language = '<?php echo $this->string()->escapeJavascript($this->translate('Check Availability')) ?>';
				var newdiv = document.createElement('div');
				newdiv.id = 'url_varify';
				newdiv.innerHTML = "<a href='javascript:void(0);'  name='check_availability' id='check_availability' onclick='GroupUrlBlur();return false;' class='check_availability_button'>"+language+"</a> <br />";

				groupurlcontainer.insertBefore(newdiv, groupurlcontainer.childNodes[2]);
				checkDraft();

				if(document.getElementById('location') && (('<?php echo !Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0);?>') || ('<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecific', 0);?>' && '<?php echo !Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.locationspecificcontent', 0); ?>'))) {
					var autocompleteSECreateLocation = new google.maps.places.Autocomplete(document.getElementById('location'));
					<?php include APPLICATION_PATH . '/application/modules/Seaocore/views/scripts/location.tpl'; ?>
				}
		});

		function checkDraft(){
			if($('draft') && $("search-wrapper")){
				if($('draft').value==0) {
					$("search-wrapper").style.display="none";
					$("search").checked= false;
				} else{
					$("search-wrapper").style.display="block";
					$("search").checked= true;
				}
			}
		}


		function GroupUrlBlur() {
			if ($('group_url_alert') == null) {
				var groupurlcontainer = $('group_url-element');
				var newdiv = document.createElement('span');
				newdiv.id = 'group_url_alert';
				newdiv.innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitegroup/externals/images/loading.gif" />';
				groupurlcontainer.insertBefore(newdiv, groupurlcontainer.childNodes[3]);
			}
			else {
				$('group_url_alert').innerHTML = '<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Sitegroup/externals/images/loading.gif" />';
			}
			var url = '<?php echo $this->url(array('action' => 'groupurlvalidation' ), 'sitegroup_general', true);?>';
			en4.core.request.send(new Request.JSON({
				url : url,
				method : 'get',
				data : {
					group_url : $('group_url').value,
          check_url : 0,
          group_id : 0,
					format : 'html'
				},

				onSuccess : function(responseJSON) {
					//$('group_url_msg-wrapper').setStyle('display', 'block');
					if (responseJSON.success == 0) {
						$('group_url_alert').innerHTML = responseJSON.error_msg;
						if ($('group_url_alert')) {
							$('group_url_alert').innerHTML = responseJSON.error_msg;
						}
					}
					else {
						$('group_url_alert').innerHTML = responseJSON.success_msg;
						if ($('group_url_alert')) {
							$('group_url_alert').innerHTML = responseJSON.success_msg;
						}
					}
				}
		}));
	}

	//<![CDATA[
		window.addEvent('load', function()
		{ 
		var url = '<?php echo $this->translate('GROUP-NAME');?>';
		  if($('group_url_address')) {
				$('group_url_address').innerHTML = $('group_url_address').innerHTML.replace(url, '<span id="group_url_address_text"><?php echo $this->translate('GROUP-NAME');?></span>');
			}

			$('group_url').addEvent('keyup', function()
			{
				var text = url;
				if( this.value != '' )
				{
					text = this.value;
				}
				
				if($('group_url_address_text')) {
					$('group_url_address_text').innerHTML = text;
				}
			});
			// trigger on group-load
			if ($('group_url').value.length)
					$('group_url').fireEvent('keyup');
		});
	//]]>
	</script>
<?php endif;?>
<?php
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>  
<script type="text/javascript">
  en4.core.runonce.add(function()
  {
    new Autocompleter.Request.JSON('tags', '<?php echo $this->url(array('module' => 'seaocore', 'controller' => 'index', 'action' => 'tag-suggest', 'resourceType' => 'sitegroup_group'), 'default', true) ?>', {
      'postVar' : 'text',
      'minLength': 1,
      'selectMode': 'pick',
      'autocompleteType': 'tag',
      'className': 'tag-autosuggest',
      'customChoices' : true,
      'filterSubset' : true,
      'multiple' : true,
      'injectChoice': function(token){
        var choice = new Element('li', {'class': 'autocompleter-choices', 'value':token.label, 'id':token.id});
        new Element('div', {'html': this.markQueryValue(token.label),'class': 'autocompleter-choice'}).inject(choice);
        choice.inputValue = token;
        this.addChoiceEvents(choice).inject(this.choices);
        choice.store('autocompleteChoice', token);
      }
    });
  });
</script>

<?php include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/payment_navigation_views.tpl'; ?>
<?php $currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD'); ?>
<div class='layout_middle sitegroup_create_wrapper clr'>
	<?php if ($this->current_count >= $this->quota  && !empty($this->quota)): ?>
	  <div class="tip">
	  	<span><?php echo $this->translate('You have already created the maximum number of groups allowed.'); ?></span> 
	  </div>
	  <br/>
	<?php else: ?>
	  <?php if($this->sitegroup_render == 'sitegroup_form') { ?>
    <?php if(!empty($this->package)):?>
	<h3><?php echo $this->translate("Create New Group") ?></h3>
	<p><?php echo $this->translate("Create a group using these quick, easy steps and get going.");?></p>	
    <h4 class="sitegroup_create_step"><?php echo $this->translate('2. Configure your group based on the package you have chosen.'); ?></h4>
	  <div class='sitegroupgroup_layout_right'>      
    	<div class="sitegroup_package_group p5">          
        <ul class="sitegroup_package_list">
        	<li class="p5">
          	<div class="sitegroup_package_list_title">
              <h3><?php echo $this->translate('Package Details'); ?>: <?php echo $this->translate(ucfirst($this->package->title)); ?></h3>
            </div>           
            <div class="sitegroup_package_stat"> 
              <span>
								<b><?php echo $this->translate("Price"). ": "; ?> </b>
								<?php if($this->package->price > 0):echo $this->locale()->toCurrency($this->package->price, $currency); else: echo $this->translate('FREE'); endif; ?>
             	</span>
             	<span>
                <b><?php echo $this->translate("Billing Cycle"). ": "; ?> </b>
                <?php echo $this->package->getBillingCycle() ?>
              </span>
              <span style="width: auto;">
              	<b><?php echo ($this->package->price > 0 && $this->package->recurrence > 0 && $this->package->recurrence_type != 'forever' ) ? $this->translate("Billing Duration"). ": ": $this->translate("Duration"). ": "; ?> </b>
               	<?php echo $this->package->getPackageQuantity() ; ?>
             	</span>
              <br />
              <span>
              	<b><?php echo $this->translate("Featured"). ": "; ?> </b>
               	<?php
                	if ($this->package->featured == 1)
                		echo $this->translate("Yes");
                	else
                  	echo $this->translate("No");
                ?>
             	</span>
              <span>
              	<b><?php echo $this->translate("Sponsored"). ": "; ?> </b>
               	<?php
                	if ($this->package->sponsored == 1)
                  	echo $this->translate("Yes");
                	else
                  	echo $this->translate("No");
             	 	?>
             	</span>
              <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad')): ?>
                <span>
                  <b><?php echo $this->translate("Ads Display"). ": "; ?> </b>
                   <?php
                    if ($this->package->ads == 1 && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.communityads', 1))
                      echo $this->translate("Yes");
                    else
                      echo $this->translate("No");
                    ?>
                </span>
              <?php endif;?>            
              
             	<span>
              	<b><?php echo $this->translate("Tell a friend"). ": "; ?> </b>
               	<?php
                  if ($this->package->tellafriend == 1)
                    echo $this->translate("Yes");
                  else
                    echo $this->translate("No");
                ?>
             	</span>
              <span>
                <b><?php echo $this->translate("Print"). ": "; ?> </b>
                 <?php
                  if ($this->package->print == 1)
                    echo $this->translate("Yes");
                  else
                    echo $this->translate("No");
                  ?>
              </span>
             	<span>
               <b><?php echo $this->translate("Rich Overview"). ": "; ?> </b>
               <?php
                if ($this->package->overview == 1)
                  echo $this->translate("Yes");
                else
                  echo $this->translate("No");
              	?>
             	</span>
             	<span>
              	<b><?php echo $this->translate("Map"). ": "; ?> </b>
               	<?php
                if ($this->package->map == 1)
                  echo $this->translate("Yes");
                else
                  echo $this->translate("No");
              	?>
             	</span>
             	<span>
              	<b><?php echo $this->translate("Insights"). ": "; ?> </b>
               	<?php
                if ($this->package->insights == 1)
                  echo $this->translate("Yes");
                else
                  echo $this->translate("No");
                ?>
             	</span>
              <span>
                  <b><?php echo $this->translate("Contact Details"). ": "; ?> </b>
                   <?php
                    if ($this->package->contact_details == 1)
                      echo $this->translate("Yes");
                    else
                      echo $this->translate("No");
                    ?>
              </span>
              <span>
                <b><?php echo $this->translate("Send an Update"). ": "; ?> </b>
                 <?php
                  if ($this->package->sendupdate == 1)
                    echo $this->translate("Yes");
                  else
                    echo $this->translate("No");
                  ?>
              </span>
<!--              <span>
                <b><?php echo $this->translate("Save To Foursquare Button"). ": "; ?> </b>
                 <?php
                  if ($this->package->foursquare == 1)
                    echo $this->translate("Yes");
                  else
                    echo $this->translate("No");
                  ?>
              </span> -->
              <?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegrouptwitter')) :?>
                <span>
                  <b><?php echo $this->translate("Display Twitter Updates"). ": "; ?> </b>
                  <?php
                    if ($this->package->twitter == 1)
                      echo $this->translate("Yes");
                    else
                      echo $this->translate("No");
                    ?>
                </span>
              <?php endif;?>
							<?php  $module= unserialize($this->package->modules);
               if(!empty($module)):
                    $subModuleStr=$this->package->getSubModulesString();
             		if(!empty($this->package->modules) && !empty ($subModuleStr)):?>
				        <span class="sitegroup_package_stat_apps">
				           <b><?php echo $this->translate("Apps available"). ": "; ?> </b>
				           <?php echo $subModuleStr; ?>
				        </span>
				      <?php endif; ?>
              <?php endif; ?>
						</div>
						<div class="sitegroup_list_details">
							<?php echo $this->translate($this->package->description); ?>
		        </div>
          	<div class="sitegroup_create_link mtop10 clr">
           		<a href="<?php echo $this->url(array('action'=>'index'), 'sitegroup_packages', true) ?>">&laquo; <?php echo $this->translate("Choose a different package"); ?></a>
          	</div>
          </li>
        </ul>
      </div>
    </div>
    <div class="sitegroupgroup_layout_left">
  <?php endif; ?>
  <?php echo $this->form->render($this); ?>
  <?php if(!empty($this->package)):?>
  	</div>
  <?php endif; ?>
  <?php } else { echo $this->translate($this->sitegroup_formrender); } ?>
  <?php endif; ?> 
</div>

<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.profile.fields', 1)): ?>
	<?php
		/* Include the common user-end field switching javascript */
		echo $this->partial('_jsSwitch.tpl', 'fields', array( 
		))
	?>
	<script type="text/javascript">

		var getProfileType = function(category_id) {
			var mapping = <?php echo Zend_Json_Encoder::encode(Engine_Api::_()->getDbTable('profilemaps', 'sitegroup')->getMapping()); ?>;
			for(i = 0; i < mapping.length; i++) {
				if(mapping[i].category_id == category_id)
					return mapping[i].profile_type;
			}
			return 0;
		}

		var defaultProfileId = '<?php echo '0_0_1' ?>'+'-wrapper';
		if($type($(defaultProfileId)) && typeof $(defaultProfileId) != 'undefined') { 
			$(defaultProfileId).setStyle('display', 'none');
		}
	</script>
<?php endif; ?>

<script type="text/javascript">
    en4.core.runonce.add(function()
    {
     
    if('<?php echo $this->quick;?>') {
       SmoothboxSEAO.active = true;
    }
 
        if (SmoothboxSEAO.active && $('sitegroups_create_quick')) {   
            if($('sitegroups_create_quick').getElements('.sg_quick_advanced').length > 0){
            var toogleAdvancedView = function(el) {
                if (el.retrieve('activeHideAdvanced', false)) {
                $('sitegroups_create_quick').getElements('.sg_quick_advanced').getParent('.form-wrapper').removeClass('dnone');
                    el.store('activeHideAdvanced', false);
                    el.innerHTML='<?php echo $this->string()->escapeJavascript($this->translate('Hide Advanced Options'));?>';
                    el.addClass('seaocore_icon_minus');
                } else {
                    $('sitegroups_create_quick').getElements('.sg_quick_advanced').getParent('.form-wrapper').addClass('dnone');
                    el.store('activeHideAdvanced', true);
                    el.innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate('Show Advanced Options')); ?>';
                    el.removeClass('seaocore_icon_minus');
                }
            };
            var el= new Element('a', {
      'class': 'buttonlink seaocore_icon_add sg_create_more'
    }).inject($('sitegroups_create_quick').getElementById('buttons-element'),'top');
                el.addEvent('click', function() {
            toogleAdvancedView(el);
            });
            toogleAdvancedView(el);
            }
        }
    });
</script>

<style type="text/css">

.sg_create_more{
  display: block !important;
  margin-bottom: 10px;
}
</style>