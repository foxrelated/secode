<h2><?php echo $this->translate("Affiliate Plugin") ?></h2>
<!-- admin menu -->
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<br />

<div class="clear">
	<div class="settings">
	<?php echo $this->form->render($this )?>
		
	</div>
</div>

<style type="text/css">
.tabs > ul > li {
    display: block;
    float: left;
    margin: 2px;
    padding: 5px;
}
.tabs > ul {  
 display: table;
 
}

.tabs > ul > li > a{
      white-space:nowrap!important;
   }
</style>