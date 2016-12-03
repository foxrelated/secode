<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php if($this->category['banner_id']):?>
	<div class="sr_sitestoreproduct_browse_banner">
		<a href="<?php echo $this->category['banner_url']?>" title="<?php echo $this->category['banner_title'] ?>" <?php if($this->category['banner_url_window'] == 1): ?> target ="_blank" <?php endif;?>><img alt="" src='<?php echo $this->storage->get($this->category['banner_id'], '')->getPhotoUrl(); ?>' /></a>
	</div>	
<?php endif; ?>