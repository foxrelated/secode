<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: create.tpl 6590 2013-04-01 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php if (!Engine_Api::_()->sitemobile()->isApp()):?>
<?php 
$breadcrumb = array(
    array("href"=>$this->siteevent->getHref(),"title"=>$this->siteevent->getTitle(),"icon"=>"arrow-r"),
    array("href"=>$this->siteevent->getHref(array('tab' => $this->content_id)),"title"=>"Review","icon"=>"arrow-d")
     );
echo $this->breadcrumb($breadcrumb);
?>
<?php endif;?>
<?php echo $this->form->render($this) ?>

<?php include_once APPLICATION_PATH . '/application/modules/Siteevent/views/sitemobile/scripts/_formCreateReview.tpl'; ?>