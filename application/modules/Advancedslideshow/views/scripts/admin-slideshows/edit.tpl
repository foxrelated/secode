<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedslideshow
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit.tpl 2011-10-22 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
  
  window.addEvent('domready', function() {       
    setTimeout("showWalk()", 200);
  });
  
  function showWalk(){    
    if($('slideshow_type').value == 'noob') {
      $('start_index-wrapper').setStyle('display', 'none');
    
    var radios = document.getElementsByName("noob_walk");
    var radioValue;
    if (radios[0].checked) {
      radioValue = radios[0].value; 
    }else {
      radioValue = radios[1].value; 
    }
    if(radioValue == 1) {
      document.getElementById("noob_walk_position-wrapper").style.display="block";  
      document.getElementById("noob_walkIcon-wrapper").style.display="block";
      document.getElementById("noob_walkSize-wrapper").style.display="block";
      document.getElementById("noob_bulletcolor-wrapper").style.display="block";
      document.getElementById("noob_bulletactivecolor-wrapper").style.display="block";
      document.getElementById("noob_walkDiv-wrapper").style.display="block";
      document.getElementById("thumbnail-wrapper").style.display="none";
      document.getElementById("opacity-wrapper").style.display="none";
      document.getElementById("thumb_backcolor-wrapper").style.display="none";
      document.getElementById("thumb_bordactivecolor-wrapper").style.display="none";
      document.getElementById("thumb_bordcolor-wrapper").style.display="none";
    } else{   
      document.getElementById("noob_walk_position-wrapper").style.display="none";
      document.getElementById("noob_walkIcon-wrapper").style.display="none";
      document.getElementById("noob_walkSize-wrapper").style.display="none";
      document.getElementById("noob_bulletcolor-wrapper").style.display="none";
      document.getElementById("noob_bulletactivecolor-wrapper").style.display="none";
      document.getElementById("noob_walkDiv-wrapper").style.display="none";
      document.getElementById("thumbnail-wrapper").style.display="block";
      if(document.getElementById("opacity-wrapperrandom"))
      document.getElementById("opacity-wrapperrandom").style.display="block";
      document.getElementById("thumb_backcolor-wrapper").style.display="block";
      document.getElementById("thumb_bordactivecolor-wrapper").style.display="block";  
      document.getElementById("thumb_bordcolor-wrapper").style.display="block";
    }
  }
  
  }
  
  var slideshowedit =function(slideshow_type, id){
    window.location.href= en4.core.baseUrl+'admin/advancedslideshow/slideshows/edit/slideshowtype/'+slideshow_type+'/advancedslideshow_id/'+id;
  }
</script>

<h2><?php echo $this->translate('Advanced Slideshow Plugin'); ?></h2>
<?php if (count($this->navigation)): ?>
  <div class='tabs'> <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?> </div>
<?php endif; ?>

<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedslideshow/externals/images/back.png" class="icon" />
<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'advancedslideshow', 'controller' => 'slideshows', 'action' => 'manage'), $this->translate('Back to Manage Slideshows'), array('class' => 'buttonlink', 'style' => 'padding-left:0px;')) ?>
<br /><br />

<div class='seaocore_settings_form'>
  <div class='settings'> <?php echo $this->form->render($this) ?> </div>
</div>

