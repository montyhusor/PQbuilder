<?php

namespace PQbuilder;

/**
 * * INSERT查询构建器组件，用于构建INSERT SQL查询
 * * INSERT query builder component, used for constructing INSERT SQL queries.
 */
class InsertComponent implements ComponentInterface
{
    /**
     * @var string 要插入数据的表名--the table name to insert data into.
     */
    protected $table;

    /**
     * @var string[] 插入数据的列名数组--array of column names for inserting data.
     */
    protected $columns = [];

    /**
     * @var array[] 要插入的值的数组集--array of value sets to be inserted.
     */
    protected $valuesSets = [];

    /**
     * 构造函数，初始化INSERT查询组件--Constructor, initializes the INSERT query component.
     *
     * @param string $table 要插入数据的表名--The table name to insert data into.
     * @param array $columns 插入数据的列名--Array of column names for inserting data.
     * @param array $values 要插入的值的数组--Array of values to be inserted.
     */
    public function __construct(
        string $table,
        array $columns = [],
        array $values = []
    ) {
        $this->table = $table;
        if (!empty($columns)) {
            $this->columns($columns);
        }
        if (!empty($values)) {
            $this->values($values);
        }
    }

    /**
     * 设置要插入的列名--Sets the column names for the insertion.
     *
     * @param array $columns 插入数据的列名--Array of column names for inserting data.
     * @return self 返回自身以支持链式调用--Returns self to support fluent interface.
     */
    public function columns(array $columns): self
    {
        $this->columns = $columns;
        return $this;
    }

    /**
     * 添加一组要插入的值--Adds a set of values to be inserted.
     *
     * @param array $values 要插入的值的数组--Array of values to be inserted.
     * @return self 返回自身以支持链式调用--Returns self to support fluent interface.
     */
    public function values(array $values): self
    {
        $this->valuesSets[] = $values;
        return $this;
    }

    /**
     * 构建并返回INSERT查询的SQL字符串--Builds and returns the SQL string for the INSERT query.
     *
     * @param ParameterBag $parameterBag 参数包，用于收集构建过程中的参数--The parameter bag for collecting parameters during the build process.
     * @return string 构建的INSERT查询SQL字符串--The constructed INSERT query SQL string.
     */
    public function build(ParameterBag $parameterBag): string
    {
        $placeholdersSets = [];

        foreach ($this->valuesSets as $values) {
            $placeholders = [];
            foreach ($values as $value) {
                $placeholder = ":insert" . uniqid();
                $placeholders[] = $placeholder;
                $parameterBag->add($placeholder, $value);
            }
            $placeholdersSets[] = "(" . implode(", ", $placeholders) . ")";
        }

        return "INSERT INTO " .
            $this->table .
            " (" .
            implode(", ", $this->columns) .
            ") VALUES " .
            implode(", ", $placeholdersSets);
    }
}

?>
