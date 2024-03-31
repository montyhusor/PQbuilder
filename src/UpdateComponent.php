<?php

namespace PQbuilder;

/**
 * * UPDATE查询构建器组件，用于构建UPDATE SQL查询。
 * * UPDATE query builder component, used for constructing UPDATE SQL queries.
 */
class UpdateComponent implements ComponentInterface, Havewhere
{
    /**
     * @var string 要更新的表名--The table name to update.
     */
    protected $table;

    /**
     * @var Expression[] 更新操作的列名和值的映射数组--Array mapping column names to their new values for the update operation.
     */
    protected $updates = [];

    /**
     * @var Condition WHERE条件构造器--WHERE condition builder.
     */
    protected $condition;

    /**
     * 构造函数，初始化UPDATE查询组件--Constructor, initializes the UPDATE query component.
     *
     * @param string $table 要更新的表名--The table name to update.
     * @param array $updates 更新操作的列名和值的映射--Mapping of column names to their new values for the update operation.
     */
    public function __construct(string $table, array $updates = [])
    {
        $this->table = $table;
        $this->condition = new Condition();
        foreach ($updates as $field => $value) {
            $this->set($field, $value);
        }
    }

    /**
     * 设置要更新的列和值--Sets the column and value to update.
     *
     * @param mixed $field 列名--Column name.
     * @param mixed $value 要设置的值--Value to set.
     * @return self 返回自身以支持链式调用--Returns self to support fluent interface.
     */
    public function set($field, $value): self
    {
        $this->updates[] = new Expression($field, "=", $value);
        return $this;
    }

    /**
     * 添加一个WHERE条件到更新查询中--Adds a WHERE condition to the update query.
     *
     * @param mixed $field 字段名--The name of the field.
     * @param string $operator 操作符，比如'='、'>'等--The operator, such as '=', '>', etc.
     * @param mixed $value 条件的值--The value for the condition.
     * @return self 返回自身以支持链式调用--Returns self to support fluent interface.
     */
    public function where($field, $operator, $value): self
    {
        $expression = new Expression($field, $operator, $value);
        $this->condition->add($expression);
        return $this;
    }

    /**
     * 添加一个orWhere条件到更新查询中--Adds an orWhere condition to the update query.
     *
     * @param mixed $field 字段名--The name of the field.
     * @param string $operator 操作符，比如'='、'>'等--The operator, such as '=', '>', etc.
     * @param mixed $value 条件的值--The value for the condition.
     * @return self 返回自身以支持链式调用--Returns self to support fluent interface.
     */
    public function orWhere($field, $operator, $value): self
    {
        $expression = new Expression($field, $operator, $value);
        $this->condition->add($expression, "OR");
        return $this;
    }

    /**
     * 开始一个新的条件组合，用于创建逻辑上括号内的查询--Start a new condition group for creating logically grouped (parenthesized) queries.
     *
     * @param string $logic 逻辑操作符 'AND' 或 'OR'，默认为 'AND'-- Logical operator 'AND' or 'OR', defaults to 'AND'.
     * @return self 返回自身以支持链式调用--Returns self for method chaining.
     */
    public function pa($logic = "AND"): self
    {
        $this->condition->startGroup($logic);
        return $this;
    }

    /**
     * 结束当前的条件组合，括号的结束--End the current condition group.
     *
     * @return self 返回自身以支持链式调用--Returns self for method chaining.
     */
    public function endPa(): self
    {
        $this->condition->endGroup();
        return $this;
    }

    /**
     * 构建并返回UPDATE查询的SQL字符串。如果存在WHERE条件，则包含在内--Builds and returns the SQL string for the UPDATE query. Includes WHERE conditions if they exist.
     * 支持通过pa()和endPa()添加的复杂的条件逻辑--Supports complex conditional logic added through pa() and endPa().
     *
     * @param ParameterBag $parameterBag 参数包，用于收集构建过程中的参数--The parameter bag for collecting parameters during the build process.
     * @return string 构建的UPDATE查询SQL字符串--The constructed SQL string for the UPDATE query.
     */
    public function build(ParameterBag $parameterBag): string
    {
        $updateParts = [];
        foreach ($this->updates as $update) {
            $updateParts[] = $update->build($parameterBag);
        }
        $sql = "UPDATE " . $this->table . " SET " . implode(", ", $updateParts);
        $whereClause = $this->condition->build($parameterBag);
        if ($whereClause) {
            $sql .= " WHERE " . $whereClause;
        }
        return $sql;
    }
}

?>
