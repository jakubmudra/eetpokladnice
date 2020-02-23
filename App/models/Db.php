<?php

namespace App\models;

use App\Config\Config;

class Db
{
    private static $connection;

    private static $settings = [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
        \PDO::ATTR_EMULATE_PREPARES => false
    ];

    /**
     * Connect to db
     */
    public static function connect()
    {
        if (!isset(self::$connection)) {
            try {
                self::$connection = @new \PDO(
                    sprintf("mysql:host=%s;dbname=%s", Config::$database["host"], Config::$database["database"]),
                    Config::$database["user"],
                    Config::$database["password"],
                    self::$settings
                );
            } catch (\PDOException $e) {
                header("Location: /error");
            }
        }
    }

    /**
     * Return one row from db
     * @param string $query  query to be runned
     * @param array $params query parrams
     * @return mixed
     */
    public static function oneRow($query, $params = [])
    {
        $return = self::$connection->prepare($query);
        $return->execute($params);
        return $return->fetch();
    }

    /**
     * Return one row from db
     * @param string $query  query to be runned
     * @param array $params query parrams
     * @return mixed
     */
    public static function allRows($query, $params = [])
    {
        $return = self::$connection->prepare($query);
        $return->execute($params);
        return $return->fetchAll();
    }

    /**
     * Return one row from db
     * @param string $query  query to be runned
     * @param array $params query parrams
     * @return mixed
     */
    public static function singleEntry($query, $params = [])
    {
        $return = self::oneRow($query, $params);
        return $return[0];
    }

    /**
     * Return one row from db
     * @param string $query  query to be runned
     * @param array $params query parrams
     * @return mixed
     */
    public static function rowCount($query, $params = [])
    {
        $return = self::$connection->prepare($query);
        $return->execute($params);
        return $return->rowCount();
    }

    /**
     * Insert data to table
     * @param $table string table name
     * @param array $params object to be inserted
     * @return mixed
     */
    public static function insert($table, $params = [])
    {
        return self::rowCount(
            "INSERT INTO `$table` (`" .
            implode('`, `', array_keys($params)) .
            "`) VALUES (" . str_repeat('?,', sizeOf($params) - 1) . "?)",
            array_values($params)
        );
    }

    /**
     * Update table data
     * @param $table
     * @param array $values
     * @param $cond
     * @param array $params
     * @return mixed
     */
    public static function update($table, $values = array(), $cond, $params = array())
    {
        return self::rowCount(
            "UPDATE `$table` SET `" .
            implode('` = ?, `', array_keys($values)) .
            "` = ? " . $cond,
            array_merge(array_values($values), $params)
        );
    }
    /**
     * Get lass id;
     * @return mixed
     */
    public static function getLastId()
    {
        return self::$connection->lastInsertId();
    }

    /**
     * Insert mupltiple lines into db
     * @param $tableName
     * @param $data
     * @return mixed
     */
    public static function insertMultiple($tableName, $data)
    {
        $rowsSQL = array();
        $toBind = array();
        $columnNames = array_keys($data[0]);

        foreach ($data as $arrayIndex => $row) {
            $params = array();
            foreach ($row as $columnName => $columnValue) {
                $param = ":" . $columnName . $arrayIndex;
                $params[] = $param;
                $toBind[$param] = $columnValue;
            }
            $rowsSQL[] = "(" . implode(", ", $params) . ")";
        }

        $sql = "INSERT INTO `$tableName` (" . implode(", ", $columnNames) . ") VALUES " . implode(", ", $rowsSQL);

        $return = self::$connection->prepare($sql);

        foreach ($toBind as $param => $val) {
            $return->bindValue($param, $val);
        }

        return $return->execute();
    }

}
