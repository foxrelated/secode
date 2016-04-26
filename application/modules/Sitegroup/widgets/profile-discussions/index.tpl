<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitegroupdiscussion/externals/styles/style_sitegroupdiscussion.css')
?>

<!--<script type="text/javascript">
  var groupDiscussionGroup = <?php //echo sprintf('%d', $this->paginators->getCurrentPageNumber()) ?>;
  var paginateGroupDiscussions = function(group) {
    var url = en4.core.baseUrl + 'widget/index/mod/sitegroupevent/name/profile-discussion';
    en4.core.request.send(new Request.HTML({
      'url' : url,
      'data' : {
        'format' : 'html',
        'subject' : en4.core.subject.guid,
        'group' : group,
        'isajax' : '1',
        'tab' : '<?php //echo $this->content_id ?>'
      }
    }), {
      'element' : $('id_' + <?php //echo $this->content_id ?>)
    });
  }
</script>-->

<?php if ($this->canPost): ?>
  <div class="seaocore_add">
    <?php
    echo $this->htmlLink(array(
        'route' => 'sitegroup_extended',
        'controller' => 'topic',
        'action' => 'create',
        'subject' => $this->sitegroup->getGuid(),
        'group_id' => $this->group_id,
        'resource_type' => $this->subject->getType(),
        'resource_id' => $this->subject->getIdentity(),
            ), $this->translate('Post New Topic'), array(
        'class' => 'buttonlink icon_sitegroup_post_new'
    ))
    ?>
  </div>
<?php endif; ?>
<?php if ($this->paginators->getTotalItemCount() > 0): ?>
  <div class="sitegroup_profile_discussion">
    <ul class="sitegroup_sitegroups">
      <?php
      foreach ($this->paginators as $topic):
        $lastpost = $topic->getLastPost();
        $lastposter = $topic->getLastPoster();
        ?>
        <li>
          <div class="sitegroup_sitegroups_replies">
            <span>
              <?php echo $this->locale()->toNumber($topic->post_count - 1) ?>
            </span>
            <?php echo $this->translate(array('reply', 'replies', $topic->post_count - 1)) ?>
          </div>
          <div class="sitegroup_sitegroups_lastreply">
            <?php echo $this->htmlLink($lastposter->getHref(), $this->itemPhoto($lastposter, 'thumb.icon')) ?>
              <?php if($lastpost):?>
                  <div class="sitegroup_sitegroups_lastreply_info">
              <?php echo $this->htmlLink($lastpost->getHref(), $this->translate('Last Post')) ?> <?php echo $this->translate('by'); ?> <?php echo $lastposter->__toString() ?>
              <br />
              <?php echo $this->timestamp(strtotime($topic->modified_date), array('tag' => 'div', 'class' => 'sitegroup_sitegroups_lastreply_info_date')) ?>
            </div>
              <?php endif;?>
          </div>
          <div class="sitegroup_sitegroups_info">
            <h3<?php if ($topic->sticky): ?> class='sitegroup_sitegroups_sticky'<?php endif; ?>>
              <?php echo $this->htmlLink($topic->getHref(), $topic->getTitle()) ?>
            </h3>
            <div class="sitegroup_sitegroups_blurb">
              <?php echo $this->viewMore(strip_tags($topic->getDescription())) ?>
            </div>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
  <?php if ($this->paginators->count() > 1): ?>
    <div>
      <?php if ($this->paginators->getCurrentPageNumber() > 1): ?>
        <div id="user_group_members_previous" class="paginator_previous">
          <?php
          echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
              'onclick' => 'paginateGroupDiscussions(groupDiscussionGroup - 1)',
              'class' => 'buttonlink icon_previous'
          ));
          ?>
        </div>
      <?php endif; ?>
      <?php if ($this->paginators->getCurrentPageNumber() < $this->paginators->count()): ?>
        <div id="user_group_members_next" class="paginator_next">
          <?php
          echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
              'onclick' => 'paginateGroupDiscussions(groupDiscussionGroup + 1)',
              'class' => 'buttonlink_right icon_next'
          ));
          ?>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>

<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('No discussion topics have been posted in this Group yet.'); ?>
      <?php
      if ($this->canPost):
        $show_link = $this->htmlLink(
                array('route' => 'sitegroup_extended',
            'controller' => 'topic',
            'action' => 'create',
            'subject' => $this->subject()->getGuid(),
            'group_id' => $this->group_id,
            'resource_type' => $this->subject->getType(),
            'resource_id' => $this->subject->getIdentity()
                ), $this->translate('here'));
        $show_label = Zend_Registry::get('Zend_Translate')->_('Click %s to start a discussion.');
        $show_label = sprintf($show_label, $show_link);
        echo $show_label;
      endif;
      ?>
    </span>
  </div>
<?php endif; ?>


