<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitegroup
 * @copyright  Copyright 2010-2011 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: create.tpl 2011-05-05 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php 

$breadcrumb = array(
    array("href"=>$this->sitegroup->getHref(),"title"=>$this->sitegroup->getTitle(),"icon"=>"arrow-r"),
    array("href"=>$this->sitegroup->getHref(array('tab' => $this->tab_selected_id)),"title"=>"Discussions","icon"=>"arrow-d")
     );

echo $this->breadcrumb($breadcrumb);
?>
<div class="layout_middle">
  <?php echo $this->form->render($this) ?>
</div>	