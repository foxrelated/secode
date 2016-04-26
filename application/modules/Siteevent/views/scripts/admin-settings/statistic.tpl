<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: statistic.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2>
    <?php echo $this->translate('Advanced Events Plugin'); ?>
</h2>

<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'> <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?> </div>
<?php endif; ?>
<?php if (!empty($this->tempStatistics)): ?>
    <div class='clear'>
        <div class='settings'>
            <form class="global_form">
                <div>
                    <h3><?php echo $this->translate('Statistics for Events'); ?></h3>
                    <p class="description"> <?php echo $this->translate('Below are some valuable statistics for the Events submitted on this site.'); ?>
                    </p>
                    <br />

                    <table class='admin_table siteevent_statistics_table' width="100%">
                        <tbody>
                            <tr>
                                <td width="50%"><?php echo $this->translate("Total Events"); ?> :</td>
                                <td><?php echo $this->totalSiteevent ?></td>
                            </tr> 
                            
                            <?php if (Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventrepeat')): ?>
                                <tr>
                                    <td width="50%"><?php echo $this->translate("Total Repeating Events"); ?> :</td>
                                    <td><?php echo $this->totalRepeatSiteevent ?></td>
                                </tr> 

                                <tr>
                                    <td width="50%"><?php echo $this->translate("Total Non-repeating Events"); ?> :</td>
                                    <td><?php echo $this->totalNonRepeatSiteevent ?></td>
                                </tr>                             
                            
                            <?php endif; ?>
                            <tr>
                                <td width="50%"><?php echo $this->translate("Total Editors"); ?> :</td>
                                <td><?php echo $this->totalEditors ?></td>
                            </tr>                        
                            <tr>
                                <td width="50%"><?php echo $this->translate("Total Published Events"); ?> :</td>
                                <td><?php echo $this->totalPublish ?></td>
                            </tr>
                            <tr>
                                <td width="50%"><?php echo $this->translate("Total Events in Draft "); ?> :</td>
                                <td><?php echo $this->totalDrafted ?></td>
                            </tr>
                            <tr>
                                <td width="50%"><?php echo $this->translate("Total Cancelled Events"); ?> :</td>
                                <td><?php echo $this->totalClosed ?></td>
                            </tr>
                            <tr>
                                <td width="50%"><?php echo $this->translate("Total Approved Events"); ?> :</td>
                                <td>
                                    <?php echo $this->totalapproved ?>
                                </td>
                            </tr>	

                            <tr>
                                <td width="50%"><?php echo $this->translate("Total Disapproved Events"); ?> :</td>
                                <td><?php echo $this->totaldisapproved ?></td>
                            </tr>

                            <tr>
                                <td width="50%"><?php echo $this->translate("Total Featured Events"); ?> :</td>
                                <td><?php echo $this->totalfeatured ?></td>
                            </tr>

                            <tr>
                                <td width="50%"><?php echo $this->translate("Total Sponsored Events"); ?> :</td>
                                <td><?php echo $this->totalsponsored ?></td>
                            </tr>

                            <tr>
                                <td width="50%"><?php echo $this->translate('Total Editor Reviews'); ?> :</td>
                                <td><?php echo $this->totalEditorReviews ?></td>
                            </tr>

                            <tr>
                                <td width="50%"><?php echo $this->translate('Total Editor Reviews in Draft'); ?> :</td>
                                <td><?php echo $this->totalDraftEditorReviews ?></td>
                            </tr>           

                            <tr>
                                <td width="50%"><?php echo $this->translate('Total User Reviews'); ?> :</td>
                                <td><?php echo $this->totalUserReviews ?></td>
                            </tr>              

                            <tr>
                                <td width="50%"><?php echo $this->translate('Total Reviews'); ?> :</td>
                                <td><?php echo $this->totalReviews ?></td>
                            </tr>
                            
                            <?php if(Engine_Api::_()->getDbtable('modules', 'core')->isModuleEnabled('siteeventdocument')): ?>
                                <tr>
                                    <td width="50%"><?php echo $this->translate('Total Documents'); ?> :</td>
                                    <td><?php echo $this->totalDocuments ?></td>
                                </tr>
                            <?php endif; ?>

                            <tr>
                                <td width="50%"><?php echo $this->translate('Total Discussions'); ?> :</td>
                                <td><?php echo $this->totalDiscussionTopics ?></td>
                            </tr>

                            <tr>
                                <td width="50%"><?php echo $this->translate('Total Discussions Posts'); ?> :</td>
                                <td><?php echo $this->totalDiscussionPosts ?></td>
                            </tr>

                            <tr>
                                <td width="50%"><?php echo $this->translate("Total Photos in Events"); ?> :</td>
                                <td><?php echo $this->totalPhotos ?></td>
                            </tr>

                            <tr>
                                <td width="50%"><?php echo $this->translate("Total Videos in Events"); ?> :</td>
                                <td><?php echo $this->totalVideos ?></td>
                            </tr>

                            <tr>
                                <td width="50%"><?php echo $this->translate('Total Comments Posts'); ?> :</td>
                                <td><?php echo $this->totalEventComments ?></td>
                            </tr>

                            <tr>
                                <td width="50%"><?php echo $this->translate('Total Likes'); ?> :</td>
                                <td><?php echo $this->totalEventLikes ?></td>
                            </tr>

                            <tr>
                                <td width="50%"><?php echo $this->translate("Total Diaries"); ?> :</td>
                                <td><?php echo $this->totalDiaries ?></td>
                            </tr>
                            <!--TICKETS STATISTICS-->
                            <?php if(Engine_Api::_()->hasModuleBootstrap('siteeventticket')): ?>
                            <tr>
                                <td width="50%"><?php echo $this->translate("Total Tickets"); ?> :</td>
                                <td><?php echo $this->totalEventTickets ?></td>
                            </tr>
                            
                            <tr>
                                <td width="50%"><?php echo $this->translate("Total Orders"); ?> :</td>
                                <td><?php echo $this->totalTicketOrders ?></td>
                            </tr>
                           <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>