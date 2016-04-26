<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: view.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/List/externals/styles/style_list.css');?>
<div class="list_thread_view">
	<h2>
	  <?php echo $this->list->__toString() ?>
	  <?php echo $this->translate('&#187; Discussions');?>
	</h2>
	
	<br />
	
	<h3>
	  <?php echo $this->topic->getTitle() ?>
	</h3>
	
	<?php $this->placeholder('listtopicnavi')->captureStart(); ?>
	
	<div class="list_lists_thread_options">
	  <?php echo $this->htmlLink(array('route' => 'list_extended', 'controller' => 'topic', 'action' => 'index', 'listing_id' => $this->list->getIdentity()), $this->translate('Back to Topics'), array(
	    'class' => 'buttonlink icon_back'
	  )) ?>
	
	  <?php if( $this->canPost ): ?>
	    <?php echo $this->htmlLink($this->url(array()) . '#reply', $this->translate('Post Reply'), array(
	      'class' => 'buttonlink icon_list_post_reply'
	    )) ?>
	  <?php endif; ?>
	
	  <?php if( $this->viewer->getIdentity() ): ?>
	    <?php if( !$this->isWatching ): ?>
	      <?php echo $this->htmlLink($this->url(array('action' => 'watch', 'watch' => '1')), $this->translate('Watch Topic'), array(
	        'class' => 'buttonlink icon_list_topic_watch'
	      )) ?>
	    <?php else: ?>
	      <?php echo $this->htmlLink($this->url(array('action' => 'watch', 'watch' => '0')), $this->translate('Stop Watching Topic'), array(
	        'class' => 'buttonlink icon_list_topic_unwatch'
	      )) ?>
	    <?php endif; ?>
	  <?php endif; ?>
	
	  <?php if( $this->list->isOwner($this->viewer()) ): ?>
	    <?php if( !$this->topic->sticky ): ?>
	      <?php echo $this->htmlLink(array('action' => 'sticky', 'sticky' => '1', 'reset' => false), $this->translate('Make Sticky'), array(
	        'class' => 'buttonlink icon_list_post_stick'
	      )) ?>
	    <?php else: ?>
	      <?php echo $this->htmlLink(array('action' => 'sticky', 'sticky' => '0', 'reset' => false), $this->translate('Remove Sticky'), array(
	        'class' => 'buttonlink icon_list_post_unstick'
	      )) ?>
	    <?php endif; ?>
	
	    <?php if( !$this->topic->closed ): ?>
	      <?php echo $this->htmlLink(array('action' => 'close', 'close' => '1', 'reset' => false), $this->translate('Close'), array(
	        'class' => 'buttonlink icon_list_post_close'
	      )) ?>
	    <?php else: ?>
	      <?php echo $this->htmlLink(array('action' => 'close', 'close' => '0', 'reset' => false), $this->translate('Open'), array(
	        'class' => 'buttonlink icon_list_post_open'
	      )) ?>
	    <?php endif; ?>
	
	    <?php echo $this->htmlLink(array('action' => 'rename', 'reset' => false), $this->translate('Rename'), array(
	      'class' => 'buttonlink smoothbox icon_list_post_rename'
	    )) ?>
	    <?php echo $this->htmlLink(array('action' => 'delete', 'reset' => false), $this->translate('Delete'), array(
	      'class' => 'buttonlink smoothbox icon_list_post_delete'
	    )) ?>
	  <?php elseif( $this->list->isOwner($this->viewer()) == false): ?>
	    <?php if( $this->topic->closed ): ?>
	      <div class="list_lists_thread_options_closed seaocore_txt_light">
	        <?php echo $this->translate('This topic has been closed.');?>
	      </div>
	    <?php endif; ?>
	  <?php endif; ?>
	</div>
	<?php $this->placeholder('listtopicnavi')->captureEnd(); ?>
	
	<?php echo $this->placeholder('listtopicnavi') ?>
	<?php echo $this->paginationControl(null, null, null, array(
	  'params' => array(
	    'post_id' => null
	  )
	)) ?>
	
	<script type="text/javascript">
	  var quotePost = function(user, href, body) {
	    if( $type(body) == 'element' ) {
	      body = $(body).getParent('li').getElement('.list_lists_thread_body_raw').get('html').trim();
	    }
	
	    var tinyMCEEditor = '<?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('list.tinymceditor', 1);?>';
	    if(tinyMCEEditor == 1) {
				tinyMCE.activeEditor.setContent('[blockquote]' + '[b][url=' + href + ']' + user + '[/url] said:[/b]\n' + htmlspecialchars_decode(body) + '[/blockquote]\n\n'); 
	    } else {
	       $('body').value = '[blockquote]' + '[b][url=' + href + ']' + user + '[/url] said:[/b]\n' + htmlspecialchars_decode(body) + '[/blockquote]\n\n';
	    }
	    $("body").focus();
	    $("body").scrollTo(0, $("body").getScrollSize().y);
	  }
	</script>
	
	<ul class='list_lists_thread'>
	  <?php foreach( $this->paginator as $post ): ?>
			<li>
				<div class="list_lists_thread_photo">
					<?php
						$user = $this->item('user', $post->user_id);
						echo $this->htmlLink($user->getHref(), $user->getTitle());
						echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'));
					?>
				</div>
				<div class="list_lists_thread_info">
					<div class="list_lists_thread_details">
						<div class="list_lists_thread_details_options">
							<?php if( $this->form ): ?>
								<?php echo $this->htmlLink('javascript:void(0);', $this->translate('Quote'), array(
									'class' => 'buttonlink icon_list_post_quote',
									'onclick' => 'quotePost("'.$this->escape($user->getTitle()).'", "'.$this->escape($user->getHref()).'", this);',
								)) ?>
							<?php endif; ?>
							<?php if( $post->user_id == $this->viewer()->getIdentity() || $this->list->getOwner()->getIdentity() == $this->viewer()->getIdentity() ): ?>
								<?php echo $this->htmlLink(array('route' => 'list_extended', 'controller' => 'post', 'action' => 'edit', 'post_id' => $post->getIdentity(), 'format' => 'smoothbox'), $this->translate('Edit'), array(
									'class' => 'buttonlink smoothbox icon_list_post_edit'
								)) ?>
								<?php echo $this->htmlLink(array('route' => 'list_extended', 'controller' => 'post', 'action' => 'delete', 'post_id' => $post->getIdentity(), 'format' => 'smoothbox'), $this->translate('Delete'), array(
									'class' => 'buttonlink smoothbox icon_list_post_delete'
								)) ?>
							<?php endif; ?>
						</div>
						<div class="list_lists_thread_details_date seaocore_txt_light">
							<?php echo $this->translate('Posted');?> <?php echo $this->timestamp(strtotime($post->creation_date)) ?>
						</div>
					</div>
					<div class="list_lists_thread_body">
						<?php echo nl2br($this->BBCode($post->body)) ?>
					</div>
					<span class="list_lists_thread_body_raw" style="display: none;">
						<?php echo $post->body; ?>
					</span>
				</div>
			</li>
	  <?php endforeach; ?>
	</ul>
	
	<?php if($this->paginator->getCurrentItemCount() > 4): ?>
	  <?php echo $this->paginationControl(null, null, null, array(
	    'params' => array(
	      'post_id' => null
	    )
	  )) ?>
	  <br />
	  <?php echo $this->placeholder('listtopicnavi') ?>
	<?php endif; ?>
	
	<br />
	
	<?php if($this->form): ?>
	  <a name="reply" />
			<?php echo $this->form->setAttrib('id', 'list_topic_reply')->render($this) ?>
		</a>
	<?php endif; ?>
</div>