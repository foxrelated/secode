<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php if ($this->showContent): ?>
    <div class='profile_fields'>
        <?php if (!empty($this->otherDetails)): ?>
            <?php echo Engine_Api::_()->siteevent()->removeMapLink($this->fieldValueLoop($this->siteevent, $this->fieldStructure)) ?>
        <?php else: ?>
            <div class="tip">
                <span ><?php echo$this->translate("There no any information."); ?></span>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>