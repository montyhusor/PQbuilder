<?php

namespace PQbuilder\newTest\chainbuild\test_fetch;

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

    echo "Fetching multiple records with qfetchAll 使用qfetchAll获取多条记录:<br>";
    $selectAll = op::qselect(
        ["id", "userID", "borrower", "bookname"],
        "testTable_library"
    )->limit(7);

    $allRecords = $qexec->qfetchAll($selectAll);
    echo "<pre>";
    print_r($allRecords);
    echo "</pre>";

    echo "Fetching a single record with qfetch 使用qfetchF获取单条记录:<br>";
    $selectBuild1 = op::qselect(
        ["id", "userID", "borrower", "bookname"],
        "testTable_library"
    );

    $singleRecord = $qexec->qfetchF($selectBuild1, \PDO::FETCH_NUM);
    echo "<pre>";
    print_r($singleRecord);
    echo "</pre>";

    echo "Fetching the first value of a column with qfetchColumn 使用qfetchColumn获取单列的第一个值:<br>";
    $selectColumn1 = op::qselect(
        ["userID", "borrower", "bookname"],
        "testTable_library"
    );

    $firstUserID = $qexec->qfetchColumn($selectColumn1, 2);
    echo "<pre>";
    print_r($firstUserID);
    echo "</pre>";

    echo "Fetching a record as an object with qfetchObject 使用qfetchObject获取单条记录并将其作为对象返回:<br>";
    $selectObject1 = op::qselect(
        ["id", "userID", "borrower", "bookname"],
        "testTable_library"
    );

    $objectRecord = $qexec->qfetchObject($selectObject1);
    echo "<pre>";
    print_r($objectRecord);
    echo "</pre>";

    class Book
    {
        public $bookname;
        public $greeting;

        public function __construct($greeting = "Hi")
        {
            $this->greeting = $greeting;
        }

        public function intro()
        {
            return $this->greeting .
                " This book is called 《" .
                $this->bookname .
                "》";
        }
    }

    echo "Using qfetchObject to fetch a record as a Book object 使用qfetchObject将单条记录获取为Book对象:<br>";
    $selectBook = op::qselect(["bookname"], "testTable_library");

    $bookObject = $qexec->qfetchObject($selectBook, Book::class, [
        "Welcome to the library!",
    ]);

    echo "<pre>";
    print_r($bookObject);
    echo "</pre>";

    echo $bookObject->intro();
} catch (\PDOException $e) {
    echo "Database error: " . $e->getMessage();
} catch (\Exception $e) {
    echo "General error: " . $e->getMessage();
}

?>
