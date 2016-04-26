<?php
$menu = $this->partial('_menu.tpl', array());
echo $menu;
?>
<div class="ynFRaising_StatisticColLeft">
	<?php
		echo $this->htmlLink($this->campaign->getHref(),$this->translate("Back to Campaign"),array('class'=>'buttonlink ynFRaising_icon_back')); 
		echo $this->filterForm->render($this)
	?>
</div>
<div class="ynfundraising_create_right_menu">
	<div class="quicklinks">
		<ul class="navigation ynfundraising_quicklinks_menu">
			<li>
				<?php echo $this->htmlLink(array('route'=>'ynfundraising_extended', 'controller'=>'campaign', 'action'=>'view-statistics-chart', 'campaign_id' => $this->campaign->campaign_id),$this->translate("Chart"), array('class'=>'active'))?>
			</li>
			<li>
				<?php echo $this->htmlLink(array('route'=>'ynfundraising_extended', 'controller'=>'campaign', 'action'=>'view-statistics-list', 'campaign_id' => $this->campaign->campaign_id),$this->translate("List"))?>
			</li>			
		</ul>
	</div>
</div>
 <div class="admin_statistics_nav">
    <a id="admin_stats_offset_previous" onclick="processStatisticsPage(-1);"><?php echo $this->translate("Previous") ?></a>
    <a id="admin_stats_offset_next" onclick="processStatisticsPage(1);" style="display: none;"><?php echo $this->translate("Next") ?></a>
  </div>
<div class="admin_statistics">
  <script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl ?>externals/swfobject/swfobject.js"></script>
  <script type="text/javascript">
    var currentArgs = {};
    var processStatisticsFilter = function(formElement) {
      var vals = formElement.toQueryString().parseQueryString();
      vals.offset = 0;
      buildStatisticsSwiff(vals);
      return false;
    }
    var processChange = function (formElement)
    {
      var vals = formElement.toQueryString().parseQueryString();
      if(vals.period == "ww")
      {
      	$('chunk').innerHTML = '<option value="dd" label="<?php echo $this->translate("By day"); ?>"><?php echo $this->translate("By day"); ?></option>';
      }

      if(vals.period == "MM")
      {
      	$('chunk').innerHTML = '<option value="dd" label="<?php echo $this->translate("By day"); ?>"><?php echo $this->translate("By day"); ?></option><option value="ww" label="<?php echo $this->translate("By week"); ?>"><?php echo $this->translate("By week"); ?></option>';
      }

      if(vals.period == "y")
      {
      	 $('chunk').innerHTML = '<option value="dd" label="<?php echo $this->translate("By day"); ?>"><?php echo $this->translate("By day"); ?></option><option value="ww" label="<?php echo $this->translate("By week"); ?>"><?php echo $this->translate("By week"); ?></option><option value="MM" label="<?php echo $this->translate("By month"); ?>"><?php echo $this->translate("By month"); ?></option>';
      }
      //buildStatisticsSwiff(vals);
      return false;
    }
    var processStatisticsPage = function(count) {
      var args = $merge(currentArgs);
      args.offset += count;
      buildStatisticsSwiff(args);
    }
    var buildStatisticsSwiff = function(args) {
      currentArgs = args;

      $('admin_stats_offset_next').setStyle('display', (args.offset < 0 ? '' : 'none'));

      var url = new URI('<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $this->url(array('action' => 'chart-data')) ?>');
      url.setData(args);

      //$('my_chart').empty();
      swfobject.embedSWF(
        "<?php echo $this->baseUrl() /*$this->layout()->staticBaseUrl*/ ?>/externals/open-flash-chart/open-flash-chart.swf",
        "my_chart",
        "100%",
        "400",
        "9.0.0",
        "expressInstall.swf",
        {
          "data-file" : escape(url.toString()),
          'id' : 'mooo'
        }
      );
    }

    /* OFC */
    var ofcIsReady = false;
    function ofc_ready()
    {
      ofcIsReady = true;
    }
    var save_image = function() {
      //window.location = 'data:image/png;base64,' + $('my_chart').get_img_binary();

      var img_src = "<img src='data:image/png;base64," + $('my_chart').get_img_binary() + "' />";
      var img_win = window.open('', 'Charts: Export as Image');
      img_win.document.write("<html><head><title>Charts: Export as Image</title></head><body>" + img_src + "</body></html>");

      return;

      // Can't get the stupid call back to work right
      var url = '<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $this->url(array('action' => 'chart-image-upload')) ?>';
      $('my_chart').post_image(url, 'onImageUploadComplete', false);
    }
    var onImageUploadComplete = function() {

    }

    window.addEvent('load', function() {
      buildStatisticsSwiff({
      	'camapign_id': '<?php echo $this->campaign->campaign_id;?>',
        'chunk' : 'dd',
        'period' : 'ww',
        'start' : 0,
        'offset' : 0
      });
    });
  </script>
  <div id="my_chart"></div>  
</div>
<div class="currencyStatus"><?php echo $this->translate("Currency: %s",$this->campaign->currency);?></div>