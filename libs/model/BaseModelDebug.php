<?php

class BaseModelDebug {
	
	public static function debug($value, $type = 'DEBUG', $verbose = FALSE, $encoding = 'UTF-8')
	{
		if (strtoupper($encoding) !== 'UTF-8') 
		{
			$value = BaseModelCommon::converEncoding($value, 'UTF-8', $encoding);
			$type  = BaseModelCommon::converEncoding($type, 'UTF-8', $encoding);
		}

		if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'FirePHP') !== FALSE)
		{
			if ($type === 'db_sql_master' || substr($type, -4) === 'warn') 
			{
				FirePHP::getInstance(true)->warn($value, $type);
			}
			elseif (in_array( $type , array('db_sql_result', 'request_return', 'request_multi_return', 'all_info', 'assassin_error_trace'), true) || strpos($type, 'redis_call_') === 0) 
			{
				FirePHP::getInstance(true)->table($value, $type);
			}
			elseif (substr($type, -5) === 'trace') 
			{
				FirePHP::getInstance(true)->trace($value, $type);
			}
			elseif (substr($type, -5) === 'error') 
			{
				FirePHP::getInstance(true)->error($value, $type);
			}
			elseif (substr($type, -4) === 'info') {
				FirePHP::getInstance(true)->info($value, $type);
			}
			else
			{
				FirePHP::getInstance(true)->log($value, $type);
			}
		}
	}
}