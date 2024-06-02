<?php
class Database {
    private $host = "localhost";
    private $db_name = "db_perpus";
    private $username = "root";
    private $password = "";
    public $connect;

    public function getConnection() {
        $this->connect = null;
        try {
            $this->connect = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->connect->exec("set names utf8");
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->connect;
    }
    public function prepare($sql) {
        return $this->connect->prepare($sql);
    }
}
?>
