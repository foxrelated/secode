<?php 
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    goals
 * @copyright  Copyright 2014 Stars Developer
 * @license    http://www.starsdeveloper.com 
 * @author     Stars Developer
 */
?>
<?php echo $this->form/*->setAttrib('class', 'global_form_popup')*/->render($this) ?>

<?php if($this->subject()->achieved == 1): ?>
<style>
#starttime-wrapper,#endtime-wrapper {
    display:none !important;
}
</style>
<?php endif;?>
<script type="text/javascript">
  $$('.core_main_goal').getParent().addClass('active');
</script>
