<?php

/**
 * Mysql操作类
 * @author leshen <986992484@qq.com>
 * @version 1.1
 */
class Mysql {

    private $db_host;       //数据库主机
    private $db_user;       //数据库登陆名
    private $db_pwd;        //数据库登陆密码
    private $db_name;       //数据库名
    private $db_charset;    //数据库字符编码
    private $db_pconn;      //长连接标识位
    private $debug;         //调试开启
    private $conn;          //数据库连接标识
    private $msg = "";      //数据库操纵信息

    /**
     * 构造方法，初始化数据库信息，然后连接数据库
     * @param string  $db_host    数据库主机
     * @param string  $db_user    数据库登陆名
     * @param string  $db_pwd     数据库登陆密码
     * @param string  $db_name    数据库名
     * @param string  $db_chaeset 数据库字符编码
     * @param boolean $db_pconn   长连接标识位
     * @param boolean $debug      调试开启
     */
    public function __construct($db_host, $db_user, $db_pwd, $db_name, $db_chaeset = 'utf8', $db_pconn = false, $debug = false) {
        $this->db_host = $db_host;
        $this->db_user = $db_user;
        $this->db_pwd = $db_pwd;
        $this->db_name = $db_name;
        $this->db_charset = $db_chaeset;
        $this->db_pconn = $db_pconn;
        $this->result = '';
        $this->debug = $debug;
        $this->initConnect();
    }

    /**
     * 连接数据库
     * @return void
     */
    public function initConnect() {
        if ($this->db_pconn) {
            $this->conn = @mysql_pconnect($this->db_host, $this->db_user, $this->db_pwd);
        } else {
            $this->conn = @mysql_connect($this->db_host, $this->db_user, $this->db_pwd);
        }
        if ($this->conn) {
            $this->query("SET NAMES " . $this->db_charset);
        } else {
            $this->msg = "数据库连接出错，错误编号：" . mysql_errno() . "错误原因：" . mysql_error();
        }
        $this->selectDb($this->db_name);
    }

    /**
     * 选择数据库
     * @param  string $dbname 数据库名
     * @return void
     */
    public function selectDb($dbname) {
        if ($dbname == "") {
            $this->db_name = $dbname;
        }
        if (!mysql_select_db($this->db_name, $this->conn)) {
            $this->msg = "数据库不可用";
        }
    }

    /**
     * 数据库执行语句
     * @param  string  $sql   sql语句
     * @param  boolean $debug 调试开启
     * @return array   由结果集转化而成的二维数组
     */
    public function query($sql, $debug = false) {
        if (!$debug) {
            $this->result = @mysql_query($sql, $this->conn);
            return $this->fetchArray(MYSQL_ASSOC);
        }
        if ($this->result == false) {
            $this->msg ="sql执行出错，错误编号：". mysql_errno() ."错误原因：". mysql_error();
        }
    }

    /**
     * 查询
     * @param  string $tableName  表名
     * @param  string $columnName 字段名
     * @param  string $where      条件
     * @return array
     */
    public function select($tableName, $columnName = "*", $where = "") {
        $sql = "SELECT " . $columnName . " FROM " . $tableName;
        $sql .= $where ? " WHERE " . $where : null;
        return $this->query($sql);
    }

    /**
     * 查询所有记录
     * @param  string $tableName  表名
     * @return array
     */
    public function findAll($tableName) {
        $sql = "SELECT * FROM $tableName";
        $this->query($sql);
    }

    /**
     * 查询一条记录
     * @param  string $tableName  表名
     * @param  string $columnName 字段名
     * @param  string $where      条件
     * @return array
     */
    public function findOne($tableName,$columnName='*',$where=""){
        $sql = "SELECT " . $columnName . " FROM " . $tableName;
        $sql .= $where ? " WHERE " . $where : null;
        $sql.= ' limit 1 ';
        $data=$this->query($sql);
        return $data? $data[0] : null;
    }

    /**
     * 新增一条记录
     * @param  string $tableName  表名
     * @param  string $column 数据
     * @return boolean
     */
    public function insert($tableName, $column = array()) {
        $columnName = "";
        $columnValue = "";
        foreach ($column as $key => $value) {
            $columnName .= $key . ",";
            $columnValue .= "'" . $value . "',";
        }
        $columnName = substr($columnName, 0, strlen($columnName) - 1);
        $columnValue = substr($columnValue, 0, strlen($columnValue) - 1);
        $sql = "INSERT INTO $tableName($columnName) VALUES($columnValue)";
        $bool= @mysql_query($sql, $this->conn);
        return $bool;
    }

    /**
     * 更新一条记录
     * @param  string $tableName 表名
     * @param  array  $column    数据
     * @param  string $where     条件
     * @return void
     */
    public function update($tableName, $column = array(), $where = "") {
        $updateValue = "";
        foreach ($column as $key => $value) {
            $updateValue .= $key . "='" . $value . "',";
        }
        $updateValue = substr($updateValue, 0, strlen($updateValue) - 1);
        $sql = "UPDATE $tableName SET $updateValue";
        $sql .= $where ? " WHERE $where" : null;
        $this->query($sql);
        if($this->result){
            $this->msg = "数据更新成功。受影响行数：" . mysql_affected_rows($this->conn);
        }
    }

    /**
     * 删除一条记录
     * @param  string $tableName 表名
     * @param  string $where     条件
     * @return void
     */
    public function delete($tableName, $where = ""){
        $sql = "DELETE FROM $tableName";
        $sql .= $where ? " WHERE $where" : null;
        $this->query($sql);
        if($this->result){
            $this->msg = "数据删除成功。受影响行数：" . mysql_affected_rows($this->conn);
        }
    }

    /**
     * 将结果集转化成二维数组
     * @param  string $result_type 结果集类型
     * @return array
     */
    public function fetchArray($result_type = MYSQL_BOTH){
        $resultArray = array();
        while(!!$row=@mysql_fetch_array($this->result,$result_type)){
            $resultArray[]=$row;
        }
        return $resultArray;
    }

    /**
     * 打印消息
     * @return string
     */
    public function printMessage(){
        return $this->msg;
    }

    /**
     * 释放结果集
     * @return void
     */
    public function freeResult(){
        @mysql_free_result($this->result);
    }

    /**
     * 析构函数，释放结果集并关闭数据库连接
     */
    public function __destruct() {
        if(!empty($this->result)){
            $this->freeResult();
        }
        mysql_close($this->conn);
    }
}

