<?php

namespace edgardmessias\unit\db\informix;

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
        $this->assertEquals("SELECT id, t.name FROM customer t", $command->sql);
    }
    
}
