<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */ 
?>
<?php if (count($this->paginator) > 0): ?>

  <div class="ui-member-list-head">
    <?php echo $this->translate(array('%s diary found.', '%s diaries found.', $this->paginator->getTotalItemCount()), $this->locale()->toNumber($this->paginator->getTotalItemCount())) ?>
  </div>

  <div class="sm-content-list">
    <ul class="sr_reviews_listing" data-role="listview" data-icon="arrow-r">
      <?php foreach ($this->paginator as $diary):?>
        <li>
          <a href="<?php echo $diary->getHref() ?>">
            <?php echo $this->itemPhoto($diary->getCoverItem(), 'thumb.icon') ?>
            <h3><?php echo $diary->title ?></h3>
            <?php if (!empty($this->statisticsDiary)): ?>
              <p>
                <?php
                $statistics = '';
                if (in_array('entryCount', $this->statisticsDiary)) {
                                    $statistics .= $this->translate(array('%s event', '%s events', $diary->total_item), $this->locale()->toNumber($diary->total_item)) . ' - ';
                                }

                if (in_array('viewCount', $this->statisticsDiary)) {
                    $statistics .= $this->translate(array('%s view', '%s views', $diary->view_count), $this->locale()->toNumber($diary->view_count)) . ' - ';
                }
                  
                  $statistics = trim($statistics);
                  $statistics = rtrim($statistics, ' - ');

                ?>
                <?php echo $statistics; ?>
              </p>
            <?php endif; ?>
            <p>
              <?php echo $this->translate('%s - created by %s', $this->timestamp($diary->creation_date), "<b>".$diary->getOwner()->getTitle()."</b>") ?>
            </p>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
  <?php echo $this->paginationControl($this->paginator, null, null, array('query' => $this->formValues, 'pageAsQuery' => true)); ?>

<?php elseif ($this->isSearched > 2): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('Nobody has created a diary with that criteria.'); ?>
    </span>
  </div>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('Nobody has created a diary yet.'); ?>
    </span>
  </div>
<?php endif; ?>
