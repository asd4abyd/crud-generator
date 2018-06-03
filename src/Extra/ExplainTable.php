<?php
/**
 * Laravel Crud Generator Database Reader
 *
 * @author    Abdelqader Osama Al Dweik <asd.abyd@gmail.com>
 * @copyright 2018 Abdelqader Osama Al Dweik / Abdelqader's Resume (http://abyd.net)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      https://github.com/asd4abyd/crud-generator
 * @see       http://abyd.net
 *
 */

namespace Dweik\CrudGenerator\Extra;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ExplainTable
{

    private $connection;
    private $connectionType;


    private $ignoredTables = [
	    'migrations',
	    'password_resets'
    ];
    private $tables        = [];
    private $tablesColumns = [];
    private $idKey         = [];

    const TYPE_INT     = 1;
    const TYPE_DECIMAL = 2;
    const TYPE_TEXT    = 3;

    const CONNECTION_MYSQL      = 1;
    const CONNECTION_POSTGRESQL = 2;
    const CONNECTION_SQLITE     = 3;

    public static function getTypeAlias($column){
        switch ($column['type']){
            case self::TYPE_INT:
                return 'int';

            case self::TYPE_DECIMAL:
                return 'decimal';

            case self::TYPE_TEXT:
            default:
                if($column['length']>0) {
                    return 'text';
                }
                return 'textarea';
        }
    }

    public function __construct($connection = null) {

        $this->connection = $connection;
        $this->connectionType = $this->checkConnectionType();
        $this->tables = $this->findTables();
    }

    public function getIdKey($table) {
        $this->getColumns($table);
        return isset($this->idKey[$table])? $this->idKey[$table]: '';
    }

    public function hasIdKey($table) {
        return $this->getIdKey($table) != '';
    }

    /**
     *
     * @return array|\Illuminate\Support\Collection|static
     */
    public function getTables() {
        return $this->tables;
    }

    public function getTableName($table){
        return Str::snake(Str::plural($table));
    }

    public function getModelName($table){
        return ucfirst(Str::camel(Str::singular($table)));
    }

    public function getColumns($table) {

        if(isset($this->tablesColumns[$table])) {
            return $this->tablesColumns[$table];
        }

        switch ($this->connectionType) {
            case self::CONNECTION_MYSQL:
                $this->tablesColumns[$table] = $this->mysqlGetColumns($table);
                break;

            case self::CONNECTION_POSTGRESQL:
                $this->tablesColumns[$table] = $this->postgresqlGetColumns($table);
                break;

            case self::CONNECTION_SQLITE:
                $this->tablesColumns[$table] = $this->sqliteGetColumns($table);
                break;

            default:
                return [];
        }

        $this->getAutoIncrement($table);

        return $this->tablesColumns[$table];
    }

    private function findTables() {

	    $ignoredTables = $this->ignoredTables;

        switch ($this->connectionType) {
            case self::CONNECTION_MYSQL:
                $sql = 'SHOW TABLES';
                break;

            case self::CONNECTION_POSTGRESQL:
                $sql = sprintf(
                    "SELECT table_name FROM information_schema.tables where table_schema = '%s' ORDER BY table_schema,table_name;",
                    DB::connection($this->connection)->getConfig('schema') ?: 'public'
                );
                break;

            case self::CONNECTION_SQLITE:
                $sql = "SELECT name FROM sqlite_master WHERE type='table';";
                break;

            default:
                return collect([]);
        }

        return collect(json_decode(json_encode(DB::connection($this->connection)->select($sql)), true))
            ->map(function ($item) use ($ignoredTables) {
            	if(in_array(array_values($item)[0], $ignoredTables)) {
            		return false;
	            }

                return array_values($item)[0];
            });
    }

    private function checkColumnType($columnType) {

        if(preg_match("/(INTEGER|SMALLINT|NUMERIC|INT|FIXED|BIT|TINYINT|MEDIUMINT|BIGINT|SMALLSERIAL|SERIAL|BIGSERIAL|BOOLEAN)/i", $columnType)) {
            return self::TYPE_INT;
        }
        elseif(preg_match("/(DECIMAL|FLOAT|DEC|DOUBLE|REAL)/i", $columnType)) {
            return self::TYPE_DECIMAL;
        }

        return self::TYPE_TEXT;
    }

    private function mysqlGetColumns($table) {

        return collect(DB::connection($this->connection)->select(sprintf('show columns from %s;', $table)))->map(function ($item) {

            $type = $this->checkColumnType($item->Type);

            $len = 0;

            if($type == self::TYPE_TEXT) {
                preg_match("/[\d]+/", $item->Type, $output_array);

                if(isset($output_array[0])){
                    $len = $output_array[0];
                }
            }

            return [
                'title'     => ucfirst(Str::snake($item->Field, ' ')),
                'field'     => $item->Field,
                'type'      => $type,
                'null'      => strtolower($item->Null) == 'yes',
                'length'    => $len,
                'default'   => (string) $item->Default,
                'increment' => strtolower($item->Extra) == 'auto_increment'
            ];
        });
    }

    private function postgresqlGetColumns($table) {

        return collect(DB::connection($this->connection)->select(
            sprintf(
                "select column_name as \"Field\", data_type as \"Type\", is_nullable as \"Null\", character_maximum_length as \"Len\", column_default as \"Extra\"
from INFORMATION_SCHEMA.COLUMNS where table_name = '%s' and table_schema='%s';",
                $table,
                DB::connection($this->connection)->getConfig('schema') ?: 'public')))->map(function ($item) {

            $type = $this->checkColumnType($item->Type);

            return [
                'title'     => ucfirst(Str::snake($item->Field, ' ')),
                'field'     => $item->Field,
                'type'      => $type,
                'null'      => strtoupper($item->Null) == 'yes',
                'length'    => $type == self::TYPE_TEXT? intval($item->Len): 0,
                'default'   => strpos($item->Extra, 'nextval')===false? (string) $item->Extra: '',
                'increment' => strpos($item->Extra, 'nextval')!==false
            ];
        });
    }

    private function sqliteGetColumns($table) {

        return collect(DB::connection($this->connection)->select(sprintf("PRAGMA table_info(%s);", $table)))
            ->map(function ($item) {

            $type = $this->checkColumnType($item->type);

            $len = 0;

            if($type == self::TYPE_TEXT) {
                preg_match("/[\d]+/", $item->type, $output_array);

                if(isset($output_array[0])){
                    $len = $output_array[0];
                }
            }

            return [
                'title'     => ucfirst(Str::snake($item->name, ' ')),
                'field'     => $item->name,
                'type'      => $type,
                'null'      => boolval($item->notnull),
                'length'    => $len,
                'default'   => (string) $item->dflt_value,
                'increment' => boolval($item->pk),
            ];
        });
    }

    private function checkConnectionType() {

        switch (get_class(DB::connection($this->connection)->getQueryGrammar())) {
            case 'Illuminate\Database\Query\Grammars\MySqlGrammar':
                return self::CONNECTION_MYSQL;

            case 'Illuminate\Database\Query\Grammars\PostgresGrammar':
                return self::CONNECTION_POSTGRESQL;

            case 'Illuminate\Database\Query\Grammars\SQLiteGrammar':
                return self::CONNECTION_SQLITE;
        }

        return 0;
    }

    private function getAutoIncrement($table) {
        foreach ($this->tablesColumns[$table] as $column){
            if($column['increment']){
                $this->idKey[$table] = $column['field'];
                break;
            }
        }
    }
}