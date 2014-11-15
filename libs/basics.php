<?php
class AssassinAutoload {

	/**
	 * 框架核心组件
	 */
	private static $coreFile = array(
		'BaseController'	=>	ASSASSIN_PATH_LIBS_CTL,
		'BaseModelRouter'	=>	ASSASSIN_PATH_LIBS_MODEL,
		'BaseModelCommon'	=>	ASSASSIN_PATH_LIBS_MODEL,
		'BaseModelDB'		=>	ASSASSIN_PATH_LIBS_MODEL,
		'BaseModelDBConnect'=>	ASSASSIN_PATH_LIBS_MODEL,
		'BaseModelMongoDB'	=>	ASSASSIN_PATH_LIBS_MODEL,
		'BaseModelPage'		=>	ASSASSIN_PATH_LIBS_MODEL,
		'BaseModelDebug'	=>	ASSASSIN_PATH_LIBS_MODEL,
		'BaseModelMessage'	=>	ASSASSIN_PATH_LIBS_MODEL,
		'BaseView'			=>	ASSASSIN_PATH_LIBS_VIEW,
		'Configure'			=>	ASSASSIN_PATH_CONFIG,
		'DBConfig'			=>	ASSASSIN_PATH_CONFIG,
		'RouterConfig'		=>	ASSASSIN_PATH_CONFIG,
		'FirePHP'			=>	array('path' => array(ASSASSIN_PATH_LIBS_MODEL, 'FirePHPCore/'), 'postfix' => '.class'),
	);
	
	public static function register()
	{
		echo 'register';
	}
	
	/**
	 *  文件引用函数
	 * @param unknown $prifixPath	文件的所在的路径
	 * @param unknown $filename		文件名
	 * @param string $postfix		文件的扩展名
	 */
	public static function includeFile($prifixPath, $filename, $postfix = '.php')
	{
		$_file = $prifixPath.$filename.$postfix;
		if (is_file($_file))
		{
			include($_file);
		}
		elseif (!include($_file)) 
		{
			defined('ASSASSIN_DEBUG') && BaseModelCommon::debug($filename . '类include文件' . $_file . '不存在，您现在使用APP为： app/' . Configure::$app . '/', 'error');	
			
			$trace = debug_backtrace();
			defined('ASSASSIN_DEBUG') && BaseModelCommon::debug($trace, __METHOD__);
			$filename = htmlspecialchars($filename);
			if ($filename == 'Controller') 
			{
				defined('ASSASSIN_DEBUG') && BaseModelCommon::debug(RouterConfig::$config, 'router_config');
				BaseModelMessage::showError('请在 RouteConfig.php 中配置 ' . htmlspecialchars(Configure::$app, ENT_QUOTES, 'UTF-8') . ' 的项目路由信息');
			}
			else
			{
				BaseModelMessage::showError($filename . ' 类不存在 '.htmlspecialchars(Configure::$app,  ENT_QUOTES, 'UTF-8').' 项目中');
			}
		}

	}
	
	/**
	 * 自动加载函数
	 * @param unknown $classname	要加载的类名
	 */
	public static function loader($classname)
	{
		
		if (!preg_match('/^[a-zA-Z][a-zA-Z0-9]*$/', $classname))
		{
			exit( $classname.' 类名格式错误');
		}
		
		if (isset(self::$coreFile[$classname]))
		{
			$path		= '';
			$filename	= $classname; 
			if (!is_array(self::$coreFile[$classname])) 
			{
				$path = self::$coreFile[$classname];
			}
			else
			{
				if (isset(self::$coreFile[$classname]['path'])) 
				{
					$path = implode('', self::$coreFile[$classname]['path']);
				}
				if (isset(self::$coreFile[$classname]['filename'])) 
				{
					$filename = self::$coreFile[$classname]['filename']; 
				}
				if (isset(self::$coreFile[$classname]['postfix'])) 
				{
					$filename .= self::$coreFile[$classname]['postfix']; 
				}
			}
			self::includeFile($path, $filename);
		}
		else
		{
			/**
			 * 自动加载项目中的类
			 */
			if (preg_grep('/.*Controller$/', array($classname)))
			{
				self::includeFile(ASSASSIN_PATH_APP_CLT, $classname);
			}
			elseif (preg_grep('/.*Model$/', array($classname)))
			{
				self::includeFile(ASSASSIN_PATH_APP_MODEL, $classname);
			}
			elseif (preg_grep('/.*ModelDB$/', array($classname)))
			{
				self::includeFile(ASSASSIN_PATH_APP_MODEL, $classname);
			}
			elseif (preg_grep('/.*Config$/', array($classname))) {
				self::includeFile(ASSASSIN_PATH_APP_CONFIG, $classname);
			}
			elseif (preg_grep('/.*Request$/', array($classname))) {
				self::includeFile(ASSASSIN_PATH_APP_LBS, $classname);
			}
			else
			{
				exit($classname.' 类名不存在');
			}
		}
	}
}

spl_autoload_register(array('AssassinAutoload', 'loader'));