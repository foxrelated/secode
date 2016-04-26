<?php
$file_path = dirname(__FILE__);
$pos = strpos($file_path,DIRECTORY_SEPARATOR.'module'.DIRECTORY_SEPARATOR);
if( $pos!== false)
{
    $path = (dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'task.php';    
}
else
{
    $path = ((dirname(__FILE__))).DIRECTORY_SEPARATOR.'task.php';
}
echo "php ".$path." Running"."<br/>";
echo "php ".$path." Subscription"."<br/>";  
echo "php ".$path." SendMail";
?>