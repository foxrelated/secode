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

<?php 
if(!Engine_Api::_()->seaocore()->checkModuleNameAndNavigation()):
include_once APPLICATION_PATH . '/application/modules/Siteevent/views/scripts/navigation_views.tpl'; 
endif;?>

<div class='global_form'>
    <!--DISPLAY DELETE FORM IF TICKETS SOLD IS '0'-->
  <?php if (isset($this->canNotDeleteMessage) && $this->canNotDeleteMessage): ?>
    <div class="tip">
      <span>
        <?php echo $this->translate("Sorry, this event cannot be deleted as some of your event tickets has been purchased by event guests.") ?>
        <div class="buttons mtop10">
          <button type="button" name="cancel" onclick="javascript:parent.SmoothboxSEAO.close();"><?php echo $this->translate("Close") ?></button>
        </div>
      </span>
    </div>
  <?php return; else:?>  
    <form method="post" class="global_form">
        <div>
            <div>
                <h3><?php echo $this->translate("Delete event?"); ?></h3>
                <p>
                    <?php echo $this->translate('Are you sure that you want to delete the event with the title "%1$s" last modified %2$s? It will not be recoverable after being deleted.', $this->siteevent->title, $this->timestamp($this->siteevent->modified_date)); ?>
                </p>
                <br />
                <p>
                    <input type="hidden" name="confirm" value="true"/>
                    <button type='submit'><?php echo $this->translate('Delete'); ?></button>
                    <?php echo $this->translate('or'); ?> <a href='<?php echo $this->url(array('action' => 'manage'), 'siteevent_general', true) ?>'><?php echo $this->translate('cancel'); ?></a>
                </p>
            </div>
        </div>
    </form>
 <?php endif;?>
</div>