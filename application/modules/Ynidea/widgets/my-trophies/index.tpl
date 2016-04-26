
    <?php if( count($this->paginator) > 0 ): ?>
      <ul class='ideas_frame ideas_browse'>
        <?php foreach( $this->paginator as $trophy ): ?>
          <li>
            <div class="ideas_photo">
              <?php echo $this->htmlLink($trophy->getHref(), $this->itemPhoto($trophy, 'thumb.normal')) ?>
            </div>
            
            <div class="ideas_options">
              <?php if( $trophy->isOwner($this->viewer()) ): ?>
              	<div>
                <?php echo $this->htmlLink(array('route' => 'ynidea_trophies', 'action' => 'edit', 'id' => $trophy->getIdentity()), $this->translate('Edit Trophy'), array(
                  'class' => 'buttonlink icon_idea_edit'
                )) ?>
                </div>
                <div>
                <?php echo $this->htmlLink(array('route' => 'ynidea_trophies', 'action' => 'delete', 'id' => $trophy->getIdentity(), 'format' => 'smoothbox'), $this->translate('Delete Trophy'), array(
                          'class' => 'buttonlink smoothbox icon_idea_delete'
                        ));
                        
                 
                ?>              
               </div>
              <?php endif; ?>
            </div>
            
           
            <div class="ideas_info">
              <div class="ideas_title">
                <h3><?php $trophy_name = Engine_Api::_()->ynidea()->subPhrase($trophy->getTitle(),50);
                          echo $this->htmlLink($trophy->getHref(),$this->translate($trophy->getTitle()));
                    ?></h3>
              </div>              
              <div class="ideas_desc">
                <?php echo wordwrap(Engine_Api::_()->ynidea()->subPhrase(strip_tags($trophy->description),250), 55, "\n", true); ?>
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
        <?php if( $this->canCreate): ?>
          <?php echo $this->translate('Why don\'t you %1$screate one%2$s?',
            '<a href="'.$this->url(array('action' => 'create'), 'ynidea_trophies').'">', '</a>') ?>
        <?php endif; ?>
        </span>
      </div>
    <?php endif; ?>




