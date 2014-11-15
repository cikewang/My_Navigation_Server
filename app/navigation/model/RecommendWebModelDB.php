<?php
/**
 * All rights reserved.
 * RecommendWeb 表
 * @author          libo <191358832@qq.com>
 * @time            2014/11/15 16:19:23
 * @version         1.0.0
 */

class RecommendWebModelDB extends BaseModelMongoDB {
	
	public function __construct($db_name = NULL, $db_config = array())
	{
		parent::__construct($db_name, $db_config);
		parent::setTableName('recommend_web');
	}
}