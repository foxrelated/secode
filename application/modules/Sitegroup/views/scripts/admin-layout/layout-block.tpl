<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: layout-block.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2 class="fleft"><?php echo $this->translate('Groups / Communities Plugin'); ?></h2>
<?php include APPLICATION_PATH . '/application/modules/Sitegroup/views/scripts/manageExtensions.tpl'; ?>
<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs clr'>
  <?php
  // Render the menu
  //->setUlClass()
  echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>
<?php endif; ?>

<div class='tabs'>
	<ul class="navigation">
		<li >
			<?php echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitegroup','controller'=>'defaultlayout','action'=>'index'), $this->translate('Group Profile Layout Type'), array())
			?>
		</li>

		<li>
			<?php
			echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitegroup','controller'=>'layout','action'=>'layout', 'group' => $this->group_id), $this->translate('Group Profile Layout Editor'), array())
		  ?>
		</li>

    <li class="active">
			<?php
			echo $this->htmlLink(array('route'=>'admin_default','module'=>'sitegroup','controller'=>'layout','action'=>'layout-block'), $this->translate('Group Profile Layout Settings'), array())
		  ?>
		</li>
	</ul>
</div>
<?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('sitegroup.layoutcreate', 0)):?>
  <div class='clear sitegroup_settings_form'>
    <div class='settings'>
      <?php echo $this->form->render($this); ?>
    </div>
  </div>
<?php else :?>

		<div class="tip">
	  	<span><?php echo $this->translate('You have disabled Group Profile Layout editing by their owners from the "Edit Group Layout" field in Global Settings. If you enable it, then from here you will be able to choose which blocks / widgets of "Core" and "SocialEngineAddOns" modules should be available to users on their Group Profile. Currently, you can configure Group Profile Layout from the "Layout" > "Layout Editor" section by selecting "Group Profile" from the "Editing" dropdown.'); ?></span>
	  </div>

<?php endif;?>
