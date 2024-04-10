<?php

namespace PQbuilder\newTest\chainbuild\test_update;

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

    function printRecords($qexec, $conditionBuilder)
    {
        $records = $qexec->qfetchAll($conditionBuilder);
        echo "<table border='1'><tr><th>ID</th><th>UserID</th><th>Borrower</th><th>Book Name</th><th>BorrowDate</th></tr>";
        foreach ($records as $row) {
            echo "<tr><td>{$row["id"]}</td><td>{$row["userID"]}</td><td>{$row["borrower"]}</td><td>{$row["bookname"]}</td><td>{$row["borrowDate"]}</td></tr>";
        }
        echo "</table><br>";
    }

    echo "Before AND Update 使用AND条件更新前:<br>";
    $selectBeforeAnd = op::qselect(
        ["id", "userID", "borrower", "bookname", "borrowDate"],
        "testTable_library"
    )
        ->where("borrower", "=", "A2")
        ->where("userID", "=", "10002");

    printRecords($qexec, $selectBeforeAnd);

    $updateBuilderAnd = op::qupdate("testTable_library")
        ->set("bookname", "Updated AND")
        ->where("borrower", "=", "A2")
        ->where("userID", "=", "10002");

    $qexec->qexecute($updateBuilderAnd);
    echo "Rows affected by AND: " . $qexec->qrowCount() . "<br>";

    echo "After AND Update 使用AND条件更新后:<br>";

    $selectAfterAnd = op::qselect(
        ["id", "userID", "borrower", "bookname", "borrowDate"],
        "testTable_library"
    )->where("bookname", "=", "Updated AND");

    printRecords($qexec, $selectAfterAnd);

    echo "Before OR Update 使用OR条件更新前:<br>";
    $selectBeforeOr = op::qselect(
        ["id", "userID", "borrower", "bookname", "borrowDate"],
        "testTable_library"
    )
        ->where("userID", "=", "10005")
        ->orWhere("borrower", "=", "C4")
        ->orWhere("bookname", "=", "PHP & MySQL: Server-side Web Development");

    printRecords($qexec, $selectBeforeOr);

    $updateBuilderOr = op::qupdate("testTable_library")
        ->set("bookname", "Updated OR")
        ->where("userID", "=", "10005")
        ->orWhere("borrower", "=", "C4")
        ->orWhere("bookname", "=", "PHP & MySQL: Server-side Web Development");

    $qexec->qexecute($updateBuilderOr);
    echo "Rows affected by OR: " . $qexec->qrowCount() . "<br>";

    echo "After OR Update 使用OR条件更新后:<br>";

    $selectAfterOr = op::qselect(
        ["id", "userID", "borrower", "bookname", "borrowDate"],
        "testTable_library"
    )->where("bookname", "=", "Updated OR");

    printRecords($qexec, $selectAfterOr);

    $qexec->committ();
} catch (\PDOException $e) {
    $qexec->rollbackt();
    echo "Database error: " . $e->getMessage();
} catch (\Exception $e) {
    $qexec->rollbackt();
    echo "General error: " . $e->getMessage();
}

?>
