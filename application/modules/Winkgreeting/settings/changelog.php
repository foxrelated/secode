<?php

return array(
  '4.0.3' => array(
    'Plugin/Menus.php' => 'Fixed bug with menu label',
    'settings/manifest.php' => 'Incremented version',
	'settings/my.sql' => 'Incremented version',
  ),
  '4.0.2' => array(
    'controllers/ComposeController.php' => 'Added functionality: wink and greeting action without reloading the page.',
    'settings/manifest.php' => 'Incremented version',
	'settings/my.sql' => 'Incremented version',
  ),
  '4.0.1' => array(
    'controllers/ComposeController.php' => 'Bugfix - Verify mail template type. Fixed issue with placeholders which were not applied for the internal mails.',
    'settings/manifest.php' => 'Incremented version',
	'settings/my.sql' => 'Incremented version',
    'settings/my-upgrade-4.0.0-4.0.1.sql' => 'Added',
  ),
) ?>