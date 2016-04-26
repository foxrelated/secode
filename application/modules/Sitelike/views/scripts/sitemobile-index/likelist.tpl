<?php
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitelike
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: likelist.tpl 6590 2010-11-04 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if(empty($this->is_ajax)):?>
	<div class="headline">
		<h2><?php echo $this->translate('Likes');?></h2>
		<div class='tabs'>
			<?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
		</div>
	</div>
<?php endif;?>

<script type="text/javascript">
 var call_status = '<?php echo $this->call_status; ?>';
 var resource_id = '<?php echo $this->resource_id; ?>';// Resource Id which are send to controller in the 'pagination' & 'searching'.
 var resource_type = '<?php echo $this->resource_type; ?>';// Resource Type which are send to controller in the 'pagination' & 'searching'.
 var url = sm4.core.baseUrl + 'sitelike/index/likelist';// URL where send ajax request.
 var likedStatus = function(call_status) {
		sm4.core.request.send({
			type: "GET", 
			dataType: "html", 
			url : url,
			data: {
				'format':'html',
				'call_status' : call_status,
				'resource_type' : resource_type,
				'resource_id' : resource_id,
				'is_ajax' : 1
			}
		},{
			'element' : $.mobile.activePage.find("#like_members"),
			'showLoading': true
			}
		);
	};
</script>

<?php //THIS IS USE FOR SHOW FRIENDS WHO LIKE MY CONTENT OR POP UP RESULT SHOW.
 include_once APPLICATION_PATH . '/application/modules/Sitelike/views/sitemobile/scripts/friend_mycontent_likelist.tpl';?>