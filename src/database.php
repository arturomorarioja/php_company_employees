<?php

/**
 * It creates a database connection
 * @return<PDO> A PDO object, or false if the connection was unsuccessful
 */
function connect(): PDO|false
{
    require_once 'db_credentials.php';

    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ];

    try {
        $pdo = new PDO($dsn, $user, $password, $options);
        return $pdo;
    } catch (PDOException) {        
        return false;
    }
}