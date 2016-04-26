<?$this->headScript()->appendFile($this->baseUrl() .'/application/modules/Profileinfochecker/externals/scripts/admin/main.js');?>
<h2>
  <?php echo $this->translate("Profile Info Checker") ?>
</h2>
<p>
  <?php echo $this->translate("This plugin calculates the number of filled profile fields. The useful widget because it reminds the user to fill out the information on his profile. <br />Use the <a href=admin/content>layuot editor</a> to arrange the Profile Info Checker you want on each page.") ?>
</p>
<br />
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<div class='settings'>
<?php echo $this->form->render($this) ?>
</div>
<style>
#current_p-element{
	background-color: #FFFFFF;
	width:204px;
}
#current_p {
	width:130px;
	border:0px;
}
</style>