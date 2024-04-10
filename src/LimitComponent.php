<?php

namespace PQbuilder;

/**
 * *LIMIT 查询构建器组件，用于构建 SQL 查询中的 LIMIT 部分。
 * *LIMIT query builder component, used for constructing the LIMIT part of an SQL query.
 */
class LimitComponent implements ComponentInterface
{
    /**
     * @var int 记录限制值，即查询返回的最大记录数--The limit value, i.e., the maximum number of records to return in a query.
     */
    protected $limit;

    /**
     * @var int 记录偏移量，指定从哪条记录开始返回结果--The offset value, specifying where to start returning records from.
     */
    protected $offset;

    /**
     * @var string LIMIT 子句中的限制值的占位符--The placeholder for the limit value in the LIMIT clause.
     */
    protected $limitPlaceholder;

    /**
     * @var string LIMIT 子句中的偏移量的占位符--The placeholder for the offset value in the LIMIT clause.
     */
    protected $offsetPlaceholder;

    /**
     * 构造函数，接收记录限制值和记录偏移量，并创建相应的占位符--Constructor, accepts the record limit value and offset, and creates corresponding placeholders.
     *
     * @param int $limit 记录限制值--The limit value.
     * @param int $offset 记录偏移量，默认为0--The offset value, defaults to 0.
     */
    public function __construct(int $limit, int $offset = 0)
    {
        $this->limit = $limit;
        $this->offset = $offset;

        $this->limitPlaceholder = ":limit" . uniqid();
        $this->offsetPlaceholder = ":offset" . uniqid();
    }

    /**
     * 构建 LIMIT 子句的 SQL 字符串，并将参数添加到参数包--Builds the SQL string for the LIMIT clause and adds parameters to the ParameterBag.
     *
     * @param ParameterBag $parameterBag 参数包，用于收集构建过程中的参数--The parameter bag used for collecting parameters during the build process.
     * @return string 构建的 LIMIT 子句 SQL 字符串--The constructed SQL string for the LIMIT clause.
     */
    public function build(ParameterBag $parameterBag): string
    {
        $parameterBag->add($this->limitPlaceholder, $this->limit);
        if ($this->offset > 0) {
            $parameterBag->add($this->offsetPlaceholder, $this->offset);
        }

        $clause = "LIMIT " . $this->limitPlaceholder;
        if ($this->offset > 0) {
            $clause .= " OFFSET " . $this->offsetPlaceholder;
        }
        return $clause;
    }
}

?>
