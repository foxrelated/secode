<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formButtonCancel.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<div id="submit-wrapper" class="form-wrapper">
    <div id="submit-label" class="form-label"> </div>
    <div id="submit-element" class="form-element">
        <button type="submit" id="done" name="done">
            <?php echo ( $this->element->getLabel() ? $this->element->getLabel() : $this->translate('Save Changes')) ?>
        </button>
        <?php echo $this->translate('or'); ?>
        <?php echo $this->htmlLink(array('route' => 'siteevent_gernal', 'action' => 'manage'), $this->translate('cancel')) ?>
    </div>
</div>