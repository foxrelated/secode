<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: create.tpl 19.10.13 08:20 jungar $
 * @author     Jungar
 */
?>
<?php
$this->headScript()
  ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Heevent/externals/scripts/manager.js')
  ->appendFile("https://maps.googleapis.com/maps/api/js?sensor=false&v=3.exp&libraries=places")
?>
<?php if( $this->parent_type == 'group' ) { ?>
  <h2>
    <?php echo $this->group->__toString() ?>
    <?php echo '&#187; '.$this->translate('Events');?>
  </h2>
<?php  } ?>
<div class="heeventform">
  <?php echo $this->form->render() ?>
</div>
<?php
  echo $this->render('create_js.tpl');
?>
<style type="text/css">
  #editprivacy-element{
    margin-bottom: 0;
    margin-top: 5px;
  }
</style>