<?php

namespace PQbuilder;

/**
 * * ParameterBag类用于管理和存储SQL查询构建过程中的参数
 * * The ParameterBag class is used for managing and storing parameters during the SQL query building process.
 */
class ParameterBag
{
    /**
     * @var array 存储参数的数组，键为参数名，值为参数值--Array to store parameters, with keys as parameter names and values as parameter values.
     */
    protected $parameters = [];

    /**
     * 向参数包中添加一个参数--Adds a parameter to the bag.
     *
     * @param string $key 参数名--The name of the parameter.
     * @param mixed $value 参数值--The value of the parameter.
     * @return void
     */
    public function add(string $key, $value): void
    {
        $this->parameters[$key] = $value;
    }

    /**
     * 返回所有参数的数组--Returns an array of all parameters.
     *
     * @return array 包含所有参数的数组--Array containing all parameters.
     */
    public function all(): array
    {
        return $this->parameters;
    }
}

?>
