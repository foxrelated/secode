<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: view.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */ 
?>
<?php 
	$breadcrumb = array(
   array("href"=>$this->sitegroup->getHref(),"title"=>$this->sitegroup->getTitle(),"icon"=>"arrow-r"),
   array("href"=>$this->sitegroup->getHref(array('tab' => $this->tab_selected_id)),"title"=>"Discussions","icon"=>"arrow-r"),
   array("title"=>$this->topic->getTitle(),"icon"=>"arrow-d","class" => "ui-btn-active ui-state-persist"));
  
	echo $this->breadcrumb($breadcrumb);
?>

<div class="sitegroup_discussion_view">
	<?php $this->placeholder('sitegrouptopicnavi')->captureStart(); ?>
	<?php $this->placeholder('sitegrouptopicnavi')->captureEnd(); ?>
	<?php echo $this->placeholder('sitegrouptopicnavi') ?>
	<?php echo $this->paginationControl(null, null, null, array('params' => array('post_id' => null)))?>
  <ul data-inset="true" data-role="listview" class="ui-listview ui-listview-inset ui-corner-all ui-shadow sm-ui-topic-view">
		<?php foreach ($this->paginator as $post): $liClass = 'group_discussions_thread_author_none'; ?>
      <li class="<?php echo $liClass ?> ui-li-has-count">
				<div class="author_photo">
					<?php
					$user = $this->item('user', $post->user_id);
					echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon')) ?>
				</div>
       <div class="sitegroup_sitegroups_thread_info">
				<div class="sitegroup_sitegroups_thread_details_options">
					<div class="thread_options" data-role="controlgroup" data-type="horizontal" data-mini="true">
						<?php if ($post->user_id == $this->viewer()->getIdentity() || $this->canEdit == 1 || $this->topic->user_id == $this->viewer()->getIdentity()): ?>
							<?php
								echo $this->htmlLink(array('route' => 'sitegroup_extended', 'controller' => 'post', 'action' => 'edit', 'post_id' => $post->getIdentity(), 'format' => 'smoothbox'), $this->translate('Edit'), array(
						'data-role'=>"button", 'data-icon'=>"edit",'data-iconpos'=>'notext'
							))?>
							<?php
							echo $this->htmlLink(array('route' => 'sitegroup_extended', 'controller' => 'post', 'action' => 'delete', 'post_id' => $post->getIdentity(), 'format' => 'smoothbox'), $this->translate('Delete'), array(
									'data-role'=>"button", 'data-icon'=>"delete",'data-iconpos'=>'notext'
							))
							?>
						<?php endif; ?>
					</div>
				</div>
				<div class="sitegroup_sitegroups_thread_details">
					<h3 class="ui-li-heading"><?php echo $this->htmlLink($user->getHref(), $user->getTitle()) ?></h3>
          <div class="sitegroup_sitegroups_thread_details_date">
						<p class="ui-li-desc"><?php echo $this->timestamp(strtotime($post->creation_date)) ?></p>
          </div>
				</div>
				<div class="sitegroup_sitegroups_thread_body">
          <?php
            $body = $post->body;
            $doNl2br = false;
            if( strip_tags($body) == $body ) {
              $body = nl2br($body);
            }
            if( !$this->decode_html && $this->decode_bbcode ) {
              $body = $this->BBCode($body, array('link_no_preparse' => true));
            }
            echo $body;
          ?>
	      </div>
	      <span class="sitegroup_sitegroups_thread_body_raw" style="display: none;">
          <?php
            $body = $post->body;
            $doNl2br = false;
            if( strip_tags($body) == $body ) {
              $body = nl2br($body);
            }
            if( !$this->decode_html && $this->decode_bbcode ) {
              $body = $this->BBCode($body, array('link_no_preparse' => true));
            }
            echo $body;
          ?>
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
  <?php echo $this->placeholder('sitegrouptopicnavi') ?>
<?php endif; ?>
<?php if ($this->form): ?>
    <a name="reply"> </a>
  <?php echo $this->form->setAttrib('id', 'sitegroup_topic_reply')->render($this) ?>
<?php endif; ?>
</div>