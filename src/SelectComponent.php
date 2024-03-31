<?php

namespace PQbuilder;

/**
 * * SELECT查询构建器组件，用于构建SELECT SQL查询
 * * SELECT query builder component, used for constructing SELECT SQL queries.
 */
class SelectComponent implements ComponentInterface, Havewhere
{
    /**
     * @var array 查询中要选择的字段列表--List of fields to select in the query.
     */
    protected $fields = [];

    /**
     * @var string FROM子句中的表名--The table name in the FROM clause.
     */
    protected $fromClause = "";

    /**
     * @var Condition WHERE条件构造器--WHERE condition builder.
     */
    protected $condition;

    /**
     * @var GroupByComponent GROUP BY条件构造器--GROUP BY condition builder.
     */
    protected $groupByComponent;

    /**
     * @var HavingComponent HAVING条件构造器--HAVING condition builder.
     */
    protected $havingComponent;

    /**
     * @var OrderByComponent ORDER BY条件构造器--ORDER BY condition builder.
     */
    protected $orderByComponent;

    /**
     * @var LimitComponent LIMIT条件构造器--LIMIT condition builder.
     */
    protected $limitComponent;

    /**
     * 构造函数，初始化SELECT查询组件--Constructor, initializes the SELECT query component.
     *
     * @param array $fields 要选择的字段列表--List of fields to be selected.
     * @param string $table FROM子句中的表名（可选）--The table name in the FROM clause (optional)
     */
    public function __construct(array $fields, string $table = "")
    {
        $this->fields = $fields;
        $this->fromClause = $table ? "FROM $table" : "";
        $this->condition = new Condition();
        $this->groupByComponent = new GroupByComponent();
        $this->havingComponent = new HavingComponent();
        $this->orderByComponent = new OrderByComponent();
    }

    /**
     * 设置FROM子句的表名--Sets the table name for the FROM clause.
     *
     * @param string $table 表名--Table name.
     * @return self 返回自身以支持链式调用--Returns self to support fluent interface.
     */
    public function from(string $table): self
    {
        $this->fromClause = "FROM $table";
        return $this;
    }

    /**
     * 添加一个WHERE条件到查询中--Adds a WHERE condition to the query.
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
     * 添加一个orWhere条件到查询中--Adds an oeWhere condition to the query.
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
     * 添加GROUP BY条件到查询中--Adds a GROUP BY condition to the query.
     *
     * @param array $columns 列名数组--Array of column names.
     * @return self 返回自身以支持链式调用--Returns self to support fluent interface.
     */
    public function groupBy(array $columns): self
    {
        foreach ($columns as $column) {
            $this->groupByComponent->addGroup($column);
        }
        return $this;
    }

    /**
     * 添加HAVING条件到查询中--Adds a HAVING condition to the query.
     *
     * @param string $field 字段名--The name of the field.
     * @param string $operator 操作符，比如'='、'>'等--The operator, such as '=', '>'  etc.
     * @param mixed $value 条件的值--The value for the condition.
     * @return self 返回自身以支持链式调用--Returns self to support fluent interface
     */
    public function having($field, $operator, $value): self
    {
        $this->havingComponent->addCondition($field, $operator, $value);
        return $this;
    }

    /**
     * 添加ORDER BY条件到查询中--Adds an ORDER BY condition to the query.
     *
     * @param string $column 列名--Column name.
     * @param string $direction 排序方向，'ASC' 或 'DESC'--Sort direction, 'ASC' or 'DESC'.
     * @return self 返回自身以支持链式调用--Returns self to support fluent interface.
     */
    public function orderBy($column, $direction = "ASC"): self
    {
        $this->orderByComponent->addOrder($column, $direction);
        return $this;
    }

    /**
     * 设置查询的LIMIT条件--Set the LIMIT condition for the query.
     *
     * @param int $limit 限制返回的记录数--The number of records to limit the query to.
     * @param int $offset 开始返回记录之前要跳过的记录数--The number of records to skip before starting to return records.
     * @return self 返回自身以支持链式调用--Returns self to support fluent interface.
     */
    public function limit($limit, $offset = 0): self
    {
        $this->limitComponent = new LimitComponent($limit, $offset);
        return $this;
    }

    /**
     * 开始一个新的条件组合，用于创建逻辑上括号内的查询--Start a new condition group for creating logically grouped (parenthesized) queries.
     *
     * @param string $logic 逻辑操作符 'AND' 或 'OR'，默认为 'AND'--Logical operator 'AND' or 'OR', defaults to 'AND'.
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
     * 构建并返回SELECT查询的SQL字符串。如果存在WHERE条件，则包含在内--Builds and returns the SQL string for the SELECT query. Includes WHERE conditions if they exist.
     * 支持通过pa()和endPa()添加的复杂的条件逻辑--Supports complex conditional logic added through pa() and endPa().
     *
     * @param ParameterBag $parameterBag 参数包，用于收集构建过程中的参数--Parameter bag for collecting parameters during the build process.
     * @return string 构建的SELECT查询SQL字符串--The constructed SELECT query SQL string.
     */
    public function build(ParameterBag $parameterBag): string
    {
        $sql =
            "SELECT " . implode(", ", $this->fields) . " " . $this->fromClause;
        $whereClause = $this->condition->build($parameterBag);
        if (!empty($whereClause)) {
            $sql .= " WHERE " . $whereClause;
        }
        $sql .= " " . $this->groupByComponent->build($parameterBag);
        $sql .= " " . $this->havingComponent->build($parameterBag);
        $sql .= " " . $this->orderByComponent->build($parameterBag);

        if ($this->limitComponent !== null) {
            $sql .= " " . $this->limitComponent->build($parameterBag);
        }

        return $sql;
    }
}

?>
