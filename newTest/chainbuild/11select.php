<?php

namespace PQbuilder\newTest\chainbuild\select;

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

    $select = op::qselect(["id", "userID", "borrower", "bookname"], "testTable_library")->where(
        "userID",
        "=",
        "30002"
    );

    $recod = $qexec->qfetchAll($select);

    printRecords($recod);

    $qexec->committ();
} catch (\PDOException $e) {
    $qexec->rollbackt();
    echo "Database error: " . $e->getMessage();
} catch (\Exception $e) {
    $qexec->rollbackt();
    echo "General error: " . $e->getMessage();
}

function printRecords($records)
{
    echo "<table border='1'><tr><th>ID</th><th>UserID</th><th>Borrower</th><th>Book Name</th></tr>";
    foreach ($records as $row) {
        echo "<tr><td>{$row["id"]}</td><td>{$row["userID"]}</td><td>{$row["borrower"]}</td><td>{$row["bookname"]}</td></tr>";
    }
    echo "</table><br>";
}

?>
