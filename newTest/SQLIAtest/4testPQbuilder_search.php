<?php
namespace PQbuilder\SQLIAtest\testPQbuilder_search;

require_once "../../vendor/autoload.php";

use PQbuilder\Factory as op;
use PQbuilder\Executor;

$config = [
    "host" => "",
    "db" => "",
    "user" => "",
    "pass" => "",
];

$qexec = new Executor($config);

echo "<h2>PQbuilder SQLIA test测试</h2>";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $search = trim($_POST["search"]);

    // 检查 $search 是否为空
    if (!empty($search)) {
        $sql = op::qselect(["*"], "testTable_library")
            ->where("userID", "=", $search)
            ->orWhere("borrower", "=", $search)
            ->orWhere("bookname", "LIKE", "%" . $search . "%");

        $results = $qexec->qfetchAll($sql);

        echo "<h2>查询结果 results：</h2>";
        if (empty($results)) {
            echo "<p>未找到匹配项 No matches found</p>";
        } else {
            foreach ($results as $row) {
                echo "<p>id: {$row["id"]} - userID: {$row["userID"]} - borrower: {$row["borrower"]} - bookname: {$row["bookname"]}</p>";
            }
        }
    } else {
        echo "<p>输入为空 Input is empty</p>";
    }
}
?>
