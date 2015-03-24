<?php

require ASSASSIN_PATH.'libs/AssassinSysInitConfig.php';

if (defined('ASSASSIN_ENV')) 
{
	switch (ASSASSIN_ENV) 
	{
		case 'development':
			error_reporting(E_ALL);
			break;
		case 'testing':
		case 'production':
			error_reporting(0);
			break;
		default:
			exit('应用程序环境没有被正确设置。');
	}
}

//__autoload 自动加载函数
require ASSASSIN_PATH_LIBS.'basics.php';

// 加载路由
BaseModelRouter::route();

$class = BaseModelCommon::getFormatName($_GET[ASSASSIN_CONTROLLER], 'class') ;
$class = htmlspecialchars($class);
$class .= 'Controller';

$controller = new $class($_GET[ASSASSIN_CONTROLLER], $_GET[ASSASSIN_ACTION]);

$controller->runCommand();