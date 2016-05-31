<h2>
    <?php echo $this->translate('Android Mobile Application') ?>
</h2>
<?php if (count($this->navigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php
        // Render the menu
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>
<?php if (count($this->subnavigation)): ?>
    <div class='seaocore_admin_tabs'>
        <?php
        // Render the menu
        echo $this->navigation()->menu()->setContainer($this->subnavigation)->render()
        ?>
    </div>
<?php endif; ?>

<?php
$db = Engine_Db_Table::getDefaultAdapter();
//$translationAdapter = $db->select()
//        ->from('engine4_core_settings', 'value')
//        ->where('`name` = ?', 'core.translate.adapter')
//        ->query()
//        ->fetchColumn();
?>

<!--<h2>
<?php // echo $this->htmlLink(array('route' => 'admin_default', 'controller' => 'language', 'action' => 'index'), $this->translate('Language Manager')) ?>
  &#187; <?php // echo $this->localeTranslation   ?>
</h2>-->
<p>
    <?php echo $this->translate("The language phrases in this pack are show below. If you're looking for a specific phrase, use the search box below to find it. Be sure to click 'Save Changes' before moving to the next page of phrases if you want to keep your changes. Beneath each phrase, you'll see the original phrase for your reference. This is useful if you've lost or accidentally deleted a phrase. Some phrases are in plural form - you'll see examples of numbers used for these phrases in italics.") ?>
</p>

<br />
<?php
if (!empty($this->taregtFileCorrupted)):
    echo '<div class="seaocore_tip"><span>Existing "' . $this->taregtFileCorrupted . '" file is corrupt. We suggest you to delete existing xml file from Language Assets page and then translate the language file again.</span></div>';
endif;
?>
<?php
if (!empty($this->missingLanguageCount) && !empty($this->isFileExist)):
    echo '<div class="seaocore_tip"><span>' . $this->translate(array('%s Missing Language', '%s Missing Languages', $this->missingLanguageCount), $this->locale()->toNumber($this->missingLanguageCount)) . ' found. Please select Missing language from the following form and translate all of them accordingly.</span></div>';
endif;
?>
<?php if (!empty($this->isFileExist) && !empty($this->filterForm)): ?>
    <div class="admin_search">
        <div class="search">
            <?php echo $this->filterForm->render($this) ?>
        </div>
    </div>
<?php endif; ?>

<br />
<?php
$url = $this->url() . $this->query;
if ($this->page) {
    if (!$this->query) {
        $url .= '?';
    } else {
        $url .= '&';
    }
    $url .= "page=" . $this->page;
}
?>
<?php if (!empty($this->paginator)): ?>
    <form action="<?php echo $url ?>" method="post">
        <div>
            <div class="admin_language_editor">
                <div class="admin_language_editor_top">
                    <!--        <div class="admin_language_editor_addphrase">
                              <a class="buttonlink" href="javascript:void(0);" onclick="addPhrase()">Add New Phrase</a>
                            </div>-->
                    <div class="admin_language_editor_pages">
                        <?php $pageInfo = $this->paginator->getPages();
                        if ($pageInfo->totalItemCount):
                            ?>
                            <?php echo $this->translate('Showing %1$s-%2$s of %3$s phrases', $pageInfo->firstItemNumber, $pageInfo->lastItemNumber, $pageInfo->totalItemCount) ?>
                        <?php else: ?>
                            <?php echo $this->translate('No phrases found.') ?>
                            <?php endif; ?>
                        <span>
                            <?php if (!empty($pageInfo->previous)): ?>
                                <?php echo $this->htmlLink(array('reset' => false, 'QUERY' => array_merge(array('page' => $pageInfo->previous), $this->values)), $this->translate('Previous Page')) ?>
                            <?php endif; ?>
                            <?php if (!empty($pageInfo->previous) && !empty($pageInfo->next)): ?>
                                |
                            <?php endif; ?>
                            <?php if (!empty($pageInfo->next)): ?>
                                <?php echo $this->htmlLink(array('reset' => false, 'QUERY' => array_merge(array('page' => $pageInfo->next), $this->values)), 'Next Page') ?>
    <?php endif; ?>
                        </span>
                    </div>
                </div>
                <ul>
                    <?php $tabIndex = 1; ?>
                    <?php foreach ($this->paginator as $item): ?>
                            <?php if (!$item['plural']): ?>
                            <li>
                                <?php
                                $height = ceil(max(Engine_String::strlen((string) $item['current']), Engine_String::strlen((string) $item['original']), 1) / 60) * 1.2;
                                echo $this->formTextarea(sprintf('values[%d]', $item['uid']), $item['current'], array('cols' => 60, 'rows' => 1, 'style' => 'height: ' . $height . 'em', 'onkeypress' => 'checkModified(this, event);'));
                                echo $this->formHidden(sprintf('keys[%d]', $item['uid']), $item['key']);
                                ?>
                                <span class="admin_language_original">
            <?php echo $this->escape($item['original']) ?>
                                </span>
                            </li>
                        <?php else: ?>
            <?php for ($i = 0; $i < $item['pluralFormCount']; $i++): ?>
                                <li>
                                    <span class="admin_language_plural">
                                    <?php echo $this->translate("This phrase is pluralized. Example values:") ?> <?php echo join(', ', $this->pluralFormSample[$i]) ?>
                                    </span>
                                    <?php
                                    $height = ceil(max(Engine_String::strlen((string) @$item['current'][$i]), Engine_String::strlen((string) @$item['original'][0]), 1) / 60) * 1.2;
                                    echo $this->formTextarea(sprintf('values[%d][%d]', $item['uid'], $i), @$item['current'][$i], array('cols' => 60, 'rows' => 2, 'style' => 'height: ' . $height . 'em', 'onkeypress' => 'checkModified(this, event);'));
                                    echo $this->formHidden(sprintf('keys[%d][%d]', $item['uid'], $i), $item['key']);
                                    ?>
                                    <span class="admin_language_original">
                <?php echo isset($item['original'][0]) ? $this->escape($item['original'][0]) : '' ?>
                                    </span>
                                </li>
                            <?php endfor; ?>
                        <?php endif; ?>
    <?php endforeach; ?>
                </ul>
                <div class="admin_language_editor_submit">
                    <?php // if ($translationAdapter != 'array'): ?>
                        <button type="submit"><?php echo (!empty($this->isFileExist)) ? $this->translate("Save Changes") : $this->translate("Create Language For App"); ?></button>
                    <?php // else: ?>
<!--                        Please uncheck the "Translation Performance" box <a href="admin/core/settings/performance">here</a> before saving your changes.    -->
    <?php // endif; ?>

                </div>
            </div>
        </div>
    </form>
<?php endif; ?>

<br />

<!--<p>
<?php
// echo $this->translate(
//           "When you've finished editing this language pack, you can return to the %s.",
//           $this->htmlLink(array('route'=>'admin_default','controller'=>'language'), 'Language Manager')) 
?>
</p>
<br />
<p>
  Also after making changes in your Language Manager you can improve the load times of you pages by using PHP Arrays.<br />
  <a href="admin/core/settings/performance">Click Here</a> and check the "Translation Performance" performance box.  <br />
  Please note that the initial converstion may take longer that 30 seconds, but will improve future page loads.
</p>-->
<script type="text/javascript">
//<![CDATA[
    var addPhrase = function () {
        var url = '<?php echo $this->url(array('action' => 'add-phrase')) ?>';
        var phrase = prompt('Type your new phrase below:');
        var redirect = '<?php echo $this->url(array('action' => 'edit')) ?>?search=' + phrase;
        if (!phrase || phrase === null || phrase === '') {
            return;
        }
        var request = new Request.JSON({
            url: url,
            data: {
                phrase: phrase,
                format: 'json'
            },
            onComplete: function () {
                window.location.href = redirect;
            }
        });

        request.send();
    }
//]]>
</script>