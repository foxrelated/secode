<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if (count($this->userGroups)): ?>
  <h3> <?php echo $this->sitegroup->getOwner()->toString() ?><?php echo $this->translate("'s Groups"); ?></h3>
  <ul class="sitegroup_sidebar_list">
       <?php  $this->partialLoop()->setObjectKey('sitegroup');
              echo $this->partialLoop('application/modules/Sitegroup/views/scripts/partialloop_widget.tpl', $this->userGroups);
			//echo $this->partialLoop('partialloop_widget.tpl', $this->userGroups)
		?>
  </ul>
<?php endif; ?>