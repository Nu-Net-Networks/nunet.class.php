<?php

/**
 * class sql: this is a helper class for building and working with sql
 *
 * @author Seth Rainsdon <seth.rainsdon@nunetnetworks.net>
 */
class sql {
    /*
     * function createSql($tableName, $dataArray) -- returns insert statment
     * 
     * $tableName - Name of table for isert
     * $dataArray - key => value pair of collom names and data for each.
     */

    private $connected;
    private $log;
    private $db;

    public function __construct() {
        $this->log = Logger::getLogger(__CLASS__);
        $this->connected = @mysql_ping() ? 1 : 0;
    }

    public function test() {
        return $this->connected;
    }

    public function connect($servername = "localhost", $username = "username", $password = "password") {

// Create connection
        $this->db = new mysqli($servername, $username, $password);

// Check connection
        if ($this->db->connect_error) {
            $this->log->error("Connection failed: " . $this->db->connect_error);
        } else {
            $this->log->trace("Connected successfully");
        }
    }

    public function createSql($tableName, $dataArray) {
        $this->log->debug("$tableName - " . json_encode($dataArray));
        $this->connected = @mysqli_ping($this->db) ? true : false;
        $colString = '';
        $valueString = '';
        if ($this->connected) {
            $this->log->debug("Connected will use mysql_real_escape_string");
        } else {
            $this->log->warn("Not Connected to a database the escaping of char will not be as secure");
        }
        foreach ($dataArray as $key => $value) {
            $colString .= "`$key`,";
            if ($this->connected) { // This will not work if there is no db connection but is safer
                $valueString .= "'" . mysqli_real_escape_string($this->db, $value) . "',";
            } else { // This will work if there is no db connection
                $search = array("\\", "\x00", "\n", "\r", "'", '"', "\x1a");
                $replace = array("\\\\", "\\0", "\\n", "\\r", "\'", '\"', "\\Z");
                $value = str_replace($search, $replace, $value);
                $valueString .= "'" . $value . "',";
            }
        }
        $colString = rtrim($colString, ",");
        $valueString = rtrim($valueString, ",");
        $sql = "INSERT INTO `$tableName` ($colString) VALUES ($valueString)";
        $this->log->debug($sql);
        return $sql;
    }

}
