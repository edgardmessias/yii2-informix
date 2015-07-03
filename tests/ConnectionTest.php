<?php

namespace edgardmessias\unit\db\informix;

/**
 * @group sphinx
 */
class ConnectionTest extends \yiiunit\framework\db\ConnectionTest {
    
    use DatabaseTestTrait;

    protected $driverName = 'informix';

}
