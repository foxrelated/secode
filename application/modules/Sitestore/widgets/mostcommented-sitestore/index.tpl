<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 
include_once APPLICATION_PATH . '/application/modules/Sitestore/views/scripts/common_style_css.tpl';
?>
<ul class="sitestore_sidebar_list">
	<?php foreach ($this->sitestores as $sitestore): ?>
		<li> 
			<?php  $this->partial()->setObjectKey('sitestore');
				echo $this->partial('application/modules/Sitestore/views/scripts/partial_widget.tpl', $sitestore);
			?>
					<?php echo $this->translate(array('%s comment', '%s comments', $sitestore->comment_count), $this->locale()->toNumber($sitestore->comment_count)) ?>
				</div>
			</div>
		</li>
	<?php endforeach; ?>
</ul>