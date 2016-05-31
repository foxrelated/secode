<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteadvsearch
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: browse-poll.tpl 2014-08-06 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if (0 == count($this->paginator)): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There are no polls yet.') ?>
      <?php if ($this->canCreate): ?>
        <?php echo $this->translate('Why don\'t you %1$screate one%2$s?', '<a href="' . $this->url(array('action' => 'create'), 'poll_general') . '">', '</a>')
        ?>
      <?php endif; ?>
    </span>
  </div>
<?php else: ?>
  <div id="list_view">
    <ul class="polls_browse">
      <?php foreach ($this->paginator as $poll): ?>
        <li id="poll-item-<?php echo $poll->poll_id ?>">
          <?php
          echo $this->htmlLink(
                  $poll->getHref(), $this->itemPhoto($poll->getOwner(), 'thumb.icon', $poll->getOwner()->getTitle()), array('class' => 'polls_browse_photo')
          )
          ?>
          <div class="polls_browse_info">
            <h3 class="polls_browse_info_title">
              <?php echo $this->htmlLink($poll->getHref(), $poll->getTitle()) ?>
              <?php if ($poll->closed): ?>
                <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Poll/externals/images/close.png' alt="<?php echo $this->translate('Closed') ?>" />
              <?php endif ?>
            </h3>
            <div class="polls_browse_info_date">
              <?php echo $this->translate('Posted by %s', $this->htmlLink($poll->getOwner(), $poll->getOwner()->getTitle())) ?>
              <?php echo $this->timestamp($poll->creation_date) ?>
              -
              <?php echo $this->translate(array('%s vote', '%s votes', $poll->vote_count), $this->locale()->toNumber($poll->vote_count)) ?>
              -
              <?php echo $this->translate(array('%s view', '%s views', $poll->view_count), $this->locale()->toNumber($poll->view_count)) ?>
            </div>
            <?php if (!empty($poll->description)): ?>
              <div class="polls_browse_info_desc">
                <?php echo $poll->description ?>
              </div>
            <?php endif; ?>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; // $this->polls is NOT empty ?>

<?php if ($this->paginator->count() > 1): ?> 
  <div class="clr" id="scroll_bar_height"></div>
  <?php if (empty($this->is_ajax)) : ?>
    <div class = "seaocore_view_more mtop10" id="seaocore_view_more" style="display: none;">
      <?php
      echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
          'id' => '',
          'class' => 'buttonlink icon_viewmore'
      ))
      ?>
    </div>
    <div class="seaocore_view_more" id="loding_image" style="display: none;">
      <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' style='margin-right: 5px;' />
      <?php echo $this->translate("Loading ...") ?>
    </div>
    <div id="hideResponse_div"> </div>
  <?php endif; ?>
<?php endif; ?>
<script>
  var url = en4.core.baseUrl + 'siteadvsearch/index/browse-poll';
  var ulClass = '.polls_browse';
</script>
<?php include APPLICATION_PATH . "/application/modules/Siteadvsearch/views/scripts/viewmoreresuls.tpl"; ?>
