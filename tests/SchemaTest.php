<?php

namespace edgardmessias\unit\db\informix;

/**
 * @group informix
 */
class SchemaTest extends \yiiunit\framework\db\SchemaTest
{

    use DatabaseTestTrait;

    protected $driverName = 'informix';

    public function testGetPDOType()
    {
        $values = [
            [null, \PDO::PARAM_STR],
            ['', \PDO::PARAM_STR],
            ['hello', \PDO::PARAM_STR],
            [0, \PDO::PARAM_INT],
            [1, \PDO::PARAM_INT],
            [1337, \PDO::PARAM_INT],
            [true, \PDO::PARAM_BOOL],
            [false, \PDO::PARAM_BOOL],
            [$fp = fopen(__FILE__, 'rb'), \PDO::PARAM_LOB],
        ];

        /* @var $schema \edgardmessias\db\informix\Schema */
        $schema = $this->getConnection()->schema;

        foreach ($values as $value) {
            $this->assertEquals($value[1], $schema->getPdoType($value[0]), 'type for value ' . print_r($value[0], true) . ' does not match.');
        }
        fclose($fp);
    }

    public function getExpectedColumns()
    {
        $columns = parent::getExpectedColumns();

        unset($columns['enum_col']);
        $columns['int_col']['dbType'] = 'integer';
        $columns['int_col']['size'] = null;
        $columns['int_col']['precision'] = null;
        $columns['int_col2']['dbType'] = 'integer';
        $columns['int_col2']['size'] = null;
        $columns['int_col2']['precision'] = null;
        $columns['smallint_col']['dbType'] = 'smallint';
        $columns['smallint_col']['size'] = null;
        $columns['smallint_col']['precision'] = null;
        $columns['float_col']['dbType'] = 'float';
        $columns['float_col']['size'] = null;
        $columns['float_col']['precision'] = null;
        $columns['float_col']['scale'] = null;
        $columns['float_col2']['dbType'] = 'float';
        $columns['float_col2']['size'] = null;
        $columns['float_col2']['precision'] = null;
        $columns['float_col2']['scale'] = null;
        $columns['time']['dbType'] = 'datetime year to second';
        $columns['time']['type'] = 'datetime';
        $columns['bool_col']['dbType'] = 'boolean';
        $columns['bool_col']['type'] = 'boolean';
        $columns['bool_col']['phpType'] = 'boolean';
        $columns['bool_col']['size'] = null;
        $columns['bool_col']['precision'] = null;
        $columns['bool_col2']['dbType'] = 'boolean';
        $columns['bool_col2']['type'] = 'boolean';
        $columns['bool_col2']['phpType'] = 'boolean';
        $columns['bool_col2']['size'] = null;
        $columns['bool_col2']['precision'] = null;
        $columns['bool_col2']['defaultValue'] = true;
        $columns['bool_col3']['type'] = 'boolean';
        $columns['bool_col3']['dbType'] = 'boolean';
        $columns['bool_col3']['phpType'] = 'boolean';
        $columns['bool_col3']['allowNull'] = true;
        $columns['bool_col3']['autoIncrement'] = false;
        $columns['bool_col3']['enumValues'] = null;
        $columns['bool_col3']['size'] = null;
        $columns['bool_col3']['precision'] = null;
        $columns['bool_col3']['scale'] = null;
        $columns['bool_col3']['defaultValue'] = false;
        $columns['ts_default']['dbType'] = 'datetime year to second';
        $columns['ts_default']['type'] = 'datetime';
        $columns['ts_default']['defaultValue'] = new \yii\db\Expression('CURRENT');
        $columns['bit_col']['dbType'] = 'smallint';
        $columns['bit_col']['type'] = 'smallint';
        $columns['bit_col']['size'] = null;
        $columns['bit_col']['precision'] = null;

        return $columns;
    }
}
