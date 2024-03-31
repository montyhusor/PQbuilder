<?php

namespace PQbuilder\newTest\chainbuild\test_select_3;

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

    echo "Selecting with nested Where & orWhere 查询嵌套的Where & orWhere:<br>";
    $selectSingle = op::qselect(
        ["id", "userID", "borrower", "bookname"],
        "testTable_library"
    )
        ->Where("bookname", "=", "Updated OR")
        ->pa("OR")
        ->pa()
        ->where("userID", "=", "30004")
        ->orWhere("borrower", "=", "B4")
        ->endPa()
        ->pa("OR")
        ->where("userID", "=", "10004")
        ->orWhere("borrower", "=", "B4")
        ->endPa()
        ->endPa()
        ->orWhere("borrower", "=", "C3");

    printRecords($qexec, $selectSingle);

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