<script type="text/javascript">
  if($('slideshow_type').value != 'flom') {
    $$('input[type=radio]:([name=thumbnail])').addEvent('click', function(e){
      $(this).getParent('.form-wrapper').getAllNext(':([id^=thumbnail-element])').setStyle('display', ($(this).get('value')>0?'none':'none'));
    });
			
    $('thumbnail-1').addEvent('click', function(){
      $('thumb_backcolor-wrapper').setStyle('display', ($(this).get('value') == '1'?'block':'none'));
      if($('opacity-wrapper'))
        $('opacity-wrapper').setStyle('display', ($(this).get('value') == '1'?'block':'none'));
    });
    $('thumbnail-0').addEvent('click', function(){
      $('thumb_backcolor-wrapper').setStyle('display', ($(this).get('value') == '0'?'none':'block'));
      if($('opacity-wrapper'))
        $('opacity-wrapper').setStyle('display', ($(this).get('value') == '0'?'none':'block'));
    });

    $('thumbnail-1').addEvent('click', function(){
      $('thumb_bordcolor-wrapper').setStyle('display', ($(this).get('value') == '1'?'block':'none'));
    });
    $('thumbnail-0').addEvent('click', function(){
      $('thumb_bordcolor-wrapper').setStyle('display', ($(this).get('value') == '0'?'none':'block'));
    });

    $('thumbnail-1').addEvent('click', function(){
      $('thumb_bordactivecolor-wrapper').setStyle('display', ($(this).get('value') == '1'?'block':'none'));
    });
    $('thumbnail-0').addEvent('click', function(){
      $('thumb_bordactivecolor-wrapper').setStyle('display', ($(this).get('value') == '0'?'none':'block'));
    });

    $$('input[type=radio]:([name=random])').addEvent('click', function(e){
      $(this).getParent('.form-wrapper').getAllNext(':([id^=random-element])').setStyle('display', ($(this).get('value')>0?'none':'none'));
    });
		
        if($('slideshow_type').value != 'noob') {
    $('random-1').addEvent('click', function(){
      $('start_index-wrapper').setStyle('display', ($(this).get('value') == '1'?'none':'block'));
    });
    $('random-0').addEvent('click', function(){
      $('start_index-wrapper').setStyle('display', ($(this).get('value') == '0'?'block':'none'));
    });
        }

    $$('input[type=radio]:([name=slide_caption])').addEvent('click', function(e){
      $(this).getParent('.form-wrapper').getAllNext(':([id^=slide_caption-element])').setStyle('display', ($(this).get('value')>0?'none':'none'));
    });
			
    $('slide_caption-1').addEvent('click', function(){
      $('caption_position-wrapper').setStyle('display', ($(this).get('value') == '1'?'block':'none'));
    });
    $('slide_caption-0').addEvent('click', function(){
      $('caption_position-wrapper').setStyle('display', ($(this).get('value') == '0'?'none':'block'));
    });

    $('slide_caption-1').addEvent('click', function(){
      $('caption_backcolor-wrapper').setStyle('display', ($(this).get('value') == '1'?'block':'none'));
    });
    $('slide_caption-0').addEvent('click', function(){
      $('caption_backcolor-wrapper').setStyle('display', ($(this).get('value') == '0'?'none':'block'));
    });

    window.addEvent('domready', function() { 
      var e4 = $('thumbnail-1');
      $('thumb_backcolor-wrapper').setStyle('display', (e4.checked ?'block':'none'));
      if($('opacity-wrapper'))
        $('opacity-wrapper').setStyle('display', (e4.checked ?'block':'none'));
      var e5 = $('thumbnail-0');
      $('thumb_backcolor-wrapper').setStyle('display', (e5.checked?'none':'block'));
      if($('opacity-wrapper'))
        $('opacity-wrapper').setStyle('display', (e4.checked ?'none':'block'));

      var e4 = $('thumbnail-1');
      $('thumb_bordcolor-wrapper').setStyle('display', (e4.checked ?'block':'none'));
      var e5 = $('thumbnail-0');
      $('thumb_bordcolor-wrapper').setStyle('display', (e5.checked?'none':'block'));

      var e4 = $('thumbnail-1');
      $('thumb_bordactivecolor-wrapper').setStyle('display', (e4.checked ?'block':'none'));
      var e5 = $('thumbnail-0');
      $('thumb_bordactivecolor-wrapper').setStyle('display', (e5.checked?'none':'block'));

if($('slideshow_type').value != 'noob') {
      var e4 = $('random-1');
      $('start_index-wrapper').setStyle('display', (e4.checked ?'none':'block'));
      var e5 = $('random-0');
      $('start_index-wrapper').setStyle('display', (e5.checked?'block':'none'));
}

      var e4 = $('slide_caption-1');
      $('caption_position-wrapper').setStyle('display', (e4.checked ?'block':'none'));
      var e5 = $('slide_caption-0');
      $('caption_position-wrapper').setStyle('display', (e5.checked?'none':'block'));

      var e4 = $('slide_caption-1');
      $('caption_backcolor-wrapper').setStyle('display', (e4.checked ?'block':'none'));
      var e5 = $('slide_caption-0');
      $('caption_backcolor-wrapper').setStyle('display', (e5.checked?'none':'block'));
    });
  }
