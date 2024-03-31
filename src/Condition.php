<?php

namespace PQbuilder;

/**
 * * 条件类，用于构建和管理SQL查询中的条件表达式
 * * The Condition class is used to build and manage condition expressions in SQL queries.
 */
class Condition
{
    /**
     * @var array 存储条件表达式及其逻辑关系--Stores condition expressions and their logical relations.
     */
    private $expressions = [];

    /**
     * @var array 条件组，用于管理嵌套条件组--Condition group stack, for managing nested condition groups.
     */
    private $groupStack = [];

    /**
     * @var array 逻辑操作符组，与条件组栈相对应--Logic operator stack, corresponding to the condition group stack.
     */
    private $logicStack = [];

    /**
     * 构造函数，初始化条件对象--Constructor, initializes the condition object.
     */
    public function __construct()
    {
        $this->groupStack[] = &$this->expressions;
        $this->logicStack[] = "";
    }

    /**
     * 添加一个条件表达式到当前条件组--Adds a condition expression to the current condition group.
     *
     * @param Expression $expression 条件表达式对象--The condition expression object.
     * @param string $logic 逻辑操作符（默认为'AND'）--Logical operator (defaults to 'AND').
     * @return self 返回自身以支持链式调用--Returns itself for chaining.
     */
    public function add(Expression $expression, $logic = "AND")
    {
        $currentGroup = &$this->groupStack[count($this->groupStack) - 1];
        $currentGroup[] = ["expr" => $expression, "logic" => $logic];
        return $this;
    }

    /**
     * 开始一个新的条件组合，用于创建逻辑上括号内的查询--Start a new condition group for creating logically grouped (parenthesized) queries.
     *
     * @param string $logic 逻辑操作符 'AND' 或 'OR'，默认为 'AND'--Logical operator 'AND' or 'OR', defaults to 'AND'.
     * @return self 返回自身以支持链式调用--Returns itself for chaining.
     */
    public function startGroup($logic = "AND")
    {
        $group = ["type" => "group", "logic" => $logic, "expressions" => []];

        $currentGroup = &$this->groupStack[count($this->groupStack) - 1];

        $currentGroup[] = &$group;

        $this->groupStack[] = &$group["expressions"];

        $this->logicStack[] = $logic;
        return $this;
    }

    /**
     * 结束当前的条件组合，括号的结束--End the current condition group.
     *
     * @return self 返回自身以支持链式调用--Returns self for method chaining.
     */
    public function endGroup()
    {
        array_pop($this->groupStack);
        array_pop($this->logicStack);
        return $this;
    }

    /**
     * 构建并返回条件组合的SQL字符串。如果没有条件，则返回空字符串--Builds and returns the SQL string for the condition group. Returns an empty string if there are no conditions.
     *
     * @param ParameterBag $parameterBag 参数包，用于收集构建过程中的参数--Parameter bag for collecting parameters during the build process.
     * @return string 构建的条件字符串--The constructed condition string.
     */
    public function build(ParameterBag $parameterBag): string
    {
        if (empty($this->expressions)) {
            return "";
        }
        return $this->buildGroup($this->expressions, $parameterBag);
    }

    private function buildGroup($group, ParameterBag $parameterBag): string
    {
        $parts = [];
        foreach ($group as $component) {
            if (isset($component["type"]) && $component["type"] === "group") {
                $expr = $this->buildGroup(
                    $component["expressions"],
                    $parameterBag
                );
                if ($expr) {
                    $logic = count($parts) > 0 ? $component["logic"] . " " : "";
                    $parts[] = $logic . "(" . $expr . ")";
                }
            } else {
                $expr = $component["expr"]->build($parameterBag);
                $logic = count($parts) > 0 ? $component["logic"] . " " : "";
                $parts[] = $logic . $expr;
            }
        }
        return implode(" ", $parts);
    }
}

?>
