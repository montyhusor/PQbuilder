<?php

namespace PQbuilder\newTest\chainbuild\test_insert;

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

    $borrowers = [
        ["10001", "A1", "phpMyAdmin Starter"],
        ["10002", "A2", "PHP is Standing Tall"],
        ["10003", "A2", "PHP: The Complete Reference"],
        ["10002", "A2", "PHP Cookbook"],
        ["10005", "A5", "PHP: For the Web"],
        ["10003", "A3", null],
        ["20001", "B1", "PHP & MySQL: Server-side Web Development"],
        ["20002", "B2", "Head first Java"],
        ["20003", "B3", "Java Cookbook"],
        ["20004", "B4", "Database Management Systems"],
        ["20005", "B5", "Python cookbook"],
        ["30001", "C1", "Python for Data Analysis"],
        ["30002", "C2", "Learning Python"],
        ["30003", "C3", "Python: the complete reference"],
        ["30004", "C4", "HTML: the complete reference"],
        ["30005", "C5", "CSS Cookbook"],
        ["30005", "C5", null],
    ];

    echo "<table border='1'><tr><th>Borrower ID</th><th>Borrower Name</th><th>Book Name</th><th>Rows Affected</th></tr>";

    foreach ($borrowers as $borrower) {
        $insert = op::qinsert("testTable_library")
            ->columns(["userID", "borrower", "bookname"])
            ->values([$borrower[0], $borrower[1], $borrower[2]]);

        $success = $qexec->qexecute($insert);
        $rowsAffected = $qexec->qrowCount();

        if ($success && $rowsAffected > 0) {
            echo "<tr><td>{$borrower[0]}</td><td>{$borrower[1]}</td><td>" .
                ($borrower[2] ?? "NULL") .
                "</td><td>$rowsAffected</td></tr>";
        } else {
            echo "<tr><td colspan='4'>Insert failed for {$borrower[1]}</td></tr>";
        }

        // sleep(rand(1, 2));
    }

    echo "</table>";

    $qexec->committ();
    echo "All insert queries executed successfully. 插入数据全部成功\n";
} catch (\PDOException $e) {
    $qexec->rollbackt();
    echo "Database error: " . $e->getMessage();
} catch (\Exception $e) {
    $qexec->rollbackt();
    echo "General error: " . $e->getMessage();
}

?>