<?php
/**
 * All rights reserved.
 * Category 表
 * @author          libo <191358832@qq.com>
 * @time            2014/11/6 17:05:37
 * @version         1.0.0
 */

class CategoryModelDB extends BaseModelMongoDB {
	
	public function __construct($db_name = NULL, $db_config = array())
	{
		parent::__construct($db_name, $db_config);
		parent::setTableName('category');
	}
}