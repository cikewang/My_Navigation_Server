<?php

class Configure {
	
	/**
	 * 当前选择的ASSASSIN_APP
	 * @var unknown
	 */
	public static $app = null;
	
	public function __construct()
	{
	}
	/**
	 * 项目选着规则
	 */
	public static function getDefaultApp()
	{
		if (strpos($_SERVER['HTTP_HOST'], 'admin') !== FALSE)
		{
			self::$app = ASSASSIN_APP_ADMIN;
		}
		else
		{
			self::$app = ASSASSIN_APP_DEFAULT;
		}
	}
	
	public function __destruct()
	{
		self::$app = null;
	}
	
}