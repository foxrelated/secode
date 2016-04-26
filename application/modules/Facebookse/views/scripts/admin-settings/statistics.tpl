<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Facebookse
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: statistics.tpl 6590 2011-01-06 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

?>
<script type="text/javascript">
//<![CDATA[
window.addEvent('domready', function () {
  if ($('show_insightsfb')) {
		fetchdurationSettings (24);
	}
		
 })
var fetchdurationSettings =function(duration) {
  $('show_insightsfb').innerHTML = '<div style="clear:both;text-align:center;"><img src="http://static.ak.fbcdn.net/images/loaders/indicator_white_large.gif" alt="" /></div>';
	if (duration != 0) {
    // SENDING REQUEST TO AJAX 
		url = en4.core.baseUrl+'admin/facebookse/settings/getinsightinfo';
		var request = new Request.HTML({
			'url' : url,
      'method' : 'post',
			'data' : {
								'format' : 'html',
								'duration' : duration,
                'task' : 'isajax'
			},
			onSuccess :  function(responseTree, responseElements, responseHTML, responseJavaScript)  { 
        $('show_insightsfb').innerHTML = responseHTML; 
			}
		});
		request.send();
	  //window.location.href= en4.core.baseUrl+'admin/facebookse/settings/statistics/general/general/'+duration;
	}
}
</script>
<h2><?php echo $this->translate('Advanced Facebook Integration / Likes, Social Plugins and Open Graph');?></h2>
<?php if( count($this->navigation) ): ?>
<div class='seaocore_admin_tabs'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<?php endif; ?>
<h3><?php echo $this->translate('Facebook Integration Statistics');?></h3>
<h4><?php echo $this->translate("Below, you can see the agregated statistics for the Facebook Integration on your site. Facebook provides you more detailed analytics for this integration over here: <a href='http://www.facebook.com/insights/' target='_blank'>http://www.facebook.com/insights/</a>.<br />Note: Insights are only available for sites with 30 or more users connected to Facebook.");?></h4><br />
<div class='tabs'>
   <ul class="navigation">
  		<?php if ($this->category == 'general') {?> 
			<li class="active">
    <?php } else { ?>
				<li>
     <?php }?> 
    <?php //We are not using Facebook Like Statistics feature so we are going to comment this. 03/02/2014.
       //$this->navigation = $this->navigation_sub;
    // Render the menu
    //->setUlClass()
   //echo $this->htmlLink(array('route'=>'facebookse_admin_manage_statistics_general','module'=>'facebookse','controller'=>'admin-settings','action'=>'statistics','category'=>'general'), $this->translate('General Statistics'), array(
              
           // )) 
    ?>
</li>
 <?php //if ($this->category == 'contentlikes') {?> 
			<li class="active">
 <?php //} else { ?>
			<li>
 <?php //}?> 
    <?php
       //$this->navigation = $this->navigation_sub;
    // Render the menu
    //->setUlClass()
   //echo $this->htmlLink(array('route'=>'facebookse_admin_manage_statistics_contentlikes','module'=>'facebookse','controller'=>'admin-settings','action'=>'statistics','category'=>'contentlikes'), $this->translate('Facebook Likes Statistics'), array(
              
           // )) 
    ?>
</li>
</ul>
</div>
<?php if (empty($this->facebookAppError)) { ?>
<?php  if ($this->category == 'contentlikes') : ?>
<!--<div class='admin_search'>
  <?php //echo $this->formFilter->render($this) ?>
</div>-->
<br />

<!--<div class='admin_results'>
  <div>
    <?php $siteUrlCount = count($this->paginator_temp) ?>
    <?php //echo $this->translate(array("%s site url found", "%s site urls found", $siteUrlCount), ($siteUrlCount)) ?>
  </div>
  <div>
    <?php// echo $this->paginationControl($this->paginator); ?>
  </div>
</div>-->

<br />

<!--<div class="admin_table_form" style="width:100%;">
  <table class='admin_table' style="width:100%;">
    <thead>
      <tr>
				<th style="text-align:left;"><?php echo $this->translate("Url") ?></th>
        <th style="text-align:center;"><?php echo $this->translate("No of Likes") ?></th>
      </tr>
    </thead>
    <tbody>
      <?php if( count($this->paginator) ): ?>
        <?php foreach( $this->paginator_temp as $item ): ?>
           <tr>
            <td align="left">
							<a href="<?php echo $item['url'] ?>" target="_blank" title="<?php echo $item['url'] ?>">
								<?php echo $item['url'] ?> 
							</a>
						</td>
            <td style="text-align:center;">
							<?php echo $item['likes'];?> 
						</td>
          </tr>
					<?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
  <br />
</div>-->
<?php else : if (!empty($this->active_users)) { ?>
	<br />
	<table class="facebookse_statistics_activeuser_table">
  	<tr>
  		<th><?php echo $this->translate('Daily Active Users');?></th>
  		<th style="border-right: 1px solid #DDDDDD;border-left: 1px solid #DDDDDD;"><?php echo $this->translate('Weekly Active Users');?></th>
  		<th><?php echo $this->translate('Monthly Active Users');?></th>
  	</tr>
  	<tr>
  		<td>  
  			<?php if (!empty($this->active_users['daily'])) {
					echo $this->active_users['daily'];
       		}
       		else {
						echo "0";
        	}
        ?>
      </td>
			<td style="border-right: 1px solid #DDDDDD;border-left: 1px solid #DDDDDD;">
				<?php if (!empty($this->active_users['weekly'])) {
			 		echo $this->active_users['weekly'];
     			}
		 			else {
						echo "0";
      		} 
      	?>
      </td>
			<td> 
				<?php if (!empty($this->active_users['monthly'])) {
			 		echo $this->active_users['monthly'];
     			}
     			else {
						echo "0";
      		}
      	?>
  	  </td>
		</tr>
	</table>
	<br />
<?php } ?>

<div class="facebookse_statistics_activeuser_form">
	<?php
		echo $this->formFilter->render($this) 
	?>
</div>
<br />
 <div id="show_insightsfb"> </div>

<?php ; endif ; } else { ?>
<ul class="form-errors"><li><ul class="errors"><li><?php  echo $this->facebookAppError;?></li></ul></li></ul>
  <?php  }?>