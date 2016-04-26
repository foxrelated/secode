<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitepagereview
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: create.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
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

<?php include_once APPLICATION_PATH . '/application/modules/Siteevent/views/sitemobile/scripts/_formUpdateReview.tpl'; ?>