<?php

/*
 * @author Henrique Valcanaia
 * @date Sep 30, 2014, 2:23:46 PM 
 */

include_once 'IDAO.php';

abstract class DAO implements IDAO {

    protected $_con = null;
    protected $_host = "you.host.name.com";
    protected $_dbname = "dbname";
    protected $_user = "dbusername";
    protected $_password = "dbpwd";
    protected $_table;

    function __construct() {
        
    }

    private function openConnection() {
        if ($this->_con == null || !$this->_con) {
            try {
                $this->_con = new PDO(
                        'mysql:host=' . $this->_host .
                        ';dbname=' . $this->_dbname .
                        ';charset=utf-8', $this->_user, $this->_password
                );

                $this->_con->exec("SET NAMES utf8");
                $this->_con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                if (!$this->_con)
                    die("Error");

                return $this->_con;
            } catch (PDOException $e) {
                echo $e->getLine() . " " . $e->getMessage();
                exit();
            }
        }
    }

    protected function closeConnection() {
        if ($this->_con != null)
            $this->_con = null;
    }

    public function __destruct() {
        $this->closeConnection();
    }

    public function getTable() {
        return $this->_table;
    }

    public function setTable($table) {
        $this->_table = $table;
    }

    public function load($fields = "*", $where = null, $order = "", $add = "") {
        if (strlen($add) > 0)
            $add = " " . $add;

        $sql = "SELECT %s FROM %s";
        if ($where != null) {
            if (is_array($where)) {
                foreach ($where as $key => $value) {
                    $whereText .= " AND $key = $value";
                }
                $whereText = substr($whereText, 5); // remove first ' AND '
            } else {
                $whereText = $where;
            }
            $sql .= " WHERE $whereText";
        }

        $sql = sprintf($sql . $order, $campos, $this->getTabela() . $add);

        $stmt = $this->execSQL($sql);
        $ret = array();
        foreach ($stmt->fetchAll() as $row) {
            $ret[] = $row;
        }
        unset($stmt);
        unset($result);

        return $ret;
    }

    public function insert($array) {
        foreach ($array as $key => $value) {
            $fields .= ", " . strtoupper($key);
            $vals .= ", '$value'";
        }
        $fields = substr($fields, 2);
        $vals = substr($vals, 2);

        $sql = "INSERT INTO " . $this->getTabela() . " ($fields) VALUES ($vals)";

        $stmt = $this->execSQL($sql);
    }

    public function update($vals, $where = null) {
        if (is_array($vals)) {
            foreach ($vals as $key => $value) {
                $fieldsVals .= ", $key = '$value'";
            }
            $fieldsVals = substr($fieldsVals, 2);
        } else {
            $fieldsVals = $vals;
        }

        $sql = "UPDATE " . $this->getTabela() . " SET $fieldsVals";

        if (isset($where)) {
            $sql .= " WHERE ";
            if (is_array($where)) {
                foreach ($where as $key => $value) {
                    $sql .= "AND $key = '$value'";
                }
                $sql = substr($sql, strlen($sql) - 3); // remove first AND
            } else {
                $sql .= " $where";
            }
        }
        $stmt = $this->execSQL($sql);
    }

    public function delete($where = null) {
        $sql = "DELETE FROM " . $this->getTabela();
        if (isset($where)) {
            $sql .= " WHERE ";
            if (is_array($where)) {
                foreach ($where as $key => $value) {
                    $sql .= "AND $key = '$value'";
                }
                $sql = substr($sql, strlen($sql) - 3); // remove first AND
            } else {
                $sql .= " $where";
            }
        }
        $stmt = $this->execSQL($sql);
    }

    private function execSQL($sql) {
        if ($this->_con == null || !$this->_con)
            $this->openConnection();

        $stmt = $this->_con->prepare($sql);
        if (!$stmt)
            die('Error preparing SQL: ' . $sql);

        if (!$stmt->execute()) {
            echo "Error in SQL execution";
            echo "SQL: " . $sql;
            echo "Error code: " . $stmt->errno;
            echo "Error: " . $stmt->error;
            die('Abort');
        }

        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);

        if (!$result) {
            die('Error runing query: ' . $sql);
        }
        unset($result);
        return $stmt;
    }

}
