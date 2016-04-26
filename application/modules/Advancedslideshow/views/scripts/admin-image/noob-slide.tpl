<h2><?php echo $this->translate('Advanced Slideshow Plugin'); ?></h2>
<?php if (count($this->navigation)): ?>
  <div class='tabs'> <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?> </div>
<?php endif; ?>

<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedslideshow/externals/images/back.png" class="icon" />
<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'advancedslideshow', 'controller' => 'slides', 'action' => 'manage', 'advancedslideshow_id' => $this->advancedslideshow_id), $this->translate('View slides listing'), array('class' => 'buttonlink', 'style' => 'padding-left:0px;')) ?>
<br /><br />
<h2><?php echo $this->translate('Available Slide Layouts'); ?></h2>
<?php 
$tempUrl= $this->url(array('module' => 'advancedslideshow', 'controller' => 'image', 'action' => 'noob-slide', 'slide' => $_GET['slide']), 'admin_default', false) ?>
     <form action="<?php echo $tempUrl . '?slide='.$_GET['slide']; ?>" method="get">
<ul class="admin_themes">
  <li class="alt_row">
    <div class="theme_wrapper"><img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Advancedslideshow/externals/images/admin/slide1.jpg" alt="default"></div>
    <div class="theme_chooser_info">
    	<h3>Layout 1</h3>
      <h4>Layout: Full width<br />Dimension: 1088 * 360</h4>
        <button class="activate_button" name ="slide" value="1">Use this  layout</button>
        <input type="hidden"  value="" id="">
   
    </div>
  </li>
   <li class="alt_row">
    <div class="theme_wrapper"><img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Advancedslideshow/externals/images/admin/slide2.jpg" alt="default"></div>
    <div class="theme_chooser_info">
    	<h3>Layout 2</h3>
      <h4>Layout: Full width<br />Dimension: 1088 * 360</h4>
        <button class="activate_button" name ="slide" value="2">Use this  layout</button>
        <input type="hidden"  value="" id="">
    </div>
  </li>
   <li class="alt_row">
    <div class="theme_wrapper"><img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Advancedslideshow/externals/images/admin/slide3.jpg" alt="default"></div>
    <div class="theme_chooser_info">
      <h3>Layout 3</h3>
      <h4>Layout: Full width<br />Dimension: 1088 * 360</h4>
        <button class="activate_button" name ="slide" value="3">Use this  layout</button>
        <input type="hidden"  value="" id="">
    </div>
  </li>
   <li class="alt_row">
    <div class="theme_wrapper"><img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Advancedslideshow/externals/images/admin/slide4.jpg" alt="default"></div>
    <div class="theme_chooser_info">
      <h3>Layout 4</h3>
      <h4>Layout: Full width<br />Dimension: 1088 * 360</h4>
        <button class="activate_button" name ="slide" value="4">Use this  layout</button>
        <input type="hidden" value="" id="">
 
    </div>
  </li>
  
     <li class="alt_row">
    <div class="theme_wrapper"><img src="<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Advancedslideshow/externals/images/admin/slide5.jpg" alt="default"></div>
    <div class="theme_chooser_info">
    	<h3>Layout 5</h3>
      <h4>Layout: Extended width<br />Dimension: 850 * 343</h4>
        <button class="activate_button" name ="slide" value="5">Use this  layout</button>
        <input type="hidden"  value="" id="">
    </div>
  </li>
</ul>

     </form>
<div class="settings">
  <?php echo $this->form->render($this) ?>
</div>
<script type="text/javascript">
  var tempCount = <?php echo $this->srcCount; ?>;
  function showBrowse(){
    var radios = document.getElementsByName("is_thumb");
    var radioValue;
    if (radios[0].checked) {
      radioValue = radios[0].value; 
    }else {
      radioValue = radios[1].value; 
    }
    if(radioValue == 1) {
      document.getElementById('thumbnail-wrapper').style.display="block";  
         } else{   
      document.getElementById('thumbnail-wrapper').style.display="none";   
    }
  }
  window.addEvent('domready', function() { 
  showBrowse();
});

 function manualBrowse(){
    if(tempCount != 0){
      if(document.getElementsByName("manual")){
        var radios = document.getElementsByName("manual");
        var radioValue;
        if (radios[0].checked) {
          radioValue = radios[0].value; 
        }else {
          radioValue = radios[1].value; 
        }
        if(radioValue == 1) {
          for(var i=1;i<=tempCount;i++)
            document.getElementById('manual_'+i+'-wrapper').style.display="block"; 
            document.getElementById('slide_html-wrapper').style.display="none"; 
        }
        else{   
          for(var i=1;i<=tempCount;i++)
            document.getElementById('manual_'+i+'-wrapper').style.display="none"; 
            document.getElementById('slide_html-wrapper').style.display="block";
        }
      }
    }
  }
  
  window.addEvent('domready', function() { 
  showBrowse();
});
  window.addEvent('domready', function() { 
  manualBrowse();
});
</script>