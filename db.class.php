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
        if ($result !== false) {
            while ($row = mysqli_fetch_object($result)) {
                $data[] = $row;
            }
            if ($data === array()) { 
                return array('error' => true, 'errorMsg' => 'No results where found with the query:"' . $query . '"');
            } else {
                return array('data' => $data, 'error' => false);
            }
        } else {
            return array('error' => true, 'errorMsg' => mysqli_error($this->_con));
        }
    }

    public function firstRow($tableName, $columns = null, $where = null) {
        $result = $this->select($tableName, $columns, $where);
        if (!$result['error']) {
            $data = array('data' => $result['data'][0], 'error' => $result['error']);
            return $data;
        } else {
            return $result;
        }
    }

    public function firstCell($tableName, $columns = null, $where = null) {
        $result = $this->firstRow($tableName, $columns, $where);
        if (!$result['error']) {
            $data = $result['data'];
            foreach ($data as $column) {
                return array('data' => $column, 'error' => $result['error']);
            }
        } else {
            return $result;
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
        if (mysqli_error($this->_con)) {
            return array('error' => true, 'errorMsg' => mysqli_error($this->_con));
        } else {
            return array('error' => false, 'response' => 'Data was inserted successfully in the table `' . $this->escape($tableName) . '`');
        }
    }

    private function escape($string) {
        return mysqli_escape_string($this->_con, $string);
    }

}

?>