<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitestore
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: edit.tpl 2013-09-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php

$this->headLink()
        ->appendStylesheet($this->layout()->staticBaseUrl
                . 'application/modules/Sitestorealbum/externals/styles/style_sitestorealbum.css');
?>
<?php echo $this->form->setAttrib('class', 'global_form_popup')->render(); ?>