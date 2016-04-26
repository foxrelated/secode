<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: index.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/List/externals/styles/style_list.css');?>

<ul class="seaocore_sidebar_list">
	<?php foreach ($this->listings as $list): ?>
		<li> 
			<?php  
				$this->partial()->setObjectKey('list');
				echo $this->partial('application/modules/List/views/scripts/partial.tpl', $list);
			?>
			<span title="<?php echo $list->rating.$this->translate(' rating'); ?>">
				<?php if (($list->rating > 0)): ?>
					<?php for ($x = 1; $x <= $list->rating; $x++): ?>
						<span class="rating_star_generic rating_star" /></span>
					<?php endfor; ?>
					<?php if ((round($list->rating) - $list->rating) > 0): ?>
						<span class="rating_star_generic rating_star_half" /></span>
					<?php endif; ?>
				<?php endif; ?>
			</span>
			</div>  
			</div>
		</li>
	<?php endforeach; ?>
</ul>