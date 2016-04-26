<?php if( count($this->paginator) > 0 ): ?>
      <ul class='ideas_frame ideas_browse' style="">
        <?php foreach( $this->paginator as $idea ):?>
          <li>
            <div class="ideas_photo">
              <?php echo $this->htmlLink($idea->getHref(), $this->itemPhoto($idea, 'thumb.normal')) ?>
            </div>
                              
            <div class="ideas_info">
              <div class="ideas_title">
                <h3><?php $idea_name = Engine_Api::_()->ynidea()->subPhrase($idea->getTitle(),50);      
                          echo $this->htmlLink(array('route' => 'ynidea_general', 'action' => 'detail', 'id' => $idea->idea_id), $this->translate($idea->getTitle()), array(
                                        'class' => ''));
                    ?></h3>
              </div>             
             <div class="ideas_title">
              <?php echo $this->translate("Score")?>: <?php echo $idea->ideal_score; ?>
             </div> 
             <div class="ideas_title">
             <?php echo $this->translate("Version date")?>: <?php echo $idea->version_date; ?>
             </div> 
             
             <div class="ideas_title">
               <?php echo $this->translate("Awards")?> 
               <br/>
                    <?php 
                        $awards = Engine_Api::_()->ynidea()->getAwards($idea->idea_id); 
                        if(count($awards) > 0)
                        {
                            foreach($awards as $award)
							{
							    $trophy = Engine_Api::_()->getItem('ynidea_trophy',$award['trophy_id']);
							    if($trophy)
								{
	                                if($award['award'] == 0){
	                                	echo "<span class='ynidea_profile_glod_medal'></span> <span style='float:left; padding-right: 10px'>(";
	                                    echo $this->htmlLink(array('route' => 'ynidea_trophies', 'action' => 'detail', 'id' => $award['trophy_id']),$trophy->title, array(
	                                        'class' => '')); 
										echo ") </span>";  
	                                }
	                                else{
	                                	echo "<span class='ynidea_profile_silver_medal'></span> <span style='float:left; padding-right: 10px'>(";
	                                    echo $this->htmlLink(array('route' => 'ynidea_trophies', 'action' => 'detail', 'id' => $award['trophy_id']),$trophy->title , array(
	                                        'class' => ''));
	                                    echo ") </span>";
	                                } 
	                            } 
								else {
									echo $this->translate("Note");
								} 
							}                                                      
                        }  
                        else
                        {
                            echo $this->translate('There is no awards.');
                        }
                    ?>
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
<?php endif; ?>
