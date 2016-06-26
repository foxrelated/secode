<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 19.10.13 08:20 jungar $
 * @author     Jungar
 */
?>
<?php
$this->headScript()
  ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Heevent/externals/scripts/manager.js')
  ->appendFile("https://maps.googleapis.com/maps/api/js?sensor=false&v=3.exp&libraries=places");
?>
<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<div class="clear">
  <div class="settings">
    <?php echo $this->form->render($this) ?>
  </div>
</div>
  <script type="text/javascript">
    window.addEvent('domready', function (e) {
      if(window.localStorage){
        window._loc = localStorage.getItem('heevent_admin_setting_location');
      }
      $$('#checkin_google_map_key-element p').set('html', '<?php echo $this->translate('HEVENT_CHECKIN_ADMIN_GLOBAL_DESCRIPTION'); ?>');
      var coverImgEl = $('heevent-admin-setting-cover');
      $('cover_params-element').addEvent('click', function (e) {
        var coverImg = $('heevent-admin-setting-cover');
        var pos = ['left', 'center', 'right'];
        if ($(e.target).get('type') == 'radio') {
          var input = $(e.target);
          coverImg.setStyle('background-position', pos[input.value]);
        } else if ($(e.target).get('id') == 'heevent_cover_repeat') {
          var cbx = $(e.target);
          coverImg.setStyle('background-repeat', (cbx.get('checked')) ? 'repeat' : 'no-repeat');
        }
      });
      $('heevent_map_zoom').addEvent('mousedown', function(){
        getLocation();
        $(this).removeEvent('mousedown');
      });
      $('heevent_map_zoom').addEvent('keypress', function(){
        clearTimeout(window._hevtoi_);
        window._hevtoi_ = setTimeout(function(){drawMap(window._loc)}, 700);
      });
      drawMap();
    });

    function getLocation() {
      if(window._loc)
       return drawMap(window._loc);
      _hem.getCurrentLocation(function (results) {
        var loc = results[0].formatted_address;
        window._loc = loc;
        if(window.localStorage){
          localStorage.setItem('heevent_admin_setting_location', loc);
        }
        drawMap(loc);
      });
    }

    function drawMap(loc) {
      if(window._loc)
        loc = window._loc;
      if(!loc) return;
      var zoom = $('heevent_map_zoom').value;
      var smapEl = $('heevent-admin-setting-map-img');
      var smapDiv = $('coverphoto-element');
      var w = 400;
      var h = 300;
      smapEl.src = 'http://maps.googleapis.com/maps/api/staticmap?center=' + encodeURIComponent(loc) + '&zoom=' + zoom + '&size=' + w + 'x' + h + '&markers=color:red|' + encodeURIComponent(loc) + '&scale=1&sensor=false';
    }

  </script>
  <style type="text/css">
    #cover_params-label,
    #coverphoto-label{
      display: none;
    }

  </style>