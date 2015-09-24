<?php

namespace edgardmessias\unit\db\informix;

/**
 * @group informix
 */
class CommandTest extends \yiiunit\framework\db\CommandTest
{
    
    use DatabaseTestTrait;

    protected $driverName = 'informix';
}
