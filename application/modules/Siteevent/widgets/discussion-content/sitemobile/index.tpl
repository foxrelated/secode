<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
 ?>
<?php  
	$breadcrumb = array(
   array("href"=>$this->siteevent->getHref(),"title"=>$this->siteevent->getTitle(),"icon"=>"arrow-r"),
   array("href"=>$this->siteevent->getHref(array('tab' => $this->tab_selected_id)),"title"=>"Discussions","icon"=>"arrow-r"),
   array("title"=>$this->topic->getTitle(),"icon"=>"arrow-d","class" => "ui-btn-active ui-state-persist"));
  
	echo $this->breadcrumb($breadcrumb);
?>

<div class="siteevent_discussion_view">
	<?php $this->placeholder('siteeventtopicnavi')->captureStart(); ?>
	<?php $this->placeholder('siteeventtopicnavi')->captureEnd(); ?>
	<?php echo $this->placeholder('siteeventtopicnavi') ?>
	<?php echo $this->paginationControl(null, null, null, array('params' => array('post_id' => null)))?>
  <ul data-inset="true" data-role="listview" class="ui-listview ui-listview-inset ui-corner-all ui-shadow sm-ui-topic-view">
		<?php foreach ($this->paginator as $post): $liClass = 'group_discussions_thread_author_none'; ?>
      <li class="<?php echo $liClass ?> ui-li-has-count">
				<div class="author_photo">
					<?php
					$user = $this->item('user', $post->user_id);
					echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon')) ?>
				</div>
       <div class="siteevent_siteevents_thread_info">
				<div class="siteevent_siteevents_thread_details_options">
					<div class="thread_options" data-role="controlgroup" data-type="horizontal" data-mini="true">
						<?php if ($post->user_id == $this->viewer()->getIdentity() || $this->canEdit == 1 || $this->topic->user_id == $this->viewer()->getIdentity()): ?>
							<?php
								echo $this->htmlLink(array('route' => 'siteevent_extended', 'controller' => 'post', 'action' => 'edit', 'post_id' => $post->getIdentity(), 'format' => 'smoothbox'), $this->translate('Edit'), array(
						'data-role'=>"button", 'data-icon'=>"edit",'data-iconpos'=>'notext'
							))?>
							<?php
							echo $this->htmlLink(array('route' => 'siteevent_extended', 'controller' => 'post', 'action' => 'delete', 'post_id' => $post->getIdentity(), 'format' => 'smoothbox'), $this->translate('Delete'), array(
									'data-role'=>"button", 'data-icon'=>"delete",'data-iconpos'=>'notext'
							))
							?>
						<?php endif; ?>
					</div>
				</div>
				<div class="siteevent_siteevents_thread_details">
					<h3 class="ui-li-heading"><?php echo $this->htmlLink($user->getHref(), $user->getTitle()) ?></h3>
          <div class="siteevent_siteevents_thread_details_date">
						<p class="ui-li-desc"><?php echo $this->timestamp(strtotime($post->creation_date)) ?></p>
          </div>
				</div>
				<div class="siteevent_siteevents_thread_body">
					<?php echo $this->viewMore(nl2br($this->BBCode($post->body))) ?>
				</div>
				<span class="siteevent_siteevents_thread_body_raw" style="display: none;">
						<?php echo $this->viewMore(strip_tags($post->body),10); ?>
				</span>
			</div>
		</li>
		<?php endforeach; ?>
	</ul>
<?php if ($this->paginator->getCurrentItemCount() > 4): ?>
  <?php
  echo $this->paginationControl(null, null, null, array(
      'params' => array(
          'post_id' => null // Remove post id
      )
  ))
  ?>
  <br />
  <?php echo $this->placeholder('siteeventtopicnavi') ?>
<?php endif; ?>
<?php if ($this->form): ?>
    <a name="reply"> </a>
  <?php echo $this->form->setAttrib('id', 'siteevent_topic_reply')->render($this) ?>
<?php endif; ?>
</div>