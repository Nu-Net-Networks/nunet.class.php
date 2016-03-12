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
        $this->db = new PDO("mysql:host=$servername;dbname=TBS;", $username, $password, array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        return $this->db;
    }

    public function checkPass($username, $password) {
        $sth = $this->db->prepare('SELECT Pass FROM Users WHERE UserName = :username LIMIT 1');

        $sth->bindParam(':username', $username);

        $sth->execute();

        $user = $sth->fetch(PDO::FETCH_OBJ);
        $hash = crypt($password . $username, $user->Pass);
        echo $user->Pass . "\n<br />$hash\n<br />";
// Hashing the password with its hash as the salt returns the same hash
        if ($this->hash_equals($user->Pass, $hash)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

  private function hash_equals($str1, $str2) {
    if(strlen($str1) != strlen($str2)) {
      return false;
    } else {
      $res = $str1 ^ $str2;
      $ret = 0;
      for($i = strlen($res) - 1; $i >= 0; $i--) $ret |= ord($res[$i]);
      return !$ret;
    }
  }
}