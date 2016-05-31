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

<?php $this->headLink()
					->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Siteadvsearch/externals/styles/style_siteadvsearch.css') ?>

<?php
$this->headScript()
			->appendFile($this->layout()->staticBaseUrl .  'externals/autocompleter/Observer.js')
			->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
			->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
			->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>

<?php if(!is_null($this->widgetName)):?>
  <?php $content = $this->widgetName;?>
<?php else:?>
  <?php $content = $this->identity;?>
<?php endif;?>

<?php $containerId = $content.'_titleAjax';?>
<?php $showDefaultFunctionName = 'showAllDeafultContent'.$content;?>
<?php $loadingImageId = 'main-search-loading_'.$content;?>
<?php $defaultContentId = $content.'_default_content';?>
<?php $showDefaultContentId = $content.'_show_default_content';?>
<?php $hideContentId = $content.'_hide_content';?>
<?php $functionName = 'showDefaultContent'.$content;?>
<?php $hidefunctionName = 'hideContent'.$content;?>
<?php $buttonId = 'search_button_'.$content;?>
<?php $pageResultFunctionName = 'getPageResults'.$content;?>
<?php $seeMoreFunctionName = 'seeMoreSearch'.$content;?>

 <div class="<?php if($this->searchbox_width == 0):?>siteadvsearch_search_box_wrap <?php else: ?>layout_siteadvsearch_search_wrapper<?php endif;?> ">
	 <div id='siteadv_menu_mini_menu'>
    <ul>
      <li id="global_search_form_container">
        <form id="global_search_form" action="<?php echo $this->url(array('controller' => 'search'), 'default', true) ?>" method="get">
          <?php $div = (!empty($this->showLocationSearch) ? '<div>' : ''); ?>
          <?php $cdiv = (!empty($this->showLocationSearch) ? '</div>' : ''); ?>  
          <?php echo $div; ?>
          	<input <?php if($this->viewer()->getIdentity()):?> style="width:<?php echo $this->searchbox_width;?>px;" <?php else:?> style="width:<?php echo $this->advsearch_search_box_width_for_nonloggedin;?>px;" <?php endif;?> type='text' class='text suggested' onclick="<?php echo $functionName;?>('click');"  name='query' id='<?php echo $containerId;?>' size='20' maxlength='130' placeholder="<?php echo $this->translate('SEARCH_BOX_MESSAGE');?>"  autocomplete="off"/>
          <?php echo $cdiv; ?>
          
          <?php if($this->showLocationSearch): ?>  
            <?php if($this->locationspecific): ?>
              <div>
                <select name="searchLocation" id="searchLocation">
                    <?php foreach($this->locationArray as $key => $locationElement):?>
                        <option <?php if(!empty($key) && $key == $this->locationValue){echo 'selected = selected';}?> value="<?php echo $key ?>"><?php echo $locationElement; ?></option>
                    <?php endforeach; ?>
                </select>
              </div>
            <?php else: ?>
              <div>
                <input style="width:<?php echo "30";?>px;" type='text' id="searchLocation" class='text suggested' name='searchLocation' size='20' maxlength='130' <?php $selectLocation = $this->translate('Select Location'); echo ($this->locationValue ? "value='$this->locationValue'" : "placeholder='$selectLocation'");?> autocomplete="off"/>
              </div> 
            <?php endif; ?>
          <?php endif; ?>  
          
          <?php echo $div; ?> 
          	<button onclick="<?php echo $showDefaultFunctionName;?>('');" id="<?php echo $buttonId?>" type="button"></button>
          <?php echo $cdiv; ?>
          <?php echo $div; ?>
          	<span style="display: none;" class="fright" id="<?php echo $loadingImageId;?>">
            	<img alt="Loading" src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteadvsearch/externals/images/loading.gif" align="middle" />
         		</span>
          <?php echo $cdiv; ?>
        </form> 
        <?php if(count($this->defaultContents) > 0):?>
          <div id="<?php echo $showDefaultContentId;?>" style="display:none;">
             <ul class="tag-autosuggest adsearch-autosuggest adsearch-stoprequest" id="<?php echo $hideContentId;?>" <?php if($this->viewer()->getIdentity()):?>style="z-index: 42; display: block; opacity: 1; width:<?php echo $this->searchbox_width+6;?>px;" <?php else:?> style="z-index: 42; display: block; opacity: 1; width:<?php echo $this->advsearch_search_box_width_for_nonloggedin+6;?>px;"  <?php endif;?>>
                <?php $countContenetType = 1;?>
                <?php foreach($this->defaultContents as $contentType):?>
                  <?php $privacyCheck = Engine_Api::_()->siteadvsearch()->canViewItemType($contentType->resource_type, $contentType->listingtype_id);?>
                  <?php if(empty($privacyCheck)) continue;?>
                  <?php if (!empty($contentType->file_id)):?>
                    <?php $photo = Engine_Api::_()->storage()->get($contentType->file_id, '')->getPhotoUrl();?>
                    <?php $content_photo = "<img src='$photo' alt='' />";?>
                  <?php else:?>
                    <?php  $content_photo = "<img src='" . $this->layout()->staticBaseUrl . "application/modules/Siteadvsearch/externals/images/search.png' alt='' />";?>
                  <?php endif;?>
                  <?php $label = $this->translate('Find in ') . $this->translate($contentType->resource_title);?>
                  <?php
                  if(Engine_Api::_()->hasModuleBootstrap('sitehashtag') && $contentType->resource_type == 'sitehashtag_hashtag')
                        continue;
                  else{
                  $item_url = $this->url(array('action' => 'index'), 'siteadvsearch_general', true);?>
                  <?php $item_url .= '?query=&type=' . $contentType->resource_type;}?>
                  <li <?php if($countContenetType == 1):?> class="autocompleter-choices autocompleter-selected" <?php else:?> class="autocompleter-choices"<?php endif;?> item_url="<?php echo $item_url;?>" onclick="<?php echo $pageResultFunctionName;?>('<?php echo $item_url;?>','<?php echo $label;?>')" value="<?php echo $label;?>"><?php echo $content_photo?><div class="autocompleter-choice"><?php echo $label;?><div></div></div></li>
                 <input type="hidden" id="<?php echo $defaultContentId;?>" value="<?php echo $item_url.',label:'.$label;?>" />
                <?php $countContenetType++;?>
              <?php endforeach;?>
            </ul>
          </div>
        <?php endif;?>
      </li>
    </ul>
  </div>
