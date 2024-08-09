<?php

class Database
{
    private $servername = 'db';
    private $username_db = 'root';
    private $password_db = 'my-secret-pw';
    private $dbname = 'mariadb';
    private $conn = null;

    public function __construct()
    {
        $this->connect();
    }

    private function connect()
    {
        try {
            $this->conn = new PDO("mysql:host=$this->servername;dbname=$this->dbname", $this->username_db, $this->password_db);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            error_log("Database connection error: " . $e->getMessage());
            echo "Database connection failed: " . $e->getMessage();
            $this->conn = null;
        }
    }

    public function getConnection()
    {
        return $this->conn;
    }
}
