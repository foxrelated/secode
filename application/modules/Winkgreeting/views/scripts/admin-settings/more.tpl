<h2>
  <?php echo $this->translate("Wink and Greeting") ?>
</h2>
<p>
<?php echo $this->translate("Plugin provides two useful options (wink and greeting) which help in establishing contact between users. When you click on one of these options the profile owner gets a message what you like his profile and your are interested in communicating with him.") ?>
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

<?php echo $this->content()->renderWidget('winkgreeting.more-plugins') ?>
