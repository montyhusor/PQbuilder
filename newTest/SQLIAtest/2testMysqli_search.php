<?php

namespace PQbuilder\SQLIAtest\testMysqli_search;

use mysqli;

$host = "";
$user = "";
$pass = "";
$db = "";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("连接失败 Connection Error: " . $conn->connect_error);
}
echo "<h2>Mysqli SQLIA test测试</h2>";
echo "<br>";
echo "<br>";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $search = trim($_POST["search"]);

    $sql = "SELECT * FROM testTable_library WHERE userID='$search' OR borrower='$search' OR bookname LIKE '%$search%'";
    echo "SQL: " . htmlspecialchars($sql) . "<br>";
    echo "<br>";
    $result = $conn->query($sql);

    if (empty($search)) {
        echo "<p>输入为空 Input is empty</p>";
    } elseif ($result->num_rows > 0 && $search != null) {
        while ($row = $result->fetch_assoc()) {
            echo "<p>id: {$row["id"]} - userID: {$row["userID"]} - borrower: {$row["borrower"]} - bookname: {$row["bookname"]}</p>";
        }
    } else {
        echo "<p>未找到匹配项 No matches found</p>";
    }
}

$conn->close();
