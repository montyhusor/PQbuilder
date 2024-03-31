<?php

namespace PQbuilder;

/**
 * * HAVING查询构建器组件，用于构建SQL查询中的HAVING部分。
 * * HAVING query builder component, used for constructing the HAVING part of an SQL query.
 */
class HavingComponent implements ComponentInterface
{
    /**
     * @var array 条件数组，存储HAVING条件的表达式和值--Array of conditions, storing expressions and values for the HAVING clause.
     */
    protected $conditions = [];

    /**
     * @var array 存储条件值的数组，与条件表达式对应--Array of values for the conditions, corresponding to the condition expressions.
     */
    protected $values = [];

    /**
     * 添加一个条件到HAVING子句--Adds a condition to the HAVING clause.
     *
     * @param string $field 字段名或表达式--Field name or expression.
     * @param string $operator 操作符，如'='、'>'等--Operator, such as '=', '>', etc.
     * @param mixed $value 条件的值--The value for the condition.
     * @return self 返回自身以支持链式调用--Returns self to support fluent interface.
     */
    public function addCondition(string $field, $operator, $value): self
    {
        if (empty($operator)) {
            throw new \InvalidArgumentException(
                "Operator can not be null or empty."
            );
        }

        $placeholder = ":having" . uniqid();

        $this->conditions[] = "$field $operator $placeholder";

        $this->values[$placeholder] = $value;

        return $this;
    }

    /**
     * 构建并返回HAVING子句的SQL字符串--Builds and returns the SQL string for the HAVING clause.
     *
     * @param ParameterBag $parameterBag 参数包，用于收集构建过程中的参数--The parameter bag for collecting parameters during the build process.
     * @return string 构建的HAVING子句SQL字符串--The constructed HAVING clause SQL string.
     */
    public function build(ParameterBag $parameterBag): string
    {
        $conditionsParts = [];

        foreach ($this->conditions as $condition) {
            $conditionsParts[] = $condition;

            [, , $placeholder] = explode(" ", $condition);

            $value = $this->values[$placeholder];
            $parameterBag->add($placeholder, $value);
        }
        return empty($conditionsParts)
            ? ""
            : "HAVING " . implode(" AND ", $conditionsParts);
    }
}

?>
