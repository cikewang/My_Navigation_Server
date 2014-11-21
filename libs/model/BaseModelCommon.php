<?php
class BaseModelCommon {
	
	/**
	 * 递归创建目录
	 * @param unknown $pathname	文件路径
	 * @param number $mode		权限
	 * @return boolean			
	 */
	public static function recursiveMkdir($pathname, $mode = 0755)
	{
		return is_dir($pathname) ? TRUE : mkdir($pathname, $mode, TRUE);
	}
	
	/**
	 * 类/函数名构造
	 * @param unknown $filename		传人名
	 * @param string $type			类型：function | class
	 * @return string				类/函数名
	 */
	public static function getFormatName($name, $type = 'function')
	{
		$name = array_map('ucfirst',explode('_', $name));
		if ('function' == $type)
		{
			$name[0] = strtolower($name[0]);
		}

		return implode('', $name);
	}

	/**
	 * [arr_filter 递归过滤数组中值，将特殊字符转换为HTML实体]
	 * @param  [type] $arr [传入的数组参数]
	 * @return [type]      [返回过滤后的数组]
	 */
	public static function filterArr(&$arr) 
	{
		if (!is_array ($arr)) 
		{
			return false;
		}
		
		foreach ($arr as $key => &$val ) 
		{
			if (is_array ($val)) 
			{
				self::filterArr($val);
			} 
			else 
			{
				$val = htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
			}
		}
		return $arr;
	}

	/**
	 * [converEncoding 转码函数]
	 * @param  [type]  $value        [需要转码的参数]
	 * @param  [type]  $toEncoding   [输出编码]
	 * @param  [type]  $fromEncoding [传入编码]
	 * @param  boolean $toArray      [是否将object转为数组输出]
	 * @return [type]                [description]
	 */
	public static function converEncoding($value, $toEncoding, $fromEncoding, $toArray = FALSE)
	{
		if (!is_array($value) && !is_object($value)) {
			$data = mb_convert_encoding($data, $toEncoding, $fromEncoding);
		}

		return $data;
	}

	public static function debug($value, $type='DEBUG', $verbose = FALSE, $encoding = 'UTF-8')
	{
		if (defined('ASSASSIN_DEBUG') && ASSASSIN_DEBUG === 1 && defined('ASSASSIN_ENV') && ASSASSIN_ENV !== 'product') 
		{
			BaseModelDebug::debug($value, $type, $verbose, $encoding);
		}
		
	}
	
	/**
	 * [filterMongoData 过滤MongoDB数据，主要转换Mongo ID]
	 * @param  [type] $arr [description]
	 * @return [type]      [description]
	 */
	public static function filterMongoData(&$arr) 
	{
		if (!is_array ($arr)) 
		{
			return false;
		}
		
		foreach ($arr as $key => &$val ) 
		{
			if (is_array ($val)) 
			{
				self::filterMongoData($val);
			}
			elseif(is_object($val) && $key == '_id')
			{
				$id_arr = (array)$val;
				$val = htmlspecialchars($id_arr['$id'], ENT_QUOTES, 'UTF-8');
			} 
			else 
			{
				$val = htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
			}
		}
		return $arr;
	}
	
}