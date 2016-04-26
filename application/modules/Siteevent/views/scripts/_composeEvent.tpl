<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Video
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: _composeVideo.tpl 10109 2013-10-31 01:53:50Z andres $
 * @author     Jung
 */
?>
<?php
$parent_type = 'user';
$parent_id = Engine_Api::_()->user()->getViewer()->getIdentity();
if (Engine_Api::_()->core()->hasSubject()) {
  $hasIntegrated = false;
  $subject = Engine_Api::_()->core()->getSubject();
  $moduleName = strtolower($subject->getModuleName());
  if ($moduleName == 'user') {
    if (!$subject->isSelf(Engine_Api::_()->user()->getViewer()))
      return;
  } else {
    
    if ($moduleName == 'sitereview' && isset($subject->listingtype_id)) {
      if ((Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => 'sitereview_listing_' . $subject->listingtype_id, 'item_module' => 'sitereview'))))
        $hasIntegrated = true;
    } else {
      if ((Engine_Api::_()->getDbtable('modules', 'siteevent')->getIntegratedModules(array('enabled' => 1, 'item_type' => $subject->getType(), 'item_module' => strtolower($subject->getModuleName())))))
        $hasIntegrated = true;
    }

    if (!$hasIntegrated) {
      return;
    }
    
    $parent_type = $subject->getType();
    $parent_id = $subject->getIdentity();
   
  }
}
 $isCreatePrivacy = Engine_Api::_()->siteevent()->isCreatePrivacy($parent_type, $parent_id);
    if (empty($isCreatePrivacy))
      return false;
?>

<?php if(Engine_Api::_()->siteevent()->hasPackageEnable()):?>
    <?php $url = $this->url(array('action' => 'index', 'parent_type' => $parent_type, 'parent_id' => $parent_id, 'return_url' => 'SE64-' . base64_encode($_SERVER['REQUEST_URI'])), "siteevent_package", true) ?>
    <?php else:?>
      <?php $url = $this->url(array('action' => 'create', 'parent_type' => $parent_type, 'parent_id' => $parent_id, 'return_url' => 'SE64-' . base64_encode($_SERVER['REQUEST_URI'])), "siteevent_general", true) ?>
<?php endif; ?>

<script type="text/javascript">
  en4.core.runonce.add(function() {
    if (Composer.Plugin.Siteevent || ($('messages_compose')&& $('messages_compose').get('tag')=='form') || ($('messages_form_reply')&& $('messages_form_reply').get('tag')=='form') )
      return;

    Asset.javascript('<?php echo $this->layout()->staticBaseUrl ?>application/modules/Siteevent/externals/scripts/composer_event.js', {
      onLoad: function() {
        composeInstance.addPlugin(new Composer.Plugin.Siteevent({
          title: '<?php echo $this->translate('Create an Event') ?>',
          lang: {
            'Create Event': '<?php echo $this->string()->escapeJavascript($this->translate('Create an Event')) ?>'
          },
          loadJSFiles: ['<?php echo $this->tinyMCESEAO()->addJS(true) ?>'],
          packageEnable : '<?php echo Engine_Api::_()->siteevent()->hasPackageEnable();?>',
          requestOptions: {
            'url': '<?php echo $url ?>'
          }
        }));
      }});
  });
</script>
<?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('siteevent.location', 1)): ?>
  <?php
  $apiKey = Engine_Api::_()->seaocore()->getGoogleMapApiKey();
$this->headScript()
->appendFile("https://maps.googleapis.com/maps/api/js?libraries=places&key=$apiKey");
  ?>
<?php endif; ?>