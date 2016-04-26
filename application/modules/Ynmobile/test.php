<?php

include './inc/scss.inc.php';

$options =array('compress'=>false);

$scss = new scssc($options  );

$directories = array( dirname(__FILE__).'/themes/iphone');

$filename = dirname(__FILE__) . '/themes/iphone/ionic.scss';

$variables = array(
    'light'                 => '#fff',
    'stable'                => '#f8f8f8',
    'positive'              => '#387ef5',
    'calm'                  => '#11c1f3',
    'balanced'              => '#33cd5f',
    'energized'             => '#ffc900',
    'assertive'             => '#ef473a',
    'royal'                 => '#886aea',
    'dark'                  => '#444',
    'base-background-color' => '#fff',
    'base-color'            => '#000',
);


$scss->setImportPaths(array(dirname($filename)));

$scss->setVariables($variables);

if(!file_exists($filename)){
    exit("file not found");
}

$content = file_get_contents($filename);

echo $scss->compile($content);
