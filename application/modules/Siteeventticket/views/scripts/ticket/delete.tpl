<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: delete.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<!--TAX MANDATORY MESSAGE DISPLAY-->
<?php if ($this->taxMandatoryMessage): ?> 
  <div class="tip"><span>
      <?php echo $this->translate('Admin has set the Tax as mandatory, you need to set tax rate before ticket creation. Please set tax rate %1$shere%2$s', '<a href =' . $this->url(array('controller' => 'tax', 'action' => 'index', 'event_id' => $this->event_id), 'siteeventticket_tax_general') . ' >', '</a>') ?></span>
  </div>
  <?php return;
endif;
?>

<div class="global_form_popup siteeventticket_dashbord_popup_form">
  <!--DISPLAY DELETE FORM IF TICKETS SOLD IS '0'-->
<?php if ($this->isTicketSold): ?>
    <div class="tip">
      <span>
  <?php echo $this->translate("Sorry, this ticket cannot be deleted as event guests has already purchased some of them.") ?>
      </span>
    </div>
    <div class="buttons mtop10">
        <button type="button" name="cancel" onclick="javascript:parent.SmoothboxSEAO.close();"><?php echo $this->translate("Close") ?></button>
    </div>
    <?php
    return;
  else:
    ?>
  <?php echo $this->form->render($this) ?>
<?php endif; ?>
</div>