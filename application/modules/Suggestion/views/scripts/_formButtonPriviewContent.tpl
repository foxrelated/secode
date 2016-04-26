<?php 
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _formButtonPriviewContent.tpl 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
echo '<div style="clear:both;"><div style="float:left;" id="sugg_submit"><button type="submit" id="done" name="done">'.$this->translate('Save').'</button></div><div style="float:left;margin:5px"  id="sugg_or"> ' . $this->translate("or") . ' </div><div style="float:left;" id="sugg_preview"><button type="button" id="done1" name="done1" onclick="popup_content(tinyMCE.get(\'content\').getContent(), sugg_bg_color.value);">'.$this->translate('Preview').'</button></div></div>'
?>

<script type="text/javascript">
function popup_content(content, bgcolor)
{
	var content_text = '<div class="sugg_newuser" style="background:' + bgcolor + ';">' + content + '</div>';
	Smoothbox.open(content_text, {autoResize : true});
}
</script>