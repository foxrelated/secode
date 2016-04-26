<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: delete.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<form method="post" class="global_form_popup">
    <div>
        <div>
            <h3><?php echo $this->translate("Delete this host?"); ?></h3>
            <p class="form-description">
                <?php echo $this->translate('Are you sure that you want to delete this host? This host will not be recoverable after being deleted.'); ?>
            </p>
            <div class="form-elements">
                <?php if (count($this->eventMoveOptions) > 1): ?>
                    <div class="form-wrapper mbot15">
                        <div class="form-label mbot10"><?php echo $this->translate('To assign events hosted by this host to another host, select a new host from the drop-down below.'); ?></div>
                        <div class="form-element">   
                            <select name="moveInto" >
                                <?php foreach ($this->eventMoveOptions as $k => $v): ?>
                                    <option value="<?php echo $k; ?>" label="<?php echo $v; ?>"><?php echo $v; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                <?php else: ?>
                    <input type="hidden" name="moveInto" value="<?php echo key($this->eventMoveOptions); ?>"/>
                <?php endif; ?>
                <div class="form-wrapper">
                    <div class="form-element">  
                        <input type="hidden" name="confirm" value="true"/>
                        <button type='submit'><?php echo $this->translate('Delete'); ?></button>
                        <?php echo $this->translate('or'); ?> 
                        <a name="cancel" id="cancel" type="button" href="javascript:void(0);" onclick="javascript:parent.Smoothbox.close();"><?php echo $this->translate('cancel'); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>