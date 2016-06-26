<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemenu
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2014-05-26 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>

<?php echo $this->partial($this->script[0], $this->script[1], array(
  'form' => $this->form
)) ?>

<script type="text/javascript">
  
  if( $("user_signup_form") ) $("user_signup_form").getElements(".form-errors").destroy();
  
  function skipForm() {
    document.getElementById("skip").value = "skipForm";
    $('SignupForm').submit();
  }
  
  function finishForm() {
    document.getElementById("nextStep").value = "finish";
  }
</script>