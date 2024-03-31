<?php

namespace PQbuilder;

/**
 * * ORDER BY查询构建器组件，用于构建SQL查询中的ORDER BY部分。
 * * ORDER BY query builder component, used for constructing the ORDER BY part of an SQL query.
 */
class OrderByComponent implements ComponentInterface
{
    /**
     * @var array 排序条件数组，存储字段名及其排序方向--Array of ordering conditions, storing field names and their sorting direction.
     */
    protected $orders = [];

    /**
     * 添加一个排序条件到ORDER BY子句---Adds an ordering condition to the ORDER BY clause
     *
     * @param string $column 列名--Column name.
     * @param string $direction 排序方向，'ASC' 或 'DESC'--Sorting direction, 'ASC' or 'DESC'
     * @return self 返回自身以支持链式调用--Returns self to support fluent interface.
     */
    public function addOrder(string $column, string $direction = "ASC"): self
    {
        $this->orders[] = $column . " " . strtoupper($direction);
        return $this;
    }

    /**
     * 构建并返回ORDER BY子句的SQL字符串--Builds and returns the SQL string for the ORDER BY clause.
     *
     * @param ParameterBag $parameterBag 参数包，用于收集构建过程中的参数--The parameter bag for collecting parameters during the build process.
     * @return string 构建的ORDER BY子句SQL字符串--the constructed ORDER BY clause SQL string
     */
    public function build(ParameterBag $parameterBag): string
    {
        if (empty($this->orders)) {
            return "";
        }
        return "ORDER BY " . implode(", ", $this->orders);
    }
}

?>
