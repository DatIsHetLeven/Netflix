<?php


class Database{
    private $dbHost = DB_HOST;
    private $dbUsername = DB_USERNAME;
    private $dbPassword = DB_PASSWORD;
    private $dbName = DB_NAME;

    private $statement;
    private $dbHandler;
    private $error;

    private static $databaseConnection = null;

    public function __construct() {
        $conn = 'mysql:host=' . $this->dbHost . ';dbname=' . $this->dbName;

        try{
            if (self::$databaseConnection === null) {
                self::$databaseConnection = new PDO($conn, $this->dbUsername, $this->dbPassword);
                self::$databaseConnection->setAttribute(PDO::ATTR_PERSISTENT, true);
                self::$databaseConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }

            $this->dbHandler = self::$databaseConnection;
        } catch (PDOException $e){
            $this->error = $e->getMessage();
            echo $this->error;
        }
    }

    // om quries te schrijven
    public function query($sql){
        $this->statement = $this->dbHandler->prepare($sql);
    }

    // bind values
    public function bind($parameter, $value, $type = null){
        switch(is_null($type)){
            case is_int($value):
                $type = PDO::PARAM_INT;
                break;
            case is_bool($value):
                $type = PDO::PARAM_BOOL;
                break;
            case is_null($value):
                $type = PDO::PARAM_NULL;
                break;
            default:
            $type = PDO::PARAM_STR;
        }

        $this->statement->bindValue($parameter, $value, $type);
    }

    // execute the prepared statement
    public function execute(){
        return $this->statement->execute();
    }

    // return an array 
    public function resultSet(){
        $this->execute();
        return $this->statement->fetchAll(PDO::FETCH_OBJ);
    }

    // return one row
    public function single(){
        $this->execute();
        return $this->statement->fetch(PDO::FETCH_OBJ);
    }

    // get the row count
    public function rowCount(){
        return $this->statement->rowCount();
    }

    public function errorInfo() {
        return $this->dbHandler->errorInfo();
    }

    protected function parseDate($string) {
        return date('d/M/Y H:i:s', strtotime($string));
    }

    protected function toDateString($date) {
        return $date->format('Y-m-d H:i:s');
    }

    public function startTransaction() {
        $this->dbHandler->beginTransaction();
    }

    public function commit() {
        $this->dbHandler->commit();
    }

    public function rollback() {
        $this->dbHandler->rollBack();
    }

    public function getLastInsertedId() {
        $this->query(
            "SELECT LAST_INSERT_ID() AS `LastId`;"
        );

        $resultRow = $this->single();
        $lastId = intval($resultRow->LastId);

        if ($lastId === 0) return null;
        return $lastId;
    }
}