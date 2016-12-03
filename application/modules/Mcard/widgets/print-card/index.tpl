<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Mcard
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2010-10-13 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h4><?php echo $this->translate("Exclusive membership card for %s", $this->comunity_name);?></h4>

<?php echo $this->userCard; ?>

<!--FULLSITE WILL HAVE PRINT MEMBERSHIP CARD LINK-->
<?php  if (Engine_API::_()->seaocore()->checkSitemobileMode('fullsite-mode')):?>
<div class="print">
  <?php
  echo $this->htmlLink(array('route' => 'default', 'module' => 'mcard', 'controller' => 'index', 'action' => 'print', 'subject_id' => $this->subject_id), $this->print_message, array('class' => 'smoothbox buttonlink'));
  ?>
</div>
<?php $userCard = preg_replace( '/\s+/', ' ', $this->userCard ); ?>
<script type="text/javascript">
  function showLightBox_Mcard(){
    var data = "<?php echo $userCard; ?>";
    Smoothbox.open(data, {width : "250",height:"100"});
  }
</script>
<?php endif;?>