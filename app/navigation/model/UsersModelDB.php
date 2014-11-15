<?php
/**
 * All rights reserved.
 * Users 表
 * @author          libo <191358832@qq.com>
 * @time            2014/11/13 17:22:40
 * @version         1.0.0
 */

class UsersModelDB extends BaseModelMongoDB {
	
	public function __construct($db_name = NULL, $db_config = array())
	{
		parent::__construct($db_name, $db_config);
		parent::setTableName('users');
	}
}