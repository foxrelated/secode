<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
 */
?>

<script type="text/javascript">
  window.addEvent('load', function () {
    var $tabs = $('main_tabs');
    if ($tabs != undefined) {
      var $li = $tabs.getElementsByTagName('li')[0];
      var $a = $li.getElementsByTagName('a')[0];
      tabContainerSwitch($a);
    }
  });
</script>

<div class="product-profile-product-info">

  <h2>
    <?php if ($this->product->sponsored) : ?>
      <img class="icon" src="application/modules/Store/externals/images/sponsoredBig.png"
           title="<?php echo $this->translate('STORE_Sponsored'); ?>">
    <?php endif; ?>
    
    <?php echo ($this->isLike) ? $this->likeButton($this->product) : ''; ?>

    <?php echo('' != trim($this->product->getTitle()) ? $this->product->getTitle() : '<em>' . $this->translate('Untitled') . '</em>'); ?>
  </h2>

  <?php if (!empty($this->product->item_condition)):?>
    <span class="condition-status">
      <td class="options"><?php echo $this->translate($this->product->getCondition()); ?></td>
    </span>
  <?php endif;?>

    <span class="owner-status">
        <?php $subject = Engine_Api::_()->user()->getUser($this->product->owner_id); ?>
        <?php echo $this->translate('See more items on '); ?>
	<a href="/profile/<?php echo($subject->username);?>" target="_blank" ><?php echo($subject->username);?>
	</a>
    </span>

  <?php echo ($this->isRate) ? $this->quickProductRate($this->product, true) : ''; ?>
</div>
<div class="product-profile-store-info">
  <?php if (null != ($store = $this->product->getStore())) : ?>
    <table>
      <tr>
        <td>
          <?php echo $this->htmlLink($store->getHref(), $this->itemPhoto($store, 'thumb.normal')); ?>
        </td>
        <td style="padding-left: 10px; vertical-align: top;">
          <h3>
            <?php echo $this->htmlLink($store->getHref(), $store->getTitle()); ?>
          </h3>

          <div>
            <?php echo $this->itemRate('page', $store->getIdentity()); ?>
            <div class="page_list_submitted" >
              <?php echo $store->view_count ?> <?php echo $this->translate("views"); ?>
            </div>
          </div>
        </td>
      </tr>
    </table>
  <?php endif; ?>
</div>
<div style="clear:both;"></div>