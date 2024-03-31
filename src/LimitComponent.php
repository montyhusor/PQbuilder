<?php

namespace PQbuilder;

/**
 * * LIMIT查询构建器组件，用于构建SQL查询中的LIMIT部分。
 * * LIMIT query builder component, used for constructing the LIMIT part of an SQL query.
 */
class LimitComponent implements ComponentInterface
{
    /**
     * @var int 记录限制值--The number of records to limit the query to.
     */
    protected $limit;

    /**
     * @var int 记录偏移量，指从哪条记录开始获取--The offset from where to start getting records.
     */
    protected $offset;

    /**
     * 构造函数，初始化LIMIT查询组件--Constructor, initializes the LIMIT query component.
     *
     * @param int $limit 记录限制值--The number of records to limit the query to.
     * @param int $offset 记录偏移量（默认为0）--The record offset (default is 0).
     */
    public function __construct(int $limit, int $offset = 0)
    {
        $this->limit = $limit;
        $this->offset = $offset;
    }

    /**
     * 构建并返回LIMIT子句的SQL字符串--Builds and returns the SQL string for the LIMIT clause.
     *
     * @param ParameterBag $parameterBag 参数包，用于收集构建过程中的参数--The parameter bag for collecting parameters during the build process.
     * @return string 构建的LIMIT子句SQL字符串--The constructed LIMIT clause SQL string.
     */
    public function build(ParameterBag $parameterBag): string
    {
        $clause = "LIMIT " . $this->limit;
        if ($this->offset > 0) {
            $clause .= " OFFSET " . $this->offset;
        }
        return $clause;
    }
}

?>
