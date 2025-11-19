<?php
// $dev_mode = true; // Change to false on live server

// if ($dev_mode) {
//     ini_set('display_errors', 1);
//     ini_set('display_startup_errors', 1);
//     error_reporting(E_ALL);
// } else {
//     ini_set('display_errors', 0);
//     ini_set('log_errors', 1);
//     ini_set('error_log', __DIR__ . '/php_errors.log');
//     error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
// }

// Prevent duplicate session starts
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Prevent redeclaring the class if file is included twice
if (class_exists('DBConn')) {
    return;
}

class DBConn
{
    private $serverhost;
    private $username;
    private $password;
    private $database;
    private $conn;

    public function __construct(
        // $serverhost = "sql312.infinityfree.com",
        // $username = "if0_40229920",
        // $password = "X2sjHqNAyNq",
        // $database = "if0_40229920_stevenport"

        $serverhost = "localhost",
        $username = "root",
        $password = "",
        $database = "stevenport"
    ) {
        $this->serverhost = $serverhost;
        $this->username   = $username;
        $this->password   = $password;
        $this->database   = $database;

        $this->conn = new mysqli($this->serverhost, $this->username, $this->password, $this->database);

        if ($this->conn->connect_error) {
            die("Connection error: " . $this->conn->connect_error);
        }
    }

    public function getConnection()
    {
        return $this->conn;
    }
}
?>
