<?php
/*
 * DataConnection class is to handle the database connection and queries
 */
class DataConnection {
    private $conn;
    private $host     = "localhost";

    // below information should be defined in environment variables and get from environment variables
    private $db       = "";
    private $username = "";
    private $password = "";

    function __construct() {
        $this->db       = getenv("DB_NAME");
        $this->username = getenv("DB_USERNAME");
        $this->password = getenv("DB_PASSWORD");
    }

    // Create database connection
    function connect() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db};charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ];
            
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            error_log("Connection failed: " . $e->getMessage());
            throw $e;
        }
    }

   /**
     * Execute a prepared update query
     * @param string $query The SQL query with placeholders
     * @param array $params Array of parameters to bind
     * @return bool|int Returns false on failure, number of affected rows on success
     */
    public function executeUpdate($query, $params = []) {
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            error_log("Prepared update query failed: " . $e->getMessage() . ", Query: " . $query);
            return false;
        }
    }

    /**
     * Execute a prepared select query
     * @param string $query The SQL query with placeholders
     * @param array $params Array of parameters to bind
     * @return array Returns array of results or empty array on failure
     */
    public function executeSelect($query, $params = []) {
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Prepared select query failed: " . $e->getMessage() . ", Query: " . $query);
            return [];
        }
    }

    /**
     * Execute a prepared select query and return single row
     * @param string $query The SQL query with placeholders
     * @param array $params Array of parameters to bind
     * @return array|null Returns single row or null on failure
     */
    public function executeSelectSingle($query, $params = []) {
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Prepared select single query failed: " . $e->getMessage() . ", Query: " . $query);
            return null;
        }
    }

    public function executeSelectSingleValue($query, $params = []) {
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchColumn();
        } catch (PDOException $e) { 
            error_log("Prepared select single value query failed: " . $e->getMessage() . ", Query: " . $query);
            return null;
        }
    }

    function executeCount($query, $params = []) {
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            error_log("Count query failed: " . $e->getMessage().", Query:".$query);
            return 0;
        }
    }

    // Close connection
    function close() {
        if($this->conn) {
            $this->conn = null;
        }
    }
}