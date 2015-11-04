<?php

namespace edgardmessias\unit\db\informix;

/**
 * @group informix
 */
class ActiveFixtureTest extends \yiiunit\framework\test\ActiveFixtureTest
{

    use DatabaseTestTrait;

    protected $driverName = 'informix';
    
}
