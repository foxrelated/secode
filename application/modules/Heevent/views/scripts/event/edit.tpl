<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: edit.tpl 19.10.13 08:20 jungar $
 * @author     Jungar
 */
?>

<?php
$this->headScript()
  ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Heevent/externals/scripts/manager.js')
  ->appendFile("https://maps.googleapis.com/maps/api/js?sensor=false&v=3.exp&libraries=places")
?>
<?php echo $this->form->render($this) ?>


<?php
  echo $this->render('create_js.tpl');
?>
