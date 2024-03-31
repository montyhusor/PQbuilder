<?php

namespace PQbuilder;

/**
 * * Factory类提供静态方法用于创建不同的查询构建组件
 * * The Factory class provides static methods for creating different query builder components.
 */
class Factory
{
    /**
     * 创建一个SELECT查询--Creates a SELECT query.
     *
     * @param array $fields 要选择的字段列表--List of fields to select.
     * @param string $table FROM子句中的表名--The table name in the FROM clause.
     * @return SelectComponent 返回SELECT查询构建组件--returns a SELECT query builder component.
     */
    public static function qselect(
        array $fields,
        string $table = ""
    ): SelectComponent {
        return new SelectComponent($fields, $table);
    }

    /**
     * 创建一个INSERT查询--Creates an INSERT query.
     *
     * @param string $table 要插入数据的表名--The table name to insert data into.
     * @param array $columns 插入数据的列名--Column names for the data to be inserted.
     * @param array $values 要插入的数据值--Values to be inserted.
     * @return InsertComponent 返回INSERT查询构建组件--Returns an INSERT query builder component.
     */
    public static function qinsert(
        string $table,
        array $columns = [],
        array $values = []
    ): InsertComponent {
        return new InsertComponent($table, $columns, $values);
    }

    /**
     * 创建一个UPDATE查询--Creates an UPDATE query.
     *
     * @param string $table 要更新数据的表名--The table name to update data.
     * @param array $updates 更新操作的列名和值的映射--Mapping of column names to their new values for the update operation.
     * @return UpdateComponent 返回UPDATE查询构建组件--Returns an UPDATE query builder component.
     */
    public static function qupdate(
        string $table,
        array $updates = []
    ): UpdateComponent {
        return new UpdateComponent($table, $updates);
    }

    /**
     * 创建一个DELETE查询--Creates a DELETE query.
     *
     * @param string $table 要删除数据的表名--The table name from which data will be deleted.
     * @return DeleteComponent 返回DELETE查询构建组件--Returns a DELETE query builder component.
     */
    public static function qdelete(string $table): DeleteComponent
    {
        return new DeleteComponent($table);
    }
}

?>
