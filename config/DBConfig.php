<?php

/**
 * 数据库配置数组
 */
$config = array(
	'mysql'	=>	array(
		// 默认数据库
		'default'	=> array(
			// 线上数据库
			'product'	=>	array(
				'master'	=> array(
					'host' 		=>	'127.0.0.1',
					'username' 	=>	'root',
					'password'	=>	'root',
					'database'	=>	'cob',
					'port'		=>	3306,
					'charset'	=>	'UTF-8'
				),
				'slave'		=>	array(
					'host' 		=>	'127.0.0.1',
					'username' 	=>	'root',
					'password'	=>	'root',
					'database'	=>	'cob',
					'port'		=>	3306,
					'charset'	=>	'UTF-8'
				)
			),
			
			// 测试数据库
			'test'	=>	array(),
			// 开发数据库
			'dev'	=>	array()
		)
	),
	'mongo'	=>	array(
		// 默认数据库
		'default'	=> array(
			// 线上数据库
			'product'	=>	array(
				'master'	=> array(
					'host' 		=>	'127.0.0.1',
					'username' 	=>	'navigation',
					'password'	=>	'123456',
					'database'	=>	'navigation',
					'port'		=>	27017,
					'charset'	=>	'UTF-8'
				),
				'slave'		=>	array(
					'host' 		=>	'127.0.0.1',
					'username' 	=>	'navigation',
					'password'	=>	'123456',
					'database'	=>	'navigation',
					'port'		=>	27017,
					'charset'	=>	'UTF-8'
				)
			),
			
			// 测试数据库
			'test'	=>	array(),
			// 开发数据库
			'dev'	=>	array()
		)
	),
		
	'memcache'	=>	array(),
	'radis'	=>	array()
	
);


class DBConfig {
	
	public static $config = array();
	
	public static function set($config)
	{
		self::$config = $config;
	}
}

DBConfig::set($config);

