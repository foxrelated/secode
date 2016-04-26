<h2><?php echo $this->translate('Advanced Slideshow Plugin');?></h2>
<?php if( count($this->navigation) ): ?>
	<div class='tabs'> <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?> </div>
<?php endif; ?>

<img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Advancedslideshow/externals/images/back.png" class="icon" />
<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'advancedslideshow', 'controller' => 'slides', 'action' => 'manage', 'advancedslideshow_id' => $this->advancedslideshow_id), $this->translate('View slides listing'), array('class'=> 'buttonlink', 'style'=> 'padding-left:0px;')) ?>
<br /><br />
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


