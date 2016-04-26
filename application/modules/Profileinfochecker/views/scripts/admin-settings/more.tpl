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

<?php echo $this->content()->renderWidget('profileinfochecker.more-plugins') ?>
