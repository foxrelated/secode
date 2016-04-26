<!-- render my widget -->
<?php echo $this->content()->renderWidget('socialstore.main-menu') ?>
<h2>
<?php echo $this->translate('Your purchase has been completed.');?>
</h2>
<?php 
$route = Engine_Api::_()->getApi('settings', 'core')->getSetting('store.pathname', "socialstore");
$url = $this->baseUrl().'/'.$route.'/my-store';
echo $this->translate('Click %1$shere%2$s to go back to your store', '<a href="'.$url.'">','</a>') ?>