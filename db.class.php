<?php

class Db {

    private $_host;
    private $_user;
    private $_password;
    private $_database;
    private $_con;

    public function __construct($host, $user, $password, $database) {
        $this->_host = $host;
        $this->_user = $user;
        $this->_password = $password;
        $this->_database = $database;
        $this->_con = mysqli_connect($host, $user, $password, $database);
    }

    public function select($tableName, $columns = null, $where = null) {
        if (!is_array($columns) && $columns !== null) {
            $columns = explode(',', $columns);
        }

        $query = "SELECT";

        if ($columns !== null) {
            $first = true;
            foreach ($columns as $column) {
                if ($first) {
                    $query .= " `" . $this->escape($column) . "`";
                    $first = false;
                } else {
                    $query .= ", `" . $this->escape($column) . "`";
                }
            }
        } else {
            $query .= " *";
        }

        $query .= " FROM `" . $this->escape($tableName) . "`";

        if ($where !== null) {
            $query .= " " . $where;
        }
        
        $result = mysqli_query($this->_con, $query);
        $data = array();
        while ($row = mysqli_fetch_object($result)) {
            $data[] = $row;
        }
        
        return $data;
    }
    
    public function firstRow($tableName, $columns = null, $where = null) {
        $data = $this->select($tableName, $columns, $where);
        return $data[0];
    }
    
    public function firstCell($tableName, $columns = null, $where = null) {
        $data = $this->firstRow($tableName, $columns, $where);
        foreach ($data as $column) {
            return $column;
        }
    }
    
    public function insert($tableName, $data) {
        $query = "INSERT INTO `" . $this->escape($tableName) . "` SET";
        
        $first = true;
        foreach ($data as $key => $value) {
            if ($first) {
                $query .= " `" . $this->escape($key) . "`='" . $this->escape($value) . "'";
                $first = false;
            } else {
                $query .= ", `" . $this->escape($key) . "`='" . $this->escape($value) . "'";
            }
        }
        
        mysqli_query($this->_con, $query);
    }

    private function escape($string) {
        return $string;
    }

}

?>