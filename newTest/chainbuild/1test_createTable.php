<?php

namespace PQbuilder\newTest\chainbuild\test_createTable;

use PDO;
use PDOException;

$host = "";
$db = "";
$user = "";
$pass = "";

try {
    $pdo = new PDO(
        "mysql:host=$host; dbname=$db; charset=utf8mb4",
        $user,
        $pass
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "CREATE TABLE IF NOT EXISTS testTable_library (
        id INT AUTO_INCREMENT PRIMARY KEY,
        userID INT NOT NULL,
        borrower VARCHAR(255) NOT NULL,
        bookname VARCHAR(255) DEFAULT NULL,
        isReturned BOOLEAN NOT NULL DEFAULT FALSE,
        borrowDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        returnDate TIMESTAMP NULL DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $pdo->exec($sql);
    echo "Table 'testTable_library' created successfully. 表创建成功";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

?>
