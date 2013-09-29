<?php

class Db {

    private $_host;
    private $_user;
    private $_password;
    private $_database;
    private $_con;

    /**
     * @param string $host The host location of where the database can be found.
     * @param string $user The username of the database.
     * @param string $password The password of the database.
     * @param string $database The name of the database.
     */
    public function __construct($host, $user, $password, $database) {
        $this->_host = $host;
        $this->_user = $user;
        $this->_password = $password;
        $this->_database = $database;
        $this->_con = mysqli_connect($host, $user, $password, $database);
    }

    /**
     * Get data from database.
     * 
     * @param string $tableName The name of the table.
     * @param string/array $columns An string which seperate the columns names with a comma or an array with colomn names. Only these columns will be returned. Default is null (all columns).
     * @param string $where A string which you want to add to the end of an query (where/order by/limit/ect). Default is null.
     * @return array Returns array with found data.
     */
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
                return array('error' => true, 'response' => 'No results where found with the query:"' . $query . '"');
            } else {
                return array('data' => $data, 'error' => false);
            }
        } else {
            return array('error' => true, 'response' => mysqli_error($this->_con));
        }
    }

    /**
     * Get data from the database and only returns the first row.
     * 
     * @param string $tableName The name of the table.
     * @param string/array $columns An string which seperate the columns names with a comma or an array with colomn names. Only these columns will be returned. Default is null (all columns).
     * @param string $where A string which you want to add to the end of an query (where/order by/limit/ect). Default is null.
     * @return array Returns array with found data.
     */
    public function firstRow($tableName, $columns = null, $where = null) {
        $result = $this->select($tableName, $columns, $where);
        if (!$result['error']) {
            $data = array('data' => $result['data'][0], 'error' => $result['error']);
            return $data;
        } else {
            return $result;
        }
    }

    /**
     * Get data from the database and only returns the first cell.
     * 
     * @param string $tableName The name of the table.
     * @param string/array $columns An string which seperate the columns names with a comma or an array with colomn names. Only these columns will be returned. Default is null (all columns).
     * @param string $where A string which you want to add to the end of an query (where/order by/limit/ect). Default is null.
     * @return array Returns array with found data.
     */
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

    /**
     * For inserting data into the database.
     * 
     * @param string $tableName The name of the table.
     * @param array $data An array with data. The key's must be the names of the columns, the value's are the value's which will be insterted.
     * @return type Returns array with response (success/failure).
     */
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
            return array('error' => true, 'response' => mysqli_error($this->_con));
        } else {
            return array('error' => false, 'response' => 'Data was inserted successfully in the table `' . $this->escape($tableName) . '`');
        }
    }
    
    /**
     * Update a table.
     * 
     * @param string $tableName The name of the table.
     * @param array $data An array with data. The key's must be the names of the columns, the value's are the value's which will be insterted.
     * @param string $where A string which you want to add to the end of an query (where/order by/limit/ect). Default is null.
     * @return type Returns array with response (success/failure).
     */
    public function update($tableName, $data, $where) {
        $query = "UPDATE `" . $this->escape($tableName) . "` SET";

        $first = true;
        foreach ($data as $key => $value) {
            if ($first) {
                $query .= " `" . $this->escape($key) . "`='" . $this->escape($value) . "'";
                $first = false;
            } else {
                $query .= ", `" . $this->escape($key) . "`='" . $this->escape($value) . "'";
            }
        }
        
        $query .= ' ' . $where;

        mysqli_query($this->_con, $query);
        if (mysqli_error($this->_con)) {
            return array('error' => true, 'response' => mysqli_error($this->_con));
        } else {
            return array('error' => false, 'response' => 'Data was inserted successfully in the table `' . $this->escape($tableName) . '`');
        }
    }

    /**
     * Can be used for more advanced queries.
     * 
     * @param string $query The query which need to be runned.
     * @return array Returns the data if the query has any.
     */
    public function query($query) {
        $result = mysqli_query($this->_con, $query);
        $data = array();
        if ($result !== false) {
            while ($row = @mysqli_fetch_object($result)) {
                $data[] = $row;
            }
            if ($data === array()) {
                return array('error' => false, 'response' => 'Query was successfull!');
            } else {
                return array('data' => $data, 'error' => false);
            }
        } else {
            return array('error' => true, 'response' => mysqli_error($this->_con));
        }
    }

    /**
     * Prevent SQL injections.
     * 
     * @param string $string The string which needs to be checked.
     * @return string A string which can be used for an query, without the problem of SQL injections.
     */
    public function escape($string) {
        return mysqli_escape_string($this->_con, $string);
    }

}

?>