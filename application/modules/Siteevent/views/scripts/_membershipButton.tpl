<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _showReview.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<?php foreach ($this->links as $link): ?>
    <?php

    $params = $link['params'];
    $params['route'] = $link['route'];
    $class = $this->class ? $link['class'] . " " . $this->class : $link['class'];
    ?>
    <?php echo $this->htmlLink($params, $this->translate($link['label']), array('class' => $class)); ?>
<?php endforeach; ?>
