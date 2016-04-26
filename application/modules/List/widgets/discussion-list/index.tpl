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

<?php if( $this->canPost || $this->paginator->count() > 1 ): ?>
  <div class="seaocore_add">
    <?php if( $this->canPost ): ?>
      <?php echo $this->htmlLink(array(
        'route' => 'list_extended',
        'controller' => 'topic',
        'action' => 'create',
        'subject' => $this->subject()->getGuid(),
      ), $this->translate('Post New Topic'), array(
        'class' => 'buttonlink icon_list_post_new'
      )) ?>
    <?php endif;?>
    <?php if( $this->paginator->count() > 1 ): ?>
      <?php echo $this->htmlLink(array(
        'route' => 'list_extended',
        'controller' => 'topic',
        'action' => 'index',
        'subject' => $this->subject()->getGuid(),
      ), $this->translate('View All ').$this->paginator->getTotalItemCount().' Topics', array(
        'class' => 'buttonlink icon_viewmore'
      )) ?>
    <?php  endif; ?>
  </div>
<?php endif; ?>

<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
  <div class="list_lists_list">
    <ul class="list_lists">
      <?php foreach( $this->paginator as $topic ):
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
  </div>
<?php else: ?>
  <div class="tip">
    <span>
			<?php if($this->canPost):
					$show_link = $this->htmlLink(array('route' => 'list_extended', 'controller' => 'topic', 'action' => 'create','subject' => $this->subject()->getGuid()),$this->translate('here'));
					$show_label = Zend_Registry::get('Zend_Translate')->_('No discussion topics have been posted in this listing yet. Click %s to start a discussion.');
					$show_label = sprintf($show_label, $show_link);
					echo $show_label;
			endif;?>
    </span>
  </div>
<?php endif; ?>