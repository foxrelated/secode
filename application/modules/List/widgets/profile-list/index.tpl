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

<script type="text/javascript">
  en4.core.runonce.add(function(){

    <?php if( !$this->renderOne ): ?>
    var anchor = $('profile_lists').getParent();
    $('profile_lists_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
    $('profile_lists_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

    $('profile_lists_previous').removeEvents('click').addEvent('click', function(){
      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
        }
      }), {
        'element' : anchor
      })
    });

    $('profile_lists_next').removeEvents('click').addEvent('click', function(){
      en4.core.request.send(new Request.HTML({
        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
        data : {
          format : 'html',
          subject : en4.core.subject.guid,
          page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
        }
      }), {
        'element' : anchor
      })
    });
    <?php endif; ?>
  });
</script>

<ul id="profile_lists" class="seaocore_profile_list">
  <?php foreach( $this->paginator as $item ): ?>
    <li>
      <div class='seaocore_profile_list_photo'>
        <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal')) ?>
      </div>
      <div class='seaocore_profile_list_info'>
        <div class='seaocore_profile_list_title'>
					<span>
						<?php if ($item->featured == 1): ?>
							<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/List/externals/images/list_goldmedal1.gif', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
						<?php endif; ?>
					</span>
					<span>
						<?php if ($item->sponsored == 1): ?>
						<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/List/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
					<?php endif; ?>
					</span>
					<?php if( $item->closed ): ?>
						<span>
							<img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/List/externals/images/close.png'/>
						</span>
					<?php endif;?>
        	<?php if (($item->rating > 0) && $this->ratngShow): ?>
						<span title="<?php echo $item->rating.$this->translate(' rating'); ?>" class="list_rating_star">
							<?php for ($x = 1; $x <= $item->rating; $x++): ?>
								<span class="rating_star_generic rating_star" ></span>
							<?php endfor; ?>
							<?php if ((round($item->rating) - $item->rating) > 0): ?>
								<span class="rating_star_generic rating_star_half" ></span>
							<?php endif; ?>
						</span>
					<?php endif; ?>
          <p>
          	<?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
          </p>	
        </div>
        <div class='seaocore_profile_info_date'>
					<?php echo $this->timestamp(strtotime($item->creation_date)) ?> - <?php echo $this->translate('posted by'); ?>
					<?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>,
					<?php echo $this->translate(array('%s comment', '%s comments', $item->comment_count), $this->locale()->toNumber($item->comment_count)) ?>,
					<?php echo $this->translate(array('%s review', '%s reviews', $item->review_count), $this->locale()->toNumber($item->review_count)) ?>,
					<?php echo $this->translate(array('%s view', '%s views', $item->view_count), $this->locale()->toNumber($item->view_count)) ?>,
					<?php echo $this->translate(array('%s like', '%s likes', $item->like_count), $this->locale()->toNumber($item->like_count)) ?>
        </div>
        <div class='seaocore_profile_info_blurb'>
          <?php echo substr(strip_tags($item->body), 0, 350); if (strlen($item->body)>349) echo $this->translate("...");?>
        </div>
      </div>
    </li>
  <?php endforeach; ?>
</ul>

<div class="seaocore_profile_list_more">
  <div id="profile_lists_previous" class="paginator_previous">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
      'onclick' => '',
      'class' => 'buttonlink icon_previous'
    )); ?>
  </div>
  <div id="profile_lists_next" class="paginator_next">
    <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
      'onclick' => '',
      'class' => 'buttonlink_right icon_next'
    )); ?>
  </div>
</div>
