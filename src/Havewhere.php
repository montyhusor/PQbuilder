<?php

namespace PQbuilder;

/**
 * * 定义了具有where条件的组件接口。
 * * Defines an interface for components that support where conditions.
 */
interface Havewhere
{
    /**
     * 添加一个where条件到查询中--Adds a where condition to the query.
     *
     * @param mixed $field 字段名--The name of the field.
     * @param string $operator 操作符，比如'='、'>'等--The operator, such as '=', '>', etc.
     * @param mixed $value 条件的值--The value for the condition.
     * @return self 返回自身以支持链式调用--Returns self to support fluent interface.
     */
    public function where($field, $operator, $value): self;
}

?>
