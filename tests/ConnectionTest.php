<?php

namespace edgardmessias\unit\db\informix;

/**
 * @group sphinx
 */
class ConnectionTest extends \yiiunit\framework\db\ConnectionTest
{
    
    use DatabaseTestTrait;

    protected $driverName = 'informix';
    
    public function testQuoteTableName()
    {
        $connection = $this->getConnection(false);
        $this->assertEquals('table', $connection->quoteTableName('table'));
        $this->assertEquals('table', $connection->quoteTableName('"table"'));
        $this->assertEquals('schema.table', $connection->quoteTableName('schema.table'));
        $this->assertEquals('schema.table', $connection->quoteTableName('schema."table"'));
        $this->assertEquals('schema.table', $connection->quoteTableName('"schema"."table"'));
        $this->assertEquals('{{table}}', $connection->quoteTableName('{{table}}'));
        $this->assertEquals('(table)', $connection->quoteTableName('(table)'));
    }

    public function testQuoteColumnName()
    {
        $connection = $this->getConnection(false);
        $this->assertEquals('column', $connection->quoteColumnName('column'));
        $this->assertEquals('column', $connection->quoteColumnName('"column"'));
        $this->assertEquals('table.column', $connection->quoteColumnName('table.column'));
        $this->assertEquals('table.column', $connection->quoteColumnName('table."column"'));
        $this->assertEquals('table.column', $connection->quoteColumnName('"table"."column"'));
        $this->assertEquals('[[column]]', $connection->quoteColumnName('[[column]]'));
        $this->assertEquals('{{column}}', $connection->quoteColumnName('{{column}}'));
        $this->assertEquals('(column)', $connection->quoteColumnName('(column)'));
    }
}
