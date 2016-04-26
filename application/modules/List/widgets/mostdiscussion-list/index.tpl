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
      <?php echo $this->translate(array('%s Discussion', '%s Discussions', $list->counttopics), $this->locale()->toNumber($list->counttopics)) ?> 
      </div>
      <div class='seaocore_sidebar_list_details'>
      	<?php echo $this->translate(array('%s Reply', '%s Replies', $list->total_count), $this->locale()->toNumber($list->total_count)) ?>
			</div>
			</div>
		</li>
  <?php endforeach; ?>
</ul>