<script type="text/javascript" src="<?php echo $this-> layout() -> staticBaseUrl?>application/modules/Advalbum/externals/scripts/jquery-1.7.1.min.js"></script>
<?php
$session = new Zend_Session_Namespace('mobile');
if(!$session -> mobile):?>
<script type="text/javascript" src="<?php echo $this-> layout() -> staticBaseUrl?>application/modules/Advalbum/externals/ParallaxSlider/js/jquery.easing.1.3.js"></script>
<script type="text/javascript" src="<?php echo $this-> layout() -> staticBaseUrl?>application/modules/Advalbum/externals/ParallaxSlider/js/slider.js"></script>
<script type="text/javascript" src="<?php echo $this-> layout() -> staticBaseUrl?>application/modules/Advalbum/externals/scripts/jquery.flexslider.js"></script>
<?php endif;
if($session -> mobile)
{
    echo $this->html_mobile_slideshow;
}
else
{
	$this->headLink()
		->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Advalbum/externals/ParallaxSlider/css/style.css')
		->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Advalbum/externals/styles/flexslider.css');
	?>
	<div class="advalbum_responsive_slideshow">
	<?php echo $this->html_ynresponsive_slideshow; ?>
	</div>
<?php }
?>
<?php
if($session -> mobile)
{
	$this->headScript()
		->appendFile($this-> layout() -> staticBaseUrl. 'application/modules/Advalbum/externals/slideshow/responsiveslides.min.js');
	$this->headLink()
		->appendStylesheet($this-> layout() -> staticBaseUrl . 'application/modules/Advalbum/externals/slideshow/responsiveslides.css');
?>

<script type="text/javascript">
jQuery.noConflict();
jQuery(function () {
	 jQuery("#ymb_home_featuredphoto").responsiveSlides({
        nav: true,
        speed: 800,
        namespace: "callbacks"
      });
   });
</script>
<?php } ?>