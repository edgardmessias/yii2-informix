<?php

namespace edgardmessias\unit\db\informix;

use yii\db\Connection;
use yii\db\Transaction;

/**
 * @group informix
 */
class ConnectionTest extends \yiiunit\framework\db\ConnectionTest
{
    
    use DatabaseTestTrait;

    protected $driverName = 'informix';
    
    public function testSerialize()
    {
        $connection = $this->getConnection(false, false);
        $connection->open();
        $serialized = serialize($connection);
        $unserialized = unserialize($serialized);
        $this->assertInstanceOf('yii\db\Connection', $unserialized);

        $this->assertEquals(123, $unserialized->createCommand("SELECT 123 FROM systables WHERE tabid = 1")->queryScalar());
    }

    public function testQuoteTableName()
    {
        $connection = $this->getConnection(false);
        if ($connection->isDelimident()) {
            $this->assertEquals('"table"', $connection->quoteTableName('table'));
            $this->assertEquals('"table"', $connection->quoteTableName('"table"'));
            $this->assertEquals('"schema"."table"', $connection->quoteTableName('schema.table'));
            $this->assertEquals('"schema"."table"', $connection->quoteTableName('schema."table"'));
            $this->assertEquals('"schema"."table"', $connection->quoteTableName('"schema"."table"'));
            $this->assertEquals('{{table}}', $connection->quoteTableName('{{table}}'));
            $this->assertEquals('(table)', $connection->quoteTableName('(table)'));
        } else {
            $this->assertEquals('table', $connection->quoteTableName('table'));
            $this->assertEquals('table', $connection->quoteTableName('"table"'));
            $this->assertEquals('schema.table', $connection->quoteTableName('schema.table'));
            $this->assertEquals('schema.table', $connection->quoteTableName('schema."table"'));
            $this->assertEquals('schema.table', $connection->quoteTableName('"schema"."table"'));
            $this->assertEquals('{{table}}', $connection->quoteTableName('{{table}}'));
            $this->assertEquals('(table)', $connection->quoteTableName('(table)'));
        }
    }

    public function testQuoteColumnName()
    {
        $connection = $this->getConnection(false);
        if ($connection->isDelimident()) {
            $this->assertEquals('"column"', $connection->quoteColumnName('column'));
            $this->assertEquals('"column"', $connection->quoteColumnName('"column"'));
            $this->assertEquals('"table"."column"', $connection->quoteColumnName('table.column'));
            $this->assertEquals('"table"."column"', $connection->quoteColumnName('table."column"'));
            $this->assertEquals('"table"."column"', $connection->quoteColumnName('"table"."column"'));
            $this->assertEquals('[[column]]', $connection->quoteColumnName('[[column]]'));
            $this->assertEquals('{{column}}', $connection->quoteColumnName('{{column}}'));
            $this->assertEquals('(column)', $connection->quoteColumnName('(column)'));
        } else {
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
    
    public function testTransactionShortcutCustom()
    {
        $connection = $this->getConnection(true);

        $result = $connection->transaction(function (Connection $db) {
            $db->createCommand()->insert('profile', ['description' => 'test transaction shortcut'])->execute();
            return true;
        }, Transaction::READ_COMMITTED);

        $this->assertTrue($result, 'transaction shortcut valid value should be returned from callback');

        $profilesCount = $connection->createCommand("SELECT COUNT(*) FROM profile WHERE description = 'test transaction shortcut';")->queryScalar();
        $this->assertEquals(1, $profilesCount, 'profile should be inserted in transaction shortcut');
    }
}
