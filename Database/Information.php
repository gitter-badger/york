<?php
namespace York\Database;

/**
 * retrieves information from mysql databases, tables and columns
 * requires an instantiated model object
 *
 * @package York\Database
 * @version $version$
 * @author  wolxXx
 */
class Information
{
    /**
     * checks if the given table exists in the given database
     *
     * @param string $databaseName
     * @param string $tableName
     *
     * @return boolean
     */
    public static function tableExists($databaseName, $tableName)
    {
        $model = new \York\Database\Model();

        return 0 !== $model->count(array(
            'method' => 'all',
            'from' => 'information_schema.COLUMNS',
            'where' => array(
                'TABLE_SCHEMA' => $databaseName,
                'TABLE_NAME' => $tableName
            )
        ));
    }

    /**
     * checks if the given column exists in the given table and database
     *
     * @param string $databaseName
     * @param string $tableName
     * @param string $columnName
     *
     * @return boolean
     */
    public static function columnExists($databaseName, $tableName, $columnName)
    {
        $model = new \York\Database\Model();

        return 0 !== $model->count(array(
            'method' => 'all',
            'from' => 'information_schema.COLUMNS',
            'where' => array(
                'TABLE_SCHEMA' => $databaseName,
                'TABLE_NAME' => $tableName,
                'COLUMN_NAME' => $columnName
            )
        ));
    }

    /**
     * retrieves all columns for a table
     *
     * @param string $databaseName
     * @param string $tableName
     *
     * @return array
     */
    public static function getColumnsForTable($databaseName, $tableName)
    {
        $model = new \York\Database\Model();

        return $model->find(array(
            'distinct' => true,
            'method' => 'all',
            'from' => 'information_schema.COLUMNS',
            'fields' => '*',
            'where' => array(
                'TABLE_SCHEMA' => $databaseName,
                'TABLE_NAME' => $tableName
            )
        ));
    }

    /**
     * retrieves the the of a column in a table
     *
     * @param string $databaseName
     * @param string $tableName
     * @param string $columnName
     *
     * @return \stdClass
     */
    public static function getTypeOfColumn($databaseName, $tableName, $columnName)
    {
        $model = new \York\Database\Model();

        return $model->find(array(
            'distinct' => true,
            'method' => 'one',
            'from' => 'information_schema.COLUMNS',
            'fields' => array(
                'DATA_TYPE'
            ),
            'where' => array(
                'TABLE_SCHEMA' => $databaseName,
                'TABLE_NAME' => $tableName,
                'COLUMN_NAME' => $columnName
            )
        ));
    }

    /**
     * retrieves all tables in a database
     * exclude array may contain some strings for excluding matching table names
     *
     * @param string $databaseName
     * @param array  $exclude
     *
     * @return array
     */
    public static function getAllTables($databaseName, $exclude = array())
    {
        $model = new \York\Database\Model();

        return $model->find(array(
            'method' => 'all',
            'distinct' => true,
            'fields' => array(
                'TABLE_NAME'
            ),
            'from' => 'information_schema.COLUMNS',
            'where' => array(
                'TABLE_SCHEMA' => $databaseName,
                'NOTIN' => array(
                    'TABLE_NAME' => $exclude
                )
            )
        ));
    }

    /**
     * @param string $table
     *
     * @return integer
     */
    public static function countTableEntries($table)
    {
        $model = new \York\Database\Model();

        return $model->count(array(
            'method' => 'all',
            'from' => array(
                $table
            )
        ));
    }
}
