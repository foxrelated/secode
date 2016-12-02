       	
						<?php echo $this->itemPhoto($sitegroup, 'thumb.icon') ?>
            <p class="ui-li-aside">
							<span>
                <?php if(false):?>
                  <?php if( $sitegroup->closed ): ?>
                    <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/close.png', '', array('class' => 'icon', 'title' => $this->translate('Closed'))) ?>
                  <?php endif;?>               
                  <?php if ($sitegroup->sponsored == 1): ?>
                    <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/sponsored.png', '', array('class' => 'icon', 'title' => $this->translate('Sponsored'))) ?>
                  <?php endif; ?>
                  <?php if ($sitegroup->featured == 1): ?>
                    <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Sitegroup/externals/images/sitegroup_goldmedal1.gif', '', array('class' => 'icon', 'title' => $this->translate('Featured'))) ?>
                  <?php endif; ?>
                <?php endif; ?>
							</span>
              </p>
            
							<h3><?php echo $sitegroup->getTitle(); ?></h3>			
  
              <p>
                <?php echo $this->timestamp(strtotime($sitegroup->creation_date)) ?> - <?php echo $this->translate('created by'); ?>
                <b><?php echo $sitegroup->getOwner()->getTitle() ?></b>
              </p>
              
              <?php if(false):?>
              <p>
							<?php echo $this->translate(array('%s like', '%s likes', $sitegroup->like_count), $this->locale()->toNumber($sitegroup->like_count)) ?>
                 <?php $sitegroupreviewEnabled = Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('sitegroupreview'); ?>               
						    <?php if (!empty($sitegroupreviewEnabled)): ?>
                -
								<?php echo $this->translate(array('%s review', '%s reviews', $sitegroup->review_count), $this->locale()->toNumber($sitegroup->review_count)) ?>                
							<?php endif; ?>
                -
							<?php echo $this->translate(array('%s comment', '%s comments', $sitegroup->comment_count), $this->locale()->toNumber($sitegroup->comment_count)) ?>
                -
							<?php echo $this->translate(array('%s view', '%s views', $sitegroup->view_count), $this->locale()->toNumber($sitegroup->view_count)) ?>
							</p>	
              <?php endif; ?>

			