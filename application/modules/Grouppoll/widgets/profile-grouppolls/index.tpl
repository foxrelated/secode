<?php 
 /**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Grouppoll
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2010-12-08 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php if($this->can_create): ?>
	<div class="seaocore_add">
	  <a href='<?php echo $this->url(array('group_id' => $this->group_id), 'grouppoll_create', true) ?>' class='buttonlink icon_grouppoll_new'><?php echo $this->translate('Create Poll');?></a>
	</div>
<?php endif; ?>

<a id="group_profile_polls_anchor" class="pabsolute"></a>

<script type="text/javascript">

  var groupPollSearchText = '<?php echo $this->search ?>';
  var groupPollPage = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()) ?>;
  en4.core.runonce.add(function() {
    var url = en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>;
    $('group_polls_search_input_text').addEvent('keypress', function(e) {
      if( e.key != 'enter' ) return;
      if($('group_polls_search_input_checkbox') && $('group_polls_search_input_checkbox').checked == true) {
				var checkbox_value = 1;
			}
			else {
				var checkbox_value = 0;
			}
			 if($('grouppoll_search') != null) {
				$('grouppoll_search').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Grouppoll/externals/images/spinner_temp.gif" /></center>'; 
			 }
        en4.core.request.send(new Request.HTML({
        'url' : url,
        'data' : {
          'format' : 'html',
          'subject' : en4.core.subject.guid,
          'search' : $('group_polls_search_input_text').value,
					'selectbox' : $('group_polls_search_input_selectbox').value,
					'checkbox' : checkbox_value,
        }
      }), {
       'element' : $('group_profile_polls_anchor').getParent()
      });
    });
  });

	function Orderselect()
  {
		var groupPollSearchSelectbox = '<?php echo $this->selectbox ?>';
		var groupPollPage = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()) ?>;
		var url = en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>;
		if($('group_polls_search_input_checkbox') && $('group_polls_search_input_checkbox').checked == true) {
			var checkbox_value = 1;
		}
		else {
			var checkbox_value = 0;
		}
		 if($('grouppoll_search') != null) {
			$('grouppoll_search').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Grouppoll/externals/images/spinner_temp.gif" /></center>'; 
		 } 
		en4.core.request.send(new Request.HTML({
			'url' : url,
      'data' : {
				'format' : 'html',
				'subject' : en4.core.subject.guid,
				 'search' : $('group_polls_search_input_text').value,
				 'selectbox' : $('group_polls_search_input_selectbox').value,
				 'checkbox' : checkbox_value,
       }
    }), {
					'element' : $('group_profile_polls_anchor').getParent()
				});
	}

	function Mypoll() {
		var groupPollSearchCheckbox = '<?php echo $this->checkbox ?>';
		var groupPollPage = <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber()) ?>;
		var url = en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>;
		if($('group_polls_search_input_checkbox') && $('group_polls_search_input_checkbox').checked == true) {
			var checkbox_value = 1;
		}
		else {
			var checkbox_value = 0;
		}
		 if($('grouppoll_search') != null) {
				$('grouppoll_search').innerHTML = '<center><img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Grouppoll/externals/images/spinner_temp.gif" /></center>'; 
		 }
				en4.core.request.send(new Request.HTML({
				'url' : url,
				'data' : {
					'format' : 'html',
					'subject' : en4.core.subject.guid,
					'search' : $('group_polls_search_input_text').value,
					'selectbox' : $('group_polls_search_input_selectbox').value,
					'checkbox' : checkbox_value,
				} 
			}), {
				'element' : $('group_profile_polls_anchor').getParent()
			});
	}

  var paginateGroupPolls = function(page) {
    var url = en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>;
		if($('group_polls_search_input_checkbox') && $('group_polls_search_input_checkbox').checked == true) {
			var checkbox_value = 1;
		}
		else {
			var checkbox_value = 0;
		}
    en4.core.request.send(new Request.HTML({
      'url' : url,
      'data' : {
        'format' : 'html',
        'subject' : en4.core.subject.guid,
        'search' : groupPollSearchText,
				'selectbox' : $('group_polls_search_input_selectbox').value,
				'checkbox' : checkbox_value,
        'page' : page
      }
    }), {
      'element' : $('group_profile_polls_anchor').getParent()
    });
  }

	var paginateGroupPolls = function(page) {
    var url = en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>;
		if($('group_polls_search_input_checkbox') && $('group_polls_search_input_checkbox').checked == true) {
			var checkbox_value = 1;
		}
		else {
			var checkbox_value = 0;
		}
    en4.core.request.send(new Request.HTML({
      'url' : url,
      'data' : {
        'format' : 'html',
        'subject' : en4.core.subject.guid,
        'search' : groupPollSearchText,
				'selectbox' : $('group_polls_search_input_selectbox').value,
				'checkbox' : checkbox_value,
        'page' : page
      }
    }), {
      'element' : $('group_profile_polls_anchor').getParent()
    });
  }
</script>

<?php if( $this->paginator->count() <= 0 && (empty($this->search) && empty($this->checkbox) && empty($this->selectbox))): ?>
	<div class="grouppoll_filter_widget" style=display:none;>
<?php else: ?>
	<div class="grouppoll_filter_widget">
<?php endif; ?>
	<?php if(!empty($this->viewer_id)):?>
		<div class="grouppoll_filter_search_first">
			<?php if($this->checkbox != 1): ?>
				<input id="group_polls_search_input_checkbox" type="checkbox" value="1" onclick="Mypoll();" ><?php echo $this->translate("Show my polls");?>
			<?php else: ?>
				<input id="group_polls_search_input_checkbox" type="checkbox" value="2"  checked = "checked" onclick="Mypoll();" ><?php echo $this->translate("Show my polls");?>
			<?php endif; ?>
		</div>
	<?php endif;?>
	<div class="grouppoll_filter_search_field">
		<?php echo $this->translate("Search:");?>
		<input id="group_polls_search_input_text" type="text" value="<?php echo $this->search; ?>" >
	</div>
	<div class="grouppoll_filter_search_field">
		<?php echo $this->translate('Browse by:');?>
		<select name="default_visibility" id="group_polls_search_input_selectbox" onchange = "Orderselect()">
			<?php if($this->selectbox == 'creation_date'): ?>
				<option value="creation_date" selected='selected'><?php echo $this->translate("Most Recent"); ?></option>
			<?php else:?>
				<option value="creation_date"><?php echo $this->translate("Most Recent"); ?></option>
			<?php endif;?>
			<?php if($this->selectbox == 'comment_count'): ?>
				<option value="comment_count" selected='selected'><?php echo $this->translate("Most Commented"); ?></option>
			<?php else:?>
				<option value="comment_count"><?php echo $this->translate("Most Commented"); ?></option>
			<?php endif;?>
			<?php if($this->selectbox == 'vote_count'): ?>
				<option value="vote_count" selected='selected'><?php echo $this->translate("Most Voted"); ?></option>
			<?php else:?>
				<option value="vote_count"><?php echo $this->translate("Most Voted"); ?></option>
			<?php endif;?>
			<?php if($this->selectbox == 'views'): ?>
				<option value="views" selected='selected'><?php echo $this->translate("Most Viewed"); ?></option>
			<?php else:?>
				<option value="views"><?php echo $this->translate("Most Viewed"); ?></option>
			<?php endif;?>
		</select>
	</div>
</div>
<?php if( count($this->paginator) > 0 ): ?>
  <ul class="seaocore_profile_list" id='grouppoll_search'>
    <?php foreach ($this->paginator as $grouppoll): ?>
    <?php if($grouppoll->owner_id != $this->viewer_id): ?>
    	<li id="grouppoll-item-<?php echo $grouppoll->poll_id ?>">
    <?php else: ?>
    	<li id="grouppoll-item-<?php echo $grouppoll->poll_id ?>" class="grouppoll_owner">
    <?php endif; ?>
      <?php echo $this->htmlLink(
        $grouppoll->getHref(),
        $this->itemPhoto($grouppoll->getOwner(), 'thumb.icon', $grouppoll->getOwner()->getTitle()),
        array('class' => 'grouppolls_profile_photo', 'title' => $grouppoll->title)
      ) ?>
			<div class="seaocore_profile_list_options">
        <?php echo $this->htmlLink($grouppoll->getHref(), $this->translate('View poll'), array('class' => 'buttonlink icon_grouppoll_viewall')) ?>
				<?php if($grouppoll->owner_id == $this->viewer_id || $grouppoll->group_owner_id == $this->viewer_id): ?>
					<?php echo $this->htmlLink(array('route' => 'grouppoll_delete', 'poll_id' => $grouppoll->poll_id, 'group_id' => $grouppoll->group_id), $this->translate('Delete poll'), array(
			'class'=>'buttonlink icon_grouppoll_delete')) ?>
          <?php if( !$grouppoll->closed ): ?>
						<?php echo $this->htmlLink(array(
							'route' => 'grouppoll_specific',
							'poll_id' => $grouppoll->poll_id,
							'group_id' => $grouppoll->group_id,
							'tab'=>$this->identity_temp,
							'closed' => 1,
						), $this->translate('Close Poll'), array(
							'class' => 'buttonlink icon_grouppoll_close'
						)) ?>
					<?php else: ?>
						<?php echo $this->htmlLink(array(
							'route' => 'grouppoll_specific',
							'poll_id' => $grouppoll->poll_id,
							'group_id' => $grouppoll->group_id,
							'tab'=>$this->identity_temp,
							'closed' => 0,
						), $this->translate('Open Poll'), array(
							'class' => 'buttonlink icon_grouppoll_open'
						)) ?>
					<?php endif; ?>
				<?php endif; ?>
			</div>
      <div class="seaocore_profile_list_info">
        <div class="seaocore_profile_list_title">
					<?php if($grouppoll->approved != 1): ?>
						<span>
							<?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Grouppoll/externals/images/grouppoll_approved0.gif', '', array('class' => 'icon', 'title' => $this->translate('Not Approved'))) ?>
						</span>	
					<?php endif;?>
					<?php if( $grouppoll->closed ): ?>
						<span>
							<img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Grouppoll/externals/images/close.png' alt="<?php echo $this->translate('Closed') ?>" />
						</span>
					<?php endif ?>
          <p><?php echo $this->htmlLink($grouppoll->getHref(), $grouppoll->title) ?></p>
        </div>
        <div class="seaocore_profile_info_date">
          <?php echo $this->translate('Created by %s', $this->htmlLink($grouppoll->getOwner(), $grouppoll->getOwner()->getTitle())) ?>
          <?php echo $this->timestamp($grouppoll->creation_date) ?>
          -
          <?php echo $this->translate(array('%s vote', '%s votes', $grouppoll->vote_count), $this->locale()->toNumber($grouppoll->vote_count)) ?>
          -
          <?php echo $this->translate(array('%s view', '%s views', $grouppoll->views), $this->locale()->toNumber($grouppoll->views)) ?>
					-
          <?php echo $this->translate(array('%s comment', '%s comments', $grouppoll->comment_count), $this->locale()->toNumber($grouppoll->comment_count)) ?>
        </div>
        <?php if (!empty($grouppoll->description)): ?>
          <div class="seaocore_profile_info_blurb">
                  <?php $grouppoll_description = strip_tags($grouppoll->description);
												$grouppoll_description = Engine_String::strlen($grouppoll_description) > 270 ? Engine_String::substr($grouppoll_description, 0, 270) . '..' : $grouppoll_description;
															?>
            <?php  echo $grouppoll_description ?>
          </div>
        <?php endif; ?>
      </div>
    </li>
    <?php endforeach; ?>
  </ul>  
	<?php if( $this->paginator->count() > 1 ): ?>
	  <div class="seaocore_pagination">
	    <?php if( $this->paginator->getCurrentPageNumber() > 1 ): ?>
	      <div id="user_group_members_previous" class="paginator_previous">
	        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
	          'onclick' => 'paginateGroupPolls(groupPollPage - 1)',
	          'class' => 'buttonlink icon_previous'
	        )); ?>
	      </div>
	    <?php endif; ?>
	    <?php if( $this->paginator->getCurrentPageNumber() < $this->paginator->count() ): ?>
	      <div id="user_group_members_next" class="paginator_next">
	        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Next') , array(
	          'onclick' => 'paginateGroupPolls(groupPollPage + 1)',
	          'class' => 'buttonlink_right icon_next'
	        )); ?>
	      </div>
	    <?php endif; ?>
	  </div>
	<?php endif; ?>

<?php elseif($this->paginator->count() <= 0 && ($this->search != '' || $this->checkbox == 1 || $this->selectbox == 'views' ||  $this->selectbox == 'comment_count' || $this->selectbox == 'featured' || $this->selectbox == 'rating')):?>	
	<div class="tip" id='grouppoll_search'>
		<span>
			<?php echo $this->translate('No polls were found matching your search criteria.');?>
		</span>
	</div>
<?php else: ?>
  <div class="tip" id='grouppoll_search'>
		<span>
			<?php echo $this->translate('No polls have been created in this group yet.'); ?>
			<?php if ($this->can_create):  ?>
				<?php echo $this->translate('Be the first to %1$screate%2$s one!', '<a href="'.$this->url(array('group_id' => $this->group_id), 'grouppoll_create').'">', '</a>'); ?>
			<?php endif; ?>
		</span>
	</div>	
<?php endif;?>

