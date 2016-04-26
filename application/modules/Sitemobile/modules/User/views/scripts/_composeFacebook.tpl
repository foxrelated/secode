<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: _composeFacebook.tpl 9800 2012-10-17 01:16:09Z richard $
 * @author     John
 */
/**
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
?>


<?php
$facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
$facebookApi = $facebookTable->getApi();
// Disabled
if (!$facebookApi ||
        'publish' != Engine_Api::_()->getApi('settings', 'core')->core_facebook_enable) {
  return;
}
// Not logged in
if (!$facebookTable->isConnected()) {
  return;
}
// Not logged into correct facebook account
if (!$facebookTable->checkConnection()) {
  return;
}

// Add script
$this->headScript()
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/User/externals/scripts/composer_facebook.js');
?>

<script type="text/javascript">
  en4.core.runonce.add(function() {
    composeInstance.addPlugin(new Composer.Plugin.Facebook({
      lang : {
        'Publish this on Facebook' : '<?php echo $this->translate('Publish this on Facebook') ?>'
      }
    }));
  });
</script>
