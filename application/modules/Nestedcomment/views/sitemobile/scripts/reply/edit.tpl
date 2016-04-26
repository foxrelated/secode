<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Nestedcomment
 * @copyright  Copyright 2014-2015 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit.tpl 2014-11-07 00:00:00 SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php //GET VERSION OF SITEMOBILE APP.
  $RemoveClassDone = true;
  if(Engine_Api::_()->sitemobile()->isApp()) {
if(!Engine_Api::_()->nestedcomment()->checkVersion(Engine_Api::_()->getDbtable('modules', 'core')->getModule('sitemobileapp')->version, '4.8.6p1'))
      $RemoveClassDone = false;
  }
 
?>
<?php if (!Engine_Api::_()->sitemobile()->isApp()): ?>
    <?php
    $this->headTranslate(array(
        'Are you sure you want to delete this?',
    ));
    ?>
  <?php 
    include_once APPLICATION_PATH . '/application/modules/Nestedcomment/views/sitemobile/scripts/_activitySettings.tpl';
  ?>  
    
  <div id="replies">

    <div class="ps-carousel-comments sm-ui-popup sm-ui-popup-container-wrapper">
        <div class="">
            <div class="sm-ui-popup-top ui-header ui-bar-a">
                <a href="javascript:void(0);" data-role="button" data-corners="true" data-shadow="true" class="ui-btn-left ui-link ui-btn ui-btn-a ui-shadow ui-corner-all" onclick=" $('.ui-page-active').removeClass('dnone'); $('#editpopup_'+ <?php echo $this->comment_id;?>).remove();$('#comment-option-'+ <?php echo $this->comment_id;?>).css('display', 'none');$('#comment_information-'+ <?php echo $this->comment_id;?>).css('display', 'block');$(window).scrollTop(parentScrollTop)" role="button" style="margin-top:5px;"><?php echo $this->translate('Cancel'); ?></a>
               <h2 class="ui-title" id="count-feedcomments"><?php echo $this->translate("Edit"); ?></h2>
              <a href="javascript:void(0);" data-role="button" data-corners="true" data-shadow="true" class="ui-btn-right ui-link ui-btn ui-btn-a ui-shadow ui-corner-all done" onclick="sm4.core.comments.attachEdit($('#edit-form'));"><?php echo $this->translate(array('Update', 'Updates', 1),  $this->locale()->toNumber(1)) ?></a>  
            </div>
        <?php if (isset($this->form)): ?>  
            <div class="sm-comments-post-comment-form" style="top:42px;">
                
                    <table cellspacing="0" cellpadding="0">
                        <tr>    
                            <td class="sm-cmf-left">
                                <form id="edit-form" enctype="application/x-www-form-urlencoded" action="" method="post" data-ajax="false">
                                    <?php
                                    foreach ($this->form->getElements() as $key => $value):
                                        if ($key != "submit") : echo $this->form->$key;
                                        endif;
                                    endforeach;
                                    ?>
                                </form>
                            </td>
                        </tr>
                    </table>
                    <?php if (!empty($this->comment->attachment_type) && null !== ($attachment = $this->item($this->comment->attachment_type, $this->comment->attachment_id))): ?>
                        <div class="seaocore_comments_attachment" id="seaocore_comments_attachment">
                          <div class="seaocore_comments_attachment_photo">
                            <?php if (null !== $attachment->getPhotoUrl()): ?>
                                <?php echo $this->itemPhoto($attachment, 'thumb.normal', $attachment->getTitle()); ?>
                            <?php endif; ?>
                          </div>
                        </div>
                    <?php endif; ?>
                    <?php if ($taggingEnabled): ?>  
                        <div class="cont-sep t_l b_medium"></div>
                        <div id="activitypost-container-temp" action-id='edit-form' action-title='edit'>
                            <div class="compose_buttons">
                                
            <?php if ($taggingEnabled): ?>
                                    <a id="comment-add-people" href="javascript:void(0);" data-role="none" onclick="sm4.activity.composer.showEditPluginForm(this, 'addpeople', '#body', '<?php echo $this->comment_id ?>');">
                                        <i class="cm-icons cm-icon-user"></i>
                                    </a>
            <?php endif; ?>
                            </div>
                            <div id="edit_options_box">
                              

            <?php if ($taggingEnabled): ?>
                                    <div id="adv_edit_post_container_tagging" class="post_container_tags ui-page-content" style="display:none;" title="<?php echo $this->translate('Who are you with?') ?>" >
                                        <div id="aff_edit_mobile_aft_search-element">
                                            <div class="sm-post-search-fields">
                                                <table width="100%">
                                                    <tr>
                                                        <td class="sm-post-search-fields-left">
                                                            <input class="ui-input-field " type="text" autocomplete="off" value="" id="aff_edit_mobile_aft_search" name="aff_mobile_aft_search" placeholder='<?php echo $this->translate("Start typing a name..."); ?>' data-role="none" />
                                                        </td>
                                                    </tr>
                                                </table>			
                                                <span role="status" aria-live="polite"></span>
                                            </div>
                                            <div id="toEditValues-temp-wrapper" style="border:none;display:none;">
                                                <div id="toEditValues-temp-element">
                                                    <input type="hidden" id="toEditValues-temp" value=""  name="toValues-temp" />
                                                    <input type="hidden" id="toEditValues" value=""  name="toValues" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="toEditValuesdone-wrapper" style="display:none;"></div>
                                    <div class="ui-header o_hidden" id="ui-post-edit-header-addpeople" style="display:none;">
                                        <button id="compose-submit" data-role="button" data-icon="false" class="ui-btn-left ui-btn-default" onclick="$('#aff_edit_mobile_aft_search').val('');
                                          sm4.activity.composer.addpeople.addEditFriends('#edit-form');
                                          return false;" style="background-color:transparent !important;"><?php echo $this->translate("Add") ?></button>
                                        <?php if (!Engine_Api::_()->sitemobile()->isApp()) : ?>
                                            <a data-role="button" data-icon="false" href="" data-wrapperels="span"  class="ui-btn-right" onclick="$('#aff_edit_mobile_aft_search').val('');
                                              sm4.activity.toggleEditPostArea(this, false, 'addpeople');"><?php echo $this->translate('Cancel'); ?></a>
                                        <?php else: ?>
                                            <a href='javascript://' class='ui-btn-right'  data-role="button" data-icon='arrow-l'  data-iconpos="notext"  data-logo="true" onclick="$('#aff_edit_mobile_aft_search').val('');
                                               sm4.activity.toggleEditPostArea(this, false, 'addpeople');" ></a>
                                    <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div> 
                    <?php endif; ?>
               
            </div>	
    <?php endif; ?>
    </div>

  </div>   
<?php else: ?>


<?php endif; ?>

<script type="text/javascript">
    sm4.core.runonce.add(function(){
      $('#edit-form').find('#body').val('<?php echo $this->comment->body;?>');
      $('#edit-form').find('#body').focus();
    });                   
    sm4.core.runonce.add(function() {
      $('.ps-close-popup').on('click', function() {
        <?php if($RemoveClassDone):?>   
            $('.ui-page-active').removeClass('dnone');
         <?php else : ?>
           $('.ui-page-active').removeClass('pop_back_max_height');
         <?php endif;?>  
        $('.ps-close-popup').closest('#editpopup_' + '<?php echo $this->comment->comment_id;?>').remove();
        $.mobile.silentScroll(parentScrollTop); 
      });
    });
</script> 