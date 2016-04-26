<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if (empty($this->searchAjax)) : ?>
<h2><?php echo $this->translate('Suggestions / Recommendations Plugin') ?></h2>

<?php if (count($this->navigation)): ?>
  <div class='tabs'>
  <?php
  // Render the menu
  echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>
<?php endif; ?>

<div class='tabs'>
   <ul class="navigation">
  		<?php if ($this->category == 'listview') {?> 
			<li class="active">
    <?php } else { ?>
				<li>
     <?php }?> 
    <?php
       //$this->navigation = $this->navigation_sub;
    // Render the menu
    //->setUlClass()
   echo $this->htmlLink(array('route'=>'suggestion_admin_setting_invite_statistics','category'=>'listview'), $this->translate('List View'), array(
              
            )) 
    ?>
</li>
 <?php if ($this->category == 'graphview') {?> 
			<li class="active">
 <?php } else { ?>
			<li>
 <?php }?> 
    <?php
       //$this->navigation = $this->navigation_sub;
    // Render the menu
    //->setUlClass()
   echo $this->htmlLink(array('route'=>'suggestion_admin_setting_invite_statistics','category'=>'graphview'), $this->translate('Graph View'), array(
              
            )) 
    ?>
</li>
</ul>
</div>

<?php if ($this->category == 'listview'): ?>

<div class='admin_search'>
  <?php echo $this->formFilter->render($this) ?>
</div>

<script type="text/javascript">
  var currentOrder = '<?php echo $this->order ?>';
  var currentOrderDirection = '<?php echo $this->order_direction ?>';
  var changeOrder = function(order, default_direction){
    // Just change direction
    if( order == currentOrder ) {
      $('order_direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
    } else {
      $('order').value = order;
      $('order_direction').value = default_direction;
    }
    $('filter_form').submit();
  }
  var searchMembers = function (thisobj) {
    $('statistics_info').innerHTML = "<center><img src='<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Seaocore/externals/images/loadings.gif' alt='' /></center>";
    
   currentSearchParams = $('search_inviters').getParent('form').toQueryString();
  // var getallcomposer = composeInstance.plugins;

   var param = (currentSearchParams ? currentSearchParams + '&' : '') + 'searchAjax=1&format=html&method=post'; 	

    en4.core.request.send(new Request.HTML( {
      url : 'http://localhost/sev4v_new/admin/suggestion/settings/invite-statistics',      
      data : param,
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript)
      {
        $('statistics_info').innerHTML = responseHTML;
      }
    }), {
      'force':true
    });
    
  }
  
</script>



<br />

<div id="statistics_info">
<?php endif; ?>
 <?php endif; ?>
  <?php if ($this->category == 'listview'): ?>
<div class='admin_results'>
  <div>
    <?php $count = $this->paginator->getTotalItemCount() ?>
    <?php echo $this->translate(array("%s inviter found", "%s inviters found", $count),
        $this->locale()->toNumber($count)) ?>
  </div>
  <div>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
      'pageAsQuery' => true,
      'query' => $this->formValues,
      //'params' => $this->formValues,
    )); ?>
  </div>
</div>

<br />

<div class="admin_table_form">
<form id='multimodify_form' method="post" action="<?php echo $this->url(array('action'=>'multi-modify'));?>" onSubmit="multiModify()">
  <table class='admin_table'>
    <thead>
      <tr>       
        <th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('user_id', 'DESC');"><?php echo $this->translate("ID") ?></a></th>
        <th><a href="javascript:void(0);" onclick="javascript:changeOrder('displayname', 'ASC');"><?php echo $this->translate("Display Name") ?></a></th>
        <th><a href="javascript:void(0);" onclick="javascript:changeOrder('username', 'ASC');"><?php echo $this->translate("Username") ?></a></th>
        <th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('email', 'ASC');"><?php echo $this->translate("Email") ?></a></th>   
        
        <th style='width: 1%;'><a href="javascript:void(0);" onclick="javascript:changeOrder('totalInvites', 'ASC');"><?php echo $this->translate("Total Sent Invites") ?></a></th>
        <th style='width: 1%;'><?php echo $this->translate("Total Referred Invites") ?></a></th>       
      </tr>
    </thead>
    <tbody>
      <?php if( count($this->paginator) ): ?>
        <?php foreach( $this->paginator as $item ) :
          $user = $this->item('user', $item->user_id);
          ?>
          <tr>            
            <td><?php echo $item->user_id ?></td>
            <td class='admin_table_bold'>
              <?php echo $this->htmlLink($user->getHref(),
                  $this->string()->truncate($user->getTitle(), 10),
                  array('target' => '_blank'))?>
            </td>
            <td class='admin_table_user'><?php echo $this->htmlLink($user->getHref(), $user->username, array('target' => '_blank')) ?></td>
            <td class='admin_table_email'>
              <?php if( !$this->hideEmails ): ?>
                <a href='mailto:<?php echo $item->email ?>'><?php echo $item->email ?></a>
              <?php else: ?>
                (hidden)
              <?php endif; ?>
            </td>
            <td class='admin_table_user'><?php echo $item->totalInvites; ?></td>
            <td class='admin_table_user'><?php echo Engine_Api::_()->getApi('invite', 'seaocore')->inviteCounts($item->user_id, 'signedup'); ?></td>
            
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
  <br /> 
