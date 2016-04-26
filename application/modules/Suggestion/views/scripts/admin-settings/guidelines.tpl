<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: guidelines.tpl 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<h2>
  <?php echo $this->translate('Suggestions / Recommendations Plugin') ?>
</h2>
<?php if (count($this->navigation)): ?>
	<div class='tabs'>
  <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
  </div>
<?php endif; ?>

	<a href="<?php echo $this->url(array('module' => 'suggestion', 'controller' => 'settings', 'action' => 'manage-module'), 'admin_default', true) ?>" class="buttonlink" style="background-image:url(<?php echo $this->layout()->staticBaseUrl; ?>application/modules/Seaocore/externals/images/back.png);padding-left:23px;"><?php echo $this->translate("Back to Manage Module Settings"); ?></a><br />
	  <div class='settings' style="margin-top:15px;">
	    <h3><?php echo $this->translate("Guidelines to display 'Suggest to Friend' link on the content view pages of 3rd party plugin") ?></h3>
	    <p><?php echo $this->translate("Please follow the steps below to place “Suggest to Friend” link on the content view pages of the 3rd party integrated module / plugin.<br />Content view pages of these 3rd party modules / plugins can be widgetized or non-widgetized. You will need to follow these guidelines for these 2 cases:"); ?>
		  <br /><br />
	  <?php echo $this->translate("1) If the content view page is static / non-widgetized:"); ?><br />&nbsp;&nbsp;
	  <?php	echo $this->translate("a) Open the template file for the content view page of the desired 3rd party module / plugin."); ?><br />&nbsp;&nbsp;
	  <?php echo $this->translate("b) Place the below mentioned code in the file at the desired position to display ‘Suggest to Friend’ link:"); ?><br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	  <b class="bold"><?php echo $this->translate('echo $this->content()->renderWidget("suggestion.suggestion-link")'); ?></b><br /><br />
	  <?php echo $this->translate("2) If the content view page is widgetized:"); ?><br />&nbsp;&nbsp;
	  <?php echo $this->translate("i) If you want a separate “Suggest to Friend” link, then follow the below steps:"); ?><br />&nbsp;&nbsp;&nbsp;&nbsp;
	  <?php echo $this->translate("a) Go to Layout Editor and select desired content view page from the ‘Editing’ section."); ?><br />&nbsp;&nbsp;&nbsp;&nbsp;
	  <?php echo $this->translate("b) Place the 'Suggest to Friend Link' widget at the desired location from the Layout Editor."); ?><br /><br />&nbsp;&nbsp;
	  <?php echo $this->translate("ii) If you want 'Suggest to Friend' link to be shown in the quick links available on the content view page then follow the steps below (Note : This link will be displayed in quick links only if the page has a gutter navigation menu (like for ‘Blog’ there is a ‘Blog Gutter Navigation Menu’ in the ‘Menu Editor’ section available in the Layout dropdown):"); ?><br />
    </p><br />
    <div class="admin_seaocore_guidelines_wrapper">
      <ul class="admin_seaocore_guidelines">
        <li>	
          <div class="steps">
            <a href="javascript:void(0);" onClick="guideline_show('step-1');"><?php echo $this->translate("Step 1") ?></a>              
            <div id="step-1" style='display: none;'>
              <p>
				<?php echo $this->translate("a) Open the file: <b>application/modules/Suggestion/Plugin/Menus.php</b>") ?><br /><br />
				<?php echo $this->translate("b) In the below mentioned code replace ‘MODULE-NAME’ by the desired module / plugin name. (For example, module name for Blog is ‘blog’ and in ‘SuggestFriendMODULE-NAME’ replace MODULE-NAME similar to ‘SuggestFriendBlog’ for blog with first letter in capital.)<br />You can check the ‘MODULE-NAME’ for desired plugin from the Manage Modules Settings of this ‘Manage Modules’ section."); ?><br /><br />
			      <div class="code_box">
				<?php echo $this->translate('public function onMenuInitialize_SuggestFriendMODULE-NAME($row) { // First letter of the module / plugin name will be in capital, like for Blog: ‘blog’.<br />&nbsp;&nbsp;
		    $sugg_check = Engine_Api::_()->suggestion()->getModSettings("MODULE-NAME", "link"); // Replace MODULE-NAME by the desired module / plugin name, like for Blog: ‘blog’.<br />&nbsp;&nbsp;
		    $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();<br />&nbsp;&nbsp;
		    if (!empty($sugg_check) && !empty($user_id)) {<br />&nbsp;&nbsp;&nbsp;&nbsp;
		      $subject = Engine_Api::_()->core()->getSubject();<br /><br />&nbsp;&nbsp;&nbsp;&nbsp;

		      $modContentObj = Engine_Api::_()->suggestion()->getSuggestedFriend("MODULE-NAME", $subject->getIdentity(), 1); // Replace MODULE-NAME by the desired module / plugin name, like for Blog: ‘blog’.<br />&nbsp;&nbsp;&nbsp;&nbsp;
		      if (!empty($modContentObj)) {<br />&nbsp;&nbsp;&nbsp;&nbsp;
			$contentCreatePopup = @COUNT($modContentObj);<br />&nbsp;&nbsp;&nbsp;&nbsp;
		      }<br /><br />&nbsp;&nbsp;&nbsp;&nbsp;

		      if (!empty($contentCreatePopup) && !empty($subject->search)) {<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			$params = $row->params;<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			$params["params"]["modName"] = "MODULE-NAME"; // Replace MODULE-NAME by the desired module / plugin name, like for Blog: ‘blog’.<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			$params["params"]["modContentId"] = $subject->getIdentity();<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			$params["params"]["modError"] = 1;<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			return $params;<br />&nbsp;&nbsp;&nbsp;&nbsp;
		      }<br />&nbsp;&nbsp;
		    }<br />
		  }'); ?>
		</div>
              </p>								
            </div>
          </div>
        </li>
        <li>	
          <div class="steps">
            <a href="javascript:void(0);" onClick="guideline_show('step-2');"><?php echo $this->translate("Step 2") ?></a>              
            <div id="step-2" style='display: none;'>
              <p>
		<?php echo $this->translate("Search for the code : <b>class Suggestion_Plugin_Menus {</b>"); ?><br /><br />
		<?php echo $this->translate("Now, copy and paste the modified code from step 2, just after the above line."); ?>
              </p>								
            </div>
          </div>
        </li>
        <li>	
          <div class="steps">
            <a href="javascript:void(0);" onClick="guideline_show('step-3');"><?php echo $this->translate("Step 3") ?></a>              
            <div id="step-3" style='display: none;'>
              <p>
		  <?php echo $this->translate("In the below mentioned query, replace “MODULE_NAME” by the desired module / plugin name and execute it in the respective database."); ?><br /><br />
		  <div class="code_box">
		    <?php echo $this->translate('INSERT IGNORE INTO `engine4_core_menuitems` (`name`, `module`, `label`, `plugin`, `params`, `menu`, `submenu`, `enabled`, `custom`, `order`) VALUES ( "suggest_friend_MODULE-NAME", "suggestion", "Suggest to Friends", "Suggestion_Plugin_Menus", \'{"route":"suggest_to_friend_link","class":"buttonlink icon_list_friend_suggestion smoothbox"}\', "MODULE-NAME_gutter", NULL , "1", "0", "999" );'); ?>
		  </div><br /><br />
		  <?php echo $this->translate("(Note : To disable ‘Suggest to Friend’ link from gutter navigation menu, go to the ‘Menu Editor’ section from the Layout dropdown in the Admin Panel.)") ?>
              </p>								
            </div>
          </div>
        </li>
      </ul>        
    </div>
  </div>  
</div>
<script type="text/javascript">
  function guideline_show(id) {
    if($(id).style.display == 'block') {
      $(id).style.display = 'none';
    } else {
      $(id).style.display = 'block';
    }
  }
</script>

