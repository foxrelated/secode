<script type="text/javascript">

var fbShare = {
						url: '<?php echo  ( _ENGINE_SSL ? 'https://' : 'http://' ). $_SERVER['HTTP_HOST'].$this->url()?>',
						size: 'small',
						badge_text: 'C0C0C0',
						badge_color: 'CC00FF',
						google_analytics: 'false'
						}
</script>


<?php if($this->fields->display == 1){
	$title='';
		if($this->fields->field_type == 0)
		{
			$title='Website';
		}
		else if($this->fields->field_type == 1)
		{
			$title='Phone';
		}
		else if($this->fields->field_type == 2)
		{
			$title= 'Status';
		}
		else if($this->fields->field_type == 4)
		{
			$title= 'Profile link';
		}
		else if($this->fields->field_type == 5)
		{
			$title= 'Custom url';
		}
		else 
		{
			$title='Contact';
		}
	?>
<h3><?php echo "QR Code ".$title; ?></h3>
<div style="width:130px;">
<img alt="image" src="public/user/<?php echo $this->fields->image_url ;?>" id="imgwidth">

</div>
	<a download="image" href='public/user/<?php echo $this->fields->image_url ?>' style="float:left;margin-left: 15px;"><img alt="Download" src="application/modules/Qrcode/externals/images/download_button.jpg">&nbsp;</a>
	<script type="text/javascript" src="http://widgets.fbshare.me/files/fbshare.js"></script>			
<?php }?>










