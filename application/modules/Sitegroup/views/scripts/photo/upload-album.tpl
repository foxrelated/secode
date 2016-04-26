<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: uploadalbum.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
  include APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/Adintegration.tpl';
?>
<?php
	$this->headLink()
  ->appendStylesheet($this->layout()->staticBaseUrl
    . 'application/modules/Sitegroupalbum/externals/styles/style_sitegroupalbum.css');
?>
<script type="text/javascript">
  var updateTextFields = function()
  {
    var album = document.getElementById("album");
    var name = document.getElementById("title-wrapper");
    var auth_tag = document.getElementById("auth_tag-wrapper");

    if (album.value == 0)
    {
      name.style.display = "block";
      auth_tag.style.display = "block";
    }
    else
    {
      name.style.display = "none";
      auth_tag.style.display = "none";
    }
  }
  en4.core.runonce.add(updateTextFields);

  var album_id = '<?php echo $this->album_id ?>';
  var group_id = '<?php echo $this->sitegroup->group_id; ?>';
</script>

<?php include_once APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/payment_navigation_views.tpl'; ?>

<?php $albumid = Zend_Controller_Front::getInstance()->getRequest()->getParam('album_id', null); ?>
<?php if (!empty($albumid)): ?>
  <?php $albums = Engine_Api::_()->getItem('sitegroup_album', $albumid); ?>
<?php endif; ?>
<div class="sitegroup_viewgroups_head">
  <?php echo $this->htmlLink($this->sitegroup->getHref(), $this->itemPhoto($this->sitegroup, 'thumb.icon', '', array('align' => 'left'))) ?>

  <?php if(!empty($this->can_edit)):?>
		<div class="fright">
			<a href='<?php echo $this->url(array('group_id' => $this->sitegroup->group_id), 'sitegroup_edit', true) ?>' class='buttonlink icon_sitegroups_dashboard'><?php echo $this->translate('Dashboard');?></a>
		</div>
	<?php endif;?>
  <h2>
    <?php echo $this->sitegroup->__toString() ?>
    <?php echo $this->translate('&raquo; '); ?>
    <?php echo $this->htmlLink($this->sitegroup->getHref(array('tab'=>$this->tab_selected_id)), $this->translate('Albums')) ?>
    <?php if (!empty($albumid) && empty($this->can_edit)) : ?>
      <?php echo $this->translate('&raquo; '); ?>
      <?php echo $albums->title; ?>
    <?php endif; ?>
  </h2>
</div>
<?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('communityad') && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.communityads', 1) && Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adalbumcreate', 3) && $group_communityad_integration && Engine_Api::_()->sitegroup()->showAdWithPackage($this->sitegroup)): ?>
  <div class="layout_right" id="communityad_uploadalbum">
    <?php echo $this->content()->renderWidget("communityad.ads", array( "itemCount"=>Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.adalbumcreate', 3),"loaded_by_ajax"=>1,'widgetId'=>'group_uploadalbum'))?>
  </div>
<?php endif; ?>
<div class="layout_middle">
  <?php echo $this->form->render($this) ?>
</div>	