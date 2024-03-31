<?php

namespace PQbuilder\newTest\chainbuild\test_delete;

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

    $delete = op::qdelete("testTable_library")->where("bookname", "is", null);

    $success = $qexec->qexecute($delete);
    $rowsAffected = $qexec->qrowCount();

    if ($success && $rowsAffected != 0) {
        echo "Delete operation was successful. 删除操作成功\n";
        echo "<br>";
        echo "Rows affected 影响行数: $rowsAffected\n";
    } else {
        echo "Rows affected 影响行数: $rowsAffected\n";
    }

    $qexec->committ();
} catch (\PDOException $e) {
    $qexec->rollbackt();
    echo "Database error: " . $e->getMessage();
} catch (\Exception $e) {
    $qexec->rollbackt();
    echo "General error: " . $e->getMessage();
}

?>
