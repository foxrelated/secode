<?php 
function finalizeUrl($url)
{
	if ($url)
	{
		if (strpos($url, 'https://') === FALSE && strpos($url, 'http://') === FALSE)
		{
			$pageURL = 'http';
			if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")
			{
				$pageURL .= "s";
			}
			$pageURL .= "://";
			$pageURL .= $_SERVER["SERVER_NAME"];
			$url = $pageURL . '/'. ltrim( $url, '/');
		}
	}

	return $url;
}
$this->doctype('XHTML1_RDFA');
$this->headMeta() -> setProperty('og:image', finalizeUrl($this->campaign->getPhotoUrl()));
$this->headMeta() -> setProperty('og:url', finalizeUrl($this->campaign->getHref()));	
$this->headMeta() -> setProperty('og:type', 'website');	
$this->headMeta() -> setProperty('og:title', $this->campaign->getTitle());	
$this->headMeta() -> setProperty('og:description', $this->campaign->getDescription());	
$this->headMeta() -> setProperty('og:updated_time', strtotime($this->campaign->modified_date));
?>
<div id="ynfundraising-shareinfo">
    <input type='hidden' value='<?php echo $this->campaign_id?>' id='campaign_id' />
    <input type='hidden' value='<?php echo $this->token?>' id='token' />
</div>
<div class="ynfundraising-addthis ynfundraising-share"><span><?php echo $this->translate("Shares"); ?></span><span class="ynfundraising-question">?</span><span class="ynfundraising-value" id="share_value"><?php echo $this->shares;?></span></div>
<div class="ynfundraising-addthis ynfundraising-click"><span><?php echo $this->translate("Clicks"); ?></span><span class="ynfundraising-question">?</span><span class="ynfundraising-value"><?php echo $this->clicks;?></span></div>
<div class="ynfundraising-addthis viral"><span><?php echo $this->translate("Viral Lifl"); ?></span><span class="ynfundraising-question">?</span><span class="ynfundraising-value"><?php echo $this->viralLift;?>%</span></div>

<!-- AddThis Button BEGIN -->
<?php $server = $_SERVER["SERVER_NAME"];?>
<div class="addthis_toolbox addthis_default_style " onclick="share();" {literal}
     addthis:url="<?php echo 'http://'.$server.$this->campaign->getHref().'?user='.$this->user_id;?>"
     addthis:title="Share this page now"
     addthis:description="Share this page now"{/literal}>
    <a class="addthis_button_facebook"></a>
    <a class="addthis_button_twitter"></a>
    <a class="addthis_button_preferred_3"></a>
    <a class="addthis_button_compact"></a>
    <a class="addthis_counter addthis_bubble_style"></a>
</div>
<script type="text/javascript">
    var addthis_config = {
        "data_track_addressbar":false
    };

</script>
<script type="text/javascript" src="http://s7.addthis.com/js/300/addthis_widget.js#pubid=<?php echo $this->pubid?>"></script>
<script type="text/javascript">
    function share() {
        var request = new Request.JSON({
            'method' : 'post',
            'url' :  en4.core.baseUrl + 'ynfundraising/campaign/share',
            'data' : {
                'campaign_id' : <?php echo $this->campaign->getIdentity()?>
            },
            'onComplete':function(responseObject)
            {  
            	$('share_value').innerHTML = responseObject.share;
            }
        });
        request.send();  
    }
</script>
<!-- AddThis Button END -->