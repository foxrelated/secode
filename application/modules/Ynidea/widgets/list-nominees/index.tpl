    <h3>List Nominee</h3>
    <?php if( count($this->paginator) > 0 ): ?>
      <ul class='ideas_frame ideas_browse'>
        <?php foreach( $this->paginator as $nominee ): ?>
          <div class="ideas_info">
              <div class="ideas_title">
                <h3><?php                           
                          echo $this->htmlLink($nominee->getHref(), $nominee->idea_title);
                    ?>
                </h3>
              </div>  
              <div class="ideas_desc">
                <?php echo Engine_Api::_()->ynidea()->subPhrase(strip_tags($nominee->trophy_score_ave),250); ?>
              </div>            
              <div class="ideas_desc">
                <?php echo Engine_Api::_()->ynidea()->subPhrase(strip_tags($nominee->trophy_title),250); ?>
              </div>
            </div>
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
        <?php echo $this->translate('You have not nominees yet.') ?>        
        </span>
      </div>
    <?php endif; ?>




