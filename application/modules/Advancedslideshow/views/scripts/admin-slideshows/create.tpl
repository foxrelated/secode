<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedslideshow
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: create.tpl 2011-10-22 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<script type="text/javascript">
 
  window.addEvent('domready', function() {   if ($('slideshow_type').value == 'noob')              
      setTimeout("showWalk()", 200);
  });
 
  
  function showWalk(){
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
      document.getElementById("opacity-wrapper").style.display="block";
      document.getElementById("thumb_backcolor-wrapper").style.display="block";
      document.getElementById("thumb_bordactivecolor-wrapper").style.display="block";  
      document.getElementById("thumb_bordcolor-wrapper").style.display="block"; 
    }
  }
  
  
  var resource_type = '<?php echo Zend_Controller_Front::getInstance()->getRequest()->getParam('resource_type', null); ?>';
  var slideshowparam =function(slideshow_type, widget_page){
    if(resource_type) {
      window.location.href= en4.core.baseUrl+'admin/advancedslideshow/slideshows/create/slidetype/'+slideshow_type+'/widget/'+widget_page+'/resource_type/'+resource_type;      
    }
    else {
      window.location.href= en4.core.baseUrl+'admin/advancedslideshow/slideshows/create/slidetype/'+slideshow_type+'/widget/'+widget_page;
    }
  }

  var slideshowWidthHeight =function(widget_position) {
    if(widget_position == 'full_width1' || widget_position == 'full_width2' || widget_position == 'full_width3' || widget_position == 'full_width4' || widget_position == 'full_width5') {
      $('width').value = 938;
      $('height').value = 275;
      $('slideshow_widget_preview1').value = '<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedslideshow/externals/images/widget_layout1.gif';
    }
    if(widget_position == 'right_column1' || widget_position == 'right_column2' || widget_position == 'right_column3' || widget_position == 'left_column1' || widget_position == 'left_column2' || widget_position == 'left_column3') {
      $('width').value = 174;
      $('height').value = 180;
      $('slideshow_widget_preview1').value = '<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedslideshow/externals/images/widget_layout1.gif';
    }
    if(widget_position == 'middle_column1' || widget_position == 'middle_column2' || widget_position == 'middle_column3') {
      $('width').value = 517;
      $('height').value = 250;
      $('slideshow_widget_preview1').value = '<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedslideshow/externals/images/widget_layout1.gif';
    }
    if(widget_position == 'extreme1' || widget_position == 'extreme2' || widget_position == 'extreme3') {
      if(resource_type == 'sitestoreproduct_category') {
        $('width').value = 860;
        $('height').value = 300;
      }
      else {
        $('width').value = 728;
        $('height').value = 275;
      }
      $('slideshow_widget_preview1').value = '<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedslideshow/externals/images/widget_layout2.gif';
    }
  }

  function showPositions() {
    parent.Smoothbox.open($('slideshow_widget_preview1').value);
  }
	
  window.addEvent('domready', function() { 
    var widget_position = $('widget_position').value;
    slideshowWidthHeight(widget_position);
  });
</script>

<h2><?php echo $this->translate('Advanced Slideshow Plugin'); ?></h2>

<?php if (count($this->navigation)): ?>
  <div class='tabs'> <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?> </div>
<?php endif; ?>

<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedslideshow/externals/images/back.png" class="icon" />
<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'advancedslideshow', 'controller' => 'slideshows', 'action' => 'manage'), $this->translate('Back to Manage Slideshows'), array('class' => 'buttonlink', 'style' => 'padding-left:0px;')) ?>
<br /><br />

<?php
$temDescription = '<div class="tip"><span>We have released a new Slideshow Type: "HTML Slides with Bullet Navigation". For details, please <a href="http://www.socialengineaddons.com/content/enhancements-advanced-slideshow-plugin-multiple-slideshows" target="_blank">visit here</a>.</span></div>';
$this->form->setDescription("Create a new Slideshow here. Below, you will be able to choose the page for which you want to create the Slideshow, and the corresponding widget position for it. For widgetized pages, you will be able to adjust the vertical position of the Slideshow widget from the Layout Editor. For non-widgetized pages, you will need to place the generated code at the appropriate place in the template file of desired page. Below you will also be able to configure and customize your slideshow based on various parameters. Visit the Demo tab to see the demo of the various slideshow types." . $temDescription);

$this->form->addDecorator('Description', array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
?>


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
