<h2><?php echo $this->translate("Fundraising Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<p>
  <?php echo $this->translate("YNFUNDRAISING_VIEWS_SCRIPTS_ADMIN_MAILS_INDEX_DESCRIPTION") ?>
</p>

<br /> 
<div class='admin_search'>   
<?php  echo $this->form->render($this); ?>
</div>
 <?php echo $this->count." ".$this->translate('mails(s)');   ?>
 <br/>
 
<?php if( count($this->paginator) ): ?>
<?php endif; ?>