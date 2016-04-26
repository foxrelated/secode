    <h3><?php echo $this->translate("List Of Trophies") ?></h3>
    <?php if( count($this->paginator) > 0 ): ?>
      <ul class='ideas_frame ideas_browse'>
        <?php foreach( $this->paginator as $trophy ): ?>
          <li>
            <div class="ideas_photo">
              <?php echo $this->htmlLink($trophy->getHref(), $this->itemPhoto($trophy, 'thumb.normal')) ?>
            </div> 
            <div class="ideas_options">
              		<div><?php echo $this->timestamp($trophy->modified_date); ?></div>
              		<div id="trophy_status">
						<span><?php echo $this->translate("Status");?>:</span>
						<span style="font-weight: bold; text-transform: capitalize;">
							<?php echo $this->translate($trophy->status);?>
						</span>
					</div>
              		<div><?php echo $this->translate("Nominees: %s", $trophy->getNominees());?></div>
              		<div><?php echo $this->translate("Judges: %s", $trophy->getJudges());?></div>
            </div>
            <div class="ideas_info">
              <div class="ideas_title">
                <div class="ideas_photo">
                <?php $idea_title = Engine_Api::_()->ynidea()->subPhrase($trophy->getTitle(),100);?>
                <?php echo $this->htmlLink($trophy->getHref(), $trophy->getTitle());?>
                -
                <?php echo $trophy->getOwner();?>
                </div>
              </div>              
              <div class="ideas_desc">
                <?php echo Engine_Api::_()->ynidea()->subPhrase(strip_tags($trophy->description),500); ?>
                
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
        <?php echo $this->translate('You have not trophies yet.') ?>        
        </span>
      </div>
    <?php endif; ?>




