<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesmusic
 * @package    Sesmusic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: albumsongplaylistartistdayofthe.tpl 2015-03-30 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headScript()
    ->appendFile($baseUrl . 'externals/autocompleter/Observer.js')
    ->appendFile($baseUrl . 'externals/autocompleter/Autocompleter.js')
    ->appendFile($baseUrl . 'externals/autocompleter/Autocompleter.Local.js')
    ->appendFile($baseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>
<script type="text/javascript">
  
  function oftheday(value) {
    setCookie("sesmusic_oftheday", value, 1);
  }
  
  function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+d.toGMTString();
    document.cookie = cname + "=" + cvalue + "; " + expires+"; path=/"; 
  } 
  
  //Take refrences from "/application/modules/Blog/views/scripts/index/create.tpl"
  en4.core.runonce.add(function() {
    setCookie("sesmusic_oftheday", "", - 3600);    
    var album_id = getParams('page');
    $('album_id-wrapper').style.display = 'none';
    var contentAutocomplete = new Autocompleter.Request.JSON('album_title', "<?php echo $this->url(array('module' => 'sesmusic', 'controller' => 'settings', 'action' => 'get-albums'), 'admin_default', true) ?>/album_id/" + album_id, {
      'delay' : 250,
      'postVar': 'text',
      'minLength': 1,
      'selectMode': 'pick',
      'autocompleteType': 'tag',
      'className': 'sesbasic-autosuggest',
      'customChoices': true,
      'filterSubset': true,
      'multiple': false,
      'injectChoice': function(token) {
        var choice = new Element('li', {'class': 'autocompleter-choices', 'html': token.photo, 'id': token.label});
        new Element('div', {'html': this.markQueryValue(token.label), 'class': 'autocompleter-choice'}).inject(choice);
        choice.inputValue = token;
        this.addChoiceEvents(choice).inject(this.choices);
        choice.store('autocompleteChoice', token);
      }
    });
    contentAutocomplete.addEvent('onSelection', function(element, selected, value, input) {
      document.getElementById('album_id').value = selected.retrieve('autocompleteChoice').id;
    });
  });
 
  function getParams(page) {
    
    var params;
    var regexp;  

    page = page.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
    regexp = "[\\?&]" + page + "=([^&#]*)";
    regex = new RegExp(regexp);
    params = regex.exec(parent.window.location.href);

    if (params == null)
      return "";
    else
      return params[1];
  }
</script>
<div class="form-wrapper">
  <div class="form-label"></div>
  <div id="album_title-element" class="form-element">
    <?php echo "Enter the name of the content [chosen from above setting.]."; ?>
    <input type="text" style="width:300px;" class="text" value="" id="album_title" name="album_title">
  </div>
</div>

<script type="text/javascript">
  var cal_starttime_onHideStart = function(){
    // check end date and make it the same date if it's too
    cal_endtime.calendars[0].start = new Date( $('starttime-date').value );
    // redraw calendar
    cal_endtime.navigate(cal_endtime.calendars[0], 'm', 1);
    cal_endtime.navigate(cal_endtime.calendars[0], 'm', -1);
  }
  var cal_endtime_onHideStart = function(){
    // check start date and make it the same date if it's too
    cal_starttime.calendars[0].end = new Date( $('endtime-date').value );
    // redraw calendar
    cal_starttime.navigate(cal_starttime.calendars[0], 'm', 1);
    cal_starttime.navigate(cal_starttime.calendars[0], 'm', -1);
  }
</script>