<?php
namespace PQbuilder;

/**
 * * 组件接口定义，所有的查询构建组件都必须实现这个接口。
 * * Interface definition for components, all query builder components must implement this interface.
 */
interface ComponentInterface
{
    /**
     * 构建查询组件的SQL字符串--Build the SQL string for the query component.
     *
     * @param ParameterBag $parameterBag 参数包，用于收集构建过程中的参数--The parameter bag for collecting parameters during the build process.
     * @return string 返回构建的SQL字符串--Returns the constructed SQL string.
     */
    public function build(ParameterBag $parameterBag): string;
}

?>
