<?php

namespace edgardmessias\unit\db\informix;

use edgardmessias\db\informix\Schema;

/**
 * @group informix
 */
class QueryBuilderTest extends \yiiunit\framework\db\QueryBuilderTest
{

    use DatabaseTestTrait;

    use \yii\db\SchemaBuilderTrait;

    protected $driverName = 'informix';

    /**
     * @throws \Exception
     * @return \edgardmessias\db\informix\QueryBuilder
     */
    protected function getQueryBuilder()
    {
        if (self::$params === null) {
            self::$params = include __DIR__ . '/data/config.php';
        }
        $databases = self::getParam('databases');
        $this->database = $databases[$this->driverName];

        $connection = $this->getConnection(true, false);

        \Yii::$container->set('db', $connection);

        return new \edgardmessias\db\informix\QueryBuilder($connection);
    }

    /**
     * adjust dbms specific escaping
     * @param $sql
     * @return mixed
     */
    protected function replaceQuotes($sql)
    {
        if ($this->getQueryBuilder()->db->isDelimident()) {
            return str_replace('`', '"', $sql);
        }
        return str_replace('`', '', $sql);
    }

    /**
     * this is not used as a dataprovider for testGetColumnType to speed up the test
     * when used as dataprovider every single line will cause a reconnect with the database which is not needed here
     */
    public function columnTypes()
    {
        return [
            [Schema::TYPE_PK, $this->primaryKey(), 'serial NOT NULL PRIMARY KEY'],
            [Schema::TYPE_PK . '(8)', $this->primaryKey(8), 'serial NOT NULL PRIMARY KEY'],
            [Schema::TYPE_PK . ' CHECK (value > 5)', $this->primaryKey()->check('value > 5'), 'serial NOT NULL PRIMARY KEY CHECK (value > 5)'],
            [Schema::TYPE_PK . '(8) CHECK (value > 5)', $this->primaryKey(8)->check('value > 5'), 'serial NOT NULL PRIMARY KEY CHECK (value > 5)'],
            [Schema::TYPE_STRING, $this->string(), 'varchar(255)'],
            [Schema::TYPE_STRING . '(32)', $this->string(32), 'varchar(32)'],
            [Schema::TYPE_STRING . " CHECK (value LIKE 'test%')", $this->string()->check("value LIKE 'test%'"), "varchar(255) CHECK (value LIKE 'test%')"],
            [Schema::TYPE_STRING . "(32) CHECK (value LIKE 'test%')", $this->string(32)->check("value LIKE 'test%'"), "varchar(32) CHECK (value LIKE 'test%')"],
            [Schema::TYPE_STRING . ' NOT NULL', $this->string()->notNull(), 'varchar(255) NOT NULL'],
            [Schema::TYPE_TEXT, $this->text(), 'text'],
            [Schema::TYPE_TEXT . '(255)', $this->text(), 'text', Schema::TYPE_TEXT],
            //-219	Wildcard matching may not be used with non-character types.
            //[Schema::TYPE_TEXT . ' CHECK (value LIKE "test%")', $this->text()->check("value LIKE 'test%'"), 'text CHECK (value LIKE "test%")'],
            //[Schema::TYPE_TEXT . '(255) CHECK (value LIKE "test%")', $this->text()->check("value LIKE 'test%'"), 'text CHECK (value LIKE "test%")', Schema::TYPE_TEXT . ' CHECK (value LIKE "test%")'],
            [Schema::TYPE_TEXT . ' NOT NULL', $this->text()->notNull(), 'text NOT NULL'],
            [Schema::TYPE_TEXT . '(255) NOT NULL', $this->text()->notNull(), 'text NOT NULL', Schema::TYPE_TEXT . ' NOT NULL'],
            [Schema::TYPE_SMALLINT, $this->smallInteger(), 'smallint'],
            [Schema::TYPE_SMALLINT . '(8)', $this->smallInteger(8), 'smallint'],
            [Schema::TYPE_INTEGER, $this->integer(), 'integer'],
            [Schema::TYPE_INTEGER . '(8)', $this->integer(8), 'integer'],
            [Schema::TYPE_INTEGER . ' CHECK (value > 5)', $this->integer()->check('value > 5'), 'integer CHECK (value > 5)'],
            [Schema::TYPE_INTEGER . '(8) CHECK (value > 5)', $this->integer(8)->check('value > 5'), 'integer CHECK (value > 5)'],
            [Schema::TYPE_INTEGER . ' NOT NULL', $this->integer()->notNull(), 'integer NOT NULL'],
            [Schema::TYPE_BIGINT, $this->bigInteger(), 'bigint'],
            [Schema::TYPE_BIGINT . '(8)', $this->bigInteger(8), 'bigint'],
            [Schema::TYPE_BIGINT . ' CHECK (value > 5)', $this->bigInteger()->check('value > 5'), 'bigint CHECK (value > 5)'],
            [Schema::TYPE_BIGINT . '(8) CHECK (value > 5)', $this->bigInteger(8)->check('value > 5'), 'bigint CHECK (value > 5)'],
            [Schema::TYPE_BIGINT . ' NOT NULL', $this->bigInteger()->notNull(), 'bigint NOT NULL'],
            [Schema::TYPE_FLOAT, $this->float(), 'smallfloat'],
            [Schema::TYPE_FLOAT . '(16)', $this->float(16), 'smallfloat'],
            [Schema::TYPE_FLOAT . ' CHECK (value > 5.6)', $this->float()->check('value > 5.6'), 'smallfloat CHECK (value > 5.6)'],
            [Schema::TYPE_FLOAT . '(16) CHECK (value > 5.6)', $this->float(16)->check('value > 5.6'), 'smallfloat CHECK (value > 5.6)'],
            [Schema::TYPE_FLOAT . ' NOT NULL', $this->float()->notNull(), 'smallfloat NOT NULL'],
            [Schema::TYPE_DOUBLE, $this->double(), 'float'],
            [Schema::TYPE_DOUBLE . '(16)', $this->double(16), 'float'],
            [Schema::TYPE_DOUBLE . ' CHECK (value > 5.6)', $this->double()->check('value > 5.6'), 'float CHECK (value > 5.6)'],
            [Schema::TYPE_DOUBLE . '(16) CHECK (value > 5.6)', $this->double(16)->check('value > 5.6'), 'float CHECK (value > 5.6)'],
            [Schema::TYPE_DOUBLE . ' NOT NULL', $this->double()->notNull(), 'float NOT NULL'],
            [Schema::TYPE_DECIMAL, $this->decimal(), 'decimal(10,0)'],
            [Schema::TYPE_DECIMAL . '(12,4)', $this->decimal(12, 4), 'decimal(12,4)'],
            [Schema::TYPE_DECIMAL . ' CHECK (value > 5.6)', $this->decimal()->check('value > 5.6'), 'decimal(10,0) CHECK (value > 5.6)'],
            [Schema::TYPE_DECIMAL . '(12,4) CHECK (value > 5.6)', $this->decimal(12, 4)->check('value > 5.6'), 'decimal(12,4) CHECK (value > 5.6)'],
            [Schema::TYPE_DECIMAL . ' NOT NULL', $this->decimal()->notNull(), 'decimal(10,0) NOT NULL'],
            [Schema::TYPE_DATETIME, $this->dateTime(), 'datetime year to second'],
            [Schema::TYPE_DATETIME . " CHECK (value BETWEEN '2011-01-01 00:00:00' AND '2013-01-01 00:00:00')", $this->dateTime()->check("value BETWEEN '2011-01-01 00:00:00' AND '2013-01-01 00:00:00'"), "datetime year to second CHECK (value BETWEEN '2011-01-01 00:00:00' AND '2013-01-01 00:00:00')"],
            [Schema::TYPE_DATETIME . ' NOT NULL', $this->dateTime()->notNull(), 'datetime year to second NOT NULL'],
            [Schema::TYPE_TIMESTAMP, $this->timestamp(), 'datetime year to second'],
            [Schema::TYPE_TIMESTAMP . " CHECK (value BETWEEN '2011-01-01 00:00:00' AND '2013-01-01 00:00:00')", $this->timestamp()->check("value BETWEEN '2011-01-01 00:00:00' AND '2013-01-01 00:00:00'"), "datetime year to second CHECK (value BETWEEN '2011-01-01 00:00:00' AND '2013-01-01 00:00:00')"],
            [Schema::TYPE_TIMESTAMP . ' NOT NULL', $this->timestamp()->notNull(), 'datetime year to second NOT NULL'],
            [Schema::TYPE_TIME, $this->time(), 'datetime hour to second'],
            [Schema::TYPE_TIME . " CHECK (value BETWEEN '12:00:00' AND '13:01:01')", $this->time()->check("value BETWEEN '12:00:00' AND '13:01:01'"), "datetime hour to second CHECK (value BETWEEN '12:00:00' AND '13:01:01')"],
            [Schema::TYPE_TIME . ' NOT NULL', $this->time()->notNull(), 'datetime hour to second NOT NULL'],
            [Schema::TYPE_DATE, $this->date(), 'datetime year to day'],
            [Schema::TYPE_DATE . " CHECK (value BETWEEN '2011-01-01' AND '2013-01-01')", $this->date()->check("value BETWEEN '2011-01-01' AND '2013-01-01'"), "datetime year to day CHECK (value BETWEEN '2011-01-01' AND '2013-01-01')"],
            [Schema::TYPE_DATE . ' NOT NULL', $this->date()->notNull(), 'datetime year to day NOT NULL'],
            [Schema::TYPE_BINARY, $this->binary(), 'blob'],
            [Schema::TYPE_BOOLEAN, $this->boolean(), 'boolean'],
            [Schema::TYPE_BOOLEAN . " DEFAULT 't' NOT NULL", $this->boolean()->notNull()->defaultValue(true), "boolean DEFAULT 't' NOT NULL"],
            [Schema::TYPE_MONEY, $this->money(), 'money(19,4)'],
            [Schema::TYPE_MONEY . '(16,2)', $this->money(16, 2), 'money(16,2)'],
            [Schema::TYPE_MONEY . ' CHECK (value > 0.0)', $this->money()->check('value > 0.0'), 'money(19,4) CHECK (value > 0.0)'],
            [Schema::TYPE_MONEY . '(16,2) CHECK (value > 0.0)', $this->money(16, 2)->check('value > 0.0'), 'money(16,2) CHECK (value > 0.0)'],
            [Schema::TYPE_MONEY . ' NOT NULL', $this->money()->notNull(), 'money(19,4) NOT NULL'],
        ];
    }

    public function conditionProvider()
    {
        $conditions = parent::conditionProvider();

        $conditions[49] = [ ['in', ['id', 'name'], [['id' => 1, 'name' => 'foo'], ['id' => 2, 'name' => 'bar']]], $this->replaceQuotes('((`id` = :qp0 AND `name` = :qp1) OR (`id` = :qp2 AND `name` = :qp3))'), [':qp0' => 1, ':qp1' => 'foo', ':qp2' => 2, ':qp3' => 'bar']];
        $conditions[50] = [ ['not in', ['id', 'name'], [['id' => 1, 'name' => 'foo'], ['id' => 2, 'name' => 'bar']]], $this->replaceQuotes('((`id` != :qp0 OR `name` != :qp1) AND (`id` != :qp2 OR `name` != :qp3))'), [':qp0' => 1, ':qp1' => 'foo', ':qp2' => 2, ':qp3' => 'bar']];

        //Remove composite IN
        unset($conditions[51]);
        unset($conditions[52]);

        return $conditions;
    }
}
