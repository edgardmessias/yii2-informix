<?php

namespace edgardmessias\unit\db\informix;

/**
 * @group informix
 */
class BatchQueryResultTest extends \yiiunit\framework\db\BatchQueryResultTest
{

    use DatabaseTestTrait;

    protected $driverName = 'informix';
}
