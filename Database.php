<?php
/**
 * Database Wrapper Class
 * Menggunakan Prepared Statements untuk security
 */

class Database {
    private $conn;
    private $config;
    
    public function __construct($config) {
        $this->config = $config;
        $this->connect();
    }
    
    /**
     * Connect to database
     */
    private function connect() {
        try {
            $this->conn = new mysqli(
                $this->config['host'],
                $this->config['user'],
                $this->config['pass'],
                $this->config['db']
            );
            
            // Check connection
            if ($this->conn->connect_error) {
                throw new Exception("Database connection failed: " . $this->conn->connect_error);
            }
            
            // Set charset
            $this->conn->set_charset($this->config['charset']);
            
        } catch (Exception $e) {
            error_log("[DB ERROR] " . $e->getMessage());
            throw new Exception("Database connection error. Please contact administrator.");
        }
    }
    
    /**
     * Execute query with prepared statement
     * $query = "SELECT * FROM users WHERE id = ?"
     * $types = "i" (for integer), "s" (for string), "d" (for double), "b" (for blob)
     * $params = [1]
     */
    public function query($query, $types = '', $params = []) {
        try {
            if (empty($types) && empty($params)) {
                // Direct query (for non-parameterized queries)
                $result = $this->conn->query($query);
                if (!$result) {
                    throw new Exception($this->conn->error);
                }
                return $result;
            }
            
            // Prepared statement
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new Exception($this->conn->error);
            }
            
            // Bind parameters
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            
            // Execute
            if (!$stmt->execute()) {
                throw new Exception($stmt->error);
            }
            
            return $stmt->get_result();
            
        } catch (Exception $e) {
            error_log("[QUERY ERROR] Query: $query | Error: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Get single row
     */
    public function getRow($query, $types = '', $params = []) {
        $result = $this->query($query, $types, $params);
        return $result->fetch_assoc();
    }
    
    /**
     * Get all rows
     */
    public function getAll($query, $types = '', $params = []) {
        $result = $this->query($query, $types, $params);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Get last insert ID
     */
    public function lastId() {
        return $this->conn->insert_id;
    }
    
    /**
     * Get affected rows
     */
    public function affectedRows() {
        return $this->conn->affected_rows;
    }
    
    /**
     * Close connection
     */
    public function close() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
?>
