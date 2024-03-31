<?php

namespace PQbuilder\newTest\chainbuild\test_select_2;

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

    echo "Selecting with orderBy, groupBy, and limit进行带有orderBy、groupBy和limit的查询:<br>";
    $selectX = op::qselect(
        ["bookname", "COUNT(id) AS count"],
        "testTable_library"
    )
        ->groupBy(["bookname"])
        ->having("COUNT(id)", ">", 1)
        ->orderBy("count", "DESC")
        ->limit(3);

    printRecords($qexec, $selectX);

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
    echo "<table border='1'><tr><th>BookName</th><th>Count</th></tr>";
    foreach ($records as $row) {
        echo "<tr><td>{$row["bookname"]}</td><td>{$row["count"]}</td></tr>";
    }
    echo "</table><br>";
}

?>
