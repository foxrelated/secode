<!-- store main menu -->
<?php echo $this->content()->renderWidget('socialstore.main-menu') ?>

<!--  render form-->
<?php echo $this->form->render($this);

$this->headScript()
    ->appendFile($this->baseUrl().'/application/modules/Socialstore/externals/scripts/core.js');
?>
<script type="text/javascript">
function removeSubmit()
{
   $('buttons-wrapper').hide(); 
}
</script>