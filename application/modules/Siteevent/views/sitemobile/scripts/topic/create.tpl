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

<?php 

$breadcrumb = array(
    array("href"=>$this->siteevent->getHref(),"title"=>$this->siteevent->getTitle(),"icon"=>"arrow-r"),
    array("href"=>$this->siteevent->getHref(array('tab' => $this->tab_selected_id)),"title"=>"Discussions","icon"=>"arrow-d")
     );
echo $this->breadcrumb($breadcrumb);
?>
<div class="layout_middle">
  <?php echo $this->form->render($this) ?>
</div>