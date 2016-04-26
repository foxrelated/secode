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
<script type="text/javascript">
function goto(url)
{
    parent.window.open(url,"","status=yes,resizable=yes,scrollbars=yes,fullscreen=no,titlebar=no,width = 1000,height=600");
    return false;
}
</script>