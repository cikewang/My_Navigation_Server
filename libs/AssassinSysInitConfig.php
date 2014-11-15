<?php
define('ASSASSIN_VERSION', '1.0');

//************************************************项目运行环境*******************************************************************//
defined('ASSASSIN_ENV') 				||	define('ASSASSIN_ENV',				'development');	// 运行环境，生产环境：production，测试环境：testing，开发环境：development
defined('ASSASSIN_DEFAULT_DB') 			||	define('ASSASSIN_DEFAULT_DB',		'default');		// 数据库选择配置
defined('ASSASSIN_DBTYPE')				||	define('ASSASSIN_DBTYPE',			'mongo');		// 数据库类型： mysql,mongo


//************************************************应用名称*******************************************************************//
defined('ASSASSIN_APP_DEFAULT') 		||	define('ASSASSIN_APP_DEFAULT',		'navigation');		// 默认项目


//************************************************应用\控制器\方法参数名*******************************************************************//
defined('ASSASSIN_PATH_APP') 			||	define('ASSASSIN_PATH_APP',			ASSASSIN_PATH.'app/');
defined('ASSASSIN_PATH_CONFIG') 		||	define('ASSASSIN_PATH_CONFIG', 		ASSASSIN_PATH.'config/');
defined('ASSASSIN_PATH_MODEL')			||	define('ASSASSIN_PATH_MODEL', 		ASSASSIN_PATH.'model/');
defined('ASSASSIN_PATH_LIBS') 			||	define('ASSASSIN_PATH_LIBS', 		ASSASSIN_PATH.'libs/');
defined('ASSASSIN_PATH_LIBS_CTL')		||	define('ASSASSIN_PATH_LIBS_CTL',	ASSASSIN_PATH_LIBS.'controller/');
defined('ASSASSIN_PATH_LIBS_MODEL')		||	define('ASSASSIN_PATH_LIBS_MODEL', 	ASSASSIN_PATH_LIBS.'model/');
defined('ASSASSIN_PATH_LIBS_VIEW')		||	define('ASSASSIN_PATH_LIBS_VIEW', 	ASSASSIN_PATH_LIBS.'view/');

//************************************************缓存目录*******************************************************************//
defined('ASSASSIN_PATH_CACHE') 			||	define('ASSASSIN_PATH_CACHE', 		ASSASSIN_PATH.'cache/');


//************************************************调试 DEBUG*******************************************************************//
defined('ASSASSIN_DEBUG')				||	define('ASSASSIN_DEBUG', 				1);		//DEBUG 模式 0:关,1:开			
defined('ASSASSIN_TIMEOUT')				||	define('ASSASSIN_TIMEOUT', 				5);		//默认脚本执行超时报警时间(秒)	
defined('ASSASSIN_DBCONNECT_TIMEOUT') 	||	define('ASSASSIN_DBCONNECT_TIMEOUT',   0.5);	// 默认数据库连接时间过长报警时间（秒）

//************************************************应用\控制器\方法参数名*******************************************************************//
defined('ASSASSIN_APP')					||	define('ASSASSIN_APP', 'p');
defined('ASSASSIN_CONTROLLER') 			||	define('ASSASSIN_CONTROLLER', 'c');
defined('ASSASSIN_ACTION')				||	define('ASSASSIN_ACTION', 'a');