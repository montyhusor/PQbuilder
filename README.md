# PQbuilder

> PQbuilder 是一个为MySQL设计的PHP查询构建库，使开发者能够以链式调用的方式构建SQL查询。
> PQbuilder is a PHP query builder library designed for MySQL, enabling developers to construct the SQL queries through chainable method calls.

## 主要特性

- **基础CRUD操作 - Basic CRUD Operations**：支持SELECT、INSERT、UPDATE、DELETE查询。- Supports SELECT, INSERT, UPDATE and DELETE queries.
- **链式调用 - Chainable Calls**：支持链式调用。- Supports chainable calls.
- **自动参数绑定 - Automatic Parameter Binding**：减少SQL注入的风险。- Reduces the risk of SQL injection.
- **条件构建 - Condition Building**：支持`IN`语句、嵌套逻辑和复杂条件构建。- Supports `IN` statements, nested logic, and the construction of complex conditions.
- **事务处理 - Transaction Management**：有事务开始、提交和回滚的方法。- Includes methods for starting transactions, committing, and rolling back.
- **数据抓取方法 - Data Fetching Methods**：提供`qfetchAll`、`qfetchF`、`qfetchColumn`、`qfetchObject`方法。- Provides `qfetchAll`, `qfetchF`, `qfetchColumn` and `qfetchObject`. methods.

## 安装 - Installation

通过Composer进行安装：

```bash
composer require montyhusor/pqbuilder
```

## 示例 - Examples

### 插入 - INSERT

```php
use PQbuilder\Factory as op;
use PQbuilder\Executor;

$config = [
    "host" => "host",
    "db" => "database",
    "user" => "username",
    "pass" => "password",
    "options" => [],
];

$eor = new Executor($config);

$insert = op::qinsert("testTable_library")
    ->columns(["userID", "borrower", "bookname"])
    ->values(["10001", "A1", "phpMyAdmin Starter"])
    ->values(["10002", "A2", "PHP is Standing Tall"]);

$eor->qexecute($insert);
echo "Affected rows: " . $eor->qrowCount();
```

### 更新 - UPDATE

```php
use PQbuilder\Factory as op;
use PQbuilder\Executor;

$eor = new Executor($config);

$update = op::qupdate("testTable_library")
    ->set("bookname", "PHP Cookbook")
    ->where("borrower", "=", "A1");

$eor->qexecute($update);
echo "Affected rows: " . $eor->qrowCount();
```

### 删除 - DELETE

```php
use PQbuilder\Factory as op;
use PQbuilder\Executor;

$eor = new Executor($config);

$delete = op::qdelete("testTable_library")
    ->where("bookname", "=", "PHP Cookbook");

$eor->qexecute($delete);
echo "Affected rows: " . $eor->qrowCount();
```

### 查询 - SELECT

```php
use PQbuilder\Factory as op;
use PQbuilder\Executor;

$eor = new Executor($config);

$select = op::qselect(["id", "userID", "borrower", "bookname"], "testTable_library")
    ->where("userID", "IN", [10001, 10002, 10003]);

// 获取所有记录
$records = $eor->qfetchAll($select);

// 获取第一条记录
$firstRecord = $eor->qfetchF($select);

// 获取单列的第一个值
$ColumnfirstV = $eor->qfetchColumn($select, 3);
```

### 使用`pa()`和`endPa()`进行嵌套逻辑的查询 - Using `pa()` and `endPa()` for Nested Logic Queries

```php
use PQbuilder\Factory as op;
use PQbuilder\Executor;

$eor = new Executor($config);

$select = op::qselect(["id", "userID", "borrower", "bookname"], "testTable_library")
    ->where("userID", "=", 10002)
    ->pa()
        ->where("bookname", "=", "PHP Cookbook")
        ->orWhere("bookname", "=", "PHP is Standing Tall")
    ->endPa();

$records = $eor->qfetchAll($select);
```

### 使用`qfetchObject`获取记录作为对象 - Using qfetchObject to Fetch Records as Objects

定义Book类：

```php
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
        return $this->greeting . " This book is called 《" . $this->bookname . "》";
    }
}
```

获取Book对象：

```php
use PQbuilder\Factory as op;
use PQbuilder\Executor;

$eor = new Executor($config);

$selectBook = op::qselect(["bookname"], "testTable_library");
$bookObject = $eor->qfetchObject($selectBook, Book::class, ["Welcome to the library!"]);

echo $bookObject->intro();
```

### 使用`groupBy()`, `orderBy()`, `having()`, `limit()` - Using `groupBy()`, `orderBy()`, `having()`, `limit()`

```php
use PQbuilder\Factory as op;
use PQbuilder\Executor;

$eor = new Executor($config);

$selectX = op::qselect(
        ["bookname", "COUNT(id) AS count"],
        "testTable_library"
    )
        ->groupBy(["bookname"])
        ->having("COUNT(id)", ">", 1)
        ->orderBy("count", "DESC")
        ->limit(3);

$records = $eor->qfetchAll($selectX);
echo "<pre>";
print_r($records);
```

## 注意事项 - Considerations

- 在构建DELETE和UPDATE查询时，请确保不在没有明确条件的情况下直接使用`pa()`和`endPa()`，以防止执行无条件的全表操作并导致数据丢失。
- 当开始一个新的逻辑组合时，使用的第一个`where()`和`orWhere()`方法都会产生相同的效果。因此，在开始新的逻辑组合时直接使用where就行。
- 类似地，对于`pa()`方法，当你开始一个新的条件组合时，无论是`pa()`还是`pa("OR")`，如果它是第一个使用的，那么效果是相同的，因为它标志着一个新的逻辑分支的开始。
- 请注意，虽然`PQbuilder`旨在简化SQL查询构建过程，并提供了基础的CRUD操作及一些高级功能，但它并不覆盖所有的SQL查询操作以及一些更复杂的数据库操作，`PQbuilder`还有待完善。

>- When building DELETE and UPDATE queries, please ensure not to directly use `pa()` and `endPa()` without specific conditions, to prevent executing unconditional full-table operations and causing data loss.
>- When initiating a new logical grouping, the first use of `where()` and `orWhere()` methods will produce the same effect. Therefore, it is sufficient to directly use `where` when starting a new logical combination.
>- Please note that while `PQbuilder` is designed to simplify the SQL query building process, offering basic CRUD operations and some advanced features, it does not cover all SQL query operations or some of the more complex database interactions. `PQbuilder` is still a work in progress.