</script>

<script type="text/javascript">
  var showPreview =function(){
  
    var height = $('height').value;
    var width = $('width').value;

    if($('target-1').checked )
      var target = 1;
    else
      var target = 0;

    if($('slide_caption-1').checked )
      var caption = 1;
    else
      var caption = 0;

    var colorback_caption = $('caption_backcolor').value;
    
      if($('caption_position-1') && $('caption_position-1').checked )
      var position_caption = 1;
    else if($('caption_position-2') && $('caption_position-2').checked)
      var position_caption = 2;
    else if($('caption_position-3') && $('caption_position-3').checked)
      var position_caption = 3;
    else
      var position_caption = 0;


    var type = $('slideshow_type').value;

    if($('slideshow_type').value == 'flom') {
      var blinds = $('blinds').value;

      var interval = $('interval').value;
      if($('progressbar-1').checked)
        var progressbar = 1;
      else 
        var progressbar = 0;
    }
    else {
      if($('slideshow_type').value == 'flas') {
        var color1 = $('flash_color1').value;
        var color2 = $('flash_color2').value;
      }

      var thumb_back_color = $('thumb_backcolor').value;
      var thumb_bord_color = $('thumb_bordcolor').value;
      var thumb_bord_active = $('thumb_bordactivecolor').value;

      if($('slideshow_type').value == 'push') {
        var transition1 = $('transition1').value;
        var transition2 = $('transition2').value;
        var transition = transition1+transition2;

        if($('overlap-1').checked )
          var overlap = 1;
        else
          var overlap = 0;
      }

      if($('slideshow_type').value == 'fold') {
        var transition1 = $('transition1').value;
        var transition2 = $('transition2').value;
        var transition = transition1+transition2;

        if($('overlap-1').checked )
          var overlap = 1;
        else
          var overlap = 0;
      }

      if($('random-1').checked ) {
        var random = 1;
        var start_index = 0;
      }
      else {
        var random = 0;
        var start_index = $('start_index').value;
      }

      if($('controller-1').checked)
        var controller = 1;
      else
        var controller = 0;
			
      var delay = $('delay').value;
      var duration = $('duration').value;

      if($('thumbnail-1').checked)
        var thumb = 1;
      else
        var thumb = 0;

      if($('slide_title') && $('slide_title-1').checked)
        var title = 1;
      else
        var title = 0;
    }

    var application_path = "<?php echo $this->preview_base_url; ?>";
    var advancedslideshow_id = "<?php echo $this->advancedslideshow_id; ?>";

    if($('slideshow_type').value == 'flas') {
      var query_string = 'height='+height+'&width='+width+'&type='+type+'&thumb_back_color='+escape(thumb_back_color)+'&thumb_bord_color='+escape(thumb_bord_color)+'&thumb_bord_active='+escape(thumb_bord_active)+'&caption='+caption+'&controller='+controller+'&delay='+delay+'&duration='+duration+'&thumb='+thumb+'&title='+title+'&random='+random+'&color1='+escape(color1)+'&color2='+escape(color2)+'&start_index='+start_index+'&position_caption='+position_caption+'&colorback_caption='+escape(colorback_caption)+'&target='+target;
    }
    else if($('slideshow_type').value == 'push' ) {
      var query_string = 'height='+height+'&width='+width+'&type='+type+'&thumb_back_color='+escape(thumb_back_color)+'&thumb_bord_color='+escape(thumb_bord_color)+'&thumb_bord_active='+escape(thumb_bord_active)+'&caption='+caption+'&controller='+controller+'&delay='+delay+'&duration='+duration+'&thumb='+thumb+'&title='+title+'&overlap='+overlap+'&random='+random+'&color1='+escape(color1)+'&color2='+escape(color2)+'&transition='+transition+'&start_index='+start_index+'&position_caption='+position_caption+'&colorback_caption='+escape(colorback_caption)+'&target='+target; 
    }
    else if($('slideshow_type').value == 'fold' ) {
      var query_string = 'height='+height+'&width='+width+'&type='+type+'&thumb_back_color='+escape(thumb_back_color)+'&thumb_bord_color='+escape(thumb_bord_color)+'&thumb_bord_active='+escape(thumb_bord_active)+'&caption='+caption+'&controller='+controller+'&delay='+delay+'&duration='+duration+'&thumb='+thumb+'&title='+title+'&overlap='+overlap+'&random='+random+'&color1='+escape(color1)+'&color2='+escape(color2)+'&transition='+transition+'&start_index='+start_index+'&position_caption='+position_caption+'&colorback_caption='+escape(colorback_caption)+'&target='+target;
    }
    else if($('slideshow_type').value == 'flom' ) {
      var query_string = 'height='+height+'&width='+width+'&type='+type+'&blinds='+blinds+'&interval='+interval+'&progressbar='+progressbar+'&caption='+caption+'&position_caption='+position_caption+'&colorback_caption='+escape(colorback_caption)+'&target='+target;
    }
    else {
      var query_string = 'height='+height+'&width='+width+'&type='+type+'&thumb_back_color='+escape(thumb_back_color)+'&thumb_bord_color='+escape(thumb_bord_color)+'&thumb_bord_active='+escape(thumb_bord_active)+'&caption='+caption+'&controller='+controller+'&delay='+delay+'&duration='+duration+'&thumb='+thumb+'&title='+title+'&random='+random+'&start_index='+start_index+'&position_caption='+position_caption+'&colorback_caption='+escape(colorback_caption)+'&target='+target;
    }
    if($('slideshow_type').value == 'noob') {
      
                        
      var noob_bulletcolor = $('noob_bulletcolor').value;
      var noob_bulletactivecolor = $('noob_bulletactivecolor').value; 
      
      var noob_effect = $('noob_effect').value;
      if($('noob_autoplay-1').checked )
        var noob_autoplay = 1;
      else
        var noob_autoplay = 0;
      if($('noob_walkIcon-1').checked )
        var noob_walkIcon = 1;
      else
        var noob_walkIcon = 0;
      if($('noob_walkDiv-1').checked )
        var noob_walkDiv = 1;
      else
        var noob_walkDiv = 0;
      if($('noob_walk-1').checked )
        var noob_walk = 1;
      else
        var noob_walk = 0;
      if($('noob_walk_position-left').checked )
        var noob_walk_position = 'Left';
      else if($('noob_walk_position-right').checked )
        var noob_walk_position = 'Right';
      else
        var noob_walk_position = 'Middle';
      
      var  noob_walkSize =  $('noob_walkSize').value;
      var noob_bulletcolor = $('noob_bulletcolor').value;
      var noob_bulletactivecolor = $('noob_bulletactivecolor').value;
      var  opacity =  $('opacity').value;
      query_string = query_string + '&height='+height+'&width='+width+'&type='+type+'&thumb_back_color='+escape(thumb_back_color)+'&thumb_bord_color='+escape(thumb_bord_color)+'&thumb_bord_active='+escape(thumb_bord_active)+'&caption='+caption+'&controller='+controller+'&delay='+delay+'&duration='+duration+'&thumb='+thumb+'&title='+title+'&random='+random+'&start_index='+start_index+'&position_caption='+position_caption+'&colorback_caption='+escape(colorback_caption)+'&target='+target+'&noob_effect='+noob_effect+'&noob_autoplay='+noob_autoplay+'&noob_walkIcon='+noob_walkIcon+'&noob_walkDiv='+noob_walkDiv+'&noob_walk='+noob_walk+'&noob_walk_position='+noob_walk_position+'&noob_walkSize='+noob_walkSize+'&opacity='+opacity+'&noob_bulletcolor='+escape(noob_bulletcolor)+'&noob_bulletactivecolor='+escape(noob_bulletactivecolor);    
      
    }
    height = parseInt(height) + parseInt(75);
    width = parseInt(width) + parseInt(75);
    my_window= window.open (application_path+"admin/advancedslideshow/slideshows/previewedit/advancedslideshow_id/"+advancedslideshow_id+"?"+query_string, null, "width="+width+",height="+height+"resizable=0");
  }
</script>