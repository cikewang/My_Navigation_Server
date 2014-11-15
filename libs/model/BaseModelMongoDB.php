<?php
/**
 * All rights reserved.
 * MongoDB 基础类
 * @author          libo <191358832@qq.com>
 * @time            2014/11/6 14:41:56
 * @version         1.0.0
 */

class BaseModelMongoDB {
	
	/**
	 * 指定数据库配置
	 * @var array
	 */
	protected $DBConfig = array();

	/**
	 * 数据库名
	 * @var [type]
	 */
	protected $DBName;

	/**
	 * 数据库连接资源描述符
	 * @var [type]
	 */
	protected $link = NULL;

	/**
	 * 表名
	 * @var [type]
	 */
	protected $tableName;

	/**
	 * [$db mongo 数据库连接资源]
	 * @var [type]
	 */
	protected $db;

	/**
	 * [$DBtype 使用mongo数据库]
	 * @var string
	 */
	protected $DBtype = 'mongo';

	public function __construct($DBName, $DBConfig = array())
	{
		$this->DBName = $DBName;
		$this->DBConfig = $DBConfig;
	}

	/**
	 * [find 查询表中所有数据]
	 * @return [type] [description]
	 */
	public function find($where = array(), $fields = array(), $master_or_slave = 'slave')
	{
		$this->_sendQuery($master_or_slave);
		$data = $this->db->find($where, $fields);

		if (empty($data)) {
			return FALSE;
		}
		$d = array();
		foreach ($data as $key => $value) {
			$d[] = $value;
		}
		return $d;
	}

	/**
	 * [findOne description]
	 * @param  array  $where           [description]
	 * @param  array  $fields          [description]
	 * @param  string $master_or_slave [description]
	 * @return [type]                  [description]
	 */
	public function findOne($where = array(), $fields = array(), $master_or_slave ='slave')
	{
		$this->_sendQuery($master_or_slave);
		return $this->db->findOne($where, $fields);
	}

	/**
	 * [insert 插入数据]
	 * @param  array  $data [description]
	 * @return [type]       [description]
	 */
	public function insert($data = array(), $master_or_slave = 'slave')
	{
		$this->_sendQuery($master_or_slave);
		return $this->db->insert($data);
	}

	/**
	 * [update 更新数据]
	 * @param  [type] $data  [description]
	 * @param  [type] $where [description]
	 * @return [type]        [description]
	 */
	public function update($data, $where,  $master_or_slave = 'slave')
	{
		$this->_sendQuery($master_or_slave);
		return $this->db->update($where, $data);
	}

	private function _sendQuery($master_or_slave ='slave', &$result = array())
	{
		$this->checkLink($master_or_slave);
		if (empty($this->link)) 
		{
			BaseModelMessage::showError('MongoDB 资源连接错误');
		}

		$databaseName = $this->getDatabaseName();
		$tableName = $this->getTableName();
		$this->db = $this->link->$databaseName->$tableName;
	}

	/**
	 * [getNextId 获得表中下一个ID]
	 * @param  [type] $db [description]
	 * @return [type]     [description]
	 */
	public function getNextId($master_or_slave = 'slave')
	{
		$this->_sendQuery($master_or_slave);
		$count = $this->db->count();
		return ++$count;
	}

	public function checkLink($master_or_slave = 'slave', $reConnect = FALSE)
    {
    	$this->link = BaseModelDBConnect::connectDB($this->DBName, $master_or_slave, $this->DBConfig, $this->DBtype, $reConnect);
    }

	/**
	 * 设置表名
	 * @param [string] $tableName [表名]
	 */
	public function setTableName($tableName)
    {
    	$this->tableName  = $tableName;
    }

    /**
     * 获取表名
     * @return [string] [表名]
     */
    public function getTableName()
    {
    	return $this->tableName;
    }

    public function getDatabaseName()
    {
    	return BaseModelDBConnect::getDatabaseName();
    }

}