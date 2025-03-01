<?php
/**
 * Database Connection
 * Establishes a connection to the MySQL database
 */

class Database {
    private $host;
    private $port;
    private $username;
    private $password;
    private $database;
    private $connection;
    private $socket;
    
    public function __construct() {
        $this->host = DB_HOST;
        $this->port = defined('DB_PORT') ? DB_PORT : '3306';
        $this->username = DB_USER;
        $this->password = DB_PASS;
        $this->database = DB_NAME;
        $this->socket = defined('DB_SOCKET') ? DB_SOCKET : null;
        $this->connect();
    }
    
    private function connect() {
        try {
            // Use socket path for MAMP if defined
            if ($this->socket) {
                $this->connection = new mysqli(
                    $this->host, 
                    $this->username, 
                    $this->password, 
                    $this->database, 
                    $this->port,
                    $this->socket
                );
            } else {
                $this->connection = new mysqli(
                    $this->host, 
                    $this->username, 
                    $this->password, 
                    $this->database, 
                    $this->port
                );
            }
            
            if ($this->connection->connect_error) {
                throw new Exception("Connection failed: " . $this->connection->connect_error);
            }
            
            // Set character set to utf8mb4
            $this->connection->set_charset("utf8mb4");
            
        } catch (Exception $e) {
            if (DEBUG_MODE) {
                die("Database Connection Error: " . $e->getMessage());
            } else {
                die("A database error occurred. Please try again later.");
            }
        }
    }
    
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Query preparation failed: " . $this->connection->error);
            }
            
            if (!empty($params)) {
                $types = '';
                $bindParams = [];
                
                foreach ($params as $param) {
                    if (is_int($param)) {
                        $types .= 'i';
                    } elseif (is_float($param)) {
                        $types .= 'd';
                    } elseif (is_string($param)) {
                        $types .= 's';
                    } else {
                        $types .= 'b';
                    }
                    $bindParams[] = $param;
                }
                
                array_unshift($bindParams, $types);
                call_user_func_array([$stmt, 'bind_param'], $this->refValues($bindParams));
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            
            return $result;
            
        } catch (Exception $e) {
            if (DEBUG_MODE) {
                die("Query Error: " . $e->getMessage());
            } else {
                die("A database error occurred. Please try again later.");
            }
        }
    }
    
    private function refValues($arr) {
        $refs = [];
        foreach ($arr as $key => $value) {
            $refs[$key] = &$arr[$key];
        }
        return $refs;
    }
    
    public function fetchAll($sql, $params = []) {
        $result = $this->query($sql, $params);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function fetchOne($sql, $params = []) {
        $result = $this->query($sql, $params);
        return $result->fetch_assoc();
    }
    
    public function insert($sql, $params = []) {
        $this->query($sql, $params);
        return $this->connection->insert_id;
    }
    
    public function update($sql, $params = []) {
        $this->query($sql, $params);
        return $this->connection->affected_rows;
    }
    
    public function escape($string) {
        return $this->connection->real_escape_string($string);
    }
    
    public function close() {
        if ($this->connection) {
            $this->connection->close();
        }
    }
}

// Initialize the database connection
$db = new Database();
?> 