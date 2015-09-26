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
