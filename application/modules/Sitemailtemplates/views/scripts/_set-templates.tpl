<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemailtemplates
 * @copyright  Copyright 2011-2012 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _set-templates.tpl 6590 2012-06-20 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php 
  $siteTitle = '';
	$bodyContent = '';
  $headerContent = '';
	$siteUrl = $this->siteUrl;
	$site_title = $this->site_title;
	if(!empty($this->img_path)) {
	$logo_photo = $this->img_path;
	}
	else {
		$logo_photo = 'application/modules/Sitemailtemplates/externals/images/web.png';
	}

  $upload_image = explode('/',$logo_photo);
  $encoded_image = rawurlencode($upload_image[2]);
  if($upload_image[0] == 'application' && $upload_image[1] == 'modules' && $upload_image[2] == 'Sitemailtemplates' && $upload_image[3] == 'externals') {
		$path = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST']. $this->baseUrl(). '/'.$logo_photo;
	}
	else {
	  $path = ( _ENGINE_SSL ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST']. $this->baseUrl(). '/'.'public/admin'.'/'.$encoded_image;
	}
?>

<?php $bodyContent .= '<div style="text-align:center;overflow:hidden;">';?>
<?php if($this->show_icon && $this->sitelogo_location == 'body'):?>
	<?php	$bodyContent .= '<div style="float:' .$this->sitelogo_position. '"><a href="' . $siteUrl . '" target="_blank"><img src="'.$path.'" style="max-width:800px;vertical-align: middle;" border="0" /></a></div>';?>
<?php endif;?>
	
<?php if($this->show_title && $this->sitetitle_location == 'body'):?>
	<?php	$bodyContent .= '<div style="margin:0 10px;float:' .$this->sitetitle_position. ';font-family:' .$this->sitetitle_fontfamily. ';font-size:' .$this->sitetitle_fontsize. 'px;"><a href="' .$siteUrl .'" target="_blank" style="text-decoration:none; color:' . $this->header_titlecolor. ';font-weight:bold;">' .$site_title. '</a></div>'; ;?>
<?php endif;?>
	
<?php if($this->show_tagline && $this->tagline_location == 'body'):?>
	<?php	$bodyContent .= '<div style="margin:0 10px;float:' .$this->tagline_position. ';font-family:' .$this->tagline_fontfamily. ';font-size:' .$this->tagline_fontsize. 'px;color:' .$this->header_tagcolor. ';">' .$this->tagline_title. '</div>';?>
<?php endif;?>

<?php $bodyContent .= '</div>'; ?>

<?php if($this->tagline_location == 'above_header'):?>
 <?php $headerContent .= '<tr><td style="text-align:center;"><div style="margin:0 10px 5px;float:' .$this->tagline_position. ';font-family:' .$this->tagline_fontfamily. ';font-size:' .$this->tagline_fontsize. 'px;color:' .$this->header_tagcolor. ';">' .$this->tagline_title. '</div></td></tr>';?>
<?php endif;?>

<?php 
	$description = Zend_Registry::get('Zend_Translate')->_("<p><span style='color: #92999C;'>If you are a member of&nbsp;  <a href='%s' target='_parent'>$site_title</a> and do not want to receive these emails from us in the future, then visit your account settings to manage email notifications. To continue receiving our emails, please add us to your address book or safe list.</span></p>");
	$description= sprintf($description, $siteUrl);

	if($this->show_icon && $this->sitelogo_location == 'header') {
		
		//$path_img = 'http://' . $_SERVER['HTTP_HOST'] . $logo_photo;
		if (!empty($path)) {
			if($this->show_title && $this->sitetitle_location == 'header') {
			$siteTitle .= '<div style="float:' .$this->sitelogo_position. '"><a href="' . $siteUrl . '" target="_blank"><img src="'.$path.'" style="max-width:800px;vertical-align: middle;" border="0" /></a></div>';
			}
			else {
				$siteTitle .= '<div style="float:' .$this->sitelogo_position. '"><a href="' . $siteUrl . '" target="_blank"><img alt="'.$site_title.'" src="'.$path.'" style="max-height:800px;vertical-align: middle;" border="0" /></a></div>';
			}
		}
	}
  if($this->show_title && $this->sitetitle_location == 'header') {
		$siteTitle .= '<div style="margin:0 10px;float:' .$this->sitetitle_position. ';font-family:' .$this->sitetitle_fontfamily. ';font-size:' .$this->sitetitle_fontsize. 'px;"><a href="' .$siteUrl. '" target="_blank" style="text-decoration:none; color:' . $this->header_titlecolor. ';font-weight:bold;">' .$site_title. '</a></div>';
 	}
  if($this->show_tagline && $this->tagline_location == 'header') {
		$siteTitle .= '<div style="margin:0 10px;float:' .$this->tagline_position. ';font-family:' .$this->tagline_fontfamily. ';font-size:' .$this->tagline_fontsize. 'px;color:' .$this->header_tagcolor. ';">' .$this->tagline_title. '</div>';
 	}
?>

<?php if(($this->show_title && $this->sitetitle_location == 'header') || ($this->show_icon && $this->sitelogo_location == 'header') || ($this->show_tagline && $this->tagline_location == 'header')):?> 
	<?php $headerContent .= '<tr><td style="background-color:' .$this->header_bgcol. ';padding:' .$this->header_outpadding. 'px;vertical-align:middle;text-align:center"> ' .$siteTitle. '</td></tr>' ;?>
<?php endif;?>

<?php $html = $bodyContent.$this->bodyHtmlTemplate;?>
<?php	echo $bodyHtmlTemplate = '<table border="0" cellpadding="10" cellspacing="0"><tbody><tr><td bgcolor="' .$this->body_outerbgcol .'"><table border="0" cellpadding="0" cellspacing="0" align="center" style="width:100%;"><tbody>'.$headerContent.'<tr><td colspan="0" bgcolor="' .$this->body_innerbgcol. '" style="font-family:Arial, Helvetica, sans-serif;border-bottom:' .$this->footer_bottomwidth. 'px solid ' .$this->footer_bottomcol. ';border-left:' .$this->lr_bottomwidth. 'px solid ' .$this->lr_bordercolor. ';border-right:' .$this->lr_bottomwidth. 'px solid ' .$this->lr_bordercolor. ';border-top:' .$this->header_bottomwidth. 'px solid ' .$this->header_bottomcolor. ';font-size:12px;padding:10px;" valign="top">'.$html.'</td></tr><tr><td height="5px"></td></tr><tr><td style="background-color:' . $this->signature_bgcol . ';font-size:12px;padding:8px 15px;">' .$this->textofFooter .'</td></tr></tbody></table></td></tr></tbody></table>';?>