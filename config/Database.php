<?php

/**
 * Singleton Database class to manage database connections.
 */
class Database {
    private string $host;
    private string $port;
    private string $dbName;
    private string $username;
    private string $password;
    private ?PDO $conn = null;
    private static ?Database $instance = null;

    /**
     * Private constructor to initialize database configuration
     *
     * @throws InvalidArgumentException if configuration values are missing or invalid
     */
    private function __construct() {
        $config = require 'config.php';

        if (!isset($config['database']) || !is_array($config['database'])) {
            throw new InvalidArgumentException("Invalid database configuration format.");
        }

        $this->host = $config['database']['host'] ?? throw new InvalidArgumentException("Database host not specified.");
        $this->port = $config['database']['port'] ?? throw new InvalidArgumentException("Database port not specified.");
        $this->dbName = $config['database']['db_name'] ?? throw new InvalidArgumentException("Database name not specified.");
        $this->username = $config['database']['username'] ?? throw new InvalidArgumentException("Database username not specified.");
        $this->password = $config['database']['password'] ?? throw new InvalidArgumentException("Database password not specified.");
    }

    /**
     * Prevent cloning of singleton instance
     */
    private function __clone() {}

    /**
     * Prevent unserializing of singleton instance
     *
     * @throws Exception
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize a singleton.");
    }

    /**
     * Get the singleton instance of the Database
     *
     * @return Database
     */
    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    /**
     * Get the PDO connection instance
     *
     * @return PDO
     * @throws RuntimeException if connection fails
     */
    public function getConnection(): PDO {
        if ($this->conn === null) {
            try {
                $dsn = sprintf("mysql:host=%s;port=%s;dbname=%s", $this->host, $this->port, $this->dbName);
                $this->conn = new PDO($dsn, $this->username, $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $exception) {
                error_log("Connection error: " . $exception->getMessage());
                throw new RuntimeException("Database connection failed.");
            }
        }
        return $this->conn;
    }
}
