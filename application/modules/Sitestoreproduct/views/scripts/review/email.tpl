<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestoreproduct
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: email.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitestoreproduct/externals/styles/style_sitestoreproduct.css');?>

<div class="sr_sitestoreproduct_form_popup">
  <?php echo $this->form->render($this); ?>
</div>