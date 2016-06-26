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
<script type="text/javascript">
    function changeLanguageSubmit(submitElement){
        
        var request = new Request.JSON({
        url : en4.core.baseUrl + 'sitemenu/index/clear-menus-cache/',
        data : {
            format : 'json',
        },
        onSuccess : function(responseJSON) {
            
        }
    });request.send();
        
        $(submitElement).getParent('form').submit();
    }    
</script>
<div id="global_search_form_container" class=""><?php
  if (1 !== count($this->languageNameList)):
    ?>
    <form method="post" action="<?php echo $this->url(array('controller' => 'utility', 'action' => 'locale'), 'default', true) ?>" style="display:inline-block">
      <?php $selectedLanguage = $this->translate()->getLocale() ?>
      <?php echo $this->formSelect('language', $selectedLanguage, array('onchange' => 'changeLanguageSubmit($(this));'), $this->languageNameList) ?>
      <?php echo $this->formHidden('return', $this->url()) ?>
    </form>
    <?php endif;?>
    </div>