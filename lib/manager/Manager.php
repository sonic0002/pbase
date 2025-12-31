<?php 
$DOCUMENT_ROOT = $_SERVER['DOCUMENT_ROOT'];
require_once $DOCUMENT_ROOT.'/lib/DataConnection.php';

class Manager {
    private $conn;

    function __construct() {
        $this->conn = new DataConnection();
    }

    function __destruct() {
        $this->close();
    }

    function close() {
        if($this->conn) {
            $this->conn->close();
            $this->conn = null;
        }
    }
}