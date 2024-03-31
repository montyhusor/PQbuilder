<?php

namespace PQbuilder\newTest\chainbuild\update;

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

    $updateBuilderAnd = op::qupdate("testTable_library")
        ->set("bookname", "New Book Name")
        ->pa()
        ->where("userID", "=", "10003")
        ->orWhere("userID", "=", "30004")
        ->endPa()
        ->where("bookname", "!=", "Updated OR");

    $qexec->qexecute($updateBuilderAnd);
    echo "Rows affected by AND: " . $qexec->qrowCount() . "<br>";

    $qexec->committ();
} catch (\PDOException $e) {
    $qexec->rollbackt();
    echo "Database error: " . $e->getMessage();
} catch (\Exception $e) {
    $qexec->rollbackt();
    echo "General error: " . $e->getMessage();
}
?>
