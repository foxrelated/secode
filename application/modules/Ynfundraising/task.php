<?php
//wget -O- "http://<yoursite>/?m=lite&name=task&module=ynfundraising" > /dev/null
// http://dev2.younetco.com/se4/se_427/?m=lite&name=task&module=ynfundraising

$application -> getBootstrap() -> bootstrap('translate');
$application -> getBootstrap() -> bootstrap('locale');
$application -> getBootstrap() -> bootstrap('hooks');


if(class_exists('Ynfundraising_Plugin_Task_Timeout'))
{
	Ynfundraising_Plugin_Task_Timeout::execute();
}
