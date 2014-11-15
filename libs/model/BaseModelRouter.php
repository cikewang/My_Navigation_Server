<?php
class BaseModelRouter {
	
	
	private static function init($app)
	{
		// $app	基础目录
		$appPath = ASSASSIN_PATH_APP . $app . '/';
		// $app 项目的controller 目录
		$controllerPath = $appPath . 'controller/';
		// $app 项目的 model 目录
		$modelPath = $appPath . 'model/';
		// $app 项目的 config 目录
		$configPath = $appPath . 'config/';
		// $app 项目的 libraries 目录
		$librariesPath = $appPath . 'libraries/';
		// $app 项目的 templates 目录
		$templatesPath = $appPath . 'templates/';
		// $app 项目的模板缓存目录
		
		$templateCPath = ASSASSIN_PATH_CACHE . $app . '/templates_c/';
		BaseModelCommon::recursiveMkdir($templateCPath);
		
		define('ASSASSIN_PATH_APP_CLT',		$controllerPath);
		define('ASSASSIN_PATH_APP_MODEL',	$modelPath);
		define('ASSASSIN_PATH_APP_CONFIG',	$configPath);
		define('ASSASSIN_PATH_APP_LBS',		$librariesPath);
		define('ASSASSIN_PATH_APP_TPL', 	$templatesPath);
		define('ASSASSIN_PATH_APP_TPC',		$templateCPath);
		
	}
	
	public static function route()
	{
		if (isset($_GET[ASSASSIN_APP]) && preg_match('/^\w*$/i', $_GET[ASSASSIN_APP]))
		{
			Configure::$app = $_GET[ASSASSIN_APP];
		}
		else
		{
			 Configure::getDefaultApp();
		}

		// 检查是否有 controller 参数，没有使用默认设置
		if (!isset($_GET[ASSASSIN_CONTROLLER])) 
		{

			if (isset(RouterConfig::$defaultRouter) && isset(RouterConfig::$defaultRouter[Configure::$app]['default_controller'])) 
			{
				$_GET[ASSASSIN_CONTROLLER] = RouterConfig::$defaultRouter[Configure::$app]['default_controller'];
			}
		}

		if (!isset($_GET[ASSASSIN_ACTION])) 
		{
			if (isset(RouterConfig::$defaultRouter) && isset(RouterConfig::$defaultRouter[Configure::$app]['default_action'][$_GET[ASSASSIN_CONTROLLER]])) 
			{
				$_GET[ASSASSIN_ACTION] = RouterConfig::$defaultRouter[Configure::$app]['default_action'][$_GET[ASSASSIN_CONTROLLER]];
			}
			else
			{
				$_GET[ASSASSIN_ACTION] = 'index';
			}
		}
		
		self::init(Configure::$app);
	}
}