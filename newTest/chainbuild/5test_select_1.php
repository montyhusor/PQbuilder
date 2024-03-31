<?php

namespace PQbuilder\newTest\chainbuild\test_select_1;

require_once "../../vendor/autoload.php";

use PQbuilder\Executor;
use PQbuilder\Factory as op;

$config = [
    "host" => "",
    "db" => "",
    "user" => "",
    "pass" => "",
];

try {
    $qexec = new Executor($config);
    $qexec->begint();

    echo "Selecting with a single condition (where) 查询单条记录:<br>";
    $selectSingle = op::qselect(
        ["id", "userID", "borrower", "bookname"],
        "testTable_library"
    )->where("userID", "=", "10005");

    printRecords($qexec, $selectSingle);

    echo "Selecting with multiple values (IN) 使用IN查询多条记录:<br>";
    $selectBuilderIn = op::qselect(
        ["id", "userID", "borrower", "bookname"],
        "testTable_library"
    )->where("userID", "IN", [10005, 10002, 10003, 30004]);

    printRecords($qexec, $selectBuilderIn);

    $qexec->committ();
} catch (\PDOException $e) {
    $qexec->rollbackt();
    echo "Database error: " . $e->getMessage();
} catch (\Exception $e) {
    $qexec->rollbackt();
    echo "General error: " . $e->getMessage();
}

function printRecords($qexec, $qb)
{
    $records = $qexec->qfetchAll($qb);
    echo "<table border='1'><tr><th>ID</th><th>UserID</th><th>Borrower</th><th>Book Name</th></tr>";
    foreach ($records as $row) {
        echo "<tr><td>{$row["id"]}</td><td>{$row["userID"]}</td><td>{$row["borrower"]}</td><td>{$row["bookname"]}</td></tr>";
    }
    echo "</table><br>";
}

?>
