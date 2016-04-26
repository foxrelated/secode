<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Siteevent
 * @copyright  Copyright 2013-2014 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: utility.tpl 6590 2014-01-02 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2>
    <?php echo $this->translate('Advanced Events Plugin'); ?>
</h2>

<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php
        // Render the menu
        //->setUlClass()
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>

<?php if (count($this->subNavigation)): ?>
    <div class='tabs'>
        <?php
        // Render the menu
        //->setUlClass()
        echo $this->navigation()->menu()->setContainer($this->subNavigation)->render()
        ?>
    </div>
<?php endif; ?>

<h3>
    <?php echo $this->translate('Review Video Utilities'); ?>
</h3>
<p>
    <?php echo $this->translate("This page contains utilities to help configure and troubleshoot the videos of this plugin.") ?>
</p>
<br/>

<div class="settings">
    <form>
        <div>
            <h3><?php echo $this->translate("Ffmpeg Version") ?></h3>
            <p class="form-description"><?php echo $this->translate("This will display the current installed version of ffmpeg.") ?></p>
            <textarea><?php echo $this->version; ?></textarea><br/><br/><br/>

            <h3><?php echo $this->translate("Supported Video Formats") ?></h3>
            <p class="form-description"><?php echo $this->translate('This will run and show the output of "ffmpeg -formats". Please see this event for more info.') ?></p>
            <textarea><?php echo $this->format; ?></textarea><br/><br/>
            <?php if (TRUE): ?>
            <?php else: ?>
            <?php endif; ?>
        </div>
    </form>
</div>