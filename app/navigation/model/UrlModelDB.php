<?php
/**
 * All rights reserved.
 * URL 表
 * @author          libo <191358832@qq.com>
 * @time            2014/11/6 17:10:35
 * @version         1.0.0
 */

class UrlModelDB extends BaseModelMongoDB {
	
	public function __construct($db_name = NULL, $db_config = array())
	{
		parent::__construct($db_name, $db_config);
		parent::setTableName('url');
	}
}