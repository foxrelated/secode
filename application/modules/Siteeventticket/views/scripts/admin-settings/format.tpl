<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteeventticket
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: format.tpl 2015-05-11 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2>
  <?php echo 'Advanced Events Plugin'; ?>
</h2>
<?php if (count($this->navigation)): ?>
  <div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

<?php if (count($this->navigationGeneral)): ?>
  <div class='seaocore_admin_tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigationGeneral)->render() ?>
  </div>
<?php endif; ?>

<div class='seaocore_settings_form'>
  <div class='settings'>
    <?php
    echo $this->form->render($this);
    ?>
  </div>
</div>


<script type="text/javascript">
  
  var setLanguage = function(language) {
    var url = '<?php echo $this->url(array('language' => null)) ?>';
    window.location.href = url + '/language/' + language;
  };
  
  function seeAttachementPreview() { 
    $('attachementPreviewLoadingImage-label').innerHTML = '<img src=' + en4.core.staticBaseUrl + 'application/modules/Siteeventticket/externals/images/loading.gif height=15 width=15>';
    var request = new Request.JSON({
        url: '<?php echo $this->url(array('module' => 'siteeventticket', 'controller' => 'settings', 'action' => 'save-format-preview'), "admin_default"); ?>',
        method: 'post',
        data: {
            format : 'json',
            tinyMceContent: tinymce.activeEditor.getContent(),
            adsimagepreview : $('siteeventticket_adsimage').value
        },
        //responseTree, responseElements, responseHTML, responseJavaScript
        onSuccess: function(responseJSON) {
            $('attachementPreviewLoadingImage-label').innerHTML = '';
            window.open(en4.core.baseUrl + 'admin/siteeventticket/settings/show-attachment-preview');
        }
    });
    request.send();    
  }

</script>