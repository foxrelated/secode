<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: getlikecode.tpl 6590 2011-01-06 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<div>
<h4>
<?php echo $this->translate('Your Facebook Like Button code:');?>
</h4>
<div>
<?php echo $this->translate('Copy the below code and add it to the page of your site.');?>
</div>
<?php
//GENERATING THE LIKE BUTTON CODE FOR LIKE BUTTON.

$color_scheme = '';
$layout_style = '';
$like_width = '';
$like_verb_display = '';
$like_font = '';
$show_faces = '';
 $share_button = 'share= "true" ' ;

if ($this->like_button['color_scheme'] == 'dark') {
  $color_scheme = 'colorscheme= "' . $this->like_button['color_scheme'] . '" ';
}

if ($this->like_button['layout_style'] != 'standard') {
  $layout_style = 'layout= "' . $this->like_button['layout_style'] . '" ';
}

if (empty($this->like_button['show_faces'])) {
  $show_faces = 'show_faces= "false" ' ;
}

if (empty($this->like_button['share_button'])) {
  $share_button = 'share= "false" ' ;
}

if ($this->like_button['like_width'] != 450) {
  $like_width = 'width= "' . $this->like_button['like_width'] . '" ';
}
if ($this->like_button['like_verb_display'] == 'recommend') {
  $like_verb_display = 'action= "' . $this->like_button['like_verb_display'] . '" ';
}

if (!empty($this->like_button['like_font'])) {
  $like_font = 'font= "' . $this->like_button['like_font'] . '" ';
} 

?>


<?php $like =  '<script type="text/javascript">var call_advfbjs = 1;</script><fb:like ' . $share_button . $color_scheme .  $layout_style .  $show_faces .  $like_width .  $like_verb_display .  $like_font . '></fb:like>';?>

<textarea name='like_textarea' id="txtArea"  value='' style='width:400px; height:80px;'><?php echo $like;?></textarea>
</div>