</div>
 
<script type='text/javascript'>
  
  function <?php echo $showDefaultFunctionName;?>() {
 
    if($('<?php echo $containerId;?>').value == '') {
      $('<?php echo $hideContentId;?>').style.opacity = '1';
      $('<?php echo $showDefaultContentId;?>').style.display = 'block';
    }
    else {
      var url = '<?php echo $this->url(array('action' => 'index'), 'siteadvsearch_general', true);?>' + '?query=' + encodeURIComponent($('<?php echo $containerId;?>').value) + '&type=' + 'all';
      if($('searchLocation') && $('searchLocation').value) {
         url = url + '&searchLocation=' + $('searchLocation').value;
      }
      window.location.href=url;
    }
  }
  
  function <?php echo $hidefunctionName;?>() {
    if($('<?php echo $hideContentId;?>'))
      $('<?php echo $hideContentId;?>').style.opacity = '0';
    if($('<?php echo $showDefaultContentId;?>'))
      $('<?php echo $showDefaultContentId;?>').style.display = 'none';
    $('<?php echo $loadingImageId;?>').style.display = 'none';
  }
  
  function hide(classname)  {
    
    var node = document.getElementsByTagName("body")[0];
    var a = [];
    var re = new RegExp('\\b' + classname + '\\b');
    var els = node.getElementsByTagName("*");
    for(var i=0,j=els.length; i<j; i++)
    if(re.test(els[i].className))a.push(els[i]);
    return a;
  }
  
  function <?php echo $functionName;?>(event, key) {
    
    if( $$('.adsearch-stoprequest').hasClass('advsearch-remove')) {
      $$('.adsearch-stoprequest').removeClass('advsearch-remove')
    }
    
    if(event == 'enter' && $('<?php echo $containerId;?>').value == '') {
      $('<?php echo $showDefaultContentId;?>').style.display = 'none';
      $('<?php echo $loadingImageId;?>').style.display = 'none';
      var elements = new Array();
      var z= 1;
      elements = hide('adsearch-stoprequest');
      for(i in elements ){
        if(z == 1)
        elements[i].addClass('advsearch-remove');z++;
      }
      if($('<?php echo $defaultContentId;?>')) {
        var explodedValue = $('<?php echo $defaultContentId;?>').value.split(',label:');
        $('<?php echo $containerId;?>').value = explodedValue[1];
        <?php echo $pageResultFunctionName;?>(explodedValue[0],explodedValue[1])
        return;
      }
    }
    else if(event == 'enter' && $('<?php echo $containerId;?>').value != '') {
      <?php echo $pageResultFunctionName;?>('seeMoreLink','');
    }
    if($('<?php echo $containerId;?>').value == '') {
      if($('<?php echo $hideContentId;?>'))
      $('<?php echo $hideContentId;?>').style.opacity = '1';
      if($('<?php echo $showDefaultContentId;?>'))
      $('<?php echo $showDefaultContentId;?>').style.display = 'block';
      $('<?php echo $loadingImageId;?>').style.display = 'none';
      var elements = new Array();
      var z= 1;
      elements = hide('adsearch-stoprequest');
      for(i in elements ){
        if(z == 1 && elements.length > 1)
        elements[i].addClass('advsearch-remove');z++;
      }
      return;
    }
    else {
      if(event == 'keyup' && key != 'down' && key != 'up') {
				    $('<?php echo $loadingImageId;?>').style.display = 'inline-block';
      }
      if($('<?php echo $showDefaultContentId;?>'))
      $('<?php echo $showDefaultContentId;?>').style.display = 'none';
    }
  }
  
  $('<?php echo $containerId;?>').addEvent('keyup', function(e) { 
  
    if($('advmenu_mini_menu_titleAjax')) {
        var OriginalString = $('advmenu_mini_menu_titleAjax').value;
        $('advmenu_mini_menu_titleAjax').value = OriginalString.replace(/(<([^>]+)>)/ig,"");
    }
  
    if(e.key == 'enter')
    <?php echo $functionName;?>('enter');
    else
    <?php echo $functionName;?>('keyup', e.key);
  });
  
  var contentAutocomplete;
  en4.core.runonce.add(function()
  {
    $(document.body).addEvent('click', function(event){
      var str = event.target.className;
      if(str.trim() == "text suggested") {return;}
      var elements = new Array();
      var z= 1;
      elements = hide('adsearch-stoprequest');
      for(i in elements ){
        if(z == 1)
        elements[i].addClass('advsearch-remove');z++;
      }
      if(event.target.id != '<?php echo $buttonId ?>' && event.target.id != '<?php echo $containerId ?>') {
        <?php echo $hidefunctionName;?>('');
      }
    });
    
    var item_count = 0;
    var contentLimit = '<?php echo $this->totalLimit?>';
    var requestURL = '<?php echo $this->url(array('module' => 'siteadvsearch','controller' => 'index','action' => 'get-search-content', 'limit' =>  $this->limit, 'showLocationSearch' => $this->showLocationSearch, 'showLocationBasedContent' => $this->showLocationBasedContent), "default", true) ?>';    
    
    
//    if(<?php echo $this->showLocationBasedContent; ?>) {
//        var searchLocationElement = $('searchLocation');
//        if(searchLocationElement && searchLocationElement.value != 0 && searchLocationElement.value != '') {
//            requestURL = requestURL + '/searchLocationURLValue/' + $('searchLocation').value;
//        }
//    }
    
    var addURL = 
    contentAutocomplete = new Autocompleter.Request.JSON('<?php echo $containerId;?>', requestURL, {
      'postVar' : 'text',
      //'postData' : {'searchLocationValue':$('searchLocation').value},
      'cache': false,
      'minLength': 1,
      'selectFirst': false,
      'selectMode': 'pick',
      'autocompleteType': 'tag',
      'className': 'tag-autosuggest adsearch-autosuggest adsearch-stoprequest',
      'maxChoices': contentLimit,
      'indicatorClass':'checkin-loading',
      'customChoices' : true,
      'filterSubset' : true,
      'multiple' : false,
      'injectChoice': function(token) {
	     if(typeof token.label != 'undefined' ) {
        var seeMoreText = '<?php echo $this->string()->escapeJavascript($this->translate('See more results for') . ' ');?>';
	     
        if (token.item_url != 'seeMoreLink') {
						    var choice = new Element('li', {'class': 'autocompleter-choices', 'html': token.photo,'item_url':token.item_url, onclick:'javascript:<?php echo $pageResultFunctionName;?>("'+token.item_url+'")'     });
          var divEl= new Element('div', {
           'html' : token.type ? this.options.markQueryValueCustom.call(this,(token.label)):token.label ,
           'class' : 'autocompleter-choice'
          }); 
            
          new Element('div', {
           'html' : token.type,//this.markQueryValue(token.type)  
					 'class' : 'seaocore_txt_light f_small'      
          }).inject(divEl);           
			
          divEl.inject(choice);
          new Element('input', {
           'type' : 'hidden',
           'value' : JSON.encode(token)
          }).inject(choice);
          this.addChoiceEvents(choice).inject(this.choices);
          choice.store('autocompleteChoice', token);
        }
        if(token.item_url == 'seeMoreLink') {
          var titleAjax1 = encodeURIComponent($('<?php echo $containerId;?>').value);
          var choice = new Element('li', {'class': 'autocompleter-choices', 'html': '', 'id':'stopevent', 'item_url':''});
          new Element('div', {'html': seeMoreText+ '"' + titleAjax1 + '"' ,'class': 'autocompleter-choicess', onclick:'javascript:<?php echo $seeMoreFunctionName ?>()'}).inject(choice);
            this.addChoiceEvents(choice).inject(this.choices);
            choice.store('autocompleteChoice', token);
        }
      }
    },
    onShow: function() {
      $('<?php echo $loadingImageId;?>').style.display = 'none';
    },
    markQueryValueCustom: function(str) {
		return (!this.options.markQuery || !this.queryValue) ? str
			: str.replace(new RegExp('(' + ((this.options.filterSubset) ? '' : '^') + this.queryValue.escapeRegExp() + ')', (this.options.filterCase) ? '' : 'i'), '<b>$1</b>');
	},        
    });

    if(<?php echo $this->showLocationBasedContent; ?>) {
        
//        contentAutocomplete.setOptions({
//                'postData':{'searchLocationValue':'<?php echo $this->locationValue; ?>'}});
        var searchLocationElement = $('searchLocation');
        if(searchLocationElement && searchLocationElement.value != 0 && searchLocationElement.value != '') {
            
            $('searchLocation').addEvent('change', function() {
            if($('_titleAjax')) { $('_titleAjax').value = '';}  
            contentAutocomplete.setOptions({
                'postData':{'searchLocationValue':$('searchLocation').value}});
            });
        }
    }

    contentAutocomplete.addEvent('onSelection', function(element, selected, value, input) {
      if($('<?php echo $containerId;?>').value != '') {
        window.addEvent('keyup', function(e) {
         if(e.key == 'enter') {
          if(selected.retrieve('autocompleteChoice') != 'null' ) {
           var url = selected.retrieve('autocompleteChoice').item_url;
           if (url == 'seeMoreLink') {
            <?php echo $seeMoreFunctionName ?>();
           }
           else {
            window.location.href=url;
           }
          }
         }
        });    
      }
    });
    contentAutocomplete.addEvent('onComplete', function() {
      $('<?php echo $loadingImageId;?>').style.display = 'none';
    });
  });
  
  function <?php echo $pageResultFunctionName;?>(url, label) {

    if(label != '') {
//      $('<?php echo $containerId;?>').value = encodeURIComponent(label);
        $('<?php echo $containerId;?>').value = label;
      if($('<?php echo $showDefaultContentId;?>'))
      $('<?php echo $showDefaultContentId;?>').style.display = 'none';
    }
    if(url != 'null' ) {
      if (url == 'seeMoreLink') {
        <?php echo $seeMoreFunctionName ?>();
      }
      else {
          
        if(<?php echo $this->showLocationBasedContent; ?>) {
            var searchLocationElement = $('searchLocation');
            var stringMatch = url.match('&type=');
            if(stringMatch && searchLocationElement && searchLocationElement.value != 0 && searchLocationElement.value != '') {
                url = url + '&searchLocation=' + $('searchLocation').value;
            } 
            else if(stringMatch) {
                var locationValue = '<?php echo $this->string()->escapeJavascript($this->locationValue); ?>';
                if(locationValue != '')
                    url = url + '&searchLocation=' + '<?php echo $this->string()->escapeJavascript($this->locationValue); ?>';
            }
        }
          
        window.location.href=url;
      }
    }
  }
  
  function <?php echo $seeMoreFunctionName ?>() {
 
    $('stopevent').removeEvents('click');
    var url = '<?php echo $this->url(array('action' => 'index'), 'siteadvsearch_general', true);?>' + '?query=' + encodeURIComponent($('<?php echo $containerId;?>').value) + '&type=' + 'all';
    
    var searchLocationElement = $('searchLocation');
    if(searchLocationElement && searchLocationElement.value != 0 && searchLocationElement.value != '') {
        url = url + '&searchLocation=' + $('searchLocation').value;
    }    
    
    window.location.href=url;
  }
