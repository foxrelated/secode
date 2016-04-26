<div>
    <?php echo $this->translate('ynmediaimporter_import_description');?>
</div>
<br/>
<div class="clearfix"></div>
<?php
//$serviceNames = array('facebook','picasa','flickr','instagram','yfrog');
$serviceNames = array('facebook','picasa','flickr','instagram');
$Api = Engine_Api::_() -> getApi('settings', 'core');
?>

<?php foreach($serviceNames as $serviceName):?>

<?php 
$settingName = sprintf('ynmediaimporter.%s.enable', $serviceName);
$enable = $Api -> getSetting($settingName, 1);
if( $enable):
?>
<?php
$service = Ynmediaimporter::getProvider($serviceName);
?>
<div class="ynmediaimporter_service_wrapper">    
    <?php if($service->isConnected()):?>
        
        <div class="ynmediaimporter_service_avatar">
            <center>
                <a href="<?php echo $service->getMainUrl();?>" title="<?php echo $this->translate('Discover your media');?>">
                    <img src="<?php echo $service -> getUserAvatarUrl(); ?>" width="48" height="48"/>
                </a>
            </center>
        </div>
        
	    <?php if(!Engine_Api::_() -> getApi('Core', 'Ynmediaimporter')->checkSocialBridgePlugin() && $serviceName == 'facebook'):?>
			<?php echo $this->translate("Please install or enable Social Bridge plugin!");?>
		<?php else:?>
		    <div>
		        <center>
		           <?php echo $this -> translate('Connected as '); ?><a target="_blank" href="<?php echo $service->getUserProfileUrl()?>"><?php echo $service -> getUserDisplayname(); ?></a>
		       </center>
		    </div>
		    <div>
		       <center>
		           <a href="<?php echo $service->getMainUrl();?>"><?php echo $this->translate('Discover '. $serviceName);?></a>
		       </center>       
		    </div>
		    <div>
		       <center>
		           <a href="<?php echo $service->getDisconnectUrl();?>"><?php echo $this->translate('Disconnect');?></a>
		       </center>       
		    </div>
		 <?php endif;?>
    <?php else: ?>
    <div>
        <div class="ynmediaimporter_service_image">
            <center>
                <img src="<?php echo $this->layout()->staticBaseUrl;?>application/modules/Ynmediaimporter/externals/images/service/<?php echo $serviceName; ?>.jpg" height="64"/>
            </center>
        </div>
        <div>
            <center>
            	<?php if(!Engine_Api::_() -> getApi('Core', 'Ynmediaimporter')->checkSocialBridgePlugin() && $serviceName == 'facebook'):?>
            		<?php echo $this->translate("Please install or enable Social Bridge plugin!");?>
            	<?php else:?>
	                <?php if (!Engine_Api::_() -> getApi('Core', 'Ynmediaimporter')->checkFacebookApp() && $serviceName == 'facebook') : ?>
            				<?php echo $this->translate("Please contact admin to config the Facebook settings");?>
            		<?php else: ?>
            				 <a href="<?php echo $service->getConnectUrl();?>" title="<?php echo $this->translate('Connect to '. $serviceName);?>">
	                			<?php echo $this->translate('Connect to '.$serviceName);?>
	                		</a>
            		<?php endif; ?>
	            <?php endif;?>
            </center>
        </div>
        
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>
<?php endforeach; ?>

<?php if ($this->iframeurl): ?> 
<div style="display:none">
    <iframe width="0" height="0" border="none" src="<?php echo base64_decode($this->iframeurl); ?>"></iframe>
</div>
<?php endif; ?>
