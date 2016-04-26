<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: homesponsored.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>

<?php $settings = Engine_Api::_()->getApi('settings', 'core'); ?>
<?php if ($this->direction == 1) { ?>
  <?php foreach ($this->lists as $list):  ?>
 		<div class="SlideItMoo_element seaocore_sponsored_carousel_items">
			<div class="seaocore_sponsored_carousel_items_thumb">
				<?php echo $this->htmlLink($list->getHref(), $this->itemPhoto($list, 'thumb.icon' , $list->getTitle()), array('rel' => 'lightbox[galerie]', 'class' => "thumb_icon")) ?>
			</div>
			<div class="seaocore_sponsored_carousel_items_info">
				<div class="seaocore_sponsored_carousel_items_title">
			  	<?php echo $this->htmlLink($list->getHref(), $this->seacore_api->seaocoreTruncateText($list->getTitle(),$settings->getSetting('list.title.turncationsponsored', 18)), array('title' => $list->getTitle())) ?>
        </div> 
				<div class="seaocore_sponsored_carousel_items_stat seaocore_txt_light">
           <?php echo $this->translate('posted by'); ?>
           <?php echo $this->htmlLink($list->getOwner()->getHref(), $this->seacore_api->seaocoreTruncateText($list->getOwner()->getTitle(), 10), array('title' => $list->getOwner()->getTitle())) ?>
        </div>
			</div>
		</div>	 
  <?php endforeach; ?>
<?php } else {?>

	<?php for ($i = $this->sponserdListsCount; $i < Count($this->lists); $i++):?>
		<div class="SlideItMoo_element seaocore_sponsored_carousel_items">
			<div class="seaocore_sponsored_carousel_items_thumb">
				<?php echo $this->htmlLink($this->lists[$i]->getHref(), $this->itemPhoto($this->lists[$i], 'thumb.icon' , $this->lists[$i]->getTitle()), array( 'rel' => 'lightbox[galerie]', 'class' => "thumb_icon")) ?>
			</div>
			<div class="seaocore_sponsored_carousel_items_info">
				<div class="seaocore_sponsored_carousel_items_title">
					<?php echo $this->htmlLink($this->lists[$i]->getHref(), $this->seacore_api->seaocoreTruncateText($this->lists[$i]->getTitle(),$settings->getSetting('list.title.turncationsponsored', 18)), array('title' => $this->lists[$i]->getTitle())) ?>
				</div>
				<div class="seaocore_sponsored_carousel_items_stat seaocore_txt_light">
					<?php echo $this->translate('posted by'); ?>
					<?php echo $this->htmlLink($this->lists[$i]->getOwner()->getHref(),$this->seacore_api->seaocoreTruncateText($this->lists[$i]->getOwner()->getTitle(),10), array('title' => $this->lists[$i]->getOwner()->getTitle())) ?>
				</div>
			</div>
		</div>	 
	<?php endfor;?>

	<?php for ($i = 0; $i < $this->sponserdListsCount; $i++): ?>
  	<div class="SlideItMoo_element seaocore_sponsored_carousel_items">
			<div class="seaocore_sponsored_carousel_items_thumb">
				<?php   echo $this->htmlLink($this->lists[$i]->getHref(), $this->itemPhoto($this->lists[$i], 'thumb.icon' , $this->lists[$i]->getTitle()), array( 'rel' => 'lightbox[galerie]', 'class' => "thumb_icon")) ?>
       </div>
       <div class="seaocore_sponsored_carousel_items_info">
         <div class="seaocore_sponsored_carousel_items_title">
					<?php echo $this->htmlLink($this->lists[$i]->getHref(), $this->seacore_api->seaocoreTruncateText($this->lists[$i]->getTitle(),$settings->getSetting('list.title.turncationsponsored', 18)), array('title' => $this->lists[$i]->getTitle())) ?>
        </div>
        <div class="seaocore_sponsored_carousel_items_stat seaocore_txt_light">
					<?php echo $this->translate('posted by'); ?>
          <?php echo $this->htmlLink($this->lists[$i]->getOwner()->getHref(),$this->seacore_api->seaocoreTruncateText($this->lists[$i]->getOwner()->getTitle(),10), array('title' => $this->lists[$i]->getOwner()->getTitle())) ?>
        </div>
      </div>
		</div>
	<?php endfor; ?>
<?php } ?>