</script>

<?php if($this->showLocationSearch && !$this->locationspecific): ?>
    <?php $apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
    $this->headScript()->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");?>    

    <script type="text/javascript">
        var countrycities = '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('seaocore.countrycities'); ?>';
        if(countrycities) { 
            var options = {
                types: ['(cities)'],
                componentRestrictions: {country: countrycities}
            };
        }
        else {
            var options = {
                types: ['(cities)']
            }; 
        }

        en4.core.runonce.add(function(){
            var autocomplete = new google.maps.places.Autocomplete($('searchLocation'), options);
        });
    </script>
<?php endif; ?>

<?php if($this->searchbox_width == 0) { ?>
  <style type="text/css">
  /*when user set no width fo rSearch box*/  
   .layout_left .layout_siteadvsearch_search_box .siteadvsearch_search_box_wrap, 
   .layout_right .layout_siteadvsearch_search_box .siteadvsearch_search_box_wrap, 
   .layout_middle  .layout_siteadvsearch_search_box .siteadvsearch_search_box_wrap{
    /*background-color: rgba(0, 0, 0, 0.07);*/
    border-width: 5px;
    /*padding: 5px;*/
   }
   .layout_left .layout_siteadvsearch_search_box .siteadvsearch_search_box_wrap #global_search_form input[type="text"],
   .layout_right .layout_siteadvsearch_search_box .siteadvsearch_search_box_wrap #global_search_form input[type="text"]{
    width:80% !important; 
   }
   .layout_middle .layout_siteadvsearch_search_box .siteadvsearch_search_box_wrap #global_search_form input[type="text"]{
    width:93% !important; 
   }
 </style>
<?php }
else { ?>
 <style type="text/css">
 /*When user set the width of Searchbox*/
   .layout_left .layout_siteadvsearch_search_box .layout_siteadvsearch_search_wrapper, 
   .layout_right .layout_siteadvsearch_search_box .layout_siteadvsearch_search_wrapper, 
   .layout_middle  .layout_siteadvsearch_search_box .layout_siteadvsearch_search_wrapper{
    /*background-color: rgba(0, 0, 0, 0.07);*/
    border-width: 5px;
    padding: 0;
    float:left;
   }
 </style>
<?php } ?>

<?php if($this->showLocationSearch):?>
	<style type="text/css">
		/*when Location search enabled*/
		#global_content .layout_siteluminous_landing_search .layout_siteadvsearch_search_box{
			overflow: visible;
		}
	</style>
<?php endif;?>


