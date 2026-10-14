<?php

declare(strict_types=1);

class Database {  

    private PDO $pdo;
    // This constructor initializes the Database connection
    public function __construct(array $config) {

        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8mb4";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        $this->pdo = new PDO($dsn, $config['username'], $config['password'], $options);
    }

    public function getConnection(): PDO { return $this->pdo; }
}