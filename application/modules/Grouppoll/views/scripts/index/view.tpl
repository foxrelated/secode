<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Grouppoll
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view.tpl 6590 2010-12-08 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2 style="float:left;">
	<?php echo $this->htmlLink($this->url(array('id' => $this->grouppoll->group_id, 'tab' => Engine_Api::_()->grouppoll()->getTabId()), 'group_profile'), $this->group_title, array()) ?><?php echo $this->translate("'s Polls")?>
</h2>
<span class="grouppolls_view_backlink">
	<?php echo $this->htmlLink(array('route' => 'group_profile', 'id' => $this->grouppoll->group_id, 'tab' => Engine_Api::_()->grouppoll()->getTabId()), $this->translate('Back to Group'), array('class'=>'buttonlink  icon_grouppoll_back')) ?>

	<?php if($this->grouppoll->owner_id == $this->viewer_id || $this->grouppoll->group_owner_id == $this->viewer_id): ?>
		<?php echo $this->htmlLink(array('route' => 'grouppoll_delete', 'poll_id' => $this->grouppoll->poll_id, 'group_id' => $this->grouppoll->group_id), $this->translate('Delete poll'), array(
		'class'=>'buttonlink icon_grouppoll_delete')) ?>
	<?php endif; ?>
</span>
<div class="layout_middle clr">
  <div class='grouppolls_view'>
    <h3>
      <?php echo $this->grouppoll->title ?>
      <?php if( $this->grouppoll->closed ): ?>
        <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Grouppoll/externals/images/close.png' alt="<?php echo $this->translate('Closed') ?>" />
			<?php endif ?><br/>
    </h3>
    <div class="grouppolls_view_info_date">
      <?php echo $this->translate('Created by %s', $this->htmlLink($this->grouppoll->getOwner(), $this->grouppoll->getOwner()->getTitle())) ?>
      <?php echo $this->timestamp($this->grouppoll->creation_date) ?>
    </div>
    <div class="grouppoll_desc">
      <?php echo $this->grouppoll->description ?>
    </div> 
    <?php echo $this->render('_grouppoll.tpl') ?>
		<?php if($this->can_comment == 1 && $this->viewer_id != 0): ?>
		  <?php echo $this->action("list", "comment", "core", array("type"=>"grouppoll_poll", "id"=>$this->grouppoll->poll_id)) ?>
		<?php endif; ?>
  </div>
</div>