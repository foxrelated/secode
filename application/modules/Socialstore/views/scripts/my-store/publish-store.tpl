<script type="text/javascript">
function removeSubmit()
{
   $('buttons-wrapper').hide(); 
}

</script>
<?php echo $this->content()->renderWidget('socialstore.main-menu') ?>

<div class='clear'>
  <div class='settings'>
    <?php echo $this->form->render($this) ?>
  </div>

</div>