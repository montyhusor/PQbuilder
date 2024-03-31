<?php

namespace PQbuilder;

/**
 * * 表达式类，用于构建单个查询条件表达式。
 * * Expression class, used to build individual query condition expressions.
 */
class Expression
{
    /**
     * 字段名--field name.
     */
    private $field;

    /**
     * 操作符，如'='、'>'等--operator, such as '=', '>', etc.
     */
    private $operator;

    /**
     * 条件值--condition value.
     */
    private $value;

    /**
     * 构造函数，初始化表达式组件--Constructor, initializes the expression components.
     *
     * @param mixed $field 字段名--The name of the field.
     * @param string $operator 操作符，比如'='、'>'等--The operator, such as '=', '>', etc.
     * @param mixed $value 条件的值--The value for the condition.
     */
    public function __construct($field, $operator, $value)
    {
        $this->field = $field;
        $this->operator = strtoupper($operator);
        $this->value = $value;
    }

    /**
     * 构建并返回条件表达式的SQL字符串--Builds and returns the SQL string for the condition expression.
     *
     * @param ParameterBag $parameterBag 参数包，用于收集构建过程中的参数--The parameter bag for collecting parameters during the build process.
     * @return string 构建的条件表达式SQL字符串--The constructed condition expression SQL string.
     */
    public function build(ParameterBag $parameterBag): string
    {
        if (is_array($this->value) && $this->operator === "IN") {
            $placeholders = [];
            foreach ($this->value as $val) {
                $placeholder = uniqid(":param");
                $placeholders[] = $placeholder;
                $parameterBag->add($placeholder, $val);
            }
            return sprintf(
                "%s IN (%s)",
                $this->field,
                implode(", ", $placeholders)
            );
        } else {
            $placeholder = uniqid(":param");
            $parameterBag->add($placeholder, $this->value);
            return sprintf(
                "%s %s %s",
                $this->field,
                $this->operator,
                $placeholder
            );
        }
    }
}
?>
