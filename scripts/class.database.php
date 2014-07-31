<?php

// database library for MyLocalSmallBusiness
// written by Ian Stewart (c) 2012 Whitedream Software
// created: 30 nov 2012
// modified: 9 feb 2013

require_once('user.settings.php');

class databaseexception extends Exception{};

class database {
  static public $instance;
  static protected $now;
  private $connection;

  function __construct() {
    $this->OpenConnection();
  }

  static public function StartInstance() {
    if (!isset(self::$instance)) {
      self::$instance = new database();
    }
    return self::$instance;
  }

  static public function GetNow() {
    if (!isset(self::$now)) {
      self::$now = date('Y-m-d');
    }
    return self::$now;
  }

  private function OpenConnection() {
    $this->connection = new mysqli(DATABASE_SERVER, DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_NAME);
    if ($this->connection->connect_errno) {
      throw new databaseexception(
        'Failed to connect to MySQL: (' . $this->connection->connect_errno . ') ' . $this->connection->connect_error);
    }
  }

  static public function RefreshTables() {
    return self::$instance->connection->refresh(MYSQLI_REFRESH_TABLES);
  }

  static public function LastInsertID() {
    return self::$instance->connection->insert_id;
  }

  static public function BuildSQL($display, $tables, $where = '', $order = '') {
    $query = sprintf('SELECT %s FROM %s', $display, $tables);
    if ($where != '') {
      $query .= ' WHERE ' . $where;
    }
    if ($order != '') {
      $query .= ' ORDER BY ' . $order;
    }
    return $query;
  }

  static public function Query($sql) {
    $result = self::StartInstance()->connection->query($sql);
    if (!$result) {
      throw new databaseexception('Database query failed: ' . self::$instance->connection->error . "\nSQL: $sql");
    }
    return $result;
  }

  static public function CountRows($table, $where = '') {
    $query = "SELECT COUNT(*) AS cnt FROM `{$table}`";
    if ($where) {
      $query .= ' WHERE ' . $where;
    }
    $result = self::Query($query);
    $line = $result->fetch_assoc();
    $result->free();
    return $line['cnt'];
  }

  static public function PopulateList($query, $field = 'id') {
    $list = array();
    $result = self::Query($query);
    while ($line = $result->fetch_assoc()) {
      $list[] = $line[$field];
    }
    $result->free();
    return $list;
  }

  static public function RetrieveLookupList(
    $tablename, $descriptionfield = FN_DESCRIPTION, $orderfield = FN_REF, $keyfield = FN_ID,
    $whereclause = "`status` = 'A'"
  ) {
    $query = 'SELECT `' . $keyfield . '`, `' . $descriptionfield . '` FROM `' . $tablename . '`' .
      (($whereclause == '') ? '' : ' WHERE ' . $whereclause) . 
      (($orderfield == '') ? '' : ' ORDER BY ' . $orderfield);
    return self::RetrieveLookupListByQuery($query, $keyfield, $descriptionfield);
  }

  static public function RetrieveLookupListByQuery($query, $keyfield = FN_ID, $descriptionfield = FN_DESCRIPTION) {
    $list = array();
    $result = self::Query($query);
    while ($line = $result->fetch_assoc()) {
      $id = $line[$keyfield];
      $desc = $line[$descriptionfield];
      $list[$id] = $desc;
    }
    $result->free();
    return $list;
  }

/*  static public function SelectFromTableByID($tblname, $id, $returnfield = '') {
    $query = "SELECT * FROM `{$tblname}` WHERE `id` = '{$id}'";
    $result = self::$instance->connection->query($query); // or die("Error whilst locating row from {$tblname}: " . mysql_error());
    if ($result->num_rows > 0) {
      $line = $result->fetch_assoc();
    } else {
      $line = false;
    }
    $result->free();
    if ($returnfield == '') {
      $ret = $line;
    } else {
      $ret = $line[$returnfield];
    }
    return $ret;
  } */

  static function SelectFromTableByRef($tblname, $ref, $where = '') {
    $query = "SELECT * FROM `{$tblname}` WHERE `ref` = '{$ref}'";
    if ($where != '') {
      $query .= ' AND ' . $where;
    }
    $result = self::Query($query); // or die("Error whilst locating row from {$tblname}: " . mysql_error());
    if ($result->num_rows > 0) {
      $line = $result->fetch_assoc();
    } else {
      $line = false;
    }
    $result->free();
    return $line;
  }

  static function SelectFromTableByField($tblname, $fldname, $fldvalue, $getfield = '*') {
    if (!is_numeric($fldvalue)) {
      $fldvalue = '"' . $fldvalue . '"';
    }
    $query = "SELECT {$getfield} FROM `{$tblname}` WHERE `{$fldname}` = {$fldvalue}";
    $result = self::Query($query); // or die("Error whilst locating row from {$tblname}: " . mysql_error());
    if ($result->num_rows > 0) {
      $line = $result->fetch_assoc();
      if ($getfield != '*') {
        $line = $line[$getfield];
      }
    } else {
      $line = false;
    }
    $result->free();
    return $line;
  }

  static public function SelectDescriptionFromLookup($tablename, $id) {
    return self::SelectFromTableByField($tablename, FN_ID, $id, FN_DESCRIPTION);
  }

/*  static public function UpdateRows($table, $columns, $where) {
    $ret = 'UPDATE `' . $table . '`';
    $cnt = count($columns);
    if ($cnt > 0) {
      $ret .= ' SET ';
      $list = array();
      foreach ($columns as $key => $value) {
        $list[] = '`' . $key . '` = "' . $value . '"';
      }
      
      
    }
    if ($where) {
      $ret .= ' WHERE ' . $where;
    }
    return $ret;
  } */

  static public function DeleteRows($table, $criteria) {
    $ret = 0;
    if ($critera != '') {
      try {
        $query = 'DELETE FROM `' . $table . '` WHERE ' . $criteria;
        $result = self::$instance->Query($query);
        $ret = $result->affected_rows();
      } catch (Exception $e) {
        throw new databaseexception('Could not delete from ' . $table . '. ' . $e->getMessage());
      }
    }
    return $ret;
  }

  static function TransactionBegin() {
    self::StartInstance()->connection->autocommit(false);
  }

  static function TransactionCommit() {
    self::StartInstance()->connection->commit();
    self::StartInstance()->connection->autocommit(true);
  }

  static function TransactionRollback() {
    self::StartInstance()->connection->rollback();
  }
}

// start the database as a singleton
database::StartInstance();
