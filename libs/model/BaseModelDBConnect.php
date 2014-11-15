<?php
/**
 * DB扩展类，负责数据库连接
 * @author libo
 * @date	2014年10月13日
 */
class BaseModelDBConnect {
	
	/**
	 * 数据库连接
	 * @var unknown
	 */
	protected static $links = array();
	
	protected static $linkConfig = array();

	protected static $database;
	
	/**
	 * 连接数据库
	 * @param unknown $DBName			数据库配置名
	 * @param string $master_or_slave	master：主库 | slave：从库
	 * @param unknown $DBConfig			数据库配置
	 * @param string $reConnect			保留
	 * @return unknown
	 */
	public static function connectDB($DBName ='', $master_or_slave = 'slave', $DBConfig = array(), $DBType = '',  $reConnect = FALSE)
	{
		
		$DB_ENV = ASSASSIN_ENV;
		in_array($DB_ENV, array('dev', 'test', 'product'), TRUE) || $DB_ENV = 'product';
		empty($DBName) && $DBName = ASSASSIN_DEFAULT_DB;

		$DBType = ASSASSIN_DBTYPE;
		in_array($DBType, array('mysql', 'mongo'), TRUE) || BaseModelMessage::showError('$DBType 配置错误');;

		$host = empty($DBConfig[$master_or_slave]['host']) ? DBConfig::$config[$DBType][$DBName][$DB_ENV][$master_or_slave]['host'] : $DBConfig[$master_or_slave]['host'] ;
		$port = !isset($DBConfig[$master_or_slave]['port']) || !is_numeric($DBConfig[$master_or_slave]['port']) ? DBConfig::$config[$DBType][$DBName][$DB_ENV][$master_or_slave]['port'] : $DBConfig[$master_or_slave]['port'];
		$username = empty($DBConfig[$master_or_slave]['username']) ? DBConfig::$config[$DBType][$DBName][$DB_ENV][$master_or_slave]['username'] : $DBConfig[$master_or_slave]['username'] ;
		$password = empty($DBConfig[$master_or_slave]['password']) ? DBConfig::$config[$DBType][$DBName][$DB_ENV][$master_or_slave]['password'] : $DBConfig[$master_or_slave]['password'] ;
		$database = empty($DBConfig[$master_or_slave]['database']) ? DBConfig::$config[$DBType][$DBName][$DB_ENV][$master_or_slave]['database'] : $DBConfig[$master_or_slave]['database'] ;
		$charset =  empty($DBConfig[$master_or_slave]['charset']) ? DBConfig::$config[$DBType][$DBName][$DB_ENV][$master_or_slave]['charset'] : $DBConfig[$master_or_slave]['charset'] ;

		$db_key = md5(implode('-', array($host, $username, $password, $database, $charset)));
		self::$linkConfig = array('host'=>$host, 'username'=>$username, 'password'=>$password, 'database'=>$database, 'charset'=>$charset);
		self::$database = $database;

		if (isset(self::$links[$db_key]) && !$reConnect) {
			return self::$links[$db_key];
		}

		switch ($DBType) {
			case 'msyql':
				return self::mysql_init($host, $username, $password, $database, $port, $charset);
				break;
			case 'mongo':
				return self::mongo_init($host, $username, $password, $database, $port);
				break;
		}
	}

	/**
	 * [mysql_init MySQL 数据库初始化连接]
	 * @param  [type] $host     [description]
	 * @param  [type] $username [description]
	 * @param  [type] $password [description]
	 * @param  [type] $database [description]
	 * @param  [type] $port     [description]
	 * @param  [type] $charset  [description]
	 * @return [type]           [description]
	 */
	private static function mysql_init($host, $username, $password, $database, $port, $charset)
	{
		$mysqli = mysqli_init();
		$mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, 60);
		if ($mysqli->real_connect($host, $username, $password, $database, $port))
		{
			$mysqli->set_charset($charset);
			self::$links[$db_key] = $mysqli;
			return self::$links[$db_key];
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * [mongo_init MongoDB 数据库初始化连接]
	 * @param  [type] $host     [description]
	 * @param  [type] $username [description]
	 * @param  [type] $password [description]
	 * @param  [type] $database [description]
	 * @param  [type] $port     [description]
	 * @return [type]           [description]
	 */
	private static function mongo_init($host, $username, $password, $database, $port)
	{
		if (!class_exists('mongo')) 
		{
			BaseModelMessage::showError('MongoDB 扩展不存在，请添加MongoDB扩展');
		}

		return new MongoClient("mongodb://{$username}:{$password}@{$host}:{$port}", array('db'=>$database));
	}
	

	/**
	 * 关闭数据库连接
	 * @param unknown $link
	 */
	public static function close_db(&$link)
	{
		if ($link)
		{
			mysqli_close($link);
		}
	}
	
	/**
	 * 获取当前数据库连接信息
	 * @return multitype:
	 */
	public static function getLinkInfo()
	{
		return self::$linkConfig;
	}

	/**
	 * [getDatabaseName 获得数据库名]
	 * @return [type] [description]
	 */
	public static function getDatabaseName()
	{
		return self::$database;
	}
}