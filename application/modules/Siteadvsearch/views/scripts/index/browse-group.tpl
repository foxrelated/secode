<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteadvsearch
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: browse-group.tpl 2014-08-06 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if( count($this->paginator) > 0 ): ?>
  <div id="list_view">
    <ul class='groups_browse'>
      <?php foreach( $this->paginator as $group ): ?>
        <li>
          <div class="groups_photo">
            <?php echo $this->htmlLink($group->getHref(), $this->itemPhoto($group, 'thumb.normal')) ?>
          </div>
          <div class="groups_options"></div>
          <div class="groups_info">
            <div class="groups_title">
              <h3><?php echo $this->htmlLink($group->getHref(), $group->getTitle()) ?></h3>
            </div>
            <div class="groups_members">
              <?php echo $this->translate(array('%s member', '%s members', $group->membership()->getMemberCount()),$this->locale()->toNumber($group->membership()->getMemberCount())) ?>
              <?php echo $this->translate('led by');?> <?php echo $this->htmlLink($group->getOwner()->getHref(), $group->getOwner()->getTitle()) ?>
            </div>
            <div class="groups_desc">
              <?php echo $this->viewMore($group->getDescription()) ?>
            </div>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php elseif( preg_match("/category_id=/", $_SERVER['REQUEST_URI'] )): ?>
	 <div class="tip">
			 <span>
			   <?php echo $this->translate('Nobody has created a group with that criteria.');?>
			   <?php if( $this->canCreate ): ?>
				    <?php echo $this->translate('Why don\'t you %1$screate one%2$s?',
					'<a href="'.$this->url(array('action' => 'create'), 'group_general').'">', '</a>') ?>
			   <?php endif; ?>
			 </span>
	 </div>    
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are no groups yet.') ?>
      <?php if( $this->canCreate): ?>
        <?php echo $this->translate('Why don\'t you %1$screate one%2$s?',
        '<a href="'.$this->url(array('action' => 'create'), 'group_general').'">', '</a>') ?>
      <?php endif; ?>
    </span>
  </div>
<?php endif; ?>
<div class="clr" id="scroll_bar_height"></div>
<?php if (empty($this->is_ajax)) : ?>
  <div class = "seaocore_view_more mtop10" id="seaocore_view_more" style="display: none;">
    <?php
    echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
        'id' => '',
        'class' => 'buttonlink icon_viewmore'
    ))
    ?>
  </div>
  <div class="seaocore_view_more" id="loding_image" style="display: none;">
    <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' style='margin-right: 5px;' />
    <?php echo $this->translate("Loading ...") ?>
  </div>
  <div id="hideResponse_div"> </div>
<?php endif;?>
  
<script>
  var url = en4.core.baseUrl + 'siteadvsearch/index/browse-group';
  var ulClass = '.groups_browse';
</script>
<?php include APPLICATION_PATH . "/application/modules/Siteadvsearch/views/scripts/viewmoreresuls.tpl"; ?>
   
