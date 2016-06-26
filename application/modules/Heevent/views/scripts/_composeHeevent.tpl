<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Heevent
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _composeHeevent.tpl 19.10.13 08:20 jungar $
 * @author     Jungar
 */
if(!Engine_Api::_()->core()->hasSubject()){


?>
<?php
$request = Zend_Controller_Front::getInstance()->getRequest();
$is_lineline = 0;
if ($request->getModuleName() == 'timeline' && $request->getControllerName() == 'profile' && $request->getActionName() == 'index') {
  $is_lineline = 1;
}
if($request->getModuleName() == 'page'){
  return;
}
$this->headScript()
  ->appendFile('application/modules/Heevent/externals/scripts/manager.js')
  ->appendFile('application/modules/Heevent/externals/scripts/events_composer.js')
  ->appendFile("https://maps.googleapis.com/maps/api/js?sensor=false&v=3.exp&libraries=places");
?>
<script type="text/javascript">
  Wall.runonce.add(function () {

    try {

      var feed = Wall.feeds.get("<?php echo $this->feed_uid?>");

      feed.compose.addPlugin(new Wall.Composer.Plugin.Heevent({
        title:'<?php echo $this->string()->escapeJavascript($this->translate('HEEVENT_Add Event')) ?>',
        lang:{
          'cancel':'<?php echo $this->string()->escapeJavascript($this->translate('cancel')) ?>'
        },
        is_timeline: <?php echo $is_lineline; ?>
      }));
    $('#heevent-composer-create-form #submit')
    } catch (e) {
//      console.log(e);
    }
  });

</script>
<?php
  $view = $this;
  $helperPath = APPLICATION_PATH
  			. DIRECTORY_SEPARATOR
  			. "application"
  			. DIRECTORY_SEPARATOR
  			. "modules"
  			. DIRECTORY_SEPARATOR
  			. 'Heevent'
        . DIRECTORY_SEPARATOR
        . 'views'
        . DIRECTORY_SEPARATOR
        . 'helpers'
        . DIRECTORY_SEPARATOR;

  $view->addHelperPath($helperPath, 'Heevent_View_Helper_');
?>
<div id="heevent-composer-create-form" class="heevent-create-form  heevent-form global_form"  action="<?php echo $this->url(array('action' =>'create'), 'event_general'); ?>"  >
  <?php
      echo $this->heevent()->getComposerForm();
  ?>
</div>
<div id="background_create_form"></div>
<?php
    echo $this->render('create_js.tpl');
?>
<style type="text/css">
  #heevent-composer-create-form textarea {
    min-height: inherit;
    width: auto;
  }
  #heevent-composer-create-form #auth_view-wrapper{
    display: block;
  }
</style>
<?php } ?>