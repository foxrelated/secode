<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: browse.tpl 10217 2014-05-15 13:41:15Z lucas $
 * @author     Sami
 */
?>

<div class='count_results'>
    <span class="search_icon fa fa-search"></span>
    <span class="num_results"><?php echo $this->translate(array('%s Result', '%s Results', $this->paginator->getTotalItemCount()),$this->paginator->getTotalItemCount())?></span>
    <span class="total_results">(<?php echo $this->total_content?>)</span>
    <span class="label_results"><?php echo $this->htmlLink(array('route' => 'album_general'), $this->label_content, array());?></span>
</div>

  <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>

    <ul class="thumbs">
      <?php foreach( $this->paginator as $album ): ?>
        <li>
          <a class="thumbs_photo" href="<?php echo $album->getHref(); ?>">
            <span style="background-image: url(<?php echo $album->getPhotoUrl('thumb.normal'); ?>);"></span>
          </a>
          <div class="thumbs_info">
            <div class="thumbs_title">
              <?php echo $this->htmlLink($album, $this->string()->chunk($this->string()->truncate($album->getTitle(), 45), 10)) ?>
            </div>
            <div class="author"><?php echo $this->translate('By');?>
            <?php echo $this->htmlLink($album->getOwner()->getHref(), $album->getOwner()->getTitle(), array('class' => 'thumbs_author')) ?>
            </div>
            <div class="count_photo"><?php echo $this->translate(array('%s photo', '%s photos', $album->count()),$this->locale()->toNumber($album->count())) ?></div>
          </div>
        </li>
      <?php endforeach;?>
    </ul>

    <?php if( $this->paginator->count() > 1 ): ?>
      <br />
      <?php echo $this->paginationControl(
        $this->paginator, null, null, array(
          'pageAsQuery' => false,
          'query' => $this->searchParams
          )); ?>
    <?php endif; ?>
  
    <?php elseif( $this->searchParams['category_id'] ): ?>
    <div class="tip">
      <span id="no-album-criteria">
        <?php echo $this->translate('There were no albums found matching your search criteria.');?>
      </span>
    </div>    
    
    <?php else: ?>
    <div class="tip">
      <span id="no-album">
        <?php echo $this->translate('There were no albums found matching your search criteria.');?>
      </span>
    </div>
  <?php endif; ?>