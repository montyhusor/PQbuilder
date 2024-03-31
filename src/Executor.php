<?php

namespace PQbuilder;

/**
 * * SQL执行器，用于执行构建的SQL查询并处理结果
 * * SQL Executor, responsible for executing the constructed SQL queries and handling the results.
 */
class Executor
{
    /**
     * @var \PDO PDO实例用于数据库操作--PDO instance for database operations.
     */
    protected $pdo;

    /**
     * @var \PDOStatement|null 最后执行的PDOStatement对象--The last executed PDOStatement object.
     */
    protected $lastStatement = null;

    /**
     * 构造函数，初始化数据库连接--Constructor, initializes the database connection.
     *
     * @param array $config 数据库连接配置--Database connection configuration.
     */
    public function __construct(array $config)
    {
        $defaultConfig = [
            "host" => "localhost",
            "db" => "",
            "user" => "root",
            "pass" => "",
            "charset" => "utf8",
            "options" => [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            ],
        ];

        $finalConfig = array_replace_recursive($defaultConfig, $config);

        $dsn = "mysql:host={$finalConfig["host"]};dbname={$finalConfig["db"]};charset={$finalConfig["charset"]}";
        try {
            $this->pdo = new \PDO(
                $dsn,
                $finalConfig["user"],
                $finalConfig["pass"],
                $finalConfig["options"]
            );
        } catch (\PDOException $e) {
            error_log("Connection Error: " . $e->getMessage());
            throw new \PDOException($e->getMessage(), (int) $e->getCode());
        }
    }

    /**
     * 开始一个事务--Begins a transaction.
     */
    public function begint()
    {
        $this->pdo->beginTransaction();
    }

    /**
     * 提交当前事务。
     * Commits the current transaction.
     */
    public function committ()
    {
        $this->pdo->commit();
    }

    /**
     * 回滚当前事务。
     * Rolls back the current transaction.
     */
    public function rollbackt()
    {
        $this->pdo->rollBack();
    }

    /**
     * 执行给定的查询组件构建的SQL查询--Executes the SQL query constructed by the given query component.
     *
     * @param ComponentInterface $component 查询组件--Query component.
     * @return bool 查询执行是否成功--Whether the query execution was successful.
     */
    public function qexecute(ComponentInterface $component): bool
    {
        $parameterBag = new ParameterBag();
        $sql = $component->build($parameterBag);
        $parameters = $parameterBag->all();

        try {
            $this->lastStatement = $this->pdo->prepare($sql);
            $this->bindParameters($this->lastStatement, $parameters);

            return $this->lastStatement->execute();
        } catch (\PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            throw new \PDOException($e->getMessage(), (int) $e->getCode());
        }
    }

    /**
     * 获取所有查询结果--Fetches all results from the query.
     *
     * @param ComponentInterface $component 查询组件，用于构建SQL语句--Query component used for building the SQL statement.
     * @return array 查询结果数组--Array of query results.
     */
    public function qfetchAll(ComponentInterface $component): array
    {
        $parameterBag = new ParameterBag();
        $sql = $component->build($parameterBag);
        $parameters = $parameterBag->all();

        try {
            $statement = $this->pdo->prepare($sql);
            $this->bindParameters($statement, $parameters);

            $statement->execute();

            return $statement->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            throw new \PDOException($e->getMessage(), (int) $e->getCode());
        }
    }

    /**
     * 获取单列的第一个值--Fetches the first value of a single column.
     *
     * @param ComponentInterface $component 查询组件，用于构建SQL语句--Query component used for building the SQL statement.
     * @param int $columnNumber 要获取的列的编号（从0开始）--The column number to fetch (0-indexed).
     * @return mixed 单列的值--Value of the single column.
     */
    public function qfetchColumn(
        ComponentInterface $component,
        $columnNumber = 0
    ) {
        $parameterBag = new ParameterBag();
        $sql = $component->build($parameterBag);
        $parameters = $parameterBag->all();

        $statement = $this->pdo->prepare($sql);
        $this->bindParameters($statement, $parameters);
        $statement->execute();
        return $statement->fetchColumn($columnNumber);
    }

    /**
     * 获取第一条记录--Fetches the first record.
     *
     * @param ComponentInterface $component 查询组件，用于构建SQL语句--Query component used for building the SQL statement.
     * @param int $fetchStyle 控制返回的数组格式--controls the contents of the returned array.
     * @return mixed 根据指定的获取模式返回单行数据--Returns a single row in the specified fetch style.
     */
    public function qfetchF(
        ComponentInterface $component,
        $fetchStyle = \PDO::FETCH_ASSOC
    ) {
        $parameterBag = new ParameterBag();
        $sql = $component->build($parameterBag);
        $parameters = $parameterBag->all();

        $statement = $this->pdo->prepare($sql);
        $this->bindParameters($statement, $parameters);
        $statement->execute();
        return $statement->fetch($fetchStyle);
    }

    /**
     * 获取查询结果，并以指定的类的实例返回结果行--Fetches the result row as an instance of the specified class.
     *
     * @param ComponentInterface $component 查询组件，用于构建SQL语句--Query component used for building the SQL statement.
     * @param string $className 结果应该以此类的实例形式返回--The class name that the result should be fetched into.
     * @param array $ctorArgs 构造函数参数（如果有）--Constructor arguments (if any).
     * @return object 返回指定类的实例，包含查询结果行的数据--Returns an instance of the specified class containing the data from the result row.
     */
    public function qfetchObject(
        ComponentInterface $component,
        $className = "stdClass",
        $ctorArgs = []
    ) {
        $parameterBag = new ParameterBag();
        $sql = $component->build($parameterBag);
        $parameters = $parameterBag->all();

        $statement = $this->pdo->prepare($sql);
        $this->bindParameters($statement, $parameters);
        $statement->execute();
        return $statement->fetchObject($className, $ctorArgs);
    }

    /**
     * 获取上一个执行的SQL语句影响的行数--the number of rows affected by the last SQL statement executed.
     *
     * @return int 影响的行数--The number of rows affected.
     */
    public function qrowCount()
    {
        return $this->lastStatement ? $this->lastStatement->rowCount() : 0;
    }

    /*
     * 绑定参数到PDO声明---Binds parameters to the PDO statement.
     *
     * 这个方法遍历参数数组，为每个参数绑定到PDO语句上，准备执行---This method iterates through the parameters array, binding each to the PDO statement in preparation for execution.
     *
     * @param \PDOStatement $statement 准备执行的PDO语句---The PDO statement to be executed.
     * @param array $parameters 要绑定到语句的参数数组---Array of parameters to bind to the statement.
     */
    protected function bindParameters(
        \PDOStatement $statement,
        array $parameters
    ) {
        foreach ($parameters as $key => $value) {
            $type = $this->getParameterPDOType($value);
            $statement->bindValue($key, $value, $type);
        }
    }

    /*
     * 获取参数对应的PDO类型---Gets the PDO type for the parameter.
     *
     * 根据参数的PHP类型（例如，整数、布尔值等），确定相应的PDO参数类型---Determines the appropriate PDO parameter type based on the PHP type of the parameter (e.g. integer, boolean, etc)
     *
     * @param mixed $value 参数值---The value of the parameter.
     * @return int PDO参数类型---The PDO parameter type.
     */
    protected function getParameterPDOType($value)
    {
        switch (true) {
            case is_int($value):
                return \PDO::PARAM_INT;
            case is_bool($value):
                return \PDO::PARAM_BOOL;
            case is_null($value):
                return \PDO::PARAM_NULL;
            default:
                return \PDO::PARAM_STR;
        }
    }
}

?>
