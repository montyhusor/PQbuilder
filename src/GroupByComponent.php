<?php

namespace PQbuilder;

/**
 * * GROUP BY查询构建器组件，用于构建SQL查询中的GROUP BY部分。
 * * GROUP BY query builder component, used for constructing the GROUP BY part of an SQL query.
 */
class GroupByComponent implements ComponentInterface
{
    /**
     * @var string[] GROUP BY子句中的列名数组--Array of column names in the GROUP BY clause.
     */
    protected $groups = [];

    /**
     * 添加列名到GROUP BY那--Adds a column name to the GROUP BY clause.
     *
     * @param string $column 列名--Column name.
     * @return self 返回自身以支持链式调用--Returns self to support fluent interface.
     */
    public function addGroup(string $column): self
    {
        $this->groups[] = $column;
        return $this;
    }

    /**
     * 构建并返回GROUP BY子句的SQL字符串--Builds and returns the SQL string for the GROUP BY clause.
     *
     * @param ParameterBag $parameterBag 参数包，用于收集构建过程中的参数--The parameter bag for collecting parameters during the build process.
     * @return string 构建的GROUP BY子句SQL字符串--The constructed GROUP BY clause SQL string.
     */
    public function build(ParameterBag $parameterBag): string
    {
        if (empty($this->groups)) {
            return "";
        }
        return "GROUP BY " . implode(", ", $this->groups);
    }
}

?>