</form>
</div>
<?php if (empty($this->searchAjax)) : ?>
</div>
<?php endif; ?>
<?php endif;?>

<?php // GRAPH VIEW ?> 


<?php  if ($this->category == 'graphview') : ?>
<div style="clear:both;height:15px;"></div>
	<div class="cadmc_statistics">
		<div>
	    <p>
	      <?php echo $this->translate("Use the below filter to observe various metrics of your ad over different time periods.") ?>
	    </p>
			<div class="cadmc_statistics_search">
				<?php echo $this->formFilterGraph->render($this) ?>
			</div>
			
		  <div class="cadmc_statistics_nav">
		    <a id="admin_stats_offset_previous"  class='buttonlink icon_previous' onclick="processStatisticsPage(-1);" href="javascript:void(0);" style="float:left;"><?php echo $this->translate("Previous") ?></a>
		    <a id="admin_stats_offset_next" class='buttonlink_right icon_next' onclick="processStatisticsPage(1);" href="javascript:void(0);" style="display:none;float:right;"><?php echo $this->translate("Next") ?></a>
		  </div>

  
		  
		  <script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl ?>externals/swfobject/swfobject.js"></script>
		  <script type="text/javascript">
		    var prev = '<?php echo $this->prev_link ?>';
		    var currentArgs = {};
		    var processStatisticsFilter = function(formElement) {
		      var vals = formElement.toQueryString().parseQueryString();
		      vals.offset = 0;
		      buildStatisticsSwiff(vals);
		      return false;
		    }
		    var processStatisticsPage = function(count) {
		      var args = $merge(currentArgs);
		      args.offset += count;
		      buildStatisticsSwiff(args);
		    }
		    var buildStatisticsSwiff = function(args) {

		      var earliest_date = '<?php echo $this->earliest_ad_date ?>';
		      var startObject = '<?php echo $this->startObject ?>';

		      if(args.offset < 0) {
						switch(args.period) {
							case 'ww':
							startObject = startObject - (Math.abs(args.offset)*7*86400);
							break;
							
							case 'MM':
							startObject = startObject - (Math.abs(args.offset)*31*86400);
							break;

							case 'y':
							startObject = startObject - (Math.abs(args.offset)*366*86400);
							break;
						}
						$('admin_stats_offset_previous').setStyle('display', (startObject > earliest_date ? '' : 'none'));
		      }
		      else if(args.offset > 0) {
						$('admin_stats_offset_previous').setStyle('display', 'block');
		      }
		      else if(args.offset == 0) {
						switch(args.period) {
							case 'ww':
							if (typeof args.prev_link != 'undefined') {
									$('admin_stats_offset_previous').setStyle('display', (args.prev_link >= 1 ? '' : 'none')); 
							}
							else {
									$('admin_stats_offset_previous').setStyle('display', (startObject > earliest_date ? '' : 'none'));
							}
							break;
							
							case 'MM':
								startObject = '<?php echo mktime(0, 0, 0, date('m', $this->startObject), 1, date('Y', $this->startObject)) ?>';
								$('admin_stats_offset_previous').setStyle('display', (startObject > earliest_date ? '' : 'none'));
								break;

							case 'y':
								startObject = '<?php echo mktime(0, 0, 0, 1, 1, date('Y', $this->startObject)) ?>';
								$('admin_stats_offset_previous').setStyle('display', (startObject > earliest_date ? '' : 'none'));
								break;
						}
		      }
		  
		      currentArgs = args;

		      $('admin_stats_offset_next').setStyle('display', (args.offset < 0 ? '' : 'none'));

		      var url = new URI('http://localhost/sev4v_new/seaocore/invite/chart-data');
          
		      url.setData(args);
		      
		      //$('my_chart').empty();
		      swfobject.embedSWF(
						"<?php echo $this->layout()->staticBaseUrl ?>externals/open-flash-chart/open-flash-chart.swf",
						"my_chart",
						"850",
						"400",
						"9.0.0",
						"expressInstall.swf",
						{
							"data-file" : escape(url.toString()),
							'id' : 'mooo'
						}
		      );
		    }	    

		    window.addEvent('load', function() {
		      buildStatisticsSwiff({
					'type' : 'all',
					'mode' : 'normal',
					'chunk' : 'dd',
					'period' : 'ww',
					'start' : 0,
					'offset' : 0,
					'ad_subject' : 'ad',
					'prev_link' : prev
					});
		    });
		  </script>

				<div id="my_chart">
					<center><img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' style='margin:10px 0;' /></center>
				</div>
			</div>	
			</div>
		</div>	
	
		<div>
		  <form name="setSession_form" method="post" id="setSession_form" action="<?php echo $this->url(array('module' => 'communityad', 'controller' => 'index', 'action' => 'set-session'), 'default', true) ?>">
		    <input type="hidden" name="ad_ids_session" id="ad_ids_session">
		  </form>
		</div>

		<script type="text/javascript">
		function setSession(id){

		    document.getElementById("ad_ids_session").value=id;
		    document.getElementById("setSession_form").submit();
		}
		</script>


<?php endif;?>