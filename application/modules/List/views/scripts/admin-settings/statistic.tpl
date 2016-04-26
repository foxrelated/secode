<?php
/**
* SocialEngine
*
* @category   Application_Extensions
* @package    List
* @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
* @license    http://www.socialengineaddons.com/license/
* @version    $Id: statistic.tpl 6590 2010-12-31 9:40:21Z SocialEngineAddOns $
* @author     SocialEngineAddOns
*/
?>

<h2><?php echo $this->translate('Listings / Catalog Showcase Plugin');?></h2>
<?php if( count($this->navigation) ): ?>
	<div class='seaocore_admin_tabs'> <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?> </div>
<?php endif; ?>
<div class='clear'>
  <div class='settings'>
    <form class="global_form">
      <div>
        <h3><?php echo $this->translate('Listings Statistics');?></h3>
        <p class="description"> <?php echo $this->translate('Below are some valuable statistics for the Listings submitted on this site:');?> </p>
        <table class='admin_table admin_list_statistics_table' width="100%">
          <tbody>
            <tr>
            	<td width="45%"><?php echo $this->translate('Total Listings');?> :</td>
            	<td><?php echo $this->totalList ?></td>
            </tr>
            <tr>
            	<td width="45%"><?php echo $this->translate('Total Published Listings');?> :</td>
            	<td><?php echo $this->totalPublish ?></td>
            </tr>
            <tr>
            	<td width="45%"><?php echo $this->translate('Total Listings in Draft ');?> :</td>
            	<td><?php echo $this->totalDrafted ?></td>
            </tr>
            <tr>
            	<td width="45%"><?php echo $this->translate('Total Closed Listings');?> :</td>
            	<td><?php echo $this->totalClosed ?></td>
            </tr>
            <tr>
            	<td width="45%"><?php echo $this->translate('Total Open Listings');?> :</td>
            	<td>
	            	<?php echo $this->totalOpen ?>
            	</td>
            </tr>
            <tr>
            	<td width="45%"><?php echo $this->translate('Total Approved Listings');?> :</td>
            	<td>
            		<?php echo $this->totalapproved ?>
            	</td>
            </tr>	
            
            <tr>
            	<td width="45%"><?php echo $this->translate('Total Disapproved Listings');?> :</td>
            	<td><?php echo $this->totaldisapproved ?></td>
            </tr>
            
            <tr>
            	<td width="45%"><?php echo $this->translate('Total Featured Listings');?> :</td>
            	<td><?php echo $this->totalfeatured ?></td>
            </tr>
            
            <tr>
            	<td width="45%"><?php echo $this->translate('Total Sponsored Listings');?> :</td>
            	<td><?php echo $this->totalsponsored ?></td>
            </tr>
            
           <tr>
            	<td width="45%"><?php echo $this->translate('Total Reviews');?> :</td>
            	<td><?php echo $this->totalreview ?></td>
            </tr>
            
           <tr>
            	<td width="45%"><?php echo $this->translate('Total Discussions');?> :</td>
            	<td><?php echo $this->totaldiscussion ?></td>
            </tr><br>
            
            <tr>
            	<td width="45%"><?php echo $this->translate('Total Discussions Posts');?> :</td>
            	<td><?php echo $this->totaldiscussionpost ?></td>
            </tr>

            <tr>
            	<td width="45%"><?php echo $this->translate('Total Photos in Listings');?> :</td>
            	<td><?php echo $this->totalphotopost ?></td>
            </tr>
            
            <tr>
            	<td width="45%"><?php echo $this->translate('Total Videos in Listings');?> :</td>
            	<td><?php echo $this->totalvideopost ?></td>
            </tr>
            
            <tr>
							<td width="45%"><?php echo $this->translate('Total Comments Posts');?> :</td>
            	<td>
								<?php if(!empty($this->totalcommentpost)) : ?>
									<?php echo $this->totalcommentpost ?>
								<?php else :?>
									<?php echo 0 ?>
								<?php endif; ?>
							</td>
            </tr> 
          </tbody>
        </table>
      </div>
    </form>
  </div>
</div>