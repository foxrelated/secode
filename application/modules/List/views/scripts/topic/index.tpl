<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: index.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>

<?php $this->headLink()->prependStylesheet($this->layout()->staticBaseUrl . 'application/modules/List/externals/styles/style_list.css');?>

<h2>
  <?php echo $this->list->__toString() ?>
  <?php echo $this->translate('&#187; Discussions');?>
</h2>

<div class="list_lists_options">

 <?php echo $this->htmlLink($this->list->getHref(), $this->translate('Back to Listing'), array('class' => 'buttonlink icon_back')) ?>
	<?php 
    if ($this->can_post) 
    {
			echo $this->htmlLink(array('route' => 'list_extended', 'controller' => 'topic', 'action' => 'create', 'subject' => $this->list->getGuid()), $this->translate('Post New Topic'), array('class' => 'buttonlink icon_list_post_new')) ;
		}
	?>
</div>

<?php if( $this->paginator->count() > 1 ): ?>
  <div>
    <br />
    <?php echo $this->paginationControl($this->paginator) ?>
    <br />
  </div>
<?php endif; ?>

<ul class="list_lists">
  <?php foreach( $this->paginator as $topic ): ?>
		<?php 
				$lastpost = $topic->getLastPost();
				$lastposter = Engine_Api::_()->getItem('user', $topic->lastposter_id);
		?>
		<li>

      <div class="list_lists_replies seaocore_txt_light">
        <span>
          <?php echo $this->locale()->toNumber($topic->post_count - 1) ?>
        </span>
        <?php echo $this->translate(array('reply', 'replies', $topic->post_count - 1)) ?>
      </div>

      <div class="list_lists_lastreply">
        <?php echo $this->htmlLink($lastposter->getHref(), $this->itemPhoto($lastposter, 'thumb.icon')) ?>
        <div class="list_lists_lastreply_info">
          <?php echo $this->htmlLink($lastpost->getHref(), $this->translate('Last Post')) ?> <?php echo $this->translate('by');?> <?php echo $lastposter->__toString() ?>
          <br />
          <?php echo $this->timestamp(strtotime($topic->modified_date), array('tag' => 'div', 'class' => 'list_lists_lastreply_info_date seaocore_txt_light')) ?>
        </div>
      </div>

      <div class="list_lists_info">
        <h3<?php if( $topic->sticky ): ?> class='list_lists_sticky'<?php endif; ?>>
          <?php echo $this->htmlLink($topic->getHref(), $topic->getTitle()) ?>
        </h3>
        <div class="list_lists_blurb">
          <?php echo $this->viewMore(strip_tags($topic->getDescription())) ?>
        </div>
      </div>

    </li>
  <?php endforeach; ?>
</ul>

<?php if( $this->paginator->count() > 1 ): ?>
  <div>
    <?php echo $this->paginationControl($this->paginator) ?>
  </div>
<?php endif; ?>