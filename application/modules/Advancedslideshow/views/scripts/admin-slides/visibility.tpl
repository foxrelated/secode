<?php 
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Advancedslideshow
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: visibility.tpl 2011-10-22 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div class="settings global_form_popup">
	<form method="post" class="global_form">
	 	<div>
	 		<div style="width:400px;">
			  <h3><?php echo $this->translate('Slide Visibility Details :');?></h3>
			 	<div class="form-elements">
					<?php if($this->slideshow->network): ?>
						<div class="form-wrapper">
							<div class="form-label">
								<?php echo $this->translate('Network Level Visibility :');?>
							</div>
							<div class="form-element">
								<?php foreach($this->selectedNetworks as $networks): ?>
									<?php echo $networks; ?><br />
								<?php endforeach; ?>
							</div>
						</div>
					<?php else: ?>
						<div class="form-wrapper">
							<div class="form-label">
								<?php echo $this->translate('Network Level Visibility :');?>
							</div>
							<div class="form-element">
								<?php echo $this->translate("Dis-abled"); ?>
							</div>
						</div>
					<?php endif; ?>
		
					<?php if($this->slideshow->level): ?>
						<div class="form-wrapper">
							<div class="form-label">
								<?php echo $this->translate('Member Level Visibility :');?>
							</div>
							<div class="form-element">	
								<?php foreach($this->selectedLevels as $level): ?>
									<?php echo $level; ?><br />
								<?php endforeach; ?>
							</div>
						</div>
					<?php else: ?>
						<div class="form-wrapper">
							<div class="form-label">
								<?php echo $this->translate('Member Level Visibility :');?>
							</div>
							<div class="form-element">
								<?php echo $this->translate("Dis-abled"); ?>
							</div>
						</div>
						<div class="form-wrapper">
							<div class="form-label">
								<?php echo $this->translate("Visitor's Visibility :");?>
							</div>
							<div class="form-element">
								<?php echo $this->visitor_visibility; ?>
							</div>
						</div>
					<?php endif; ?>
					<div class="form-wrapper">
						<button onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('Close');?></button>
					</div>	
			 	</div>
			</div>	
	  </div>
	</form>
</div>

<?php if( @$this->closeSmoothbox ): ?>
	<script type="text/javascript">
  		TB_close();
	</script>
<?php endif; ?>