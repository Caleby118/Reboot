<?php
class Database {
    private $host = "localhost";
    private $user = "vhserers_admin2";
    private $pwd = "INLL+eJU&Ov5";
    private $dbn = "vhserers_reboot";

    protected function connect() {
        try {
            $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbn . ';charset=utf8mb4';
            $pdo = new PDO($dsn, $this->user, $this->pwd);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo 'Connection error';
            exit();
        }
        return $pdo;
    }
}
?>