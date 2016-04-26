    <?php if( count($this->paginator) > 0 ): ?>
      <ul class='ideas_browse ideas_list_tab'>
        <?php foreach( $this->paginator as $idea ): ?>
          <li>
	        <div class="ideas_photo">
	          <?php echo $this->htmlLink($idea->getHref(), $this->itemPhoto($idea, 'thumb.icon')) ?>
	        </div> 
	        <div class="ideas_info">
	          <div class="ideas_title">
	            <div class="ideas_photo">
	            <?php $idea_title = Engine_Api::_()->ynidea()->subPhrase($idea->getTitle(),60);?>
	            <?php echo $this->htmlLink($idea->getHref(), $idea_title);?>
	            -
	            <?php echo $idea->getOwner();?>
	            </div>
	            <div class="ideas_options">
	          		<?php echo $this->timestamp(strtotime($idea->modified_date)); ?>
	        	</div>
	          </div>              
	          <div class="ideas_desc">
	            <?php echo Engine_Api::_()->ynidea()->subPhrase(strip_tags($idea->description),100); ?>
	          </div>
	        </div>
      </li>
        <?php endforeach; ?>
      </ul>
      <?php if( count($this->paginator) > 1 ): ?>
        <?php echo $this->paginationControl($this->paginator, null, null, array(
            'pageAsQuery' => true,
            'query' => $this->formValues,
          )); ?>
      <?php endif; ?>

    <?php else: ?>
      <div class="tip">
        <span>
        <?php echo $this->translate('Nobody has written an idea with that criteria.') ?>
        </span>
      </div>
    <?php endif; ?>




