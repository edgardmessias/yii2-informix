<?php

namespace edgardmessias\unit\db\informix;

use yii\db\Expression;

/**
 * @group informix
 */
class CommandTest extends \yiiunit\framework\db\CommandTest
{
    
    use DatabaseTestTrait;

    protected $driverName = 'informix';
    
    public function testAutoQuoting()
    {
        $db = $this->getConnection(false);

        $sql = 'SELECT [[id]], [[t.name]] FROM {{customer}} t';
        $command = $db->createCommand($sql);
        if ($db->isDelimident()) {
            $this->assertEquals('SELECT "id", "t"."name" FROM "customer" t', $command->sql);
        } else {
            $this->assertEquals("SELECT id, t.name FROM customer t", $command->sql);
        }
    }
    
    public function testBindParamValue()
    {
        $db = $this->getConnection();

        // bindParam
        $sql = 'INSERT INTO {{customer}}([[email]], [[name]], [[address]]) VALUES (:email, :name, :address)';
        $command = $db->createCommand($sql);
        $email = 'user4@example.com';
        $name = 'user4';
        $address = 'address4';
        $command->bindParam(':email', $email);
        $command->bindParam(':name', $name);
        $command->bindParam(':address', $address);
        $command->execute();

        $sql = 'SELECT [[name]] FROM {{customer}} WHERE [[email]] = :email';
        $command = $db->createCommand($sql);
        $command->bindParam(':email', $email);
        $this->assertEquals($name, $command->queryScalar());

        $sql = <<<SQL
INSERT INTO {{type}} ([[int_col]], [[char_col]], [[float_col]], [[blob_col]], [[numeric_col]], [[bool_col]])
  VALUES (:int_col, :char_col, :float_col, :blob_col, :numeric_col, :bool_col)
SQL;
        $command = $db->createCommand($sql);
        $intCol = 123;
        $charCol = str_repeat('abc', 33) . 'x'; // a 100 char string
        $boolCol = 'f';
        $command->bindParam(':int_col', $intCol, \PDO::PARAM_INT);
        $command->bindParam(':char_col', $charCol);
        $command->bindParam(':bool_col', $boolCol, \PDO::PARAM_BOOL);

        $floatCol = 1.23;
        $numericCol = '1.23';
        $blobCol = "\x10\x11\x12";
        $command->bindParam(':float_col', $floatCol);
        $command->bindParam(':numeric_col', $numericCol);
        $command->bindParam(':blob_col', $blobCol, \PDO::PARAM_STR);
        
        $this->assertEquals(1, $command->execute());

        $command = $db->createCommand('SELECT [[int_col]], [[char_col]], [[float_col]], [[blob_col]], [[numeric_col]], [[bool_col]] FROM {{type}}');
//        $command->prepare();
//        $command->pdoStatement->bindColumn('blob_col', $bc, \PDO::PARAM_LOB);
        $row = $command->queryOne();
        $this->assertEquals($intCol, $row['int_col']);
        $this->assertEquals($charCol, $row['char_col']);
        $this->assertEquals($floatCol, $row['float_col']);
        $this->assertEquals($blobCol, $row['blob_col']);
        $this->assertEquals($numericCol, $row['numeric_col']);
        $this->assertEquals($boolCol, ($row['bool_col'] == 0)?'f':'t');

        // bindValue
        $sql = 'INSERT INTO {{customer}}([[email]], [[name]], [[status]]) VALUES (:email, \'user5\', 0)';
        $command = $db->createCommand($sql);
        $command->bindValue(':email', 'user5@example.com');
        $command->execute();

        $sql = 'SELECT [[email]] FROM {{customer}} WHERE [[name]] = :name';
        $command = $db->createCommand($sql);
        $command->bindValue(':name', 'user5');
        $this->assertEquals('user5@example.com', $command->queryScalar());
    }

    public function testBatchInsert()
    {
        $command = $this->getConnection()->createCommand();
        $command->batchInsert(
            '{{customer}}',
            ['email', 'name', 'status'],
            [
                ['t1@example.com', 't1', 1],
                ['t2@example.com', null, 0],
            ]
        );
        $this->assertEquals(2, $command->execute());
    }
    
    public function testInsertExpression()
    {
        $db = $this->getConnection();
        $db->createCommand('DELETE FROM {{order_with_null_fk}};')->execute();


        $expression = "YEAR(CURRENT)";
        
        $command = $db->createCommand();
        $command->insert(
            '{{order_with_null_fk}}',
            [
                'created_at' => new Expression($expression),
                'total' => 1,
            ]
        )->execute();
        $this->assertEquals(1, $db->createCommand('SELECT COUNT(*) FROM {{order_with_null_fk}};')->queryScalar());
        $record = $db->createCommand('SELECT created_at FROM {{order_with_null_fk}};')->queryOne();
        $this->assertEquals([
            'created_at' => date('Y'),
        ], $record);
    }
    
    public function testCreateTable()
    {
        $db = $this->getConnection();
        if ($db->isDelimident()) {
            $db->createCommand('DROP TABLE IF EXISTS "testCreateTable";')->execute();
        }

        parent::testCreateTable();
    }
    
    public function testAlterTable()
    {
        $db = $this->getConnection();
        if ($db->isDelimident()) {
            $db->createCommand('DROP TABLE IF EXISTS "testAlterTable";')->execute();
        }
        
        parent::testAlterTable();
    }
}
